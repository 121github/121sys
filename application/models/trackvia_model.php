	<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trackvia_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }

    /*
     * Get records in our system from trackvia ids (client_ref)
     */
    public function getRecordsByTVIds($tv_record_ids) {
        $qry = "SELECT
                  r.campaign_id,
                  r.urn,
                  cr.client_ref,
                  rd.dt1 as survey_date,
                  r.parked_code,
				  r.record_status
				from records r
				inner join client_refs cr using(urn)
				left join record_details rd using(urn)
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
		if(isset($fields['House No.'])){
		$address .= ' '.$fields['House No.'];	
		}
		if(isset($fields['Address 1'])){
		$address .= ' '.$fields['Address 1'];	
		}
		if(isset($fields['Address 2'])){
		$address .= ' '.$fields['Address 2'];	
		}
		if(isset($fields['Postcode'])){
		$address .= ' '.$fields['Postcode'];
		$postcode = postcodeFormat($fields['Postcode']);
		}
		

		$insert = array("urn"=>$records['urn'],"title"=>"Appointment for survey","text"=>"Appointment set by GHS","start"=>$start,"end"=>date('Y-m-d H:i:s',strtotime($start,"+1 hour")),"postcode"=>$postcode,"status"=>1,"created_by"=>"1","date_updated"=>NULL,"date_added"=>date('Y-m-d H:i:s'),"updated_by"=>1,"address"=>$address);
		$insert_query = $this->db->insert_string("appointments",$insert);
		$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
		$this->db->query($insert_query);
	}
	
	public function cancel_appointment($urn,$start){
		$this->db->where(array("urn"=>$urn,"start"=>$start));
		$this->db->update("appointments",array("cancellation_reason"=>"Needs rebooking","updated_by"=>1,"date_updated"=>date('Y-m-d H:i:s')));
	}

}