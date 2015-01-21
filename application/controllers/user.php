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
                    if (isset($_SESSION['current_campaign'])) {
                        $this->set_campaign_features();
                    }
					$this->apply_default_filter($filter);
                    if ($this->input->post('password') == md5("pass123")) {
                        $redirect = base64_encode("user/account");
                    } else {
                        $redirect = $this->input->post('redirect');
                    }
                    if (!empty($redirect)) {
                        redirect(base64_decode($redirect));
                    } else {
                        if ($_SESSION['role'] == 5 || $_SESSION['role'] == 3) {
                            redirect('dashboard/agent');
                        }
                        if ($_SESSION['role'] == 2) {
                            redirect('dashboard/management');
                        }
                        if ($_SESSION['role'] == 4) {
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
        $data     = array(
            'pageId' => 'login',
            'pageClass' => 'login',
            'title' => '121 Calling System',
            'redirect' => $redirect
        );
        $this->template->load('default', 'user/login', $data);
    }
    
    public function logout()
    {
        if (in_array("log hours", $_SESSION['permissions'])) {
            $this->User_model->close_hours();
        }
        session_destroy();
        redirect('user/login');
    }
    
	public function apply_default_filter(){
			$this->load->model('Filter_model');
			$filter = array();
            /*set the default filter options up accounding to the "Default view" permissions */	
				if (@in_array("view live", $_SESSION['permissions'])) {
                     $filter['record_status'][] = 1;
                }			
				if (@in_array("view pending", $_SESSION['permissions'])) {
                     $filter['record_status'][] = 2;
                }	
				if (@in_array("view dead", $_SESSION['permissions'])) {
                    $filter['record_status'][] = 3;
                }
                if (@in_array("view completed", $_SESSION['permissions'])) {
                    $filter['record_status'][] = 4;
                }
              
				/*if the default is to view all parked record we just add all the parked codes to the filter (as long as there are less than 10 this will do the trick) */	
				if (@in_array("view parked", $_SESSION['permissions'])) {
                       $filter['parked_code'][] = 0;
                }
				
					if (in_array("view unassigned", $_SESSION['permissions'])) {
                    $filter['user_id'][] = 0; //this allows to search for null value in ownership table
					$filter['user_id'][] = $_SESSION['user_id'];
					}
                	if (in_array("view own records", $_SESSION['permissions'])&&!in_array("view unassigned", $_SESSION['permissions'])) {
                    $filter['user_id'][] = $_SESSION['user_id'];
					}
					
					$this->firephp->log($filter);
					if (!isset($_SESSION['filter']['values']['group_id'])) {	
                    if (in_array("view own group", $_SESSION['permissions'])&&!empty($_SESSION['group'])) {
                     $filter['group_id'][] = $_SESSION['group'];
                    }
					}
					if (!isset($_SESSION['filter']['values']['team_id'])) {	
                    if (in_array("view own team", $_SESSION['permissions'])&&!empty($_SESSION['team'])) {
                    $filter['team_id'][] = $_SESSION['team'];
                    }

                if (isset($_SESSION['current_campaign'])) {
                  $filter['campaign_id'][] =  $_SESSION['current_campaign'];
                }
				//we can add any additional filter requirements here if we ever need to
				$where = "";
                $_SESSION['filter']['where'] = $where;
            }
            
			$this->Filter_model->apply_filter($filter);	
		
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
    public function check_session()
    {
        if($this->User_model->check_session()){
		$this->User_model->load_user_session();
		$this->apply_default_filter();
		//if the user has been removed from their current campaign we should kick them out by unsetting the current campaign
		if (@!in_array($_SESSION['current_campaign'], $_SESSION['campaign_access']['array'])) {
            unset($_SESSION['current_campaign']);
            }
		echo "Session reloaded";
		}
        /* no longer showing footer 
        if(in_array("show footer",$_SESSION['permissions'])&&isset($_SESSION['current_campaign'])){	
        $user_id = $_SESSION['user_id'];
        $campaign = $_SESSION['current_campaign'];
        
        $this->load->model('Records_model');
        $duration = $this->User_model->get_duration($campaign,$user_id);
        $positive_outcome = $this->Records_model->get_positive_for_footer($campaign);
        $worked = $this->User_model->get_worked($campaign,$user_id);
        $transfers = $this->User_model->get_positive($campaign,$user_id, "Transfers");
        $cross_transfers = $this->User_model->get_cross_transfers_by_campaign_destination($campaign,$user_id);
        
        if($duration>0){
        $rate = number_format(($transfers + $cross_transfers)/($duration/60/60),2);
        } else { $rate = 0; }
        echo json_encode(array("duration"=>$duration,"worked"=>$worked,"transfers"=>$transfers+$cross_transfers,"rate"=>$rate,"positive_outcome"=>$positive_outcome));
        
        }
        */
    }
    
    public function set_campaign_features()
    {
        $campaign_features = $this->Form_model->get_campaign_features($_SESSION['current_campaign']);
        $features          = array();
        foreach ($campaign_features as $row) {
            $features[] = $row['name'];
        }
        $_SESSION['campaign_features'] = $features;
        
    }
    
    
    /* at the bottom of default.php template: when the campaign drop down is changed we set the new campaign in the session so we can filter all the records easily */
    public function current_campaign()
    {
        $campaign = intval($this->uri->segment(3));
        $user_id  = $_SESSION['user_id'];
        if ($campaign > "0") {
            if (in_array($campaign, $_SESSION['campaign_access']['array'])) {
                /* no longer logging in realtime  
                if(in_array("log hours",$_SESSION['permissions'])){
                //start logging the duration on the selected campaign
                $this->User_model->update_hours_log($campaign,$user_id);
                }
                */
                //reset the permissions
                $this->User_model->set_permissions();
                //this function lets you add and remove permisisons based on the selected campaign rather than user role! :)
                $campaign_permissions = $this->User_model->campaign_permissions($campaign);
                foreach ($campaign_permissions as $row) {
                    //a 1 indicates the permission should be added otherwize it is revoked!
                    if ($row['permission_state'] == "1") {
                        $_SESSION['permissions'][$row['permission_id']] = $row['permission_name'];
                    } else {
                        unset($_SESSION['permissions'][$row['permission_id']]);
                    }
                }
                $_SESSION['current_campaign'] = $campaign;
				$this->set_campaign_features();
				
                unset($_SESSION['next']);
                unset($_SESSION['filter']);
            }
        } else {
            if (!in_array("all campaigns", $_SESSION['permissions'])) {
                unset($_SESSION['current_campaign']);
                unset($_SESSION['campaign_features']);
                /* no longer logging in realtime 
                $this->User_model->close_hours();
                */
                echo "no campaign selected";
            }
        }
    }
    
    public function index()
    {
        //redirect('user/account');
    }
    
    
}