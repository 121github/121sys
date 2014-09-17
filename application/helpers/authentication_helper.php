<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('user_auth_check') )
{
    function user_auth_check()
    {
        //unset($_SESSION['login']);
        if (!isset($_SESSION['user_id'])) 
        { 
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo 'Timeout';
                exit;
            }
			$url = base64_encode($_SERVER['REQUEST_URI']);
            redirect(base_url() . "index.php/user/login/$url");
        }
        return true;
    }   
}


/* End of file authentication_helper.php */
/* Location: ./application/helpers/authentication_helper.php */