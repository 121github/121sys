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
    public function get_events($start,$end) {
	//$where = " and campaign_id in(".$_SESSION['campaign_access']['list'].")";
	$where = "";
	if($start){
		$where .= " and date(`start`) >= '$start' ";
	} 
	if($end){
		$where .= " and date(`start`) <= '$end' ";
	} 
	if(isset($_SESSION['current_campaign'])){
		$where .= " and campaign_id = '{$_SESSION['current_campaign']}' ";
	} 
		
	$query = "select appointment_id id,title, start, end, group_concat(distinct user_id separator ',') attendees, icon, appointment_type from appointments left join appointment_attendees using(appointment_id) join records using(urn) left join appointment_types using(appointment_type_id) where 1 ";
	$query .= $where;
	$query .= " group by appointment_id";
	$this->firephp->log($query);
       return $this->db->query($query)->result_array();
	}
	
	public function set_event_time($id,$start,$end){
		$this->db->where(array("appointment_id"=>$id));
		$this->db->update("appointments",array("start"=>$start,"end"=>$end));	
		$this->firephp->log($this->db->last_query());
	}
	
}