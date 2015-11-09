<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trigger_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('email');
    }

	//send email to the primary contact
    public function send_email_to_contact($template_id,$urn)
    {
		$qry = "select fullname,email from contacts where urn = '$urn'";
		$row = $this->db->query($qry)->row_array();
		if (valid_email($row['email'])) {
		$email_triggers[$template_id] = array("cc" => array(), "email" => array($row['fullname']=>$row['email']), "bcc" => array());
		 $_SESSION['email_triggers'] = $email_triggers;
		  return true;
		}
		return false;
	}
	//send email to the first company email
	public function send_email_to_company($template_id,$urn)
    {
		$qry = "select name,email from companies where urn = '$urn'";
		$row = $this->db->query($qry)->row_array();
		if (valid_email($row['email'])) {
		$email_triggers[$template_id] = array("cc" => array(), "email" => array($row['name']=>$row['email']), "bcc" => array());
		 $_SESSION['email_triggers'] = $email_triggers;
		 return true;
		} 
		return false;
	}
	//send email to all email addresses associated with the record
	public function send_email_to_all($template_id,$urn)
    {
		$qry = "select c.name company_name,con.fullname contact_name,c.email company_email,con.email contact_email from records left join companies c using(urn) left join contacts con using(urn) where urn = '$urn'";
		$results = $this->db->query($qry)->result_array();
		$emails = array();
		foreach($results as $row){
			$emails[$row['contact_name']]=$row['contact_email'];
			$emails[$row['company_name']]=$row['company_email'];
		if (count($emails)>0)  {
		$email_triggers[$template_id] = array("cc" => array(), "email" => $emails, "bcc" => array());
		 $_SESSION['email_triggers'] = $email_triggers;
		 return true;
		}
		}
		return false;
	}
	
	//send sms to the first company mobile
	public function send_sms_to_company($template_id,$urn)
    {
		$qry = "select name company_name,telephone_number company_tel from company_telephone join companies c using(company_id) where urn = '$urn'";
		$results = $this->db->query($qry)->result_array();
		$mobiles = array();
		foreach($results as $row){
			if(preg_match('/^447|^\+447^00447|^07/',$row['company_tel'])){
			$mobiles[$row['company_name']]=$row['company_tel'];
						}
		if (count($mobiles)>0)  {
		$sms_triggers[$template_id] = array("mobiles" => $mobiles);
		 $_SESSION['sms_triggers'] = $sms_triggers;
		 return true;
		}
		}
		return false;
	}
	//send sms to the primary contact mobile
	public function send_sms_to_contact($template_id,$urn)
    {
		$qry = "select fullname contact_name,telephone_number contact_tel from company_telephone join companies c using(company_id) where urn = '$urn'";
		$results = $this->db->query($qry)->result_array();
		$mobiles = array();
		foreach($results as $row){
				if(preg_match('/^447|^\+447^00447|^07/',$row['contact_tel'])){
			$mobiles[$row['contact_name']]=$row['contact_tel'];
			} 
		if (count($mobiles)>0)  {
		$sms_triggers[$template_id] = array("mobiles" => $mobiles);
		 $_SESSION['sms_triggers'] = $sms_triggers;
		 return true;
		}
		}
		return false;
	}
	//send sms to all available mobile numbers
	public function send_sms_to_all($template_id,$urn)
    {
			$qry = "select c.name company_name,con.fullname contact_name,ct.telephone_number contact_tel,cont.telephone_number contact_tel from records left join companies c using(urn) left join contacts con using(urn) join company_telephone ct using(company_id) join contact_telephone cont using(contact_id) where urn = '$urn'";
		$results = $this->db->query($qry)->result_array();
		$emails = array();
		foreach($results as $row){
			if(preg_match('/^447|^\+447^00447|^07/',$row['contact_tel'])){
			$mobiles[$row['contact_name']]=$row['contact_tel'];
			} 
						if(preg_match('/^447|^\+447^00447|^07/',$row['company_tel'])){
			$mobiles[$row['company_name']]=$row['company_tel'];
						}
		if (count($emails)>0)  {
		$sms_triggers[$template_id] = array("mobiles" => $mobiles);
		 $_SESSION['sms_triggers'] = $sms_triggers;
		 return true;
		}
		}
		return false;
	}
	
	
}