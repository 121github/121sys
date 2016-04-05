<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Records_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->name_field = "concat(title,' ',firstname,' ',lastname)";
    }
	
	public function save_notes($urn,$notes){
		
	 $this->db->where("urn", $urn);
    return $this->db->replace('sticky_notes', array(
                "note" => $notes,
                "urn" => $urn,
                "updated_by" => @$_SESSION['user_id']
            ));
			
	}
	
	public function update_custom_source_field($urn,$source_id){
		$check = "select field from record_details_fields join campaigns using(campaign_id) join records using(campaign_id) where urn = '$urn' and is_source = 1";
		if($this->db->query($check)->num_rows()>0){
			$field = $this->db->query($check)->row()->field;
		$query = "update record_details set `$field` = (select source_name from data_sources where source_id = '$source_id')";
		$this->db->query($query); 
		}
	}
	public function update_custom_pot_field($urn,$pot_id){
		$check = "select field from record_details_fields join campaigns using(campaign_id) join records using(campaign_id) where urn = '$urn' and is_pot = 1";
		if($this->db->query($check)->num_rows()>0){
			$field = $this->db->query($check)->row()->field;
		$query = "update record_details set `$field` = (select pot_name from data_pots where pot_id = '$pot_id')";
		$this->db->query($query); 
	}
	}
	
	public function save_record_options($data){
	$this->db->where("urn",$data['urn']);
	$this->db->update("records",$data);
	}
	
	public function get_task_history($urn){
		$qry = "select task_name task, task_status, date_format(`timestamp`,'%d/%m/%Y %H:%i') `date`, name from task_history join users using(user_id) join tasks using(task_id) join task_status_options using(task_status_id) order by `timestamp` desc";
		return $this->db->query($qry)->result_array();
	}

    public function check_max_dials($urn)
    {
        //checks if a record has had too many non-contactable outcomes and removes it from the pot
        $qry = "update records r join (select urn,max_dials,count(*) count from history left join outcomes using(outcome_id) left join campaigns using(campaign_id) where delay_hours is not null group by urn)md on md.urn = r.urn set r.outcome_id = 137, r.outcome_reason_id = null, r.record_status = 3 where r.urn = '$urn' and max_dials <= count and r.record_status = 1 and max_dials <> 0";
        $this->db->query($qry);
        return $this->db->affected_rows();
    }

    public function update($urn, $data)
    {
        $this->db->where("urn", $urn);
        $this->db->where_in('campaign_id', $_SESSION['campaign_access']['array']);
        $this->db->update("records", $data);
    }

    public function save_task($data)
    {
        //save the task status
        $this->db->insert_update("record_tasks", $data);
        //save to task history
        $task_history = $data;
        $task_history['user_id'] = $_SESSION['user_id'];
        $this->db->insert("task_history", $task_history);
    }

    public function save_record_color($urn, $color)
    {
        $color = color_name_to_hex($color);
        $this->db->where("urn", $urn);
        $this->db->update("records", array("record_color" => $color));
    }

    public function get_campaign_tasks($campaign_id)
    {
        $qry = "select task_id,task_name,task_status_id,task_status,task_name from campaign_tasks join tasks using(task_id) left join tasks_to_options using(task_id) left join task_status_options using(task_status_id) where campaign_id = '$campaign_id'";
        return $this->db->query($qry)->result_array();
    }

    public function get_record_tasks($urn)
    {
        $qry = "select task_id,task_status_id from record_tasks where urn = '$urn'";
        return $this->db->query($qry)->result_array();
    }

    public function find_related_records($urn=false, $campaign = false, $original = false)
    {
		if($urn&&!$original){
        $qry = "select companies.name,companies.website,concat(coma.add1,coma.postcode) address,concat(cona.add1,cona.postcode) contact_address,comt.telephone_number company_telephone,cont.telephone_number contact_telephone,concat(fullname,dob) contact from records left join companies using(urn) left join company_addresses coma using(company_id) left join contacts using(urn) left join company_telephone comt using(company_id) left join contact_telephone cont using(contact_id) left join contact_addresses cona using(contact_id)  where urn = '$urn'";
        if ($campaign) {
            $qry .= " and campaign_id = '$campaign'";
        }
        $qry .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $result = $this->db->query($qry)->result_array();
        $original = array();
        foreach ($result as $row) {
            if ($row['name']) {
                $original['name'] = $row['name'];
            }
            if ($row['website']) {
                $original['website'] = $row['website'];
            }
            if ($row['contact']) {
                $original['contacts'][$row['contact']] = str_replace(array("Mr", "Mrs", "Miss"), array("", "", ""), $row['contact']);
            }
            if ($row['address']) {
                $original['addresses'][$row['address']] = $row['address'];
            }
            if ($row['company_telephone']) {
                $original['company_numbers'][$row['company_telephone']] = $row['company_telephone'];
            }
            if ($row['contact_telephone']) {
                $original['contact_numbers'][$row['contact_telephone']] = $row['contact_telephone'];
            }
            if ($row['contact_address']) {
                $original['contact_addresses'][$row['contact_address']] = $row['contact_address'];
            }
        }
		}
        //now look for matches using the data from the original
        $matches = array();
        foreach ($original as $k => $v) {
            if ($k == "name") {
                $name = str_ireplace("limited", "", $v);
                $name = str_ireplace("ltd", "", $name);
                $name = str_ireplace("plc", "", $name);
                $name = str_ireplace(" ", "", $name);

                $query = "select urn,'company name' matched_on from companies left join records using(urn) where replace(replace(replace(name,'limited',''),'ltd',''),' ','') = '" . addslashes($name) . "' and urn <> $urn";
                if ($campaign) {
                    $query .= " and campaign_id = '$campaign'";
                }
                $query .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
                $co_matches = $this->db->query($query)->result_array();
                array_push($matches, $co_matches);
            }
            if ($k == "website") {
                $query = "select urn, 'website' matched_on from companies left join records using(urn) where website = '" . addslashes($v) . "' and urn <> $urn";
                if ($campaign) {
                    $query .= " and campaign_id = '$campaign'";
                }
                $query .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
                $website_matches = $this->db->query($query)->result_array();
                array_push($matches, $website_matches);
            }
            if ($k == "contacts") {
                foreach ($v as $contact) {
                    $query = "select urn,'contact name' matched_on from contacts left join records using(urn) where fullname = '" . addslashes($contact) . "' and urn <> $urn";
                    if ($campaign) {
                        $query .= " and campaign_id = '$campaign'";
                    }
                    $query .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
                    $contact_matches = $this->db->query($query)->result_array();
                    array_push($matches, $contact_matches);
                }
            }
            if ($k == "addresses") {
                foreach ($v as $address) {
                    $query = "select urn,'address' matched_on from companies left join records using(urn) inner join company_addresses using(company_id) where concat(add1,postcode) = '$address' and urn <> $urn";
                    if ($campaign) {
                        $query .= " and campaign_id = '$campaign'";
                    }
                    $query .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
                    $address_matches = $this->db->query($query)->result_array();
                    array_push($matches, $address_matches);
                }
            }
            if ($k == "contact_addresses") {
                foreach ($v as $address) {
                    $query = "select urn,'address' matched_on from contacts left join records using(urn) inner join contact_addresses using(contact_id) where concat(add1,postcode) = '$address' and urn <> $urn";
                    if ($campaign) {
                        $query .= " and campaign_id = '$campaign'";
                    }
                    $query .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
                    $address_matches = $this->db->query($query)->result_array();
                    array_push($matches, $address_matches);
                }
            }
            if ($k == "company_numbers") {
                foreach ($v as $number) {
                    $query = "select urn,'company telephone' matched_on from records left join companies using(urn) inner join company_telephone using(company_id) where telephone_number = '$number' and urn <> $urn";
                    if ($campaign) {
                        $query .= " and campaign_id = '$campaign'";
                    }
                    $query .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
                    $q = $this->db->query($query);
                    if ($q->num_rows()) {
                        $company_matches = $q->result_array();
                        array_push($matches, $company_matches);
                    }
                }
                if ($k == "contact_numbers") {
                    foreach ($v as $number) {
                        $query = "select urn,'contact telephone' matched_on from records left join contacts using(urn) inner join contact_telephone using(contact_id) where telephone_number = '$number' and urn <> $urn";
                        if ($campaign) {
                            $query .= " and campaign_id = '$campaign'";
                        }
                        $query .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
                        $contact_matches = $this->db->query($query)->result_array();
                        array_push($matches, $contact_matches);
                    }
                }
            }
        }
        $urns = array();
        $matched_on = array();
        foreach ($matches as $k => $match) {
            if (!empty($match[0]['urn'])) {
                $urns[] = $match[0]['urn'];
                $matched_on[$match[0]['urn']] = $match[0]['matched_on'];
                //add to the related records table (testing)
                $insert_query = $this->db->insert_string("related_records", array("source" => $match[0]['urn'], "target" => $urn, "matched_on" => $match[0]['matched_on']));
                $insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query);
                $this->db->query($insert_query);
            }
        }
        //now return all the data from related/similar records found
        if (count($urns) > 0) {
            $urn_list = "," . implode(',', $urns);
        } else {
            $urn_list = "";
        }
        $query = "select campaign_name,urn,name,status_name from records left join companies using(urn) left join status_list on record_status_id = record_status left join campaigns using(campaign_id) where urn in('' $urn_list)";
        if ($campaign) {
            $query .= " and campaign_id = '$campaign'";
        }
        $query .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $data = $this->db->query($query)->result_array();
        foreach ($data as $k => $row) {
            $data[$k]['matched_on'] = $matched_on[$row['urn']];
        }
        return $data;
    }

    public function get_client_from_urn($urn)
    {
        $this->db->join("records", "campaigns.campaign_id=records.campaign_id", "left");
        $this->db->where("urn", $urn);
        return $this->db->get("campaigns")->row()->client_id;
    }

    public function get_campaign_from_urn($urn)
    {
        $this->db->select('campaign_id');
        $this->db->where('urn', $urn);
        $query = $this->db->get('records');
        if ($query->num_rows()) {
            return $query->row()->campaign_id;
        } else {
            return false;
        }

    }

	public function get_record_row($urn){
	$this->db->where("urn",$urn);
	return $this->db->get("records")->row_array();		
	}

    public function get_record()
    {

		//if a virgin order for the campaign is set then it's declare it here to insert into the virgin query below
		$custom_order = isset($_SESSION['custom_order'])?" order by " . $_SESSION['custom_order']:"";
		$custom_join = isset($_SESSION['custom_joins'])?$_SESSION['custom_joins']:"";
		//other variables
        $urn = 0;
        $data_filter = (isset($_SESSION['current_campaign'])?" and campaign_id = '".$_SESSION['current_campaign']."'":"");
		$data_filter .= (isset($_SESSION['current_source'])?" and source_id = '".$_SESSION['current_source']."'":"");
		$data_filter .= (isset($_SESSION['current_pot'])?" and pot_id = '".$_SESSION['current_pot']."'":"");
        $user_id = $_SESSION['user_id'];
        if (isset($_SESSION['current_campaign'])) {
            $priority = array();
            //1st priority where last outcome needs a callback within 10 mins belonging to the user
            $priority[] = "select urn,user_id from records left join ownership using(urn) where 1 $data_filter and record_status = 1 and parked_code is null and  progress_id is null and nextcall between now() - interval 10 MINUTE and now() + interval 10 MINUTE and (user_id = '$user_id') and outcome_id in(select outcome_id from outcomes where requires_callback = 1) order by case when outcome_id = 2 then 1 else 2 end, date_updated";
            //next priority is any all other DMS and emails belonging to the user
            $priority[] = "select urn,user_id from records left join ownership using(urn) where 1 $data_filter and record_status = 1 and parked_code is null and progress_id is null and nextcall<now() and outcome_id in(select outcome_id from outcomes where requires_callback = 1) and (user_id = '$user_id') order by case when outcome_id = 2 then 1 else 2 end,nextcall,dials";
            //next priority is lapsed callbacks	beloning to the user
            $priority[] = "select urn,user_id from records left join ownership using(urn) where 1 $data_filter and record_status = 1 and parked_code is null and progress_id is null and nextcall<now() and (outcome_id in(select outcome_id from outcomes where requires_callback = 1) or outcome_id=1) and (user_id = '$user_id') order by case when outcome_id = 2 then 1 else 2 end,nextcall,date_updated,dials";
            //next priority is lapsed callbacks	unassigned
            if (in_array("view unassigned", $_SESSION['permissions']) || in_array("search unassigned", $_SESSION['permissions'])) {
                $priority[] = "select urn,user_id from records left join ownership using(urn) where 1 $data_filter and record_status = 1 and parked_code is null and progress_id is null and nextcall<now() and (outcome_id in(select outcome_id from outcomes where requires_callback = 1) or outcome_id=1) and user_id is null order by case when outcome_id = 2 then 1 else 2 end,date_updated,dials";
            }
            //next priority is virgin and assigend to the user
			$priority[] = "select urn,user_id from records left join ownership using(urn) $custom_join where 1 $data_filter and record_status = 1 and parked_code is null and progress_id is null and (outcome_id is null) and (user_id = '$user_id')" . $custom_order ;
            if (in_array("view unassigned", $_SESSION['permissions']) || in_array("search unassigned", $_SESSION['permissions'])) {
                //next priority is virgin and unassigned
			$priority[] = "select urn,user_id from records left join ownership using(urn) $custom_join where 1 $data_filter and record_status = 1 and parked_code is null and progress_id is null and outcome_id is null and user_id is null ". $custom_order ;
            }
            //next priority is any other record with a nextcall date in order of lowest dials (current user)
            $priority[] = "select urn,user_id from records left join ownership using(urn) where 1 $data_filter and record_status = 1 and parked_code is null and progress_id is null and (nextcall<now() or nextcall is null) and (user_id = '$user_id') order by date_updated,dials";
            //next any other record with a nextcall date in order of lowest dials (any user)
            if (in_array("view unassigned", $_SESSION['permissions']) || in_array("search unassigned", $_SESSION['permissions'])) {
                $priority[] = "select urn,user_id from records left join ownership using(urn) where 1 $data_filter and record_status = 1 and parked_code is null and progress_id is null and (nextcall<now() or nextcall is null) and user_id is null order by date_updated,dials";
            }
            foreach ($priority as $k => $qry) {
                $query = $this->db->query($qry." limit 1");
				$_SESSION['last_query'] = $this->db->last_query();
                if ($query->num_rows() > 0) {
					//$this->firephp->log($this->db->last_query());
                    $urn = $query->row(0)->urn;
                    $owner = $query->row(0)->user_id;
                    break;
                }            }
            //if no user is allocated we should add the a user to prevent someone else landing on this record
            if (empty($owner) && in_array("keep records", $_SESSION['permissions'])) {
                $this->db->replace("ownership", array("user_id" => $user_id, "urn" => $urn));
            }
            return $urn;
        }
    }

    public function get_records_by_urn_list($urn_list)
    {
        $qry = "SELECT *
				from records
				WHERE urn IN " . $urn_list;

        return $this->db->query($qry)->result_array();
    }

    public function get_complete_records_by_urn_list($urn_list)
    {
        $qry = "SELECT *,
                  r.urn,
                  IF(com.name IS NOT NULL, com.name, IF(con.fullname IS NOT NULL, con.fullname, ''))             AS name,
                  data_sources.source_name,
                  IF(coma.add1 IS NOT NULL, coma.add1, IF(cona.add1 IS NOT NULL, cona.add1, ''))                 AS add1,
                  IF(coma.postcode IS NOT NULL, coma.postcode, IF(cona.postcode IS NOT NULL, cona.postcode, '')) AS postcode,
                  status_list.status_name,
                  r.date_added
				from records r
				  LEFT JOIN status_list ON r.record_status = status_list.record_status_id
				  LEFT JOIN data_sources ON data_sources.source_id = r.source_id
                  LEFT JOIN client_refs cref ON cref.urn = r.urn
                  LEFT JOIN contacts con ON con.urn = r.urn
                  LEFT JOIN contact_addresses cona ON con.contact_id = cona.contact_id
                  LEFT JOIN companies com ON com.urn = r.urn
                  LEFT JOIN company_addresses coma ON coma.company_id = com.company_id
                  LEFT JOIN company_telephone comt ON comt.company_id = com.company_id
                  LEFT JOIN contact_telephone cont ON cont.contact_id = con.contact_id
                WHERE r.urn IN (" . implode(",",$urn_list).")
                GROUP BY r.urn";

        return $this->db->query($qry)->result_array();
    }

    public function get_records_by_urn($urn)
    {
        $qry = "SELECT *
				from records
				WHERE urn = " . $urn;

        return $this->db->query($qry)->result_array();
    }

    public function get_record_details_by_urn($urn)
    {
        $qry = "SELECT *
				from record_details
				WHERE urn = " . $urn;

        return $this->db->query($qry)->result_array();
    }

    public function get_record_details_by_id($id)
    {
        $qry = "SELECT *
				from record_details
				WHERE detail_id = " . $id;

        $result = $this->db->query($qry)->result_array();

        return  (!empty($result)?$result[0]:array());


    }

    public function get_record_details_by_urn_list($urn_list)
    {
        $qry = "SELECT *
				from record_details
				WHERE urn IN " . $urn_list;

        return $this->db->query($qry)->result_array();
    }

    //sets the manager progress status as 1=pending so they know that something needs looking at on this record
    public function set_pending($urn)
    {
        $this->db->where("urn", $urn);
        $this->db->update("records", array(
            "progress_id" => "1"
        ));
    }

    //reset the record
    public function reset_record($urn)
    {
        $this->db->where("urn", $urn);
        return $this->db->update("records", array(
            "progress_id" => NULL,
            "outcome_id" => NULL,
            "record_status" => "1",
            "nextcall" => NULL
        ));
    }

    //unpark the record
    public function unpark_record($urn)
    {
        $this->db->where("urn", $urn);
        return $this->db->update("records", array(
            "parked_code" => NULL
        ));
    }

    //sets the record as "dead"
    public function set_dead($urn)
    {
        $this->db->where("urn", $urn);
        $this->db->update("records", array(
            "record_status" => "3"
        ));
    }

    //sets the record status as defined in the function
    public function set_status($urn, $status)
    {
        $this->db->where("urn", $urn);
        $this->db->update("records", array(
            "record_status" => $status
        ));
    }

    //sets the record status as defined in the function
    public function set_progress($urn, $progress)
    {
        $this->db->where("urn", $urn);
        $this->db->update("records", array(
            "progress_id" => $progress
        ));
    }

    //function to list all the records
    public function get_records($options,$urn=false)
    {
        $tables = $options['visible_columns']['tables'];
        $columns =  $options['visible_columns']['columns'];
        $table_columns = $options['visible_columns']['select'];
        $filter_columns = $options['visible_columns']['filter'];
        $order_columns = $options['visible_columns']['order'];
			$datafield_ids = array();
		foreach($table_columns as $k=>$col){
				$datafield_ids[$k] = 0;	
		if(strpos($col,"custom_")!==false){
			$split = explode("_",$col);
			$datafield_ids[$k] = intval($split[1]);
			$filter_columns[$k] = "t_".intval($split[1]).".value";
			$order_columns[$k] = "t_".intval($split[1]).".value";
			$table_columns[$k] = "t_".intval($split[1]).".value " .$columns[$k]['data'];
		}
		}

//these tables must be joined to the query regardless of the selected columns to allow the map to function
        $required_tables = array("record_planner", "record_planner_user", "ownership", "campaigns", "contact_locations", "company_locations");
        foreach ($required_tables as $rt) {
            if (!in_array($rt, $tables)) {
                $tables[] = $rt;
            }
        }

        $join = array();
        //add mandatory column selections here
        $required_select_columns = array("r.urn",
            "date_format(rp.start_date,'%d/%m/%Y') planner_date",
            "rp.user_id planner_user_id",
            "rp.record_planner_id",
            "rp.postcode as planner_postcode",
            "rpu.name planner_user",
            "r.urn marker_id",
            "GROUP_CONCAT(DISTINCT CONCAT(coma.postcode, '(',company_locations.lat,'/',company_locations.lng,')','|',company_locations.location_id) separator ',') as company_location",
            "GROUP_CONCAT(DISTINCT CONCAT(cona.postcode, '(',contact_locations.lat,'/',contact_locations.lng,')','|',contact_locations.location_id) separator ',') as contact_location",
            "r.record_color",
            "r.map_icon",
            "camp.map_icon as campaign_map_icon"
        );
        //if any of the mandatory columns are missing from the columns array we push them in
        foreach ($required_select_columns as $required) {
            if (!in_array($required, $table_columns)) {
                $table_columns[] = $required;
            }
        }

		

        //turn the selection array into a list
        $selections = implode(",", $table_columns);
		$qry = "";
        $select = "select $selections
                from records r ";
		$numrows = "select count(distinct r.urn) numrows
                from records r ";		
        //if any join is required we should apply it here
        if (isset($_SESSION['filter']['join'])) {
            $join = $_SESSION['filter']['join'];
        }

        //the joins for all the tables are stored in a helper
        $table_joins = table_joins();
        $join_array = join_array();

		$tablenum=0;
        foreach ($tables as $k=>$table) {
			if($table=="custom_panels"){ $tablenum++;
		
			$field_id = $datafield_ids[$k];
				$join[] = " left join (select max(id) id,urn from custom_panel_values join custom_panel_data using(data_id) where field_id = '$field_id' group by urn) mc_$field_id on mc_$field_id.urn =  r.urn left join  custom_panel_values t_$field_id on t_$field_id.id = mc_$field_id.id ";
			}
			
			if($table<>"custom_panels"){
            if (array_key_exists($table, $join_array)) {
                foreach ($join_array[$table] as $t) {
                    $join[$t] = $table_joins[$t];
                }
            } else {
                $join[$table] = $table_joins[$table];
            }
        }
		}

        foreach ($join as $join_query) {
            $qry .= $join_query;
        }
		
		
        $qry .= $this->get_where($options, $filter_columns);
	
		//get the total number of records before any limits or pages are applied
        $count = $this->db->query($numrows.$qry)->row()->numrows;
		
		$qry .= " group by r.urn";
		
        //if any order has been set then we should apply it here
        $start = $options['start'];
        $length = $options['length'];
        if (isset($_SESSION['filter']['order']) && $options['draw'] == "1") {
            $order = $_SESSION['filter']['order'];
        } else {
            $order = " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'] . ",urn";
            unset($_SESSION['filter']['order']);
            unset($_SESSION['filter']['values']['order']);
        }

        $qry .= $order;
		if($length>0){
        $qry .= "  limit $start,$length";
		}
		$this->firephp->log($select.$qry);
        $records = $this->db->query($select.$qry)->result_array();
        $records['count'] = $count;
        
        return $records;
    }

	public function get_all_comments($urns=array()){
		$comments = array();
		if(empty($urns)){
		return $comments;	
		}
		$qry = "SELECT lch.urn, lch.comments
FROM (

SELECT max( history_id ) mhid, urn
FROM history
WHERE comments <> ''";
$qry .= "and urn in(0,".implode($urns,",").")";
$qry .= "GROUP BY urn
)last_history
JOIN history lch ON last_history.mhid = lch.history_id join records r on r.urn = lch.urn where r.campaign_id in({$_SESSION['campaign_access']['list']})";
		
foreach($this->db->query($qry)->result_array() as $row){
$comments[$row['urn']] = $row['comments'];	
}

return $comments;
	}
	
    public function get_nav($options)
    {

        $tables = $options['visible_columns']['tables'];
        //these tables must be joined to the query regardless of the selected columns to allow the map to function
        $required_tables = array("appointments", "record_planner", "record_planner_user", "ownership", "campaigns", "contact_locations", "company_locations");
        foreach ($required_tables as $rt) {
            if (!in_array($rt, $tables)) {
                $tables[] = $rt;
            }
        }
        $table_columns = $options['visible_columns']['select'];
        $filter_columns = $options['visible_columns']['filter'];
        $order_columns = $options['visible_columns']['order'];

        $join = array();

        $qry = "select r.urn
                from records r ";
        //if any join is required we should apply it here
        if (isset($_SESSION['filter']['join'])) {
            $join = $_SESSION['filter']['join'];
        }

        //the joins for all the tables are stored in a helper
        $table_joins = table_joins();
        $join_array = join_array();

        foreach ($tables as $table) {
            if (array_key_exists($table, $join_array)) {
                foreach ($join_array[$table] as $t) {
                    $join[$t] = $table_joins[$t];
                }
            } else {
                $join[$table] = $table_joins[$table];
            }
        }
        foreach ($join as $join_query) {
            $qry .= $join_query;
        }

        $qry .= $this->get_where($options, $filter_columns);
        $qry .= " group by r.urn";
        //$this->firephp->log($qry);
        $count = $this->db->query($qry)->num_rows();

        //if any order has been set then we should apply it here
        $start = $options['start'];
        $length = $options['length'];
        if (isset($_SESSION['filter']['order']) && $options['draw'] == "1") {
            $order = $_SESSION['filter']['order'];
        } else {
            $order = " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'] . ",urn";
            unset($_SESSION['filter']['order']);
            unset($_SESSION['filter']['values']['order']);
        }

        $qry .= $order;
        $records = $this->db->query($qry)->result_array();
        $_SESSION['navigation'] = array();
        foreach ($records as $row) {
            $_SESSION['navigation'][] = $row['urn'];
			//$this->firephp->log($row['urn']);
        }
		
    }

    public function get_where($options, $table_columns)
    {
        //the default condition in ever search query to stop people viewing campaigns they arent supposed to!
        $where = " where 1 ";

        if (isset($_SESSION['current_campaign'])) {
            //this is already added to the session filter when the campaign is selected
			//$where .= " and r.campaign_id = '".$_SESSION['current_campaign'] ."'";
        }		
        $where .= " and r.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        //Check the bounds of the map
        if ($options['bounds'] && $options['map'] == 'true') {
            $where .= " and (
                    (company_locations.lat < " . $options['bounds']['neLat'] . " and company_locations.lat > " . $options['bounds']['swLat'] . " and company_locations.lng < " . $options['bounds']['neLng'] . " and company_locations.lng > " . $options['bounds']['swLng'] . ")
                      or
                    (contact_locations.lat < " . $options['bounds']['neLat'] . " and contact_locations.lat > " . $options['bounds']['swLat'] . " and contact_locations.lng < " . $options['bounds']['neLng'] . " and contact_locations.lng > " . $options['bounds']['swLng'] . ")
                  )";
        }

        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                if ($table_columns[$k] == "map_icon" && $v['search']['value'] == "Icon") {
                    //ignore this
                } else {
                    $where .= " and " . $table_columns[$k] . " like '%" . addslashes($v['search']['value']) . "%' ";
                }
            }
        }

        //if any filter has been set then we should apply it here
        if (isset($_SESSION['filter']['where']) && !empty($_SESSION['filter']['where'])) {
            $where .= $_SESSION['filter']['where'];
        }

        /* users can only see records that have not been parked */
        if (@!in_array("search parked", $_SESSION['permissions'])) {
            $where .= " and r.parked_code is null ";
        }

        //users can see unaassigned records
        if (in_array("search unassigned", $_SESSION['permissions']) || in_array("view unassigned", $_SESSION['permissions'])) {
            $unassigned = " or ow.user_id is null ";
        } else {
            $unassigned = "";
        }

        //users can only see their own records
        if (!in_array("search any owner", $_SESSION['permissions'])) {
            $where .= " and (ow.user_id = '{$_SESSION['user_id']}' $unassigned) ";
        }
        return $where;

    }

    public function count_records($options)
    {

        $qry = "select count(r.urn) as `count` from records r  ";
        //if any join is required we should apply it here
        if (isset($_SESSION['filter']['join'])) {
            $join = $_SESSION['filter']['join'];
            foreach ($join as $join_query) {
                $qry .= $join_query;
            }
        }
        //set the default criteria
        $qry .= " where campaign_id in({$_SESSION['campaign_access']['list']}) ";

        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $qry .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }

        //if any filter has been set then we should apply it here
        if (isset($_SESSION['filter']['where'])) {
            $qry .= $_SESSION['filter']['where'];
        }


        //$this->firephp->log($qry);
        return $this->db->query($qry)->row('count');
    }

    public function get_details($urn, $features)
    {
        $select = "select r.urn,r.source_id,source_name,r.pot_id,pot_name,c4, cr.client_ref,if(r.map_icon is null,camp.map_icon,r.map_icon) map_icon, r.record_color, c.contact_id,if(fullname = '','No Name',fullname) fullname,c.email,c.notes,linkedin,date_format(dob,'%d/%m/%Y') dob, c.notes,email_optout,c.website,c.position,ct.telephone_id, ct.description as tel_name,ct.telephone_number,ct.tps,a.address_id,custom_panel_name, custom_panel_format,a.add1,a.add2,a.add3,a.city,a.county,a.country,a.postcode,con_pc.lat latitidue,con_pc.lng longitude,a.`primary` is_primary,date_format(r.nextcall,'%d/%m/%Y %H:%i') nextcall,o.outcome,r.outcome_id,r.outcome_reason_id,r.record_status,r.progress_id,pd.description as progress,urgent,date_format(r.date_updated,'%d/%m/%Y %H:%i') date_updated,r.last_survey_id,r.campaign_id,camp.campaign_name,r.reset_date,park_reason,camp.telephone_protocol,camp.telephone_prefix ";
        $from = " from records r ";
        $from .= " left join client_refs cr using(urn) left join data_pots using(pot_id) left join record_details rd using(urn) ";
		$from .= " left join data_sources ds on r.source_id = ds.source_id  ";
        $from .= "  left join outcomes o using(outcome_id) left join progress_description pd using(progress_id) ";
        $from .= "  left join park_codes pc using(parked_code) ";
        $from .= "left join contacts c using(urn) left join contact_telephone ct using(contact_id) left join contact_addresses a using(contact_id) left join locations con_pc on a.location_id = con_pc.location_id left join campaigns camp using(campaign_id) ";

        if (in_array(4, $features)) {
            $select .= " ,sticky.note as sticky_note ";
            $from .= " left join sticky_notes sticky using(urn) ";
        }
        if (in_array(2, $features)) {
            $select .= ",coma.postcode planner_postcode,com.company_id,com.name coname, sector_name, subsector_name,com.description codescription, com.conumber,com.website cowebsite,com.employees,comt.telephone_id cotelephone_id, comt.description cotel_name,comt.telephone_number cotelephone_number,coma.`primary` cois_primary,ctps,coma.address_id coaddress_id,coma.add1 coadd1,coma.add2 coadd2,coma.add3 coadd3,coma.city cocity,coma.county cocounty,coma.country cocountry,coma.postcode copostcode,com_pc.lat colatitude,com_pc.lng colongitude";
            $from .= " left join companies com using(urn) left join company_addresses coma using(company_id) left join locations com_pc on com_pc.location_id = coma.location_id left join company_telephone comt using(company_id) left join company_subsectors using(company_id) left join subsectors using(subsector_id) left join sectors using(sector_id)";
        } else {
		  $select .= ",a.postcode planner_postcode";
		}
        if (in_array(6, $features)) {
            $select .= " ,sc.script_name,sc.script_id,sc.script,sc.expandable  ";
            $from .= "  left join scripts_to_campaigns using(campaign_id) left join scripts sc using(script_id) ";
        }
        $where = "  where r.campaign_id in({$_SESSION['campaign_access']['list']}) and urn = '$urn' ";
        $order = " order by c.sort,c.contact_id,ct.description ";
        $qry = $select . $from . $where . $order;
        $results = $this->db->query($qry)->result_array();

        $fav = "select urn from favorites where urn = '$urn' and user_id = '{$_SESSION['user_id']}'";
        $favorite = $this->db->query($fav)->num_rows();
        //put the contact details into array
        $data = array();
        if (count($results)) {
            foreach ($results as $result):
                $use_fullname = true;
                if ($result['contact_id']) {
                    $data['contacts'][$result['contact_id']]['name'] = array(
                        "fullname" => $result['fullname'],
                        "use_full" => $use_fullname
                    );
                    if (!isset($data['contacts'][$result['contact_id']]['visible'])) {
                        $data['contacts'][$result['contact_id']]['visible'] = array(
                            "Job" => $result['position'],
                            "DOB" => $result['dob'],
                            "Email address" => $result['email'],
                            "Linkedin" => $result['linkedin'],
                            "Email Optout" => $result['email_optout'],
                            "Website" => $result['website'],
                            "Notes" => $result['notes']
                        );
                    }

                    $data['contacts'][$result['contact_id']]['telephone'][$result['telephone_id']] = array(
                        "tel_name" => $result['tel_name'],
                        "tel_num" => $result['telephone_number'],
                        "tel_tps" => $result['tps']
                    );

                    //we only want to display the primary address for each contact
                    if ($result['is_primary'] == "1") {
                        $data['contacts'][$result['contact_id']]['visible']['Address']['add1'] = $result['add1'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['add2'] = $result['add2'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['add3'] = $result['add3'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['city'] = $result['city'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['county'] = $result['county'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['country'] = $result['country'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['postcode'] = $result['postcode'];
                        array_filter($data['contacts'][$result['contact_id']]['visible']['Address']);
                    }
                }

                if (in_array(2, $features)) {
                    if ($result['company_id']) {
                        $data['company'][$result['company_id']]["Company Name"] = $result['coname'];

                        if (!isset($data['company'][$result['company_id']]['visible'])) {
                            $data['company'][$result['company_id']]['visible'] = array(
                                "Sector" => $result['sector_name'],
                                "Subsector" => $result['subsector_name'],
                                "Description" => $result['codescription'],
                                "Website" => $result['cowebsite'],
                                "Employees" => $result['employees'],
                                "Company #" => $result['conumber']
                            );
                        }

                        $data['company'][$result['company_id']]['telephone'][$result['cotelephone_id']] = array(
                            "tel_name" => $result['cotel_name'],
                            "tel_num" => $result['cotelephone_number'],
                            "tel_tps" => $result['ctps']
                        );

                        //we only want to display the primary address for the company
                        if ($result['cois_primary'] == "1") {
                            $data['company'][$result['company_id']]['visible']['Address']['add1'] = $result['coadd1'];
                            $data['company'][$result['company_id']]['visible']['Address']['add2'] = $result['coadd2'];
                            $data['company'][$result['company_id']]['visible']['Address']['add3'] = $result['coadd3'];
                            $data['company'][$result['company_id']]['visible']['Address']['city'] = $result['cocity'];
                            $data['company'][$result['company_id']]['visible']['Address']['county'] = $result['cocounty'];
                            $data['company'][$result['company_id']]['visible']['Address']['country'] = $result['cocountry'];
                            $data['company'][$result['company_id']]['visible']['Address']['postcode'] = $result['copostcode'];
                            array_filter($data['company'][$result['company_id']]['visible']['Address']);
                        }

                    }
                }
                if (in_array(6, $features)) {
                    //put any scripts into the array
                    if ($result['script_id']) {
                        $data['scripts'][$result['script_id']] = array(
                            "script_id" => $result['script_id'],
                            "name" => $result['script_name'],
                            "script" => $result['script'],
                            "expandable" => $result['expandable']
                        );
                    }
                }
                //put the record details into the array
                $data['record'] = array(
                    "urn" => $result['urn'],
					"telephone_prefix" => $result['telephone_prefix'],
					"telephone_protocol" => $result['telephone_protocol'],
                    "client_ref" => $result['client_ref'],
                    "park_reason" => $result['park_reason'],
                    "nextcall" => $result['nextcall'],
                    "outcome" => $result['outcome'],
                    "outcome_id" => $result['outcome_id'],
                    "outcome_reason_id" => $result['outcome_reason_id'],
                    "record_status" => $result['record_status'],
                    "progress" => $result['progress'],
					"source_id" => $result['source_id'],
					"source_name" => $result['source_name'],
					"pot_id" => $result['pot_id'],
					"pot_name" => $result['pot_name'],
                    "progress_id" => $result['progress_id'],
                    "urgent" => $result['urgent'],
                    "last_update" => $result['date_updated'],
                    "last_survey_id" => $result['last_survey_id'],
                    "campaign_id" => $result['campaign_id'],
                    "campaign" => $result['campaign_name'],
                    "favorite" => $favorite,
                    "reset_date" => $result['reset_date'],
                    "custom_name" => $result['custom_panel_name'],
					"custom_format" => $result['custom_panel_format'],
                    "map_icon" => $result['map_icon'],
                    "color" => $result['record_color'],
                    "c4" => $result['c4'],
					"planner_postcode" => $result['planner_postcode']
                );
            endforeach;
        }
        if (in_array(4, $features)) {
            $data['record']["sticky_note"] = $result['sticky_note'];
        }
        //return the completed array
        return $data;
    }

    //if the record is unassigned set the current user as the owner. If it is assigned to somebody else then show an error
    public function take_ownership($urn)
    {
        $qry = "select * from ownership where urn = '$urn'";
        $result = $this->db->query($qry)->result_array();
        if (count($result) == 0) {
            $this->db->insert("ownership", array("urn" => $urn, "user_id" => $_SESSION['user_id']));
        } else {
            $is_owner = false;
            //check if the user is assigned to this record
            foreach ($result as $row) {
                if ($_SESSION['user_id'] == $row['user_id']) {
                    $is_owner = true;
                }
            }
            if (in_array('view own records',$_SESSION['permissions'])) {
                //redirect to error page is user is not the owner
                if (!$is_owner) {
					if(isset($_SESSION['navigation'])){
						//skip to the next record
					$k = array_search($urn,$_SESSION['navigation']);
					redirect(base_url() . "records/detail/".$_SESSION['navigation'][$k+1]);
					} else {
                    redirect(base_url() . "error/ownership");
					}
                }
            }
        }
    }

    public function get_history($urn, $limit, $offset)
    {
        $limit_ = ($limit) ? "limit " . $offset . "," . $limit : '';

        $qry = "select date_format(contact,'%d/%m/%y %H:%i') contact, u.name client_name,if(outcome_id is null or outcome_id = 0,if(pd.description is null,'No Action Required',pd.description),if(cc.campaign_name is not null,concat('Cross transfer to ',cc.campaign_name),outcome)) as outcome,if(outcome_reason is null,'',outcome_reason) outcome_reason , history.history_id, if(comments is null,'',comments) comments, keep_record,u.user_id,if(call_direction=1,'Inbound',if(call_direction=0,'Outbound','')) call_direction from history left join outcomes using(outcome_id) left join outcome_reasons using(outcome_reason_id) left join progress_description pd using(progress_id) left join users u using(user_id) left join cross_transfers on cross_transfers.history_id = history.history_id ";
        $qry .= " left join campaigns cc on cc.campaign_id = cross_transfers.campaign_id where urn = '$urn' order by history_id desc " . $limit_;
        return $this->db->query($qry)->result_array();
    }

    public function get_history_by_id($history_id)
    {
        $qry = "select date_format(history.contact,'%d/%m/%Y %H:%i') contact, history.history_id, history.outcome_id, history.progress_id, if(history.comments is null,'',history.comments) comments, outcomes.outcome, pd.description as progress, u.name, call_direction, outcome_reason_id, outcome_reason
                from history
                left join outcomes using(outcome_id)
				left join outcome_reasons using(outcome_reason_id)
                left join progress_description pd using(progress_id)
                left join users u using(user_id)";
        $qry .= " where history.history_id = '$history_id'";
        $result = $this->db->query($qry)->result_array();
        return $result[0];
    }

    public function save_history($post)
    {
        if (!empty($post['history_id'])) {
            $this->db->where("history_id", $post['history_id']);
            return $this->db->update("history", $post);
        } else {
            return $this->db->insert("history", $post);
        }
    }

    public function remove_history($id)
    {
        $this->db->where("history_id", $id);
        return $this->db->delete("history");
    }

    public function get_source($urn)
    {
        $qry = "select ds.* from data_sources ds inner join records on records.source_id = ds.source_id where urn = '$urn'";
        $result = $this->db->query($qry)->result_array();

        return (!empty($result) ? $result[0] : array());
    }
	
	  public function get_pot($urn)
    {
        $qry = "select dp.* from records dp left join data_pots on data_pots.pot_id = dp.pot_id where urn = '$urn'";
        $result = $this->db->query($qry)->result_array();

        return (!empty($result) ? $result[0] : array());
    }

    public function get_outcomes($campaign)
    {
        $qry = "select outcome_id,outcome,delay_hours,`disabled` from outcomes left join outcomes_to_campaigns using(outcome_id) where campaign_id = '$campaign' and enable_select = 1 union select outcome_id, outcome ,delay_hours,`disabled` from role_outcomes join outcomes using(outcome_id) where campaign_id in({$_SESSION['campaign_access']['list']}) and (campaign_id = '$campaign' or campaign_id is null) and role_id = '{$_SESSION['role']}'  order by outcome";
        return $this->db->query($qry)->result_array();
    }

    public function get_outcome_reasons($campaign)
    {
        $qry = "select outcome_id,outcome_reason,outcome_reason_id from outcome_reason_campaigns inner join outcome_reasons using(outcome_reason_id) where campaign_id = '$campaign' order by outcome_reason";
        return $this->db->query($qry)->result_array();
    }

    public function get_users($urn = "", $campaign_id = "")
    {
        if (empty($urn) && empty($campaign_id)):
            $qry = "select user_id,name,user_email,user_telephone from users where user_status = 1 and user_id in(select user_id from users_to_campaigns where campaign_id in({$_SESSION['campaign_access']['list']})) ";
        elseif (empty($urn) && !empty($campaign_id)):
            $qry = "select user_id,name,user_email,user_telephone from users where user_status = 1 and user_id in(select user_id from users_to_campaigns where campaign_id = '$campaign_id') ";
        else:
            $qry = "SELECT user_id, name, user_email, user_telephone, ownership.urn FROM ownership JOIN users USING (user_id) JOIN records USING ( urn )  where user_status = 1 and urn = '$urn' and campaign_id in({$_SESSION['campaign_access']['list']})";
        endif;
        return $this->db->query($qry)->result_array();
    }

    public function get_attendees($urn = false, $campaign_id = false, $postcode = false)
    {
        if ($urn):
            $qry = "select user_id,name,user_email,user_telephone,home_postcode from users join users_to_campaigns using(user_id) left join records using(campaign_id) where urn='$urn' and attendee=1 and user_status=1 and campaign_id in({$_SESSION['campaign_access']['list']})";
        elseif ($campaign_id):
            $qry = "select user_id,name,user_email,user_telephone,home_postcode from users left join users_to_campaigns using(user_id) where user_status = 1 and  attendee = 1 and  campaign_id = '$campaign_id'";
        else:
            $qry = "select user_id,name,user_email,user_telephone,home_postcode from users where user_status = 1 and attendee = 1 and user_id in(select user_id from users_to_campaigns where campaign_id in({$_SESSION['campaign_access']['list']})) ";
        endif;
		$qry .= " order by name";
        $attendees = $this->db->query($qry)->result_array();
		//TODO optimise this, its slow
		if($postcode){
		foreach($attendees as $k=>$row){
			$attendees[$k]['distance'] = "";
		if(!empty($row['home_postcode'])){
			$attendee_postcode = get_postcode_data($row['home_postcode']);
			
			if(isset($attendee_postcode['latitude'])){
			$contact_postcode = get_postcode_data($postcode);
			if(isset($contact_postcode['latitude'])){
			$distance_between = distance($attendee_postcode['latitude'], $attendee_postcode['longitude'], $contact_postcode['latitude'], $contact_postcode['longitude'], "N");
		$attendees[$k]['distance'] = number_format($distance_between,1);
			} else {
			$attendees[$k]['distance'] = "";	
			}
		}
		}
		}
		uasort($attendees,'arraySort_distance');
		}
		return $attendees;
    }

    public function get_addresses($urn = "")
    {
        $qry = "select 'contact' as `type`, fullname as name,address_id id,add1,add2,add3,add4,locality,city,county,country,postcode, contact_addresses.description from contact_addresses inner join contacts using(contact_id) where urn = '$urn'";
        $addresses = $this->db->query($qry)->result_array();
        $qry = "select 'company' as `type`,name,address_id id,add1,add2,add3,add4,locality,city,county,country,postcode, company_addresses.description from company_addresses inner join companies using(company_id) where urn = '$urn'";
        $companies = $this->db->query($qry)->result_array();
        foreach ($companies as $row) {
            $addresses[] = $row;
        }
//        $qry = "select 'appointment' as `type`,name,address_id id,add1,add2,add3,county,postcode from appointment_addresses where urn = '$urn'";
//        $appointments = $this->db->query($qry)->result_array();
//        foreach ($appointments as $row) {
//            $appointments[] = $row;
//        }
        return $addresses;
    }

    public function get_appointment_types($urn = false, $campaign = false)
    {
        if ($urn) {
            $qry = "select appointment_type_id id,appointment_type name, icon from appointment_types at join campaign_appointment_types using(appointment_type_id) records using(campaign_id) where urn = '$urn'";
        } else {
            $qry = "select appointment_type_id id,appointment_type name,icon from appointment_types join campaign_appointment_types using(appointment_type_id) where campaign_id = '$campaign'";
        }
		$query = $this->db->query($qry);
		if($query->num_rows()){
        return  $query->result_array();
		} else {
		//if no app types have been set for the campaign just use the defaults
		$qry = "select appointment_type_id id,appointment_type name, icon from appointment_types where is_default = 1 ";
		return  $this->db->query($qry)->result_array();
		}
    }

    public function save_ownership($urn, $owners = false)
    {
        //first remove the old owners for the urn
        $this->db->where("urn", $urn);
        $this->db->delete("ownership");
        //then add the new owners
        if ($owners) {
            foreach ($owners as $user) {
                $data = array(
                    "urn" => $urn,
                    "user_id" => $user
                );
                $this->db->replace("ownership", $data);
            }
        }
    }

    //returns an array of users that own a record (urn)
    public function get_ownership($urn)
    {
        $qry = "select user_id from ownership left join users using(user_id) where user_status = 1 and urn = '$urn'";
        return $this->db->query($qry)->result_array();
    }

    //returns an array of users that own a record (urn) by urn list
    public function get_ownership_by_urn_list($urn_list)
    {
        $qry = "select urn, user_id from ownership left join users using(user_id) where user_status = 1 and urn IN $urn_list";
        return $this->db->query($qry)->result_array();
    }

    public function add_ownership($urn, $user_id)
    {
        //insert ignore
        $qry = "INSERT IGNORE INTO ownership (urn,user_id) VALUES ('$urn','$user_id')";
        $this->db->query($qry);
        return $this->db->insert_id();
    }

    //updates a record
    public function update_record($post)
    {
		
        //if no nextcall is set then we just use the current timestamp else we convert the uk date to mysql
        if (empty($post['nextcall']) || !isset($post['nextcall'])) {
            $post['nextcall'] = date('Y-m-d H:i:s');
        } else {
            $post["nextcall"] = to_mysql_datetime($post["nextcall"]);
            //if the time set is less than now then we set it as now because nextcall dates should not be in the past
            if (strtotime($post["nextcall"]) < strtotime('now')) {
                $post["nextcall"] = date('Y-m-d H:i:s');
            }
        }
        //$this->firephp->log($post['nextcall']);
        $post['date_updated'] = date('Y-m-d H:i:s');
        $update_array = array(
            "nextcall",
            "date_updated"
        );
        //if the update is from an agent it will have an outcome_id
        if (isset($post['outcome_id'])) {
            if ($post["pending_manager"] == "1") {
                $post["progress_id"] = "1";
                $update_array[] = "progress_id";
            } else if ($post["pending_manager"] == "2") {
                $post["progress_id"] = "1";
                $post["urgent"] = "1";
                $update_array[] = "urgent";
                $update_array[] = "progress_id";
            }
            if (isset($post['outcome_reason_id'])) {
                $update_array[] = "outcome_reason_id";
            }
            //only change the outcome and increase dial count if they are not just adding notes (outcome_id = 67)
            if ($post['outcome_id'] <> "67" && $post['outcome_id'] <> "68"&&!empty($post['outcome_id'])) {
                $update_array[] = "outcome_id";
                $qry = "update records set dials = dials+1 where urn = '" . intval($post['urn']) . "'";
                $this->db->query($qry);
            }

        } else {
            //if the update is from a manager we update the progress_id instead of the outcome_id. Making sure an empty value is NULL
            $this->db->where("urn", $post['urn']);
            $this->db->update("records", array(
                "progress_id" => NULL
            ));
            
        }
		if (isset($post["progress_id"])&&intval($post["progress_id"]) > 0) {
                $update_array[] = "progress_id";
            }
		
        $this->db->where("urn", $post['urn']);
        $this->db->update("records", elements($update_array, $post));
    }

    //add entry to the history table
    public function add_history($hist)
    {
        //if no nextcall is set then we just use the current timestamp else we convert the uk date to mysql
        if (empty($hist['nextcall']) || !isset($hist['nextcall'])) {
            $hist["nextcall"] = date('Y-m-d H:i:s');
        } else {
            $hist["nextcall"] = to_mysql_datetime($hist['nextcall']);
            //if the time set is less than now then we set it as now because nextcall dates should not be in the past
            if (strtotime($hist["nextcall"]) < strtotime('now')) {
                $hist["nextcall"] = date('Y-m-d H:i:s');
            }
        }
		if (!isset($hist['pot_id']) || empty($hist['pot_id'])) {
            $record = $this->get_record_row($hist['urn']);
            $hist['pot_id'] = $record['pot_id'];
			$hist['source_id'] = $record['source_id'];
        }
		if(empty($hist['outcome_id'])){
		unset($hist['outcome_id']);
		}
		
        $hist["contact"] = date('Y-m-d H:i:s');
        $hist["user_id"] = $_SESSION['user_id'];
        $hist["role_id"] = $_SESSION['role'];
        $hist["group_id"] = $_SESSION['group'];
        $hist["team_id"] = (!empty($_SESSION['team']) ? $_SESSION['team'] : NULL);
		$hist['loaded'] = $_SESSION['record_loaded'];
        $this->db->insert("history", elements(array(
            "urn",
            "campaign_id",
			"loaded",
            "nextcall",
            "contact",
            "description",
            "outcome_id",
            "outcome_reason_id",
            "comments",
            "nextcall",
            "user_id",
            "role_id",
            "team_id",
            "group_id",
            "contact_id",
            "progress_id",
            "last_survey",
            "call_direction",
            "source_id",
			"pot_id"
        ), $hist, NULL));
       	$history_id = $this->db->insert_id();
		//add last comment
		if(!empty($hist['comments'])){
		$this->db->replace("record_comments",array("urn"=>$hist['urn'],"last_comment"=>$hist["comments"]));
		}
		return $history_id;
    }


    //get the campaign of a given urn
    public function get_campaign($urn = "")
    {
        if (intval($urn)) {
			$query = "select records.campaign_id,campaign_name,campaign_type_id,if(user_layouts.layout is not null,user_layouts.layout,record_layout) record_layout,logo,campaign_name,client_name from records left join campaigns using(campaign_id) left join clients using(client_id) left join (select * from user_layouts where user_id = '".$_SESSION['user_id']."') user_layouts on user_layouts.campaign_id = records.campaign_id where urn = '$urn'";
            $rows = $this->db->query($query)->result_array();
            return $rows[0];
        }
    }

    //get the last comment for a given urn
    public function get_last_comment($urn)
    {
        $urn = intval($urn);
        $comment = "";
        $qry = "select comments from history where urn = '$urn' and comments <> '' and comments is not null order by history_id desc limit 1";
		if($this->db->query($qry)->num_rows()){
        $comment = $this->db->query($qry)->row()->comments;
		}
        return $comment;
    }

    //find all dates that a record has been contacted on
    public function get_calls($urn)
    {
        $qry = "select contact from history where urn = '$urn' and `group_id` = 1";
        $query = $this->db->query($qry);
        return $query->result_array();
    }

    //find all dates that a record has been contacted on
    public function get_xfers($camp)
    {
        $qry = "select xfer_campaign id,campaign_name name from campaign_xfers left join campaigns on campaigns.campaign_id = xfer_campaign where campaign_xfers.campaign_id = '$camp'";
        return $this->db->query($qry)->result_array();
    }

    public function get_record_details($urn)
    {
        $qry = "select * from record_details where urn = '$urn'";
        $query = $this->db->query($qry);
        return $query->result_array();
    }

    public function get_additional_info($urn = false, $campaign, $id = false)
    {
        $fields_qry = "select `field`,`field_name`,`is_select`,is_buttons,is_decimal,is_radio,is_renewal,format,editable,is_owner,is_pot,is_source from record_details_fields where campaign_id = '$campaign' and is_visible = 1 order by sort";
        $fields_result = $this->db->query($fields_qry)->result_array();
        $fields = "";
        foreach ($fields_result as $row) {
            $options = array();
            $stuff1[$row['field_name']] = $row['field'];
            $renewal[$row['field_name']] = $row['format'];
            $editable[$row['field_name']] = $row['editable'];
            $is_select[$row['field_name']] = $row['is_select'];
			$is_buttons[$row['field_name']] = $row['is_buttons'];
            $is_radio[$row['field_name']] = $row['is_radio'];
			$is_decimal[$row['field_name']] = $row['is_decimal'];
            if ($row['is_select'] == 1 || $row['is_radio'] == 1|| $row['is_buttons'] == 1) {
                if ($row['is_owner'] == "1") {
                    $is_select[$row['field_name']] = 1;
                    $users = $this->get_users(false, $campaign);
                    foreach ($users as $user) {
                        $options[] = array("id" => $user['user_id'], "option" => $user['name']);
                    }
				} else if($row['is_pot'] == "1"){
					//update source from record
					$update = "update record_details join records using(urn) left join data_pots using(pot_id) set `".$row['field']."` = pot_name";
					$this->db->query($update);
					$pots = $this->get_pots(false, $campaign);
                    foreach ($pots as $pot) {
                        $options[] = array("id" => $pot['pot_id'], "option" => $pot['pot_name']);
                    }
				} else if($row['is_source'] == "1"){
					//update source from record
					$update = "update record_details join records using(urn) left join data_sources using(source_id) set `".$row['field']."` = source_name";
					$this->db->query($update);
					$sources = $this->get_sources(false, $campaign);
                    foreach ($sources as $source) {
                        $options[] = array("id" => $source['source_id'], "option" => $source['source_name']);
                    }
                } else {
                    $this->db->select("id,option");
                    $this->db->where(array(
                        "field" => $row['field'],
                        "campaign_id" => $campaign
                    ));
                    $this->db->order_by("option");
                    $option_result = $this->db->get("record_details_options")->result_array();
                    foreach ($option_result as $opt) {
                        $options[] = array("id" => $opt['option'], "option" => $opt['option']);
                    }
                }
				$this->firephp->log($options);
                $stuff2[$row['field_name']] = $options;
            }

            $sqlfield = $row['field'];
            $fields .= "$sqlfield" . " as `" . $row['field_name'] . "`,";
        }

        $select = $fields . "detail_id ";
        $qry = "select $select from record_details where urn='$urn'";
        if ($id) {
            $qry = "select $select from record_details where detail_id='$id'";
        }
        $result = $this->db->query($qry)->result_array();
        if (count($result) == 0) {
            $this->db->insert("record_details", array("urn" => $urn));
            $id = $this->db->insert_id();
            $qry = "select $select from record_details where detail_id='$id'";
            $result = $this->db->query($qry)->result_array();
        }

        $info = array();
        foreach ($result as $id => $detail) {
            foreach ($detail as $k => $v) {
                if ($k <> "detail_id") {
                    $info[$id][$k]["id"] = $detail['detail_id'];
                    $info[$id][$k]["code"] = $stuff1[$k];
                    $info[$id][$k]["name"] = $k;
                    $info[$id][$k]["editable"] = $editable[$k];
                    $info[$id][$k]["is_radio"] = $is_radio[$k];
                    $info[$id][$k]["is_select"] = $is_select[$k];
					$info[$id][$k]["is_buttons"] = $is_buttons[$k];
					 $info[$id][$k]["is_decimal"] = $is_decimal[$k];
                    if (isset($renewal[$k])) {
                        $info[$id][$k]["formatted"] = (!empty($v) ? date($renewal[$k], strtotime($v)) : "-");
                    }
                    if (isset($stuff2[$k])) {
                        $info[$id][$k]["options"] = $stuff2[$k];
                    }
                    $info[$id][$k]["value"] = (!empty($v) ? $v : "-");
                    if (strpos($stuff1[$k], "c") !== false) {
                        $info[$id][$k]["type"] = "varchar";
                    } else if (substr($stuff1[$k], 1, 1) == "t") {
                        $info[$id][$k]["type"] = "datetime";
                        $info[$id][$k]["value"] = (!empty($v) ? date("d/m/Y H:i", strtotime($v)) : "-");
                    } else if (strpos($stuff1[$k], "n") !== false) {
                        $info[$id][$k]["type"] = "number";
						if(!empty($v)){
						if($info[$id][$k]["is_decimal"]=="1"){
						$info[$id][$k]["value"] = number_format($v,2);
						} else {
						$info[$id][$k]["value"] = number_format($v);
						}
						}
                    } else {
                        $info[$id][$k]["type"] = "date";
                        $info[$id][$k]["value"] = (!empty($v) ? date("d/m/Y", strtotime($v)) : "-");
                    }
                }

            }

        }

        return $info;
    }

    public function get_name_from_user_id($id)
    {
        $this->db->where("user_id", $id);
        return $this->db->get('users')->row()->name;
    }

    public function save_additional_info($post)
    {
        foreach ($post as $k => $v) {
            if (substr($k, 0, 1) == "d" && $k <> "detail_id") {
                $post[$k] = to_mysql_datetime($post[$k]);
            }
        }
        if (!empty($post['detail_id'])) {
            $this->db->where("detail_id", $post['detail_id']);
            $this->db->update("record_details", $post);
			return $post['detail_id'];
        } else {
           $this->db->insert("record_details", $post);
		    return $this->db->insert_id();
        }
    }

    public function remove_custom_item($id)
    {
        $this->db->where("detail_id", $id);
        $this->db->delete("record_details");
    }

    public function delete_appointment($data)
    {
		//remove from custom_panels
		$update_link = "update custom_panel_data set appointment_id = NULL where appointment_id = '{$data['appointment_id']}'";
		$this->db->query($update_link);
		$delete_link = "delete from custom_panel_values where `value` = '{$data['appointment_id']}' and field_id in(select field_id from custom_panel_fields where is_appointment_id = 1)";
		$this->db->query($delete_link);
		//now do the appointments table
        $this->db->where("appointment_id", $data['appointment_id']);

        $this->db->set("status", '0');
        $this->db->set("cancellation_reason", $data['cancellation_reason']);
        $this->db->set("updated_by", $_SESSION['user_id']);
        $this->db->set("date_updated", date('Y-m-d H:i:s'));

        $this->db->update("appointments");
    }

    //get appointmnet data for a given urn
    public function get_appointments($urn, $id = false)
    {
        $this->db->select("appointments.appointment_id,title,if(length(text)>60,concat(substr(text,1,60),'...'),text) text,start,end,urn,postcode,appointment_attendees.user_id,users.name,cancellation_reason", false);
        $this->db->join("appointment_attendees", "appointment_attendees.appointment_id=appointments.appointment_id", "LEFT");
		$this->db->join("users", "appointment_attendees.user_id=users.user_id", "LEFT");
        $this->db->where(array(
            "urn" => $urn
        ));
        if ($id) {
            $this->db->where("appointments.appointment_id", $id);
        }
        $this->db->group_by("appointment_id");
        $result = $this->db->get("appointments")->result_array();
        return $result;
    }

    //get appointmnet data for a given id
    public function get_appointment($appointment_id)
    {
        $this->db->select("appointments.appointment_id,title,if(length(text)>60,concat(substr(text,1,60),'...'),text) text,start,end,urn,postcode,appointment_attendees.user_id,cancellation_reason", false);
        $this->db->join("appointment_attendees", "appointment_attendees.appointment_id=appointments.appointment_id", "LEFT");
        $this->db->where("appointments.appointment_id", $appointment_id);
        $result = $this->db->get("appointments")->result_array();
        return $result;
    }
	
	public function get_branch_from_attendee($attendee){
	$qry = "select branch_id from branch_region_users join branch using(region_id) where user_id = '$attendee'";
	$branch =  $this->db->query($qry)->row_array();
	if(isset($branch['branch_id'])){
	return $branch['branch_id'];	
	} else {
	return NULL;	
	}
	}
	
    public function save_appointment($post)
    {
		if(isset($post['data_id'])){
		$data_id = $post['data_id'];
		unset($post['data_id']);
		}
		$attendees = $post['attendees'];
        unset($post['attendees']);
		if(empty($post['branch_id'])){
		 $post['branch_id'] = NULL;
		}
        if ($post['contact_id'] == 'other') {
            $post['contact_id'] = NULL;
        }

        if (!empty($post['appointment_id'])) {
            $this->db->where("appointment_id", $post['appointment_id']);
            $this->db->delete("appointment_attendees");
            foreach ($attendees as $attendee) {
                $this->db->insert("appointment_attendees", array(
                    "appointment_id" => $post['appointment_id'],
                    "user_id" => $attendee
                ));
            }


            $this->db->where(
                "appointment_id", $post['appointment_id']
            );
            $this->db->where(
                "urn", $post['urn']
            );
            $post['location_id'] = NULL;
            $post['date_updated'] = date('Y-m-d H:i:s');
            $post['updated_by'] = $_SESSION['user_id'];
            $this->db->update("appointments", $post);
            return $post['appointment_id'];
        } else {
            $post['created_by'] = $_SESSION['user_id'];
            $this->db->insert("appointments", $post);
            $insert = $this->db->insert_id();
            foreach ($attendees as $attendee) {
                $this->db->insert("appointment_attendees", array(
                    "appointment_id" => $insert,
                    "user_id" => $attendee
                ));
            }
            return $insert;
        }
    }
	
	public function link_appointment_to_custom_data($data_id,$appointment_id){
				$this->db->where("data_id",$data_id);
				$this->db->update("custom_panel_data",array("appointment_id"=>$appointment_id));
				//get the linked appointment field
				
				//now replace it
				$app_field = "select field_id from custom_panel_fields where is_appointment_id = 1";
				if($this->db->query($app_field)->num_rows()){
				$field_id = $this->db->query($app_field)->row()->field_id;
				$update = "replace into custom_panel_values values('',$data_id,$field_id,$appointment_id)";
				$this->db->query($update);	
				}
	}

    //when a record is update this function is ran to see if an email should be sent to anyone
    public function get_email_triggers($campaign_id, $outcome_id)
    {
        $cc = array();
        $bcc = array();
        $main = array();
        $this->db->join("email_trigger_recipients", "email_triggers.trigger_id = email_trigger_recipients.trigger_id", "inner");
        $this->db->join("users", "users.user_id = email_trigger_recipients.user_id", "inner");
        $this->db->where("outcome_id", $outcome_id);
        $this->db->where("campaign_id", $campaign_id);
        $result = $this->db->get("email_triggers")->result_array();
        $email_triggers = array();
        foreach ($result as $row) {
            if (!empty($row['user_email']) && !empty($row['template_id'])) {
                if ($row['type'] == "cc") {
                    $cc[$row['name']] = $row['user_email'];
                } else if ($row['type'] == "bcc") {
                    $bcc[$row['name']] = $row['user_email'];
                } else {
                    $main[$row['name']] = $row['user_email'];
                }
                $email_triggers[$row['template_id']] = array("cc" => $cc, "email" => $main, "bcc" => $bcc);
            }
        }
        $_SESSION['email_triggers'] = $email_triggers;
        return $email_triggers;
    }

    //when a record is update this function is ran to see if an email should be sent to anyone
    public function get_sms_triggers($campaign_id, $outcome_id)
    {
        $main = array();
        $this->db->join("sms_trigger_recipients", "sms_triggers.trigger_id = sms_trigger_recipients.trigger_id", "inner");
        $this->db->join("users", "users.user_id = sms_trigger_recipients.user_id", "inner");
        $this->db->where("outcome_id", $outcome_id);
        $this->db->where("campaign_id", $campaign_id);
        $result = $this->db->get("sms_triggers")->result_array();
        $sms_triggers = array();
        foreach ($result as $row) {
            if (!empty($row['user_telephone']) && !empty($row['template_id']) && preg_match('/^447|^\+447^00447|^07/', $row['user_telephone'])) {
                $main[$row['name']] = $row['user_email'];
                $sms_triggers[$row['template_id']] = array("mobile" => $main);
            }
        }
        $_SESSION['sms_triggers'] = $sms_triggers;
        return $sms_triggers;
    }

    //when a record is update this function is ran to see if the urn should be sent to any other function
    public function get_function_triggers($campaign_id, $outcome_id)
    {
        $this->db->where(array("outcome_id" => $outcome_id, "campaign_id" => $campaign_id));
        $function_triggers = $this->db->get("function_triggers")->result_array();
        $functions = array();
        foreach ($function_triggers as $row) {
            $functions[] = $row['path'];
        }
        return $functions;
    }


    /*get all the new owners for a specific outcome*/
    public function get_owners_for_outcome($campaign_id, $outcome_id)
    {
        $owners = array();
        $this->db->where(array(
            "outcome_id" => $outcome_id,
            "campaign_id" => $campaign_id
        ));
        $this->db->join("ownership_trigger_users", "ownership_triggers.trigger_id = ownership_trigger_users.trigger_id", "LEFT");
        $result = $this->db->get("ownership_triggers")->result_array();
        foreach ($result as $row) {
            $owners[] = $row['user_id'];
        }
        return $owners;
    }


    public function get_campaign_managers($campaign = "")
    {
        $managers = array();
        $this->db->where("campaign_id", $campaign);
        $result = $this->db->get("campaign_managers")->result_array();
        foreach ($result as $row) {
            $managers[] = $row['user_id'];
        }
        return $managers;
    }

    public function add_xfer($id, $campaign)
    {
        $this->db->insert('cross_transfers', array('history_id' => $id, 'campaign_id' => $campaign));
    }

    public function get_positive_for_footer($campaign)
    {
        $qry = "select outcome,outcome_id from outcomes_to_campaigns left join outcomes using(outcome_id) where campaign_id = '$campaign' and `positive` = 1 group by outcome_id";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }

    public function get_status_by_name($name)
    {
        $status = $this->db->get_where('status_list', array('status_name' => $name))->result();

        return $status[0];
    }

    public function get_source_by_name($name)
    {
        $source = $this->db->get_where('data_sources', array('source_name' => $name))->result();

        return $source[0];
    }

    public function save_record($form)
    {
        $this->db->insert("records", $form);

        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $insert_id;
    }


    public function save_attachment($form)
    {
        $this->db->insert("attachments", $form);

        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $insert_id;
    }

    /**
     * Get attachments
     */
    public function get_attachments($urn, $limit, $offset,$webform=false)
    {
        $limit_ = ($limit) ? "limit " . $offset . "," . $limit : '';

        $qry = "select a.attachment_id, a.name, a.type, a.path, DATE_FORMAT(a.date,'%d/%m/%Y %H:%i:%s') as date, u.name as user
		    	from attachments a
		    	inner join users u ON (u.user_id = a.user_id)
		    	where urn = " . $urn;
				if($webform){
				$qry .= " and webform = $webform "; 	
				}
		    	$qry .= " order by a.date desc
		    	" . $limit_;
		//$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }

    /**
     * Get attachments
     */
    public function get_attachment_by_id($id)
    {

        $this->db->select("a.*");
        $this->db->from("attachments a");
        $this->db->where('attachment_id', $id);

        $result = $this->db->get()->result_array();
		if(count($result)){
        return $result[0];
		} else {
		return false;	
		}
    }

    public function delete_attachment($id)
    {
        $this->db->where("attachment_id", $id);
        $this->db->delete("attachments");
    }

    public function get_webforms($urn)
    {
        $qry = "select webform_answers.id,date_format(webform_answers.updated_on,'%d/%m/%Y %H:%i') updated_on, id,records.campaign_id,webforms.webform_id,webform_name,records.urn,users.name,u.name updated_by_name,date_format(completed_on,'%d/%m/%Y %H:%i') completed_on,completed_by,appointment_id from records left join campaigns using(campaign_id) left join webforms_to_campaigns using(campaign_id) join webforms using(webform_id) left join webform_answers on records.urn = webform_answers.urn and webforms.webform_id = webform_answers.webform_id left join users on user_id = completed_by left join users u on webform_answers.updated_by = u.user_id where records.urn = '$urn' and (appointment_type_id is null or updated_on is not null)";
        return $this->db->query($qry)->result_array();
    }

    public function updated_recently($urn)
    {
        //added status and parked code because they should be able to skip records that they cannot update
        $qry = "select urn from records where urn = '$urn' and (date_updated > subdate(now(),interval 10 minute) or record_status <> '1' or parked_code is not null)";
        if ($this->db->query($qry)->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Save a record planner
     */
    public function save_record_planner($record_planner)
    {

        $record_planner_id = $record_planner['record_planner_id'];
        unset($record_planner['record_planner_id']);

        if ($record_planner_id) {
            //Update the record planner
            $this->db->where('record_planner_id', $record_planner_id);
            return $this->db->update('record_planner', $record_planner);
        } else {
            //Insert a new record planner
            return $this->db->insert('record_planner', $record_planner);
        }
    }


    /**
     * Set record icon map
     */
    public function set_icon($record)
    {

        $urn = $record['urn'];
        unset($record['urn']);

        //Update the icon
        $this->db->where('urn', $urn);
        return $this->db->update('records', $record);
    }

    /**
     * Get used icons
     */
    public function get_used_icons()
    {
        $qry = "SELECT DISTINCT
                  r.map_icon AS record_map_icon,
                  camp.map_icon AS campaign_map_icon
                FROM records r
                  JOIN campaigns camp USING (campaign_id)
                WHERE campaign_status = 1 and r.map_icon IS NOT NULL OR camp.map_icon IS NOT NULL  and campaign_id in({$_SESSION['campaign_access']['list']}) ";

        return $this->db->query($qry)->result_array();
    }

    public function insert_client_ref($urn, $client_ref)
    {
        $this->db->insert_update("client_refs", array("urn" => $urn, "client_ref" => $client_ref));
    }
  public function get_custom_panels($campaign){
		$query = "select * from custom_panels join campaign_custom_panels using(custom_panel_id) where campaign_id in({$_SESSION['campaign_access']['list']}) and campaign_id = ' $campaign' ";
		return $this->db->query($query)->result_array();
  }
  
   public function get_custom_panel($panel_id){
		$query = "select * from custom_panels join campaign_custom_panels using(custom_panel_id) where campaign_id in({$_SESSION['campaign_access']['list']}) and custom_panel_id = '$panel_id' ";
		return $this->db->query($query)->row_array();
  }
  
    public function get_custom_panel_fields($id){
		$panel_fields_query = "select custom_panel_fields.field_id,custom_panel_fields.name,modal_column,format,type,read_only,hidden,option_id,custom_panel_options.name option_name,subtext as option_subtext,tooltip,format from custom_panel_fields left join custom_panel_options using(field_id) where custom_panel_id = '$id' order by sort";
	return $this->db->query($panel_fields_query)->result_array();
  }
     public function get_custom_panel_data($urn,$id){
		$panel_data_query = "select id,data_id,field_id,`value`,custom_panel_fields.name,modal_column,date_format(created_on, '%D %M %Y') created_on from custom_panels left join custom_panel_fields using(custom_panel_id) left join custom_panel_values using(field_id) left join custom_panel_data using(data_id) where urn = '$urn' and custom_panel_id = '$id' order by created_on desc";
	return $this->db->query($panel_data_query)->result_array();
  }
  public function create_custom_data_with_linked_appointments($appointment_id){
	  $check = "select data_id from custom_panel_fields join custom_panel_values where is_appointment_id = 1 and `value` = '$appointment_id'";
	  if($this->db->query($check)->num_rows()){
		 return $this->db->query($check)->row()->data_id;
	  }
	$query = "select custom_panel_id,appointment_type_id,linked_appointment_type_ids from custom_panels join campaign_custom_panels using(custom_panel_id) join records using(campaign_id) join appointments using(urn) where appointment_id = '$appointment_id' and linked_appointment_type_ids is not null";
	 foreach($this->db->query($query)->result_array() as $row){
		 $appointment_type_ids = explode(",",$row['linked_appointment_type_ids']);
		 if(in_array($row['appointment_type_id'], $appointment_type_ids)){
		$this->db->query("insert into custom_panel_data values('',(select urn from appointments where appointment_id = '$appointment_id'),$appointment_id,now(),".$_SESSION['user_id'].",now(),1)");
			
		$data_id = $this->db->insert_id();
		$this->db->query("insert into custom_panel_values (select '',$data_id,field_id,if(is_appointment_id=1,'$appointment_id','') from custom_panel_fields where custom_panel_id = '{$row['custom_panel_id']}' and is_appointment_id = 1)");
		return $data_id;
		 }
	 }
  }
  
  public function get_pot_from_id($id){
	  return $this->db->get_where("data_pots",array("pot_id"=>$id))->row()->pot_name;
  }
    public function get_source_from_id($id){
	   return $this->db->get_where("data_sources",array("source_id"=>$id))->row()->source_name;
  }
  
  public function get_pots($urn,$campaign){
	  return $this->db->query("select pot_id,pot_name from data_pots group by pot_id")->result_array();
  }
    public function get_sources($urn,$campaign){
	  return $this->db->query("select data_sources.source_id,source_name from data_sources join records using(source_id) where campaign_id = '$campaign' group by source_id")->result_array();
  }
   public function save_pot($urn,$id){
	   if(empty($id)){
		$id = NULL;   
	   }
	   $this->db->where(array("urn"=>$urn));
	   return $this->db->update("records",array("pot_id"=>$id));
  }
  
   public function save_source($urn,$id){
	   if(empty($id)){
		$id = NULL;   
	   }
	   $this->db->where(array("urn"=>$urn));
	   return $this->db->update("records",array("source_id"=>$id));
  }
}

?>