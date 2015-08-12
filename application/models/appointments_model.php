<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Appointments_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }
	
	public function slot_availability($urn){
		$qry = "select slot_name,slot_description, sum(max_slots), user_id, campaign_id, `day` from appointment_slots join appointment_slot_assignment using(appointment_slot_id) where 1 ";
		
		if($user_id){
		$qry .= " and user_id = '$user_id' ";
		}
		if($campaign_id){
		$qry .= " and campaign_id = '$campaign_id' ";
		}
		$qry .= " group by day";
		$slot_data = $this->db->query($qry)->result_array();
		$slots = array();
		$thresholds = array();
		
		foreach($slot_data as $row){
		//GHS campaign appointment slot thresholds
		
		$thresholds["Monday"]= array('am' => 0,'am_max'=>15,'pm' => 0,'pm_max'=>15);
		$thresholds["Tuesday"]= array('am' => 0,'am_max'=>15,'pm' => 0,'pm_max'=>15);
		$thresholds["Wednesday"]= array('am' => 0,'am_max'=>15,'pm' => 0,'pm_max'=>15);
		$thresholds["Thursday"]= array('am' => 0,'am_max'=>15,'pm' => 0,'pm_max'=>15);
		$thresholds["Friday"]= array('am' => 0,'am_max'=>15,'pm' => 0,'pm_max'=>15);
		$thresholds["Saturday"]= array('am' => 0,'am_max'=>15,'pm' => 0,'pm_max'=>15);
		$thresholds["Sunday"]= array('am' => 0,'am_max'=>5,'pm' => 0,'pm_max'=>15);
		
		}
		
for($i = 0; $i < 30; $i++){
    $slots[date("D jS M", strtotime('+'. $i .' days'))] = $thresholds[date("l", strtotime('+'. $i .' days'))];
}

		$am = "select date(`start`) start,count(*) count from appointments left join records using(urn) where time(`start`) between '09:00:00' and '12:59:00' and date(`start`) between curdate() and  adddate(curdate(),interval 30 day) and campaign_id = (select campaign_id from records where urn ='$urn') group by date(`start`) ";
		$pm = "select date(`start`) start,count(*) count from appointments left join records using(urn )where time(`start`) between '13:00:00' and '18:00:00' and date(`start`) between curdate() and  adddate(curdate(),interval 30 day) and campaign_id = (select campaign_id from records where urn ='$urn') group by date(`start`)";
		$eve = ""; //not using
		
		$am_results = $this->db->query($am)->result_array();
		$pm_results = $this->db->query($pm)->result_array();
		//$eve_results = $this->db->query($eve)->result_array();

		foreach($am_results as $row){
			$date = date("D jS M", strtotime($row['start']));
			@$slots[$date]['am']=$row['count'];
		}
		foreach($pm_results as $row){
			$date = date("D jS M", strtotime($row['start']));
			@$slots[$date]['pm']=$row['count'];
		}
		/*
		foreach($eve_results as $row){
			$date = date("D jS M", strtotime($row['start']));
				$day = date("l", strtotime($row['start']));
			@$slots[$date][$day]['eve']++;
		}
		*/
		return $slots;
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
		$this->firephp->log($qry);
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

}
