<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Calendar_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
		$this->db2 = $this->load->database('uk_postcodes',true); 
    }


    public function get_events($options)
    {
        $join = " left join records using(urn) ";
        $where = "";
        $having = "";
        $select_distance = "";
        $start = $options['start'];
        $end = $options['end'];
        $order_by = "";
        if (isset($options['modal']) && !empty($options['distance'])) {
            $order_by = " order by distance";
        }

        $join .= " left join companies using(urn) left join contacts using(contact_id) left join campaigns using(campaign_id) left join campaign_types using(campaign_type_id) left join appointment_attendees using(appointment_id) left join users using(user_id) ";
        if (!empty($options['postcode']) && !empty($options['distance'])) {
            $distance = intval($options['distance']) * 1.1515;
            $postcode_qry =  "select postcode,latitude lat, longitude lng from uk_postcodes.PostcodeIo where postcode = '".$options['postcode']."'";
            $geodata = $this->db2->query($postcode_qry);
            if ($geodata->num_rows() > 0) {
                $coords = $geodata->row_array();
            } else {
                $coords = postcode_to_coords($options['postcode']);
            }
            $join .= " left join locations using(location_id) ";
            $having .= " having distance <= $distance";
            $select_distance .= ",(((ACOS(SIN((" .
                $coords['lat'] . "*PI()/180)) * SIN((lat*PI()/180))+COS((" .
                $coords['lat'] . "*PI()/180)) * COS((lat*PI()/180)) * COS(((" .
                $coords['lng'] . "- lng)*PI()/180))))*180/PI())*60*1.1515) AS distance";

            if (isset($coords['lat']) && isset($coords['lng'])) {

                $where .= " and ( ";
                //Distance from the company or the contacts addresses
                $where .= $coords['lat'] . " BETWEEN (lat-" . $distance . ") AND (lat+" . $distance . ")";
                $where .= " and " . $coords['lng'] . " BETWEEN (lng-" . $distance . ") AND (lng+" . $distance . ")";
                /*$where .= " and ((((
                    ACOS(
                        SIN(" . $coords['lat'] . "*PI()/180) * SIN(lat*PI()/180) +
                        COS(" . $coords['lat'] . "*PI()/180) * COS(lat*PI()/180) * COS(((" . $coords['lng'] . " - lng)*PI()/180)
                    )
                )*180/PI())*160*0.621371192)) <= " . $distance . ")";*/

                $where .= " )";
            }
        }
		 $where .= " and campaign_id in(".$_SESSION['campaign_access']['list'].")";
		if (isset($_SESSION['current_campaign'])) {
            $where .= " and campaign_id = '".$_SESSION['current_campaign']."'";
        }
        if (!empty($options['campaigns'])) {
            $where .= " and campaign_id in(" . implode(",", $options['campaigns']) . ")";
        }
        if (!empty($options['users'])) {
            $where .= " and user_id in(" . implode(",", $options['users']) . ")";
        }
        if (!empty($start)) {
            $where .= " and date(`start`) >= '$start' ";
        }
        if (!empty($end)) {
            $where .= " and date(`end`) <= '$end' ";
        }

        if (isset($options['modal'])) {
            $query = "select appointments.postcode, if(companies.name,'',companies.name) as title, if(contacts.fullname,'',contacts.fullname) as contact, appointment_id,`start`,`end`,users.name as user $select_distance from appointments $join where 1 $where $having $order_by";
        } else {
            $query = "select appointments.urn,appointment_id,campaign_name,date(start) as `date`, appointments.title,text,`start`,`end`,postcode,if(appointments.`status`='1','','Cancelled') as `status`,if(companies.name,'',companies.name) as company, if(contacts.fullname,'',contacts.fullname) as contact, users.name as user $select_distance from appointments $join where 1 and appointments.`status` = 1 $where $having $order_by";
           
        }
        $array = array();
        $users = array();
        foreach ($this->db->query($query)->result_array() as $row) {
            $array[$row['appointment_id']] = $row;
            $users[$row['appointment_id']][] = $row['user'];
        }

        foreach ($array as $k => $row) {
            $attendees = "";
            if (isset($users[$k])) {
                $attendees = implode(",", $users[$k]);
            }
            $array[$k]['attendeelist'] = (!empty($attendees) ? $attendees : "No Attendees!");
            $array[$k]['color'] = genColorCodeFromText($attendees);
        }
        //$this->firephp->log($query);
        return $array;
    }

    public function get_postcode_from_urn($urn)
    {
        $query = "select if(campaign_type_id=1,cona.postcode,coma.postcode) postcode from records left join campaigns using(campaign_id) left join contacts using(urn) left join contact_addresses cona using(contact_id) left join companies using(urn) left join company_addresses coma using(company_id) where urn = '" . intval($urn) . "' and (cona.`primary`=1 or coma.`primary`=1) ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $pc = $result->row(0)->postcode;;
            return $pc;
        }
    }


    /**
     * Add new appointment rule
     */
    public function add_appointment_rule($data) {
        return $this->db->insert("appointment_rules", $data);
    }

    /**
     * Update appointment rule
     */
    public function update_appointment_rule($data)
    {
        $this->db->where('appointment_rules_id', $data['appointment_rules_id']);
        return $this->db->update("appointment_rules", $data);
    }

    /**
     * Delete an appointment rule
     */
    public function delete_appointment_rule($appointment_rules_id) {
        $this->db->where("appointment_rules_id", $appointment_rules_id);
        return $this->db->delete("appointment_rules");
    }

    /**
     * Get appointment rules
     */
    public function get_appointment_rules($distinct_user=false) {
        $qry = "select appointment_rules_id, reason_id, block_day, other_reason, users.name, reason
                from appointment_rules
                join appointment_rule_reasons using (reason_id)
                join users using (user_id) join users_to_campaigns using(user_id)
                where campaign_id in({$_SESSION['campaign_access']['list']}) ";
		if($distinct_user){
		$qry .= " group by block_day,user_id";	
		}
        return $this->db->query($qry)->result_array();
    }
	
    public function get_appointment_override($distinct_user=false) {
        $qry = "select appointment_override_id, `date` block_day, max_slots, users.name, notes reason
                from appointment_slot_override
                join users using (user_id) join users_to_campaigns using(user_id)
                where campaign_id in({$_SESSION['campaign_access']['list']}) ";
		if($distinct_user){
		$qry .= " group by block_day,user_id";	
		}
        return $this->db->query($qry)->result_array();
    }
    /**
     * Get appointment rules by date
     */
    public function get_appointment_rules_by_date($block_day=false) {
        $qry = "select appointment_rules_id, reason_id, date_format(block_day,'%d/%m/%Y') uk_date,block_day, other_reason, users.name, reason, appointment_slot_id, CONCAT(slot_name,' (',TIME_FORMAT(slot_start, '%H:%i'),'-',TIME_FORMAT(slot_end, '%H:%i'),')') as slot_name
                from appointment_rules
                inner join appointment_rule_reasons using (reason_id)
                inner join users using (user_id)
                left join appointment_slots using (appointment_slot_id)
				join users_to_campaigns using(user_id)
                where campaign_id in({$_SESSION['campaign_access']['list']}) and block_day>=curdate() ";
				if($block_day){
				$qry .= " and block_day = '" . $block_day . "'"; 
				}
				$qry .= " order by block_day";
				$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }

    /**
     * Get appointment rules by date
     */
    public function get_appointment_rules_by_date_and_user($block_day=false, $user_id=false)
    {
        $qry = "select appointment_rules_id, reason_id, date_format(block_day,'%d/%m/%Y') uk_date,block_day, other_reason, users.name, reason, appointment_slot_id, CONCAT(slot_name,' (',TIME_FORMAT(slot_start, '%H:%i'),'-',TIME_FORMAT(slot_end, '%H:%i'),')') as slot_name
                from appointment_rules
                inner join appointment_rule_reasons using (reason_id)
                inner join users using (user_id)
                left join appointment_slots using (appointment_slot_id)
				join users_to_campaigns using(user_id) where campaign_id in({$_SESSION['campaign_access']['list']})
                and block_day>=curdate()";
				if($block_day){
				$qry .= " and block_day = '" . $block_day . "'"; 
				}
				if($user_id){
				$qry .= " and user_id = '" . $user_id . "'";
				}
				$qry .= " order by block_day,slot_start";
        return $this->db->query($qry)->result_array();
    }

}