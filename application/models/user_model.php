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
    public function validate_login($username, $password, $user_id = false)
    {
        if ($user_id) {
            $check_field = "user_id";
        } else {
            $check_field = "username";
        }
        $qry    = "SELECT *, DATE_FORMAT(last_login,'%D %M %Y') AS logdate, 
                   DATE_FORMAT(last_login,'%T') AS logtime 
                   FROM users WHERE $check_field = ? 
                   AND password = ? AND user_status = 1 ";
        $result = $this->db->query($qry, array(
            $username,
            $password
        ))->result_array();
        
        if (!empty($result)) {
            $config_query        = "SELECT * from configuration";
            $config              = $this->db->query($config_query)->row_array(0);
            $_SESSION['config']  = $config;
            $result              = $result[0];
            $_SESSION['user_id'] = $result['user_id'];
			$_SESSION['last_action'] = time();
            //get the permissions for the users role and store them in the session
            $this->load_user_session();
            $this->db->where("user_id", $result['user_id']);
            $this->db->update("users", array(
                "last_login" => date('Y-m-d H:i:s'),
                "failed_logins" => "0"
            ));
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
                $this->load_user_session();
                if (isset($_SESSION['current_campaign']) && !in_array($_SESSION['current_campaign'], $_SESSION['campaign_access']['array'])) {
                    unset($_SESSION['current_campaign']);
                }
                echo "Session reloaded";
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
        
        $theme_folder = $this->db->query("select theme_folder from user_groups where group_id = '" . $_SESSION['group'] . "'")->row()->theme_folder;
        if (!empty($theme_folder)) {
            $_SESSION['theme_folder'] = $theme_folder;
        }
        
        $role_permissions        = $this->db->query("select * from role_permissions left join permissions using(permission_id) where role_id = '" . $_SESSION['role'] . "'")->result_array();
        $_SESSION['permissions'] = array();
        foreach ($role_permissions as $row) {
            $_SESSION['permissions'][$row['permission_id']] = $row['permission_name'];
        }
        if (in_array("all campaigns", $_SESSION['permissions'])) {
            //admin has all access
            $qry = "select campaign_id from `campaigns` where campaign_status = 1";
        } else {
            //other users can can only see what they have access to
            $qry = "select campaign_id from campaigns left join `users_to_campaigns` using(campaign_id) where campaign_status = 1 and user_id = '" . $_SESSION['user_id'] . "' group by campaign_id";
        }
        $user_campaigns = $this->db->query($qry)->result_array();
        
        if (count($user_campaigns) < 1 && $_SESSION['role'] <> 1) {
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
        }
        //save the campaign access into a list format so we can use it in all the queries. eg "where campaign_id in({$_SESSION['campaign_access']})"
        $_SESSION['campaign_access']['list'] = $campaign_access;
        //finally we unflag the update session    
        $this->db->where('user_id', $_SESSION['user_id']);
        $this->db->update('users', array(
            'reload_session' => '0'
        ));
    }
    public function set_password($password)
    {
        $password = md5($password);
        $this->db->where("user_id", $_SESSION['user_id']);
        $this->db->update("users", array(
            "password" => $password
        ));
    }
    
	public function update_hours_log($campaign, $user_id){
		//check if their is an entry in the hours table and if no put one in
		$qry = "select hours_id from hours where user_id = '$user_id' and date(`date`) = curdate() and hours.campaign_id = '$campaign'";
		if(!$this->db->query($qry)->num_rows()){
			$this->firephp->log($qry);
			$qry = "insert into hours set user_id = '$user_id',duration=0,exception=0,`date`=now(),campaign_id = '$campaign'";	
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
	   $this->firephp->log($qry);
        $query = $this->db->query($qry);
        if ($query->num_rows()) {
            return $query->row()->secs;
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
    
    public function get_positive($campaign, $user_id, $positive = 0)
    {
        if ($positive == "Transfers") {
            $outcome_id = "70,71";
        } else if ($positive == "Surveys") {
            $outcome_id = "60";
        } else if ($positive == "Appointments") {
            $outcome_id = "72";
        }
        $qry   = "select count(distinct urn) transfers from history where outcome_id in($outcome_id) and campaign_id = '$campaign' and user_id = '$user_id' and date(contact) = curdate()";
        $query = $this->db->query($qry);
        if ($query->num_rows()) {
            return $query->row()->transfers;
        } else {
            return "0";
        }
    }
    
	public function close_hours(){
	$qry = "update hours_logged set end_time = now() where end_time is null and user_id = '{$_SESSION['user_id']}'";
	$this->db->query($qry);
	}

	
}
