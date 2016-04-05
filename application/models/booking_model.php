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
    public function get_events($start,$end,$attendee) {
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
	if(isset($_SESSION['current_campaign'])){
		$where .= " and campaign_id = '{$_SESSION['current_campaign']}' ";
	} 
		
	$query = "select appointment_id id,title, start, end, text, group_concat(distinct user_id separator ',') attendees, icon, appointment_type from appointments left join appointment_attendees using(appointment_id) join records using(urn) left join appointment_types using(appointment_type_id) where 1 ";
	$query .= $where;
	$query .= " group by appointment_id";
	//$this->firephp->log($query);
       return $this->db->query($query)->result_array();
	}
	
	public function set_event_time($id,$start,$end){
		$this->db->where(array("appointment_id"=>$id));
		$this->db->update("appointments",array("start"=>$start,"end"=>$end));	
		//$this->firephp->log($this->db->last_query());
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
	
}