<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_webform_id($campaign)
    {
        $this->db->where("campaign_id", $campaign);
        $query = $this->db->get("webforms_to_campaigns");
        if ($query->num_rows() > 0) {
            return $query->row()->webform_id;
        } else {
            return "0";
        }
    }

    public function get_placeholder_data($urn = NULL)
    {
        $user_qry = "";

        if (isset($_SESSION['user_id'])) {
            $user_qry = " ,(select name as user from users where user_id = '{$_SESSION['user_id']}') user,(select user_email from users where user_id = '{$_SESSION['user_id']}') user_email, (select user_telephone from users where user_id = '{$_SESSION['user_id']}') user_telephone ";
        }
        //check if an appointment has been made and use the appointment contact in the placeholder
        $query = "select urn from appointments where urn = '$urn' and contact_id is not null";
        if ($this->db->query($query)->num_rows() > 0) {
            $contact_details = " left join (select urn,max(appointment_id) max_id from appointments where urn='$urn') a_id using (urn) left join appointments a on a.appointment_id = a_id.max_id left join contacts using(contact_id) left join contact_telephone using(contact_id) left join contact_addresses ca using(contact_id) left join appointment_attendees using(appointment_id) left join appointment_types using(appointment_type_id) left join users attendees on appointment_attendees.user_id = attendees.user_id where records.urn = '$urn'";
            $attendee = " if(attendees.name is null,'Sir/Madam',attendees.name) attendee ";
            $appointment_fields = " appointment_type, if(a.address<>'',a.address,'') address, a.`title`,a.`text`,date_format(`start`,'%d/%m/%Y %H:%i') `start`,a.`end`,a.`date_added`,date_format(`start`,'%d/%m/%Y') `appointment_date`,if(time(`start`)<'12:30:00','am','pm') time_slot, ";
        } else {
            $contact_details = " left join contacts using(urn) left join contact_telephone using(contact_id) left join contact_addresses ca using(contact_id) where records.urn = '$urn'";
            $attendee = " 'Sir/Madam' attendee ";
            $appointment_fields = "";
        }

        $qry = "select records.urn,campaign_name,date_format(nextcall,'%d/%m/%Y %H:%i'), date_format(curdate(),'%d/%m/%Y') `date`, nextcall,date_format(records.date_updated,'%d/%m/%Y %H:%i') lastcall,outcome,dials,status_name, records.urgent,if(campaign_type_id = 1,fullname,if(fullname is not null,concat(fullname,' from ', companies.name),companies.name)) contact,if(employees is null,'Unknown',employees) employees, companies.name company,records.campaign_id,companies.description,companies.website,companies.conumber,contacts.fullname,contacts.gender,contacts.position,date_format(contacts.dob,'%d/%m/%Y') dob,if(contacts.email is not null,contacts.email,'') email,if(contact_telephone.telephone_number is null,company_telephone.telephone_number,contact_telephone.telephone_number) telephone,$appointment_fields $attendee,c1,c2,c3,c4,c5,d1,d2,dt1,dt2,n1,n2,n3, concat(ca.add1,' ',ca.add2,', ',ca.postcode) contact_address,
		 concat(coa.add1,' ',coa.add2,', ',coa.postcode) company_address, if(campaign_type_id=1,ca.add1,coa.add1) add1, if(campaign_type_id=1,ca.add2,coa.add2) add2, if(campaign_type_id=1,ca.add3,coa.add3) add3,if(campaign_type_id=1,ca.county,coa.county) county,if(campaign_type_id=1,ca.postcode,coa.postcode) postcode, $user_qry from records left join outcomes using(outcome_id) left join campaigns using(campaign_id) left join status_list on record_status = record_status_id left join companies using(urn) left join company_telephone using(company_id) left join company_addresses coa using(company_id) left join record_details using(urn) ";
        $qry .= $contact_details;
$qry .= " group by urn";
        return $this->db->query($qry)->result_array();

    }

    public function template_to_form($template_id)
    {
        $form = array();

        $this->db->where('template_id', $template_id);
        $result = $this->db->get('email_templates')->result_array();
        foreach ($result as $row) {
            $form['template_id'] = $row['template_id'];
            $form['subject'] = $row['template_subject'];
            $form['body'] = $row['template_body'];
            $form['send_from'] = $row['template_from'];
            $form['send_to'] = "";
            $form['cc'] = $row['template_cc'];
            $form['bcc'] = $row['template_bcc'];
            $form['template_unsubscribe'] = $row['template_unsubscribe'];
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
        $this->db->order_by("template_name");
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

    public function check_email_history($template_id, $urn)
    {
        $this->db->where(array("template_id" => $template_id, "urn" => $urn));
        $result = $this->db->get("email_history");
        if ($result->num_rows()) {
            return true;
        }
    }

    public function check_unsubscribed($send_to, $client)
    {
        $list = explode(",", $send_to);
        foreach ($list as $email) {
            $this->db->where(array("email_address" => $email, "client_id" => $client));
            if ($this->db->get("email_unsubscribe")->num_rows()) {
                return $email;
            }
        }
    }

    public function unsubscribe($data)
    {
        return $this->db->replace("email_unsubscribe", $data);
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
            ))
            ) {
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
        $limit_ = ($limit) ? "limit " . $offset . "," . $limit : '';

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
                      e.read_confirmed_date,
                      e.status,
                      u.*,
                      t.*,
                      e.pending
		    	from email_history e
		    	inner join users u ON (u.user_id = e.user_id)
		    	inner join email_templates t ON (t.template_id = e.template_id)
		    	where e.urn = " . $urn . "
		    	order by e.sent_date desc
		    	" . $limit_;

        return $this->db->query($qry)->result_array();
    }

    /**
     * Get emails history by filter data
     */
    public function get_emails_by_filter($options)
    {

        $date_from = $options['date_from'];
        $agent = $options['agent'];
        $date_to = $options['date_to'];
        $template = $options['template'];
        $campaign = $options['campaign'];
        $team_manager = $options['team'];
        $source = $options['source'];
        $id = $options['id'];
        $group = $options['group'];
        $sent = $options['sent'];
        $read = $options['read'];
        $pending = $options['pending'];

        if ($id != "TOTAL") {
            if ($group == "agent") {
                $agent = $options['id'];
            } else if ($group == "date") {
                $date = $options['id'];
            } else if ($group == "time") {
                $hour = $options['id'];
            } else if ($group == "campaign") {
                $campaign = $options['id'];
            }
        }

        $where = "";
        if (!empty($date_from)) {
            $where .= " and date(eh.sent_date) >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(eh.sent_date) <= '$date_to' ";
        }
        if (isset($date)) {
            $where .= " and date(eh.sent_date) = '$date' ";
        }
        if (isset($hour)) {
            $where .= " and hour(eh.sent_date) = '$hour' ";
        }
        if (!empty($template)) {
            $where .= " and eh.template_id = '$template' ";
        }
        if (!empty($campaign)) {
            $where .= " and c.campaign_id = '$campaign' ";
        }
        if (!empty($team_manager)) {
            $where .= " and u.team_id = '$team_manager' ";
        }
        if (!empty($agent)) {
            $where .= " and eh.user_id = '$agent' ";
            $name = "u.name";
        }
        if (!empty($source)) {
            $where .= " and r.source_id = '$source' ";
        }
        if ($sent == 0 || $sent == 1) {
            $where .= " and eh.status = '$sent' ";
        }
        if (!empty($read) && ($sent == 1)) {
            $where .= " and eh.read_confirmed = '$read' ";
        }
        if ($pending == 1) {
            $where .= " and eh.pending = '$pending' ";
        }

        $qry = "select eh.email_id,
                  DATE_FORMAT(eh.sent_date,'%d/%m/%Y %H:%i:%s') as sent_date,
                  eh.subject,
                  eh.body,
                  eh.send_from,
                  eh.send_to,
                  eh.cc,
                  eh.bcc,
                  eh.user_id,
                  eh.urn,
                  eh.template_id,
                  eh.read_confirmed,
                  eh.read_confirmed_date,
                  eh.status,
                  eh.pending,
                  u.*,
                  t.*
            from email_history eh
            inner join users u ON (u.user_id = eh.user_id)
            inner join records r ON (r.urn = eh.urn)
            inner join campaigns c ON (c.campaign_id = r.campaign_id)
            inner join email_templates t ON (t.template_id = eh.template_id)
            where 1 $where
            order by eh.sent_date desc";

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
                      e.read_confirmed_date,
                      e.status,
                      u.*,
                      t.*,
                      e.pending
		    	from email_history e
		    	inner join users u ON (u.user_id = e.user_id)
		    	inner join email_templates t ON (t.template_id = e.template_id)
		    	where e.email_id = " . $email_id;

        $results = $this->db->query($qry)->result_array();

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
     * Update the status of an email from the email history
     *
     * @param Form $form
     */
    public function update_email_history($form)
    {
        $this->db->where("email_id", $form['email_id']);
        return $this->db->update("email_history", $form);
    }

    /**
     * Delete an email from the history
     */
    public function delete_email($email_id)
    {
        $this->db->where("email_id", $email_id);
        return $this->db->delete("email_history");
    }

    public function set_record_history($data)
    {
        $qry = "insert into history set campaign_id = (select campaign_id from records where urn = '" . $data['urn'] . "'), urn='" . $data['urn'] . "', contact=now(),description='Batch email sent',outcome_id = 84, comments = (select template_name from email_templates where template_id = '" . $data['template_id'] . "'),user_id = '" . $data['user_id'] . "'";
        $this->db->query($qry);
    }

    public function remove_title($croncode)
    {
        $mr = "update email_history set body = replace(body,'Dear Mr','Dear') where cron_code = '$croncode'";
        $this->db->query($mr);
        $miss = "update email_history set body = replace(body,'Dear Miss','Dear') where cron_code = '$croncode'";
        $this->db->query($miss);
        $mrs = "update email_history set body = replace(body,'Dear Mrs','Dear') where cron_code = '$croncode'";
        $this->db->query($mrs);
        $ms = "update email_history set body = replace(body,'Dear Ms','Dear') where cron_code = '$croncode'";
        $this->db->query($ms);
    }

    public function get_recipients($campaign_id, $outcome_id)
    {
        $recipients = array();
        $cc = array();
        $bcc = array();
        $this->db->where(array(
            "outcome_id" => $outcome_id,
            "campaign_id" => $campaign_id
        ));
        $this->db->join("email_trigger_recipients", "email_triggers.trigger_id = email_trigger_recipients.trigger_id", "LEFT");
        $this->db->join("users", "users.user_id = email_trigger_recipients.user_id", "LEFT");

        $result = $this->db->get("email_triggers")->result_array();
        foreach ($result as $row) {
            if ($row['type'] == "cc") {
                $cc[$row['name']] = $row['user_email'];
            } else if ($row['type'] == "bcc") {
                $bcc[$row['name']] = $row['user_email'];
            } else {
                $main[$row['name']] = $row['user_email'];
            }
        }
        $recipients['main'] = $main;
        $recipients['cc'] = $cc;
        $recipients['bcc'] = $bcc;
        return $recipients;
    }

    public function set_email_outcome($urn)
    {
        //just sets an email sent outcome id where an email was sent
        $this->db->where("urn", $urn);
        $this->db->update('records', array("outcome_id" => 84));
    }

    /**
     * Get pending emails
     */
    public function get_pending_emails($num_emails, $cron_code = false)
    {
        $qry = "select
                      template_id,
                      email_id,
                      template_name,
                      template_hostname,
                      template_username,
                      template_password,
                      template_port,
                      template_encryption,
                      email_templates.template_unsubscribe,
                      body,
                      subject,
                      send_from,
                      send_to,
                      cc,
                      bcc,
                      urn,
                      user_id,
                      status,
                      pending
                    from email_history
                    inner join email_templates using(template_id)
                    where pending=1";
        if ($cron_code) {
            $qry .= " and cron_code = '" . $cron_code . "'";
        } else {
            $qry .= " and cron_code is null";
        }
        $qry .= " order by email_id asc
                    limit 0," . $num_emails;

        $result = $this->db->query($qry)->result_array();

        return $result;
    }

    //Get the webform by urn (ordered by timestamp)
    public function get_webform_answers_by_urn($urn) {
        $qry = "select
                 *
                from webform_answers wa
                inner join webforms w using(webform_id)
                inner join webform_questions wq using(webform_id)
                inner join webforms_to_campaigns wc using(webform_id)
                where urn = ".$urn."
                order by updated_by desc";

        $result = $this->db->query($qry)->result_array();

        return $result;
    }


    public function get_ics_email_addresses($appointment_id) {

        $qry = "select
                  a.*,
                  GROUP_CONCAT(DISTINCT (IF(atus.ics,atus.user_email,NULL)) SEPARATOR ',') as attendees,
                  GROUP_CONCAT(DISTINCT (IF(brus.ics,brus.user_email,NULL)) SEPARATOR ',') as region_users,
                  GROUP_CONCAT(DISTINCT (IF(bus.ics,bus.user_email,NULL)) SEPARATOR ',') as branch_users,
                  b.branch_email,
		          br.region_email
                from  appointments a
                left join appointment_attendees at using(appointment_id)
                left join users atus ON (atus.user_id = at.user_id)
                left join branch b using(branch_id)
                left join branch_regions br using (region_id)
                left join branch_region_users bru using (region_id)
                left join users brus ON (brus.user_id = bru.user_id)
                left join branch_user bu using (branch_id)
                left join users bus ON (bus.user_id = bu.user_id)
                where appointment_id = ".$appointment_id."
                group by appointment_id";

        $result = $this->db->query($qry)->result_array();

        return (isset($result[0])?$result[0]:NULL);
    }

}