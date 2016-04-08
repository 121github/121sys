<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    public function update_campaign_status($post){
	  $id = intval($post['campaign']);
	  $status = intval($post['status']);
	  $this->db->where("campaign_id",$id);
	  $this->db->update("campaigns",array("campaign_status"=>$status,"start_date"=>$start_date,"end_date"=>NULL));
	  //set the end date as the last time it was dialled
	  if($status==0){
	  $this->db->query("update campaigns join (select max(contact) mc,campaign_id from history group by campaign_id) h using(campaign_id) set end_date = h.mc  where campaign_status = 0 and campaign_id = $id");
	  }	  
	}
    public function save_day_slots($data)
    {
        $user_id = $data['user_id'];
        unset($data['user_id']);
        $this->db->where("user_id", $user_id);
        $this->db->delete("appointment_slot_assignment");
        $insert = array();
		$default = array();
        foreach ($data as $date => $slots) {
            $day = date('N', strtotime($date));
            
            foreach ($slots as $slot_id => $max_apps) {
				 $default[$slot_id] = array(
                    "user_id" => $user_id,
                    "max_slots" => 0,
                    "day" => NULL,
                    "appointment_slot_id" => $slot_id
                );
				
                $insert[] = array(
                    "user_id" => $user_id,
                    "max_slots" => $max_apps,
                    "day" => $day,
                    "appointment_slot_id" => $slot_id
                );
            }
        }
		$this->db->insert_batch("appointment_slot_assignment", $default);
        $this->db->insert_batch("appointment_slot_assignment", $insert);
        
    }
    
    public function save_date_slots($data)
    {
		$notes = $data['notes'];
        $date_from = to_mysql_datetime($data['date_from']);
        $date_to   = to_mysql_datetime($data['date_to']);
        $range     = date_range($date_from, $date_to, '+1 day', 'Y-m-d');
        $insert    = array();
        foreach ($range as $date) {
			foreach($data['slot_id'] as $slot_id){
		 $this->db->where(array("user_id"=>$data['user_id'],"date"=>$date,"appointment_slot_id"=>$slot_id));
         $this->db->delete("appointment_slot_override");
				
            $insert[] = array(
                "user_id" => $data['user_id'],
                "max_slots" => $data['max_apps'],
                "date" => $date,
                "appointment_slot_id" => $slot_id,
				"notes"=>$notes
            );
			}
        }
        $this->db->insert_batch("appointment_slot_override", $insert);    
    }
      public function add_slot_group($name){
		$this->db->insert("appointment_slot_groups",array("slot_group_name"=>$name));
		return $this->db->insert_id();  
	  }
    public function get_all_slots()
    {	
		$this->db->join("appointment_slot_groups","appointment_slot_groups.slot_group_id=appointment_slots.slot_group_id","LEFT");
		$this->db->order_by("appointment_slots.slot_group_id");
        return $this->db->get("appointment_slots")->result_array();
    }
    
    public function get_slot($id)
    {
        $this->db->where("appointment_slot_id", $id);
        return $this->db->get("appointment_slots")->row_array();
    }
    public function get_user_slots($id)
    {
        $this->db->where("user_id", $id);
        return $this->db->get("appointment_slot_assignment")->row_array();
    }
    
    public function save_slot($data)
    {	unset($data['new_group']);
        $this->db->where("appointment_slot_id", $data['appointment_slot_id']);
        return $this->db->insert_update("appointment_slots", $data);
    }
    public function get_day_slots($id)
    {
        $this->db->where("slot_group_id", $id);
        $this->db->group_by("appointment_slot_id");
        return $this->db->get("appointment_slots")->result_array();
    }
    
    public function delete_date_slots($id)
    {
        $this->db->where("slot_override_id", $id);
        return $this->db->delete("appointment_slot_override");
    }
    
    public function get_date_slots($id)
    {
        $qry = "select *,date_format(`date`,'%d/%m/%Y') `date` from appointment_slot_override join appointment_slots using(appointment_slot_id) where user_id = '$id' and `date` >= curdate() order by date desc";
        return $this->db->query($qry)->result_array();
    }
    
    public function get_all_slot_groups()
    {
        return $this->db->get("appointment_slot_groups")->result_array();
    }
    public function get_user_day_slots($id)
    {
        $qry = "select * from appointment_slot_assignment where user_id = '$id' order by day";
        return $this->db->query($qry)->result_array();
    }
    public function get_user_slot_group($user_id)
    {
        $qry = "select slot_group_id,slot_group_name from appointment_slots aps left join appointment_slot_groups using(slot_group_id) left join appointment_slot_assignment asa using(appointment_slot_id) left join appointment_slot_override aso on aso.appointment_slot_id = aps.appointment_slot_id where (asa.appointment_slot_id is not null and asa.user_id = '$user_id' or aso.appointment_slot_id is not null and aso.user_id = '$user_id') group by aps.slot_group_id";
        return $this->db->query($qry)->result_array();
    }
    
    public function get_slots_in_group($group_id)
    {
        $qry = "select appointment_slot_id,slot_name,slot_start,slot_end,slot_description from appointment_slots left join appointment_slot_assignment asa using(appointment_slot_id) where slot_group_id = '$group_id' group by appointment_slot_id";
        return $this->db->query($qry)->result_array();
    }
    
    public function campaigns_with_attendees()
    {
        $campaigns = array();
        $qry       = "select * from campaigns join users_to_campaigns using(campaign_id) join users using(user_id) left join campaign_groups using(campaign_group_id) where attendee = 1 ";
		$qry .= " and campaign_id in({$_SESSION['campaign_access']['list']}) "; 
		$qry .= "group by campaign_id order by campaign_group_name,campaign_name";
        $result    = $this->db->query($qry)->result_array();
        foreach ($result as $row) {
            $campaigns[$row['campaign_group_name']][] = $row;
        }
        return $campaigns;
    }
    
    public function delete_slot($id)
    {
        $this->db->where("appointment_slot_id", $id);
        return $this->db->delete("appointment_slots");
    }
    
    
    
    public function delete_campaign_group($id)
    {
        $this->db->where("campaign_group_id", $id);
        $this->db->delete("campaign_groups");
    }
    
    public function get_campaign_group($id)
    {
        $this->db->where("campaign_group_id", $id);
        return $this->db->get("campaign_groups")->row_array();
    }
    
    public function set_campaign_group_ids($id, $campaigns)
    {
        foreach ($campaigns as $campaign_id) {
            $this->db->where("campaign_id", $campaign_id);
            $this->db->update("campaigns", array(
                "campaign_group_id" => $id
            ));
        }
    }
    
    public function save_campaign_group($form)
    {
        if (empty($form['campaign_group_id'])) {
            $this->db->insert("campaign_groups", $form);
            $id = $this->db->insert_id();
        } else {
            $id = $form['campaign_group_id'];
            $this->db->where("campaign_group_id", $id);
            $this->db->update("campaign_groups", $form);
        }
        return $id;
    }
    
    public function campaigns_in_campaign_group($id)
    {
        $campaigns = array();
        $this->db->where("campaign_group_id", $id);
        foreach ($this->db->get("campaigns")->result_array() as $row) {
            $campaigns[] = $row['campaign_id'];
        }
        return $campaigns;
    }
    
    public function get_campaign_groups()
    {
        $this->db->select("campaign_groups.campaign_group_id id,campaign_group_name name, if(`count` is null,'0',`count`) `count`", false);
        $this->db->join("(select campaign_group_id,count(*) count from campaigns group by campaign_group_id) b", "b.campaign_group_id=campaign_groups.campaign_group_id", "LEFT", FALSE);
        $this->db->group_by("campaign_groups.campaign_group_id");
        $data = $this->db->get("campaign_groups")->result_array();
        return $data;
    }
    
    public function clone_campaign($new_name, $id, $tables = array())
    {
        //check it doesnt exist
        $this->db->where("campaign_name", $new_name);
        if ($this->db->get("campaigns")->num_rows()) {
            return false;
        }
        $qry = "insert into campaigns (campaign_id,campaign_group_id,campaign_name,record_layout,logo,campaign_type_id,client_id,campaign_status,email_recipients,reassign_to,custom_panel_name,custom_panel_format,min_quote_days,daily_data,map_icon,virgin_order_1,virgin_order_2,virgin_order_string,virgin_order_join,telephone_protocol,telephone_prefix,timeout) select '',campaign_group_id,'$new_name',record_layout,logo,campaign_type_id,client_id,campaign_status,email_recipients,reassign_to,custom_panel_name,min_quote_days,daily_data,map_icon,virgin_order_1,virgin_order_2,virgin_order_string,virgin_order_join,telephone_protocol,telephone_prefix,timeout from campaigns where campaign_id = '$id'";
        $this->db->query($qry);
        $new_id = $this->db->insert_id();
        if (in_array("features", $tables)) {
            //add campaign features
            $qry = "insert into campaigns_to_features select $new_id,feature_id from campaigns_to_features where campaign_id = $id";
            $this->db->query($qry);
        }
        if (in_array("extra_fields", $tables)) {
            //add campaign features
            $qry = "insert into record_details_fields select '',$new_id,field,field_name,is_select,is_buttons,is_decimal,is_radio,sort,is_visible,is_renewal,`format`,editable,is_color,is_owner,is_client_ref,is_pot,is_source from record_details_fields where campaign_id = $id";
            $this->db->query($qry);
            $qry2 = "insert into record_details_options select '',$new_id,field,`option` from record_details_options where campaign_id = $id";
            $this->db->query($qry2);
        }
        
        if (in_array("managers", $tables)) {
            //add campaign managers 
            $qry = "insert into campaign_managers select $new_id,user_id from campaign_managers where campaign_id = $id";
            $this->db->query($qry);
        }
        if (in_array("appointment_slots", $tables)) {
            //add campaign appointment_slots
            $qry = "insert into appointment_slot_assignment select '',appointment_slot_id,$new_id,user_id,max_slots,day,source_id from appointment_slot_assignment where campaign_id = $id";
            $this->db->query($qry);
        }
        if (in_array("appointment_types", $tables)) {
            //add campaign appointment_types
            $qry = "insert into campaign_appointment_types select $new_id,appointment_type_id from campaign_appointment_types where campaign_id = $id";
            $this->db->query($qry);
        }
        
        if (in_array("permissions", $tables)) {
            //add campaign permissions
            $qry = "insert into campaign_permissions select $new_id,permission_id,permission_state from campaign_permissions where campaign_id = $id";
            $this->db->query($qry);
        }
        
        if (in_array("tasks", $tables)) {
            //add campaign permissions
            $qry = "insert into campaign_tasks select $new_id,task_id from campaign_tasks where campaign_id = $id";
            $this->db->query($qry);
        }
        
        if (in_array("triggers", $tables)) {
            //add campaign triggers
            $qry = "insert into campaign_triggers select '',$new_id,path from campaign_triggers where campaign_id = $id";
            $this->db->query($qry);
        }
        
        if (in_array("outcomes", $tables)) {
            //add campaign outcomes
            $qry = "insert into outcomes_to_campaigns select outcome_id,$new_id from outcomes_to_campaigns where campaign_id = $id";
            $this->db->query($qry);
            
            //add campaign outcomes
            $qry = "insert into outcome_reason_campaigns select $new_id,outcome_id,outcome_reason_id from outcome_reason_campaigns where campaign_id = $id";
            $this->db->query($qry);
        }
        if (in_array("scripts", $tables)) {
            //add campaign outcome scripts
            $qry = "insert into scripts_to_campaigns select script_id,$new_id from scripts_to_campaigns where campaign_id = $id";
            $this->db->query($qry);
        }
        if (in_array("sms", $tables)) {
            //add campaign sms templates
            $qry = "insert into sms_template_to_campaigns select template_id,$new_id from sms_template_to_campaigns where campaign_id = $id";
            $this->db->query($qry);
        }
        if (in_array("sms_triggers", $tables)) {
            //add campaign triggers
            $qry = "insert into sms_triggers select trigger_id,$new_id,outcome_id,template_id from sms_triggers where campaign_id = $id";
            $this->db->query($qry);
        }
        
        if (in_array("email", $tables)) {
            //add campaign triggers
            $qry = "insert into email_template_to_campaign select template_id,$new_id from email_template_to_campaign where campaign_id = $id";
            $this->db->query($qry);
        }
        
        if (in_array("email_triggers", $tables)) {
            //add campaign triggers
            $qry = "insert into email_triggers select '',$new_id,outcome_id,template_id from email_triggers where campaign_id = $id";
            $this->db->query($qry);
        }
        
        if (in_array("surveys", $tables)) {
            //add campaign surveys
            $qry = "insert into surveys_to_campaigns select '',survey_info_id,$new_id,`default` from surveys_to_campaigns where campaign_id = $id";
            $this->db->query($qry);
        }
        if (in_array("webforms", $tables)) {
            //add campaign webforms
            $qry = "insert into webforms_to_campaigns select webform_id,$new_id from webforms_to_campaigns where campaign_id = $id";
            $this->db->query($qry);
        }
        if (in_array("ownership_triggers", $tables)) {
            //add campaign ownership
            $qry = "insert into ownership_triggers select trigger_id,$new_id,outcome_id from ownership_triggers where campaign_id = $id";
            $this->db->query($qry);
        }
        
        return $new_id;
    }
    
    /* functions for the admin campaigns page */
    public function get_campaign_details($campaign = "")
    {
        $qry = "select campaign_id,campaign_name,campaign_type_desc,record_layout,client_name,IF(campaign_status=1,'Live','Dead') campaign_status_text,campaign_type_id,custom_panel_name,custom_panel_format,campaign_status, client_id, IF(start_date is null,'-',date_format(start_date,'%d/%m/%Y')) start_date,IF(end_date is null,'-',date_format(end_date,'%d/%m/%Y')) end_date, min_quote_days, max_quote_days, bc.months_ago, bc.months_num, map_icon, max_dials, virgin_order_1,virgin_order_2,virgin_order_string,virgin_order_join,telephone_protocol,telephone_prefix  from campaigns left join campaign_types using(campaign_type_id) left join clients using(client_id) left join backup_by_campaign bc using (campaign_id) where 1";
        if (!empty($urn)) {
            $qry .= " and camapign_id = '$campaign'";
        }
        $qry .= " order by campaign_name";
        return $this->db->query($qry)->result_array();
    }
    public function add_new_campaign($form)
    {
        $this->db->insert("campaigns", $form);
        return $this->db->insert_id();
    }
    public function update_campaign($form)
    {
        $this->db->where("campaign_id", $form['campaign_id']);
        return $this->db->update("campaigns", $form);
    }
    public function delete_campaign($id)
    {
        $this->db->where("campaign_id", $id);
        return $this->db->delete("campaigns");
    }
    public function add_campaign_access($camp, $user)
    {
        return $this->db->replace("users_to_campaigns", array(
            "campaign_id" => $camp,
            "user_id" => $user
        ));
    }
    public function add_campaign_outcome($camp, $outcome)
    {
        return $this->db->replace("outcomes_to_campaigns", array(
            "campaign_id" => $camp,
            "outcome_id" => $outcome
        ));
    }
    public function revoke_campaign_access($camp, $user)
    {
        $this->db->where(array(
            "campaign_id" => $camp,
            "user_id" => $user
        ));
        return $this->db->delete("users_to_campaigns");
    }
    public function remove_campaign_outcome($camp, $outcome)
    {
        $this->db->where(array(
            "campaign_id" => $camp,
            "outcome_id" => $outcome
        ));
        return $this->db->delete("outcomes_to_campaigns");
    }
    public function add_client($client_name)
    {
        $var = $this->db->escape($client_name);
        $qry = "insert ignore into clients set client_name = $var";
        $this->db->query($qry);
        return $this->db->insert_id();
    }
    public function find_client($client_name)
    {
        $qry   = "select client_id from clients where client_name = " . $this->db->escape($client_name);
        $query = $this->db->query($qry);
        if ($query->num_rows() > 0) {
            return $query->row()->client_id;
        } else {
            return false;
        }
    }
    public function save_campaign_features($form)
    {
        //first delete the old campaign features
        $this->db->where("campaign_id", $form['campaign_id']);
        $this->db->delete("campaigns_to_features");
        //then add the new ones
        if (isset($form['features'])) {
            foreach ($form['features'] as $feature) {
                $insert_string = $this->db->insert_string("campaigns_to_features", array(
                    "campaign_id" => $form['campaign_id'],
                    "feature_id" => $feature
                ));
                $insert_query  = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_string);
                $this->db->query($insert_query);
            }
        }
    }
    
    public function get_backup_by_campaign($campaign_id)
    {
        $qry = "select *
                from backup_by_campaign
                where campaign_id = " . $campaign_id;
        return $this->db->query($qry)->result_array();
    }
    
    public function update_backup_by_campaign($form)
    {
        $this->db->where("campaign_id", $form['campaign_id']);
        return $this->db->update("backup_by_campaign", $form);
    }
    
    public function insert_backup_by_campaign($form)
    {
        return $this->db->insert("backup_by_campaign", $form);
    }
    
    /* functions for the admin logs page */
    public function get_logs()
    {
        $qry = "select username,name,date_format(last_login,'%d/%m/%y %H:%i') last_login,failed_logins,date_format(last_failed_login,'%d/%m/%y %H:%i') last_failed_login from users  where last_login is not null and user_id in(select user_id from users_to_campaigns where campaign_id in({$_SESSION['campaign_access']['list']})) order by last_login desc";
        return $this->db->query($qry)->result_array();
    }
    /* functions for the admin users page */
    public function get_users()
    {
        if ($_SESSION['role'] == "1") {
            $where = "";
        } else {
            $where = " and user_id in(select user_id from users_to_campaigns where campaign_id in({$_SESSION['campaign_access']['list']})) ";
        }
        $qry    = "select user_id,name,username,if(group_name is null,'-',group_name) group_name,team_id,if(team_name is null,'-',team_name) team_name,role_name,IF(user_status = 1,'On','Off') status_text,user_status,user_groups.group_id,role_id,user_email,user_telephone,ext,phone_un,phone_pw,attendee,ics,home_postcode from users  left join user_roles using(role_id) left join user_groups using(group_id) left join teams using(team_id) where 1 $where order by CASE WHEN user_status = 1 THEN 0 ELSE 1 END,role_id,name";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }
    /* functions for the admin groups page */
    public function get_groups()
    {
        if ($_SESSION['group'] == 1) {
            $qry    = "select * from user_groups";
            $result = $this->db->query($qry)->result_array();
        } else {
            $result = array(
                $_SESSION['group']
            );
        }
        return $result;
    }
    public function add_new_group($form)
    {
        return $this->db->insert("user_groups", $form);
    }
    public function update_group($form)
    {
        $this->db->where("group_id", $form['group_id']);
        return $this->db->update("user_groups", $form);
    }
    public function delete_group($id)
    {
        $this->db->where("group_id", $id);
        return $this->db->delete("user_groups");
    }
    /* team admin functions */
    public function get_teams()
    {
        $qry    = "select * from teams";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }
    public function add_new_team($form)
    {
        $managers = $form['managers'];
        unset($form['managers']);
        $this->db->insert("teams", $form);
        $id = $this->db->insert_id();
        foreach ($managers as $manager) {
            $this->db->insert("team_managers", array(
                "team_id" => $id,
                'user_id' => $manager
            ));
        }
    }
    public function update_team($form)
    {
        $this->db->where('team_id', $form['team_id']);
        $this->db->delete('team_managers');
        foreach ($form['managers'] as $manager) {
            $this->db->insert("team_managers", array(
                "team_id" => $form['team_id'],
                'user_id' => $manager
            ));
        }
        unset($form['managers']);
        $this->db->where("team_id", $form['team_id']);
        return $this->db->update("teams", $form);
    }
    public function delete_team($id)
    {
        $this->db->where("team_id", $id);
        return $this->db->delete("team_managers");
        $this->db->where("team_id", $id);
        return $this->db->delete("teams");
    }
    /* end team admin functions */
    public function add_new_user($form)
    {
        if (empty($form['team_id'])) {
            $form['team_id'] = NULL;
        }
        if (empty($form['ext'])) {
            $form['ext'] = NULL;
        }
        if (empty($form['phone_un'])) {
            $form['phone_un'] = NULL;
        }
        if (empty($form['password'])) {
            $form['password'] = '32250170a0dca92d53ec9624f336ca24';
        }

        $this->db->insert("users", $form);
        return $this->db->insert_id();
    }
    public function update_user($form)
    {
        if (empty($form['team_id'])) {
            $form['team_id'] = NULL;
        }
        if (empty($form['ext'])) {
            $form['ext'] = NULL;
        }
        if (empty($form['phone_un'])) {
            $form['phone_un'] = NULL;
        }
        $this->db->where("user_id", $form['user_id']);
        return $this->db->update("users", $form);
    }
    public function delete_user($id)
    {
        $this->db->where("user_id", $id);
        return $this->db->delete("users");
    }
    
    public function save_campaign_permissions($campaign, $permissions)
    {
        $this->db->where("campaign_id", $campaign);
        $this->db->delete("campaign_permissions");
        foreach ($permissions as $permission) {
            $permission['campaign_id'] = $campaign;
            $this->db->insert("campaign_permissions", $permission);
        }
        return true;
    }
    
    //functions for roles page
    public function get_roles()
    {
        $qry    = "select * from user_roles";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }
    public function delete_role($id)
    {
        $this->db->where("role_id", $id);
        return $this->db->delete("user_roles");
    }
    public function add_new_role($form)
    {
        unset($form['permission']);
        $this->db->insert("user_roles", $form);
        return $this->db->insert_id();
    }
    public function role_permissions($id)
    {
        $this->db->where("role_id", $id);
        return $this->db->get("role_permissions")->result_array();
    }
    public function campaign_permissions($id)
    {
        $this->db->where("campaign_id", $id);
        return $this->db->get("campaign_permissions")->result_array();
    }
    public function update_role($form)
    {
        $this->db->where("role_id", $form['role_id']);
        $this->db->update("user_roles", array(
            "role_name" => $form['role_name'],
			"landing_page" => $form['landing_page'],
			"timeout" => $form['timeout']
        ));
        $this->db->where("role_id", $form['role_id']);
        $this->db->delete("role_permissions");
        if (isset($form['permission'])) {
            foreach ($form['permission'] as $id => $val) {
                $this->db->insert('role_permissions', array(
                    "role_id" => $form['role_id'],
                    "permission_id" => $id
                ));
            }
        }
    }
    //functions for permissions page
    public function get_permissions()
    {
        $qry    = "select * from permissions order by permission_group,permission_name,description";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }
    
    /**
     * Remove an Default Hour
     */
    public function delete_default_hour($id)
    {
        $this->db->where("default_hours_id", $id);
        return $this->db->delete("default_hours");
    }
    
    public function get_custom_fields($campaign = array())
    {
        $this->db->select("record_details_fields.id,record_details_fields.field,field_name,is_select,is_buttons,is_decimal,is_visible,is_renewal,editable,sort,is_color,is_client_ref,is_owner,is_pot,is_source");
        $this->db->where_in("record_details_fields.campaign_id", $campaign);
        $this->db->join("record_details_options", 'record_details_options.id=record_details_fields.id', 'LEFT');
        $query = $this->db->get("record_details_fields");
        return $query->result_array();
    }
    
    public function save_custom_fields($post)
    {
        $campaign = $post['campaign'];
        unset($post['campaign']);
        
        $this->db->where('campaign_id', $campaign);
        $this->db->delete('record_details_fields');
        foreach ($post as $k => $v) {
            if (!empty($v['name'])) {
                
                $insert = array(
                    "campaign_id" => $campaign,
                    "field" => $k,
                    "field_name" => $v['name']
                );
                if (isset($v['visible'])) {
                    $insert["is_visible"] = 1;
                }
                if (!isset($v['editable'])) {
                    $insert["editable"] = 0;
                }
                if (isset($v['is_select'])) {
                    $insert["is_select"] = 1;
                }
				 if (isset($v['is_buttons'])) {
                    $insert["is_buttons"] = 1;
                }
				if (isset($v['is_decimal'])) {
                    $insert["is_decimal"] = 1;
                }
                if (isset($v['is_radio'])) {
                    $insert["is_radio"] = 1;
                }
                if (isset($v['is_client_ref'])) {
                    $insert["is_client_ref"] = 1;
                }
                if (isset($v['is_color'])) {
                    $insert["is_color"] = 1;
                }
                if (isset($v['is_owner'])) {
                    $insert["is_owner"] = 1;
                }
				 if (isset($v['is_pot'])) {
                    $insert["is_pot"] = 1;
                }
				 if (isset($v['is_source'])) {
                    $insert["is_source"] = 1;
                }
                if ($k == "d1" && isset($v['renewal'])) {
                    $insert["is_renewal"] = 1;
                }
                
                $this->db->insert("record_details_fields", $insert);
            }
            
        }
    }
    
}