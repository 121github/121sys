<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Appointments_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }
	
	public function slot_availability($campaign_id,$user_id=false,$postcode=false,$distance=false,$source=false,$app_type=false){
		$days = array(1=>"Monday",2=>"Tuesday",3=>"Wednesday",4=>"Thursday",5=>"Friday",6=>"Saturday",7=>"Sunday");
		$timeslots = array();
		$where= "";
		if(!empty($user_id)){
		$where .= " and user_id = '$user_id' ";
		$holidays_where = $where;
		}
		if($campaign_id){
		$where .= " and campaign_id = '$campaign_id' ";
		}
		if($source){
		$where .= " and (source_id = '$source' or source_id is null )";	
		}
		//first configure the default array for all days
		$qry = "select appointment_slot_id,slot_name,slot_description,slot_start,slot_end,user_id, max_slots max_apps,`day` from appointment_slots join appointment_slot_assignment using(appointment_slot_id) where `day` is null  $where ";
		$this->firephp->log($qry);
		$max = array();
		$default = $this->db->query($qry)->result_array();
		if(count($default)=="0"){
		return array("error"=>"The selected user does not have appointment slots configured");	
		}
		$thresholds = array();
		foreach($days as $day_num => $day){
			foreach($default as $row){
			$daycheck = "select slot_assignment_id from appointment_slot_assignment where appointment_slot_id = ".$row['appointment_slot_id']." and user_id = ".$row['user_id']." and day = ".$day_num;
			
			if(!$this->db->query($daycheck)->num_rows()){
			$timeslots[$row['appointment_slot_id']] = array("slot_name"=>$row['slot_name'],"slot_description"=>$row['slot_description'],"slot_start"=>$row['slot_start'],"slot_end"=>$row['slot_end'],"reason"=>"");
			$max[$day_num][$row['user_id']]['default'] = $row['max_apps'];
			unset($row['max_apps']);
			$thresholds[$day][$row['appointment_slot_id']] = $row;
			$thresholds[$day][$row['appointment_slot_id']]['max_apps'] = $max[$day_num][$row['user_id']]['default'];
			$thresholds[$day][$row['appointment_slot_id']]['apps'] = 0;
			} else {
			$thresholds[$day][$row['appointment_slot_id']] = $row;	
			$thresholds[$day][$row['appointment_slot_id']]['max_apps'] = 0;
			$thresholds[$day][$row['appointment_slot_id']]['apps'] = 0;
			}
			}
		}
		
		//get any user specified days
		$defined_slots = array();
		$get_slots = "select appointment_slot_id,`date`,max_slots from appointment_slot_override where `date` > curdate() $where group by appointment_slot_id";
		$get_slots_result = $this->db->query($get_slots)->result_array();
		foreach($get_slots_result as $row){
		$defined_slots[$row['date']][$row['appointment_slot_id']]["max_apps"] = $row['max_slots']; 
		}
		$this->firephp->log($defined_slots);
		//now find the specified daily slots and overwrite the default array
		$max = array();
		foreach($days as $daynum => $day){
		$qry = "select appointment_slot_id,slot_name,slot_description,slot_start,slot_end,user_id, max_slots max_apps,`day` from appointment_slots join appointment_slot_assignment using(appointment_slot_id) where `day` = $daynum $where ";
		$daily_slots = $this->db->query($qry)->result_array();
		foreach($daily_slots as $k=>$row){
			$thresholds[$day][$row['appointment_slot_id']]['apps'] = 0;
			$timeslots[$row['appointment_slot_id']] = array("appointment_slot_id"=>$row['appointment_slot_id'],"slot_name"=>$row['slot_name'],"slot_description"=>$row['slot_description'],"slot_start"=>$row['slot_start'],"slot_end"=>$row['slot_end']);
			if(isset($thresholds[$day][$row['appointment_slot_id']]['max_apps'])){
				$thresholds[$day][$row['appointment_slot_id']]['max_apps'] += $row['max_apps'];
			} else {
			$thresholds[$day][$row['appointment_slot_id']] = $row;
			}
			}
		
		}
		
		
		
/* get user holidays to remove from slots */
$holidays = array();
if($user_id){
		$get_holidays = "select reason,block_day from appointment_rules join appointment_rule_reasons using(reason_id) where 1 "; 
		$get_holidays .= $holidays_where;

		foreach($this->db->query($get_holidays)->result_array() as $k=>$row){
			$holidays[$row['block_day']] = array("reason"=>$row['reason']);
		}
}
/* end holidays */

/* now push all the data into each day for the next 30 days and if there is a holiday for the day we remove the slots and add the reason */

for($i = 1; $i < 30; $i++){
	$date = date("Y-m-d", strtotime('+'. $i .' days'));
	$this_day =  $thresholds[date("l", strtotime('+'. $i .' days'))];
	
		if(array_key_exists($date,$defined_slots)){
	foreach($this_day as $slot => $details){
		if(isset($defined_slots[$date][$slot])){
		$this_day[$slot]['max_apps']=$defined_slots[$date][$slot]["max_apps"];
	}
	}
		}
	if(array_key_exists($date,$holidays)){
		foreach($this_day as $slot => $details){
			$this_day[$slot]['max_apps']=0;
				@$this_day[$slot]['reason']=$holidays[$date]['reason'];
		}	
	}
	
    $slots[date("D jS M", strtotime('+'. $i .' days'))]  =$this_day;
}
/* now get the appointments in each slot for each day and push them into the array */

$join_locations = "";
$distance_select = "";
$distance_order = "";
	
		if(!empty($postcode)){
		    $coords = postcode_to_coords($postcode);
			if(isset($coords['lat'])&&isset($coords['lng'])){
				$distance_order = " order by distance";
				$distance_select = ",min((((ACOS(SIN((" .
                $coords['lat'] . "*PI()/180)) * SIN((lat*PI()/180))+COS((" .
                $coords['lat'] . "*PI()/180)) * COS((lat*PI()/180)) * COS(((" .
                $coords['lng'] . "- lng)*PI()/180))))*180/PI())*60*1.1515)) AS distance";
				
                        $join_locations = " left join locations on locations.location_id = appointments.location_id ";
						if($distance>0){
                        $where .= " and ( ";
                        //Distance from the company or the contacts addresses
                        $where .= " (";
                        $where .= $coords['lat'] . " BETWEEN (locations.lat-" . $distance . ") AND (locations.lat+" . $distance . ")";
                        $where .= " and " . $coords['lng'] . " BETWEEN (locations.lng-" . $distance . ") AND (locations.lng+" . $distance . ")";
                        $where .= " and ((((
							ACOS(
								SIN(" . $coords['lat'] . "*PI()/180) * SIN(locations.lat*PI()/180) +
								COS(" . $coords['lat'] . "*PI()/180) * COS(locations.lat*PI()/180) * COS(((" . $coords['lng'] . " - locations.lng)*PI()/180)
							)
						)*180/PI())*160*0.621371192)) <= " . $distance . ")";
                        
                        $where .= " ) )";	
						}
		}	else {
			return array("error"=>"There was an error with the postcode");	
		}
		}
		

		
		
