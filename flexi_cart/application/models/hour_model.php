<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Hour_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /**
     * Get the Hours in a particular date range
     */
    public function get_hours($options)
    {
    	$date_from = $options['date_from'];
    	$date_to = $options['date_to'];
    	$campaign = $options['campaign'];
    	$agent = $options['agent'];
    	$team = $options['team'];
    	$where = "";
    	
    	if (!empty($campaign)) {
    		$where .= " and c.campaign_id = '$campaign'";
    	}
    	if (!empty($agent)) {
    		$where .= " and u.user_id = '$agent' ";
    	}
    	    	if (!empty($team)) {
    		$where .= " and u.team_id IN ($team) ";
    	}

    	$qry = "select DATE_FORMAT(h.date,'%d/%m/%Y') date,
                      h.hours_id,
                      u.name as user_name,
                      u.user_id,
                      c.campaign_id,
                      c.campaign_name,
                      h.duration,
                      h.time_logged,
                      if(h.comment is null,'',h.comment) comment,
                      if(m.name is not null,m.name,'-') as updated_name,
                      if(h.updated_date is not null,h.updated_date,'-') as updated_date,
                      dh.duration as default_hours
		    	from users u
		    	inner join role_permissions rp ON (rp.role_id = u.role_id)
		    	inner join permissions p ON (rp.permission_id = p.permission_id)
		    	inner join users_to_campaigns uc ON (uc.user_id = u.user_id)
		    	inner join campaigns c ON (c.campaign_id = uc.campaign_id)
		    	left join hours h ON (h.user_id = u.user_id and h.campaign_id = uc.campaign_id and h.date >= '$date_from 00:00:00' and h.date <= '$date_to 23:59:59')
		    	left join users m ON (m.user_id = h.updated_id)
		    	left join default_hours dh ON (u.user_id = dh.user_id and c.campaign_id = dh.campaign_id)
		    	where p.permission_name = 'log hours' and u.user_status = 1 ";

    	$qry .= $where;

    	$qry .= "order by user_name asc";

    	return $this->db->query($qry)->result_array();
    }
    
    /**
     * Add a new hour
     *
     * @param Form $form
     */
    public function add_new_hour($form)
    {
        $this->db->insert("hours", $form);
        
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
        
    }
    
    /**
     * Update a hour
     *
     * @param Form $form
     */
    public function update_hour($form)
    {
        $this->db->where("hours_id", $form['hours_id']);
        return $this->db->update("hours", $form);
    }

    /**
     * Remove an Hour
     */
    public function delete_hour($id)
    {
        $this->db->where("hours_id", $id);
        return $this->db->delete("hours");
    }

    /**
     * Get the Default Hours in a particular date range
     */
    public function get_default_hours($options)
    {
        $campaign = $options['campaign'];
        $agent = $options['agent'];
        $team = $options['team'];
        $where = "";

        if (!empty($campaign)) {
            $where .= " and c.campaign_id = '$campaign'";
        }
        if (!empty($agent)) {
            $where .= " and u.user_id = '$agent' ";
        }
        if (!empty($team)) {
            $where .= " and u.team_id IN ($team) ";
        }

        $qry = "select h.default_hours_id,
                      u.name as user_name,
                      u.user_id,
                      c.campaign_id,
                      c.campaign_name,
                      h.duration
		    	from users u
		    	inner join role_permissions rp ON (rp.role_id = u.role_id)
		    	inner join permissions p ON (rp.permission_id = p.permission_id)
		    	inner join users_to_campaigns uc ON (uc.user_id = u.user_id)
		    	inner join campaigns c ON (c.campaign_id = uc.campaign_id)
		    	left join default_hours h ON (h.user_id = u.user_id and h.campaign_id = uc.campaign_id)
		    	where p.permission_name = 'log hours' ";

        $qry .= $where;

        $qry .= "order by user_name asc, c.campaign_name";


        return $this->db->query($qry)->result_array();
    }

    /**
     * Add a new default hour
     *
     * @param Form $form
     */
    public function add_new_default_hour($form)
    {
        $this->db->insert("default_hours", $form);

        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $insert_id;

    }

    /**
     * Update a default hour
     *
     * @param Form $form
     */
    public function update_default_hour($form)
    {
        $this->db->where("default_hours_id", $form['default_hours_id']);
        return $this->db->update("default_hours", $form);
    }

    /**
     * Remove an Default Hour
     */
    public function delete_default_hour($id)
    {
        $this->db->where("default_hours_id", $id);
        return $this->db->delete("default_hours");
    }
}