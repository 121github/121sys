<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /* functions for the admin campaigns page */
    public function get_campaign_details($campaign = "")
    {
        $qry = "select campaign_id,campaign_name,campaign_type_desc,record_layout,client_name,IF(campaign_status=1,'Live','Dead') campaign_status_text,campaign_type_id,custom_panel_name, campaign_status, client_id, IF(start_date is null,'-',date_format(start_date,'%d/%m/%Y')) start_date,IF(end_date is null,'-',date_format(end_date,'%d/%m/%Y')) end_date, min_quote_days, max_quote_days, bc.months_ago, bc.months_num  from campaigns left join campaign_types using(campaign_type_id) left join clients using(client_id) left join backup_by_campaign bc using (campaign_id) where 1";
        if (!empty($urn)) {
            $qry .= " and camapign_id = '$campaign'";
        }
        $qry .= " order by campaign_id desc";
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
        $qry = "select client_id from clients where client_name = " . $this->db->escape($client_name);
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
				$insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_string);
				$this->db->query($insert_query);
            }
        }
    }

    public function get_backup_by_campaign($campaign_id)
    {
        $qry = "select *
                from backup_by_campaign
                where campaign_id = ".$campaign_id;
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
        $qry    = "select user_id,name,username,if(group_name is null,'-',group_name) group_name,team_id,if(team_name is null,'-',team_name) team_name,role_name,IF(user_status = 1,'On','Off') status_text,user_status,user_groups.group_id,role_id,user_email,user_telephone,ext,phone_un,phone_pw from users  left join user_roles using(role_id) left join user_groups using(group_id) left join teams using(team_id) where 1 $where order by CASE WHEN user_status = 1 THEN 0 ELSE 1 END,role_id,name";
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
		if(empty($form['team_id'])){
		$form['team_id'] = NULL;
		}
		if(empty($form['ext'])){
		$form['ext'] = NULL;
		}
        return $this->db->insert("users", $form);
    }
    public function update_user($form)
    {
		if(empty($form['team_id'])){
		$form['team_id'] = NULL;
		}
		if(empty($form['ext'])){
		$form['ext'] = NULL;
		}
        $this->db->where("user_id", $form['user_id']);
        $this->db->update("users", $form);
		$this->firephp->log($this->db->last_query());
    }
    public function delete_user($id)
    {
        $this->db->where("user_id", $id);
        return $this->db->delete("users");
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
    public function update_role($form)
    {
        $this->db->where("role_id", $form['role_id']);
        $this->db->update("user_roles", array(
            "role_name" => $form['role_name']
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
        $qry    = "select * from permissions order by permission_group,permission_name";
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
	
	public function get_custom_fields($campaign){
		$this->db->select("record_details_fields.id,record_details_fields.field,field_name,is_select,is_visible,is_renewal,sort");
		$this->db->where("record_details_fields.campaign_id",intval($campaign));
		$this->db->join("record_details_options",'record_details_options.id=record_details_fields.id','LEFT');
		$query = $this->db->get("record_details_fields");
		return $query->result_array();
	}
	
	public function save_custom_fields($post){
		$campaign = $post['campaign'];
		unset($post['campaign']);
		
		$this->db->where('campaign_id',$campaign);
		$this->db->delete('record_details_fields');
			
		foreach($post as $k=>$v){
		if(!empty($v['name'])){
			
			$insert = array("campaign_id"=>$campaign,"field"=>$k,"field_name"=>$v['name'],"is_select"=>"0");
			if(isset($v['visible'])){
				$insert["is_visible"] = 1;
			}
			if($k=="d1"&&isset($v['renewal'])){
				$insert["is_renewal"] = 1;
			}
			$this->db->insert("record_details_fields",$insert);
		}
			
		}
	}
	
}