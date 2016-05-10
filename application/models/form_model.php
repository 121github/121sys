<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*  This page contains functiosn to populate dropdown menus on forms and filters. The queries simply return each id and value in the table in the format id=>name */
class Form_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        if (isset($_SESSION['user_id'])) {
            $this->role_id = $_SESSION['role'];
            $this->user_id = $_SESSION['user_id'];
        }
    }
	
	public function pots_in_campaign($campaign){
		$qry = "select pot_id id, pot_name name from data_pots join records using(pot_id) where campaign_id = '$campaign'";
		return $this->db->query($qry)->result_array();
	}
	
	public function get_drivers($campaign_id=false,$region_id=false,$branch_id=false){
		$qry = "select user_id, name, region_name from users join branch_region_users using(user_id) join branch_regions using(region_id) join branch using(region_id) join branch_campaigns using(branch_id) where 1 ";
		$qry .= " and attendee = 0 ";
		$qry .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
		if($campaign_id){
		$qry .= " and campaign_id = '$campaign_id' ";
		}
		if($region_id){
		$qry .= " and branch.region_id = '$region_id' ";
		}
		if($branch_id){
		$qry .= " and branch.branch_id = '$branch_id' ";
		}
		$qry .= " group by user_id order by region_name,name ";
		return $this->db->query($qry)->result_array();
		
	}
	
		public function get_campaign_branches($campaign_id){
		$qry = "select branch_id,branch_name from branch join branch_campaigns using(branch_id) where campaign_id = '$campaign_id'";
		$qry .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
		$qry .=  " order by branch_name";
		$result = $this->db->query($qry)->result_array();
		return $result;
	}
	
	public function get_campaign_regions($campaign_id){
		$qry = "select region_id,region_name from branch_regions join branch using(region_id) join branch_campaigns using(branch_id) where campaign_id = '$campaign_id'";
		$qry .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
		$qry .=  " group by region_id order by region_name ";
		$result = $this->db->query($qry)->result_array();
		return $result;
	}
	
	public function get_contacts($urn){
		//this is used to get the available contacts in the appoitnment form
		$this->db->select("contact_id id,fullname name");
		$this->db->where("urn",$urn);
		$this->db->where('(fullname is NOT NULL or fullname <> "")', NULL, FALSE);
		return $this->db->get('contacts')->result_array();
	}
	
    public function get_custom_views()
    {
        $directory = APPPATH . 'views/records/custom';
        return array_diff(scandir($directory), array(
            '..',
            '.'
        ));
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
    public function get_campaign_access($id, $array = false)
    {
        $this->db->select("users.user_id id,name");
        $this->db->join("users", "users.user_id = users_to_campaigns.user_id", "left");
        if (!empty($id)) {
            $this->db->where("campaign_id", $id);
        }
        if (!empty($array)) {
            $campaigns = implode(",", $array);
            $this->db->where("campaign_id in($campaigns)");
        }
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
    public function get_campaign_features($campaign = false,$order="feature_id")
    {
        $exclude = "";
        //before we get features in this campaign we need to check the user has permissions on these features
        if ($_SESSION['role'] <> "1") {
            if (!in_array("view recordings", $_SESSION['permissions'])) {
                $exclude .= " and feature_name <> 'Recordings'";
            }
            if (!in_array("view appointments", $_SESSION['permissions'])) {
                $exclude .= " and feature_name <> 'Appointment Setting'";
            }
            if (!in_array("view history", $_SESSION['permissions'])) {
                $exclude .= " and feature_name <> 'History'";
            }
            if (!in_array("view ownership", $_SESSION['permissions'])) {
                $exclude .= " and feature_name <> 'Ownership Changer'";
            }
            if (!in_array("view surveys", $_SESSION['permissions'])) {
                $exclude .= " and feature_name <> 'Surveys'";
            }
            if (!in_array("view email", $_SESSION['permissions'])) {
                $exclude .= " and feature_name <> 'Emails'";
            }
            if (!in_array("view attachments", $_SESSION['permissions'])) {
                $exclude .= " and feature_name <> 'Attachments'";
            }
        } else {
            $exclude = '';
        }
        $qry = "select feature_id id,feature_name name,panel_path path from campaign_features left join campaigns_to_features using(feature_id) where 1 $exclude ";
        $qry .= ($campaign ? " and campaign_id = $campaign " : "");
        $qry .= " group by feature_id order by $order";
        return $this->db->query($qry)->result_array();
    }
    public function get_all_campaigns()
    {
        $qry = "select campaign_id id,campaign_name name,record_layout,campaign_type_desc type, daily_data, min_quote_days, max_quote_days, max_dials,virgin_order_1,virgin_order_2 from campaigns left join campaign_types using(campaign_type_id) order by campaign_status desc,campaign_name";
        return $this->db->query($qry)->result_array();
    }
	   public function get_campaigns()
    {
        $qry = "select campaign_id id,campaign_name name,record_layout,campaign_type_desc type, daily_data, min_quote_days, max_quote_days, max_dials,virgin_order_1,virgin_order_2 from campaigns left join campaign_types using(campaign_type_id) where campaign_status = 1 order by campaign_name";
        return $this->db->query($qry)->result_array();
    }
    public function get_campaigns_by_date($date)
    {
        $qry = "select campaign_id id,campaign_name name,record_layout,campaign_type_desc type, daily_data, min_quote_days, max_quote_days, max_dials,virgin_order_1,virgin_order_2 from campaigns left join campaign_types using(campaign_type_id) where start_date >= '".$date."' order by campaign_name";
        return $this->db->query($qry)->result_array();
    }
    public function get_calendar_campaigns()
    {
        $qry = "select campaign_id id,campaign_name name from campaigns left join campaigns_to_features using(campaign_id) left join campaign_features using(feature_id) where campaign_id in({$_SESSION['campaign_access']['list']}) and campaign_status = 1 and feature_name = 'Appointment Setting' group by campaign_id order by campaign_name";
        
        return $this->db->query($qry)->result_array();
    }
    
    public function get_calendar_users($campaign_ids = array(),$postcode=false,$type=false)
    {
        $where = "";
		$union = "";
		$select = "";
		$order = " order by name ";
        if (count($campaign_ids) > 0) {
            $where .= " and campaign_id in(" . implode(",", $campaign_ids) . ")";
        } else if(isset($_SESSION['current_campaign'])){
		$where = " and campaign_id = " . $_SESSION['current_campaign'];	
		}
		if($type){
			$union .= " union select user_id id,name name,home_postcode postcode from users left join users_to_campaigns using(user_id) left join campaigns using(campaign_id) join user_appointment_types using(user_id) where appointment_type_id = '$type' and campaign_id in({$_SESSION['campaign_access']['list']})  and campaign_status = 1 and attendee = 1 $where group by user_id ";
		}
        $qry = "select user_id id,name name,home_postcode postcode from users left join users_to_campaigns using(user_id) left join campaigns using(campaign_id) where user_id not in(select user_id from user_appointment_types) and campaign_id in({$_SESSION['campaign_access']['list']}) and campaign_status = 1 and attendee = 1 $where group by user_id $union";
        return $this->db->query($qry)->result_array();
		
		
			$new = "select from (select from users_in_campaign where user not in user_app_types union select from users_in_campaigns where user in user_app_types and type = $type)";
    }
	

	
	
	 public function get_calendar_types($campaign_ids = array())
    {
        $where = "";
        if (count($campaign_ids) > 0) {
        $where = " and campaign_id in(" . implode(",", $campaign_ids) . ")";
        } else if(isset($_SESSION['current_campaign'])){
		$where = " and campaign_id = " . $_SESSION['current_campaign'];	
		}
        $qry = "select appointment_type_id id,appointment_type name from appointment_types join campaign_appointment_types using(appointment_type_id) join campaigns using(campaign_id) where campaign_id in({$_SESSION['campaign_access']['list']})  and campaign_status = 1 $where group by appointment_type_id order by name";  
		//$this->firephp->log($qry);      
		$query = $this->db->query($qry);
		if($query->num_rows()){
        return  $query->result_array();
		} else {
		//if no app types have been set for the campaign just use the defaults
		$qry = "select appointment_type_id id,appointment_type name, icon from appointment_types where is_default = 1 ";
		return  $this->db->query($qry)->result_array();
		}
    }
    
    public function get_campaigns_by_user($user_id)
    {
        $qry = "select campaign_id id,campaign_name name,campaign_type_desc type 
    			from campaigns 
    			left join campaign_types using(campaign_type_id)
    			inner join users_to_campaigns using (campaign_id)
    			where user_id = $user_id and campaign_id in({$_SESSION['campaign_access']['list']}) 
    			order by campaign_name";
        return $this->db->query($qry)->result_array();
    }
    
    public function get_user_campaigns()
    {
        $qry = "select campaign_id id,campaign_name name from campaigns where campaign_id in({$_SESSION['campaign_access']['list']}) and campaign_status = 1 group by campaign_id order by campaign_name";
        return $this->db->query($qry)->result_array();
    }

    public function get_user_campaigns_ordered_by_group()
    {
        $qry = "select campaign_id id,campaign_name name, IF(campaign_group_name IS NOT NULL,campaign_group_name,'_OTHERS') group_name from campaigns left join campaign_groups using (campaign_group_id) where campaign_id in({$_SESSION['campaign_access']['list']}) and campaign_status = 1 group by campaign_id order by group_name, campaign_name";

        return $this->db->query($qry)->result_array();
    }
    
    public function get_user_email_campaigns()
    {
        $qry = "select campaign_id id,campaign_name name from campaigns where campaign_id in({$_SESSION['campaign_access']['list']}) and campaign_status = 1 and campaign_id in(select campaign_id from campaigns_to_features where feature_id = 9) group by campaign_id order by campaign_name";
        return $this->db->query($qry)->result_array();
    }

    public function get_user_sms_campaigns()
    {
        $qry = "select campaign_id id,campaign_name name from campaigns where campaign_id in({$_SESSION['campaign_access']['list']}) and campaign_status = 1 and campaign_id in(select campaign_id from campaigns_to_features where feature_id = 12) group by campaign_id order by campaign_name";
        return $this->db->query($qry)->result_array();
    }
    
    public function get_clients()
    {
        $qry = "select client_id id,client_name name from clients left join campaigns using(client_id) where campaign_status = 1 and campaign_id in({$_SESSION['campaign_access']['list']}) group by client_id order by client_name";
        return $this->db->query($qry)->result_array();
    }
	public function get_all_clients()
    {
        $qry = "select client_id id,client_name name from clients left join campaigns using(client_id) group by client_id order by client_name";
        return $this->db->query($qry)->result_array();
    }
    public function get_users()
    {
        if ($_SESSION['role'] == 1) {
            $qry = "select user_id id,name, ur.role_name from users inner join user_roles ur using (role_id) where user_status = 1 order by name";
        } else if (@in_array("search any owner", $_SESSION['permissions'])) {
            $qry = "select user_id id,name, ur.role_name from users_to_campaigns left join users using(user_id) inner join user_roles ur using (role_id) where user_status = 1 and campaign_id in ({$_SESSION['campaign_access']['list']}) group by user_id order by name";
        } else {
            $qry = "select user_id id,name, ur.role_name from users inner join user_roles ur using (role_id) where user_id = '{$_SESSION['user_id']}'";
        }
        return $this->db->query($qry)->result_array();
    }
    public function get_users_with_email()
    {
        if ($_SESSION['role'] == 1) {
            $qry = "select user_id id,name, ur.role_name from users inner join user_roles ur using (role_id) where user_status = 1 and user_email is not null order by name";
        } else if (@in_array("search any owner", $_SESSION['permissions'])) {
            $qry = "select user_id id,name, ur.role_name from users_to_campaigns left join users using(user_id) inner join user_roles ur using (role_id) where user_status = 1 and campaign_id in ({$_SESSION['campaign_access']['list']}) and user_email is not null group by user_id order by name";
        } else {
            $qry = "select user_id id,name, ur.role_name from users inner join user_roles ur using (role_id) where user_id = '{$_SESSION['user_id']}' and user_email is not null";
        }
        return $this->db->query($qry)->result_array();
    }
    public function get_agents()
    {
        $qry = "select user_id id,name from users left join role_permissions using(role_id) left join permissions using(permission_id) left join users_to_campaigns using(user_id) where permission_name = 'report on' and campaign_id in ({$_SESSION['campaign_access']['list']}) group by user_id order by name";
        
        return $this->db->query($qry)->result_array();
    }
	  public function get_users_logged()
    {
        $qry = "select user_id id,name from users left join role_permissions using(role_id) left join permissions using(permission_id) left join users_to_campaigns using(user_id) where permission_name = 'log hours' group by user_id";
        return $this->db->query($qry)->result_array();
    }
    public function get_sources()
    {
        if (in_array("all campaigns", $_SESSION['permissions'])) {
            $qry = "select source_id id,source_name name from data_sources";
        } else {
            $qry = "select source_id id,source_name name from records left join data_sources using(source_id) where campaign_id in ({$_SESSION['campaign_access']['list']}) group by source_name order by source_name";
        }
        return $this->db->query($qry)->result_array();
    }

    public function get_sources_by_campaign_list($campaign_list)
    {
        $where = " campaign_id in({$_SESSION['campaign_access']['list']})";
        if (isset($campaign_list) && !empty($campaign_list)) {
            $where = " campaign_id IN (" . implode(",", $campaign_list) . ") ";
        }
        $qry = "select source_id id,source_name name from records inner join data_sources using(source_id) where " . $where . " group by source_name order by source_name";

        return $this->db->query($qry)->result_array();
    }

	public function get_pots()
    {
            $qry = "select pot_id id,pot_name name from records left join data_pots using(pot_id) where campaign_id in ({$_SESSION['campaign_access']['list']}) group by pot_name order by pot_name";

        $x =  $this->db->query($qry)->result_array();
		return $x;
    }

    public function get_pots_by_campaign_list($campaign_list)
    {
        $where = " campaign_id in({$_SESSION['campaign_access']['list']})";
        if (isset($campaign_list) && !empty($campaign_list)) {
            $where = " campaign_id IN (" . implode(",", $campaign_list) . ") ";
        }
        $qry = "select pot_id id,pot_name name from records inner join data_pots using(pot_id) where " . $where . " group by pot_name order by pot_name";

        return $this->db->query($qry)->result_array();
    }
	
    public function get_categories()
    {
        $qry = "select question_cat_id id,question_cat_name name from questions_to_categories";
        return $this->db->query($qry)->result_array();
    }
    public function get_outcomes()
    {
        $qry = "select outcome_id id,outcome name from outcomes left join outcomes_to_campaigns using(outcome_id) where campaign_id in({$_SESSION['campaign_access']['list']}) group by outcome_id order by outcome ";
        return $this->db->query($qry)->result_array();
    }

    public function get_outcomes_by_campaign_list($campaign_list)
    {
        $where = " campaign_id in({$_SESSION['campaign_access']['list']})";
        if (isset($campaign_list) && !empty($campaign_list)) {
            $where = " campaign_id IN (" . implode(",", $campaign_list) . ") ";
        }
        $qry = "select outcome_id id,outcome name, positive from outcomes left join outcomes_to_campaigns using(outcome_id) where " . $where . " group by outcome_id order by outcome ";

        return $this->db->query($qry)->result_array();
    }

	   public function get_outcome_reasons($campaign_id,$outcome)
    {
        $qry = "select outcome_reason_id id,outcome_reason name from outcome_reasons join outcome_reason_campaigns using(outcome_reason_id) where outcome_id = '$outcome' and campaign_id = '$campaign_id' and campaign_id in({$_SESSION['campaign_access']['list']}) order by outcome_reason ";
        return $this->db->query($qry)->result_array();
    }
    public function get_progress_descriptions()
    {
        $qry = "select progress_id id,description name from progress_description";
        return $this->db->query($qry)->result_array();
    }
    public function get_sectors()
    {
        $qry = "select sector_id id,sector_name name from sectors order by sector_name";
        return $this->db->query($qry)->result_array();
    }
    public function get_subsectors($sectors = array())
    {
        $where = "";
        if (count($sectors) > 0) {
            $sector_list = "(0";
            foreach ($sectors as $sector_id) {
                $sector_list .= "," . $sector_id;
            }
            $sector_list .= ")";
            $where = " and sector_id in $sector_list";
        }
        $qry = "select subsector_id id,subsector_name name from subsectors where 1 $where order by subsector_name";
        return $this->db->query($qry)->result_array();
    }
    public function get_status_list()
    {
        $qry = "select record_status_id id,status_name name from status_list where record_status_id = 1";
		if(in_array("search dead",$_SESSION['permissions'])){  $qry .= " or record_status_id > 1"; } 
        return $this->db->query($qry)->result_array();
    }
	    public function get_all_groups()
    {
        $qry = "select group_id id,group_name name,theme_images,theme_color from user_groups group by user_groups.group_id";
        return $this->db->query($qry)->result_array();
    }
    public function get_groups()
    {
        $qry = "select group_id id,group_name name,theme_images,user_groups.theme_color from user_groups left join users using(group_id) left join users_to_campaigns using(user_id) where campaign_id in({$_SESSION['campaign_access']['list']}) group by user_groups.group_id";
        return $this->db->query($qry)->result_array();
    }
    public function get_teams()
    {
        $qry = "select teams.team_id id,team_name name,group_id,if(group_name is not null,group_name,'-') group_name from teams left join user_groups using(group_id) left join users using(group_id) left join users_to_campaigns using(user_id) where campaign_id in({$_SESSION['campaign_access']['list']}) group by teams.team_id order by team_name";
        return $this->db->query($qry)->result_array();
    }
    public function get_roles()
    {
        $qry = "select role_id id,role_name name,landing_page,timeout from user_roles";
        return $this->db->query($qry)->result_array();
    }
    public function get_managers()
    {
        if (in_array("all campaigns", $_SESSION['permissions'])) {
            $qry = "select user_id id,name from users where user_id in (select user_id from team_managers) and user_status = 1";
        } else {
            $qry = "select user_id id,name from users where user_id in team_managers left join users using(user_id) where group_id = '{$_SESSION['group']}'";
        }
        return $this->db->query($qry)->result_array();
    }
    public function get_team_managers($team)
    {
        $qry = "select user_id id from team_managers where team_id = '$team'";
        return $this->db->query($qry)->result_array();
    }
    public function populate_outcomes($id)
    {
        $qry = "select outcome_id id,outcome name from outcomes where outcome_id not in(select outcome_id from outcomes_to_campaigns where campaign_id = '$id') order by outcome";
        return $this->db->query($qry)->result_array();
    }
    public function campaign_outcomes($id)
    {
        $qry = "select outcome_id id,outcome name from outcomes where outcome_id in(select outcome_id from outcomes_to_campaigns where campaign_id = '$id') order by outcome";
        return $this->db->query($qry)->result_array();
    }
    public function get_templates()
    {
        if (in_array("all campaigns", $_SESSION['permissions'])) {
            $qry = "select template_id id,template_name name from email_templates order by template_name";
        } else {
            $qry = "select template_id id,template_name name from email_templates left join email_template_to_campaigns using(template_id) where campaign_id in({$_SESSION['campaign_access']['list']}) group by template_id order by template_name";
        }
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
    /**
     * Get a template by campaign ids
     *
     * @param $campaign_ids
     * @return Template
     */
    public function get_templates_by_campaign_ids($campaign_ids)
    {
        $this->db->select("t.template_id id,t.template_name name");
        $this->db->from("email_templates t");
        $this->db->join("email_template_to_campaigns c", "c.template_id = t.template_id");
        $this->db->where_in("c.campaign_id", $campaign_ids);

        return $this->db->get()->result_array();
    }

    /**
     * Get templates ordered by campaign group
     *
     */
    public function get_templates_ordered_by_campaign_group()
    {
        $qry = "select
                  template_id id,
                  template_name name,
                  IF(campaign_group_name IS NOT NULL,campaign_group_name,'_OTHERS') group_name
            from email_templates
            left join email_template_to_campaigns using (template_id)
            left join campaigns using (campaign_id)
            left join campaign_groups using (campaign_group_id)
            where
                campaign_id in({$_SESSION['campaign_access']['list']})
                and campaign_status = 1
            group by campaign_id
            order by group_name, campaign_name";

        return $this->db->query($qry)->result_array();
    }
    /**
     * Get templates ordered by campaign group
     *
     */
    public function get_sms_templates_ordered_by_campaign_group()
    {
        $qry = "select
                  template_id id,
                  template_name name,
                  IF(campaign_group_name IS NOT NULL,campaign_group_name,'_OTHERS') group_name
            from email_templates
            left join sms_template_to_campaigns using (template_id)
            left join campaigns using (campaign_id)
            left join campaign_groups using (campaign_group_id)
            where
                campaign_id in({$_SESSION['campaign_access']['list']})
                and campaign_status = 1
            group by campaign_id
            order by group_name, campaign_name";

        return $this->db->query($qry)->result_array();
    }


    /**
     * Get a sms template by campaign_id
     *
     * @param integer $id
     * @return Template
     */
    public function get_sms_templates_by_campaign_id($campaign_id)
    {
        $this->db->select("t.template_id id,t.template_name name");
        $this->db->from("sms_templates t");
        $this->db->join("sms_template_to_campaigns c", "c.template_id = t.template_id");
        $this->db->where("c.campaign_id", $campaign_id);
		 $this->db->group_by("t.template_id");
        return $this->db->get()->result_array();
    }

    /**
     * Get sms templates
     */
    public function get_sms_templates()
    {
        if (in_array("all campaigns", $_SESSION['permissions'])) {
            $qry = "select template_id id,template_name name from sms_templates order by template_name";
        } else {
            $qry = "select template_id id,template_name name from sms_templates left join sms_template_to_campaigns using(template_id) where campaign_id in({$_SESSION['campaign_access']['list']}) order by template_name";
        }
        return $this->db->query($qry)->result_array();
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
    
    
    public function get_time_exception_type()
    {
        $this->db->select('exception_type_id id,exception_name  name, paid');
        return $this->db->get('time_exception_type')->result_array();
    }

    public function get_hour_exception_type()
    {
        $this->db->select('exception_type_id id,exception_name  name, paid');
        return $this->db->get('hour_exception_type')->result_array();
    }
    
    public function get_renewal_date_field($campaign_id)
    {
        
        $qry = "select field
                from record_details_fields
                where is_renewal='1'
                and campaign_id = " . $campaign_id;
        
        $result = $this->db->query($qry)->result_array();
        return $result;
    }

    public function get_appointment_rule_reasons()
    {
        $qry = "select reason_id id, reason name from appointment_rule_reasons order by reason";
        return $this->db->query($qry)->result_array();
    }

    public function get_appointment_slots()
    {
        $qry = "select
                  appointment_slot_id id,
                  CONCAT(slot_name,' (',TIME_FORMAT(slot_start, '%H:%i'),'-',TIME_FORMAT(slot_end, '%H:%i'),')') name from appointment_slots order by slot_start";
        return $this->db->query($qry)->result_array();
    }

    /**
     * Get campaign_triggers
     */
    public function get_campaign_triggers_by_campaign_id($campaign_id) {
        $qry = "Select * from campaign_triggers where campaign_id=".$campaign_id;

        return $this->db->query($qry)->result_array();
    }

    /**
     * Get sms senders
     */
    public function get_sms_senders()
    {
        $qry = "select sender_id id,name from sms_sender order by name";
        return $this->db->query($qry)->result_array();
    }
}