<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
        
    }
    
	public function get_placeholder_data($urn=NULL){
		$user_qry="";
		if(isset($_SESSION['user_id'])){
		$user_qry = " ,(select name as user from users where user_id = '{$_SESSION['user_id']}') user ";	
			
		}
		$qry = "select records.urn,date_format(nextcall,'%d/%m/%Y %H:%i') nextcall,date_format(records.date_updated,'%d/%m/%Y %H:%i') lastcall,outcome,dials,status_name, records.urgent,if(campaign_type_id = 1,fullname,companies.name) contact, companies.name company,companies.description,companies.website,companies.company_number,contacts.fullname,contacts.gender,contacts.position,contacts.dob,contacts.email,if(contact_telephone.telephone_number is null,company_telephone.telephone_number,contact_telephone.telephone_number) telephone, a.`title`,a.`text`,date_format(`start`,'%d/%m/%Y %H:%i') `start`,a.`end`,a.`date_added`,if(attendees.name is null,'Sir/Madam',attendees.name) attendee $user_qry from records left join outcomes using(outcome_id) left join campaigns using(campaign_id) left join status_list on record_status = record_status_id left join companies using(urn) left join contacts using(urn) left join contact_telephone using(contact_id) left join company_telephone using(company_id) left join record_details using(urn) left join (select urn,max(appointment_id) max_id from appointments where urn='$urn') a_id using (urn) left join appointments a on a.appointment_id = a_id.max_id  left join appointment_attendees using(appointment_id) left join users attendees on appointment_attendees.user_id = attendees.user_id where records.urn = '$urn'";
		//$this->firephp->log($qry);
		return $this->db->query($qry)->result_array();
		
	}
	
    public function template_to_form($template_id){
		$form=array();		
		
		$this->db->where('template_id',$template_id);	
		$result = $this->db->get('email_templates')->result_array();
		foreach($result as $row){
			$form['template_id'] = $row['template_id'];
			$form['subject'] = $row['template_subject'];
			$form['body'] = $row['template_body'];
			$form['send_from'] = $row['template_from'];
			$form['send_to'] = "";
			$form['cc'] = $row['template_cc'];
			$form['bcc'] = $row['template_bcc'];
		}
		return $form;
		
	}
	
    //function to return all data for the survey notification email
    public function survey_email($survey_id = "")
    {
        if (!empty($survey_id)) {
            $qry = "select urn, campaign_id, nps_question, answer,user_id,notes, survey_name, contact_id, survey_id, urgent from survey_answers left join questions using(question_id)  left join survey_info using(survey_info_id) left join surveys using(survey_id) left join answer_notes using(answer_id) left join records using(urn) where survey_id= '$survey_id'";
        }
        return $this->db->query($qry)->result_array();
    }
    
    /**
     * Get the templates
     */
    public function get_templates()
    {
        $this->db->select("*");
        $this->db->from("email_templates t");
        return $this->db->get()->result_array();
    }
    
    /**
     * Get the campaings for all the existing templates
     */
    public function get_campaigns_by_templates()
    {
        $this->db->select("c.*");
        $this->db->from("email_template_to_campaigns c");
        $this->db->join("email_templates c", "c.template_id = t.template_id");
        return $this->db->get()->result_array();
    }
    
    /**
     * Get a template
     *
     * @param integer $id
     * @return Template
     */
    public function get_template($id)
    {
        $this->db->select("*");
        $this->db->where("template_id", $id);
        
        $results = $this->db->get("email_templates")->result_array();
        return $results[0];
    }
    
    
    /**
     * Get the campaings by template
     */
    public function get_campaigns_by_template_id($id)
    {
        $this->db->select("c.*");
        $this->db->from("email_template_to_campaigns c");
        $this->db->where("c.template_id", $id);
        return $this->db->get()->result_array();
    }
    
    /**
     * Get the attachments by template
     */
    public function get_attachments_by_template_id($id)
    {
        $this->db->select("a.*");
        $this->db->from("email_template_attachments a");
        $this->db->where("a.template_id", $id);
        return $this->db->get()->result_array();
    }

    /**
     * Get the attachments by email_id
     */
    public function get_attachments_by_email_id($id)
    {
        $this->db->select("a.*");
        $this->db->from("email_history_attachments a");
        $this->db->where("a.email_id", $id);
        return $this->db->get()->result_array();
    }
    
    /**
     * Add a new template
     *
     * @param Form $form
     */
    public function add_new_template($form)
    {
        $this->db->insert("email_templates", $form);
        
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
        
    }
    
    /**
     * Update a template
     *
     * @param Form $form
     */
    public function update_template($form)
    {
        $this->db->where("template_id", $form['template_id']);
        return $this->db->update("email_templates", $form);
    }
    
    /**
     * Remove a template
     *
     * @param integer $id
     */
    public function delete_template($id)
    {
        $this->db->where("template_id", $id);
        return $this->db->delete("email_templates");
    }
    
    /**
     * Insert the campaings for a template
     */
    public function insert_campaigns_by_template_id($template_id, $campaignList)
    {
        $response = true;
        
        foreach ($campaignList as $campaign) {
            if (!$this->db->insert("email_template_to_campaigns", array(
                "template_id" => $template_id,
                "campaign_id" => $campaign
            ))) {
                $response = false;
            }
        }
        
        return $response;
    }
    
    /**
     * Remove the campaings by template
     */
    public function delete_campaigns_by_template_id($template_id, $campaignList)
    {
        
        $this->db->where("template_id", $template_id);
        $this->db->where_in("campaign_id", $campaignList);
        return $this->db->delete("email_template_to_campaigns");
    }
    
    
    /**
     * Insert a new attachment by template
     */
    public function insert_attachment_by_template_id($template_id, $attachment)
    {
        
        $response = $this->db->insert("email_template_attachments", array(
            "template_id" => $template_id,
            "name" => $attachment['name'],
            "path" => $attachment['path']
        ));
        
        return $response;
    }
    
    /**
     * Insert a new attachment by email history id
     */
    public function insert_attachment_by_email_id($email_id, $attachment)
    {
        
        $response = $this->db->insert("email_history_attachments", array(
            "email_id" => $email_id,
            "name" => $attachment['name'],
            "path" => $attachment['path']
        ));
        
        return $response;
    }
    
    /**
     * Remove all the attachments by template
     */
    public function delete_attachments_by_template_id($template_id)
    {
        
        $this->db->where("template_id", $template_id);
        return $this->db->delete("email_template_attachments");
    }
    
    
    /**
     * Remove an attachment by id
     */
    public function delete_attachment_by_id($attachment_id)
    {
        
        $this->db->where("id", $attachment_id);
        return $this->db->delete("email_template_attachments");
    }
    
    /**
     * Get emails history
     */
    public function get_emails($urn, $limit, $offset)
    {

        $qry = "select e.email_id,
                      DATE_FORMAT(e.sent_date,'%d/%m/%Y %H:%i:%s') as sent_date,
                      e.subject,
                      e.body,
                      e.send_from,
                      e.send_to,
                      e.cc,
                      e.bcc,
                      e.user_id,
                      e.urn,
                      e.template_id,
                      e.read_confirmed,
                      u.*,
                      t.*
		    	from email_history e
		    	inner join users u ON (u.user_id = e.user_id)
		    	inner join email_templates t ON (t.template_id = e.template_id)
		    	where e.urn = " . $urn."
		    	order by e.sent_date desc
		    	limit ".$offset.",".$limit;

        return $this->db->query($qry)->result_array();
    }
    
    /**
     * Get email history by id
     */
    public function get_email_by_id($email_id)
    {
        $qry = "select e.email_id,
                      DATE_FORMAT(e.sent_date,'%d/%m/%Y %H:%i:%s') as sent_date,
                      e.subject,
                      e.body,
                      e.send_from,
                      e.send_to,
                      e.cc,
                      e.bcc,
                      e.user_id,
                      e.urn,
                      e.template_id,
                      e.read_confirmed,
                      u.*,
                      t.*
		    	from email_history e
		    	inner join users u ON (u.user_id = e.user_id)
		    	inner join email_templates t ON (t.template_id = e.template_id)
		    	where e.email_id = " . $email_id;

        $results =  $this->db->query($qry)->result_array();
        
        return $results[0];
    }
    
    /**
     * Add a new email to the history
     *
     * @param Form $form
     */
    public function add_new_email_history($form)
    {
        $this->db->insert("email_history", $form);
        
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
        
    }
    
    /**
     * Delete an email from the history
     */
    public function delete_email($email_id)
    {
        $this->db->where("email_id", $email_id);
        return $this->db->delete("email_history");
    }
    
    public function get_recipients($campaign_id, $outcome_id)
    {
        $recipients = array();
        $this->db->where(array(
            "outcome_id" => $outcome_id,
            "campaign_id" => $campaign_id
        ));
        $this->db->join("email_trigger_recipients", "email_triggers.trigger_id = email_trigger_recipients.trigger_id", "LEFT");
        $this->db->join("users", "users.user_id = email_trigger_recipients.user_id", "LEFT");
        
        $result = $this->db->get("email_triggers")->result_array();
        foreach ($result as $row) {
            $recipients[$row['name']] = $row['user_email'];
        }
        return $recipients;
    }
    
}