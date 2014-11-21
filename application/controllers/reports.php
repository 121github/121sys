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
		$outcomes       = $this->Form_model->get_outcomes();
		        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Campaign Transfer',
            'page' => array(
                'reports' => 'transfers',
                'inner' => 'campaigntransfer'
            ),
            'javascript' => array(
                'charts.js',
                'report/campaigntransfer.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
			'outcomes' => $outcomes,
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
            $results = $this->Report_model->get_transfers_data($this->input->post());
            
            $date_from_search = $this->input->post("date_from");
            $date_to_search   = $this->input->post("date_to");
            $agent_search      = $this->input->post("agent");
            $campaign_search  = $this->input->post("campaign");
            $team_search  = $this->input->post("team");
            $source_search  = $this->input->post("source");
            
            $aux = array();
            foreach ($results as $row) {
				if($row['total_dials']){
				$aux[$row['campaign_id']]['name'] = $row['campaign_name'];
				$aux[$row['campaign_id']]['duration'] = $row['duration'];
				$aux[$row['campaign_id']]['transfers'] = $row['transfer_count'];
				$aux[$row['campaign_id']]['cross_transfers'] = $row['cross_count'];
				$aux[$row['campaign_id']]['total_dials'] = $row['total_dials'];
				}
            }

            $totalTransfers      = 0;
            $totalCrossTransfers = 0;
            $totalDials          = 0;
            $totalDuration		 = 0;
            $url = base_url()."search/custom/history";
            $url .= (!empty($agent_search) ? "/user/$agent_search" : "");
			$url .= (!empty($campaign_search) ? "/campaign/$campaign_search" : "");
            $url .= (!empty($date_from_search) ? "/contact-from/$date_from_search" : "");
            $url .= (!empty($date_to_search) ? "/contact-to/$date_to_search" : "");
            $url .= (!empty($team_search) ? "/team/$team_search" : "");
            $url .= (!empty($source_search) ? "/source/$source_search" : "");
            
            foreach ($aux as $campaign => $row) {
            	$transfers      = (array_key_exists('transfers', $row)) ? $row['transfers'] : 0;
	            $crossTransfers = (array_key_exists('cross_transfers', $row)) ? $row['cross_transfers'] : 0;
	            
	            $urlCampaign = $url."/campaign/".$campaign;
	            $transfersUrl = $urlCampaign."/outcome/Transfer";
	            $crossTransfersUrl = $url."/cross/".$campaign;
	            $totalTransfersUrl = $url."/transfers/".$campaign;
	            $allDialsUrl = $url."/alldials/".$campaign;
	            $data[]         = array(
                    "campaign" => $campaign,
                    "name" => $row['name'],
                    "transfers" => $transfers,
	            	"transfers_url" => $transfersUrl,
                    "cross_transfers" => $crossTransfers,
	            	"cross_transfers_url" => $crossTransfersUrl,
                    "total_transfers" => $transfers + $crossTransfers,
	            	"total_transfers_url" => $totalTransfersUrl,
                    "total_dials" => $row['total_dials'],
	            	"total_dials_url" => $allDialsUrl,
	            	"duration" => ($row['duration'])?$row['duration']:0,
	            	"rate" => ($row['duration']>0)?round(($transfers + $crossTransfers)/($row['duration']/3600),3):0
                );
                $totalTransfers += $transfers;
                $totalCrossTransfers += $crossTransfers;
                $totalDials += ($row['total_dials']?$row['total_dials']:"0");
                $totalDuration += ($row['duration']?$row['duration']:"0");
            }
            
            $totalTransfersPercent      = ($totalDials)?number_format(($totalTransfers * 100) / $totalDials, 2):0;
            $totalCrossTransfersPercent = ($totalDials)?number_format(($totalCrossTransfers * 100) / $totalDials, 2):0;
            $totalPercent               = ($totalDials)?number_format((($totalTransfers + $totalCrossTransfers) * 100) / $totalDials, 2):0;
            
            $url .= (!empty($campaign_search) ? "/campaign/$campaign_search" : "");
            
            array_push($data, array(
                "campaign" => "TOTAL",
                "name" => "",
                "transfers" => $totalTransfers . " (" . $totalTransfersPercent . "%)",
                "transfers_url" => $url."/outcome/Transfer",
                "cross_transfers" => $totalCrossTransfers . " (" . $totalCrossTransfersPercent . "%)",
                "cross_transfers_url" => $url."/outcome/Cross Transfer",
                "total_transfers" => ($totalTransfers + $totalCrossTransfers) . " (" . $totalPercent . "%)",
                "total_transfers_url" => $url."/outcome/Transfer"."/outcome/Cross Transfer",
                "total_dials" => $totalDials,
                "total_dials_url" => $url,
                "duration" => $totalDuration,
                "rate" => ($totalDuration>0)?round(($totalTransfers + $totalCrossTransfers)/($totalDuration/3600),3):0
            ));
            
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
    //this is the controller loads the initial view for the campaign appointment report dashboard
    public function outcomes()
    {
		if($this->uri->segment(3)=="campaign"){
			$group = "campaign";
		} else if($this->uri->segment(3)=="agent"){
			$group = "agent";
		} else if($this->uri->segment(3)=="date"){
			$group = "date";
		} else {
			$group = "campaign";
		}
		
		if(intval($this->uri->segment(4))){
        $outcome_id = $this->uri->segment(4);
		} else {
		$outcome_id = "72";	
		}
        $campaigns    = $this->Form_model->get_user_campaigns();
        $teamManagers = $this->Form_model->get_teams();
        $sources      = $this->Form_model->get_sources();
		$agents       = $this->Form_model->get_agents();
		$outcomes       = $this->Form_model->get_outcomes();
		
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Campaign Outcme',
            'page' => array(
                'reports' => 'outcomes',
                'inner' => $group
            ),
            'javascript' => array(
                'charts.js',
                'report/outcomes.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
			'group' => $group,
			'outcome_id'=>$outcome_id,
			'outcomes' => $outcomes,
            'campaigns' => $campaigns,
            'sources' => $sources,
            'team_managers' => $teamManagers,
			'agents' => $agents,
            'css' => array(
                'dashboard.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'reports/outcomes.php', $data);
    }
    
    //this controller sends the campaign appointment report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function outcome_data()
    {
     if ($this->input->is_ajax_request()) {
            $data    = array();
			
            $results = $this->Report_model->get_outcome_data($this->input->post());
			
            $date_from_search = $this->input->post("date_from");
            $date_to_search   = $this->input->post("date_to");
            $agent_search      = $this->input->post("agent");
            $campaign_search  = $this->input->post("campaign");
            $team_search  = $this->input->post("team");
            $source_search  = $this->input->post("source");
			$outcome_id  = $this->input->post("outcome");
			$group  =  $this->input->post("group");
			
			$this->db->where("outcome_id",$outcome_id);
			$query = $this->db->get("outcomes");
			if($query->num_rows()>0){
				$outcome = $query->row()->outcome;
			}
		
		
            $aux = array();
            foreach ($results as $row) {
				if($row['total_dials']){
				if($group=="date"){ 
				$aux[$row['id']]['sql']= $row['sql']; }
				$aux[$row['id']]['name'] = $row['name'];
				$aux[$row['id']]['duration'] = $row['duration'];
				$aux[$row['id']]['outcomes'] = $row['outcome_count'];
				$aux[$row['id']]['total_dials'] = $row['total_dials'];
				}
            }

            $totalOutcomes      = 0;
            $totalDials          = 0;
            $totalDuration		 = 0;
            $url = base_url()."search/custom/history";
            $url .= (!empty($agent_search) ? "/user/$agent_search" : "");
			$url .= (!empty($campaign_search) ? "/campaign/$campaign_search" : "");
            $url .= (!empty($date_from_search) ? "/contact-from/$date_from_search" : "");
            $url .= (!empty($date_to_search) ? "/contact-to/$date_to_search" : "");
            $url .= (!empty($team_search) ? "/team/$team_search" : "");
            $url .= (!empty($source_search) ? "/source/$source_search" : "");
             if($group=="date"){ $group="contact"; }
            foreach ($aux as $id => $row) {
				$outcomes      = (array_key_exists('outcomes', $row)) ? $row['outcomes'] : 0;
				//create the click through hyperlinks
	             if($group=="contact"){ 
				 $allDialsUrl = $url."/contact/".$row['sql'];
				 $outcomesUrl = $allDialsUrl."/outcome/".$this->input->post('outcome');
				 } 
				 else if( $group == "agent") {
				 $allDialsUrl = $url."/user/".$id;
				 $outcomesUrl = $allDialsUrl."/outcome/".$this->input->post('outcome');	 
					  }
				 else {  
				  $allDialsUrl = $url."/alldials/".$id;
				 $outcomesUrl =$url."/campaign/".$id."/outcome/".$this->input->post('outcome');	 
				 }
				
	            $data[]         = array(
                    "id" => $id,
                    "name" => $row['name'],
                    "outcomes" => $outcomes,
	            	"outcomes_url" => $outcomesUrl,
                    "total_dials" => $row['total_dials'],
	            	"total_dials_url" => $allDialsUrl,
	            	"duration" => ($row['duration'])?$row['duration']:0,
	            	"rate" => ($row['duration']>0)?round(($outcomes)/($row['duration']/3600),3):0
                );
                $totalOutcomes += $outcomes;
                $totalDials += ($row['total_dials']?$row['total_dials']:"0");
                $totalDuration += ($row['duration']?$row['duration']:"0");
            }
            
            $totalOutcomesPercent      = ($totalDials)?number_format(($totalOutcomes * 100) / $totalDials, 2):0;
       
            $totalPercent               = ($totalDials)?number_format((($totalOutcomes) * 100) / $totalDials, 2):0;
            
            $url .= (!empty($campaign_search) ? "/campaign/$campaign_search" : "");
            
            array_push($data, array(
                "id" => "TOTAL",
                "name" => "",
                "outcomes" => $totalOutcomes . " (" . $totalOutcomesPercent . "%)",
                "outcomes_url" => $url."/outcome/$outcome",
                "total_dials" => $totalDials,
                "total_dials_url" => $url,
                "duration" => $totalDuration,
                "rate" => ($totalDuration>0)?round(($totalOutcomes)/($totalDuration/3600),3):0
            ));
            
            echo json_encode(array(
                "success" => true,
				"outcome"=>$outcome,
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
        $sources      = $this->Form_model->get_sources();
        
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Reports',
            'title' => 'Reports | Agent Transfers',
            'page' => array(
                'reports' => 'transfers',
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
        	'sources' => $sources,
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
            $results = $this->Report_model->get_agent_transfer_data($this->input->post());
            
            $date_from_search = $this->input->post("date_from");
            $date_to_search   = $this->input->post("date_to");
            $agent_search      = $this->input->post("agent");
            $campaign_search  = $this->input->post("campaign");
            $team_search  = $this->input->post("team");
            $source_search  = $this->input->post("source");
            
            $aux = array();
            foreach ($results as $row) {
				if($row['total_dials']){
				$aux[$row['user_id']]['name'] = $row['name'];
				$aux[$row['user_id']]['duration'] = $row['duration'];
				$aux[$row['user_id']]['transfers'] = $row['transfer_count'];
				$aux[$row['user_id']]['cross_transfers'] = $row['cross_count'];
				$aux[$row['user_id']]['total_dials'] = $row['total_dials'];
				}
            }

            $totalTransfers      = 0;
            $totalCrossTransfers = 0;
            $totalDials          = 0;
            $totalDuration		 = 0;
            
            $url = base_url() . "search/custom/history";
            $url .= (!empty($agent_search) ? "/user/$agent_search" : "");
            $url .= (!empty($date_from_search) ? "/contact/$date_from_search:emore" : "");
            $url .= (!empty($date_to_search) ? "/contact/$date_to_search:eless" : "");
            $url .= (!empty($team_search) ? "/team/$team_search" : "");
            $url .= (!empty($source_search) ? "/source/$source_search" : "");
            
            foreach ($aux as $agent => $row) {
            	$transfers      = (array_key_exists('transfers', $row)) ? $row['transfers'] : 0;
	            $crossTransfers = (array_key_exists('cross_transfers', $row)) ? $row['cross_transfers'] : 0;
	            
	            $urlAgent =  $url."/user/".$agent;
				if(empty($campaign_search)){
				$transfersUrl = $urlAgent."/outcome/Transfer";	
				} else {
	            $transfersUrl = $urlAgent."/outcome/Transfer/campaign/$campaign_search";
				}
				if(empty($campaign_search)){
	            $crossTransfersUrl = $urlAgent."/outcome/Cross Transfer";
				} else {
				$crossTransfersUrl = $urlAgent."/cross/$campaign_search";	
				}
				if(empty($campaign_search)){
	            $totalTransfersUrl = $urlAgent."/outcome_1/Transfer/outcome_2/Cross Transfer";
				} else {
				$totalTransfersUrl = $urlAgent."/transfers/$campaign_search";	
				}
				if(empty($campaign_search)){
	            $allDialsUrl = $urlAgent."/alldials/all";
				} else {
				$allDialsUrl = $urlAgent."/alldials/$campaign_search";	
				}
                $data[]         = array(
                    "agent" => $agent,
                    "name" => $row['name'],
                    "transfers" => $transfers,
	            	"transfers_url" => $transfersUrl,
                    "cross_transfers" => $crossTransfers,
	            	"cross_transfers_url" => $crossTransfersUrl,
                    "total_transfers" => $transfers + $crossTransfers,
	            	"total_transfers_url" => $totalTransfersUrl,
                    "total_dials" => $row['total_dials'],
	            	"total_dials_url" => $allDialsUrl,
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
            
            $url .= (!empty($agent_search) ? "/user/$agent_search" : "");
            
            array_push($data, array(
                "agent" => "TOTAL",
                "name" => "",
                "transfers" => $totalTransfers . " (" . $totalTransfersPercent . "%)",
                "transfers_url" => $url."/outcome/Transfer",
                "cross_transfers" => $totalCrossTransfers . " (" . $totalCrossTransfersPercent . "%)",
                "cross_transfers_url" => $url."/outcome/Cross Transfer",
                "total_transfers" => ($totalTransfers + $totalCrossTransfers) . " (" . $totalPercent . "%)",
                "total_transfers_url" => $url."/outcome/Transfer"."/outcome/Cross Transfer",
                "total_dials" => $totalDials,
                "total_dials_url" => $url,
                "duration" => $totalDuration,
                "rate" => ($totalDuration>0)?round(($totalTransfers + $totalCrossTransfers)/($totalDuration/3600),3):0
            ));
            
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
                'reports' => 'transfers',
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
            $results = $this->Report_model->get_daily_transfer_data($this->input->post());
            
            $date_from_search = $this->input->post("date_from");
            $date_to_search   = $this->input->post("date_to");
            $agent_search      = $this->input->post("agent");
            $campaign_search  = $this->input->post("campaign");
            $team_search  = $this->input->post("team");
            $source_search  = $this->input->post("source");
            $group_by_field  = $this->input->post("view");
			
		$group_by_details["user_id"] = array("name"=>"name","field"=>"h.user_id","url"=>"user");
		$group_by_details["team_id"] = array("name"=>"team_name","field"=>"h.team_id","url"=>"team");
		$group_by_details["source_id"] = array("name"=>"source_name","field"=>"r.source_id","url"=>"source");
		$group_by_details["campaign_id"] = array("name"=>"campaign_name","field"=>"h.campaign_id","url"=>"campaign");
			
            $aux = array();
            foreach ($results as $row) {
				if($row['total_dials']){
				$aux[$row['date']]['name'] = "All";
				$aux[$row['date']]['duration'] = $row['duration'];
				$aux[$row['date']]['transfers'] = $row['transfer_count'];
				$aux[$row['date']]['cross_transfers'] = $row['cross_count'];
				$aux[$row['date']]['total_dials'] = $row['total_dials'];
				$aux[$row['date']]['url']  = "";
				
				if(!empty($group_by_field)){
				$aux[$row['date']]['name'] = $row['name'];
				$aux[$row['date']]['id'] = $row['id'];
				$aux[$row['date']]['url'] = "/".$group_by_details[$group_by_field]['url']."/".$row['id'];
				}
				}
            }
				
            $totalTransfers      = 0;
            $totalCrossTransfers = 0;
            $totalDials          = 0;
            $totalDuration		 = 0;
            
            $url = base_url() . "search/custom/history";
            $url .= (!empty($agent_search) ? "/user/$agent_search" : "");
            $url .= (!empty($team_search) ? "/team/$team_search" : "");
            $url .= (!empty($source_search) ? "/source/$source_search" : "");
            
            foreach ($aux as $date => $row) {
            	$transfers      = (array_key_exists('transfers', $row)) ? $row['transfers'] : 0;
	            $crossTransfers = (array_key_exists('cross_transfers', $row)) ? $row['cross_transfers'] : 0;
	            
	            $urlDate = $url."/contact/".$date.$row['url'];
				if(empty($campaign_search)){
				$transfersUrl = $urlDate."/outcome/Transfer";	
				} else {
	            $transfersUrl = $urlDate."/outcome/Transfer/campaign/$campaign_search";
				}
				if(empty($campaign_search)){
	            $crossTransfersUrl = $urlDate."/outcome/Cross Transfer";
				} else {
				$crossTransfersUrl = $urlDate."/cross/$campaign_search";	
				}
				if(empty($campaign_search)){
	            $totalTransfersUrl = $urlDate."/outcome_1/Transfer/outcome_2/Cross Transfer";
				} else {
				$totalTransfersUrl = $urlDate."/transfers/$campaign_search";	
				}
				if(empty($campaign_search)){
	            $allDialsUrl = $urlDate."/alldials/all";
				} else {
				$allDialsUrl = $urlDate."/alldials/$campaign_search";	
				}

                
                $data[]         = array(
                    "date" => $date,
                    "name" => $row['name'],
                    "transfers" => $transfers,
	            	"transfers_url" => $transfersUrl,
                    "cross_transfers" => $crossTransfers,
	            	"cross_transfers_url" => $crossTransfersUrl,
                    "total_transfers" => $transfers + $crossTransfers,
	            	"total_transfers_url" => $totalTransfersUrl,
                    "total_dials" => $row['total_dials'],
	            	"total_dials_url" => $urlDate,
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
                "transfers" => $totalTransfers . " (" . $totalTransfersPercent . "%)",
                "transfers_url" => $url."/outcome/Transfer",
                "cross_transfers" => $totalCrossTransfers . " (" . $totalCrossTransfersPercent . "%)",
                "cross_transfers_url" => $url."/outcome/Cross Transfer",
                "total_transfers" => ($totalTransfers + $totalCrossTransfers) . " (" . $totalPercent . "%)",
                "total_transfers_url" => $url."/outcome/Transfer"."/outcome/Cross Transfer",
                "total_dials" => $totalDials,
                "total_dials_url" => $url,
                "duration" => $totalDuration,
                "rate" => ($totalDuration>0)?round(($totalTransfers + $totalCrossTransfers)/($totalDuration/3600),3):0
            ));
            
            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }
    
}
