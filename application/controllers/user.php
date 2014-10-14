<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
		 $this->load->model('Form_model');
    }
    
    public function login()
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|strtolower');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|strtolower|md5');
            
            if ($this->form_validation->run()) {
                $username = $this->input->post('username');
                if ($this->User_model->validate_login($this->input->post('username'), $this->input->post('password'))) {
                    
                    $redirect = $this->input->post('redirect');
                    if ($redirect) {
                        redirect(str_replace("121sys/","",base64_decode($redirect)));
                    } else {
						if($_SESSION['role']==5||$_SESSION['role']==3){
						redirect('dashboard/agent');
						}
						if($_SESSION['role']==2){
						redirect('dashboard/management');
						}
						if($_SESSION['role']==4){
						redirect('dashboard/client');
						} else {
                        redirect('dashboard');
						}
                    }
                }
                $this->session->set_flashdata('error', 'Invalid username or password.');
				$this->session->set_flashdata('username', $username);
                redirect('user/login'); //Need to redirect to show the flash error.
            }
        }
        
        session_destroy();
        
        $redirect = ($this->uri->segment(3) ? $this->uri->segment(3) : false);
        $data = array(
'pageId' => 'login',
            'pageClass' => 'login',
            'title' => '121 Calling System',
            'redirect' => $redirect
        );
        $this->template->load('default', 'user/login', $data);
    }
    
    public function logout()
    {
		session_destroy();
        redirect('user/login');
    }
    
    public function account()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $this->load->library('form_validation');
            //$this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');
            
            $this->form_validation->set_rules('current_pass', 'current password', 'trim|required|strtolower|md5');
            $this->form_validation->set_rules('new_pass', 'new password', 'trim|required|strtolower|min_length[5]|matches[conf_pass]');
            $this->form_validation->set_rules('conf_pass', 'confirm password', 'trim|required|strtolower|min_length[5]');
            
            
            
            if ($this->form_validation->run()) {
                
                if ($this->User_model->validate_login($_SESSION['user_id'], $this->input->post('current_pass'), true)) {
                    $response = $this->User_model->set_password($this->input->post('new_pass'));
                    echo json_encode(array(
                        "success" => true,
                        "msg" => 'Password was updated'
                    ));
                    exit;
                } else {
                    echo json_encode(array(
                        "msg" => 'Current password was incorrect'
                    ));
                    exit;
                }
                
            }
            echo json_encode(array(
                "msg" => validation_errors()
            ));
            exit;
        }
        
        $data = array(
'pageId' => 'my-account',
            'pageClass' => 'my-account',
            'title' => 'My Account'
        );
        $this->template->load('default', 'user/account', $data);
    }
    
    /* at the bottom of default.php template: this function is ran every time a page is loaded and it checks whether user permissions/access have been changed or not so they can be reapplied without needing to log out */
	public function check_session(){
		$this->User_model->check_session();
		if(in_array("show footer",$_SESSION['permissions'])&&isset($_SESSION['current_campaign'])){	
		$user_id = $_SESSION['user_id'];
		$campaign = $_SESSION['current_campaign'];
		$duration = $this->User_model->update_hours_log($campaign,$user_id);
		$worked = $this->User_model->get_worked($campaign,$user_id);
		$transfers = $this->User_model->get_transfers($campaign,$user_id);
		$rate = number_format($transfers/($duration/60/60),2);
		echo json_encode(array("duration"=>$duration,"worked"=>$worked,"transfers"=>$transfers,"rate"=>$rate));
		}
	}
	
	/* at the bottom of default.php template: when the campaign drop down is changed we set the new campaign in the session so we can filter all the records easily */
	public function current_campaign(){
		$campaign=intval($this->uri->segment(3));
		if(intval($campaign)>0){
			if(in_array($campaign,$_SESSION['campaign_access']['array'])){
				        $campaign_features = $this->Form_model->get_campaign_features($campaign);
        				$features  = array();
        foreach ($campaign_features as $row) {
            $features[]         = $row['name'];
        }
				unset($_SESSION['filter']);
				$_SESSION['campaign_features']=$features;
				$_SESSION['current_campaign']=$campaign;
		}
		} else {
		if(!in_array("campaign search",$_SESSION['permissions'])){	
			unset($_SESSION['current_campaign']);
			unset($_SESSION['campaign_features']);
			echo "Redirect";
			}
		}
	}
	
    public function index()
    {
        //redirect('user/account');
    }
    
    
}