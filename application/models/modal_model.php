<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Modal_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();

    }

	public function get_modal_fields($id, $modal_type)
	{
		if ($modal_type == "1") {
			$campaign = $this->db->query("select campaign_id from records where urn = '$id'")->row()->campaign_id;
		}
		if ($modal_type == "2") {
			$campaign = $this->db->query("select campaign_id from appointments join records using(urn) where appointment_id = '$id'")->row()->campaign_id;
		}

		$get_modal_query = "select id from modal_config where modal_id = '$modal_type' and ((user_id is null or user_id = '" . $_SESSION['user_id'] . "') and (campaign_id is null or campaign_id = '$campaign')) order by user_id desc, campaign_id desc limit 1";
		$modal_config_id = $this->db->query($get_modal_query)->row()->id;

		$query = "select * from modals join modal_config using(modal_id) join modal_columns on modal_config.id = modal_columns.modal_config_id join modal_datafields using(column_id) join datafields using(datafield_id) left join record_details_fields on datafield_title = field where modal_id = $modal_type and modal_config_id = '$modal_config_id' and (datafields.campaign is null or datafields.campaign = '$campaign') order by column_sort,modal_datafields.sort";

		if ($this->db->query($query)->num_rows()) {
			$fields = $this->db->query($query)->result_array();
		} else {
			return false;
		}

		$visible_fields = array();
		foreach ($fields as $field) {
			$visible_fields['modal'][] = $field;
			$visible_fields['fields'][] = array("data" => !empty($field['datafield_alias']) ? $field['datafield_alias'] : $field['datafield_select']);
			$visible_fields['headings'][] = empty($field['field_name']) ? $field['datafield_title'] : $field['field_name'];
			$visible_fields['select'][] = "if(" . $field['datafield_select'] . " is null,'-'," . $field['datafield_select'] . ")" . " `" . $field['datafield_title'] . "`";
			$visible_fields['tables'][] = $field['datafield_table'];
		}

        foreach ($visible_fields['headings'] as $k => $heading) {
			if (in_array($heading, $this->custom_fields)) {
				$current_campaign = (isset($_SESSION['current_campaign']) ? $_SESSION['current_campaign'] : '');
				$this->db->where(array("urn" => $id, "field" => $heading));
				$this->db->join("records", "records.campaign_id=record_details_fields.campaign_id", 'LEFT');
				$field = $this->db->get('record_details_fields')->row_array();
                if (count($field)) {
					$visible_fields['headings'][$k] = @$field['field_name'];
				}
			}
		}
		return $visible_fields;
	}
	
	public function get_extra_field_names($urn){
		$extra_fields = $this->db->query("select field,field_name from record_details_fields join records using(campagn_id) where urn = '$urn'")->result_array();
		$field_names = array();
		foreach($extra_fields as $field => $name){
		$field_names[$field] = $name;	
		}	
		return $field_names;
	}

    public function get_record($options, $urn)
    {

        $tables = $options['tables'];
        $table_columns = $options['select'];

        $datafield_ids = array();

        foreach ($table_columns as $k => $col) {
            $title = $options['headings'][$k];
            $datafield_ids[$k] = 0;
            if (strpos($col, "custom_") !== false) {
                $split = explode("_", $col);
                $datafield_ids[$k] = intval($split[1]);
                $table_columns[$k] = "t_" . intval($split[1]) . ".value '$title'";
            }
        }

        $required_select_columns = array("r.urn", "r.campaign_id");
        foreach ($required_select_columns as $required) {
            if (!in_array($required, $table_columns)) {
                $table_columns[] = $required;
            }
        }

        $join = array();
        $selections = implode(",", $table_columns);
        $qry = "select $selections
                from records r ";

        //the joins for all the tables are stored in a helper
        $table_joins = table_joins();
        $join_array = join_array();
        $tablenum = 0;

        foreach ($tables as $k => $table) {
            if ($table == "custom_panels") {
                $tablenum++;
                $field_id = $datafield_ids[$k];
                $join[] = " left join (select max(id) id,urn from custom_panel_values join custom_panel_data using(data_id) where field_id = '$field_id' group by urn) mc_$field_id on mc_$field_id.urn =  r.urn left join  custom_panel_values t_$field_id on t_$field_id.id = mc_$field_id.id ";
            }

            if ($table <> "custom_panels") {
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
        $qry .= " where r.urn = '$urn' ";

        return $this->db->query($qry)->row_array();
    }

    public function get_appointment($options, $id)
    {
        $tables = $options['tables'];
        $required_tables = array("appointments", "campaigns", "companies", "company_addresses", "contacts", "contact_addresses", "contact_locations", "company_locations", "appointment_locations", "ownership", "record_planner", "record_planner_user", "ownership", "outcomes", "appointment_attendees", "appointment_users", "appointment_types");
        foreach ($required_tables as $rt) {
            if (!in_array($rt, $tables)) {
                $tables[] = $rt;
            }
        }
        $table_columns = $options['select'];
        $datafield_ids = array();

        foreach ($table_columns as $k => $col) {
            $title = $options['headings'][$k];
            $datafield_ids[$k] = 0;
            if (strpos($col, "custom_") !== false) {
                $split = explode("_", $col);
                $datafield_ids[$k] = intval($split[1]);
                $table_columns[$k] = "t_" . intval($split[1]) . ".value '$title'";
            }
        }

        $required_select_columns = array("a.appointment_id", "r.campaign_id","appointment_type");
        foreach ($required_select_columns as $required) {
            if (!in_array($required, $table_columns)) {
                $table_columns[] = $required;
            }
        }
        $join = array();
        $selections = implode(",", $table_columns);
        $qry = "select $selections
                from records r ";

        //the joins for all the tables are stored in a helper
        $table_joins = table_joins();
        $join_array = join_array();
        $tablenum = 0;
        $tableappnum = 0;
        foreach ($tables as $k => $table) {
            if ($table == "custom_panels") {
                $tablenum++;

                $field_id = $datafield_ids[$k];
                $join[] = " left join (select max(id) id,urn from custom_panel_values join custom_panel_data using(data_id) where field_id = '$field_id' group by urn) mc_$field_id on mc_$field_id.urn =  r.urn left join  custom_panel_values t_$field_id on t_$field_id.id = mc_$field_id.id ";
            }
            if ($table == "custom_panels_appointments") {
                $tableappnum++;
                $field_id = $datafield_ids[$k];
                $join[] = " left join (select id,appointment_id from custom_panel_values join custom_panel_data using(data_id) where field_id = '$field_id') mc_$field_id on mc_$field_id.appointment_id =  a.appointment_id left join custom_panel_values t_$field_id on t_$field_id.id = mc_$field_id.id ";
            }
            if ($table <> "custom_panels") {
                if (array_key_exists($table, $join_array)) {
                    foreach ($join_array[$table] as $t) {
                        $join[$t] = @$table_joins[$t];
                    }
                } else if(isset($table_joins[$table])){
                    $join[$table] = @$table_joins[$table];
                }
            }
        }

        foreach ($join as $join_query) {
            $qry .= $join_query;
        }
        $qry .= " where a.appointment_id = '$id' group by a.appointment_id";
		//$this->firephp->log($qry);
        return $this->db->query($qry)->row_array();
    }
	
	
	public function get_record_options($urn){
	$this->db->select("record_color,campaigns.map_icon campaign_icon,records.map_icon,park_reason, source_id,pot_id,records.campaign_id,records.parked_code",false);
	$this->db->join("campaigns","records.campaign_id=campaigns.campaign_id");
	$this->db->join("park_codes","park_codes.parked_code=records.parked_code","LEFT");
	$this->db->where("urn",$urn);
	return $this->db->get("records")->row_array();	
	}
	
	public function table_fields(){
	$fields = array("campaign_id"=>array("name"=>"Campaign name","remove"=>$_SESSION['data_access']['mix_campaigns']?true:false),
			"source_id"=>array("name"=>"Data source"),
			"client_id"=>array("name"=>"Client name"),
			"campaign_type_id"=>array("name"=>"Campaign type"),
			"urn"=>array("name"=>"URN"),
			"client_ref"=>array("name"=>"Client reference"),
			"outcome_id"=>array("name"=>"Outcome"),
			"progress_id"=>array("name"=>"Progress"),
			"record_status"=>array("name"=>"Record status",$_SESSION['data_access']['dead']?true:false),
			"parked_code"=>array("name"=>"Parked code",!$_SESSION['data_access']['parked']?true:false),
			"group_id"=>array("name"=>"Group ownership",!$_SESSION['data_access']['group_records']?true:false),
			"user_id"=>array("name"=>"User ownership","remove"=>!$_SESSION['data_access']['user_records']?true:false),
			"nextcall"=>array("name"=>"Nextcall date"),
			"date_updated"=>array("name"=>"Lastcall date"),
			"date_added"=>array("name"=>"Created date"),
			"fullname"=>array("name"=>"Contact name"),
			"phone"=>array("name"=>"Contact phone"),
			"position"=>array("name"=>"Contact position"),
			"dob"=>array("name"=>"Contact DOB"),
			"contact_email"=>array("name"=>"Contact email"),
			"address"=>array("name"=>"Contact address"),
			"company_id"=>array("name"=>"Company ID"),
			"coname"=>array("name"=>"Company Name"),
			"company_phone"=>array("name"=>"Company phone"),
			"sector_id"=>array("name"=>"Sector"),
			"subsector_id"=>array("name"=>"Subsector"),
			"turnover"=>array("name"=>"Turnover"),
			"employees"=>array("name"=>"Employees"),
			"postcode"=>array("name"=>"Postcode"),
			"distance"=>array("name"=>"Distance"),
			"new_only"=>array("name"=>"New records only"),
			"dials"=>array("name"=>"Number of dials"),
			"survey"=>array("name"=>"With survey only"),
			"favorites"=>array("name"=>"Favorites only"),
			"urgent"=>array("name"=>"Urgent only"),
			"email"=>array("name"=>"Email filter"),
			"no_company_tel"=>array("name"=>"Companies without numbers"),
			"no_phone_tel"=>array("name"=>"Contacts without numbers"),
			);	
		
	}
	
    public function view_record($urn)
    {
        $qry = "select planner_id,p.postcode planner_postcode,if(p.start_date is not null,date_format(p.start_date,'%d/%m/%Y'),'') planner_date,r.urn,r.nextcall,u.name owner,status_name,campaign_name,r.campaign_id,if(outcome is null,'New',outcome) outcome,if(comments is null,'n/a',if(length(comments)>70,concat(SUBSTR(comments,1,70),'...'),comments)) comments ,if(com.name is not null,com.name,con.fullname) name,fullname,if(client_ref is null,'-',client_ref) client_ref, if(r.date_updated is null,'n/a',date_format(r.date_updated,'%D %M %y')) lastcall, if(r.nextcall is null,'n/a',date_format(r.nextcall,'%D %M %y')) nextcall,if(park_reason is null,'n/a',park_reason) parked,custom_panel_name from records r left join client_refs using(urn) left join ownership using(urn) left join users u using(user_id) left join status_list sl on sl.record_status_id = r.record_status left join campaigns using(campaign_id) left join contacts con using(urn) left join (select record_planner_id planner_id,start_date,postcode,urn from record_planner where user_id = '" . $_SESSION['user_id'] . "' and planner_status = 1) p using(urn) left join companies com using(urn)  left join park_codes using(parked_code) left join outcomes  using(outcome_id) left join (select max(history_id) mhid,urn from history where comments <> '' group by urn) mhis using(urn) left join history h on h.history_id = mhis.mhid where r.urn = '$urn'";
        return $this->db->query($qry)->result_array();
    }

    public function view_history($urn)
    {
        $qry = "select name,if(outcome_id is null,pd.description,outcome) outcome, date_format(contact,'%D %M %Y %H:%i') contact, if(comments is null,'n/a',if(length(comments)>50,concat(SUBSTR(comments,1,60),'...'),comments)) comments from history left join progress_description pd using(progress_id) left join users using(user_id) left join outcomes using(outcome_id) where urn = '$urn' order by contact desc limit 5";
        //$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }

    public function view_appointments($urn)
    {
        $qry = "select appointment_id, title, `text`, date_format(start,'%D %M %Y') `date`,start sqlstart, date_format(start,'%l:%i%p') `time`,address, access_address,if(u.name is null,'system',u.name) name,`status`,cancellation_reason from appointments a left join users u on u.user_id = a.created_by where urn = '$urn' order by start desc limit 5";
        return $this->db->query($qry)->result_array();
    }

    public function view_appointment_meta($id, $postcode = false)
    {
        $distance_query = "";
        if ($postcode) {
            $coords = postcode_to_coords($postcode);
            $distance_query = ",(((ACOS(SIN((" .
                $coords['lat'] . "*PI()/180)) * SIN((lo.lat*PI()/180))+COS((" .
                $coords['lat'] . "*PI()/180)) * COS((lo.lat*PI()/180)) * COS(((" .
                $coords['lng'] . "- lo.lng)*PI()/180))))*180/PI())*60*1.1515) distance";
        }
        $query = "select u.user_id,if(c.name is null,'n/a',c.name) coname,campaign_name,campaigns.campaign_id,appointment_id,a.appointment_confirmed,a.urn,title,text,date_format(start,'%d/%m/%Y %H:%i') start,date_format(start,'%W %D %M %Y %l:%i%p') starttext,date_format(end,'%d/%m/%Y %H:%i') end,postcode,a.status,(select name from users where user_id = a.created_by) created_by,date_format(a.date_added,'%d/%m/%Y %l:%i%p') date_added, u.name attendee,u.user_id attendee_id, appointment_type,  cancellation_reason, appointment_type_id as `type`, address,access_address, access_postcode, contact_id  $distance_query, a.branch_id, webform_answers.id answers_id, webforms.webform_id,btn_text,sticky_notes.note from appointments a left join records using(urn)left join sticky_notes using(urn) left join campaigns using(campaign_id) left join appointment_types using(appointment_type_id) left join locations lo using(location_id) left join appointment_attendees aa using(appointment_id) left join users u on u.user_id = aa.user_id left join companies c using(urn) left join webforms using(appointment_type_id) left join webform_answers using(appointment_id) where appointment_id = '$id' ";
        return $this->db->query($query)->result_array();
    }

    public function get_filter_values($name, $values) {
        switch ($name) {
            case 'campaign_id':
                $qry = "select campaign_id as id, campaign_name as value from campaigns where campaign_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
				break;
            case 'source_id':
                $qry = "select source_id as id, source_name as value from data_sources where source_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'client_id':
                $qry = "select client_id as id, client_name as value from clients where client_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'campaign_type_id':
                $qry = "select campaign_type_id as id, campaign_type_desc as value from campaign_types where campaign_type_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'outcome_id':
                $qry = "select outcome_id as id, outcome as value from outcomes where outcome_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'progress_id':
                $qry = "select progress_id as id, description as value from progress_description where progress_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'record_status':
                $qry = "select record_status_id as id, status_name as value from status_list where record_status_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'parked_code':
                $qry = "select parked_code as id, park_reason as value from park_codes where parked_code IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'group_id':
                $qry = "select group_id as id, group_name as value from user_groups where group_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'user_id':
                $qry = "select user_id as id, name as value from users where user_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'contact_id':
                $qry = "select contact_id as id, fullname as value from contacts where contact_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'company_id':
                $qry = "select company_id as id, name as value from companies where company_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'sector_id':
                $qry = "select sector_id as id, sector_name as value from sectors where sector_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            case 'subsector_id':
                $qry = "select subsector_id as id, subsector_name as value from subsectors where subsector_id IN (".implode(",",$values).")";
                $results = $this->db->query($qry)->result_array();
                break;
            default:
                return $values;
        }

        $aux = array();
        foreach($results as $result) {
            $aux[$result['id']] = $result['value'];
        }

        return $aux;
    }

}