<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*  This page contains functiosn to populate dropdown menus on forms and filters. The queries simply return each id and value in the table in the format id=>name */
class Webform_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
	
	
	public function check_form_permission($email_id,$urn,$campaign_id){
		//check the email id matches the urn so custoemrs can only see their own form
		$qry = "select * from records left join email_history using(urn) where email_history.urn = '$urn' and email_id = '$email_id' and campaign_id = '$campaign_id'";
		//$this->firephp->log($qry);
		if($this->db->query($qry)->num_rows()>0){
		return true;	
		}
	}
	
	public function get_path($form){
	$this->db->where('webform_id',$form);
	return $this->db->get('webforms')->row()->webform_path;
	}
	
    public function get_all_data($urn,$campaign_id,$form)
    {
		
		$data = array();
		$qry = "select * from webform_answers where webform_id = '$form' and urn = '$urn'";
				
		$data['values'] = $this->db->query($qry)->row_array();
		$qry = "select c.contact_id,co.name, co.website,cot.telephone_number cophone,ct.telephone_number cphone,coa.add1,coa.add2,coa.add3,coa.county,coa.postcode,co.email,c.fullname,date_format(c.dob,'%d/%m/%Y') dob,coa.country,c.email,c1 from records left join record_details using(urn) left join contacts c using(urn) left join contact_telephone ct using(contact_id) left join companies co using(urn) left join company_telephone cot using(company_id) left join contact_addresses ca using(contact_id) left join company_addresses coa using(company_id) left join email_history eh using(urn) where urn ='$urn' and campaign_id ='$campaign_id' group by urn";
		//$this->firephp->log($qry);
		$result = $this->db->query($qry)->result_array();
		foreach($result as $row){
			$data['company'] = array("name"=>$row['name'],"website"=>$row['website'],"phone"=>$row['cophone'],"add1"=>$row['add1'],"add2"=>$row['add2'],"add3"=>$row['add3'],"county"=>$row['county'],"country"=>$row['country'],"postcode"=>$row['postcode'],"email"=>$row['email']);
			$data['contact'] = array("contact_id"=>$row['contact_id'],"name"=>$row['fullname'],"email"=>$row['email'],"website"=>$row['website'],"phone"=>$row['cphone'],"add1"=>$row['add1'],"add2"=>$row['add2'],"add3"=>$row['add3'],"county"=>$row['county'],"country"=>$row['country'],"postcode"=>$row['postcode'],"email"=>$row['email'],"dob"=>$row['dob']);
			$data['custom'] = array("c1"=>$row['c1']);
		}
		
		return $data;
	}
	public function update_contact($contact){
		$this->db->where("contact_id",$contact['contact_id']);
	$this->db->update("contacts",$contact);	
	}
	
	public function save_answer($data){
		$urn = intval($data['urn']);
		$webform_id = intval($data['id']);
		$user_id = $_SESSION['user_id'];
		$answers = "";
		foreach($data['answers'] as $column => $answer){
	    $a = $this->db->escape($answer);
		if(!empty($a)){
			$answers .= ",`$column` = $a";
		}
		}
		//first we try an insert
		$qry_start = "insert ignore into ";
		$qry_end = "webform_answers set updated_by=$user_id,webform_id=$webform_id,urn=$urn $answers";
		if(!empty($data['complete'])){
		$qry_end .= ",completed_on=now(),completed_by=".$_SESSION['user_id'];	
		}
		$qry = $qry_start.$qry_end;
		$result = $this->db->query($qry);
		 //if no insert was done (because the webform exists) then we update 
		 if($this->db->insert_id()==0){
			$qry_start = "update ";
			$qry_where = " where urn = '$urn' and webform_id = '$webform_id' ";
			$qry = $qry_start.$qry_end.$qry_where;

			 $result = $this->db->query($qry); 
		 }
		return $result;
	}
	
}