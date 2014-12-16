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
        //$this->firephp->log($all_data);
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
    public function reassign_data($user, $state, $campaign, $count = "all")
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
        $delqry = "delete from ownership where user_id = '$user' and urn in(select urn from records where campaign_id = '$campaign' and progress_id is null and record_status = 1 $where)";
        $this->db->query($delqry);
        if ($count == "all") {
            $insqry = "insert into ownership (select urn,'$user' from records where campaign_id = '$campaign' and progress_id is null and record_status = 1 $where)";
            $this->db->query($insqry);
            $_SESSION['prevuser'] = $user;
        } else {
            $insqry = "update ownership set user_id = '$user' where user_id = '{$_SESSION['prevuser']}' and urn in(select urn from records where campaign_id = '$campaign' and progress_id is null and record_status = 1 $where) limit $count";
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

    public function get_backup_data_by_campaign($options) {
        $where = "";
        if (!empty($options['campaign_id'])) {
            $where .= " and r.campaign_id = '".$options['campaign_id']."'";
        }

        $update_date = "";
        $update_date_from = "";
        $update_date_to = "";
        if (!empty($options['update_date_from'])) {
            $update_date_from = "(r.date_updated >= '".$options['update_date_from']."' or (r.date_updated is null and r.date_added >=  '".$options['update_date_from']."'))";
        }
        if (!empty($options['update_date_to'])) {
            $update_date_to = "(r.date_updated <= '".$options['update_date_to']."' or (r.date_updated is null and r.date_added <=  '".$options['update_date_to']."'))";
        }
        $update_date .= $update_date_from.(strlen($update_date_from)>0 && strlen($update_date_to)>0?" and ":"").$update_date_to;

        $renewal_date = "";
        $renewal_date_from = "";
        $renewal_date_to = "";
        if (!empty($options['renewal_date_from'])) {
            $renewal_date_from = "(rd.d1 >= '".$options['renewal_date_from']."')";
        }
        if (!empty($options['renewal_date_to'])) {
            $renewal_date_to = (strlen($renewal_date)>0?" and ":"")."(rd.d1 >= '".$options['renewal_date_to']."')";
        }
        $renewal_date .= $renewal_date_from.(strlen($renewal_date_from)>0 && strlen($renewal_date_to)>0?" and ":"").$renewal_date_to;

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

        $where = "";
        if (!empty($campaign)) {
            $where .= " and bc.campaign_id = '$campaign' ";
        }

        $qry = "select bc.campaign_id, bc.name, bc.path,
                  IF(bc.backup_date ,date_format(bc.backup_date,'%d/%m/%y'),'-') as backup_date,
                  bc.num_records, bc.user_id,
                  IF(bc.update_date_from ,date_format(bc.update_date_from,'%d/%m/%y'),'-') as update_date_from,
                  IF(bc.update_date_to ,date_format(bc.update_date_to,'%d/%m/%y'),'-') as update_date_to,
                  IF(bc.renewal_date_from ,date_format(bc.renewal_date_from,'%d/%m/%y'),'-') as renewal_date_from,
                  IF(bc.renewal_date_to ,date_format(bc.renewal_date_to,'%d/%m/%y'),'-') as renewal_date_to,
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
}