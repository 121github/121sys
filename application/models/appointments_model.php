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
		$slots = array();
for($i = 0; $i < 30; $i++){
    $slots[date("D jS M", strtotime('+'. $i .' days'))] = array('am' => 0,'am_max'=>4,'pm' => 0,'pm_max'=>4, 'eve' => 0,'eve_max'=>3); //set all days as 0 to begin with
}

		$am = "select date(`start`) start,count(*) from appointments left join records using(urn) where time(`start`) between '08:00:00' and '12:00:00' and date(`start`) between curdate() and  adddate(curdate(),interval 30 day) and campaign_id = (select campaign_id from records where urn ='$urn') group by date(`start`) ";
		$pm = "select date(`start`) start,count(*) from appointments left join records using(urn )where time(`start`) between '12:01:00' and '17:00:00' and date(`start`) between curdate() and  adddate(curdate(),interval 30 day) and campaign_id = (select campaign_id from records where urn ='$urn') group by date(`start`)";
		$eve = "select date(`start`) start,count(*) from appointments left join records using(urn) where time(`start`) between '17:01:00' and '22:00:00' and date(`start`) between curdate() and  adddate(curdate(),interval 30 day) and campaign_id = (select campaign_id from records where urn ='$urn') group by date(`start`)";
		
		$am_results = $this->db->query($am)->result_array();
		$pm_results = $this->db->query($pm)->result_array();
		$eve_results = $this->db->query($eve)->result_array();

		foreach($am_results as $row){
			$date = date("D jS M", strtotime($row['start']));
			$slots[$date]['am']++;
		}
		foreach($pm_results as $row){
			$date = date("D jS M", strtotime($row['start']));
			$slots[$date]['pm']++;
		}
		foreach($eve_results as $row){
			$date = date("D jS M", strtotime($row['start']));
			$slots[$date]['eve']++;
		}
		return $slots;
	}
	
    public function appointment_data($count = false, $options = false)
    {
        $table_columns = array(
            "record_color",
            "date_format(a.`start`,'%d/%m/%y H:i')",
            "com.name",
            "u.name",
            "date_format(a.`date_added`,'%d/%m/%y')",
            "postcode"
        );
        $order_columns = array(
            "record_color",
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
                  postcode,
                  lat,
                  lng,
                  appointment_id,
				  title,
                  records.urn,
				  appointment_id marker_id,
				  records.record_color,
				  records.map_icon,
                  camp.map_icon as campaign_map_icon,
                  ow.user_id ownership_id,
                  owu.name ownership,
                  outcome
                from appointments a
                  left join appointment_attendees aa using(appointment_id)
                  left join users u on u.user_id = aa.user_id
                  left join records using(urn)
                  left join campaigns camp using(campaign_id)
                  left join companies com using(urn)
                  left join locations loc using(location_id)
                  left join ownership ow on ow.urn = records.urn
                  left join users owu on ow.user_id = owu.user_id
                  left join outcomes o on o.outcome_id = records.outcome_id ";
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
            $where .= " and (lat < ".$options['bounds']['neLat']." and lat > ".$options['bounds']['swLat']." and lng < ".$options['bounds']['neLng']." and lng > ".$options['bounds']['swLng'].") ";
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
