<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Branches_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }
	

	
	public function get_branch_info($id=false,$postcode=false){
		$this->db->where("postcode", $postcode);
        $this->db->join("locations", "location_id=postcode_id", "LEFT");
        $check_location = $this->db->get("uk_postcodes");
        if ($check_location->num_rows()) {
          $coords = $check_location->row_array();
		} else {
		 $coords = postcode_to_coords($postcode);
		}
		$qry ="select br.region_id,b.branch_id,b.branch_name,region_name,consultant,consultant_id,consultant_email,(((ACOS(SIN((" .
                $coords['lat'] . "*PI()/180)) * SIN((lat*PI()/180))+COS((" .
                $coords['lat'] . "*PI()/180)) * COS((lat*PI()/180)) * COS(((" .
                $coords['lng'] . "- lng)*PI()/180))))*180/PI())*60*1.1515) AS distance from branch b join branch_addresses ba using(branch_id) join locations l using(location_id) join branch_regions br using(region_id) join branch_region_users bru using(region_id) left join (select user_id consultant_id,name consultant,user_email consultant_email  from users where attendee=1) consultants on bru.user_id = consultant_id  join branch_user bu on bu.branch_id = b.branch_id join branch_campaigns bc on bc.branch_id = b.branch_id where 1";
		$qry .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
		if($id){
		$qry .= " and br.region_id = '$id' ";	
		}
		$qry .= "  group by b.branch_id order by distance limit 5";

		$result = $this->db->query($qry)->result_array();

		foreach($result as $k=>$row){
		$region_id = $row['region_id'];
		$result[$k]['distance'] = number_format($row['distance'],2). " miles";
		if(!empty($row['consultant_id'])){
		$result[$k]['consultants'][] = array("id"=>$row['consultant_id'],"name"=>$row['consultant'],"email"=>$row['consultant_email']);
		}
		$driver_qry =  "select user_id id,name,user_email email,home_postcode,vehicle_reg from users join branch_region_users using(user_id) where attendee = 0 and region_id = ".$region_id;
		$result[$k]['drivers'] = $this->db->query($driver_qry)->result_array();
		}
		return array("success"=>true,"branches"=>$result);
	}
	
}