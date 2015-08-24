<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trackvia_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }


   public function get_rebookings($campaign = "")
    {
        $qry = "select urn,fullname,campaign_name,if(records.date_updated is null,'Never',date_format(records.date_updated,'%d/%m/%y')) lastcall, if(dials>0,'warning','danger') col from records left join contacts using(urn) left join appointments a using(urn) left join campaigns using(campaign_id) where urgent = 1 and cancellation_reason is not null and record_status = 1";
          if (!empty($campaign)) {
            $qry .= " and campaign_id = '".$campaign."'";
        }
		$qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by urn order by records.date_updated asc";
        return $this->db->query($qry)->result_array();
    }


	public function get_record($urn){
		$query = "select * from records inner join campaigns using(campaign_id) join client_refs using(urn) left join record_details using(urn) left join webform_answers using(urn) left join contacts using(urn) left join contact_addresses using(contact_id) left join contact_telephone using(contact_id) left join outcomes using(outcome_id) left join outcome_reasons using(outcome_reason_id) where urn = '$urn' group by urn";
		$row = $this->db->query($query)->row_array();
		return $row;
	}


	public function get_record_rows($urn){
		$query = "select * from records left join client_refs using(urn) left join record_details using(urn) left join webform_answers using(urn) left join contacts using(urn) left join contact_addresses using(contact_id) left join contact_telephone using(contact_id) left join outcomes using(outcome_id) left join outcome_reasons using(outcome_reason_id) where urn = '$urn'";
		return $this->db->query($query)->result_array();
	}

	public function get_appointment($urn){
	$query = "select a.urn,a.title,a.`text`,client_ref,date(a.`start`) `date`,if(time(`start`)<'12:30:00','am','pm') slot,fullname,campaign_id from records left join (select max(appointment_id) appointment_id, urn from appointments apps group by urn) ma on ma.urn = records.urn left join appointments a using(appointment_id) inner join contacts on contacts.urn = records.urn left join client_refs on records.urn = client_refs.urn left join record_details on record_details.urn = records.urn left join webform_answers on webform_answers.urn = records.urn where a.urn = '$urn' group by a.appointment_id";
		return $this->db->query($query)->row_array();
	}

    /*
     * Get records in our system from trackvia ids (client_ref)
     */
    public function getRecordsByTVIds($tv_record_ids) {
		//having to put a 3000 limit on this or msql timesout
		if(count($tv_record_ids)<3000){
        $qry = "SELECT
                  r.campaign_id,
                  r.urn,
                  cr.client_ref,
                  a.start as survey_datetime,
				  date(a.start) as survey_date,
                  r.parked_code,
				  r.record_status,
				  r.urgent,
				  r.record_color,
				  r.source_id
				from records r
				inner join client_refs cr using(urn)
				left join (select max(appointment_id) appointment_id,urn from appointments group by urn) ma using(urn)
				left join appointments a on a.appointment_id = ma.appointment_id
				WHERE cr.client_ref IN (".implode(',',$tv_record_ids).")";

        return $this->db->query($qry)->result_array();
		} else {
		return array();	
		}
    }


public function update_extra($data){
	  return $this->db->update_batch('record_details', $data, 'urn');
}
    //Update records in our system
    public function updateRecords($records) {
        return $this->db->update_batch('records', $records, 'urn');
    }
	
	//Update notes in our system
    public function updateNotes($notes) {
		 return $this->db->insert_update_batch('sticky_notes', $notes);
    }

	public function create_appointment($fields,$records,$start){
		
		$address = "";
		$postcode = "";
		$app_data = "<br>";
		if(isset($fields['House No.'])){
		$address .= ' '.$fields['House No.'];	
		}
		if(isset($fields['Address 1'])){
		$address .= ' '.$fields['Address 1'];	
		}
		if(isset($fields['Address 2'])){
		$address .= ' '.$fields['Address 2'];	
		}
		if(isset($fields['PostCode'])){
		$address .= ' '.$fields['PostCode'];
		$postcode = postcodeFormat($fields['PostCode']);
		}
		
		foreach($fields as $k=>$v){
		if(!empty($v)){
		$app_data .= "$k: $v<br>";
		}
		}
		//check if exists
		$insert = array("urn"=>$records['urn'],"title"=>"Appointment for survey","text"=>"Appointment set for GHS Survey $app_data","start"=>$start,"end"=>date('Y-m-d H:i:s',strtotime("+1 hour",strtotime($start))),"postcode"=>$postcode,"status"=>1,"created_by"=>"123","date_updated"=>NULL,"date_added"=>date('Y-m-d H:i:s'),"updated_by"=>123,"address"=>$address);
		$insert_query = $this->db->insert_string("appointments",$insert);
		$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
		$this->db->query($insert_query);
		$appointment_id = $this->db->insert_id();
		$this->db->insert("appointment_attendees",array("appointment_id"=>$appointment_id,"user_id"=>"121"));
	}
	
	public function uncancel_appointment($urn,$date){
	$this->db->_protect_identifiers=false;
	$this->db->where(array("urn"=>$urn,"date(start)"=>$date));
	$this->db->update("appointments",array("cancellation_reason"=>NULL,"status"=>1));	
	}
	
	public function cancel_appointment($urn,$start){
		$this->db->_protect_identifiers=false;
		$this->db->where(array("urn"=>$urn,"date(start)"=>$start));
		$this->db->update("appointments",array("cancellation_reason"=>"Needs rebooking","status"=>0,"updated_by"=>1,"date_updated"=>date('Y-m-d H:i:s')));
	}

	public function add_record($data){
		$this->db->insert("records",$data);
		return $this->db->insert_id();
	}
	public function add_client_refs($data){
		$this->db->insert("client_refs",$data);
	}
	public function add_contact($data){
		$this->db->insert("contacts",$data);
		return $this->db->insert_id();
	}
	public function add_address($data){
		$this->db->insert("contact_addresses",$data);
	}
	public function add_telephone($data){
		$this->db->insert("contact_telephone",$data);
	}
	public function add_record_details($data){
		$insert_query =  $this->db->insert_string("record_details",$data);
		$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
		$this->db->query($insert_query);
	}

		
		
		public function get_121_counts($name){		
if($name=="GHS Southway survey"){
	$qry = "select urn from records where campaign_id = 22 and source_id = '34'";
	return $this->db->query($qry)->num_rows();
} else if($name=="GHS Southway rebook"){
	$qry = "select urn from records where campaign_id = 22 and source_id = '35'";
	return $this->db->query($qry)->num_rows();
} else if($name=="GHS Southway booked"){
	$qry = "select urn from records inner join appointments using(urn) where campaign_id = 22 and source_id = '37' and outcome_id = 72 and record_status = 4 and date(`start`) >= curdate()";
	return $this->db->query($qry)->num_rows();
}  else if($name=="GHS Private survey"){
	$qry = "select urn from records where campaign_id = 29 and source_id = '39' ";
	return $this->db->query($qry)->num_rows();
} else if($name=="GHS Private rebook"){
	$qry = "select urn from records where campaign_id = 29 and source_id = '38' ";
	return $this->db->query($qry)->num_rows();
} else if($name=="GHS Private booked"){
	$qry = "select urn from records inner join appointments using(urn) where campaign_id = 29 and source_id = '36'  and record_status = 4 and outcome_id = 72 and date(`start`) >= curdate()";
	return $this->db->query($qry)->num_rows();
} else if($name=="GHS Private not viable"){
	$qry = "select urn from records where campaign_id = 28 and source_id = '40' ";
	return $this->db->query($qry)->num_rows();
}	
}

}