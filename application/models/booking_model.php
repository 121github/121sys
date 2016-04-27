	<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Booking_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }

    /**
     * Get the data by urn to insert in the CRM
     *
     * @param $urn
     * @return mixed
     */
    public function get_events($start=false,$end=false,$attendee=false,$status=false,$appointment_type=false,$postcode = false) {
		//$where = " and campaign_id in(".$_SESSION['campaign_access']['list'].")";
		$where = "";
		if($start){
			$where .= " and date(`start`) >= '$start' ";
		}
		if($end){
			$where .= " and date(`start`) <= '$end' ";
		}
		if($attendee){
			$where .= " and appointment_attendees.user_id = '$attendee'";
		}
		if($appointment_type){
			$where .= " and appointments.appointment_type_id = '$appointment_type'";
		}
		if($status != ""){
			$where .= " and status = '$status'";
		}
		if(isset($_SESSION['current_campaign'])){
			$where .= " and campaign_id = '{$_SESSION['current_campaign']}' ";
		}

		$join_locations = "";
        $distance_select = "";
        $distance_order = "";
		$distance = 0;
        if (!empty($postcode)) {
            $coords = postcode_to_coords($postcode);
            if (isset($coords['lat']) && isset($coords['lng'])) {
                $distance_select = ",min((((ACOS(SIN((" .
                    $coords['lat'] . "*PI()/180)) * SIN((lat*PI()/180))+COS((" .
                    $coords['lat'] . "*PI()/180)) * COS((lat*PI()/180)) * COS(((" .
                    $coords['lng'] . "- lng)*PI()/180))))*180/PI())*60*1.1515)) AS distance";

                $join_locations = " left join locations on locations.location_id = appointments.location_id ";
                if ($distance > 0) {
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
            }
		}

		$query = "select appointment_id id,title, start, end, status, text, group_concat(distinct user_id separator ',') attendees, icon, appointment_type $distance_select from appointments $join_locations left join appointment_attendees using(appointment_id) join records using(urn) left join appointment_types using(appointment_type_id) where 1 ";
		$query .= $where;
		$query .= " group by appointment_id";

      	return $this->db->query($query)->result_array();
	}
	
	public function set_event_time($id,$start,$end){
		$this->db->where(array("appointment_id"=>$id));
		$this->db->update("appointments",array("start"=>$start,"end"=>$end));	
		//$this->firephp->log($this->db->last_query());
	}

	public function getGoogleToken($user_id, $api_name) {
		$query = "SELECT * from apis where user_id = ".$user_id." and api_name = '".$api_name."'";

		return $this->db->query($query)->result_array();

	}

	public function getGoogleEventId($appointment_id) {
		$query = "SELECT google_id from appointments where appointment_id = ".$appointment_id;

		$result = $this->db->query($query)->result_array();

		return (!empty($result)?$result[0]['google_id']:null);

	}

	public function saveGoogleEventId($appointment_id, $google_id) {

		$this->db->where('appointment_id', $appointment_id);
		return  $this->db->update('appointments', array(
			"google_id" => $google_id
		));
	}
	
	   /**
     * Get appointment rules by date
     */
    public function get_appointment_rules_by_date_and_user($date=false, $user_id=false)
    {
        $qry = "select slot_override_id,date_format(`date`,'%d/%m/%Y') uk_date, `date`, slot_name, notes, name, max_slots from appointment_slot_override left join appointment_slots using(appointment_slot_id) left join users using(user_id) where 1 ";
				if($date){
				$qry .= " and `date` = '" . $date . "'"; 
				} else {
				$qry .= " and date(`date`) >= curdate() "; 	
				}
				if(!empty($user_id)){
				$qry .= " and user_id = '" . $user_id . "'";
				}
				$qry .= " order by `date`";
				//$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }

	/**
	 * Set the google calendar
	 */
	public function set_google_calendar($options) {

		$this->db->where('user_id', $options['user_id']);
		$this->db->where('campaign_id', $options['campaign_id']);
		$this->db->where('calendar_id', $options['calendar_id']);
		$this->db->where('calendar_name', $options['calendar_name']);
        $this->db->where('api_id', $options['api_id']);
		$this->db->insert_update("google_calendar", $options);
        return $this->db->insert_id();
	}

    /**
     * Remove the google calendar from a particular user
     */
    public function remove_google_calendar($id) {

        $this->db->where('google_calendar_id', $id);
        $this->db->delete("google_calendar");

        return $this->db->affected_rows();
    }

    public function get_appointments_by_google_id($google_id) {
        $this->db->select("appointments.*, GROUP_CONCAT(appointment_attendees.user_id SEPARATOR ';') attendees", false);
        $this->db->join("appointment_attendees", "appointment_attendees.appointment_id=appointments.appointment_id", "LEFT");
        $this->db->group_by("appointment_attendees.user_id");
        $this->db->where("appointments.google_id", $google_id);
        $result = $this->db->get("appointments")->result_array();
        return (!empty($result)?$result[0]:array());
    }

	public function get_google_calendars_by_user($user_id) {
        $query = "select * from google_calendar gc inner join campaigns c using(campaign_id) where user_id =".$user_id;

        return $this->db->query($query)->result_array();
	}

	public function get_google_calendars_selected() {
		$query = "select * from google_calendar gc inner join campaigns c using(campaign_id)";

		return $this->db->query($query)->result_array();
	}

	public function get_google_calendars_selected_by_campaign($campaign_id) {
		$query = "select * from google_calendar gc inner join campaigns c using(campaign_id) where gc.campaign_id = ".$campaign_id;

		return $this->db->query($query)->result_array();
	}

    public function getGoogleCalendar($google_calendar_id) {
        $this->db->select("google_calendar.*", false);
        $this->db->where("google_calendar.google_calendar_id", $google_calendar_id);
        $result = $this->db->get("google_calendar")->result_array();
        return (!empty($result)?$result[0]:array());
    }

    public function get_google_calendars_by_user_and_campaign($user_id, $campaign_id) {
        $this->db->select("google_calendar.*", false);
        $this->db->where("google_calendar.user_id", $user_id);
        $this->db->where("google_calendar.campaign_id", $campaign_id);
        $result = $this->db->get("google_calendar")->result_array();

        return (!empty($result)?$result[0]:array());
    }
	
}