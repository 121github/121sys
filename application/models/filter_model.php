<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Filter_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }
    
    public function count_records($filter)
    {
        $qry   = "select count(r.urn) as `count` from records r ";
        //convert the filter options into a query and them to the base query
        $addon = $this->Filter_model->create_query_filter($filter);
        $qry .= $addon;
        
        $count = $this->db->query($qry)->row('count');
        return $count;
    }
    
    public function apply_filter($filter)
    {
        //setting the second parameter to 'true' stores the filter in the session
        $addon                        = $this->Filter_model->create_query_filter($filter, true);
        $_SESSION['filter']['values'] = $this->Filter_model->clean_filter($filter);
    }
    
    public function create_query_filter($filter, $use = false)
    {
        $filter_options["urn"]              = array(
            "table" => "records",
            "type" => "id",
            "alias" => "r.urn"
        );
        $filter_options["parked_code"]      = array(
            "table" => "records",
            "type" => "multiple",
            "alias" => "r.parked_code"
        );
        $filter_options["nextcall"]         = array(
            "table" => "records",
            "type" => "range",
            "alias" => "r.nextcall"
        );
        $filter_options["date_updated"]     = array(
            "table" => "records",
            "type" => "range",
            "alias" => "r.date_updated"
        );
        $filter_options["date_added"]       = array(
            "table" => "records",
            "type" => "range",
            "alias" => "r.date_added"
        );
        $filter_options["progress_id"]      = array(
            "table" => "records",
            "type" => "multiple",
            "alias" => "r.progress_id"
        );
        $filter_options["record_status"]    = array(
            "table" => "records",
            "type" => "multiple",
            "alias" => "r.record_status"
        );
        $filter_options["campaign_id"]      = array(
            "table" => "records",
            "type" => "multiple",
            "alias" => "r.campaign_id"
        );
        $filter_options["outcome_id"]       = array(
            "table" => "records",
            "type" => "multiple",
            "alias" => "outcomes.outcome_id"
        );
        $filter_options["coname"]           = array(
            "table" => "companies",
            "type" => "like",
            "alias" => "com.name"
        );
        $filter_options["sector_id"]        = array(
            "table" => "subsectors",
            "type" => "id",
            "alias" => "sec.sector_id"
        );
        $filter_options["subsector_id"]     = array(
            "table" => "company_subsectors",
            "type" => "id",
            "alias" => "subsec.subsector_id"
        );
        $filter_options["company_id"]       = array(
            "table" => "companies",
            "type" => "id",
            "alias" => "com.company_id"
        );
        $filter_options["employees"]        = array(
            "table" => "companies",
            "type" => "range",
            "alias" => "com.employees"
        );
        $filter_options["turnover"]         = array(
            "table" => "companies",
            "type" => "range",
            "alias" => "com.turnover"
        );
        $filter_options["contact_id"]       = array(
            "table" => "contacts",
            "type" => "id",
            "alias" => "con.contact_id"
        );
        $filter_options["fullname"]         = array(
            "table" => "contacts",
            "type" => "like",
            "alias" => "con.fullname"
        );
        $filter_options["position"]         = array(
            "table" => "contacts",
            "type" => "like",
            "alias" => "con.position"
        );
        $filter_options["dob"]              = array(
            "table" => "contacts",
            "type" => "range",
            "alias" => "con.dob"
        );
        $filter_options["email"]            = array(
            "table" => "contacts",
            "type" => "id",
            "alias" => "con.email"
        );
        $filter_options["phone"]            = array(
            "table" => "contact_telephone",
            "type" => "like",
            "alias" => "con_tel.telephone_number"
        );
        $filter_options["address"]          = array(
            "table" => "contact_addresses",
            "type" => "like",
            "alias" => "concat(con_add.add1,con_add.add2,con_add.add3,con_add.postcode)"
        );
        $filter_options["group_id"]         = array(
            "table" => "users",
            "type" => "multiple",
            "alias" => "u.group_id"
        );
        $filter_options["user_id"]          = array(
            "table" => "ownership",
            "type" => "multiple",
            "alias" => "ow.user_id"
        );
        $filter_options["favorites"]        = array(
            "table" => "",
            "type" => "",
            "alias" => ""
        );
        $filter_options["new_only"]         = array(
            "table" => "",
            "type" => "",
            "alias" => ""
        );
        $filter_options["survey"]           = array(
            "table" => "surveys",
            "type" => "",
            "alias" => ""
        );
        $filter_options["urgent"]           = array(
            "table" => "",
            "type" => "",
            "alias" => ""
        );
        $filter_options["source_id"]        = array(
            "table" => "records",
            "type" => "multiple",
            "alias" => "r.source_id"
        );
        $filter_options["dials"]            = array(
            "table" => "records",
            "type" => "custom",
            "alias" => "dials"
        );
        $filter_options["client_id"]        = array(
            "table" => "campaigns",
            "type" => "multiple",
            "alias" => "camp.client_id"
        );
        $filter_options["campaign_type_id"] = array(
            "table" => "campaigns",
            "type" => "multiple",
            "alias" => "camp.campaign_type_id"
        );
        $filter_options["client_ref"]       = array(
            "table" => "client_refs",
            "type" => "id",
            "alias" => "cref.client_ref"
        );
        $filter_options["order"]            = array(
            "table" => "",
            "type" => "",
            "alias" => ""
        );
        $filter_options["order_direction"]  = array(
            "table" => "",
            "type" => "",
            "alias" => ""
        );
        $filter_options["postcode"]         = array(
            "table" => "address",
            "type" => "",
            "alias" => ""
        );
        $filter_options["distance"]         = array(
            "table" => "",
            "type" => "",
            "alias" => ""
        );
        $filter_options["lat"]              = array(
            "table" => "",
            "type" => "",
            "alias" => ""
        );
        $filter_options["lng"]              = array(
            "table" => "",
            "type" => "",
            "alias" => ""
        );
        $qry                                = "";
        $special                            = "";
        $multiple                           = "";
        $join                               = array();
        $where                              = " and r.campaign_id in ({$_SESSION['campaign_access']['list']}) ";
        $order                              = "";
        if (!empty($filter)) {
            //if the filter field is a specific id
            foreach ($filter as $field => $data) {
                $multiple = "";
                
                /* Skip over empty filter fields and remove them from the filter array */
                if (empty($data) && $filter_options[$field]['type'] != "range") {
                    unset($filter[$field]);
                    continue;
                    
                }
                if ($filter_options[$field]['type'] == "range") {
                    if (empty($data[0])) {
                        unset($data[0]);
                        unset($filter[$field][0]);
                    }
                    if (empty($data[1])) {
                        unset($data[1]);
                        unset($filter[$field][1]);
                    }
                }
                
                /* apply the sort field */
                if ($field == "order") {
                    switch ($data) {
                        case "nextcall":
                            $order = " order by CASE WHEN r.nextcall IS NULL THEN 1 ELSE 0 END,r.nextcall ";
                            break;
                        case "lastcall":
                            $order = " order by CASE WHEN r.date_updated IS NULL THEN 1 ELSE 0 END,r.date_updated ";
                            break;
                        case "creation":
                            $order = " order by CASE WHEN r.date_added IS NULL THEN 1 ELSE 0 END,r.date_added ";
                            break;
                        case "turnover":
                            $join['companies'] = " left join companies com on com.urn = r.urn ";
                            $order             = " order by CASE WHEN com.turnover IS NULL THEN 1 ELSE 0 END, com.turnover ";
                            break;
                        case "companies":
                            $join['companies'] = " left join companies on com.urn = r.urn ";
                            $order             = " order by CASE WHEN com.employees IS NULL THEN 1 ELSE 0 END, com.employees  ";
                            break;
                        case "distance":
                            //$order = " order by distance ";  
                            break;
                        case "random":
                            $order = " order by rand() ";
                            break;
                        default:
                            $order = "";
                            break;
                    }
                }
                /* apply the sort order if an order has been set*/
                if ($field == "order_direction" && !empty($order)) {
                    $order .= ($data == "descending" ? " asc" : " desc");
                }
                /*apply the special filetrs */
                if ($field == "favorites") {
                    $where .= " and urn in(select urn from favorites where user_id = '{$_SESSION['user_id']}') ";
                }
                if ($field == "new_only") {
                    $where .= " and r.outcome_id is null and r.progress_id is null and r.nextcall is null and record_status = 1 ";
                }
                if ($field == "urgent") {
                    $where .= " and urgent = '1' ";
                }
                if ($field == "survey") {
                    $where .= " and survey_id is not null ";
                    $join['surveys'] = " left join surveys sur on sur.urn = r.urn ";
                }
                
                /* join any additional tables if they are required */
                if ($filter_options[$field]['table'] == "contacts") {
                    $join['contacts'] = " left join contacts con on con.urn = r.urn ";
                }
                if ($filter_options[$field]['table'] == "surveys") {
                    $join['surveys'] = " left join surveys sur on sur.urn = r.urn ";
                }
                if ($filter_options[$field]['table'] == "client_refs") {
                    $join['client_refs'] = " left join client_refs cref on client_refs.urn = r.urn ";
                }
                if ($filter_options[$field]['table'] == "campaigns") {
                    $join['campaigns'] = " left join campaigns camp on camp.campaign_id = r.campaign_id ";
                }
                if ($filter_options[$field]['table'] == "contact_telephone") {
                    $join['contacts']          = " left join contacts con on c.urn = r.urn ";
                    $join['contact_telephone'] = " left join contact_telephone con_tel on con.contact_id = con_tel.contact_id ";
                }
                if ($filter_options[$field]['table'] == "contact_addresses") {
                    $join['contacts']          = " left join contacts  con on con.urn = r.urn ";
                    $join['contact_addresses'] = " left join contact_addresses con_add on con.contact_id = con_add.contact_id ";
                }
                if ($filter_options[$field]['table'] == "companies") {
                    $join['companies']          = " left join companies com on com.urn = r.urn ";
                    $join['company_subsectors'] = " left join company_subsectors csubsec on csubsec.company_id = com.company_id ";
                    $join['subsectors']         = " left join subsectors subsec on subsec.subsector_id = csubsec.subsector_id ";
                    $join['sectors']            = " left join sectors sec on sec.sector_id = subsec.sector_id ";
                }
                if ($filter_options[$field]['table'] == "ownership") {
                    $join['ownership'] = " left join ownership ow on ow.urn = r.urn";
                }
                if ($filter_options[$field]['table'] == "users") {
                    $join['ownership'] = " left join ownership ow on ow.urn = r.urn";
                    $join['users']     = " left join users u on u.user_id = ow.user_id";
                }
                if ($filter_options[$field]['table'] == "address") {
                    if (!isset($join['companies'])) {
                        $join['companies'] = " left join companies com on com.urn = r.urn ";
                    }
                    if (!isset($join['company_addresses'])) {
                        $join['company_addresses'] = " left join company_addresses com_add on com_add.company_id = com.company_id left join uk_postcodes com_pc on com_add.postcode = com_pc.postcode ";
                    }
                    if (!isset($join['contacts'])) {
                        $join['contacts'] = " left join contacts  con on con.urn = r.urn ";
                    }
                    if (!isset($join['contact_addresses'])) {
                        $join['contact_addresses'] = " left join contact_addresses con_add on con.contact_id = con_add.contact_id left join uk_postcodes con_pc on com_add.postcode = con_pc.postcode";
                    }
                }
                
                //if the filter field has a type of 'id' then it requires an exact match
                if ($filter_options[$field]['type'] == "id" && !empty($data)) {
                    $where .= " and {$filter_options[$field]['alias']} = '$data' ";
                }
                //if the filter ty[e is a 'like' we get rid of any spaces before searching - this helps with postcodes and phone numbers
                if ($filter_options[$field]['type'] == "like" && !empty($data)) {
                    $where .= " and replace({$filter_options[$field]['alias']},' ','') like replace('%$data%',' ','') ";
                }
                //if the filter type is a 'custom' we get the operator from the value
                if ($filter_options[$field]['type'] == "custom" && !empty($data)) {
                    $parts = explode(":", $data);
                    if (isset($parts[1])) {
                        $val       = $parts[0];
                        $operators = array(
                            "less" => "<",
                            "more" => ">",
                            "eless" => "<=",
                            "emore" => ">="
                        );
                        $operator  = (in_array($parts[1], $operators) ? $operators[$parts[1]] : "=");
                        if ($data == "zero") {
                            $val = "0";
                        }
                        $where .= " and {$filter_options[$field]['alias']} $operator '$val' ";
                    } else {
                        $where .= " and {$filter_options[$field]['alias']} = '$data' ";
                    }
                }
                //if the filter type is a multiselect or checkboxes we have to loop through and add them inside brackets
                if ($filter_options[$field]['type'] == "multiple" && count($data)) {
                    $where .= " and (";
                    foreach ($data as $val) {
                        $multiple .= " {$filter_options[$field]['alias']} = '$val' or";
                    }
                    $multiple = rtrim($multiple, "or");
                    $where .= $multiple . " )";
                    
                }
                
                //if the filter field is a data range or integer range we use 'between' both start and end should be populated for this to work 
                if ($filter_options[$field]['type'] == "range" && count($filter_options[$field])) {
                    if (in_array($field, array(
                        "dob",
                        "nextcall",
                        "date_updated",
                        "date_added"
                    ))) {
                        //convert these fields to mysql format
                        $data = array_map("to_mysql_datetime", $data);
                    }
                    if (isset($data[0]) && isset($data[1])) {
                        $where .= " and {$filter_options[$field]['alias']} between '{$data[0]}' and '{$data[1]}' and {$filter_options[$field]['alias']} is not null ";
                    } else if (isset($data[0]) && !isset($data[1])) {
                        $where .= " and {$filter_options[$field]['alias']} > '{$data[0]}' and {$filter_options[$field]['alias']} is not null ";
                    } else if (!isset($data[0]) && isset($data[1])) {
                        $where .= " and {$filter_options[$field]['alias']} < '{$data[1]}'and {$filter_options[$field]['alias']} is not null  ";
                    }
                }
                
                if ($field == 'postcode' && count($filter[$field])) {
                    $distance = ($filter['distance']) ? $filter['distance'] : 0;
                    
                    if (isset($filter['lat']) && isset($filter['lng'])) {
                        
                        $where .= " and ( ";
                        //Distance from the company or the contacts addresses
                        $where .= " (";
                        $where .= $filter['lat'] . " BETWEEN (com_pc.lat-" . $distance . ") AND (com_pc.lat+" . $distance . ")";
                        $where .= " and " . $filter['lng'] . " BETWEEN (com_pc.lng-" . $distance . ") AND (com_pc.lng+" . $distance . ")";
                        $where .= " and ((((
							ACOS(
								SIN(" . $filter['lat'] . "*PI()/180) * SIN(com_pc.lat*PI()/180) +
								COS(" . $filter['lat'] . "*PI()/180) * COS(com_pc.lat*PI()/180) * COS(((" . $filter['lng'] . " - com_pc.lng)*PI()/180)
							)
						)*180/PI())*160*0.621371192)) <= " . $distance . ")";
                        
                        $where .= " ) or (";
                        
                        $where .= $filter['lat'] . " BETWEEN (con_pc.lat-" . $distance . ") AND (con_pc.lat+" . $distance . ")";
                        $where .= " and " . $filter['lng'] . " BETWEEN (con_pc.lng-" . $distance . ") AND (con_pc.lng+" . $distance . ")";
                        $where .= " and ((((
							ACOS(
								SIN(" . $filter['lat'] . "*PI()/180) * SIN(con_pc.lat*PI()/180) +
								COS(" . $filter['lat'] . "*PI()/180) * COS(con_pc.lat*PI()/180) * COS(((" . $filter['lng'] . " - con_pc.lng)*PI()/180)
							)
						)*180/PI())*160*0.621371192)) <= " . $distance . ")";
                        
                        $where .= " ))";
                    }
                    
                }
                
            }
            foreach ($join as $join_query) {
                $qry .= $join_query;
            }
            
        }
        //if the second parameter in the function is set to true then we will store the filter into the session so it's use throughout the system
        if ($use) {
            $_SESSION['filter']['join']  = $join;
            $_SESSION['filter']['where'] = $where;
            $_SESSION['filter']['order'] = $order . ",urn";
        }
        if (!empty($where)) {
            $qry .= " where 1 " . $where;
        }
        
        if (isset($filter['campaign_id'])) {
            if (count($filter['campaign_id']) == "1") {
                $_SESSION['current_campaign'] = $filter['campaign_id'][0];
            } else if (count($filter['campaign_id']) > "1") {
                unset($_SESSION['current_campaign']);
            }
        }
        
        return $qry;
        
    }
    
    public function clean_filter($filter)
    {
        foreach ($filter as $k => $v) {
            if (is_array($v)) {
                $filter[$k] = array_filter($v);
                if (!count($filter[$k])) {
                    unset($filter[$k]);
                }
            } else {
                if (empty($v)) {
                    unset($filter[$k]);
                }
            }
            
        }
        return $filter;
    }
    
    public function custom_search($options)
    {
        $table             = $_SESSION['custom_view']['table'];
        $fields            = $_SESSION['custom_view']['fields'];
        $array             = $_SESSION['custom_view']['array'];
        $group_by          = "";
        $agent             = "";
        $or                = array();
        $outcome_selection = "if(cc.campaign_name is not null,concat('Cross transfer to ',cc.campaign_name),outcome) outcome";
        
        if ($table == "records") {
            $table_columns = array(
                "campaigns.campaign_name",
                "fullname",
                "outcome",
                "date_format(r.date_updated,'%d/%m/%y %H:%i')",
                "date_format(records.nextcall,'%d/%m/%y %H:%i')",
                "rand()"
            );
            $qry           = "select campaigns.campaign_name,$table.urn,fullname,$outcome_selection,date_format($table.nextcall,'%d/%m/%y %H:%i') nextcall, date_format(records.date_updated,'%d/%m/%y %H:%i') date_updated from $table left join contacts on records.urn = contacts.urn left join campaigns on $table.campaign_id = campaigns.campaign_id left join outcomes on outcomes.outcome_id = $table.outcome_id left join progress_description on progress_description.progress_id = records.progress_id left join ownership on records.urn = ownership.urn left join users on users.user_id = ownership.user_id left join data_sources on data_sources.source_id = records.source_id";
            $group_by      = " group by records.urn";
        } else {
            $join_records  = " left join records on records.urn = history.urn ";
            $table_columns = array(
                "campaigns.campaign_name",
                "fullname",
                "outcome",
                "date_format(contact,'%d/%m/%y %H:%i')",
                "date_format(records.nextcall,'%d/%m/%y %H:%i')",
                "rand()"
            );
            $qry  = "select campaigns.campaign_name,$table.urn,fullname,$outcome_selection,date_format($table.contact,'%d/%m/%y %H:%i') date_updated, date_format(records.nextcall,'%d/%m/%y %H:%i') nextcall from $table $join_records left join contacts on records.urn = contacts.urn left join campaigns on records.campaign_id = campaigns.campaign_id left join outcomes on outcomes.outcome_id = $table.outcome_id left join progress_description on progress_description.progress_id = records.progress_id  ";
            $group_by = " group by history_id ";
            
            
            //if agent they can only see todays
            if (in_array("set call outcomes", $_SESSION['permissions'])) {
                $agent .= " and date(contact) = curdate() ";
            }
        }
        
        //only join the status table if we need it
        if (in_array("source", $fields)) {
            $qry .= " left join data_sources on data_sources.source_id = records.source_id ";
        }
        if (in_array("status", $fields)) {
            $qry .= " left join status_list on records.record_status = status_list.record_status_id ";
        }
        if ($table == "records") {
            //join the cross transfer table
            $qry .= " left join history on records.urn = history.urn and history.contact = records.date_updated";
            $qry .= " left join cross_transfers on cross_transfers.history_id = history.history_id ";
            $qry .= " left join campaigns cc on cc.campaign_id = cross_transfers.campaign_id ";
            //only join the user tables if we need them
            if (in_array("user", $fields) || in_array("group_id", $fields) || in_array("team", $fields)) {
                $qry .= " left join ownership on records.urn = ownership.urn ";
                $qry .= " left join users on users.user_id = ownership.user_id ";
                $qry .= " left join teams on users.team_id = teams.team_id ";
            }
        } else {
            //join the cross transfer table
            $qry .= " left join cross_transfers on cross_transfers.history_id = history.history_id ";
            $qry .= " left join campaigns cc on cc.campaign_id = cross_transfers.campaign_id ";
            
            if (in_array("user", $fields) || in_array("group_id", $fields) || in_array("team", $fields)) {
                $qry .= " left join users on history.user_id = users.user_id ";
                $qry .= " left join teams on users.team_id = teams.team_id ";
            }
        }
        //only join the survey tables if we need them
        if (in_array("survey", $fields) || in_array("score", $fields) || in_array("question", $fields) || in_array("survey-from", $fields) || in_array("survey-to", $fields)) {
            $qry .= " left join surveys on surveys.urn = records.urn ";
            $qry .= " left join survey_info on surveys.survey_info_id = survey_info.survey_info_id ";
            $qry .= " left join survey_answers on surveys.survey_id = survey_answers.survey_id ";
        }
        
        //this gets ALL transfers including cross transfers
        $all_transfer = "";
        $all_dials    = "";
        $contact_qry  = "";
        if (in_array("contact-from", $fields)) {
            $contact_qry .= " and date(contact) >= '" . $array['contact-from'] . "'";
            unset($array['contact-from']);
        }
        if (in_array("contact-to", $fields)) {
            $contact_qry .= " and date(contact) <= '" . $array['contact-to'] . "'";
            unset($array['contact-to']);
        }
        if (in_array("transfers", $fields)) {
            $all_transfer = " and  (campaigns.campaign_id = '" . $array['transfers'] . "' and outcomes.outcome_id=70 or cc.campaign_id = " . $array['transfers'] . " and outcomes.outcome_id='71')";
            unset($array['outcome']);
            unset($array['cross']);
            unset($array['transfers']);
            unset($array['campaigns.campaign_id']);
        }
        if (in_array("alldials", $fields)) {
            if (intval($array['alldials'])) {
                $all_dials = " and  (campaigns.campaign_id = '" . $array['alldials'] . "' and outcomes.outcome_id<>71 or cc.campaign_id = " . $array['alldials'] . " and outcomes.outcome_id = 71) ";
            } else {
                $all_dials = " and  (outcomes.outcome_id<>71 or cc.campaign_id is not null) ";
            }
            unset($array['outcome']);
            unset($array['cross']);
            unset($array['alldials']);
            unset($array['campaigns.campaign_id']);
        }
        
        $qry .= " where campaigns.campaign_id in({$_SESSION['campaign_access']['list']}) $agent $all_transfer $all_dials $contact_qry";
        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $qry .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        $where_array = array();
        foreach ($array as $key => $val) {
            $keysplit = explode(":", $key);
            $key      = $keysplit[0];
            $split    = explode(":", $val);
            if (isset($split[1])) {
                $val      = $split[0];
                $operator = $split[1];
                if ($operator == "less") {
                    $where_array[$key] = " and " . $key . " < '" . $val . "'";
                }
                if ($operator == "more") {
                    $where_array[$key] = " and " . $key . " > '" . $val . "'";
                }
                if ($operator == "eless") {
                    $where_array[$key] = " and " . $key . " <= '" . $val . "'";
                }
                if ($operator == "emore") {
                    $where_array[$key] = " and " . $key . " >= '" . $val . "'";
                }
                if ($operator == "not") {
                    if ($val == "null") {
                        $where_array[$key] = " and " . $key . " is not null ";
                    } else {
                        $where_array[$key] = " and " . $key . " <> '" . $val . "'";
                    }
                }
            }
            if (empty($operator)) {
                if ($val == "null") {
                    $where_array[$key] = " and " . $key . " is null ";
                } else {
                    $where_array[$key] = " and " . $key . " = '" . $val . "'";
                }
            }
            if (isset($keysplit[1])) {
                if (isset($or[$key])) {
                    $or[$key] .= str_replace("and", "or", $where_array[$key]);
                } else {
                    $or[$key] = str_replace("and", "or", $where_array[$key]);
                }
                unset($where_array[$key]);
            }
            
        }
        
        foreach ($or as $key => $or_clause) {
            if (!empty($or_clause)) {
                $where_array[$key] = " and (" . substr($or_clause, 3) . ") ";
            }
        }
        
        foreach ($where_array as $where_clause) {
            $qry .= $where_clause;
        }
        
        
        $qry .= $group_by;
		$this->firephp->log($qry);
        $start  = $options['start'];
        $length = $options['length'];
		$qry .= $group_by;
        $qry .= " order by CASE WHEN " . $table_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $table_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'];
        $count = $this->db->query($qry)->num_rows();
        $qry .= " limit " . $options['length'] . " offset " . $options['start'];
        $data = $this->db->query($qry)->result_array();
        return array(
            "count" => $count,
            "data" => $data
        );
    }
}