<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Dashboard_model');
    }
    
   
    //this laods the user dashboard view  
    public function user_dash()
    {
        $months  = $campaigns = $this->Form_model->get_campaigns();
        $surveys = $this->Form_model->get_surveys();
        
        $data = array(
            'pageId' => 'Dashboard',
            'title' => 'Dashboard',
			'page'=> array('dashboard'=>'overview'),
            'javascript' => array(
                'charts.js',
                'dashboard.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
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
    public function agent()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $surveys   = $this->Form_model->get_surveys();
        
        $data = array(
            'pageId' => 'Dashboard',
            'title' => 'Dashboard',
			'page'=> array('dashboard'=>'agent'),
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
        $this->template->load('default', 'dashboard/agent_dash.php', $data);
    }
    //this is the controller loads the initial view for the client dashboard
    public function client()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $surveys   = $this->Form_model->get_surveys();
        
        $data = array(
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
    
    //this is the controller loads the initial view for the management dashboard
    public function management()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $surveys   = $this->Form_model->get_surveys();
        
        $data = array(
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
            $filter  = $this->input->post('filter');
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
            $filter  = array(
                "range" => $this->input->post('range'),
                "campaign" => $this->input->post('campaign')
            );
            $results = $this->Dashboard_model->get_outcomes($filter);
            foreach ($results as $k => $row) {
                $data[] = array(
                    "outcome" => $row['outcome'],
                    "count" => $row['count']
                );
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
    //this controller sends the system stats data back the page in JSON format. It ran when the javascript function "system_stats" is executed
    public function system_stats()
    {
        if ($this->input->is_ajax_request()) {
            $campaign = intval($this->input->post('campaign'));
            $data     = $this->Dashboard_model->system_stats($campaign);
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
            $comments = intval($this->input->post('comments'));
            $data     = $this->Dashboard_model->get_comments($comments);
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
            $campaign = intval($this->input->post('campaign'));
            $data     = $this->Dashboard_model->get_urgent($campaign);
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
            $campaign = intval($this->input->post('campaign'));
            $data     = $this->Dashboard_model->get_favorites($campaign);
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
            $filter  = array(
                "campaign" => intval($this->input->post('campaign')),
                "user" => intval($this->input->post('user'))
            );
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
            $filter  = array(
                "campaign" => intval($this->input->post('campaign')),
                "user" => intval($this->input->post('user'))
            );
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
            $filter  = array(
                "campaign" => intval($this->input->post('campaign')),
                "user" => intval($this->input->post('user'))
            );
            $results = $this->Dashboard_model->client_progress($filter);
            foreach ($results as $k => $row) {
                $results[$k]['time'] = date('g:i a', strtotime($row['nextcall']));
                $results[$k]['date'] = date('jS M', strtotime($row['nextcall']));
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
            $campaign = intval($this->input->post('campaign'));
            $results  = $this->Dashboard_model->agent_activity($campaign);
            $now      = time();
            foreach ($results as $k => $row) {
                $results[$k]['when']        = timespan(strtotime($row['when']), $now) . " ago";
                $results[$k]['survey_date'] = timespan(strtotime($row['survey_date']), $now) . " ago";
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
            $campaign = intval($this->input->post('campaign'));
            $results  = $this->Dashboard_model->agent_success($campaign);
            $now      = time();
            foreach ($results as $k => $row) {
                $results[$k]['rate'] = number_format(($results[$k]['surveys'] / $results[$k]['dials']) * 100, 1) . "%";
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
            $campaign = intval($this->input->post('campaign'));
            $results  = $this->Dashboard_model->agent_data($campaign);
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
    
    
    
    
}