foreach($timeslots as $id=>$timeslot){
	$app_type_where = "";
	if($app_type){
	$app_type_where = " and appointment_type_id = '$app_type' ";	
	}
		$qry = "select date(`start`) start $distance_select, count(*) count from appointments $join_locations left join records using(urn) join appointment_attendees using(appointment_id) where `status` = 1  and time(`start`) between '".$timeslot['slot_start']."' and '".$timeslot['slot_end']."' and date(`start`) between curdate() and  adddate(curdate(),interval 30 day) $where $app_type_where group by date(`start`) $distance_order";
				
		$results = $this->db->query($qry)->result_array();
		
		$i=0;
		foreach($results as $row){
			$date = date("D jS M", strtotime($row['start']));
			@$slots[$date][$id]['sqldate']=$row['start'];
			@$slots[$date][$id]['apps']=$row['count'];
			//the smallest distance for this timeslot
			@$slots[$date][$id]['min_distance']=number_format($row['distance'],2);
			if($i==0){
			//first record is best distance	
			@$slots[$date][$id]['best_distance']=true;	
			}
			$i++;
		}
}

		return array("timeslots"=>$timeslots,"apps"=>$slots);
		
		
	}
	
    public function appointment_data($count = false, $options = false)
    {
        $table_columns = array(
            "CONCAT(IFNULL(records.map_icon,''),IFNULL(camp.map_icon,''))",
            "date_format(a.`start`,'%d/%m/%y H:i')",
            "com.name",
            "u.name",
            "date_format(a.`date_added`,'%d/%m/%y')",
            "postcode"
        );
        $order_columns = array(
            "r.date_added",
            "a.start",
            "com.name",
            "u.name",
            "a.date_added",
            "a.postcode"
        );

        $qry = "select
                  date_format(`start`,'%d/%m/%y %H:%i') start,
                  com.name,
                  u.name attendee,
                  date_format(a.`date_added`,'%d/%m/%y') date_added,
                  a.postcode,
                  loc.lat,
                  loc.lng,
                  loc.location_id,
                  a.appointment_id,
				  a.title,
                  records.urn,
				  appointment_id marker_id,
				  records.record_color,
				  records.map_icon,
				  GROUP_CONCAT(DISTINCT CONCAT(coma.postcode, '(',company_locations.lat,'/',company_locations.lng,')','|',company_locations.location_id) separator ',') as company_location,
                  GROUP_CONCAT(DISTINCT CONCAT(cona.postcode, '(',contact_locations.lat,'/',contact_locations.lng,')','|',contact_locations.location_id) separator ',') as contact_location,
                  camp.map_icon as campaign_map_icon,
                  ow.user_id ownership_id,
                  owu.name ownership,
                  outcome,
                  date_format(rp.start_date,'%d/%m/%Y') planner_date,
                  rp.user_id planner_user_id,
                  rp.record_planner_id,
                  rp.postcode as planner_postcode,
                  rp.location_id as planner_location_id,
                  rpu.name planner_user
                from appointments a
                  left join appointment_attendees aa using(appointment_id)
                  left join users u on u.user_id = aa.user_id
                  left join records using(urn)
                  left join campaigns camp using(campaign_id)
                  left join companies com using(urn)
                  left join company_addresses coma on coma.company_id = com.company_id
                  left join locations company_locations ON (coma.location_id = company_locations.location_id)
                  left join contacts con on con.urn = records.urn
                  left join contact_addresses cona on cona.contact_id = con.contact_id
                  left join locations contact_locations ON (cona.location_id = contact_locations.location_id)
                  left join locations loc on loc.location_id = a.location_id
                  left join ownership ow on ow.urn = records.urn
                  left join users owu on ow.user_id = owu.user_id
                  left join outcomes o on o.outcome_id = records.outcome_id
                  left join record_planner rp on rp.urn = records.urn
                  left join users rpu on rpu.user_id = rp.user_id ";
        $where = $this->get_where($options, $table_columns);
        $qry .= $where;
        $qry .= " group by appointment_id";
        if ($count) {

            return $this->db->query($qry)->num_rows();
        }


        $start = $options['start'];
        $length = $options['length'];
        if (isset($_SESSION['appointment_table']['order']) && $options['draw'] == "1") {
            $order = $_SESSION['appointment_table']['order'];
        } else {
            $order = " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'];
            unset($_SESSION['appointment_table']['order']);
            unset($_SESSION['appointment_table']['values']['order']);
        }

        $qry .= $order;
        $qry .= "  limit $start,$length";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }

    private function get_where($options, $table_columns)
    {
        //the default condition in ever search query to stop people viewing campaigns they arent supposed to!
        $where = " where a.`status` = 1 and campaign_id in({$_SESSION['campaign_access']['list']}) ";
		if(isset($_SESSION['current_campaign'])){
			 $where .= " and campaign_id = '".$_SESSION['current_campaign']."' ";
		}
        //Check the bounds of the map
        if ($options['bounds'] && $options['map']=='true') {
            $where .= " and (loc.lat < ".$options['bounds']['neLat']." and loc.lat > ".$options['bounds']['swLat']." and loc.lng < ".$options['bounds']['neLng']." and loc.lng > ".$options['bounds']['swLng'].") ";
        }

        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $where .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        return $where;
    }

    public function appointment_modal($id)
    {
        $qry = "select * from appointments a left join users u on u.user_id = a.created_by where appointment_id = " . intval($id) . " group by appointment_id";
        return $this->db->query($qry)->row_array();
    }

    public function appointment_attendees($id)
    {
        $qry = "select name from appointment_attendees left join users using(user_id) where appointment_id = " . intval($id);
        return $this->db->query($qry)->result_array();
    }

    public function checkDayBlocked($attendee_id, $startDate, $endDate) {

        $qry = "select * from appointment_rules where user_id = ".$attendee_id." and block_day>='".substr(to_mysql_datetime($startDate),0,10)."' and block_day<='".substr(to_mysql_datetime($endDate),0,10)."'";

        $results = $this->db->query($qry)->result_array();

        //Return true if $results is not empty, because there is at least one block day between both dates
        return (!empty($results));
    }


    /**
     *
     * Check if the attendee already has an appointment where the block day is between the start and the end date schedulled
     *
     */
    public function checkNoAppointmentForTheDayBlocked($attendee_id, $blockDay) {

        $qry = "select * from appointments left join appointment_attendees using(appointment_id) where user_id = ".$attendee_id." and '".to_mysql_datetime($blockDay)."' >= date(start) and '".to_mysql_datetime($blockDay)."' <= date(end)";

        $results = $this->db->query($qry)->result_array();

        //Return true if $results is not empty, because there is at least one appointment where the block day is between the start and the end date schedulled
        return (!empty($results));
    }


    /**
     * Get appointment by id
     *
     */
    public function getAppointmentById($appointment_id) {

        $this->db->select('*');
        $this->db->from('appointments');
        $this->db->where('appointment_id', $appointment_id);
        $this->db->join('branch','branch.branch_id=appointments.branch_id','LEFT');
        $this->db->join('appointment_types','appointment_types.appointment_type_id=appointments.appointment_type_id','LEFT');
        $query = $this->db->get();
        if ($query->num_rows() == 1)
        {
            return $query->row();
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Get getLastAppointmentIcsByUid
     *
     */
    public function getLastAppointmentIcsByUid($uid) {

        $this->db->select('*');
        $this->db->from('appointments_ics');
        $this->db->where('uid', $uid);
        $this->db->order_by("appointments_ics_id", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1)
        {
            return $query->row();
        }
        else
        {
            return NULL;
        }
    }


    /**
     * Get the Last Appointment Updated by urn
     *
     */
    public function getLastAppointmentUpdated($urn) {

        $this->db->select('*');
        $this->db->from('appointments');
        $this->db->where('urn', $urn);
        $this->db->order_by("date_added", "desc");
        $this->db->order_by("date_updated", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1)
        {
            return $query->row();
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Get appointment by id
     *
     */
    public function saveAppointmentIcs($appointment_ics) {

        $this->db->insert('appointments_ics', $appointment_ics);

        return $this->db->insert_id();

    }

}
