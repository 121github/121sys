<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Modal_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }
	public function view_record($urn){
		$qry = "select r.urn,r.nextcall,status_name,campaign_name,if(outcome is null,'New',outcome) outcome,if(comments is null,'n/a',comments) comments ,if(com.name,com.name,con.fullname) name, if(r.date_updated is null,'n/a',date_format(r.date_updated,'%D %M %Y')) lastcall, date_format(r.nextcall,'%D %M %Y') nextcall from records r left join status_list sl on sl.record_status_id = r.record_status left join campaigns using(campaign_id) left join contacts con using(urn) left join companies com using(urn) left join outcomes using(outcome_id) left join (select max(history_id) mhid,urn from history where comments <> '' group by urn) mhis using(urn) left join history h on h.history_id = mhis.mhid where r.urn = '$urn'";
		$this->firephp->log($qry);
		return $this->db->query($qry)->row_array();
	}
	
	public function view_appointment($id,$postcode=false){
		$distance_query = "";
		if($postcode){
		$coords = postcode_to_coords($postcode);
		$distance_query = ",(((ACOS(SIN((" .
              $coords['lat'] . "*PI()/180)) * SIN((lo.lat*PI()/180))+COS((" .
              $coords['lat'] . "*PI()/180)) * COS((lo.lat*PI()/180)) * COS(((" .
              $coords['lng'] . "- lo.lng)*PI()/180))))*180/PI())*60*1.1515) distance";
		}
	$query = "select u.user_id,c.name coname,appointment_id,urn,title,text,date_format(start,'%d/%m/%Y %H:%i') start,date_format(start,'%W %D %M %Y %l:%i%p') starttext,date_format(end,'%d/%m/%Y %H:%i') end,postcode,a.status,(select name from users where user_id = a.created_by) created_by,date_format(date_added,'%d/%m/%Y') date_added, u.name attendee, appointment_type, appointment_type_id as `type`, address  $distance_query from appointments a left join appointment_types using(appointment_type_id) left join locations lo using(location_id) left join appointment_attendees aa using(appointment_id) left join users u on u.user_id = aa.user_id left join companies c using(urn) where appointment_id = '$id' ";
	return $this->db->query($query)->result_array();
	}
	
}