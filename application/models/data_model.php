<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Data_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    public function get_custom_fields($campaign)
    {
        $this->db->where("campaign_id", $campaign);
        $result = $this->db->get("record_details_fields")->result_array();
        $array  = array();
        foreach ($result as $row) {
            $array[$row['field']] = $row['field_name'];
        }
        return $array;
    }
    public function get_user_data($campaign, $states)
    {
		$data = array();
        $where = "";
        if ($states == "1") {
            //virgin record only
            $where .= " and outcome_id is null and (dials = 0 or dials is null) ";
        }
        if ($states == "2") {
            //in progress only
            $where .= " and outcome_id is not null and outcome_id not in(1,2) ";
        }
        if ($states == "3") {
            //callbacks only
            $where .= " and outcome_id in(1,2) ";
        }
        $total_count = 0;
        $parked_qry  = "select count(*) count from records left join outcomes using(outcome_id) where parked_code is not null and campaign_id = '$campaign' and progress_id is null $where ";
        $parked      = intval($this->db->query($parked_qry)->row('count'));
        $where .= "  and progress_id is null and record_status = 1 ";
        $all_data = "select count(*) count from records left join outcomes using(outcome_id) where campaign_id = '$campaign' $where group by campaign_id";
        $all      = intval($this->db->query($all_data)->row('count'));

        if ($all > 0) {
            $qry    = "select user_id,name from users_to_campaigns left join users using(user_id) left join role_permissions using(role_id) left join permissions using(permission_id) where campaign_id = '$campaign' and group_id = 1 and permission_name = 'set call outcomes' and user_id is not null ";
			if(!empty($_SESSION['team'])){
			$qry    .= " and team_id = '{$_SESSION['team']}'	";
			}
            $result = $this->db->query($qry)->result_array();
            foreach ($result as $row) {
                $count                          = 0;
                $data[$row['user_id']]['name']  = $row['name'];
                $data_qry                       = "select count(*) count from ownership left join records using(urn) where user_id = '{$row['user_id']}' and campaign_id = '$campaign' $where group by campaign_id";
                //$this->firephp->log($data_qry);
                $data[$row['user_id']]['count'] = ($this->db->query($data_qry)->row('count') ? $this->db->query($data_qry)->row('count') : "0");
                $count                          = intval($this->db->query($data_qry)->row('count'));
                $total_count += $count;
                $pc                          = ($count / $all) * 100;
                $data[$row['user_id']]['pc'] = round($pc);
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data,
                "total" => $all,
                "assigned" => $total_count,
                "unassigned" => $all - $total_count,
                "parked" => $parked,
                "n" => count($result)
            ));
        } else {
            echo json_encode(array(
                "success" => true,
                "data" => 0,
                "total" => 0,
                "unassigned" => 0,
                "assigned" => 0,
                "parked" => 0
            ));
        }
    }
    public function reassign_data($user, $state, $campaign, $dials, $count = "all")
    {
        $count = (!$count ? "0" : $count);
        $where = "";
        $limit = "";
        if ($state == "1") {
            //virgin record only
            $where .= " and outcome_id is null and (dials = 0 or dials is null) ";
        }
        if ($state == "2") {
            //in progress only
            $where .= " and outcome_id is not null and outcome_id not in(1,2) ";
        }
        if ($state == "3") {
            //callbacks only
            $where .= " and outcome_id in(1,2) ";
        }
      				$wheredials = "";
        if (!empty($dials)) {
            //callbacks only
			if($dials==4){
			$wheredials .= " and dials > 3 ";
			} else {
            $wheredials .= " and dials = '$dials' ";
			}
        }
		$delqry = "delete from ownership where user_id = '$user' and urn in(select urn from records where campaign_id = '$campaign' $wheredials and progress_id is null and record_status = 1 $where $wheredials)";
        $this->db->query($delqry);
        if ($count=="all") {
		$insqry = "insert into ownership (select '',urn,'$user' from records where campaign_id = '$campaign' and progress_id is null and record_status = 1 $where $wheredials)";

        $this->db->query($insqry);
		$this->firephp->log($insqry);
		$_SESSION['prevuser']=$user;
        } else {
			$insqry = "update ownership set user_id = '$user' where user_id = '{$_SESSION['prevuser']}' and urn in(select urn from records where campaign_id = '$campaign' and progress_id is null and record_status = 1 $where $wheredials) limit $count";
			$this->firephp->log($insqry);
        $this->db->query($insqry);
			
		}
    }
    public function create_source($source)
    {
        $this->db->insert("data_sources", array(
            "source_name" => $source
        ));
        return $this->db->insert_id();
    }
    public function import_record($row, $options)
    {
        $errors     = array();
        $contact_id = "";
        $urn        = "";
        $company_id = "";
		$client_ref = "";
		$fullname = "";
        if (isset($row["contacts"])) {
            $fullname = (!empty($row["contacts"]["fullname"]) ? $row["contacts"]["fullname"] : "");
        }
		if(empty($fullname)){
			$fullname .= (isset($row["contacts"]["title"]) ? $row["contacts"]["title"] : "");
			$fullname .= (isset($row["contacts"]["firstname"]) ? $row["contacts"]["firstname"] : "");
			$fullname .= (isset($row["contacts"]["lastname"]) ? $row["contacts"]["lastname"] : "");
		}

        if ($options["duplicates"] == "1" || $options["duplicates"] == "2") {
            if (isset($row["records"])) {
								if(isset($row["records"]['client_ref'])){
								$client_ref=$row["records"]['client_ref'];
								unset($row["records"]['client_ref']);
								}
                //insert the options into the records table data
                $row["records"]['campaign_id'] = $options['campaign'];
                $row["records"]['source_id']   = $options['source'];
				$row["records"]['date_added']   = date('Y-m-d');
				$row["records"]['dials']   = "0";
                if ($options["duplicates"] == "1" || $options["autoincrement"] == 1) {
                    $this->db->insert("records", $row["records"]);
                    if ($this->db->_error_message()) {
                        $errors[] = $this->db->_error_message();
                    }
                    $urn = $this->db->insert_id();
                }
                if ($options["duplicates"] == "2" && isset($row["records"]["urn"]) && $options["autoincrement"] == 2) {
                    $urn = $row["records"]["urn"];
                    $this->db->where("urn", $urn);
                    $this->db->delete("records");
                    $this->db->insert("records", $row["records"]);
                    if ($this->db->_error_message()) {
                        $errors[] = $this->db->_error_message();
                    }
                    $urn = $this->db->insert_id();
                }
            }
            if (isset($row["record_details"]) && !empty($urn)) {
                $row["record_details"]["urn"] = $urn;
                $this->db->insert("record_details", $row["record_details"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
            }
            if (!empty($fullname) && !empty($urn)) {
                $row["contacts"]["urn"]      = $urn;
                $row["contacts"]["fullname"] = $fullname;
                $this->db->insert("contacts", $row["contacts"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
                $contact_id = $this->db->insert_id();
            }
            if (isset($row["contact_telephone"]) && !empty($contact_id)) {
                $row["contact_telephone"]['contact_id'] = $contact_id;
                $this->db->insert("contact_telephone", $row["contact_telephone"]);
				if ($this->db->_error_message()) {
                $errors[] = $this->db->_error_message();
				}
            }
            if (isset($row["contact_addresses"]) && !empty($contact_id)) {
                $row["contact_addresses"]['contact_id'] = $contact_id;
                $row["contact_addresses"]['primary']    = 1;
                $this->db->insert("contact_addresses", $row["contact_addresses"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
            }
            if (isset($row["companies"]) && !empty($urn)) {
                $row["companies"]["urn"] = $urn;
                $this->db->insert("companies", $row["companies"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
                $company_id = $this->db->insert_id();
            }
            if (isset($row["company_telephone"]) && !empty($company_id)) {
                $row["company_telephone"]['company_id'] = $company_id;
                $this->db->insert("company_telephone", $row["company_telephone"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
            }
            if (isset($row["company_addresses"]) && !empty($company_id)) {
                $row["company_addresses"]['company_id'] = $company_id;
                $row["company_addresses"]['primary']    = 1;
                $this->db->insert("company_addresses", $row["company_addresses"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
            }
			if(!empty($client_ref)){
				$this->db->insert("client_refs", array("urn"=>$urn,"client_ref"=>$client_ref));
				 if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
			}
        } else if ($options["duplicates"] == "3" && isset($row["records"]["urn"]) && $options["autoincrement"] == 2) {
            /* if duplicate is set to update then we have to find all the ids for the given urn and use the update instead */
            if (isset($row["records"])) {
				if(isset($row["records"]['client_ref'])){
								$client_ref=$row["records"]['client_ref'];
								unset($row["records"]['client_ref']);
								}
                //insert the options into the records table data
                $row["records"]['campaign_id'] = $options['campaign'];
                $row["records"]['source_id']   = $options['source'];
                $urn                           = $row["records"]["urn"];
                $this->db->where("urn", $urn);
                $this->db->update("records", $row["records"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
            }
            if (isset($row["record_details"]) && !empty($urn)) {
                $row["record_details"]["urn"] = $urn;
                $this->db->where("urn", $urn);
                $this->db->update("record_details", $row["record_details"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
            }
            if (isset($row["contacts"]) && !empty($urn)) {
                $row["contacts"]["urn"] = $urn;
                $this->db->where("urn", $urn);
                $this->db->limit(1);
                $this->db->update("contacts", $row["contacts"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
                $this->db->where("urn", $urn);
                $contact_id = $this->db->get("contacts")->first_row()->contact_id;
            }
            if (isset($row["contact_telephone"]) && !empty($contact_id)) {
                $row["contact_telephone"]['contact_id'] = $contact_id;
                $this->db->where("contact_id", $contact_id);
                $this->db->limit(1);
                $this->db->update("contact_telephone", $row["contact_telephone"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
            }
            if (isset($row["contact_addresses"]) && !empty($contact_id)) {
                $row["contact_addresses"]['contact_id'] = $contact_id;
                $row["contact_addresses"]['primary']    = 1;
                $this->db->where("contact_id", $contact_id);
                $this->db->limit(1);
                $this->db->update("contact_addresses", $row["contact_addresses"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
            }
            if (isset($row["companies"]) && !empty($urn)) {
                $row["companies"]["urn"] = $urn;
                $this->db->where("urn", $urn);
                $this->db->limit(1);
                $this->db->update("companies", $row["companies"]);
                $errors[] = $this->db->_error_message();
                $this->db->where("urn", $urn);
                $company_id = $this->db->get("companies")->first_row()->company_id;
            }
            if (isset($row["company_telephone"]) && !empty($company_id)) {
                $row["company_telephone"]['company_id'] = $company_id;
                $this->db->where("company_id", $company_id);
                $this->db->limit(1);
                $this->db->update("company_telephone", $row["company_telephone"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
            }
			if(!empty($client_ref)){
				$this->db->where("urn", $urn);
				$this->db->update("client_refs", array("client_ref"=>$client_ref));
				 if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
			}
            if (isset($row["company_addresses"]) && !empty($company_id)) {
                $row["company_addresses"]['company_id'] = $company_id;
                $row["company_addresses"]['primary']    = 1;
                $this->db->where("company_id", $company_id);
                $this->db->limit(1);
                $this->db->update("company_addresses", $row["company_addresses"]);
                if ($this->db->_error_message()) {
                    $errors[] = $this->db->_error_message();
                }
            }
        }
		 return $errors;
    }

    public function get_daily_ration_data($options) {
        $campaign = $options['campaign'];

        $where = "";
        if (!empty($campaign)) {
            $where .= " and records.campaign_id = '$campaign' ";
        }

        $qry = "select count(*) count,
                total_records,
                pc.park_reason,
                records.parked_code,
                c.campaign_name,
                records.campaign_id,
                c.daily_data
                from records
                inner join campaigns c ON (c.campaign_id = records.campaign_id)
                left join park_codes pc ON (pc.parked_code = records.parked_code)
                left join (select count(*) total_records, r.campaign_id from records r group by r.campaign_id) tr on tr.campaign_id = records.campaign_id
                where 1";
        $qry .= $where;
        $qry .= " group by records.campaign_id, records.parked_code order by count desc ";

        return $this->db->query($qry)->result_array();

    }

    public function set_daily_ration($campaign_id, $daily_data) {
        $this->db->where("campaign_id", $campaign_id);
        return $this->db->update("campaigns", array(
            "daily_data" => $daily_data
        ));
    }

    public function get_backup_data($options) {
        $campaign = $options['campaign'];

        $where = "";
        if (!empty($campaign)) {
            $where .= " and c.campaign_id = '$campaign' ";
        }

        $qry = "select c.campaign_id, c.campaign_name, IF(bc.months_ago is not NULL,bc.months_ago,0) as months_ago, IF(bc.months_num is not NULL,bc.months_num,0) as months_num
                from campaigns c
                left join backup_by_campaign bc ON (bc.campaign_id = c.campaign_id)
                where 1";
        $qry .= $where;
        $qry .= " order by c.campaign_name asc ";

        return $this->db->query($qry)->result_array();

    }

    public function get_backup_data_by_campaign($options, $renewal_date_field = null) {
        $where = "";
        if (!empty($options['campaign_id'])) {
            $where .= " and r.campaign_id = '".$options['campaign_id']."'";
        }

        $renewal_date = "";
        if ($renewal_date_field) {
            $renewal_date_from = "";
            $renewal_date_to = "";
            if (!empty($options['renewal_date_from'])) {
                $renewal_date_from = "(date(rd.".$renewal_date_field.") >= '".$options['renewal_date_from']."')";
            }
            if (!empty($options['renewal_date_to'])) {
                $renewal_date_to = (strlen($renewal_date)>0?" and ":"")."(date(rd.".$renewal_date_field.") >= '".$options['renewal_date_to']."')";
            }
            $renewal_date .= $renewal_date_from.(strlen($renewal_date_from)>0 && strlen($renewal_date_to)>0?" and ":"").$renewal_date_to;
        }

        $update_date = "";
        $update_date_from = "";
        $update_date_to = "";
        if (!empty($options['update_date_from'])) {
            $update_date_from = "(date(r.date_updated) >= '".$options['update_date_from']."' or (r.date_updated is null and date(r.date_added) >=  '".$options['update_date_from']."'))";
        }
        if (!empty($options['update_date_to'])) {
            $update_date_to = "(date(r.date_updated) <= '".$options['update_date_to']."' or (r.date_updated is null and date(r.date_added) <=  '".$options['update_date_to']."'))";
        }
        $update_date .= $update_date_from.(strlen($update_date_from)>0 && strlen($update_date_to)>0?" and ":"").$update_date_to;

        if (strlen($update_date)>0 || strlen($renewal_date)>0) {
            $where .= " and (".$update_date.(strlen($update_date)>0 && strlen($renewal_date)>0?" or ":"").$renewal_date.")";
        }

        $qry = "select *
                from records r
                inner join campaigns c ON (c.campaign_id = r.campaign_id)
                inner join record_details rd ON (rd.urn = r.urn)
                where 1";
        $qry .= $where;

        return $this->db->query($qry)->result_array();

    }

    public function get_backup_history_data($options) {
        $campaign = $options['campaign'];
        $restored = $options['restored'];

        $where = "";
        if (!empty($campaign)) {
            $where .= " and bc.campaign_id = '$campaign' ";
        }

        $where = "";
        if (($restored == 0 || $restored == 1)&&$restored != '') {
            $where .= " and bc.restored = '$restored' ";
        }

        $qry = "select bc.backup_campaign_id, bc.campaign_id, bc.name, bc.path,
                  IF(bc.backup_date ,date_format(bc.backup_date,'%d/%m/%y'),'-') as backup_date,
                  bc.num_records, bc.user_id,
                  IF(bc.update_date_from ,date_format(bc.update_date_from,'%d/%m/%y'),'-') as update_date_from,
                  IF(bc.update_date_to ,date_format(bc.update_date_to,'%d/%m/%y'),'-') as update_date_to,
                  IF(bc.renewal_date_from ,date_format(bc.renewal_date_from,'%d/%m/%y'),'-') as renewal_date_from,
                  IF(bc.renewal_date_to ,date_format(bc.renewal_date_to,'%d/%m/%y'),'-') as renewal_date_to,
                  bc.restored, bc.restored_date,
                  c.campaign_name, u.name as user_name
                from backup_campaign_history bc
                inner join campaigns c ON (c.campaign_id = bc.campaign_id)
                inner join users u ON (u.user_id = bc.user_id)
                where 1";
        $qry .= $where;
        $qry .= " order by bc.backup_date desc limit 0,12";

        return $this->db->query($qry)->result_array();

    }

    public function save_backup_campaign_history($form)
    {
        $this->db->insert("backup_campaign_history", $form);
        return $this->db->insert_id();
    }

    public function remove_backup_campaign_data($urn_list, $campaign_id) {

        //cross_transfers
        $delqry = "delete from cross_transfers where history_id IN (select history_id from history where urn IN ".$urn_list.")";
        $this->db->query($delqry);

        //history
        $delqry = "delete from history where urn IN ".$urn_list;
        $this->db->query($delqry);

        //record_details
        $delqry = "delete from record_details where urn IN ".$urn_list;
        $this->db->query($delqry);

        //email_history_attachments
        $delqry = "delete from email_history_attachments where email_id IN (select email_id from email_history where urn IN ".$urn_list.")";
        $this->db->query($delqry);

        //email_history
        $delqry = "delete from email_history where urn IN ".$urn_list;
        $this->db->query($delqry);

        //appointment_attendees
        $delqry = "delete from appointment_attendees where appointment_id IN (select appointment_id from appointments where urn IN ".$urn_list.")";
        $this->db->query($delqry);

        //appointments
        $delqry = "delete from appointments where urn IN ".$urn_list;
        $this->db->query($delqry);

        //attachments
        $delqry = "delete from attachments where urn IN ".$urn_list;
        $this->db->query($delqry);

        //client_refs
        $delqry = "delete from client_refs where urn IN ".$urn_list;
        $this->db->query($delqry);

        //company_telephone
        $delqry = "delete from company_telephone where company_id IN (select company_id from companies where urn IN ".$urn_list.")";
        $this->db->query($delqry);

        //company_addresses
        $delqry = "delete from company_addresses where company_id IN (select company_id from companies where urn IN ".$urn_list.")";
        $this->db->query($delqry);

        //companies
        $delqry = "delete from companies where urn IN ".$urn_list;
        $this->db->query($delqry);

        //contact_telephone
        $delqry = "delete from contact_telephone where contact_id IN (select contact_id from contacts where urn IN ".$urn_list.")";
        $this->db->query($delqry);

        //contact_addresses
        $delqry = "delete from contact_addresses where contact_id IN (select contact_id from contacts where urn IN ".$urn_list.")";
        $this->db->query($delqry);

        //contacts
        $delqry = "delete from contacts where urn IN ".$urn_list;
        $this->db->query($delqry);

        //answer_notes
        $delqry = "delete from answer_notes where answer_id IN (select answer_id from survey_answers where survey_id IN (select survey_id from surveys where urn IN ".$urn_list."))";
        $this->db->query($delqry);

        //answers_to_options
        $delqry = "delete from answers_to_options where answer_id IN (select answer_id from survey_answers where survey_id IN (select survey_id from surveys where urn IN ".$urn_list."))";
        $this->db->query($delqry);

        //survey_answers
        $delqry = "delete from survey_answers where survey_id IN (select survey_id from surveys where urn IN ".$urn_list.")";
        $this->db->query($delqry);

        //surveys
        $delqry = "delete from surveys where urn IN ".$urn_list;
        $this->db->query($delqry);

        //webform_answers
        $delqry = "delete from webform_answers where urn IN ".$urn_list;
        $this->db->query($delqry);

        //sticky_notes
        $delqry = "delete from sticky_notes where urn IN ".$urn_list;
        $this->db->query($delqry);

        //favorites
        $delqry = "delete from favorites where urn IN ".$urn_list;
        $this->db->query($delqry);

        //ownership
        $delqry = "delete from ownership where urn IN ".$urn_list;
        $this->db->query($delqry);

        //campaign_xfers
        $delqry = "delete from campaign_xfers where campaign_id = ".$campaign_id;
        $this->db->query($delqry);

        //records
        $delqry = "delete from records where urn IN ".$urn_list;
        $this->db->query($delqry);
    }

    public function update_backup_campaign_history_by_id($form) {
        $this->db->where("backup_campaign_id", $form['backup_campaign_id']);
        return $this->db->update("backup_campaign_history", $form);
    }

    //##########################################################################################
    //############################### OUTCOMES #################################################
    //##########################################################################################
    public function get_outcomes() {
        $qry = "select *
                from outcomes o
                  left join status_list s ON (s.record_status_id = o.set_status)
                  left join progress_description p ON (p.progress_id = o.set_progress)
                order by outcome asc";
        return $this->db->query($qry)->result_array();
    }

    public function insert_outcome($outcome)
    {
        $this->db->insert("outcomes", $outcome);
        return $this->db->insert_id();
    }

    public function update_outcome($outcome_id, $outcome)
    {
        $this->db->where("outcome_id", $outcome_id);
        return $this->db->update("outcomes", $outcome);
    }

    public function delete_outcome($outcome_id)
    {
        $this->db->where("outcome_id", $outcome_id);
        return $this->db->delete("outcomes");
    }

    //##########################################################################################
    //############################### TRIGGERS #################################################
    //##########################################################################################
    public function get_email_triggers($form) {

        $where = "";
        if (!empty($form['campaign'])) {
            $where .= " and t.campaign_id = ".$form['campaign']." ";
        }
        if (!empty($form['outcome'])) {
            $where .= " and t.outcome_id = ".$form['outcome']." ";
        }
        if (!empty($form['template'])) {
            $where .= " and t.template_id = ".$form['template']." ";
        }

        $qry = "select t.trigger_id, t.campaign_id, t.outcome_id, t.template_id, c.campaign_name as campaign, o.outcome, et.template_name as template
                from email_triggers t
                  inner join campaigns c ON (c.campaign_id = t.campaign_id)
                  inner join outcomes o ON (o.outcome_id = t.outcome_id)
                  inner join email_templates et ON (et.template_id = t.template_id)";

        $qry .= $where;

        $qry .= "order by c.campaign_name asc";
        return $this->db->query($qry)->result_array();
    }

    public function get_email_trigger_recipients($trigger_id) {
        $qry = "select * from email_trigger_recipients where trigger_id = ".$trigger_id;

        return $this->db->query($qry)->result_array();
    }

    /**
     * Add a new email trigger
     *
     * @param Form $form
     */
    public function insert_email_trigger($form)
    {
        $this->db->insert("email_triggers", $form);
        return $this->db->insert_id();

    }

    /**
     * Update an email trigger
     *
     * @param Form $form
     */
    public function update_email_trigger($form)
    {
        $this->db->where("trigger_id", $form['trigger_id']);
        return $this->db->update("email_triggers", $form);
    }

    /**
     * Remove an email trigger
     *
     * @param integer $trigger_id
     */
    public function delete_email_trigger($trigger_id)
    {
        $this->db->where("trigger_id", $trigger_id);
        return $this->db->delete("email_triggers");
    }

    /**
     * Update the email trigger recipients. Delete the old_users and add the new_users selected
     *
     * @param Form $form
     */
    public function update_email_trigger_recipients($users, $trigger_id)
    {
        //Delete all the users for this email trigger before
        $this->db->where("trigger_id", $trigger_id);
        $results = $this->db->delete("email_trigger_recipients");

        //Insert the new users selected
        if (!empty($users) && $results) {
            $aux = array();
            foreach($users as $user) {
                array_push($aux,array(
                    'trigger_id' => $trigger_id,
                    'user_id' => $user
                ));
            }
            $users = $aux;

            $results = $this->db->insert_batch("email_trigger_recipients", $users);
        }

        return $results;
    }

    public function get_ownership_triggers($form) {
        $where = "";
        if (!empty($form['campaign'])) {
            $where .= " and t.campaign_id = ".$form['campaign']." ";
        }
        if (!empty($form['outcome'])) {
            $where .= " and t.outcome_id = ".$form['outcome']." ";
        }

        $qry = "select t.trigger_id, t.campaign_id, t.outcome_id, c.campaign_name as campaign, o.outcome
                from ownership_triggers t
                  inner join campaigns c ON (c.campaign_id = t.campaign_id)
                  inner join outcomes o ON (o.outcome_id = t.outcome_id)";

        $qry .= $where;

        $qry .= "order by c.campaign_name asc";

        return $this->db->query($qry)->result_array();
    }

    public function get_ownership_trigger_recipients($trigger_id) {
        $qry = "select * from ownership_trigger_users where trigger_id = ".$trigger_id;

        return $this->db->query($qry)->result_array();
    }

    /**
     * Add a new ownership trigger
     *
     * @param Form $form
     */
    public function insert_ownership_trigger($form)
    {
        $this->db->insert("ownership_triggers", $form);
        return $this->db->insert_id();

    }

    /**
     * Update an ownership trigger
     *
     * @param Form $form
     */
    public function update_ownership_trigger($form)
    {
        $this->db->where("trigger_id", $form['trigger_id']);
        return $this->db->update("ownership_triggers", $form);
    }

    /**
     * Remove an ownership trigger
     *
     * @param integer $trigger_id
     */
    public function delete_ownership_trigger($trigger_id)
    {
        $this->db->where("trigger_id", $trigger_id);
        return $this->db->delete("ownership_triggers");
    }

    /**
     * Update the ownership trigger recipients. Delete the old_users and add the new_users selected
     *
     * @param Form $form
     */
    public function update_ownership_trigger_recipients($users, $trigger_id)
    {
        //Delete all the users for this ownership trigger before
        $this->db->where("trigger_id", $trigger_id);
        $results = $this->db->delete("ownership_trigger_users");

        //Insert the new users selected
        if (!empty($users) && $results) {
            $aux = array();
            foreach($users as $user) {
                array_push($aux,array(
                    'trigger_id' => $trigger_id,
                    'user_id' => $user
                ));
            }
            $users = $aux;

            $results = $this->db->insert_batch("ownership_trigger_users", $users);
        }

        return $results;
    }

    //##########################################################################################
    //############################### DUPLICATES ###############################################
    //##########################################################################################

    /**
     * Get duplicates query by a filter
     */
    private function get_duplicates_qry($form) {
        $field_ar = $form['field'];
        $filter_input = $form['filter_input'];

        $where = "";
        if (!empty($form['campaign'])) {
            $where .= " and campaign_id = ".$form['campaign']." ";
        }

        $select = "";
        $join = "";
        foreach($field_ar as $field) {
            if ($field == "telephone_number") {
                if (!isset($contact_join)){
                    $contact_join = " left join contacts using (urn)";
                    $join .= $contact_join;
                }
                $join .= " left join contact_telephone using (contact_id)";
                $select .= $field.",";
            }
            elseif ($field == "postcode") {
                if (!isset($contact_join)){
                    $contact_join = " left join contacts using (urn)";
                    $join .= $contact_join;
                }
                $join .= " left join contact_addresses using (contact_id)";
                $select .= $field.",";
            }
            elseif ($field == "fullname") {
                if (!isset($contact_join)){
                    $contact_join = " left join contacts using (urn)";
                    $join .= $contact_join;
                }
                $select .= $field.",";
            }
            elseif ($field == "coname") {
                if (!isset($campaign_join)){
                    $campaign_join = " inner join campaigns using (campaign_id)";
                    $join .= $campaign_join;
                }
                $join .= " inner join companies using (urn)";
                $select .= "name,";
            }
            elseif ($field == "client_ref") {
                $join .= " inner join client_refs using (urn)";
                $select .= $field.",";
            }
        }
        $select =substr($select, 0, strlen($select)-1);


        $qry = "select ".$select.", count(*) as duplicates_count
                from records ";

        $qry .= $join;
        $qry .= " where CONCAT(".$select.") is not null and CONCAT(".$select.")<>'' and (parked_code is null or parked_code <>'5') ";
        if ($filter_input) {
            $qry .= " and CONCAT(".$select.") like '%".$filter_input."%'";
        }
        if (in_array("telephone_number",$field_ar)) {
            $qry .= " and description != 'Transfer'";
        }
        $qry .= $where;
        $qry .= " group by CONCAT(".$select.")
                having count(*)>1";

        return $qry;
    }
    /**
     * Get duplicates by a filter
     */
    public function get_duplicates($form) {
        $qry = $this->get_duplicates_qry($form);

        return $this->db->query($qry)->result_array();
    }
    /**
     * Get duplicates by a filter
     */
    public function get_duplicate_records($form) {

        $field_ar = $form['field'];
        $on_subqry = array();
        $select = array();
        foreach($field_ar as $field) {
            if ($field == "telephone_number") {
                array_push($on_subqry, "duplicates.".$field."=ct.".$field);
                array_push($select, "ct.".$field);
            }
            elseif ($field == "postcode") {
                array_push($on_subqry, "duplicates.".$field."=ca.".$field);
                array_push($select, "ca.".$field);
            }
            elseif ($field == "fullname") {
                array_push($on_subqry, "duplicates.".$field."=c.".$field);
                array_push($select, "c.".$field);
            }
            elseif ($field == "coname") {
                array_push($on_subqry, "duplicates.name=cm.name");
                array_push($select, "cm.name");
            }
            elseif ($field == "client_ref") {
                array_push($on_subqry, "duplicates.".$field."=cr.".$field);
                array_push($select, "cr.".$field);
            }
        }
        $on_subqry = implode(" AND ", $on_subqry);
        $select = implode(", ", $select);

        $qry = "select distinct r.urn, r.date_added, r.date_updated, CONCAT(".$select.") as filter from records r
                  left join contacts c ON (c.urn = r.urn)
                  left join contact_telephone ct ON (ct.contact_id = c.contact_id)
                  left join contact_addresses ca ON (ca.contact_id = c.contact_id)
                  left join companies cm ON (cm.urn = r.urn)
                  left join client_refs cr ON (cr.urn = r.urn) ";

        $subqry = $this->get_duplicates_qry($form);




        $qry .= "inner join (".$subqry.") duplicates ON (".$on_subqry.")";


        return $this->db->query($qry)->result_array();
    }

    /**
     * Delete duplicates
     *
     * Set the parked_code as Duplicate
     */
    public function delete_duplicates($urn_list) {
        $qry = "UPDATE records
                SET parked_code = (select parked_code from park_codes where park_reason = 'Duplicated')
                WHERE urn IN ".$urn_list;
        return $this->db->query($qry);
    }


    //##########################################################################################
    //############################### SUPPRESSION NUMBERS ######################################
    //##########################################################################################

    /**
     * Get suppression numbers by a filter
     */
    public function get_suppression_numbers($form) {
        $where = " where 1 ";
        if (!empty($form['campaign'])) {
            $where .= " and (campaign_id = ".$form['campaign']." OR campaign_id is NULL) ";
        }
        if (!empty($form['date_from'])) {
            $where .= " and (date(date_added) >= '".$form['date_from']."' OR date(date_updated) >= '".$form['date_from']."') ";
        }
        if (!empty($form['date_to'])) {
            $where .= " and (date(date_added) <= '".$form['date_to']."' OR date(date_updated) <= '".$form['date_to']."') ";
        }

        $qry = "SELECT
                  suppression_id,
                  telephone_number,
                  date_format(date_added,'%d/%m/%y') as date_added,
                  date_format(date_updated,'%d/%m/%y') as date_updated,
                  reason,
                  GROUP_CONCAT(campaign_name SEPARATOR ', ') as campaign_list,
                  GROUP_CONCAT(campaign_id SEPARATOR ', ') as campaign_id_list
                FROM suppression
                  LEFT JOIN suppression_by_campaign using (suppression_id)
                  LEFT JOIN campaigns using (campaign_id)";
        $qry .= $where;
        $qry .= " GROUP BY suppression_id";

        return $this->db->query($qry)->result_array();
    }

}