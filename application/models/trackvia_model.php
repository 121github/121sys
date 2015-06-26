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
        $qry = "select urn,fullname,campaign_name,date_format(a.start,'%d/%m/%y') cancelled_date,if(time(`start`)>'16:00:00','eve',if(time(`start`)<'12:00:00','am','pm')) cancelled_slot from records left join contacts using(urn) left join appointments a using(urn) left join campaigns using(campaign_id) where urgent = 1 and cancellation_reason is not null";
          if (!empty($campaign)) {
            $qry .= " and campaign_id = '".$campaign."'";
        }
		$qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by urn order by a.start asc";
        return $this->db->query($qry)->result_array();
    }


	public function get_record($urn){
		$query = "select * from records inner join campaigns using(campaign_id) inner join client_refs using(urn) left join record_details using(urn) left join webform_answers using(urn) left join contacts using(urn) left join contact_addresses using(contact_id) left join contact_telephone using(contact_id) left join outcomes using(outcome_id) left join outcome_reasons using(outcome_reason_id) where urn = '$urn' group by urn";
		return $this->db->query($query)->row_array();
	}


	public function get_record_rows($urn){
		$query = "select * from records inner join client_refs using(urn) left join record_details using(urn) left join webform_answers using(urn) left join contacts using(urn) left join contact_addresses using(contact_id) left join contact_telephone using(contact_id) left join outcomes using(outcome_id) left join outcome_reasons using(outcome_reason_id) where urn = '$urn'";
		return $this->db->query($query)->result_array();
	}

	public function get_appointment($urn){
	$query = "select a.urn,a.title,a.`text`,client_ref,date(a.`start`) `date`,if(time(`start`)>'16:00:00','eve',if(time(`start`)<'12:00:00','am','pm')) slot,fullname from records left join (select max(appointment_id) appointment_id, urn from appointments apps group by urn) ma on ma.urn = records.urn left join appointments a using(appointment_id) inner join contacts on contacts.urn = records.urn left join client_refs on records.urn = client_refs.urn left join record_details on record_details.urn = records.urn left join webform_answers on webform_answers.urn = records.urn where a.urn = '$urn' group by a.appointment_id";
		return $this->db->query($query)->row_array();
	}

    /*
     * Get records in our system from trackvia ids (client_ref)
     */
    public function getRecordsByTVIds($tv_record_ids) {
        $qry = "SELECT
                  r.campaign_id,
                  r.urn,
                  cr.client_ref,
                  a.start as survey_datetime,
				  date(a.start) as survey_date,
                  r.parked_code,
				  r.record_status,
				  r.urgent,
				  r.record_color
				from records r
				inner join client_refs cr using(urn)
				left join (select max(appointment_id) appointment_id,urn from appointments group by urn) ma using(urn)
				left join appointments a on a.appointment_id = ma.appointment_id
				WHERE cr.client_ref IN (".implode(',',$tv_record_ids).")";

        return $this->db->query($qry)->result_array();
    }


public function update_extra($data){
	  return $this->db->update_batch('record_details', $data, 'urn');
}
    //Update records in our system
    public function updateRecords($records) {
        return $this->db->update_batch('records', $records, 'urn');
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

		$insert = array("urn"=>$records['urn'],"title"=>"Appointment for survey","text"=>"Appointment set for GHS Survey $app_data","start"=>$start,"end"=>date('Y-m-d H:i:s',strtotime("+1 hour",strtotime($start))),"postcode"=>$postcode,"status"=>1,"created_by"=>"1","date_updated"=>NULL,"date_added"=>date('Y-m-d H:i:s'),"updated_by"=>1,"address"=>$address);
		$insert_query = $this->db->insert_string("appointments",$insert);
		$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
		$this->db->query($insert_query);

	}
	
	public function cancel_appointment($urn,$start){
		$this->db->_protect_identifiers=false;
		$this->db->where(array("urn"=>$urn,"date(start)"=>$start));
		$this->db->update("appointments",array("cancellation_reason"=>"Needs rebooking","updated_by"=>1,"date_updated"=>date('Y-m-d H:i:s')));
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
		$this->db->insert("record_details",$data);
	}

		
		
		public function get_121_counts($name){
		$pots = array(
		"GHS Southway Total"=>3000283398,
		"GHS Private Total"=>3000283421,
		"GHS Southway survey"=>3000719114,
		"GHS Southway rebook"=>3000719115,
		"GHS Southway booked"=>3000719175,
		"GHS Private survey"=>3000718982,
		"GHS Private rebook"=>3000718984,
		"GHS Private booked"=>3000719187,
		"GHS Private not viable"=>3000718985);
		
if($name=="GHS Southway Total"){
	$qry = "select * from records where campaign_id = 22";
	return $this->db->query($qry)->num_rows();
}
if($name=="GHS Private Total"){
	$qry = "select * from records where campaign_id in(28,29)";
	return $this->db->query($qry)->num_rows();
}
if($name=="GHS Southway survey"){
	$qry = "select * from records where source_id = '34' and record_";
	return $this->db->query($qry)->num_rows();
}
if($name=="GHS Southway rebook"){
	$qry = "select * from records where urn = 22";
	return $this->db->query($qry)->num_rows();
}
if($name=="GHS Southway booked"){
	$qry = "select * from records where urn = 22";
	return $this->db->query($qry)->num_rows();
}
if($name=="GHS Private survey"){
	$qry = "select * from records where urn = 22";
	return $this->db->query($qry)->num_rows();
}
	
if($name=="GHS Private rebook"){
	$qry = "select * from records where urn = 22";
	return $this->db->query($qry)->num_rows();
}
if($name=="GHS Private booked"){
	$qry = "select * from records where urn = 22";
	return $this->db->query($qry)->num_rows();
}
if($name=="GHS Private not viable"){
	$qry = "select * from records where urn = 22";
	return $this->db->query($qry)->num_rows();
}	
}

}