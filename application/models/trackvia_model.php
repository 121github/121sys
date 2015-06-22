	<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trackvia_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }

	public function get_appointment($urn){
	$query = "select a.urn,client_ref,date(a.`start`),if(time(`start`)>'16:00:00','eve',if(time(`start`)<'12:00:00','am','pm')) slot,fullname from records left join (select max(appointment_id) appointment_id, urn from appointments group by urn) ma using(urn) left join appointments a using(appointment_id) inner join contacts on a.contact_id = c.contact_id left join client_refs using(urn) left join record_details using(urn) left join webform_answers using(urn) where a.urn = '$urn' group by a.appointment_id";
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
                  a.start as survey_date,
                  r.parked_code,
				  r.record_status,
				  r.urgent
				from records r
				inner join client_refs cr using(urn)
				left join (select max(appointment_id) appointment_id,urn from appointments group by urn) ma using(urn)
				left join appointments a on a.appointment_id = ma.appointment_id
				WHERE cr.client_ref IN (".implode(',',$tv_record_ids).")";

        return $this->db->query($qry)->result_array();
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
		$this->firephp->log($start);
		$insert = array("urn"=>$records['urn'],"title"=>"Appointment for survey","text"=>"Appointment set for GHS Survey $app_data","start"=>$start,"end"=>date('Y-m-d H:i:s',strtotime("+1 hour",strtotime($start))),"postcode"=>$postcode,"status"=>1,"created_by"=>"1","date_updated"=>NULL,"date_added"=>date('Y-m-d H:i:s'),"updated_by"=>1,"address"=>$address);
		$insert_query = $this->db->insert_string("appointments",$insert);
		$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
		$this->db->query($insert_query);
		$this->firephp->log($insert_query);
	}
	
	public function cancel_appointment($urn,$start){
		$this->db->where(array("urn"=>$urn,"start"=>$start));
		$this->db->update("appointments",array("cancellation_reason"=>"Needs rebooking","updated_by"=>1,"date_updated"=>date('Y-m-d H:i:s')));
	}

}