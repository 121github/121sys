<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Filter_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
		$_aliases = array( 
"records" => "r",
"companies" => "com",
"company_telephone" => "comt",
"company_addresses" => "coma",
"contacts" => "con",
"contact_telephone" => "cont",
"contact_addresses" => "cona",
"ownership" => "ow",
"outcomes" => "o",
"clients" => "cl",
"client_refs" => "cr",
"campaigns" => "c",
"campaign_types" => "ct",
"appointments" => "a",
"data_sources" => "ds",
"email_history" => "eh",
"history" => "h",
"record_details" => "rd",
"surveys" => "s",
"survey_info" => "si",
"answers" => "ans",
"questions" => "q",
"users" => "u",
"sectors" => "sec",
"subsectors" => "subsec"
);
    }


    public function count_records($filter)
    {
        $qry   = "select distinct r.urn from records r ";
        //convert the filter options into a query and them to the base query
        $addon = $this->Filter_model->create_query_filter($filter);
        $qry .= $addon;
		$this->firephp->log($qry);
		$result = array(
            "query" => $qry,
            "data" => $this->db->query($qry)->result_array()
        );
        return $result;
    }

    public function get_urn_list($qry) {
        return $this->db->query($qry)->result_array();
    }
    
    public function apply_filter($filter)
    {
        //setting the second parameter to 'true' stores the filter in the session
        $this->Filter_model->create_query_filter($filter, true);
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
            "alias" => "r.outcome_id"
        );
        $filter_options["coname"]           = array(
            "table" => "companies",
            "type" => "like",
            "alias" => "com.name"
        );
        $filter_options["sector_id"]        = array(
            "table" => "subsectors",
            "type" => "multiple",
            "alias" => "sec.sector_id"
        );
        $filter_options["subsector_id"]     = array(
            "table" => "company_subsectors",
            "type" => "multiple",
            "alias" => "subsec.subsector_id"
        );
        $filter_options["company_id"]       = array(
            "table" => "companies",
            "type" => "id",
            "alias" => "com.company_id"
        );
        $filter_options["company_phone"]            = array(
            "table" => "company_telephone",
            "type" => "like",
            "alias" => "comt.telephone_number"
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
            "alias" => "cont.telephone_number"
        );
        $filter_options["address"]          = array(
            "table" => "contact_addresses",
            "type" => "like",
            "alias" => "concat(cona.add1,cona.add2,cona.add3,cona.postcode)"
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
        $filter_options["email"]              = array(
            "table" => "",
            "type" => "",
            "alias" => ""
        );
        $qry                                = "";
        $special                            = "";
		$parked								= " parked_code is null";
        $multiple                           = "";
		$skip = false; //this is used to skip the first value in a multiple array
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
                /*apply the email filetrs */
                if ($field == "email") {
                    $email_where = " and (";
                    $i = 0;
                    foreach($data as $val) {
                        $or = ($i==0)?"":" or";
                        switch ($val) {
                            case "sent":
                                $email_where .= $or." eh.status = 1";
                                break;
                            case "read":
                                $email_where .= $or." eh.read_confirmed = 1";
                                break;
                            case "unsent":
                                $email_where .= $or." eh.status = 0";
                                break;
                            default:
                                break;
                        }
                        $i++;
                    }
                    $email_where .= " )";
                    if (count($data) > 0) {
                        $join['email'] = " left join email_history eh on eh.urn = r.urn ";
                        $where .= $email_where;
                    }
                }
                /*apply the special filetrs */
                if ($field == "favorites") {
					if(@!in_array("search any owner",$_SESSION['permissions'])){
                    $where .= " and r.urn in(select distinct urn from favorites where user_id = '{$_SESSION['user_id']}') ";
					} else {
					$where .= " and r.urn in(select distinct urn from favorites) ";	
					}
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
				 if ($filter_options[$field]['table'] == "outcomes") {
                    $join['contacts'] = " left join outcomes on outcomes.outcome_id = r.outcome_id";
                }
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
                    $join['contact_telephone'] = " left join contact_telephone cont on con.contact_id = cont.contact_id ";
                }
                if ($filter_options[$field]['table'] == "contact_addresses") {
                    $join['contacts']          = " left join contacts  con on con.urn = r.urn ";
                    $join['contact_addresses'] = " left join contact_addresses cona on con.contact_id = cona.contact_id ";
                }
                if ($filter_options[$field]['table'] == "companies") {
                    $join['companies']          = " left join companies com on com.urn = r.urn ";
                    $join['company_subsectors'] = " left join company_subsectors csubsec on csubsec.company_id = com.company_id ";
                    $join['subsectors']         = " left join subsectors subsec on subsec.subsector_id = csubsec.subsector_id ";
                    $join['sectors']            = " left join sectors sec on sec.sector_id = subsec.sector_id ";
                }
                if ($filter_options[$field]['table'] == "company_telephone") {
                    $join['companies']          = " left join companies com on com.urn = r.urn ";
                    $join['company_telephone'] = " left join company_telephone comt on com.company_id = comt.company_id ";
                }
                if ($filter_options[$field]['table'] == "ownership") {
                    $join['ownership'] = " left join ownership ow on ow.urn = r.urn";
                }
                if ($filter_options[$field]['table'] == "users"||!in_array("search any owner",$_SESSION['permissions'])) {
                    $join['ownership'] = " left join ownership ow on ow.urn = r.urn";
                    $join['users']     = " left join users u on u.user_id = ow.user_id";
                }
                if ($filter_options[$field]['table'] == "address") {
                    if (!isset($join['companies'])) {
                        $join['companies'] = " left join companies com on com.urn = r.urn ";
                    }
                    if (!isset($join['company_addresses'])) {
                        $join['company_addresses'] = " left join company_addresses coma on coma.company_id = com.company_id left join locations com_pc on coma.location_id = com_pc.location_id ";
                    }
                    if (!isset($join['contacts'])) {
                        $join['contacts'] = " left join contacts  con on con.urn = r.urn ";
                    }
                    if (!isset($join['contact_addresses'])) {
                        $join['contact_addresses'] = " left join contact_addresses cona on con.contact_id = cona.contact_id left join locations con_pc on coma.location_id = con_pc.location_id";
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
					if($filter_options[$field]['alias'] == "r.parked_code" && $data[0]=="0"){
					$parked = "";	$skip = true; //don't include "0" in the search below
					}
					if($filter_options[$field]['alias'] == "ow.user_id" && $data[0]=="0"){
					$multiple .=" ow.user_id is null or";	$skip = true; //don't include "0" in the search below
					}
                    foreach ($data as $val) {
						if(!$skip){
                        $multiple .= " {$filter_options[$field]['alias']} = '$val' or";
						} else {
						//once we have skipped the first zero we set skip to false so no other values are ignored.
						$skip = false;	
						}
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
		
		/* users can only see records that have not been parked */
		 if (!isset($_SESSION['filter']['values']['parked_code'])||@!in_array("search parked",$_SESSION['permissions'])) {
        $parked .= " and (parked_code is null)";
        }
		
		
		$where .= $parked;
		
		//users can see unaassigned records
		if(@in_array("search unasssigned",$_SESSION['permissions'])){
		$unassigned = " or ow.user_id is null ";	
		} else {
		$unassigned = "";	
		}
		
		//users can only see their own records
		if(!in_array("search any owner",$_SESSION['permissions'])){
		$where .= " and (ow.user_id = '{$_SESSION['user_id']}' $unassigned) ";	
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
                "date_format(records.date_updated,'%d/%m/%y %H:%i')",
                "date_format(records.nextcall,'%d/%m/%y %H:%i')",
                "rand()"
            );
			 $order_columns = array(
                "campaigns.campaign_name",
                "fullname",
                "outcome",
                "records.date_updated",
                "records.nextcall",
                "rand()"
            );
            $qry           = "select campaigns.campaign_name,$table.urn,fullname,$outcome_selection,date_format($table.nextcall,'%d/%m/%y %H:%i') nextcall, date_format(records.date_updated,'%d/%m/%y %H:%i') date_updated from $table left join contacts on records.urn = contacts.urn left join campaigns on $table.campaign_id = campaigns.campaign_id left join outcomes on outcomes.outcome_id = $table.outcome_id left join progress_description on progress_description.progress_id = records.progress_id left join data_sources on data_sources.source_id = records.source_id";
            $group_by      = " group by records.urn";
        } else {
            $join_records  = " left join records on records.urn = history.urn ";
            $table_columns = array(
                "campaigns.campaign_name",
                "fullname",
                "outcome",
                "date_format(contact,'%d/%m/%y %H:%i')",
                "date_format(history.nextcall,'%d/%m/%y %H:%i')",
                "rand()"
            );
						 $order_columns = array(
                "campaigns.campaign_name",
                "fullname",
                "outcome",
                "contact",
                "history.nextcall",
                "rand()"
            );
            $qry  = "select campaigns.campaign_name,$table.urn,fullname,$outcome_selection,date_format($table.contact,'%d/%m/%y %H:%i') date_updated, date_format(records.nextcall,'%d/%m/%y %H:%i') nextcall from $table $join_records left join contacts on records.urn = contacts.urn left join campaigns on records.campaign_id = campaigns.campaign_id left join outcomes on outcomes.outcome_id = $table.outcome_id left join progress_description on progress_description.progress_id = records.progress_id  ";
            $group_by = " group by history.history_id ";
            
            
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

        //only join the email tables if we need them
        $email_qry = "";
		$template_qry = "";
        if (in_array("emails", $fields)||in_array("template", $fields)) {
			 $qry .= " left join email_history on email_history.urn = records.urn ";
		}
		if (in_array("emails", $fields)){
            if ($array['emails'] == "read") {
                $email_qry = " and email_history.read_confirmed = 1";
            }
            else if ($array['emails'] == "sent") {
                $email_qry = " and email_history.`status` = 1";
            }
            else if ($array['emails'] == "unsent") {
                $email_qry = " and email_history.`status` = 0";
            }
			else if ($array['emails'] == "new") {
                $email_qry = " and email_history.read_confirmed = 1 and email_history.read_confirmed_date > records.date_updated ";
            }
            unset($array['emails']);
        }

        $sent_date_qry  = "";
        if (in_array("sent-email-from", $fields)) {
            $sent_date_qry .= " and date(email_history.sent_date) >= '" . $array['sent-email-from'] . "'";
            unset($array['sent-email-from']);
        }
        if (in_array("sent-email-to", $fields)) {
            $sent_date_qry .= " and date(email_history.sent_date) <= '" . $array['sent-email-to'] . "'";
            unset($array['sent-email-to']);
        }

        $user_email_sent_qry = '';
        if (in_array("user-email-sent-id", $fields)) {
            $sent_date_qry .= " and email_history.user_id = '" . $array['user-email-sent-id'] . "'";
            unset($array['user-email-sent-id']);
        }

        //For parked records
        $parked_qry = "";
        if (in_array("parked", $fields)){
            if ($array['parked'] == "yes") {
                $parked_qry = " and records.parked_code is not null";
            }
            else if ($array['parked'] == "no") {
                $parked_qry = " and records.parked_code is null";
            }
            unset($array['parked']);
        }

        //this gets ALL transfers including cross transfers
        $all_transfer = "";
        $all_dials    = "";
        $contact_qry  = "";
		$parked  = "";
		
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

        $update_date_qry = "";
        if (in_array("update-date-from", $fields) || in_array("update-date-to", $fields) || in_array("renewal-date-from", $fields) || in_array("renewal-date-to", $fields)) {
            $update_date = "";
            $update_date_from = "";
            $update_date_to = "";
            if (in_array("update-date-from", $fields)) {
                $update_date_from = "(date(records.date_updated) >= '".$array['update-date-from']."' or (records.date_updated is null and date(records.date_added) >=  '".$array['update-date-from']."'))";
                unset($array['update-date-from']);
            }
            if (in_array("update-date-to", $fields)) {
                $update_date_to = "(date(records.date_updated) <= '".$array['update-date-to']."' or (records.date_updated is null and date(records.date_added) <=  '".$array['update-date-to']."'))";
                unset($array['update-date-to']);
            }
            $update_date .= $update_date_from.(strlen($update_date_from)>0 && strlen($update_date_to)>0?" and ":"").$update_date_to;

            $renewal_date = "";
            $renewal_date_from = "";
            $renewal_date_to = "";
            if (in_array("renewal-date-from", $fields) || in_array("renewal-date-to", $fields)) {
                $qry  .= " inner join record_details rd ON (rd.urn = records.urn)";
            }
            if (in_array("renewal-date-from", $fields)) {
                $renewal_date_from = "(date(rd.d1) >= '".$array['renewal-date-from']."')";
                unset($array['renewal-date-from']);
            }
            if (in_array("renewal-date-to", $fields)) {
                $renewal_date_to = (strlen($renewal_date)>0?" and ":"")."(date(rd.d1) >= '".$array['renewal-date-to']."')";
                unset($array['renewal-date-to']);
            }
            $renewal_date .= $renewal_date_from.(strlen($renewal_date_from)>0 && strlen($renewal_date_to)>0?" and ":"").$renewal_date_to;

            if (strlen($update_date)>0 || strlen($renewal_date)>0) {
                $update_date_qry .= " and (".$update_date.(strlen($update_date)>0 && strlen($renewal_date)>0?" or ":"").$renewal_date.")";
            }
        }

        $qry .= " where campaigns.campaign_id in({$_SESSION['campaign_access']['list']}) $parked $agent $all_transfer $all_dials $contact_qry $email_qry $sent_date_qry $template_qry $parked_qry $update_date_qry";
		
		
		
        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $qry .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        $where_array = array();
        foreach ($array as $key => $val) {
			//reset the variables
			$split = array();
			$keysplit = array();
			$operator = "";
			//split the key to see if it woll use "or" or "and"
            $keysplit = explode(":", $key);
            $key      = $keysplit[0];
			//split the value to see which operator it will use
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
        $qry .= " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'];
		$this->firephp->log($qry);
        $count = $this->db->query($qry)->num_rows();
        $qry .= " limit " . $options['length'] . " offset " . $options['start'];
        $data = $this->db->query($qry)->result_array();
        return array(
            "count" => $count,
            "data" => $data
        );
    }

    public function save_parked_code($form) {

        $urn_list = $form['urn_list'];
        $parked_code_id = $form['parked_code_id'];

        $qry = "UPDATE records SET parked_code = ".$parked_code_id." WHERE urn IN ".$urn_list;
        return $this->db->query($qry);
    }
    
    public function get_suppressed_numbers() {
    	$numbers = array();

        $qry = "SELECT s.suppression_id, s.telephone_number, s.date_added, s.reason, sc.campaign_id
                FROM suppression s
                LEFT JOIN suppression_by_campaign sc ON (sc.suppression_id = s.suppression_id)";
    	
    	return $this->db->query($qry)->result_array();
    }

    public function insert_suppression_number($form) {
        $this->db->insert('suppression', $form);
        return $this->db->insert_id();
    }

    public function update_suppression_number($form) {
        $form['date_updated'] = date("Y-m-d H:i:s");
        $this->db->where('telephone_number', $form['telephone_number']);
        return $this->db->update('suppression', $form);
    }

    public function insert_suppression_by_campaign($suppression_id, $campaign_id) {
        $this->db->insert('suppression_by_campaign', array(
            "suppression_id" => $suppression_id,
            "campaign_id" => $campaign_id
        ));
    }

    public function remove_suppression_by_campaign($suppression_id) {
        $this->db->where('suppression_id', $suppression_id);
        $this->db->delete('suppression_by_campaign');
    }
    
    public function suppress_phone_numbers($phone_number_list, $suppressed_number_list, $all_campaigns, $campaign_id) {
        foreach($phone_number_list as $phone_number)
    	{
    		if (!isset($suppressed_number_list[$phone_number['telephone_number']])) {
                $this->db->insert('suppression', $phone_number);
                $suppression_id = $this->db->insert_id();
                if (!$all_campaigns && $suppression_id) {
                    $this->db->insert('suppression_by_campaign', array(
                        "suppression_id" => $suppression_id,
                        "campaign_id" => $campaign_id
                    ));
                }
            }
            else {
                foreach($suppressed_number_list[$phone_number['telephone_number']] as $suppressed_number) {
                    if ($all_campaigns) {
                        $this->db->where('suppression_id', $suppressed_number['suppression_id']);
                        $this->db->delete('suppression_by_campaign');
                    }
                    else {
                        $this->db->insert('suppression_by_campaign', array(
                            "suppression_id" => $suppressed_number['suppression_id'],
                            "campaign_id" => $campaign_id
                        ));
                    }
                }
            }
    	}
    }

    public function add_ownership($form) {
        return $this->db->insert_batch('ownership', $form);
    }

    public function remove_ownership_by_urn_list($urn_list) {
        $qry = "DELETE FROM ownership WHERE urn IN $urn_list";
        return $this->db->query($qry);
    }

    /**************************************************************/
    /*************** COPY RECORDS ********************************/
    /**************************************************************/

    public function copy_records($records) {
    	return $this->db->insert_batch('records', $records);
    }
    
    public function copy_record_details($record_details) {
    	return $this->db->insert_batch('record_details', $record_details);
    }
    
    public function copy_companies($companies) {
    	return $this->db->insert_batch('companies', $companies);
    }

    public function copy_company_addresses($company_addresses) {
        return $this->db->insert_batch('company_addresses', $company_addresses);
    }

    public function copy_company_subsectors($company_subsectors) {
        return $this->db->insert_batch('company_subsectors', $company_subsectors);
    }

    public function copy_company_telephone($company_telephone) {
        return $this->db->insert_batch('company_telephone', $company_telephone);
    }


    public function copy_contacts($contacts) {
        return $this->db->insert_batch('contacts', $contacts);
    }

    public function copy_contact_addresses($contact_addresses) {
        return $this->db->insert_batch('contact_addresses', $contact_addresses);
    }

    public function copy_contact_telephone($contact_telephone) {
        return $this->db->insert_batch('contact_telephone', $contact_telephone);
    }

    public function get_next_autoincrement_id ($table) {
        $next = $this->db->query("SHOW TABLE STATUS LIKE '".$table."'");
        $next = $next->row(0);
        $next->Auto_increment;
        return $next->Auto_increment;
    }

    public function get_urns_inserted($urn_list, $urn_from) {
    	$qry = "select urn, urn_copied
				from records
				where urn_copied IN ".$urn_list."
				and urn >= ".$urn_from;
    	 
    	return $this->db->query($qry)->result_array();
    }
    
    public function get_companies_inserted($company_list, $company_id_from) {
    	$qry = "select company_id, company_copied
				from companies
				where company_copied IN ".$company_list."
				and company_id >= ".$company_id_from;
    
    	return $this->db->query($qry)->result_array();
    }

    public function get_contacts_inserted($contact_list, $contact_id_from) {
        $qry = "select contact_id, contact_copied
				from contacts
				where contact_copied IN ".$contact_list."
				and contact_id >= ".$contact_id_from;

        return $this->db->query($qry)->result_array();
    }
}