<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
		$this->load->model('Report_model');
    }
 //this controller loads the view for the targets page on the dashboard
    public function targets()
    {
       
        $data = array(
            'pageId' => 'Reports',
            'title' => 'Reports',
			'page'=> array('reports'=>'targets'),
            'javascript' => array(
                'charts.js',
                'targets.js',
				'lib/moment.js',
				'lib/daterangepicker.js'
				
            ),
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
				'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'reports/targets.php', $data);
    }
	
	 //this controller displays the targets data in JSON format. It gets called by the javascript function "targets_panel" 
    public function target_data()
    {
		//under contruction. Customise targets, select campaigns, date ranges & outcomes etc.
    }

//this controller loads the view for the answer page on the dashboard
    public function answers()
    {
        $surveys = $this->Form_model->get_surveys();
        $results = $this->Report_model->all_answers_data();
        $data = array(
            'pageId' => 'Reports',
            'title' => 'Reports',
			'page'=> array('reports'=>'answers'),
            'javascript' => array(
                'charts.js',
                'answers.js'
            ),
            'surveys' => $surveys,
            'answers' => $results,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css'
            )
        );
        $this->template->load('default', 'reports/answers.php', $data);
    }

 //this controller displays the answers data in JSON format. It gets called by the javascript function "answers_panel" 
    public function answers_data()
    {
        if ($this->input->is_ajax_request()) {
            $survey  = intval($this->input->post('survey'));
            $results = $this->Report_model->answers_data($survey);
			foreach($results as $k=>$v){
				//create the url for the click throughs
				$perfects = base_url()."search/custom/records/question/".$v['question_id']."/survey/".$v['survey_info_id']."/score/10";
				$lows = base_url()."search/custom/records/question/".$v['question_id']."/survey/".$v['survey_info_id']."/score/4:less";
				$results[$k]['perfects'] = $perfects;
				$results[$k]['lows'] = $lows;
			}
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "Nothing found"
            ));
            exit;
        }
    }

  //this is the controller loads the initial view for the activity dashboard
    public function activity()
    {
        
        $campaigns = $this->Form_model->get_user_campaigns();
        $surveys   = $this->Form_model->get_surveys();
		$agents   = $this->Form_model->get_agents();
        
        $data = array(
            'pageId' => 'Reports',
            'title' => 'Reports',
			'page'=> array('reports'=>'activity'),
            'javascript' => array(
                'charts.js',
                'activity.js',
				'lib/moment.js',
				'lib/daterangepicker.js'
            ),
            'campaigns' => $campaigns,
            'surveys' => $surveys,
			'agents' => $agents,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
				'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'reports/activity.php', $data);
    }    

//this controller sends the activity data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function activity_data()
    {
        if ($this->input->is_ajax_request()) {
			$data= array();
			$total=0;
            $results = $this->Report_model->get_activity($this->input->post());
			
			$date_from = $this->input->post("date_from");
			$date_to = $this->input->post("date_to");
			$user = $this->input->post("agent");
			$campaign = $this->input->post("campaign");
			
            foreach ($results as $k => $row) {
				$url = base_url()."search/custom/history";
				$url .= (!empty($campaign)?"/campaign/$campaign":"");
				$url .= (!empty($user)?"/user/$user":"");
				$url .= (!empty($date_from)?"/contact/$date_from:emore":"");
				$url .= (!empty($date_to)?"/contact/$date_to:eless":"");
				
                $total  = $row['total'];
				$pc = (isset($row['total'])?number_format(($row['count'] / $row['total']) * 100, 1):"-");
                $data[] = array(
                    "outcome" => $row['outcome'],
                    "count" => $row['count'],
                    "pc" => $pc,
					"url"=>$url."/outcome/".$row['outcome']
                );
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data,
                "total" => $total
            ));
        }
    }

}
