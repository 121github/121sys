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
        
        $qry = "SELECT *, DATE_FORMAT(last_login,'%D %M %Y') AS logdate, 
                   DATE_FORMAT(last_login,'%T') AS logtime 
                   FROM users WHERE $check_field = ? 
                   AND password = ? AND user_status = 1 ";
        
        $result = $this->db->query($qry, array(
            $username,
            $password
        ))->result_array();
        if (!empty($result)) {
            $config_query = "SELECT * from configuration";
            $config       = $this->db->query($config_query)->row_array(0);
            
            $_SESSION['config']  = $config;
            $result              = $result[0];
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['name']    = $result['name'];
            $_SESSION['role']    = $result['role_id'];
            $_SESSION['group']   = $result['group_id'];
            $_SESSION['logdate'] = $result['logdate'];
            $_SESSION['logtime'] = $result['logtime'];
            $_SESSION['email']   = $result['user_email'];
            $_SESSION['ext']     = $result['ext'];
			//get the permissions for the users role and store them in the session
            $role_permissions = $this->db->query("select * from role_permissions left join permissions using(permission_id) where role_id = '".$result['role_id']."'")->result_array();
			foreach($role_permissions as $row){
			$_SESSION['permissions'][$row['permission_id']]=$row['permission_name'];
			}
            //get the campaigns that this user has access to
            $user_campaigns                       = $this->db->query("select campaign_id from `users_to_campaigns` where user_id = '" . $result['user_id'] . "'")->result_array();
            $campaign_access                      = "'',";
            $_SESSION['campaign_access']['array'] = array('');
            foreach ($user_campaigns as $row) {
                $campaign_access .= $row['campaign_id'] . ",";
                $_SESSION['campaign_access']['array'][] = $row['campaign_id'];
            }
            //save the campaign access into a list format so we can use it in all the queries. eg "where campaign_id in({$_SESSION['campaign_access']})"
            $_SESSION['campaign_access']['list'] = rtrim($campaign_access, ",");
            
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
    
    
    public function set_password($password)
    {
        $password = md5($password);
        $this->db->where("user_id", $_SESSION['user_id']);
        $this->db->update("users", array(
            "password" => $password
        ));
    }
}