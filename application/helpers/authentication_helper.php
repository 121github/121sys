<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('user_auth_check') )
{
    function user_auth_check($force_campaign=true)
    {
		
		if(!isset($_SESSION['current_campaign'])&&$force_campaign){
			redirect(base_url());	
			}
        //unset($_SESSION['login']);
        if (!isset($_SESSION['user_id'])) 
        { 
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo 'Timeout';
                exit;
            }
			$url = base64_encode($_SERVER['REQUEST_URI']);
            redirect(base_url() . "user/login/$url");
        }
        return true;
    }   
	
	function campaign_access_dropdown(){
		$CI =& get_instance();
		$user = $_SESSION['user_id'];
		$qry = "select campaign_id id,campaign_name name,client_name client,campaign_type_desc type from users_to_campaigns left join campaigns using(campaign_id) left join clients using(client_id) left join campaign_types using(campaign_type_id) where user_id = '$user' and campaign_status = 1 and campaign_id in (" .$_SESSION['campaign_access']['list'].")";
		$result = $CI->db->query($qry)->result_array();
		$campaigns = array();
		foreach($result as $row){
			$campaigns[$row['client']][] = array("id"=>$row['id'],"name"=>$row['name'],"type"=>$row['type'],"client"=>$row['client']);
		}
		return $campaigns;
	}
	
}


/* End of file authentication_helper.php */
/* Location: ./application/helpers/authentication_helper.php */