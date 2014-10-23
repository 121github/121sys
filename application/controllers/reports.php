<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Report_model');
        $this->_campaigns = campaign_access_dropdown();
    }
    //this controller loads the view for the targets page on the dashboard
    public function targets()
    {
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Targets',
            'page' => array(
                'reports' => 'targets'
            ),
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
        $data    = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Answers',
            'page' => array(
                'reports' => 'answers'
            ),
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
            foreach ($results as $k => $v) {
                //create the url for the click throughs
                $perfects                = base_url() . "search/custom/records/question/" . $v['question_id'] . "/survey/" . $v['survey_info_id'] . "/score/10";
                $lows                    = base_url() . "search/custom/records/question/" . $v['question_id'] . "/survey/" . $v['survey_info_id'] . "/score/4:less";
                $results[$k]['perfects'] = $perfects;
                $results[$k]['lows']     = $lows;
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
        $campaigns    = $this->Form_model->get_user_campaigns();
        $agents       = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        $sources      = $this->Form_model->get_sources();
		
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Activity',
            'page' => array(
                'reports' => 'activity'
            ),
            'javascript' => array(
                'charts.js',
                'report/activity.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
            'campaigns' => $campaigns,
            'team_managers' => $teamManagers,
            'agents' => $agents,
			'sources' => $sources,
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
            $data    = array();
			$data_array = array();
            $total   = 0;
            $results = $this->Report_model->get_activity($this->input->post());
            $date_from = $this->input->post("date_from");
            $date_to   = $this->input->post("date_to");
            $user      = $this->input->post("agent");
            $campaign  = $this->input->post("campaign");
			$team  = $this->input->post("team");
			$source  = $this->input->post("source");
            
			
			
			$overall_array = array();
			$post = $this->input->post();
			if($this->input->post('team')||$this->input->post('agent')){
			$colname = $this->input->post('colname');
			unset($post['team']);
			unset($post['agent']);
			$overall = $this->Report_model->get_activity($post);
			$overall_array = array();
			foreach ($overall as $k => $row) {
			$overall_array[$row['outcome']]["overall_total"]  = $row['total'];
			$overall_array[$row['outcome']]["overall"]  = (isset($row['total']) ? number_format(($row['count'] / $row['total']) * 100, 1) : "-");
			}
			}
			
            foreach ($results as $k => $row) {
                $url = base_url() . "search/custom/history";
                $url .= (!empty($campaign) ? "/campaign/$campaign" : "");
                $url .= (!empty($user) ? "/user/$user" : "");
                $url .= (!empty($date_from) ? "/contact/$date_from:emore" : "");
                $url .= (!empty($date_to) ? "/contact/$date_to:eless" : "");
			    $url .= (!empty($team) ? "/team/$team" : "");
                $url .= (!empty($source) ? "/source/$source" : "");
                $url .= (!empty($user) ? "/user/$user" : "");
				
                $total  = $row['total'];
                $pc     = (isset($row['total']) ? number_format(($row['count'] / $row['total']) * 100, 1) : "-");
                $data = array(
                    "outcome" => $row['outcome'],
                    "count" => $row['count'],
                    "pc" => $pc,
                    "url" => $url . "/outcome/" . $row['outcome']
                );
				if(isset($overall_array[$row['outcome']]["overall"])){
					$data["overall"] = $overall_array[$row['outcome']]["overall"];
					$data["colname"] = $colname;
				}
				$data_array[] = $data;
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data_array,
                "total" => $total
            ));
        }
    }
    
    
    //this is the controller loads the initial view for the campaign transfer report dashboard
    public function campaigntransfer()
    {
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $teamManagers = $this->Form_model->get_teams();
        $sources      = $this->Form_model->get_sources();
		$agents       = $this->Form_model->get_agents();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Campaign Transfer',
            'page' => array(
                'reports' => 'campaign',
                'inner' => 'campaigntransfer'
            ),
            'javascript' => array(
                'charts.js',
                'report/campaigntransfer.js',
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
        $this->template->load('default', 'reports/campaigntransfer.php', $data);
    }
    
    //this controller sends the campaign transfer report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function campaigntransfer_data()
    {
        if ($this->input->is_ajax_request()) {
            $data    = array();
            $results = $this->Report_model->get_campaign_report_by_outcome($this->input->post(),"transfers");
            
            $campaign_search  = $this->input->post("campaign");
            
            $aux = array();
            
            foreach ($results as $row) {
                if ($row['outcome'] == 'Transfer') {
                	$aux[$row['campaign']]['name'] = $row['name'];
                	$aux[$row['campaign']]['duration'] = $row['duration'];
                    $aux[$row['campaign']]['transfers'] = $row['count'];
                    $aux[$row['campaign']]['total_dials'] = (isset($aux[$row['campaign']]['total_dials'])) ? $aux[$row['campaign']]['total_dials'] + $row['count'] : $row['count'];
                } elseif ($row['outcome'] == 'Cross Transfer') {
                	$campaign_transfer = $this->Report_model->get_campaign_by_id($row['campaign_id']);
                	$aux[$row['campaign_id']]['name'] = $campaign_transfer[0]['campaign_name'];
                	$aux[$row['campaign']]['duration'] = $row['duration'];
                    $aux[$row['campaign_id']]['cross_transfers'] = $row['count'];
                    $aux[$row['campaign_id']]['total_dials'] = (isset($aux[$row['campaign']]['total_dials'])) ? $aux[$row['campaign']]['total_dials'] + $row['count'] : $row['count'];
                }
                else {
                	$aux[$row['campaign']]['name'] = $row['name'];
                	$aux[$row['campaign']]['duration'] = $row['duration'];
                	$aux[$row['campaign']]['total_dials'] = (isset($aux[$row['campaign']]['total_dials'])) ? $aux[$row['campaign']]['total_dials'] + $row['count'] : $row['count'];
                }
                
            }
            
            $totalTransfers      = 0;
            $totalCrossTransfers = 0;
            $totalDials          = 0;
            $totalDuration		 = 0;
            foreach ($aux as $campaign => $row) {
            	$transfers      = (array_key_exists('transfers', $row)) ? $row['transfers'] : 0;
	            $crossTransfers = (array_key_exists('cross_transfers', $row)) ? $row['cross_transfers'] : 0;
	            $data[]         = array(
                    "campaign" => $campaign,
                    "name" => $row['name'],
                    "transfers" => $transfers,
                    "cross_transfers" => $crossTransfers,
                    "total_transfers" => $transfers + $crossTransfers,
                    "total_dials" => $row['total_dials'],
	            	"duration" => ($row['duration'])?$row['duration']:0,
	            	"rate" => ($row['duration']>0)?round(($transfers + $crossTransfers)/($row['duration']/3600),3):0
                );
                $totalTransfers += $transfers;
                $totalCrossTransfers += $crossTransfers;
                $totalDials += $row['total_dials'];
                $totalDuration += $row['duration'];
            }
            
            $totalTransfersPercent      = number_format(($totalTransfers * 100) / $totalDials, 2) . '%';
            $totalCrossTransfersPercent = number_format(($totalCrossTransfers * 100) / $totalDials, 2) . '%';
            $totalPercent               = number_format((($totalTransfers + $totalCrossTransfers) * 100) / $totalDials, 2) . '%';
            
            array_push($data, array(
                "campaign" => "TOTAL",
                "name" => "",
                "transfers" => $totalTransfers . " (" . $totalTransfersPercent . ")",
                "cross_transfers" => $totalCrossTransfers . " (" . $totalCrossTransfersPercent . ")",
                "total_transfers" => ($totalTransfers + $totalCrossTransfers) . " (" . $totalPercent . ")",
                "total_dials" => $totalDials,
                "duration" => $totalDuration,
                "rate" => ($row['duration']>0)?round(($totalTransfers + $totalCrossTransfers)/($totalDuration/3600),3):0
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
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $teamManagers = $this->Form_model->get_teams();
        $sources      = $this->Form_model->get_sources();
		$agents       = $this->Form_model->get_agents();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Campaign Appointment',
            'page' => array(
                'reports' => 'campaign',
                'inner' => 'campaignappointment'
            ),
            'javascript' => array(
                'charts.js',
                'report/campaignappointment.js',
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
        $this->template->load('default', 'reports/campaignappointment.php', $data);
    }
    
    //this controller sends the campaign appointment report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function campaignappointment_data()
    {
        if ($this->input->is_ajax_request()) {
            $data    = array();
            $results = $this->Report_model->get_campaign_report_by_outcome($this->input->post(),"appointments");
            
            $aux = array();
            
            foreach ($results as $row) {
                if ($row['outcome'] == 'Appointment') {
                	$aux[$row['campaign']]['name'] = $row['name'];
                	$aux[$row['campaign']]['duration'] = $row['duration'];
                    $aux[$row['campaign']]['appointments'] = $row['count'];
                    $aux[$row['campaign']]['total_dials'] = (isset($aux[$row['campaign']]['total_dials'])) ? $aux[$row['campaign']]['total_dials'] + $row['count'] : $row['count'];
                }
                elseif ($row['outcome'] != 'Cross Transfer') {
                	$aux[$row['campaign']]['name'] = $row['name'];
                	$aux[$row['campaign']]['duration'] = $row['duration'];
                	$aux[$row['campaign']]['total_dials'] = (isset($aux[$row['campaign']]['total_dials'])) ? $aux[$row['campaign']]['total_dials'] + $row['count'] : $row['count'];
                }
            }
            
            $totalAppointments = 0;
            $totalDials        = 0;
            $totalDuration		 = 0;
            foreach ($aux as $campaign => $row) {
                $appointments = (array_key_exists('appointments', $row)) ? $row['appointments'] : 0;
                $data[]       = array(
                    "campaign" => $campaign,
                    "name" => $row['name'],
                    "appointments" => $appointments,
                    "total_dials" => $row['total_dials'],
                	"duration" => ($row['duration'])?$row['duration']:0,
                	"rate" => ($row['duration']>0)?round($appointments/($row['duration']/3600),3):0
                );
                $totalAppointments += $appointments;
                $totalDials += $row['total_dials'];
                $totalDuration += $row['duration'];
            }
            
            $totalAppointmentsPercent = ($totalDials>0)?number_format(($totalAppointments * 100) / $totalDials, 2) . '%':0;
            
            array_push($data, array(
                "campaign" => "TOTAL",
                "name" => "",
                "appointments" => $totalAppointments . " (" . $totalAppointmentsPercent . ")",
                "total_dials" => $totalDials,
                "duration" => $totalDuration,
                "rate" => ($totalDuration>0)?round($totalAppointments/($totalDuration/3600),3):0
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
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $teamManagers = $this->Form_model->get_teams();
        $sources      = $this->Form_model->get_sources();
        $agents       = $this->Form_model->get_agents();
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Campaign Survey',
            'page' => array(
                'reports' => 'campaign',
                'inner' => 'campaignsurvey'
            ),
            'javascript' => array(
                'charts.js',
                'report/campaignsurvey.js',
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
        $this->template->load('default', 'reports/campaignsurvey.php', $data);
    }
    
    //this controller sends the campaign survey report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function campaignsurvey_data()
    {
        if ($this->input->is_ajax_request()) {
            $data    = array();
            $results = $this->Report_model->get_campaign_report_by_outcome($this->input->post(),"surveys");
            
            $aux = array();
            
            foreach ($results as $row) {
                if ($row['outcome'] == 'Survey Complete') {
                	$aux[$row['campaign']]['name'] = $row['name'];
                	$aux[$row['campaign']]['duration'] = $row['duration'];
                    $aux[$row['campaign']]['complete_surveys'] = $row['count'];
                    $aux[$row['campaign']]['total_dials'] = (isset($aux[$row['campaign']]['total_dials'])) ? $aux[$row['campaign']]['total_dials'] + $row['count'] : $row['count'];
                } 
            }
            
            $totalCompleteSurveys = 0;
            $totalRefusedSurveys  = 0;
            $totalDials           = 0;
            $totalDuration		 = 0;
            foreach ($aux as $campaign => $row) {
                $completeSurveys = (array_key_exists('complete_surveys', $row)) ? $row['complete_surveys'] : 0;
                $data[]          = array(
                    "campaign" => $campaign,
                    "name" => $row['name'],
                    "complete_surveys" => $completeSurveys,
                    "total_dials" => $row['total_dials'],
                	"duration" => ($row['duration'])?$row['duration']:0,
                	"rate" => ($row['duration']>0)?round(($completeSurveys)/($row['duration']/3600),3):0
                );
                $totalCompleteSurveys += $completeSurveys;
                $totalDials += $row['total_dials'];
                $totalDuration += $row['duration'];
            }
            
            $totalCompleteSurveysPercent = ($totalDials>0)?number_format(($totalCompleteSurveys * 100) / $totalDials, 2) . '%':0;
            
            array_push($data, array(
                "campaign" => "TOTAL",
                "name" => "",
                "complete_surveys" => $totalCompleteSurveys . " (" . $totalCompleteSurveysPercent . ")",
                "total_dials" => $totalDials,
                "duration" => $totalDuration,
                "rate" => ($totalDuration>0)?round(($totalCompleteSurveys)/($totalDuration/3600),3):0
            ));
            
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
    //this is the controller loads the initial view for the campaign dials report dashboard
    public function campaigndials()
    {
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $teamManagers = $this->Form_model->get_teams();
        $agents       = $this->Form_model->get_agents();
        $sources      = $this->Form_model->get_sources();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Campaign Dials',
            'page' => array(
                'reports' => 'campaign',
                'inner' => 'campaigndials'
            ),
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
            $data    = array();
            $results = $this->Report_model->get_campaigndials_report($this->input->post());
            
            
            $aux = array();
            
            foreach ($results as $row) {
                $data[] = array(
                    "name" => $row['name'],
                    "total" => $row['count']
                );
            }
            
            
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
    //this is the controller loads the initial view for the agenttransfer report dashboard
    public function agenttransfer()
    {
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $agents       = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Agent Transfers',
            'page' => array(
                'reports' => 'agent',
                'inner' => 'agenttransfer'
            ),
            'javascript' => array(
                'charts.js',
                'report/agenttransfer.js',
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
        $this->template->load('default', 'reports/agenttransfer.php', $data);
    }
    
    //this controller sends the agenttransfer report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function agenttransfer_data()
    {
        if ($this->input->is_ajax_request()) {
            $data    = array();
            $results = $this->Report_model->get_agent_report_by_outcome($this->input->post());
            
            $aux = array();
            
            foreach ($results as $row) {
                $aux[$row['agent']]['name'] = $row['name'];
                $aux[$row['agent']]['duration'] = $row['duration'];
                if ($row['outcome'] == 'Transfer') {
                    $aux[$row['agent']]['transfers'] = $row['count'];
                } elseif ($row['outcome'] == 'Cross Transfer') {
                    $aux[$row['agent']]['cross_transfers'] = $row['count'];
                }
                $aux[$row['agent']]['total_dials'] = (isset($aux[$row['agent']]['total_dials'])) ? $aux[$row['agent']]['total_dials'] + $row['count'] : $row['count'];
            }
            
            $totalTransfers      = 0;
            $totalCrossTransfers = 0;
            $totalDials          = (count($aux) == 0)?1:0;
            $totalDuration		 = 0;
            foreach ($aux as $agent => $row) {
                $transfers      = (array_key_exists('transfers', $row)) ? $row['transfers'] : 0;
                $crossTransfers = (array_key_exists('cross_transfers', $row)) ? $row['cross_transfers'] : 0;
                $data[]         = array(
                    "agent" => $agent,
                    "name" => $row['name'],
                    "transfers" => $transfers,
                    "cross_transfers" => $crossTransfers,
                    "total_transfers" => $transfers + $crossTransfers,
                    "total_dials" => $row['total_dials'],
                	"duration" => ($row['duration'])?$row['duration']:0,
                	"rate" => ($row['duration']>0)?round(($transfers + $crossTransfers)/($row['duration']/3600),3):0
                );
                $totalTransfers += $transfers;
                $totalCrossTransfers += $crossTransfers;
                $totalDials += $row['total_dials'];
                $totalDuration += $row['duration'];
            }
            
            $totalTransfersPercent      = number_format(($totalTransfers * 100) / $totalDials, 2) . '%';
            $totalCrossTransfersPercent = number_format(($totalCrossTransfers * 100) / $totalDials, 2) . '%';
            $totalPercent               = number_format((($totalTransfers + $totalCrossTransfers) * 100) / $totalDials, 2) . '%';
            
            array_push($data, array(
                "agent" => "TOTAL",
                "name" => "",
                "transfers" => $totalTransfers . " (" . $totalTransfersPercent . ")",
                "cross_transfers" => $totalCrossTransfers . " (" . $totalCrossTransfersPercent . ")",
                "total_transfers" => ($totalTransfers + $totalCrossTransfers) . " (" . $totalPercent . ")",
                "total_dials" => (count($results) > 0)?$totalDials:0,
                "duration" => $totalDuration,
                "rate" => round(($totalTransfers + $totalCrossTransfers)/($totalDuration/3600),3)
            ));
            
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
    //this is the controller loads the initial view for the agentappointment report dashboard
    public function agentappointment()
    {
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $agents       = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Agent Appointments',
            'page' => array(
                'reports' => 'agent',
                'inner' => 'agenttransfer'
            ),
            'javascript' => array(
                'charts.js',
                'report/agentappointment.js',
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
        $this->template->load('default', 'reports/agentappointment.php', $data);
    }
    
    //this controller sends the agentappointment report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function agentappointment_data()
    {
        if ($this->input->is_ajax_request()) {
            $data    = array();
            $results = $this->Report_model->get_agent_report_by_outcome($this->input->post());
            
            $aux = array();
            
            foreach ($results as $row) {
                if ($row['outcome'] == 'Appointment') {
                	$aux[$row['agent']]['name'] = $row['name'];
                	$aux[$row['agent']]['duration'] = $row['duration'];
                    $aux[$row['agent']]['appointments'] = $row['count'];
                    $aux[$row['agent']]['total_dials'] = (isset($aux[$row['agent']]['total_dials'])) ? $aux[$row['agent']]['total_dials'] + $row['count'] : $row['count'];
                }
                elseif ($row['outcome'] != 'Cross Transfer') {
                	$aux[$row['agent']]['name'] = $row['name'];
                	$aux[$row['agent']]['total_dials'] = (isset($aux[$row['agent']]['total_dials'])) ? $aux[$row['agent']]['total_dials'] + $row['count'] : $row['count'];
                }
                
            }
            
            $totalAppointments = 0;
            $totalDials          = (count($aux) == 0)?1:0; 
            $totalDuration		 = 0;
            foreach ($aux as $agent => $row) {
                $appointments = (array_key_exists('appointments', $row)) ? $row['appointments'] : 0;
                $data[]       = array(
                    "agent" => $agent,
                    "name" => $row['name'],
                    "appointments" => $appointments,
                	"total_dials" => $row['total_dials'],
                	"duration" => ($row['duration'])?$row['duration']:0,
                	"rate" => ($row['duration']>0)?round($appointments/($row['duration']/3600),3):0
                );
                $totalAppointments += $appointments;
                $totalDials += $row['total_dials'];
                $totalDuration += $row['duration'];
            }
            
            $totalAppointmentsPercent = number_format(($totalAppointments * 100) / $totalDials, 2) . '%';
            
            array_push($data, array(
                "agent" => "TOTAL",
                "name" => "",
                "appointments" => $totalAppointments . " (" . $totalAppointmentsPercent . ")",
                "total_dials" => (count($results) > 0)?$totalDials:0,
                "duration" => $totalDuration,
                "rate" => ($row['duration']>0)?round($totalAppointments/($totalDuration/3600),3):0
            ));
            
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
    //this is the controller loads the initial view for the agentsurvey report dashboard
    public function agentsurvey()
    {
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $agents       = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Agent Surveys',
            'page' => array(
                'reports' => 'agent',
                'inner' => 'agenttransfer'
            ),
            'javascript' => array(
                'charts.js',
                'report/agentsurvey.js',
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
        $this->template->load('default', 'reports/agentsurvey.php', $data);
    }
    
    //this controller sends the agentsurvey report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function agentsurvey_data()
    {
        if ($this->input->is_ajax_request()) {
            $data    = array();
            $results = $this->Report_model->get_agent_report_by_outcome($this->input->post());
            
            $aux = array();
            
            foreach ($results as $row) {
                $aux[$row['agent']]['name'] = $row['name'];
                $aux[$row['agent']]['duration'] = $row['duration'];
                if ($row['outcome'] == 'Survey Complete') {
                    $aux[$row['agent']]['complete_surveys'] = $row['count'];
                } elseif ($row['outcome'] == 'Survey Refused') {
                    $aux[$row['agent']]['refused_surveys'] = $row['count'];
                }
                $aux[$row['agent']]['total_dials'] = (isset($aux[$row['agent']]['total_dials'])) ? $aux[$row['agent']]['total_dials'] + $row['count'] : $row['count'];
            }
            
            $totalCompleteSurveys = 0;
            $totalRefusedSurveys  = 0;
            $totalDials          = (count($aux) == 0)?1:0;
            $totalDuration		 = 0;
            foreach ($aux as $agent => $row) {
                $completeSurveys = (array_key_exists('complete_surveys', $row)) ? $row['complete_surveys'] : 0;
                $refusedSurveys  = (array_key_exists('refused_surveys', $row)) ? $row['refused_surveys'] : 0;
                $data[]          = array(
                    "agent" => $agent,
                    "name" => $row['name'],
                    "complete_surveys" => $completeSurveys,
                    "refused_surveys" => $refusedSurveys,
                    "total_surveys" => $completeSurveys + $refusedSurveys,
                    "total_dials" => $row['total_dials'],
                	"duration" => ($row['duration'])?$row['duration']:0,
                	"rate" => ($row['duration']>0)?round(($completeSurveys + $refusedSurveys)/($row['duration']/3600),3):0
                );
                $totalCompleteSurveys += $completeSurveys;
                $totalRefusedSurveys += $refusedSurveys;
                $totalDials += $row['total_dials'];
                $totalDuration += $row['duration'];
            }
            
            $totalCompleteSurveysPercent = number_format(($totalCompleteSurveys * 100) / $totalDials, 2) . '%';
            $totalRefusedSurveysPercent  = number_format(($totalRefusedSurveys * 100) / $totalDials, 2) . '%';
            $totalSurveysPercent         = number_format((($totalCompleteSurveys + $totalRefusedSurveys) * 100) / $totalDials, 2) . '%';
            
            array_push($data, array(
                "agent" => "TOTAL",
                "name" => "",
                "complete_surveys" => $totalCompleteSurveys . " (" . $totalCompleteSurveysPercent . ")",
                "refused_surveys" => $totalRefusedSurveys . " (" . $totalRefusedSurveysPercent . ")",
                "total_surveys" => ($totalCompleteSurveys + $totalRefusedSurveys) . " (" . $totalSurveysPercent . ")",
                "total_dials" => (count($results) > 0)?$totalDials:0,
                "duration" => $totalDuration,
                "rate" => ($row['duration']>0)?round(($totalCompleteSurveys + $totalRefusedSurveys)/($totalDuration/3600),3):0
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
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $teamManagers = $this->Form_model->get_teams();
        $agents       = $this->Form_model->get_agents();
        $sources      = $this->Form_model->get_sources();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Agent Dials',
            'page' => array(
                'reports' => 'agent',
                'inner' => 'agentdials'
            ),
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
            $data    = array();
            $results = $this->Report_model->get_agentdials_report($this->input->post(), array(
                "Transfer",
                "Cross Transfer"
            ));
            
            
            $aux = array();
            
            foreach ($results as $row) {
                
                $aux[$row['advisor']]['name']  = $row['name'];
                $aux[$row['advisor']]['total'] = $row['count'];
            }
            
            foreach ($aux as $advisor => $row) {
                $data[] = array(
                    "advisor" => $advisor,
                    "name" => $row['name'],
                    "total" => $row['total']
                );
            }
            
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
    
    //this is the controller loads the initial view for the transfer daily comparision report dashboard
    public function dailytransfer()
    {
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $teamManagers = $this->Form_model->get_teams();
        $agents       = $this->Form_model->get_agents();
        $sources      = $this->Form_model->get_sources();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Individual Daily Comparison',
            'page' => array(
                'reports' => 'daily',
                'inner' => 'dailytransfer'
            ),
            'javascript' => array(
                'charts.js',
                'report/dailytransfer.js',
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
        $this->template->load('default', 'reports/dailytransfer.php', $data);
    }
    
    //this controller sends the transfer daily comparision report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function dailytransfer_data()
    {
        if ($this->input->is_ajax_request()) {
            $data    = array();
            $results = $this->Report_model->get_daily_report_by_outcome($this->input->post());
            
            $agent = $this->input->post("agent");
            $name  = "All Users";
            
            $aux = array();
            foreach ($results as $row) {
                if (!empty($agent)) {
                    $name = $row['name'];
                }
                if ($row['outcome'] == 'Transfer') {
                    $aux[$row['date']]['transfers'] = $row['count'];
                } elseif ($row['outcome'] == 'Cross Transfer') {
                    $aux[$row['date']]['cross_transfers'] = $row['count'];
                }
                $aux[$row['date']]['total_dials'] = (isset($aux[$row['date']]['total_dials'])) ? $aux[$row['date']]['total_dials'] + $row['count'] : $row['count'];
                $aux[$row['date']]['duration'] = $row['duration'];
            }
            
            $totalTransfers      = 0;
            $totalCrossTransfers = 0;
            $totalDials          = (count($aux) == 0)?1:0;
            $totalDuration		 = 0;
            foreach ($aux as $date => $row) {
                $transfers      = (array_key_exists('transfers', $row)) ? $row['transfers'] : 0;
                $crossTransfers = (array_key_exists('cross_transfers', $row)) ? $row['cross_transfers'] : 0;
                $data[]         = array(
                    "date" => $date,
                    "name" => $name,
                    "transfers" => $transfers,
                    "cross_transfers" => $crossTransfers,
                    "total_transfers" => $transfers + $crossTransfers,
                    "total_dials" => $row['total_dials'],
                    "duration" => ($row['duration'])?$row['duration']:0,
                    "rate" => ($row['duration']>0)?round(($transfers + $crossTransfers)/($row['duration']/3600),3):0
                );
                $totalTransfers += $transfers;
                $totalCrossTransfers += $crossTransfers;
                $totalDials += $row['total_dials'];
                $totalDuration += $row['duration'];
            }
            
            $totalTransfersPercent      = number_format(($totalTransfers * 100) / $totalDials, 2) . '%';
            $totalCrossTransfersPercent = number_format(($totalCrossTransfers * 100) / $totalDials, 2) . '%';
            $totalPercent               = number_format((($totalTransfers + $totalCrossTransfers) * 100) / $totalDials, 2) . '%';
            
            array_push($data, array(
                "date" => "TOTAL",
                "name" => "",
                "transfers" => $totalTransfers . " (" . $totalTransfersPercent . ")",
                "cross_transfers" => $totalCrossTransfers . " (" . $totalCrossTransfersPercent . ")",
                "total_transfers" => ($totalTransfers + $totalCrossTransfers) . " (" . $totalPercent . ")",
                "total_dials" => (count($results) > 0)?$totalDials:0,
                "duration" => $totalDuration,
                "rate" => ($row['duration']>0)?round(($totalTransfers + $totalCrossTransfers)/($totalDuration/3600),3):0
            ));
            
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
    //this is the controller loads the initial view for the appointment daily comparision report dashboard
    public function dailyappointment()
    {
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $teamManagers = $this->Form_model->get_teams();
        $agents       = $this->Form_model->get_agents();
        $sources      = $this->Form_model->get_sources();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Individual Daily Comparison',
            'page' => array(
                'reports' => 'daily',
                'inner' => 'dailyappointment'
            ),
            'javascript' => array(
                'charts.js',
                'report/dailyappointment.js',
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
        $this->template->load('default', 'reports/dailyappointment.php', $data);
    }
    
    //this controller sends the appointment daily comparision report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function dailyappointment_data()
    {
        if ($this->input->is_ajax_request()) {
            $data    = array();
            $results = $this->Report_model->get_daily_report_by_outcome($this->input->post());
            
            $agent = $this->input->post("agent");
            $name  = "All Users";
            
            $aux = array();
            foreach ($results as $row) {
                if (!empty($agent)) {
                    $name = $row['name'];
                }
                if ($row['outcome'] == 'Appointment') {
                    $aux[$row['date']]['appointments'] = $row['count'];
                }
                $aux[$row['date']]['total_dials'] = (isset($aux[$row['date']]['total_dials'])) ? $aux[$row['date']]['total_dials'] + $row['count'] : $row['count'];
                $aux[$row['date']]['duration'] = $row['duration'];
            }
            
            $totalAppointments = 0;
            $totalDials          = (count($aux) == 0)?1:0;
            $totalDuration		 = 0;
            foreach ($aux as $date => $row) {
                $appointments = (array_key_exists('appointments', $row)) ? $row['appointments'] : 0;
                $data[]       = array(
                    "date" => $date,
                    "name" => $name,
                    "appointments" => $appointments,
                    "total_dials" => $row['total_dials'],
                	"duration" => ($row['duration'])?$row['duration']:0,
                	"rate" => ($row['duration']>0)?round($appointments/($row['duration']/3600),3):0
                );
                $totalAppointments += $appointments;
                $totalDials += $row['total_dials'];
                $totalDuration += $row['duration'];
            }
            
            $totalAppointmentsPercent = number_format(($totalAppointments * 100) / $totalDials, 2) . '%';
            
            array_push($data, array(
                "date" => "TOTAL",
                "name" => "",
                "appointments" => $totalAppointments . " (" . $totalAppointmentsPercent . ")",
                "total_dials" => (count($results) > 0)?$totalDials:0,
                "duration" => $totalDuration,
                "rate" => round($totalAppointments/($totalDuration/3600),3)
            ));
            
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
    //this is the controller loads the initial view for the surveys daily comparision report dashboard
    public function dailysurvey()
    {
        
        $campaigns    = $this->Form_model->get_user_campaigns();
        $teamManagers = $this->Form_model->get_teams();
        $agents       = $this->Form_model->get_agents();
        $sources      = $this->Form_model->get_sources();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Individual Daily Comparison',
            'page' => array(
                'reports' => 'daily',
                'inner' => 'dailysurvey'
            ),
            'javascript' => array(
                'charts.js',
                'report/dailysurvey.js',
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
        $this->template->load('default', 'reports/dailysurvey.php', $data);
    }
    
    //this controller sends the appointment daily comparision report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function dailysurvey_data()
    {
        if ($this->input->is_ajax_request()) {
            $data    = array();
            $results = $this->Report_model->get_daily_report_by_outcome($this->input->post());
            
            $agent = $this->input->post("agent");
            $name  = "All Users";
            
            $aux = array();
            foreach ($results as $row) {
                if (!empty($agent)) {
                    $name = $row['name'];
                }
                if ($row['outcome'] == 'Survey Complete') {
                    $aux[$row['date']]['complete_surveys'] = $row['count'];
                } elseif ($row['outcome'] == 'Survey Refused') {
                    $aux[$row['date']]['refused_surveys'] = $row['count'];
                }
                $aux[$row['date']]['total_dials'] = (isset($aux[$row['date']]['total_dials'])) ? $aux[$row['date']]['total_dials'] + $row['count'] : $row['count'];
                $aux[$row['date']]['duration'] = $row['duration'];
            }
            
            $totalCompleteSurveys = 0;
            $totalRefusedSurveys  = 0;
            $totalDials          = (count($aux) == 0)?1:0;
            $totalDuration		 = 0;
            foreach ($aux as $date => $row) {
                $completeSurveys = (array_key_exists('complete_surveys', $row)) ? $row['complete_surveys'] : 0;
                $refusedSurveys  = (array_key_exists('refused_surveys', $row)) ? $row['refused_surveys'] : 0;
                $data[]          = array(
                    "date" => $date,
                    "name" => $name,
                    "complete_surveys" => $completeSurveys,
                    "refused_surveys" => $refusedSurveys,
                    "total_surveys" => $completeSurveys + $refusedSurveys,
                    "total_dials" => $row['total_dials'],
                	"duration" => ($row['duration'])?$row['duration']:0,
                	"rate" => ($row['duration']>0)?round(($completeSurveys + $refusedSurveys)/($row['duration']/3600),3):0
                );
                $totalCompleteSurveys += $completeSurveys;
                $totalRefusedSurveys += $refusedSurveys;
                $totalDials += $row['total_dials'];
                $totalDuration += $row['duration'];
            }
            
            $totalCompleteSurveysPercent = number_format(($totalCompleteSurveys * 100) / $totalDials, 2) . '%';
            $totalRefusedSurveysPercent  = number_format(($totalRefusedSurveys * 100) / $totalDials, 2) . '%';
            $totalSurveysPercent         = number_format((($totalCompleteSurveys + $totalRefusedSurveys) * 100) / $totalDials, 2) . '%';
            
            array_push($data, array(
                "date" => "TOTAL",
                "name" => "",
                "complete_surveys" => $totalCompleteSurveys . " (" . $totalCompleteSurveysPercent . ")",
                "refused_surveys" => $totalRefusedSurveys . " (" . $totalRefusedSurveysPercent . ")",
                "total_surveys" => ($totalCompleteSurveys + $totalRefusedSurveys) . " (" . $totalSurveysPercent . ")",
                "total_dials" => (count($results) > 0)?$totalDials:0,
                "duration" => $totalDuration,
                "rate" => ($row['duration']>0)?round(($totalCompleteSurveys + $totalRefusedSurveys)/($totalDuration/3600),3):0
            ));
            
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
}
