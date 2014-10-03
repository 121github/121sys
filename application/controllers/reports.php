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
            'title' => 'Reports | Targets',
			'page'=> array('reports'=>'targets'),
            'javascript' => array(
                'charts.js',
                'report/targets.js',
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
            'title' => 'Reports | Answers',
			'page'=> array('reports'=>'answers'),
            'javascript' => array(
                'charts.js',
                'report/answers.js'
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
            'title' => 'Reports | Activity',
			'page'=> array('reports'=>'activity'),
            'javascript' => array(
                'charts.js',
                'report/activity.js',
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
    
    
    //this is the controller loads the initial view for the campaign transfer report dashboard
    public function campaigntransfer()
    {
    
    	$campaigns = $this->Form_model->get_user_campaigns();
    	$teamManagers   = $this->Form_model->get_team_managers();
    	$sources = $this->Form_model->get_sources();
    
    	$data = array(
    			'pageId' => 'Reports',
    			'title' => 'Reports | Campaign Transfer',
    			'page'=> array('reports'=>'campaign', 'inner' => 'campaigntransfer'),
    			'javascript' => array(
    					'charts.js',
    					'report/campaigntransfer.js',
    					'lib/moment.js',
    					'lib/daterangepicker.js'
    			),
    			'campaigns' => $campaigns,
    			'sources' => $sources,
    			'team_managers' => $teamManagers,
    			'css' => array(
    					'dashboard.css',
    					'daterangepicker-bs3.css'
    			)
    	);
    	$this->template->load('default', 'reports/campaigntransfer.php', $data);
    }
    
    //this controller sends the campaign transfer report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function campaigntransfer_data()
    {
    	if ($this->input->is_ajax_request()) {
    		$data= array();
    		$results = $this->Report_model->get_campaign_report_by_outcome($this->input->post(), array("Transfer", "Cross Transfer"));
    			
    		$aux = array();
    		
    		foreach ($results as $row) {
    			if ($row['outcome'] == 'Transfer') {
    				$aux[$row['date']]['transfers'] = $row['count'];
    			}
    			elseif ($row['outcome'] == 'Cross Transfer') {
    				$aux[$row['date']]['cross_transfers'] = $row['count'];
    			}
    		}
    		
    		$totalTransfers = 0;
    		$totalCrossTransfers = 0;
    		foreach ($aux as $date => $row) {
    			$transfers = (array_key_exists('transfers', $row))?$row['transfers']:0;
    			$crossTransfers = (array_key_exists('cross_transfers', $row))?$row['cross_transfers']:0;
    			$data[] = array(
    					"date" => $date,
    					"transfers" => $transfers,
    					"cross_transfers" => $crossTransfers,
    					"total_transfers"=>$transfers + $crossTransfers,
    					"duration" => 0,
    					"rate" => 0
    			);
    			$totalTransfers += $transfers;
    			$totalCrossTransfers += $crossTransfers;
    		}
    		array_push($data, array(
    			"date" => "TOTAL",
    			"transfers" => $totalTransfers,
    			"cross_transfers" => $totalCrossTransfers,
    			"total_transfers"=>$totalTransfers + $totalCrossTransfers,
    			"duration" => 0,
    			"rate" => 0
    		));
    		
    		echo json_encode(array(
    				"success" => true,
    				"data" => $data
    		));
    	}
    }
    
    //this is the controller loads the initial view for the campaign appointment report dashboard
    public function campaignappointment()
    {
    
    	$campaigns = $this->Form_model->get_user_campaigns();
    	$teamManagers   = $this->Form_model->get_team_managers();
    	$sources = $this->Form_model->get_sources();
    
    	$data = array(
    			'pageId' => 'Reports',
    			'title' => 'Reports | Campaign Appointment',
    			'page'=> array('reports'=>'campaign', 'inner' => 'campaignappointment'),
    			'javascript' => array(
    					'charts.js',
    					'report/campaignappointment.js',
    					'lib/moment.js',
    					'lib/daterangepicker.js'
    			),
    			'campaigns' => $campaigns,
    			'sources' => $sources,
    			'team_managers' => $teamManagers,
    			'css' => array(
    					'dashboard.css',
    					'daterangepicker-bs3.css'
    			)
    	);
    	$this->template->load('default', 'reports/campaignappointment.php', $data);
    }
    
    //this controller sends the campaign appointment report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function campaignappointment_data()
    {
    	if ($this->input->is_ajax_request()) {
    		$data= array();
    		$results = $this->Report_model->get_campaign_report_by_outcome($this->input->post(), array("Appointment"));
    		 
    		$aux = array();
    
    		foreach ($results as $row) {
    			$aux[$row['date']]['appointments'] = $row['count'];
    		}
    
    		$totalAppointments = 0;
    		foreach ($aux as $date => $row) {
    			$appointments = (array_key_exists('appointments', $row))?$row['appointments']:0;
    			$data[] = array(
    					"date" => $date,
    					"appointments" => $appointments,
    					"duration" => 0,
    					"rate" => 0
    			);
    			$totalAppointments += $appointments;
    		}
    		array_push($data, array(
    		"date" => "TOTAL",
    		"appointments" => $totalAppointments,
    		"duration" => 0,
    		"rate" => 0
    		));
    
    		echo json_encode(array(
    				"success" => true,
    				"data" => $data
    		));
    	}
    }
    
    //this is the controller loads the initial view for the campaign survey report dashboard
    public function campaignsurvey()
    {
    
    	$campaigns = $this->Form_model->get_user_campaigns();
    	$teamManagers   = $this->Form_model->get_team_managers();
    	$sources = $this->Form_model->get_sources();
    
    	$data = array(
    			'pageId' => 'Reports',
    			'title' => 'Reports | Campaign Survey',
    			'page'=> array('reports'=>'campaign', 'inner' => 'campaignsurvey'),
    			'javascript' => array(
    					'charts.js',
    					'report/campaignsurvey.js',
    					'lib/moment.js',
    					'lib/daterangepicker.js'
    			),
    			'campaigns' => $campaigns,
    			'sources' => $sources,
    			'team_managers' => $teamManagers,
    			'css' => array(
    					'dashboard.css',
    					'daterangepicker-bs3.css'
    			)
    	);
    	$this->template->load('default', 'reports/campaignsurvey.php', $data);
    }
    
    //this controller sends the campaign survey report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function campaignsurvey_data()
    {
    	if ($this->input->is_ajax_request()) {
    		$data= array();
    		$results = $this->Report_model->get_campaign_report_by_outcome($this->input->post(), array("Survey"));
    		 
    		$aux = array();
    
    		foreach ($results as $row) {
    			$aux[$row['date']]['surveys'] = $row['count'];
    		}
    
    		$totalSurveys = 0;
    		foreach ($aux as $date => $row) {
    			$surveys = (array_key_exists('surveys', $row))?$row['surveys']:0;
    			$data[] = array(
    					"date" => $date,
    					"surveys" => $surveys,
    					"duration" => 0,
    					"rate" => 0
    			);
    			$totalSurveys += $surveys;
    		}
    		array_push($data, array(
    		"date" => "TOTAL",
    		"surveys" => $totalSurveys,
    		"duration" => 0,
    		"rate" => 0
    		));
    
    		echo json_encode(array(
    				"success" => true,
    				"data" => $data
    		));
    	}
    }
    
    //this is the controller loads the initial view for the individual report dashboard
    public function individual()
    {
    
    	$campaigns = $this->Form_model->get_user_campaigns();
    	$agents   = $this->Form_model->get_agents();
    	$teamManagers   = $this->Form_model->get_team_managers();
    
    	$data = array(
    			'pageId' => 'Reports',
    			'title' => 'Reports | Individual',
    			'page'=> array('reports'=>'individual'),
    			'javascript' => array(
    					'charts.js',
    					'report/individual.js',
    					'lib/moment.js',
    					'lib/daterangepicker.js'
    			),
    			'campaigns' => $campaigns,
    			'agents' => $agents,
    			'team_managers' => $teamManagers,
    			'css' => array(
    					'dashboard.css',
    					'daterangepicker-bs3.css'
    			)
    	);
    	$this->template->load('default', 'reports/individual.php', $data);
    }
    
    //this controller sends the individual report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function individual_data()
    {
    	if ($this->input->is_ajax_request()) {
    		$data= array();
    		$results = $this->Report_model->get_individual_report($this->input->post());
    		 
    		$aux = array();
    
    		foreach ($results as $row) {
    			$aux[$row['agent']]['name'] = $row['name'];
    			if ($row['outcome'] == 'Transfer') {
    				$aux[$row['agent']]['transfers'] = $row['count'];
    			}
    			elseif ($row['outcome'] == 'Cross Transfer') {
    				$aux[$row['agent']]['cross_transfers'] = $row['count'];
    			}
    		}
    
    		$totalTransfers = 0;
    		$totalCrossTransfers = 0;
    		foreach ($aux as $agent => $row) {
    			$transfers = (array_key_exists('transfers', $row))?$row['transfers']:0;
    			$crossTransfers = (array_key_exists('cross_transfers', $row))?$row['cross_transfers']:0;
    			$data[] = array(
    					"agent" => $agent,
    					"name" => $row['name'],
    					"transfers" => $transfers,
    					"cross_transfers" => $crossTransfers,
    					"total_transfers"=>$transfers + $crossTransfers,
    					"duration" => 0,
    					"rate" => 0
    			);
    			$totalTransfers += $transfers;
    			$totalCrossTransfers += $crossTransfers;
    		}
    		array_push($data, array(
    		"agent" => "TOTAL",
    		"name" => "",
    		"transfers" => $totalTransfers,
    		"cross_transfers" => $totalCrossTransfers,
    		"total_transfers"=>$totalTransfers + $totalCrossTransfers,
    		"duration" => 0,
    		"rate" => 0
    		));
    
    		echo json_encode(array(
    				"success" => true,
    				"data" => $data
    		));
    	}
    }
    
    //this is the controller loads the initial view for the individual daily comparision report dashboard
    public function individualdaily()
    {
    
    	$campaigns = $this->Form_model->get_user_campaigns();
    	$teamManagers   = $this->Form_model->get_team_managers();
    	$agents   = $this->Form_model->get_agents();
    	$sources = $this->Form_model->get_sources();
    
    	$data = array(
    			'pageId' => 'Reports',
    			'title' => 'Reports | Individual Daily Comparison',
    			'page'=> array('reports'=>'individualdaily'),
    			'javascript' => array(
    					'charts.js',
    					'report/individual-daily.js',
    					'lib/moment.js',
    					'lib/daterangepicker.js'
    			),
    			'campaigns' => $campaigns,
    			'sources' => $sources,
    			'agents' => $agents,
    			'team_managers' => $teamManagers,
    			'css' => array(
    					'dashboard.css',
    					'daterangepicker-bs3.css'
    			)
    	);
    	$this->template->load('default', 'reports/individualdaily.php', $data);
    }
    
    //this controller sends the individual daily comparision report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function individualdaily_data()
    {
    if ($this->input->is_ajax_request()) {
    		$data= array();
    		$results = $this->Report_model->get_individualdaily_report($this->input->post());
    		
    		$agent = $this->input->post("agent");
    		$name = "All Users";
    		
    		$aux = array();
    		foreach ($results as $row) {
    			if (!empty($agent)) {
    				$name = $row['name'];
    			}
    			if ($row['outcome'] == 'Transfer') {
    				$aux[$row['date']]['transfers'] = $row['count'];
    			}
    			elseif ($row['outcome'] == 'Cross Transfer') {
    				$aux[$row['date']]['cross_transfers'] = $row['count'];
    			}
    		}
    		
    		$totalTransfers = 0;
    		$totalCrossTransfers = 0;
    		foreach ($aux as $date => $row) {
    			$transfers = (array_key_exists('transfers', $row))?$row['transfers']:0;
    			$crossTransfers = (array_key_exists('cross_transfers', $row))?$row['cross_transfers']:0;
    			$data[] = array(
    					"date" => $date,
    					"name" => $name,
    					"transfers" => $transfers,
    					"cross_transfers" => $crossTransfers,
    					"total_transfers"=>$transfers + $crossTransfers,
    					"duration" => 0,
    					"rate" => 0
    			);
    			$totalTransfers += $transfers;
    			$totalCrossTransfers += $crossTransfers;
    		}
    		array_push($data, array(
    			"date" => "TOTAL",
    			"name" => "",
    			"transfers" => $totalTransfers,
    			"cross_transfers" => $totalCrossTransfers,
    			"total_transfers"=>$totalTransfers + $totalCrossTransfers,
    			"duration" => 0,
    			"rate" => 0
    		));
    		
    		echo json_encode(array(
    				"success" => true,
    				"data" => $data
    		));
    	}
    }
    
    //this is the controller loads the initial view for the agent dials report dashboard
    public function agentdials()
    {
    
    	$campaigns = $this->Form_model->get_user_campaigns();
    	$teamManagers   = $this->Form_model->get_team_managers();
    	$agents   = $this->Form_model->get_agents();
    	$sources = $this->Form_model->get_sources();
    
    	$data = array(
    			'pageId' => 'Reports',
    			'title' => 'Reports | Agent Dials',
    			'page'=> array('reports'=>'agentdials'),
    			'javascript' => array(
    					'charts.js',
    					'report/agentdials.js',
    					'lib/moment.js',
    					'lib/daterangepicker.js'
    			),
    			'campaigns' => $campaigns,
    			'sources' => $sources,
    			'team_managers' => $teamManagers,
    			'agents' => $agents,
    			'css' => array(
    					'dashboard.css',
    					'daterangepicker-bs3.css'
    			)
    	);
    	$this->template->load('default', 'reports/agentdials.php', $data);
    }
    
    //this controller sends the agent dials report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function agentdials_data()
    {
    	if ($this->input->is_ajax_request()) {
    		$data= array();
    		$results = $this->Report_model->get_agentdials_report($this->input->post());
    		

    		$aux = array();
    
    		foreach ($results as $row) {
    			
    			$aux[$row['advisor']]['name'] = $row['name'];
    			$aux[$row['advisor']]['total'] = $row['count'];
    		}
    
    		foreach ($aux as $advisor => $row) {
    			$data[] = array(
    					"advisor" => $advisor,
    					"name" => $row['name'],
    					"total"=>$row['total'],
    			);
    		}
    
    		echo json_encode(array(
    				"success" => true,
    				"data" => $data,
    		));
    	}
    }
    
    //this is the controller loads the initial view for the campaign dials report dashboard
    public function campaigndials()
    {
    
    	$campaigns = $this->Form_model->get_user_campaigns();
    	$teamManagers   = $this->Form_model->get_team_managers();
    	$agents   = $this->Form_model->get_agents();
    	$sources = $this->Form_model->get_sources();
    
    	$data = array(
    			'pageId' => 'Reports',
    			'title' => 'Reports | Campaign Dials',
    			'page'=> array('reports'=>'campaigndials'),
    			'javascript' => array(
    					'charts.js',
    					'report/campaigndials.js',
    					'lib/moment.js',
    					'lib/daterangepicker.js'
    			),
    			'campaigns' => $campaigns,
    			'sources' => $sources,
    			'team_managers' => $teamManagers,
    			'agents' => $agents,
    			'css' => array(
    					'dashboard.css',
    					'daterangepicker-bs3.css'
    			)
    	);
    	$this->template->load('default', 'reports/campaigndials.php', $data);
    }
    
    //this controller sends the campaign dials report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function campaigndials_data()
    {
    	if ($this->input->is_ajax_request()) {
    		$data= array();
    		$results = $this->Report_model->get_campaigndials_report($this->input->post());
    
    
    		$aux = array();
    
    		foreach ($results as $row) {
    			$data[] = array(
    					"name" => $row['name'],
    					"total"=>$row['count'],
    			);
    		}
    
    
    		echo json_encode(array(
    				"success" => true,
    				"data" => $data,
    		));
    	}
    }
}
