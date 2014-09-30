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
        $qry = "select campaign_id,campaign_name,campaign_type_desc,client_name,IF(campaign_status=1,'Live','Dead') campaign_status_text,campaign_type_id,custom_panel_name, campaign_status, client_id, IF(start_date is null,'-',date_format(start_date,'%d/%m/%Y')) start_date,IF(end_date is null,'-',date_format(end_date,'%d/%m/%Y')) end_date  from campaigns left join campaign_types using(campaign_type_id) left join clients using(client_id) where 1";
        if (!empty($urn)) {
            $qry .= " and camapign_id = '$campaign'";
        }
        $qry .= " order by campaign_id desc";
        return $this->db->query($qry)->result_array();
    }
    public function add_new_campaign($form)
    {
		$this->db->insert("campaigns",$form);
		return  $this->db->insert_id();
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
    public function save_campaign_features($form)
    {
        //first delete the old campaign features
        $this->db->where("campaign_id", $form['campaign_id']);
        $this->db->delete("campaigns_to_features");
        //then add the new ones
        if (isset($form['features'])) {
            foreach ($form['features'] as $feature) {
                $this->db->insert("campaigns_to_features", array(
                    "campaign_id" => $form['campaign_id'],
                    "feature_id" => $feature
                ));
            }
        }
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
        $qry    = "select user_id,name,username,group_name,role_name,IF(user_status = 1,'On','Off') status_text,user_status,group_id,role_id,user_email,user_telephone from users left join user_roles using(role_id) left join user_groups using(group_id) where user_id in(select user_id from users_to_campaigns where campaign_id in({$_SESSION['campaign_access']['list']})) order by CASE WHEN user_status = 1 THEN 0 ELSE 1 END";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }
    /* functions for the admin groups page */
    public function get_groups()
    {
		if($_SESSION['group']==1){ 
		$qry    = "select * from user_groups";
	 	$result = $this->db->query($qry)->result_array();
        } else {
		$result = array($_SESSION['group']);
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
    public function add_new_user($form)
    {
        return $this->db->insert("users", $form);
    }
    public function update_user($form)
    {
        $this->db->where("user_id", $form['user_id']);
        return $this->db->update("users", $form);
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
        return $this->db->insert("user_roles", $form);
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
        foreach ($form['permission'] as $id => $val) {
            $this->db->insert('role_permissions', array(
                "role_id" => $form['role_id'],
                "permission_id" => $id
            ));
        }
    }
    //functions for permissions page
    public function get_permissions()
    {
        $qry    = "select * from permissions";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }
}