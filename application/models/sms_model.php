<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sms_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
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
            $appointment_fields = " appointment_type, if(a.address<>'',a.address,'') address, a.`title`,a.`text`,date_format(`start`,'%d/%m/%Y %H:%i') `start`,a.`end`,a.`date_added`,date_format(`start`,'%d/%m/%Y') `appointment_date`,if(time(`start`)>'16:00:00','eve',if(time(`start`)<'12:00:00','am','pm')) time_slot, ";
        } else {
            $contact_details = " left join contacts using(urn) left join contact_telephone using(contact_id) left join contact_addresses ca using(contact_id) where records.urn = '$urn'";
            $attendee = " 'Sir/Madam' attendee ";
            $appointment_fields = "";
        }

        $qry = "select records.urn,campaign_name,date_format(nextcall,'%d/%m/%Y %H:%i') nextcall,date_format(records.date_updated,'%d/%m/%Y %H:%i') lastcall,outcome,dials,status_name, records.urgent,if(campaign_type_id = 1,fullname,if(fullname is not null,concat(fullname,' from ', companies.name),companies.name)) contact, companies.name  company,records.campaign_id,companies.description,companies.website,companies.conumber,contacts.fullname,contacts.gender,contacts.position,contacts.dob,if(contacts.email is not null,contacts.email,'') email,if(contact_telephone.telephone_number is null,company_telephone.telephone_number,contact_telephone.telephone_number) telephone,$appointment_fields $attendee,c1,c2,c3,c4,c5,d1,d2,dt1,dt2,n1,n2,n3, concat(ca.add1,' ',ca.add2,', ',ca.postcode) contact_address $user_qry from records left join outcomes using(outcome_id) left join campaigns using(campaign_id) left join status_list on record_status = record_status_id left join companies using(urn) left join company_telephone using(company_id) left join record_details using(urn) ";
        $qry .= $contact_details;

        return $this->db->query($qry)->result_array();

    }

    public function template_to_form($template_id)
    {
        $form = array();

        $this->db->where('template_id', $template_id);
        $result = $this->db->get('sms_templates')->result_array();
        foreach ($result as $row) {
            $form['template_id'] = $row['template_id'];
            $form['text'] = $row['template_text'];
            $form['template_unsubscribe'] = $row['template_unsubscribe'];
        }
        return $form;

    }


    /**
     * Get the templates
     */
    public function get_templates()
    {
        $qry = "select
                  st.*,
                  ss.*
                from sms_templates st
                inner join sms_sender ss ON (st.template_sender_id = ss.sender_id)
                order by template_name";

        $results = $this->db->query($qry)->result_array();
        return $results;
    }

    /**
     * Get the campaings for all the existing templates
     */
    public function get_campaigns_by_templates()
    {
        $this->db->select("c.*");
        $this->db->from("sms_template_to_campaigns c");
        $this->db->join("sms_templates c", "c.template_id = t.template_id");
        return $this->db->get()->result_array();
    }

    public function check_sms_history($template_id, $urn)
    {
        $this->db->where(array("template_id" => $template_id, "urn" => $urn));
        $result = $this->db->get("sms_history");
        if ($result->num_rows()) {
            return true;
        }
    }

    public function check_unsubscribed($send_to, $client)
    {
        $list = explode(",", $send_to);
        foreach ($list as $sms) {
            $this->db->where(array("sms_address" => $sms, "client_id" => $client));
            if ($this->db->get("sms_unsubscribe")->num_rows()) {
                return $sms;
            }
        }
    }

    public function unsubscribe($data)
    {
        return $this->db->replace("sms_unsubscribe", $data);
    }

    /**
     * Get a template
     *
     * @param integer $id
     * @return Template
     */
    public function get_template($id)
    {
        $qry = "select
                  st.*,
                  ss.*
                from sms_templates st
                inner join sms_sender ss ON (st.template_sender_id = ss.sender_id)
                where st.template_id = ".$id;

        $results = $this->db->query($qry)->result_array();
        return $results[0];
    }


    /**
     * Get the campaings by template
     */
    public function get_campaigns_by_template_id($id)
    {
        $this->db->select("c.*");
        $this->db->from("sms_template_to_campaigns c");
        $this->db->where("c.template_id", $id);


        $results = $this->db->get()->result_array();

        return $results;
    }

    /**
     * Add a new template
     *
     * @param Form $form
     */
    public function add_new_template($form)
    {
        $this->db->insert("sms_templates", $form);

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
        return $this->db->update("sms_templates", $form);
    }

    /**
     * Remove a template
     *
     * @param integer $id
     */
    public function delete_template($id)
    {
        $this->db->where("template_id", $id);
        return $this->db->delete("sms_templates");
    }

    /**
     * Insert the campaings for a template
     */
    public function insert_campaigns_by_template_id($template_id, $campaignList)
    {
        $response = true;

        foreach ($campaignList as $campaign) {
            if (!$this->db->insert("sms_template_to_campaigns", array(
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
        return $this->db->delete("sms_template_to_campaigns");
    }


    /**
     * Get sms history
     */
    public function get_sms_by_urn($urn, $limit, $offset)
    {
        $limit_ = ($limit) ? "limit " . $offset . "," . $limit : '';

        $qry = "select s.sms_id,
                      DATE_FORMAT(s.sent_date,'%d/%m/%Y %H:%i:%s') as sent_date,
                      s.text,
                      s.sender_id,
                      ss.name as send_from,
                      s.send_to,
                      s.user_id,
                      s.urn,
                      s.template_id,
                      s.status_id,
                      st.status_reason as status,
                      u.*,
                      t.*
		    	from sms_history s
		    	inner join sms_status st ON (s.status_id = st.sms_status_id)
		    	inner join sms_sender ss ON (s.sender_id = ss.sender_id)
		    	left join users u ON (u.user_id = s.user_id)
		    	inner join sms_templates t ON (t.template_id = s.template_id)
		    	where s.urn = " . $urn . "
		    	order by s.sent_date desc
		    	" . $limit_;

        return $this->db->query($qry)->result_array();
    }

    /**
     * Get sms history by filter data
     */
    public function get_sms_by_filter($options)
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

        $qry = "select s.sms_id,
                      DATE_FORMAT(s.sent_date,'%d/%m/%Y %H:%i:%s') as sent_date,
                      s.text,
                      s.send_from,
                      s.send_to,
                      s.user_id,
                      s.urn,
                      s.template_id,
                      s.status_id,
                      st.status_reason as status,
                      u.*,
                      t.*
            from sms_history s
            inner join sms_status st ON (s.status_id = st.sms_status_id)
		    left join users u ON (u.user_id = s.user_id)
            inner join records r ON (r.urn = s.urn)
            inner join campaigns c ON (c.campaign_id = r.campaign_id)
            inner join sms_templates t ON (t.template_id = s.template_id)
            where 1 $where
            order by s.sent_date desc";

        return $this->db->query($qry)->result_array();
    }

    /**
     * Get sms history by id
     */
    public function get_sms_by_id($sms_id)
    {
        $qry = "select s.sms_id,
                      DATE_FORMAT(s.sent_date,'%d/%m/%Y %H:%i:%s') as sent_date,
                      s.text,
                      s.sender_id,
                      ss.name as send_from,
                      s.send_to,
                      s.user_id,
                      s.urn,
                      s.template_id,
                      s.status_id,
                      st.status_reason as status,
                      u.*,
                      t.*
		    	from sms_history s
		    	inner join sms_status st ON (s.status_id = st.sms_status_id)
		    	inner join sms_sender ss ON (s.sender_id = ss.sender_id)
		    	left join users u ON (u.user_id = s.user_id)
		    	inner join sms_templates t ON (t.template_id = s.template_id)
		    	where s.sms_id = " . $sms_id;

        $results = $this->db->query($qry)->result_array();

        return $results[0];
    }

    /**
     * Add a new sms to the history
     *
     * @param Form $form
     */
    public function add_new_sms_history($form)
    {
        $this->db->insert("sms_history", $form);

        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $insert_id;

    }

    /**
     * Add sms's to the history
     *
     * @param $sms_histories
     */
    public function add_sms_histories($sms_histories)
    {
        $result = $this->db->insert_batch('sms_history', $sms_histories);

        $this->db->trans_complete();

        return $result;

    }

    /**
     * Update the status of an sms from the sms history
     *
     * @param Form $form
     */
    public function update_sms_history($form)
    {
        $this->db->where("sms_id", $form['sms_id']);
        return $this->db->update("sms_history", $form);
    }

    /**
     * Update sms's to the history
     *
     * @param $sms_histories
     */
    public function update_sms_histories($sms_histories)
    {
        $result = $this->db->update_batch('sms_history', $sms_histories,'sms_id');

        $this->db->trans_complete();

        return $result;

    }

    /**
     * Delete an sms from the history
     */
    public function delete_sms($sms_id)
    {
        $this->db->where("sms_id", $sms_id);
        return $this->db->delete("sms_history");
    }

    public function set_record_history($data)
    {
        $qry = "insert into history set campaign_id = (select campaign_id from records where urn = '" . $data['urn'] . "'), urn='" . $data['urn'] . "', contact=now(),description='Batch sms sent',outcome_id = 84, comments = (select template_name from sms_templates where template_id = '" . $data['template_id'] . "'),user_id = '" . $data['user_id'] . "'";
        $this->db->query($qry);
    }

    public function remove_title($croncode)
    {
        $mr = "update sms_history set body = replace(body,'Dear Mr','Dear') where cron_code = '$croncode'";
        $this->db->query($mr);
        $miss = "update sms_history set body = replace(body,'Dear Miss','Dear') where cron_code = '$croncode'";
        $this->db->query($miss);
        $mrs = "update sms_history set body = replace(body,'Dear Mrs','Dear') where cron_code = '$croncode'";
        $this->db->query($mrs);
        $ms = "update sms_history set body = replace(body,'Dear Ms','Dear') where cron_code = '$croncode'";
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
        $this->db->join("sms_trigger_recipients", "sms_triggers.trigger_id = sms_trigger_recipients.trigger_id", "LEFT");
        $this->db->join("users", "users.user_id = sms_trigger_recipients.user_id", "LEFT");

        $result = $this->db->get("sms_triggers")->result_array();
        foreach ($result as $row) {
            if ($row['type'] == "cc") {
                $cc[$row['name']] = $row['user_sms'];
            } else if ($row['type'] == "bcc") {
                $bcc[$row['name']] = $row['user_sms'];
            } else {
                $main[$row['name']] = $row['user_sms'];
            }
        }
        $recipients['main'] = $main;
        $recipients['cc'] = $cc;
        $recipients['bcc'] = $bcc;
        return $recipients;
    }

    public function set_sms_outcome($urn)
    {
        //just sets an sms sent outcome id where an sms was sent
        $this->db->where("urn", $urn);
        $this->db->update('records', array("outcome_id" => 84));
    }

    /**
     * Get pending sms
     */
    public function get_pending_sms($limit)
    {
        $qry = "select *
            from sms_history
            where status_id=1
            limit 0,".$limit;


        $result = $this->db->query($qry)->result_array();

        return $result;
    }

    public function get_remind_appointments($template_id)
    {
        $qry = "select
                  DISTINCT CONCAT(appointment_id,'_',telephone_number),
                  a.appointment_id,
                  c.fullname as contact,
                  r.urn,
                  a.start,
                  DATE_FORMAT(start,'%d/%m/%Y') as appointment_date,
                  if(time(start)<'12:30:00','am','pm') time_slot,
                  telephone_number as sms_number,
                  template_id,
                  t.template_text as sms_text,
                  t.template_sender_id as sender_id,
                  s.name as sms_from
                from appointments a
                inner join records r using(urn)
                inner join contacts c ON (c.contact_id=a.contact_id)
                inner join contact_telephone ct ON (c.contact_id=ct.contact_id)
                inner join sms_template_to_campaigns using (campaign_id)
                inner join sms_templates t using (template_id)
                inner join sms_sender s ON (t.template_sender_id = s.sender_id)
                left join appointment_attendees aat using (appointment_id)
                left join users uat using (user_id)
                where telephone_number REGEXP '^(447|[[.+.]]447|00447|0447|07)'
                    and template_id = ".$template_id."
                    and date(start) BETWEEN (CURDATE() + INTERVAL 1 DAY) AND (CURDATE() + INTERVAL 2 DAY)";

        $result = $this->db->query($qry)->result_array();

        return $result;
    }

}