<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('user_auth_check') )
{
    function user_auth_check($force_campaign=true)
    {
		$CI =& get_instance();
		$CI->load->model('User_model');
		$inactivity = false;
		//check last action time in session, if > 15 minutes destroy the session to log the user out
			if (isset($_SESSION['last_action'])&&$_SESSION['last_action'] + $_SESSION['timeout'] < time()) {
			$CI->User_model->log_timeout($_SESSION['user_id']);
			session_destroy();
			session_start(); //start the session again just so we can add an error message
			$inactivity = true;
		 }
        if (!isset($_SESSION['user_id'])) 
        { 
				//set the error message for the login page
				if($inactivity){
				$_SESSION['logout_message'] = 'You have been logged out due to inactivy';	
				} else {
				$_SESSION['logout_message'] = 'You are not logged in';		
				}
		
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo 'Logout';
                exit;
            }
			$url = base64_encode($_SERVER['REQUEST_URI']);
			//$url = base64_encode(str_replace(dirname($_SERVER['SCRIPT_NAME']),'',$_SERVER['REQUEST_URI']));
            redirect(base_url() . "user/login/$url");
        } 
		
		$_SESSION['last_action'] = time();
		$CI->User_model->log_activity($_SESSION['user_id']);
        return true;
    }   
	
	function campaign_access_dropdown(){
		$CI =& get_instance();
		$user = $_SESSION['user_id'];
	if(in_array("all campaigns",$_SESSION['permissions'])){
		$qry = "select campaign_id id,campaign_name name,client_name client,campaign_type_desc type from campaigns left join clients using(client_id) left join campaign_types using(campaign_type_id) where campaign_status = 1 group by campaign_id order by client_name,campaign_name";
	} else {
		$qry = "select campaign_id id,campaign_name name,client_name client,campaign_type_desc type from users_to_campaigns left join campaigns using(campaign_id) left join clients using(client_id) left join campaign_types using(campaign_type_id) where user_id = '$user' and campaign_status = 1 and campaign_id in (" .$_SESSION['campaign_access']['list'].") group by campaign_id order by client_name,campaign_name";
	}
		$query = $CI->db->query($qry);

		$result = $CI->db->query($qry)->result_array();
		if($CI->db->query($qry)->num_rows()){
		$campaigns = array();
		foreach($result as $row){
			$campaigns[$row['client']][] = array("id"=>$row['id'],"name"=>$row['name'],"type"=>$row['type'],"client"=>$row['client']);
		}
		return $campaigns;
		} else {
		unset($_SESSION['current_campaign']);
		}

	}
	
	function campaign_pots(){
				$CI =& get_instance();
		$user = $_SESSION['user_id'];
		$campaign = (isset($_SESSION['current_campaign'])?" and campaign_id = '".$_SESSION['current_campaign']."'":"");
	if(in_array("all campaigns",$_SESSION['permissions'])){	
		$qry = "select pot_id id,pot_name name, campaign_name from data_pots join records using(pot_id) join campaigns using(campaign_id) where campaign_status = 1 $campaign group by pot_id,campaign_id order by campaign_name,pot_name";
	} else {
		$qry = "select pot_id id,pot_name name,campaign_name from users_to_campaigns left join campaigns using(campaign_id) join records using(campaign_id) join data_pots using(pot_id) where user_id = '$user' and campaign_status = 1 and campaign_id in (" .$_SESSION['campaign_access']['list'].") $campaign group by pot_id,campaign_id order by campaign_name,pot_name";
	}
		$query = $CI->db->query($qry);

		$result = $CI->db->query($qry)->result_array();
		if($CI->db->query($qry)->num_rows()){
		$pots = array();
		foreach($result as $row){
			$pots[$row['campaign_name']][] = array("id"=>$row['id'],"name"=>$row['name'],"campaign_name"=>$row['campaign_name']);
		}
		return $pots;
		} else {
		unset($_SESSION['current_pot']);
		}
		
	}
	
	
	function check_page_permissions($required){
	if(@!in_array($required,$_SESSION['permissions'])){
		redirect(base_url() . "dashboard");
	}	
	}
	
	
}


/* End of file authentication_helper.php */
/* Location: ./application/helpers/authentication_helper.php */
