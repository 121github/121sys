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
	
	
	public function get_webform_answers($webform_id=false,$urn=false){
	$qry = "select * from webform_answers where urn = '".intval($urn)."' ";
	if($webform_id){
	$qry .= " and webform_id = '".intval($webform_id)."' ";	
	}
	return $this->db->query($qry)->row_array();	
	}
	
	public function check_form_permission($email_id,$urn,$campaign_id){
		//check the email id matches the urn so custoemrs can only see their own form
		$qry = "select * from records left join email_history using(urn) where email_history.urn = '".intval($urn)."' and email_id = '".intval($email_id)."' and campaign_id = '".intval($campaign_id)."'";
		//$this->firephp->log($qry);
		if($this->db->query($qry)->num_rows()>0){
		return true;	
		}
	}
	
	public function get_path($form){
	$this->db->where('webform_id',$form);
	return $this->db->get('webforms')->row()->webform_path;
	}
	
    public function get_all_data($urn,$campaign_id,$form,$appointment_id=false)
    {
		
		
		
		$data = array();
		//get appointment details
		if($appointment_id){
		$qry = "select *,date_format(appointments.start,'%d/%m/%Y %H:%i') start_datetime,date_format(appointments.start,'%d/%m/%Y') start_date,date_format(appointments.start,'%H:%i') start_time,appointment_type from appointments join appointment_types using(appointment_type_id) join users on user_id = created_by join contacts using(contact_id) join contact_telephone using(contact_id) where appointment_id = '".$appointment_id."'";
		$data['appointment'] = $this->db->query($qry)->row_array();
		}
		
		
		$qry = "select * from webform_answers wa where webform_id = '".intval($form)."' and wa.urn = '".intval($urn)."'";
		$qry .= ($appointment_id?" and appointment_id = '".$appointment_id."' ":"");	
		$data['values'] = $this->db->query($qry)->row_array();
		$data['values']['appointment_id'] = $appointment_id;
		$custom_strings = custom_fields("strings");
		$custom_numbers = custom_fields("numbers");
		$custom_field_list = implode(",",$custom_strings).",".implode(",",$custom_numbers);
		$qry = "select company_id,c.contact_id,co.name, co.website,cot.telephone_number cophone,ct.telephone_number cphone,coa.add1,coa.add2,coa.add3,coa.county,coa.postcode,co.email,c.fullname,date_format(c.dob,'%d/%m/%Y') dob,coa.country,c.email,$custom_field_list,date_format(d1,'%d/%m/%Y') d1,date_format(d2,'%d/%m/%Y') d2,date_format(d3,'%d/%m/%Y') d3,date_format(d4,'%d/%m/%Y') d4,date_format(d5,'%d/%m/%Y') d5,date_format(d6,'%d/%m/%Y') d6,date_format(d7,'%d/%m/%Y') d7,date_format(d8,'%d/%m/%Y') d8,date_format(d9,'%d/%m/%Y') d9,date_format(d10,'%d/%m/%Y') d10,dt1,dt2,dt3,dt4,dt5,dt6,dt7,dt8,dt9,dt10 from records left join record_details using(urn) left join contacts c using(urn) left join contact_telephone ct using(contact_id) left join companies co using(urn) left join company_telephone cot using(company_id) left join contact_addresses ca using(contact_id) left join company_addresses coa using(company_id) left join email_history eh using(urn) where urn ='".intval($urn)."' and campaign_id ='".intval($campaign_id)."' group by contact_id";
		//$this->firephp->log($qry);
		$result = $this->db->query($qry)->result_array();
		foreach($result as $row){
			$data['company'][$row['company_id']] = array("name"=>$row['name'],"website"=>$row['website'],"phone"=>$row['cophone'],"add1"=>$row['add1'],"add2"=>$row['add2'],"add3"=>$row['add3'],"county"=>$row['county'],"country"=>$row['country'],"postcode"=>$row['postcode'],"email"=>$row['email']);
			$data['contacts'][$row['contact_id']] = array("contact_id"=>$row['contact_id'],"name"=>$row['fullname'],"email"=>$row['email'],"website"=>$row['website'],"phone"=>$row['cphone'],"add1"=>$row['add1'],"add2"=>$row['add2'],"add3"=>$row['add3'],"county"=>$row['county'],"country"=>$row['country'],"postcode"=>$row['postcode'],"email"=>$row['email'],"dob"=>$row['dob']);
			$custom_fields = custom_fields();
			foreach($custom_fields as $custom){
			$data['custom'][$custom] = $row[$custom];
			}
		}
		$data['urn'] = $urn;
		return $data;
	}
	public function update_contact($contact){
		$this->db->where("contact_id",$contact['contact_id']);
	$this->db->update("contacts",$contact);	
	}
	
	public function save_answer($data){
		$appointment_id = $data['appointment_id']?intval($data['appointment_id']):"NULL";
		$urn = intval($data['urn']);
		$webform_id = intval($data['webform_id']);
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
		$qry_end = "webform_answers set updated_by=$user_id,webform_id=$webform_id,urn=$urn,appointment_id=$appointment_id $answers";
		if(!empty($data['complete'])){
		$qry_end .= ",completed_on=now(),completed_by=".$_SESSION['user_id'];	
		}
		$qry = $qry_start.$qry_end;
		if(!$data['id']){
		$result = $this->db->query($qry);
		$id = $this->db->insert_id();
		} else {
			$id = intval($data['id']);
			$qry_start = "update ";
			$qry_where = " where id='".$id."'";
			$qry = $qry_start.$qry_end.$qry_where;
			 $result = $this->db->query($qry); 
		 }
		return $id;
	}
	
}