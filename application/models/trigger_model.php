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
		$qry = "select company_name,contact_name,c.email company_email,con.email contact_email from records left join companies c usign(urn) left join contacts con using(urn) where urn = '$urn'";
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
		
	}
	//send sms to the primary contact mobile
	public function send_sms_to_contact($template_id,$urn)
    {
		
	}
	//send sms to all available mobile numbers
	public function send_sms_to_all($template_id,$urn)
    {
		
	}
	
	
}