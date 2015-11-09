<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Time_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /**
     * Get the Time in a particular date range
     */
    public function get_time($options)
    {
    	$date_from = $options['date_from'];
    	$date_to = $options['date_to'];
    	$agent = $options['agent'];
    	$team = $options['team'];
    	$where = "";

    	if (!empty($agent)) {
    		$where .= " and u.user_id = '$agent' ";
    	}
    	    	if (!empty($team)) {
    		$where .= " and u.team_id IN ($team) ";
    	}

    	$qry = "select DATE_FORMAT(t.date,'%d/%m/%Y') date,
                      t.time_id,
                      u.name as user_name,
                      u.user_id,
                      t.start_time,
                      t.end_time,
                      if(t.comment is null,'',t.comment) comment,
                      if(m.name is not null,m.name,'-') as updated_name,
                      if(t.updated_date is not null,t.updated_date,'-') as updated_date,
                      (select sum(he.duration) from time_exception he where t.time_id = he.time_id) as exceptions,
                      dt.start_time as default_start_time,
                      dt.end_time as default_end_time
		    	from users u
		    	inner join role_permissions rp ON (rp.role_id = u.role_id)
		    	inner join permissions p ON (rp.permission_id = p.permission_id)
		    	left join time t ON (t.user_id = u.user_id and t.date >= '$date_from 00:00:00' and t.date <= '$date_to 23:59:59')
		    	left join users m ON (m.user_id = t.updated_id)
		    	left join default_time dt ON (u.user_id = dt.user_id)
		    	where p.permission_name = 'log hours' ";

    	$qry .= $where;

    	$qry .= "order by user_name asc";

    	return $this->db->query($qry)->result_array();
    }
    
    /**
     * Add a new Time Exception
     */
    public function add_time_exception($form)
    {
    	$this->db->insert("time_exception", $form);
    	return $this->db->insert_id();
    }
    
    /**
     * Remove an Time Exception
     */
    public function delete_time_exception($id)
    {
    	$this->db->where("exception_id", $id);
    	return $this->db->delete("time_exception");
    }
    
    /**
     * Get the Time Exceptions for a particular time
     */
    public function get_time_exception($time_id)
    {
    	$qry    = "select * 
    			from time_exception
    			inner join time_exception_type using(exception_type_id)
    			where time_id = ".$time_id;
    	
    	return $this->db->query($qry)->result_array();
    }
    
	/**
     * Add a new time
     *
     * @param Form $form
     */
    public function add_new_time($form)
    {
        $this->db->insert("time", $form);
        
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
        
    }
    
    /**
     * Update a time
     *
     * @param Form $form
     */
    public function update_time($form)
    {
        $this->db->where("time_id", $form['time_id']);
        return $this->db->update("time", $form);
    }

    /**
     * Remove an Time
     */
    public function delete_time($id)
    {
        $this->db->where("time_id", $id);
        return $this->db->delete("time");
    }

    /**
     * Get the Default Time in a particular date range
     */
    public function get_default_time($options)
    {
        $agent = $options['agent'];
        $team = $options['team'];
        $where = "";

        if (!empty($agent)) {
            $where .= " and u.user_id = '$agent' ";
        }
        if (!empty($team)) {
            $where .= " and u.team_id IN ($team) ";
        }

        $qry = "select t.default_time_id,
                      u.name as user_name,
                      u.user_id,
                      t.start_time,
                      t.end_time
		    	from users u
		    	inner join role_permissions rp ON (rp.role_id = u.role_id)
		    	inner join permissions p ON (rp.permission_id = p.permission_id)
		    	left join default_time t ON (t.user_id = u.user_id)
		    	where p.permission_name = 'log hours' ";

        $qry .= $where;

        $qry .= "order by user_name asc";


        return $this->db->query($qry)->result_array();
    }

    /**
     * Add a new default time
     *
     * @param Form $form
     */
    public function add_new_default_time($form)
    {
        $this->db->insert("default_time", $form);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $insert_id;

    }

    /**
     * Update a default time
     *
     * @param Form $form
     */
    public function update_default_time($form)
    {
        $this->db->where("default_time_id", $form['default_time_id']);
        return $this->db->update("default_time", $form);
    }

    /**
     * Remove an Default Time
     */
    public function delete_default_time($id)
    {
        $this->db->where("default_time_id", $id);
        return $this->db->delete("default_time");
    }
}