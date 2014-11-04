<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*  This page contains functiosn to populate dropdown menus on forms and filters. The queries simply return each id and value in the table in the format id=>name */
class Form_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
		if(isset($_SESSION['user_id'])){
        $this->role_id = $_SESSION['role'];
        $this->user_id = $_SESSION['user_id'];
		}
    }
    public function users_in_group($group_id, $campaign)
    {
        $this->db->select("user_id id,name");
        if (!empty($group_id)) {
            $this->db->where("group_id", $group_id);
        }
        $this->db->where("user_id not in(select user_id from users_to_campaigns where campaign_id = '$campaign')");
        $this->db->order_by("name");
        return $this->db->get("users")->result_array();
    }
    public function get_campaign_access($id)
    {
        $this->db->select("users.user_id id,name");
        $this->db->join("users", "users.user_id = users_to_campaigns.user_id", "left");
        $this->db->where("campaign_id", $id);
        $this->db->order_by("name");
        return $this->db->get("users_to_campaigns")->result_array();
    }
    public function get_surveys($campaign_id = "")
    {
        $qry = "select survey_info_id id,survey_name name,`default` from survey_info left join surveys_to_campaigns using(survey_info_id) where survey_status = 1 ";
        if (!empty($campaign_id)) {
            $qry .= " and campaign_id = '$campaign_id'";
        }
        $qry .= " group by survey_info_id order by survey_name";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }
    public function get_campaign_types($live = true)
    {
        $qry = "select campaign_type_id id,campaign_type_desc name from campaign_types left join campaigns using(campaign_type_id) ";
        $qry .= ($live ? " where campaign_status = 1 " : "");
        $qry .= " group by campaign_type_id order by campaign_type_desc";
        return $this->db->query($qry)->result_array();
    }
    public function get_campaign_features($campaign = false)
    {
		//before we get features in this campaign we need to check the user has permissions on these features
		if(!in_array("view recordings",$_SESSION['permissions'])){
		$exclude = " and feature_name <> 'Recordings'";	
		}
		if(!in_array("view appointments",$_SESSION['permissions'])){
		$exclude = " and feature_name <> 'Appointment Setting'";	
		}
		if(!in_array("view history",$_SESSION['permissions'])){
		$exclude = " and feature_name <> 'History'";	
		}
		if(!in_array("view ownership",$_SESSION['permissions'])){
		$exclude = " and feature_name <> 'Ownership Changer'";	
		}
		if(!in_array("view surveys",$_SESSION['permissions'])){
		$exclude = " and feature_name <> 'Surveys'";	
		}
		if(!in_array("view email",$_SESSION['permissions'])){
		$exclude = " and feature_name <> 'Emails'";	
		}
        $qry = "select feature_id id,feature_name name,panel_path path from campaign_features left join campaigns_to_features using(feature_id) where 1 $exclude ";
        $qry .= ($campaign ? " and campaign_id = $campaign " : "");
        $qry .= " group by feature_id order by feature_id";
        return $this->db->query($qry)->result_array();
    }
    public function get_campaigns()
    {
        $qry = "select campaign_id id,campaign_name name,campaign_type_desc type from campaigns left join campaign_types using(campaign_type_id) order by campaign_name";
        return $this->db->query($qry)->result_array();
    }
    
    public function get_campaigns_by_user($user_id)
    {
    	$qry = "select campaign_id id,campaign_name name,campaign_type_desc type 
    			from campaigns 
    			left join campaign_types using(campaign_type_id)
    			inner join users_to_campaigns using (campaign_id)
    			where user_id = $user_id
    			order by campaign_name";
    	return $this->db->query($qry)->result_array();
    }
    
    public function get_user_campaigns()
    {
        $qry = "select campaign_id id,campaign_name name from campaigns where campaign_id in({$_SESSION['campaign_access']['list']}) and campaign_status = 1 group by campaign_id order by campaign_name";
        return $this->db->query($qry)->result_array();
    }
    public function get_clients()
    {
        $qry = "select client_id id,client_name name from clients left join campaigns using(client_id) left join users_to_campaigns using(campaign_id) where campaign_status = 1 and  user_id = '{$_SESSION['user_id']}' group by client_id order by client_id";
        if (!$this->db->query($qry)->result_array()) {
            $qry = "select client_id id,client_name name from clients left join campaigns using(client_id) where campaign_status = 1 group by client_id order by client_id";
        }
        return $this->db->query($qry)->result_array();
    }
    public function get_users()
    {
        if ($_SESSION['role'] == 1) {
            $qry = "select user_id id,name from users where user_status = 1 ";
        } else {
            $qry = "select user_id id,name from users_to_campaigns left join users using(user_id) where user_status = 1 and campaign_id in ({$_SESSION['campaign_access']['list']}) group by user_id";
        }
        return $this->db->query($qry)->result_array();
    }
    public function get_agents()
    {
		$qry = "select user_id id,name from users left join role_permissions using(role_id) left join permissions using(permission_id) left join users_to_campaigns using(user_id) where permission_name = 'log hours' and campaign_id in ({$_SESSION['campaign_access']['list']}) group by user_id";

        return $this->db->query($qry)->result_array();
    }
    public function get_sources()
    {
        if ($_SESSION['role'] == 1) {
            $qry = "select source_id id,source_name name from data_sources";
        } else {
            $qry = "select source_id id,source_name name from records left join data_sources using(source_id) left join users_to_campaigns using(campaign_id) where campaign_id in ({$_SESSION['campaign_access']['list']}) group by source_id";
        }
        return $this->db->query($qry)->result_array();
    }
    public function get_categories()
    {
        $qry = "select question_cat_id id,question_cat_name name from questions_to_categories";
        return $this->db->query($qry)->result_array();
    }
    public function get_outcomes()
    {
        $qry = "select outcome_id id,outcome name from outcomes left join outcomes_to_roles using(outcome_id) where role_id = '{$this->role_id}' order by outcome";
        return $this->db->query($qry)->result_array();
    }
    public function get_progress_descriptions()
    {
        $qry = "select progress_id id,description name from progress_description";
        return $this->db->query($qry)->result_array();
    }
    public function get_sectors()
    {
        $qry = "select sector_id id,sector_name name from sectors";
        return $this->db->query($qry)->result_array();
    }
    public function get_subsectors()
    {
        $qry = "select subsector_id id,subsector_name name from subsectors";
        return $this->db->query($qry)->result_array();
    }
    public function get_status_list()
    {
        $qry = "select record_status_id id,status_name name from status_list";
        return $this->db->query($qry)->result_array();
    }
    public function get_groups()
    {
        $qry = "select group_id id,group_name name,theme_folder as theme_folder from user_groups";
        return $this->db->query($qry)->result_array();
    }
    public function get_teams()
    {
        $qry = "select team_id id,team_name name,group_id,if(group_name is not null,group_name,'-') group_name from teams left join user_groups using(group_id)";
        return $this->db->query($qry)->result_array();
    }
    public function get_roles()
    {
        $qry = "select role_id id,role_name name from user_roles";
        return $this->db->query($qry)->result_array();
    }
    public function get_managers()
    {
        $qry = "select user_id id,name from users where role_id in(2,3)";
        return $this->db->query($qry)->result_array();
    }
    public function get_team_managers($team)
    {
        $qry = "select user_id id from team_managers where team_id = '$team'";
        return $this->db->query($qry)->result_array();
    }
    public function populate_outcomes($id)
    {
        $qry = "select outcome_id id,outcome name from outcomes where outcome_id not in(select outcome_id from outcomes_to_campaigns where campaign_id = '$id')";
        return $this->db->query($qry)->result_array();
    }
    public function campaign_outcomes($id)
    {
        $qry = "select outcome_id id,outcome name from outcomes where outcome_id in(select outcome_id from outcomes_to_campaigns where campaign_id = '$id')";
        return $this->db->query($qry)->result_array();
    }
    public function get_templates()
    {
        $qry = "select template_id id,template_name name from email_templates order by template_name";
        return $this->db->query($qry)->result_array();
    }
    /**
     * Get a template by campaign_id
     *
     * @param integer $id
     * @return Template
     */
    public function get_templates_by_campaign_id($campaign_id)
    {
        $this->db->select("t.template_id id,t.template_name name");
        $this->db->from("email_templates t");
        $this->db->join("email_template_to_campaigns c", "c.template_id = t.template_id");
        $this->db->where("c.campaign_id", $campaign_id);
        return $this->db->get()->result_array();
    }
    public function get_users_in_role($role_id)
    {
        $this->db->select('user_id id,name');
        $this->db->where("role_id", $role_id);
        return $this->db->get('users')->result_array();
    }
	
	public function get_parked_codes()
    {
        $this->db->select('parked_code id,park_reason name');
        return $this->db->get('park_codes')->result_array();
    }
    
    public function get_hours_exception_type()
    {
    	$this->db->select('exception_type_id id,exception_name  name, paid');
    	return $this->db->get('hours_exception_type')->result_array();
    }
}