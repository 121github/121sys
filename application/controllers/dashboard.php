<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
		$this->_campaigns = campaign_access_dropdown();
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Dashboard_model');
        $this->load->model('User_model');
		unset($_SESSION['navigation']);
    }
    
   
    //this laods the user dashboard view  
    public function user_dash()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
		$email_campaigns = $this->Form_model->get_user_email_campaigns();
        $surveys = $this->Form_model->get_surveys();
        $agents       = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        $sources      = $this->Form_model->get_sources();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Dashboard',
            'title' => 'Dashboard',
			'page'=> array('dashboard'=>'overview'),
            'javascript' => array(
                'charts.js',
                'dashboard.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
			'agents'=>$agents,
			'team_managers'=>$teamManagers,
			'sources'=>$sources,
			'email_campaigns' => $email_campaigns,
            'campaigns' => $campaigns,
            'surveys' => $surveys,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/dashboard.php', $data);
    }
    
	    //this is the controller loads the initial view for the activity dashboard
    public function callbacks()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
        $surveys   = $this->Form_model->get_surveys();
        $type = $this->uri->segment(3);
		if($type=="missed"){
			$date_from = date('2014-07-02');
			$date_to = date('Y-m-d H:s');
			$btntext = "Missed";
		} else if($type=="upcoming"){
			$date_from = date('Y-m-d H:s');
			$date_to = date('Y-m-d 2020-01-01'); //if i'm not here in 5 years this might break :O
			$btntext = "Upcoming";
		} else {
			$date_from = "";
			$date_to = "";
			$btntext = "";
		}
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Dashboard',
            'title' => 'Dashboard',
			'page'=> array('dashboard'=>'callbacks'),
            'javascript' => array(
                'charts.js',
                'dashboard.js',
				'lib/moment.js',
                'lib/daterangepicker.js'
            ),
			'date_from'=>$date_from,
			'date_to'=>$date_to,
			'btntext'=>$btntext,
            'campaigns' => $campaigns,
            'surveys' => $surveys,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
				'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/callbacks.php', $data);
    }
    
    //this is the controller loads the initial view for the activity dashboard
    public function agent()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
        $surveys   = $this->Form_model->get_surveys();
        $type = $this->uri->segment(3);
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Dashboard',
            'title' => 'Dashboard',
			'page'=> array('dashboard'=>'agent'),
            'javascript' => array(
                'charts.js',
                'dashboard.js',
				'lib/moment.js',
                'lib/daterangepicker.js'
            ),
			'type'=>$type,
            'campaigns' => $campaigns,
            'surveys' => $surveys,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
				'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/agent_dash.php', $data);
    }
    //this is the controller loads the initial view for the client dashboard
    public function client()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
        $surveys   = $this->Form_model->get_surveys();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Dashboard',
            'title' => 'Dashboard',
			'page'=> array('dashboard'=>'client'),
            'javascript' => array(
                'charts.js',
                'dashboard.js'
            ),
            'campaigns' => $campaigns,
            'surveys' => $surveys,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css'
            )
        );
        $this->template->load('default', 'dashboard/client_dash.php', $data);
    }
    
	 //this is the controller loads the initial view for the nbf dashboard
    public function nbf()
    {        
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Dashboard',
            'title' => 'Dashboard',
			'page'=> array('dashboard'=>'nbf'),
            'javascript' => array(
                'charts.js',
                'dashboard.js'
            ),
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css'
            )
        );
        $this->template->load('default', 'dashboard/nbf_dash.php', $data);
    }
	
    //this is the controller loads the initial view for the management dashboard
    public function management()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
        $surveys   = $this->Form_model->get_surveys();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Dashboard',
            'title' => 'Dashboard',
			'page'=> array('dashboard'=>'management'),
            'javascript' => array(
                'charts.js',
                'dashboard.js'
            ),
            'campaigns' => $campaigns,
            'surveys' => $surveys,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css'
            )
        );
        $this->template->load('default', 'dashboard/management_dash.php', $data);
    }
    
    
    //this controller sends the history data back the page in JSON format. It ran when the javascript function "history_panel" is executed
    public function get_history()
    {
        if ($this->input->is_ajax_request()) {
            $filter  = $this->input->post();
            $results = $this->Dashboard_model->get_history($filter);
            foreach ($results as $k => $row) {
                $results[$k]['time'] = date('g:i a', strtotime($row['contact']));
                $results[$k]['date'] = date('jS M', strtotime($row['contact']));
            }
            
            echo json_encode(array(
                "success" => true,
                "data" => $results
            ));
        }
    }
    //this controller sends the outcome data back the page in JSON format. It ran when the javascript function "outcomes_panel" is executed
    public function get_outcomes()
    {
        if ($this->input->is_ajax_request()) {
			$data = array();
            $filter  = $this->input->post();
            $results = $this->Dashboard_model->get_outcomes($filter);
            foreach ($results as $k => $row) {
                $data[] = array(
                    "outcome" => $row['outcome'],
                    "count" => $row['count']
                );
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data,
				"date"=>date('Y-m-d')
            ));
        }
    }
    
    //this controller sends the system stats data back the page in JSON format. It ran when the javascript function "system_stats" is executed
    public function system_stats()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
            $data     = $this->Dashboard_model->system_stats($filter);
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
        
    }
    //this controller sends the comments data back the page in JSON format. It ran when the javascript function "comments_panel" is executed
    public function get_comments()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
            $data     = $this->Dashboard_model->get_comments($filter);
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
        
    }
    //this controller sends the urgent records back the page in JSON format. It ran when the javascript function "urgent_panel" is executed
    public function get_urgent()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Records_model');
            $filter = $this->input->post();
            $data     = $this->Dashboard_model->get_urgent($filter);
            foreach ($data as $k => $v) {
                $comment                  = $this->Records_model->get_last_comment($v['urn']);
                $data[$k]['last_comment'] = (!empty($comment) ? $comment : "No Comment Found");
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data,
                "msg" => "No records found"
            ));
        }
        
    }
	
	    //this controller sends the urgent records back the page in JSON format. It ran when the javascript function "urgent_panel" is executed
    public function get_pending()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Records_model');
            $filter = $this->input->post();
            $data     = $this->Dashboard_model->get_pending($filter);
            foreach ($data as $k => $v) {
                $comment                  = $this->Records_model->get_last_comment($v['urn']);
                $data[$k]['last_comment'] = (!empty($comment) ? $comment : "No Comment Found");
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data,
                "msg" => "No records found"
            ));
        }
        
    }
	    //this controller sends the urgent records back the page in JSON format. It ran when the javascript function "appointments_panel" is executed
    public function get_appointments()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Records_model');
            $filter = $this->input->post();
            $data     = $this->Dashboard_model->get_appointments($filter);
            foreach ($data as $k => $v) {
                $comment                  = $this->Records_model->get_last_comment($v['urn']);
                $data[$k]['last_comment'] = (!empty($comment) ? $comment : "No Comment Found");
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data,
                "msg" => "No records found"
            ));
        }
        
    }
    //this controller sends the favorites records back the page in JSON format. It ran when the javascript function "favorites_panel" is executed
    public function get_favorites()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
            $data     = $this->Dashboard_model->get_favorites($filter);
            echo json_encode(array(
                "success" => true,
                "data" => $data,
                "msg" => "No records found"
            ));
        }
        
    }
    //this controller displays the missed callback data in JSON format. It gets called by the javascript function "missed_callbacks_panel" 
    public function missed_callbacks()
    {
        if ($this->input->is_ajax_request()) {
            $filter  = $this->input->post();
            $results = $this->Dashboard_model->missed_callbacks($filter);
            foreach ($results as $k => $row) {
                $results[$k]['time'] = date('g:i a', strtotime($row['nextcall']));
                $results[$k]['date'] = date('jS M', strtotime($row['nextcall']));
            }
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "No callbacks found"
            ));
        }
        
    }
    //this controller displays the upcoming callback data in JSON format. It gets called by the javascript function "upcoming_callbacks_panel" 
    public function upcoming_callbacks()
    {
        if ($this->input->is_ajax_request()) {
           $filter  = $this->input->post();
            $results = $this->Dashboard_model->upcoming_callbacks($filter);
            foreach ($results as $k => $row) {
                $results[$k]['time'] = date('g:i a', strtotime($row['nextcall']));
                $results[$k]['date'] = date('jS M', strtotime($row['nextcall']));
            }
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "No callbacks found"
            ));
        }
        
    }
    //this controller displays the progress data in JSON format. It gets called by the javascript function "progress_panel" 
    public function client_progress()
    {
        if ($this->input->is_ajax_request()) {
          $filter  = $this->input->post();
            $results = $this->Dashboard_model->client_progress($filter);
            foreach ($results as $k => $row) {
                $results[$k]['time'] = date('g:i a', strtotime($row['nextcall']));
                $results[$k]['date'] = date('jS M y', strtotime($row['nextcall']));
            }
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "No records require your attention"
            ));
        }
        
    }
	
	//this controller displays the progress data in JSON format. It gets called by the javascript function "progress_panel" 
    public function nbf_progress()
    {
        if ($this->input->is_ajax_request()) {
        $filter  = $this->input->post();
            $results = $this->Dashboard_model->nbf_progress($filter);
            foreach ($results as $k => $row) {
                $results[$k]['time'] = date('g:i a', strtotime($row['nextcall']));
                $results[$k]['date'] = date('jS M y', strtotime($row['nextcall']));
            }
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "No records require your attention"
            ));
        }
        
    }
    //this controller displays the activity data in JSON format. It gets called by the javascript function "agent_activity" 
    public function agent_activity()
    {
        $this->load->helper('date');
        if ($this->input->is_ajax_request()) {
            $filter  = $this->input->post();
            $results  = $this->Dashboard_model->agent_activity($filter);
            $now      = time();
            foreach ($results as $k => $row) {
                $results[$k]['when']        = timespan(strtotime($row['when']), $now) . " ago";
                $results[$k]['outcome_date'] = timespan(strtotime($row['outcome_date']), $now) . " ago";
            }
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "Nothing found"
            ));
        }
        
    }
    
    //this controller displays the success data in JSON format. It gets called by the javascript function "agent_success" 
    public function agent_success()
    {
        if ($this->input->is_ajax_request()) {
            $filter  = $this->input->post();
            $results  = $this->Dashboard_model->agent_success($filter);
            $now      = time();
            foreach ($results as $k => $row) {
                $results[$k]['rate'] = number_format(($results[$k]['transfers'] / $results[$k]['dials']) * 100, 1) . "%";
            }
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "Nothing found"
            ));
        }
        
    }
    //this controller displays the agent data in JSON format. It gets called by the javascript function "agent_data" 
    public function agent_data()
    {
        if ($this->input->is_ajax_request()) {
              $filter  = $this->input->post();
            $results  = $this->Dashboard_model->agent_data($filter);
            $now      = time();
            foreach ($results as $k => $row) {
                $results[$k]['pc_virgin']      = number_format(($results[$k]['virgin'] / $results[$k]['total']) * 100, 1) . "%";
                $results[$k]['pc_in_progress'] = number_format(($results[$k]['in_progress'] / $results[$k]['total']) * 100, 1) . "%";
                $results[$k]['pc_completed']   = number_format(($results[$k]['completed'] / $results[$k]['total']) * 100, 1) . "%";
            }
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "Nothing found"
            ));
        }
    }
 
    public function all_callbacks()
    {
        if ($this->input->is_ajax_request()) {
              $filter  = $this->input->post();
            $results = $this->Dashboard_model->all_callbacks($filter);
            foreach ($results as $k => $row) {
                $results[$k]['time'] = date('g:i a', strtotime($row['nextcall']));
                $results[$k]['date'] = date('jS M', strtotime($row['nextcall']));
            }
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "No callbacks found"
            ));
        }
    }
	
     //this controller displays the timely callback data in JSON format. It gets called by the javascript function "timely_callbacks_panel" 
    public function timely_callbacks()
    {
        if ($this->input->is_ajax_request()) {
              $filter  = $this->input->post();
            $results = $this->Dashboard_model->timely_callbacks($filter);
            foreach ($results as $k => $row) {
                $results[$k]['time'] = date('g:i a', strtotime($row['nextcall']));
                $results[$k]['date'] = date('jS M', strtotime($row['nextcall']));
            }
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "No callbacks found"
            ));
        }
    }
    
    //this controller displays the current hours data in JSON format. It gets called by the javascript function "agent_current_hours"
    public function agent_current_hours()
    {
    	$results = array();
    	
    	if ($this->input->is_ajax_request()) {
    		$filter  = $this->input->post();
    		$agents = $this->Form_model->get_agents();
    		
    		foreach ($agents as $agent) {
    			if (empty($campaign_form)) {
	    			$campaigns = $this->Form_model->get_campaigns_by_user($agent['id']);
	    			foreach ($campaigns as $campaign) {
	    				$duration = $this->User_model->get_duration_working($campaign['id'],$agent['id']);
	    				if ($duration) {
	    					$results[$agent['name']]['duration'] = $duration->secs;
	    					$results[$agent['name']]['campaign'] = $duration->campaign_name;
	    					
	    					$worked = $this->User_model->get_worked($campaign['id'],$agent['id']);
	    					$results[$agent['name']]['worked'] = $worked;
	    					
	    					$transfers = $this->User_model->get_positive($campaign['id'],$agent['id'], "Transfers");
	    					$cross_transfers = $this->User_model->get_cross_transfers_by_campaign_destination($campaign['id'],$agent['id']);
	    					$results[$agent['name']]['transfers'] = $transfers+$cross_transfers;
	    					
	    					$rate = ($duration->secs > 0)?number_format(($transfers+$cross_transfers)/($duration->secs/60/60),2):0;
	    					$results[$agent['name']]['rate'] = $rate;
	    				}
	    			}
    			}
    			else {
    				$duration = $this->User_model->get_duration_working($filter['campaign'],$agent['id']);
    				if ($duration) {
    					$results[$agent['name']]['duration'] = $duration->secs;
    					$results[$agent['name']]['campaign'] = $duration->campaign_name;
    					
    					$worked = $this->User_model->get_worked($filter['campaign'],$agent['id']);
    					$results[$agent['name']]['worked'] = $worked;
    					
    					$transfers = $this->User_model->get_positive($filter['campaign'],$agent['id'], "Transfers");
    					$cross_transfers = $this->User_model->get_cross_transfers_by_campaign_destination($filter['campaign'],$agent['id']);
    					$results[$agent['name']]['transfers'] = $transfers+$cross_transfers;
    					
    					$rate = ($duration->secs > 0)?number_format(($transfers+$cross_transfers)/($duration->secs/60/60),2):0;
    					$results[$agent['name']]['rate'] = $rate;
    				}
    			}
    		}
    		
    		echo json_encode(array(
    				"success" => (count($results) > 0),
    				"data" => $results,
    				"msg" => "Nothing found"
    		));
    	}
    
    }
    public function get_email_stats(){
		if ($this->input->is_ajax_request()) {
    		$filter = $this->input->post();
			$stats = $this->Dashboard_model->get_email_stats($filter);
			echo json_encode(array("success"=>true,"data"=>$stats));
			exit;
		}
	}
    
}
