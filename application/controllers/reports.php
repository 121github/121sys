<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
        $this->_campaigns = campaign_access_dropdown();
        

        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Report_model');
    }

    public function overview()
    {

        $report_filter = $this->Filter_model->build_filter_options(true);
		$report_filter['campaigns'] = $this->_campaigns;
		$title = "Overview Report";
        $data = array(
			'report_filter'=>$report_filter,
            'campaign_access' => $this->_campaigns,
            'title' => $title,
            'page' => 'activity',
            'javascript' => array(
                'charts.js',
				'report/reports.js',
                'report/overview.js',
               
                'lib/daterangepicker.js'
            ),
				 'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            ),
			'submenu' => array("file"=>'default_submenu.php',"title"=>$title)
        );
        $this->template->load('default', 'reports/overview.php', $data);


    }

    public function overview_data()
    {
        if ($this->input->is_ajax_request()) {
            $data = array();
            $data_array = array();
            $totals = array();
            $total = 0;
            $by_pots = false;
            if ($this->uri->segment(3) == "pots") {
                $by_pots = true;
            }
           
$field_assoc = array("date_from"=>"h.contact",
			"date_to"=>"h.contact",
			"user_id"=>"h.user_id",
			"campaign_id"=>"h.campaign_id",
			"team_id"=>"h.team_id",
			"pot_id"=>"h.pot_id",
			"outcome_id"=>"h.outcome_id",
			"source_id"=>"h.source_id",
			"group_id"=>"h.group_id");
			
			$query_params = $this->Report_model->build_filter_query($this->input->post(),$field_assoc);
			$results = $this->Report_model->get_overview($query_params, $by_pots);
            $date_from = $this->input->post("date_from");
            $date_to = $this->input->post("date_to");
            $users = $this->input->post("user_id");
            $campaigns = $this->input->post("campaign_id");
			$outcomes = $this->input->post("outcome_id");
            $teams = $this->input->post("team_id");
            $pots = $this->input->post("pot_id");
			$sources = $this->input->post("source_id");
			$branches = $this->input->post("branch_id");
			$regions = $this->input->post("region_id");
            /*
                        $overall_array = array();
                        $post = $this->input->post();
                        if ($this->input->post('teams') || $this->input->post('agents')) {
                            $colname = $this->input->post('colname');
                            unset($post['teams']);
                            unset($post['agents']);
                            $overall = $this->Report_model->get_overview($post);
                            $overall_array = array();
                            foreach ($overall as $k => $row) {
                                $overall_array[$row['outcome']]["overall_total"] = $row['total'];
                                $overall_array[$row['outcome']]["overall"] = (isset($row['total']) ? number_format(($row['count'] / $row['total']) * 100, 1) : "-");
                            }
                        }
                        */
            $url = base_url() . "search/custom/history";
            $url .= (!empty($campaigns) ? "/hcampaign/" . implode("_", $campaigns) . (count($campaigns) > 1 ? ":in" : "") : "");
            $url .= (!empty($users) ? "/user/" . implode("_", $users) . (count($users) > 1 ? ":in" : "") : "");
            $url .= (!empty($date_from) ? "/contact-from/$date_from" : "");
            $url .= (!empty($date_to) ? "/contact-to/$date_to" : "");
            $url .= (!empty($teams) ? "/team/" . implode("_", $teams) . (count($teams) > 1 ? ":in" : "") : "");
            $url .= (!empty($sources) ? "/hsource/" . implode("_", $sources) . (count($sources) > 1 ? ":in" : "") : "");
            $url .= (!empty($outcomes) ? "/outcome/" . implode("_", $outcomes) . (count($outcomes) > 1 ? ":in" : "") : "");
			
            $total_url = $url;
            $campaign_results = array();
            $campaign_total = array();

            if (!$by_pots) {
                foreach ($results as $k => $row) {
                    $campaign_total[$row['campaign_id']] = 0;
                    $campaign_results[$row['campaign_id'] . ":" . $row['campaign_group_id']] = array("campaign" => $row['campaign_name'], "count" => 0, "color" => get_color($row['campaign_group_id']));
                    $totals[$row['user_id']] = 0;
                }
                foreach ($results as $k => $row) {
                    $data[$row['user_id'] . ":" . $row['name']] = $campaign_results;
                }
                foreach ($results as $k => $row) {
                    $total = $row['total'];
                    $data[$row['user_id'] . ":" . $row['name']][$row['campaign_id'] . ":" . $row['campaign_group_id']] = array(
                        "campaign" => $row['campaign_name'],
                        "user" => $row['name'],
                        "user_id" => $row['user_id'],
                        "count" => $row['count'],
                        "total_url" => $url . "/user/" . $row['user_id'],
                        "url" => $url . "/user/" . $row['user_id'] . '/campaign/' . $row['campaign_id'],
                        "color" => get_color($row['campaign_group_id'])
                    );
                    isset($campaign_total[$row['campaign_id']]) ? $campaign_total[$row['campaign_id']] += $row['count'] : 0;
                    //this builds the campaign totals by creating a "user" called total and adding the counts
                    $data["Totals"][$row['campaign_id'] . ":" . $row['campaign_group_id']] = array(
                        "campaign" => $row['campaign_name'],
                        "user" => "Total",
                        "user_id" => 0,
                        "count" => $campaign_total[$row['campaign_id']],
                        "total_url" => $url . "/user/" . $row['user_id'],
                        "grand_total_url" => $url,
                        "url" => $url . '/campaign/' . $row['campaign_id'],
                        "color" => get_color($row['campaign_group_id'])
                    );

                    $totals[$row['user_id']] += $row['count'];
                    $data_array = $data;
                }
            }
            if ($by_pots) {
                foreach ($results as $k => $row) {
                    $campaign_total[$row['campaign_id']] = 0;
                    $campaign_results[$row['campaign_id'] . ":"] = array("campaign" => $row['campaign_name'], "count" => 0, "color" => get_color($row['campaign_group_id']));
                    $totals[$row['user_id']] = 0;
                };
                foreach ($results as $k => $row) {
                    $data[$row['user_id'] . ":" . $row['name']] = $campaign_results;
                }
                foreach ($results as $k => $row) {
                    $total = $row['total'];
                    $data[$row['user_id'] . ":" . $row['name']][$row['campaign_id'] . ":"] = array(
                        "campaign" => $row['campaign_name'],
                        "user" => $row['name'],
                        "user_id" => $row['user_id'],
                        "count" => $row['count'],
                        "total_url" => $url . "/user/" . $row['user_id'],
                        "url" => $url . "/user/" . $row['user_id'] . '/pot/' . (empty($row['campaign_id']) ? "null" : $row['campaign_id']),
                        "color" => get_color($row['campaign_group_id'])
                    );
                    isset($campaign_total[$row['campaign_id']]) ? $campaign_total[$row['campaign_id']] += $row['count'] : 0;
                    //this builds the campaign totals by creating a "user" called total and adding the counts
                    $data["Totals"][$row['campaign_id'] . ":"] = array(
                        "campaign" => $row['campaign_name'],
                        "user" => "Total",
                        "user_id" => 0,
                        "count" => $campaign_total[$row['campaign_id']],
                        "total_url" => $url . "/user/" . $row['user_id'],
                        "grand_total_url" => $url,
                        "url" => $url . '/pot/' . (empty($row['campaign_id']) ? "null" : $row['campaign_id']),
                        "color" => get_color($row['campaign_group_id'])
                    );

                    $totals[$row['user_id']] += $row['count'];
                    $data_array = $data;
                }
            }
            //the grand total
            $totals[0] = $total;

            //return the data
            echo json_encode(array(
                "success" => true,
                "data" => $data_array,
                "total" => $total,
                "totals" => $totals,
                "total_url" => $total_url
            ));
        }

    }

    public function data()
    {

        $campaigns_by_group = $this->Form_model->get_user_campaigns_ordered_by_group();
        $aux = array();
        foreach ($campaigns_by_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $campaigns_by_group = $aux;

        $current_campaign = (isset($_SESSION['current_campaign']) ? array($_SESSION['current_campaign']) : array());
        $campaign_outcomes = $this->Form_model->get_outcomes_by_campaign_list($current_campaign);

        $aux = array(
            "Positive" => array(),
            "Not_Positive" => array(),
        );
        foreach ($campaign_outcomes as $outcome) {
            if ($outcome['positive']) {
                array_push($aux['Positive'], $outcome);
            } else {
                array_push($aux['Not_Positive'], $outcome);
            }
        }
        $campaign_outcomes = $aux;

        $agents = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        $sources = $this->Form_model->get_sources_by_campaign_list($current_campaign);
        $data_pot = $this->Form_model->get_pots_by_campaign_list($current_campaign);
		$title = "Data Report";
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' => $title,
            'page' => 'data'
        ,
            'javascript' => array(
                'charts.js',
                'report/data.js',
               
                'lib/daterangepicker.js'
            ),
            'campaigns_by_group' => $campaigns_by_group,
            'campaign_outcomes' => $campaign_outcomes,
            'team_managers' => $teamManagers,
            'agents' => $agents,
            'sources' => $sources,
            'data_pot' => $data_pot,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            ),
			'submenu' => array("file"=>'default_submenu.php',"title"=>$title)
        );
        $this->template->load('default', 'reports/data.php', $data);
    }


    //this controller loads the view for the targets page on the dashboard
    public function reports()
    {

        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' => 'Reports | Targets',
            'page' => 'targets'
        ,
            'javascript' => array(
                'charts.js',
                'report/targets.js',
               
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

    //this controller loads the view for the capture page on the reports
    public function capture()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
        $teamManagers = $this->Form_model->get_teams();
        $sources = $this->Form_model->get_sources();
        $data_pot = $this->Form_model->get_pots();
        $agents = $this->Form_model->get_agents();
        $outcomes = $this->Form_model->get_outcomes();
		$title = "Data Capture Report";
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' => $title,
            'page' => 'data_capture',
            'campaigns' => $campaigns,
            'sources' => $sources,
            'data_pot' => $data_pot,
            'team_managers' => $teamManagers,
            'agents' => $agents,
            'javascript' => array(
                'report/data_capture.js',
               
                'lib/daterangepicker.js'

            ),
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            ),
			'submenu' => array("file"=>'default_submenu.php',"title"=>$title),
        );
        $this->template->load('default', 'reports/data_capture.php', $data);
    }


    //this controller loads the view for the targets page on the dashboard
    public function targets()
    {
		$title = "Targets Report";
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' => $title,
            'page' => 'targets'
        ,
            'javascript' => array(
                'charts.js',
                'report/targets.js',
               
                'lib/daterangepicker.js'

            ),
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            ),
			'submenu' => array("file"=>'default_submenu.php',"title"=>$title)
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
		$title = "Survey Answer Report";
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' => 'Reports | Answers',
            'page' => 'answers'
        ,
            'javascript' => array(
                'charts.js',
                'report/answers.js'
            ),
            'surveys' => $surveys,
            'answers' => $results,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css'
            ),
			'submenu' => array("file"=>'answers_report.php',"title"=>$title)
        );
        $this->template->load('default', 'reports/answers.php', $data);
    }

    //this controller displays the answers data in JSON format. It gets called by the javascript function "answers_panel" 
    public function answers_data()
    {
        if ($this->input->is_ajax_request()) {
            $survey = intval($this->input->post('survey'));
            $results = $this->Report_model->answers_data($survey);
            foreach ($results as $k => $v) {
                //create the url for the click throughs
                $perfects = base_url() . "search/custom/records/question/" . $v['question_id'] . "/survey/" . $v['survey_info_id'] . "/score/10";
                $lows = base_url() . "search/custom/records/question/" . $v['question_id'] . "/survey/" . $v['survey_info_id'] . "/score/7:less";
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
        $campaigns_by_group = $this->Form_model->get_user_campaigns_ordered_by_group();
        $aux = array();
        foreach ($campaigns_by_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $campaigns_by_group = $aux;

        $current_campaign = (isset($_SESSION['current_campaign']) ? array($_SESSION['current_campaign']) : array());
        $campaign_outcomes = $this->Form_model->get_outcomes_by_campaign_list($current_campaign);

        $aux = array(
            "Positive" => array(),
            "Not_Positive" => array(),
        );
        foreach ($campaign_outcomes as $outcome) {
            if ($outcome['positive']) {
                array_push($aux['Positive'], $outcome);
            } else {
                array_push($aux['Not_Positive'], $outcome);
            }
        }
        $campaign_outcomes = $aux;

        $report_filter = $this->Filter_model->build_filter_options(true);
		$report_filter['campaigns'] = $this->_campaigns;
		$title = "Activity Report";
        $data = array(
			'report_filter'=>$report_filter,
            'campaign_access' => $this->_campaigns,
            'title' => $title,
            'page' => 'activity',
            'javascript' => array(
                'charts.js',
				'report/reports.js',
                'report/activity.js',
               
                'lib/daterangepicker.js'
            ),
                'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            ),
			'submenu' => array("file"=>'default_submenu.php',"title"=>$title)
        );
        $this->template->load('default', 'reports/activity.php', $data);
    }

    //this controller sends the activity data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function activity_data()
    {
        if ($this->input->is_ajax_request()) {
            $data = array();
            $data_array = array();
            $total = 0;
			$field_assoc = array("date_from"=>"h.contact",
			"date_to"=>"h.contact",
			"user_id"=>"h.user_id",
			"campaign_id"=>"h.campaign_id",
			"team_id"=>"h.team_id",
			"outcome_id"=>"h.outcome_id",
			"pot_id"=>"h.pot_id",
			"source_id"=>"h.source_id",
			"branch_id"=>"r.branch_id",
			"region_id"=>"r.region_id",
			"group_id"=>"h.group_id");
			
			$query_params = $this->Report_model->build_filter_query($this->input->post(),$field_assoc);
            $results = $this->Report_model->get_activity($query_params);
            $date_from = $this->input->post("date_from");
            $date_to = $this->input->post("date_to");
            $users = $this->input->post("agents");
            $campaigns = $this->input->post("campaigns");
            $teams = $this->input->post("teams");
            $pots = $this->input->post("pots");
			$branch = $this->input->post("branches");
            $region = $this->input->post("regions");


            $overall_array = array();
            $post = $this->input->post();
            if ($this->input->post('teams') || $this->input->post('agents')) {
                $colname = $this->input->post('colname');
                unset($post['teams']);
                unset($post['agents']);
                $overall = $this->Report_model->get_activity($post,$field_assoc);
                $overall_array = array();
                foreach ($overall as $k => $row) {
                    $overall_array[$row['outcome']]["overall_total"] = $row['total'];
                    $overall_array[$row['outcome']]["overall"] = (isset($row['total']) ? number_format(($row['count'] / $row['total']) * 100, 1) : "-");
                }
            }
            $url = base_url() . "search/custom/history";
            $url .= (!empty($campaigns) ? "/hcampaign/" . implode("_", $campaigns) . (count($campaigns) > 1 ? ":in" : "") : "");
            $url .= (!empty($users) ? "/user/" . implode("_", $users) . (count($users) > 1 ? ":in" : "") : "");
            $url .= (!empty($date_from) ? "/contact-from/$date_from" : "");
            $url .= (!empty($date_to) ? "/contact-to/$date_to" : "");
            $url .= (!empty($teams) ? "/team/" . implode("_", $teams) . (count($teams) > 1 ? ":in" : "") : "");
            $url .= (!empty($sources) ? "/hsource/" . implode("_", $sources) . (count($sources) > 1 ? ":in" : "") : "");
            $total_url = $url;
            foreach ($results as $k => $row) {
                $total = $row['total'];
                $pc = (isset($row['total']) ? number_format(($row['count'] / $row['total']) * 100, 1) : "-");
                $data = array(
                    "outcome" => $row['outcome'],
                    "count" => $row['count'],
                    "pc" => $pc,
                    "url" => $url . "/outcome/" . $row['outcome']
                );
                if (isset($overall_array[$row['outcome']]["overall"])) {
                    $data["overall"] = $overall_array[$row['outcome']]["overall"];
                    $data["colname"] = $colname;
                }
                $data['colour'] = substr(dechex(crc32($row['outcome'])), 0, 6);
                $data_array[] = $data;
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data_array,
                "total" => $total,
                "total_url" => $total_url
            ));
        }
    }


    //this is the controller loads the initial view for the campaign appointment report dashboard
    public function outcomes()
    {
        if ($this->uri->segment(3) == "campaign") {
            $group = "campaign";
        } else if ($this->uri->segment(3) == "agent") {
            $group = "agent";
        } else if ($this->uri->segment(3) == "date") {
            $group = "date";
        } else if ($this->uri->segment(3) == "time") {
            $group = "time";
        } else if ($this->uri->segment(3) == "outcome_reason") {
            $group = "reason";
        } else {
            $group = "campaign";
        }

        if (intval($this->uri->segment(4))) {
            $outcome_id = $this->uri->segment(4);
        } else {
            $outcome_id = "70";
        }

        $campaigns_by_group = $this->Form_model->get_user_campaigns_ordered_by_group();
        $aux = array();
        foreach ($campaigns_by_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $campaigns_by_group = $aux;

        $current_campaign = (isset($_SESSION['current_campaign']) ? array($_SESSION['current_campaign']) : array());

        $teamManagers = $this->Form_model->get_teams();
        $sources = $this->Form_model->get_sources_by_campaign_list($current_campaign);
        $data_pot = $this->Form_model->get_pots_by_campaign_list($current_campaign);
        $agents = $this->Form_model->get_agents();

        $campaign_outcomes = $this->Form_model->get_outcomes_by_campaign_list($current_campaign);

        $aux = array(
            "Positive" => array(),
            "Not_Positive" => array(),
        );
        foreach ($campaign_outcomes as $outcome) {
            if ($outcome['positive']) {
                array_push($aux['Positive'], $outcome);
            } else {
                array_push($aux['Not_Positive'], $outcome);
            }
        }
        $campaign_outcomes = $aux;
		$title = "Performance Report";
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' => $title,
            'page' => "outcome_report_$group",
            'javascript' => array(
                'charts.js',
                'report/outcomes.js',
               
                'lib/daterangepicker.js'
            ),
            'group' => $group,
            'outcome_id' => $outcome_id,
            'campaign_outcomes' => $campaign_outcomes,
            'campaigns_by_group' => $campaigns_by_group,
            'sources' => $sources,
            'data_pot' => $data_pot, 'team_managers' => $teamManagers,
            'agents' => $agents,
            'css' => array(
                'dashboard.css',
                'daterangepicker-bs3.css'
            ),
			'submenu' => array("file"=>'outcomes_report.php',"page"=>$group,"title"=>$title)
        );
        $this->template->load('default', 'reports/outcomes.php', $data);
    }

    //this controller sends the campaign appointment report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function outcome_data()
    {
        if ($this->input->is_ajax_request()) {
            $data = array();

            $form = $this->input->post();
            $form["date_from"] = ($this->input->post("date_from")) ? $this->input->post("date_from") : date('Y-m-d', strtotime("2014-07-02"));
            $form["date_to"] = ($this->input->post("date_to")) ? $this->input->post("date_to") : date('Y-m-d');
            $results = $this->Report_model->get_outcome_data($form);

            $date_from_search = $form["date_from"];
            $date_to_search = $form["date_to"];
            $agent_search = (isset($form["agents"]) ? $form["agents"] : array());
            $campaign_search = (isset($form["campaigns"]) ? $form["campaigns"] : array());
            $team_search = (isset($form["teams"]) ? $form["teams"] : array());
            $source_search = (isset($form["sources"]) ? $form["sources"] : array());
            $outcome_ids = isset($form["outcomes"]) ? $form["outcomes"] : array();
            $group = $form["group"];


            $outcome = "Outcomes";
            if (!empty($outcome_ids)) {
                $this->db->where_in("outcome_id", $outcome_ids);
                $query = $this->db->get("outcomes");
                if ($query->num_rows() > 0) {
                    if ($query->num_rows() < 2) {
                        $outcome = $query->row()->outcome;
                    }
                }
            }


            $aux = array();
            foreach ($results as $row) {
                if ($row['total_dials']) {
                    if (($group == "date") || ($group == "time")) {
                        $aux[$row['id']]['sql'] = $row['sql'];
                        $aux[$row['id']]['group'] = $group;
                    }
                    $aux[$row['id']]['name'] = $row['name'];
                    $aux[$row['id']]['colour'] = substr(dechex(crc32($row['name'])), 0, 6);
                    $aux[$row['id']]['duration'] = $row['duration'];
                    $aux[$row['id']]['exceptions'] = $row['exceptions'];
                    $aux[$row['id']]['rate_duration'] = $row['duration'] - $row['exceptions'];
                    $aux[$row['id']]['outcomes'] = $row['outcome_count'];
                    $aux[$row['id']]['total_dials'] = $row['total_dials'];
                }
            }

            $totalOutcomes = 0;
            $totalDials = 0;
            $totalDuration = 0;
            $totalExceptions = 0;
            $url = base_url() . "search/custom/history";
            $url .= (!empty($agent_search) ? "/user/" . implode("_", $agent_search) . (count($agent_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($campaign_search) ? "/hcampaign/" . implode("_", $campaign_search) . (count($campaign_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($date_from_search) ? "/contact-from/$date_from_search" : "");
            $url .= (!empty($date_to_search) ? "/contact-to/$date_to_search" : "");
            $url .= (!empty($team_search) ? "/team/" . implode("_", $team_search) . (count($team_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($source_search) ? "/hsource/" . implode("_", $source_search) . (count($source_search) > 1 ? ":in" : "") : "");
            if ($group == "date") {
                $group = "contact";
            }
            foreach ($aux as $id => $row) {
                $outcomes = (array_key_exists('outcomes', $row)) ? $row['outcomes'] : 0;
                //create the click through hyperlinks
                if ($group == "contact") {
                    $allDialsUrl = $url . "/outcome/null:not/contact/" . $row['sql'];
                    $outcomesUrl = $allDialsUrl . "/outcome/" . implode("_", $form['outcomes']) . (count($form['outcomes']) > 1 ? ":in" : "");
                } else if ($group == "time") {
                    $allDialsUrl = $url . "/outcome/null:not/time/" . $row['sql'];
                    $outcomesUrl = $allDialsUrl . "/outcome/" . implode("_", $form['outcomes']) . (count($form['outcomes']) > 1 ? ":in" : "");
                } else if ($group == "agent") {
                    $allDialsUrl = $url . "/outcome/null:not/user/" . $id;
                    $outcomesUrl = $allDialsUrl . "/outcome/" . implode("_", $form['outcomes']) . (count($form['outcomes']) > 1 ? ":in" : "");
                } else if ($group == "reason") {
                    $allDialsUrl = $url . "/outcome/null:not/reason/" . $id;
                    $outcomesUrl = $allDialsUrl . "/outcome/" . implode("_", $form['outcomes']) . (count($form['outcomes']) > 1 ? ":in" : "");
                } else {
                    $allDialsUrl = $url . "/outcome/null:not/alldials/" . $id;
                    $outcomesUrl = $url . "/outcome/null:not/hcampaign/" . $id . "/outcome/" . implode("_", $form['outcomes']) . (count($form['outcomes']) > 1 ? ":in" : "");
                }

                $data[] = array(
                    "id" => $id,
                    "name" => $row['name'],
                    "colour" => $row['colour'],
                    "outcomes" => $outcomes,
                    "outcomes_url" => $outcomesUrl,
                    "total_dials" => $row['total_dials'],
                    "total_dials_url" => $allDialsUrl,
                    "duration" => ($row['duration']) ? $row['duration'] : 0,
                    "exceptions" => ($row['exceptions']) ? $row['exceptions'] : 0,
                    "rate" => ($row['rate_duration'] > 0) ? round(($outcomes) / ($row['rate_duration'] / 3600), 3) : 0,
                    "dials_rate" => ($row['rate_duration'] > 0) ? round(($row['total_dials']) / ($row['rate_duration'] / 3600), 2) : 0,
                    "group" => $group
                );
                $totalOutcomes += $outcomes;
                $totalDials += ($row['total_dials'] ? $row['total_dials'] : "0");
                $totalDuration += ($row['duration'] ? $row['duration'] : "0");
                $totalExceptions += ($row['exceptions'] ? $row['exceptions'] : "0");
            }

            $totalOutcomesPercent = ($totalDials) ? number_format(($totalOutcomes * 100) / $totalDials, 2) : 0;

            $totalPercent = ($totalDials) ? number_format((($totalOutcomes) * 100) / $totalDials, 2) : 0;

            $url .= (!empty($campaign_search) ? "/hcampaign/" . implode("_", $campaign_search) . (count($campaign_search) > 1 ? ":in" : "") : "");
            $url .= ($group == "reason" ? "/reason/null:not" : "");

            array_push($data, array(
                "id" => "TOTAL",
                "name" => "",
                //"outcomes" => $totalOutcomes . " (" . $totalOutcomesPercent . "%)",
                "outcomes" => $totalOutcomes,
                "outcomes_url" => $url . "/outcome/$outcome",
                "total_dials" => $totalDials,
                "total_dials_url" => $url,
                "duration" => $totalDuration,
                "exceptions" => $totalExceptions,
                "rate" => (($totalDuration - $totalExceptions) > 0) ? round(($totalOutcomes) / (($totalDuration - $totalExceptions) / 3600), 3) : 0,
                "dials_rate" => (($totalDuration - $totalExceptions) > 0) ? round(($totalDials) / (($totalDuration - $totalExceptions) / 3600), 2) : 0,
                "group" => $group
            ));

            echo json_encode(array(
                "success" => true,
                "outcome" => $outcome,
                "data" => $data,
                "group" => $group
            ));
        }
    }

    /**
     * Get the outcomes for the filter by campaign list
     */
    public function get_outcomes_filter()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $campaigns = (isset($form['campaign_id']) ? $form['campaign_id'] : array());

            $campaign_outcomes = $this->Form_model->get_outcomes_by_campaign_list($campaigns);

            $aux = array(
                "Positive" => array(),
                "Not_Positive" => array(),
            );
            foreach ($campaign_outcomes as $outcome) {
                if ($outcome['positive']) {
                    array_push($aux['Positive'], $outcome);
                } else {
                    array_push($aux['Not_Positive'], $outcome);
                }
            }
            $campaign_outcomes = $aux;

            echo json_encode(array(
                "success" => true,
                "campaign_outcomes" => $campaign_outcomes
            ));
        }
    }
	
	  /**
     * Get the branch for the filter by campaign list
     */
	    public function get_branches_filter()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $campaigns = (isset($form['campaign_id']) ? $form['campaign_id'] : array());

            $campaign_branches = $this->Form_model->get_branch_by_campaign_list($campaigns);

            echo json_encode(array(
                "success" => true,
                "campaign_branches" => $campaign_branches
            ));
        }
    }
	
	 /**
     * Get the users for the filter by campaign list
     */
    public function get_users_filter()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $campaigns = (isset($form['campaign_id']) ? $form['campaign_id'] : array());
            $campaign_users = $this->Form_model->get_users_by_campaign_list($campaigns);

            echo json_encode(array(
                "success" => true,
                "campaign_users" => $campaign_users
            ));
        }
    }

    /**
     * Get the sources for the filter by campaign list
     */
    public function get_sources_filter()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $campaigns = (isset($form['campaign_id']) ? $form['campaign_id'] : array());

            $campaign_sources = $this->Form_model->get_sources_by_campaign_list($campaigns);

            echo json_encode(array(
                "success" => true,
                "campaign_sources" => $campaign_sources
            ));
        }
    }

    /**
     * Get the pots for the filter by campaign list
     */
    public function get_pots_filter()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $campaigns = (isset($form['campaign_id']) ? $form['campaign_id'] : array());

            $campaign_pots = $this->Form_model->get_pots_by_campaign_list($campaigns);

            echo json_encode(array(
                "success" => true,
                "campaign_pots" => $campaign_pots
            ));
        }
    }

    //this is the controller loads the initial view for the email reports
    public function email()
    {
        if ($this->uri->segment(3) == "campaign") {
            $group = "campaign";
        } else if ($this->uri->segment(3) == "user") {
            $group = "user";
        } else if ($this->uri->segment(3) == "date") {
            $group = "date";
        } else if ($this->uri->segment(3) == "time") {
            $group = "time";
        } else if ($this->uri->segment(3) == "template") {
            $group = "template";
        } else {
            $group = "campaign";
        }

        $campaigns_by_group = $this->Form_model->get_user_campaigns_ordered_by_group();
        $aux = array();
        foreach ($campaigns_by_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $campaigns_by_group = $aux;

        $templates_by_campaign_group = $this->Form_model->get_templates_ordered_by_campaign_group();
        $aux = array();
        foreach ($templates_by_campaign_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $templates_by_campaign_group = $aux;

        $current_campaign = (isset($_SESSION['current_campaign']) ? array($_SESSION['current_campaign']) : array());

        $teamManagers = $this->Form_model->get_teams();
        $sources = $this->Form_model->get_sources_by_campaign_list($current_campaign);
        $data_pot = $this->Form_model->get_pots_by_campaign_list($current_campaign);
        $agents = $this->Form_model->get_agents();
		$title = "Email Report";
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' =>$title,
            'page' => "email_report_$group",
            'javascript' => array(
                'charts.js',
                'report/email.js',
               
                'lib/daterangepicker.js'
            ),
            'group' => $group,
            'templates_by_campaign_group' => $templates_by_campaign_group,
            'campaigns_by_group' => $campaigns_by_group,
            'sources' => $sources,
            'data_pot' => $data_pot, 'team_managers' => $teamManagers,
            'agents' => $agents,
            'css' => array(
                'dashboard.css',
                'daterangepicker-bs3.css'
            ),
			'submenu' => array("file"=>'email_report.php',"page"=>$group,"title"=>$title)
        );

        $this->template->load('default', 'reports/email.php', $data);
    }


    //this controller sends the sms report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function email_data()
    {
        if ($this->input->is_ajax_request()) {
            $data = array();

            $form = $this->input->post();
            $form["date_from"] = ($this->input->post("date_from")) ? $this->input->post("date_from") : date('Y-m-d', strtotime("2014-07-02"));
            $form["date_to"] = ($this->input->post("date_to")) ? $this->input->post("date_to") : date('Y-m-d');
            $results = $this->Report_model->get_email_data($form);

            $date_from_search = $form["date_from"];
            $date_to_search = $form["date_to"];
            $agent_search = (isset($form["agents"]) ? $form["agents"] : array());
            $campaign_search = (isset($form["campaigns"]) ? $form["campaigns"] : array());
            $template_search = (isset($form["templates"]) ? $form["templates"] : array());
            $team_search = (isset($form["teams"]) ? $form["teams"] : array());
            $source_search = (isset($form["sources"]) ? $form["sources"] : array());
            $group = $form["group"];


            $aux = array();
            foreach ($results as $row) {
                if ($row['email_sent_count']) {
                    $aux[$row['id']]['sql'] = $row['sql'];
                    $aux[$row['id']]['name'] = $row['name'];
                    $aux[$row['id']]['emails_read'] = $row['email_read_count'];
                    $aux[$row['id']]['emails_pending'] = $row['email_pending_count'];
                    $aux[$row['id']]['emails_unsent'] = $row['email_unsent_count'];
                    $aux[$row['id']]['emails_sent'] = $row['email_sent_count'];
                }
            }

            $totalEmailsRead = 0;
            $totalEmailPending = 0;
            $totalEmailUnsent = 0;
            $totalEmailSent = 0;
            $emails_read = 0;
            $url = base_url() . "search/custom/records";
            $url .= (!empty($agent_search) ? "/user/" . implode("_", $agent_search) . (count($agent_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($template_search) ? "/template/" . implode("_", $template_search) . (count($template_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($campaign_search) ? "/campaign/" . implode("_", $campaign_search) . (count($campaign_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($date_from_search) ? "/sent-email-from/$date_from_search" : "");
            $url .= (!empty($date_to_search) ? "/sent-email-to/$date_to_search" : "");
            $url .= (!empty($team_search) ? "/team/" . implode("_", $team_search) . (count($team_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($source_search) ? "/source/" . implode("_", $source_search) . (count($source_search) > 1 ? ":in" : "") : "");
            if ($group == "date") {
                $group = "contact";
            }
            foreach ($aux as $id => $row) {
                $emails_read = (array_key_exists('emails_read', $row)) ? $row['emails_read'] : 0;
                $emails_unsent = (array_key_exists('emails_unsent', $row)) ? $row['emails_unsent'] : 0;
                $emails_pending = (array_key_exists('emails_pending', $row)) ? $row['emails_pending'] : 0;
                //create the click through hyperlinks
                if ($group == "contact") {
                    $emailUrl = $url . "/sent-email-date/" . $row['sql'];
                } else if ($group == "time") {
                    $emailUrl = $url . "/sent-email-time/" . $row['sql'];
                } else if ($group == "user") {
                    $emailUrl = $url . "/user-email-sent-id/" . $id;
                } else if ($group == "campaign") {
                    $emailUrl = $url . "/campaign/" . $id;
                } else {
                    $emailUrl = $url . "/allemails/" . $id;
                }

                $data[] = array(
                    "id" => $id,
                    "sql" => $row['sql'],
                    "name" => $row['name'],
                    "colour" => substr(dechex(crc32($row['name'])), 0, 6),
                    "emails_read" => $emails_read,
                    "emails_read_url" => $emailUrl . "/emails/read",
                    "emails_pending" => $emails_pending,
                    "emails_pending_url" => $emailUrl . "/emails/pending",
                    "emails_unsent" => $emails_unsent,
                    "emails_unsent_url" => $emailUrl . "/emails/unsent",
                    "emails_sent" => $row['emails_sent'],
                    "emails_sent_url" => $emailUrl . "/emails/sent",
                    "percent_read" => (($row['emails_sent'] > 0) ? number_format(($emails_read * 100) / ($row['emails_sent']), 2) : 0) . "%",
                    "percent_pending" => (($emails_pending > 0) ? number_format(($emails_pending * 100) / ($row['emails_sent']), 2) : 0) . "%",
                    "percent_unsent" => (($row['emails_sent'] > 0) ? number_format(($emails_unsent * 100) / ($row['emails_sent']), 2) : 0) . "%",
                    "group" => $group
                );
                $totalEmailsRead += $emails_read;
                $totalEmailUnsent += $emails_unsent;
                $totalEmailPending += $emails_pending;
                $totalEmailSent += ($row['emails_sent'] ? $row['emails_sent'] : "0");
            }

            $totalEmailsReadPercent = ($totalEmailSent) ? number_format(($totalEmailsRead * 100) / $totalEmailSent, 2) : 0;
            $totalEmailsPendingPercent = ($totalEmailPending) ? number_format(($totalEmailPending * 100) / $totalEmailSent, 2) : 0;
            $totalEmailsUnsentPercent = ($totalEmailSent) ? number_format(($totalEmailUnsent * 100) / $totalEmailSent, 2) : 0;


            //$url .= (!empty($campaign_search) ? "/campaign/$campaign_search" : "");

            array_push($data, array(
                "id" => "TOTAL",
                "sql" => "TOTAL",
                "name" => "",
                "emails_read" => $totalEmailsRead,
                "emails_read_url" => $url . "/emails/read",
                "emails_pending" => $totalEmailPending,
                "emails_pending_url" => $url . "/emails/pending",
                "emails_unsent" => $totalEmailUnsent,
                "emails_unsent_url" => $url . "/emails/unsent",
                "emails_sent" => $totalEmailSent,
                "emails_sent_url" => $url . "/emails/sent",
                "percent_read" => $totalEmailsReadPercent . "%",
                "percent_pending" => $totalEmailsPendingPercent . "%",
                "percent_unsent" => $totalEmailsUnsentPercent . "%",
                "group" => $group
            ));

            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }

    public function contact_counts()
    {
        //get number of records loaded into the system
        echo "2241 Matrix Starting Companies";
        echo "<br>";
        //get the number of contacts added on the day the data was loaded
        $first_contacts = $this->db->query("select count(*) result from contacts where date_created < '2015-01-21 14:00'")->row()->result;
        echo "$first_contacts Matrix Starting Contacts";
        echo "<hr><br>";
        //get the number of records marked as data captured
        $captured = $this->db->query("select count(*) result from records where campaign_id = 5 and outcome_id = 89")->row()->result;
        echo "$captured called resulted in Data Captured";
        echo "<br>";
        //get the number of records mared as data captured
        $contacts = $this->db->query("select count(*) result from contacts left join records using(urn) where campaign_id = 5")->row()->result;
        echo "$contacts number of contacts now";
        echo "<br>";
        //contacts added
        echo $contacts - $first_contacts . " new contacts have been added";
        echo "<br>";
        //records have had details updated

        echo $captured - ($contacts - $first_contacts) . " records have had contact details updated";
        echo "<br>";
    }


    //this is the controller loads the initial view for the productivity report
    public function productivity()
    {
        $campaigns_by_group = $this->Form_model->get_user_campaigns_ordered_by_group();
        $aux = array();
        foreach ($campaigns_by_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $campaigns_by_group = $aux;

        $agents = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        $outcomes = $this->Form_model->get_outcomes();

        $current_campaign = (isset($_SESSION['current_campaign']) ? array($_SESSION['current_campaign']) : array());
        $campaign_outcomes = $this->Form_model->get_outcomes_by_campaign_list($current_campaign);

        $aux = array(
            "Positive" => array(),
            "Not_Positive" => array(),
        );
        foreach ($campaign_outcomes as $outcome) {
            if ($outcome['positive']) {
                array_push($aux['Positive'], $outcome);
            } else {
                array_push($aux['Not_Positive'], $outcome);
            }
        }
        $campaign_outcomes = $aux;

        $sources = $this->Form_model->get_sources_by_campaign_list($current_campaign);
        $data_pot = $this->Form_model->get_pots_by_campaign_list($current_campaign);
		$title = "Productivity";
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' =>$title,
            'page' => 'productivity',
            'javascript' => array(
                'charts.js',
                'report/productivity.js',
               
                'lib/daterangepicker.js'
            ),
            'team_managers' => $teamManagers,
            'agents' => $agents,
            'sources' => $sources,
            'data_pot' => $data_pot, 'campaign_outcomes' => $campaign_outcomes,
            'campaigns_by_group' => $campaigns_by_group,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            ),
				'submenu' => array("file"=>'default_submenu.php',"title"=>$title)
        );
        $this->template->load('default', 'reports/productivity.php', $data);
    }


    //this controller sends the productivity data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function productivity_data()
    {
        if ($this->input->is_ajax_request()) {
            $total = 0;
            $results = $this->Report_model->get_productivity($this->input->post());

            $aux = array();
            foreach ($results as $row) {
                $row['colour'] = substr(dechex(crc32($row['agent'])), 0, 6);
                array_push($aux, $row);
            }
            $results = $aux;

            $outcomes = $this->input->post('outcomes');
            $campaigns = $this->input->post('campaigns');
            $teams = $this->input->post('teams');
            $sources = $this->input->post('sources');
            $pots = $this->input->post('pots');

            $outcomes_url = (!empty($outcomes) ? "/outcome/" . implode("_", $outcomes) . (count($outcomes) > 1 ? ":in" : "") : "");
            $campaigns_url = (!empty($campaigns) ? "/campaign/" . implode("_", $campaigns) . (count($campaigns) > 1 ? ":in" : "") : "");
            $teams_url = (!empty($teams) ? "/team/" . implode("_", $teams) . (count($teams) > 1 ? ":in" : "") : "");
            $sources_url = (!empty($sources) ? "/source/" . implode("_", $sources) . (count($sources) > 1 ? ":in" : "") : "");
            $pots_url = (!empty($pots) ? "/pot/" . implode("_", $pots) . (count($pots) > 1 ? ":in" : "") : "");
            $filter_url = $outcomes_url . $campaigns_url . $teams_url . $sources_url . $pots_url;

            $outcome_colname = "Outcomes";
            if (!empty($outcomes)) {
                $this->db->where_in("outcome_id", $outcomes);
                $query = $this->db->get("outcomes");
                if ($query->num_rows() > 0) {
                    if ($query->num_rows() < 2) {
                        $outcome_colname = $query->row()->outcome;
                    }
                }
            }

            echo json_encode(array(
                "success" => (!empty($results)),
                "outcome_colname" => $outcome_colname,
                "data" => $results,
                "filter_url" => $filter_url,
                "total" => $total,
                "msg" => (empty($results) ? "No results found" : "")
            ));
        }
    }

    //this is the controller loads the initial view for the realtime report
    public function realtime()
    {
        $agents = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        $outcomes = $this->Form_model->get_outcomes();
        $campaigns = $this->Form_model->get_campaigns();
        $teams = $this->Form_model->get_teams();
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' => 'Reports | Realtime',
            'page' => 'realtime',
            'javascript' => array(
                'charts.js',
                'report/realtime.js',
               
                'lib/daterangepicker.js'
            ),
            'team_managers' => $teamManagers,
            'agents' => $agents,
            'campaigns' => $campaigns,
            'outcomes' => $outcomes,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'reports/realtime.php', $data);
    }

    public function realtime_data()
    {
        if ($this->input->is_ajax_request()) {
            $data = array();
            $total = 0;
            $history = $this->Report_model->get_realtime_history($this->input->post());
            $hours = $this->Report_model->get_realtime_hours($this->input->post());
            $hours_logged = $this->Report_model->get_realtime_hours_logged($this->input->post());

            //$this->firephp->log($history);
            //$this->firephp->log($hours);
            //$this->firephp->log($hours_logged);
            if (count($history > 0)) {
                foreach ($history as $row) {
                    $row['duration'] = 0;
                    $row['dph'] = 0;
                    $data[$row['user_id']] = $row;
                }
            }
            if (count($hours > 0)) {
                foreach ($hours as $row) {
                    if (!isset($data[$row['user_id']])) {
                        $data[$row['user_id']] = $row;
                        $data[$row['user_id']]['count'] = 0;
                    }
                    $data[$row['user_id']]['duration'] += $row['duration'];
                }
            }
            if (count($hours_logged > 0)) {
                foreach ($hours_logged as $row) {
                    if (!isset($data[$row['user_id']])) {
                        $data[$row['user_id']] = $row;
                    }
                    $data[$row['user_id']]['duration'] += $row['duration'];
                }
            }

            foreach ($data as $k => $row) {
                if ($row['duration'] > 0) {
                    $this->load->helper('date');
                    $data[$k]['dph'] = number_format($row['count'] / ($row['duration'] / 3600), 2);
                    $data[$k]['duration'] = timespan(0, $row['duration']);
                }
            }

            echo json_encode(array(
                "success" => (!empty($data)),
                "data" => $data,
                "total" => $total,
                "msg" => (empty($results) ? "No results found" : "")
            ));
        }
    }


    public function capture_data()
    {

        $results = $this->Report_model->get_audit_data($this->input->post());
        echo json_encode(array(
            "success" => true,
            "data" => $results,
            "msg" => "No results found"
        ));
    }


    //this is the controller loads the initial view for the last outcomes report
    public function last_outcomes()
    {
        $campaigns_by_group = $this->Form_model->get_user_campaigns_ordered_by_group();
        $aux = array();
        foreach ($campaigns_by_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $campaigns_by_group = $aux;

        $current_campaign = (isset($_SESSION['current_campaign']) ? array($_SESSION['current_campaign']) : array());
        $campaign_outcomes = $this->Form_model->get_outcomes_by_campaign_list($current_campaign);

        $aux = array(
            "Positive" => array(),
            "Not_Positive" => array(),
        );
        foreach ($campaign_outcomes as $outcome) {
            if ($outcome['positive']) {
                array_push($aux['Positive'], $outcome);
            } else {
                array_push($aux['Not_Positive'], $outcome);
            }
        }
        $campaign_outcomes = $aux;

        $sources = $this->Form_model->get_sources_by_campaign_list($current_campaign);
        $data_pot = $this->Form_model->get_pots_by_campaign_list($current_campaign);
		$title = "Last Outcome Report";
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' => $title,
            'page' => 'client_report_outcomes',
            'javascript' => array(
                'charts.js',
                'report/last_outcomes.js',
               
                'lib/daterangepicker.js'
            ),
            'sources' => $sources,
            'data_pot' => $data_pot,
            'campaign_outcomes' => $campaign_outcomes,
            'campaigns_by_group' => $campaigns_by_group,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            ),
			'submenu' => array("file"=>'default_submenu.php',"title"=>$title)
        );
        $this->template->load('default', 'reports/last_outcomes.php', $data);
    }


    //this controller sends the last outcomes data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function last_outcomes_data()
    {
        if ($this->input->is_ajax_request()) {
            $total_records = $this->Report_model->get_total_records($this->input->post());
            $aux = array();
            foreach ($total_records as $row) {
                if (!isset($aux['total'])) {
                    $aux['total'] = 0;
                }
                $row['colour'] = substr(dechex(crc32($row['num_dials'])), 0, 6);
                array_push($aux, $row);
                $aux['total'] += $row['num'];
            }
            $total_records = $aux;

            $last_outcomes = $this->Report_model->get_last_outcomes($this->input->post());
            $aux = array();
            foreach ($last_outcomes as $row) {
                if (!isset($aux[$row['status']])) {
                    $aux[$row['status']] = array();
                    $aux[$row['status']]['total'] = 0;
                }
                $data = array(
                    'outcome' => $row['outcome'],
                    'outcome_id' => $row['outcome_id'],
                    'num' => $row['num'],
                    'colour' => substr(dechex(crc32($row['outcome'])), 0, 6)
                );
                array_push($aux[$row['status']], $data);
                $aux[$row['status']]['total'] += $row['num'];
            }
            $last_outcomes = $aux;

            $outcomes = $this->input->post('outcomes');
            $campaigns = $this->input->post('campaigns');
            $sources = $this->input->post('sources');
            $pots = $this->input->post('pots');

            $outcomes_url = (!empty($outcomes) ? "/outcome/" . implode("_", $outcomes) . (count($outcomes) > 1 ? ":in" : "") : "");
            $campaigns_url = (!empty($campaigns) ? "/campaign/" . implode("_", $campaigns) . (count($campaigns) > 1 ? ":in" : "") : "");
            $sources_url = (!empty($sources) ? "/source/" . implode("_", $sources) . (count($sources) > 1 ? ":in" : "") : "");
            $pots_url = (!empty($pots) ? "/pot/" . implode("_", $pots) . (count($pots) > 1 ? ":in" : "") : "");

            $filter_url = $outcomes_url . $campaigns_url . $sources_url . $pots_url;

            echo json_encode(array(
                "success" => (!empty($total_records) || !empty($last_outcomes)),
                "total_records" => $total_records,
                "last_outcomes" => $last_outcomes,
                "filter_url" => $filter_url,
                "msg" => (empty($results) ? "No results found" : "")
            ));
        }
    }

    //this is the controller loads the initial view for the dials report
    public function dials()
    {
        $campaigns_by_group = $this->Form_model->get_user_campaigns_ordered_by_group();
        $aux = array();
        foreach ($campaigns_by_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $campaigns_by_group = $aux;

        $current_campaign = (isset($_SESSION['current_campaign']) ? array($_SESSION['current_campaign']) : array());
        $campaign_outcomes = $this->Form_model->get_outcomes_by_campaign_list($current_campaign);

        $aux = array(
            "Positive" => array(),
            "Not_Positive" => array(),
        );
        foreach ($campaign_outcomes as $outcome) {
            if ($outcome['positive']) {
                array_push($aux['Positive'], $outcome);
            } else {
                array_push($aux['Not_Positive'], $outcome);
            }
        }
        $campaign_outcomes = $aux;

        $sources = $this->Form_model->get_sources_by_campaign_list($current_campaign);
        $data_pot = $this->Form_model->get_pots_by_campaign_list($current_campaign);
$title = "Dials Report";
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' => $title,
            'page' => 'client_report_dials',
            'javascript' => array(
                'charts.js',
                'report/dials_report.js',
               
                'lib/daterangepicker.js'
            ),
            'sources' => $sources,
            'data_pot' => $data_pot,
            'campaign_outcomes' => $campaign_outcomes,
            'campaigns_by_group' => $campaigns_by_group,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            ),
				'submenu' => array("file"=>'default_submenu.php',"title"=>$title)
        );
        $this->template->load('default', 'reports/dials.php', $data);
    }


    //this controller sends the dials data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function dials_data()
    {
        if ($this->input->is_ajax_request()) {
            $total_dials = $this->Report_model->get_total_dials($this->input->post());
            $aux = array();
            foreach ($total_dials as $row) {
                if (!isset($aux['total'])) {
                    $aux['total'] = 0;
                }
                $row['colour'] = substr(dechex(crc32($row['contact'])), 0, 6);
                array_push($aux, $row);
                $aux['total'] += $row['num'];
            }
            $total_dials = $aux;

            $dials_by_outcome = $this->Report_model->get_dials_by_outcome($this->input->post());
            $aux = array();
            foreach ($dials_by_outcome as $row) {
                if (!isset($aux[$row['contact']])) {
                    $aux[$row['contact']] = array();
                    $aux[$row['contact']]['total'] = 0;
                }
                $data = array(
                    'outcome' => $row['outcome'],
                    'outcome_id' => $row['outcome_id'],
                    'num' => $row['num'],
                    'colour' => substr(dechex(crc32($row['outcome'])), 0, 6)
                );
                array_push($aux[$row['contact']], $data);
                $aux[$row['contact']]['total'] += $row['num'];
            }
            $dials_by_outcome = $aux;

            $outcomes = $this->input->post('outcomes');
            $campaigns = $this->input->post('campaigns');
            $sources = $this->input->post('sources');
            $pots = $this->input->post('pots');

            $outcomes_url = (!empty($outcomes) ? "/outcome/" . implode("_", $outcomes) . (count($outcomes) > 1 ? ":in" : "") : "");
            $campaigns_url = (!empty($campaigns) ? "/campaign/" . implode("_", $campaigns) . (count($campaigns) > 1 ? ":in" : "") : "");
            $sources_url = (!empty($sources) ? "/source/" . implode("_", $sources) . (count($sources) > 1 ? ":in" : "") : "");
            $pots_url = (!empty($pots) ? "/pot/" . implode("_", $pots) . (count($pots) > 1 ? ":in" : "") : "");

            $filter_url = $outcomes_url . $campaigns_url . $sources_url . $pots_url;

            echo json_encode(array(
                "success" => (!empty($total_dials) || !empty($dials_by_outcome)),
                "total_dials" => $total_dials,
                "dials_by_outcome" => $dials_by_outcome,
                "filter_url" => $filter_url,
                "msg" => (empty($results) ? "No results found" : "")
            ));
        }
    }

    //this is the controller loads the initial view for the sms reports
    public function sms()
    {
        if ($this->uri->segment(3) == "campaign") {
            $group = "campaign";
        } else if ($this->uri->segment(3) == "agent") {
            $group = "agent";
        } else if ($this->uri->segment(3) == "date") {
            $group = "date";
        } else if ($this->uri->segment(3) == "time") {
            $group = "time";
        } else if ($this->uri->segment(3) == "template") {
            $group = "template";
        } else {
            $group = "campaign";
        }
		  $campaigns_by_group = $this->Form_model->get_user_campaigns_ordered_by_group();
        $aux = array();
        foreach ($campaigns_by_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $campaigns_by_group = $aux;
		
        $templates_by_campaign_group = $this->Form_model->get_sms_templates_ordered_by_campaign_group();
        $aux = array();
        foreach ($templates_by_campaign_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $templates_by_campaign_group = $aux;

        $current_campaign = (isset($_SESSION['current_campaign']) ? array($_SESSION['current_campaign']) : array());
        $teamManagers = $this->Form_model->get_teams();
        $sources = $this->Form_model->get_sources();
        $data_pot = $this->Form_model->get_pots();
        $agents = $this->Form_model->get_agents();
		$title = "SMS Report";
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' => $title,
            'page' => "sms_report_$group",
            'javascript' => array(
                'charts.js',
                'report/sms.js',
               
                'lib/daterangepicker.js'
            ),
			'campaigns_by_group' => $campaigns_by_group,
			'templates_by_campaign_group' => $templates_by_campaign_group,
            'group' => $group,
            'sources' => $sources,
            'data_pot' => $data_pot, 'team_managers' => $teamManagers,
            'agents' => $agents,
            'css' => array(
                'dashboard.css',
                'daterangepicker-bs3.css'
            ),
			'submenu' => array("file"=>'sms_report.php',"page"=>$group,"title"=>$title)
        );

        $this->template->load('default', 'reports/sms.php', $data);
    }


    //this controller sends the sms report data back the page in JSON format. It ran when the page loads and any time the filter is changed
    public function sms_data()
    {
        if ($this->input->is_ajax_request()) {
            $data = array();

            $form = $this->input->post();
            $form["date_from"] = ($this->input->post("date_from")) ? $this->input->post("date_from") : date('Y-m-d', strtotime("2014-07-02"));
            $form["date_to"] = ($this->input->post("date_to")) ? $this->input->post("date_to") : date('Y-m-d');
            $results = $this->Report_model->get_sms_data($form);

                  $date_from_search = $form["date_from"];
            $date_to_search = $form["date_to"];
            $agent_search = (isset($form["agents"]) ? $form["agents"] : array());
            $campaign_search = (isset($form["campaigns"]) ? $form["campaigns"] : array());
            $template_search = (isset($form["templates"]) ? $form["templates"] : array());
            $team_search = (isset($form["teams"]) ? $form["teams"] : array());
            $source_search = (isset($form["sources"]) ? $form["sources"] : array());
            $group = $form["group"];


            $aux = array();
            foreach ($results as $row) {
                if ($row['sms_sent_count']) {
                    $aux[$row['id']]['sql'] = $row['sql'];
                    $aux[$row['id']]['name'] = ($row['name'] ? $row['name'] : ($group === 'user' ? 'Automatic' : ''));
                    $aux[$row['id']]['credits'] = $row['credits'];
                    $aux[$row['id']]['sms_sent'] = $row['sms_sent_count'];
                    $aux[$row['id']]['sms_delivered'] = $row['sms_delivered_count'];
                    $aux[$row['id']]['sms_pending'] = $row['sms_pending_count'];
                    $aux[$row['id']]['sms_undelivered'] = $row['sms_undelivered_count'];
                    $aux[$row['id']]['sms_unknown'] = $row['sms_unknown_count'];
                    $aux[$row['id']]['sms_error'] = $row['sms_error_count'];
                }
            }

            $totalSmsSent = 0;
            $totalCredits = 0;
            $totalSmsDelivered = 0;
            $totalSmsPending = 0;
            $totalSmsUndelivered = 0;
            $totalSmsUnknown = 0;
            $totalSmsError = 0;
            $totalSmsUnsent = 0;
			 $url = base_url() . "search/custom/records";
           $url .= (!empty($agent_search) ? "/user_sms_sent_id/" . implode("_", $agent_search) . (count($agent_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($template_search) ? "/template/" . implode("_", $template_search) . (count($template_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($campaign_search) ? "/campaign/" . implode("_", $campaign_search) . (count($campaign_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($date_from_search) ? "/sent-sms-from/$date_from_search" : "");
            $url .= (!empty($date_to_search) ? "/sent-sms-to/$date_to_search" : "");
            $url .= (!empty($team_search) ? "/team/" . implode("_", $team_search) . (count($team_search) > 1 ? ":in" : "") : "");
            $url .= (!empty($source_search) ? "/source/" . implode("_", $source_search) . (count($source_search) > 1 ? ":in" : "") : "");
            if ($group == "date") {
                $group = "contact";
            }
            foreach ($aux as $id => $row) {
                $sms_sent = (array_key_exists('sms_sent', $row)) ? $row['sms_sent'] : 0;
                $sms_credits = (array_key_exists('credits', $row)) ? $row['credits'] : 0;
                $sms_delivered = (array_key_exists('sms_delivered', $row)) ? $row['sms_delivered'] : 0;
                $sms_pending = (array_key_exists('sms_pending', $row)) ? $row['sms_pending'] : 0;
                $sms_undelivered = (array_key_exists('sms_undelivered', $row)) ? $row['sms_undelivered'] : 0;
                $sms_unknown = (array_key_exists('sms_unknown', $row)) ? $row['sms_unknown'] : 0;
                $sms_error = (array_key_exists('sms_error', $row)) ? $row['sms_error'] : 0;
                //create the click through hyperlinks
                if ($group == "contact") {
                    $smsUrl = $url . "/sent-sms-date/" . $row['sql'];
                } else if ($group == "time") {
                    $smsUrl = $url . "/sent-smss-time/" . $row['sql'];
                } else if ($group == "user") {
                    $smsUrl = $url . "/user-sms-sent-id/" . $id;
                } else if ($group == "campaign") {
                    $smsUrl = $url . "/campaign/" . $id;
                } else {
                    $smsUrl = $url . "/allsms/" . $id;
                }

                $data[] = array(
                    "id" => $id,
                    "sql" => $row['sql'],
                    "name" => $row['name'],
                    "credits" => $sms_credits,
                    "sms_sent" => $sms_sent,
                    "sms_sent_url" => $smsUrl,
                    "sms_delivered" => $sms_delivered,
                    "sms_delivered_url" => $smsUrl . "/sms-status/" . SMS_STATUS_SENT,
                    "sms_pending" => $sms_pending,
                    "sms_pending_url" => $smsUrl . "/sms-status/" . SMS_STATUS_PENDING,
                    "sms_undelivered" => $sms_undelivered,
                    "sms_undelivered_url" => $smsUrl . "/sms-status/" . SMS_STATUS_UNDELIVERED,
                    "sms_unknown" => $sms_unknown,
                    "sms_unknown_url" => $smsUrl . "/sms-status/" . SMS_STATUS_UNKNOWN,
                    "sms_error" => $sms_error,
                    "sms_error_url" => $smsUrl . "/sms-status/" . SMS_STATUS_ERROR,
                    "percent_sent" => (($sms_delivered > 0) ? number_format(($sms_delivered * 100) / $sms_sent, 2) : 0) . "%",
                    "percent_pending" => (($sms_pending + $sms_unknown > 0) ? number_format((($sms_pending + $sms_unknown) * 100) / $sms_sent, 2) : 0) . "%",
                    "percent_unsent" => (($sms_error + $sms_undelivered > 0) ? number_format((($sms_error + $sms_undelivered) * 100) / $sms_sent, 2) : 0) . "%",
                    "group" => $group
                );
                $totalSmsSent += $sms_sent;
                $totalCredits += $sms_credits;
                $totalSmsDelivered += $sms_delivered;
                $totalSmsPending += $sms_pending;
                $totalSmsUndelivered += $sms_undelivered;
                $totalSmsUnknown += $sms_unknown;
                $totalSmsError += $sms_error;
            }

            $totalSmsPendingPercent = ($totalSmsPending || $totalSmsUnknown) ? number_format((($totalSmsPending + $totalSmsUnknown) * 100) / $totalSmsSent, 2) : 0;
            $totalSmsSentPercent = ($totalSmsDelivered) ? number_format(($totalSmsDelivered * 100) / $totalSmsSent, 2) : 0;
            $totalSmsUnsentPercent = ($totalSmsError || $totalSmsUndelivered) ? number_format((($totalSmsError + $totalSmsUndelivered) * 100) / $totalSmsSent, 2) : 0;


        $url .= (!empty($campaign_search) ? "/campaign/" . implode("_", $campaign_search) . (count($campaign_search) > 1 ? ":in" : "") : "");

            array_push($data, array(
                "id" => "TOTAL",
                "sql" => "TOTAL",
                "name" => "",
                "sms_sent" => $totalSmsSent,
                "credits" => $totalCredits,
                "sms_sent_url" => $url,
                "sms_delivered" => $totalSmsDelivered,
                "sms_delivered_url" => $url . "/sms-status/" . SMS_STATUS_SENT,
                "sms_pending" => $totalSmsPending,
                "sms_pending_url" => $url . "/sms-status/" . SMS_STATUS_PENDING,
                "sms_undelivered" => $totalSmsUndelivered,
                "sms_undelivered_url" => $url . "/sms-status/" . SMS_STATUS_UNDELIVERED,
                "sms_unknown" => $totalSmsUnknown,
                "sms_unknown_url" => $url . "/sms-status/" . SMS_STATUS_UNKNOWN,
                "sms_error" => $totalSmsError,
                "sms_error_url" => $url . "/sms-status/" . SMS_STATUS_ERROR,
                "percent_sent" => $totalSmsSentPercent . "%",
                "percent_pending" => $totalSmsPendingPercent . "%",
                "percent_unsent" => $totalSmsUnsentPercent . "%",
                "group" => $group
            ));

            echo json_encode(array(
                "success" => true,
                "data" => $data
            ));
        }
    }

    /**
     * Get the current campaign on the session if it is selected
     */
    public function get_current_campaign()
    {
        $current_campaign = (isset($_SESSION['current_campaign_name']) ? $_SESSION['current_campaign_name'] : NULL);

        echo json_encode(array(
            "success" => ($current_campaign),
            "current_campaign" => $current_campaign
        ));
    }

    /* data counts for the data report */
    public function data_counts()
    {
        if ($this->input->is_ajax_request()) {
            $data = array();
            $data_array = array();
            $total = 0;
            $results = $this->Report_model->get_data_counts($this->input->post());
            $date_from = $this->input->post("date_from");
            $date_to = $this->input->post("date_to");
            $users = $this->input->post("agents");
            $campaigns = $this->input->post("campaigns");
            $teams = $this->input->post("teams");
            $pots = $this->input->post("pots");


            $overall_array = array();
            $post = $this->input->post();
            if ($this->input->post('teams') || $this->input->post('agents')) {
                $colname = $this->input->post('colname');
                unset($post['teams']);
                unset($post['agents']);
                $overall = $this->Report_model->get_data_counts($post);
                $overall_array = array();
                foreach ($overall as $k => $row) {
                    $overall_array[$row['name']]["overall_total"] = $row['total'];
                    $overall_array[$row['name']]["overall"] = (isset($row['total']) ? number_format(($row['count'] / $row['total']) * 100, 1) : "-");
                }
            }
            foreach ($results as $k => $row) {
                $url = base_url() . "search/custom/records";
                $url .= (!empty($campaigns) ? "/campaign/" . implode("_", $campaigns) . (count($campaigns) > 1 ? ":in" : "") : "");
                $url .= (!empty($users) ? "/user/" . implode("_", $users) . (count($users) > 1 ? ":in" : "") : "");
                $url .= (!empty($date_from) ? "/updated-from/$date_from" : "");
                $url .= (!empty($date_to) ? "/updated-to/$date_to" : "");
                $url .= (!empty($teams) ? "/team/" . implode("_", $teams) . (count($teams) > 1 ? ":in" : "") : "");
                $url .= (!empty($sources) ? "/hsource/" . implode("_", $sources) . (count($sources) > 1 ? ":in" : "") : "");
                //tr,ta,tp,va,vp,wa,wp,fd,fc
                $total += $row['tr'];
                $data = array(
                    "name" => $row['name'],
                    "id" => $row['id'],
                    "status" => $row['campaign_status'],
                    "tr" => empty($row['tr']) ? 0 : $row['tr'],
                    "ta" => empty($row['ta']) ? 0 : $row['ta'],
                    "tp" => empty($row['tp']) ? 0 : $row['tp'],
                    "va" => empty($row['va']) ? 0 : $row['va'],
                    "vp" => empty($row['vp']) ? 0 : $row['vp'],
                    "wa" => empty($row['wa']) ? 0 : $row['wa'],
                    "wp" => empty($row['wp']) ? 0 : $row['wp'],
                    "fd" => empty($row['fd']) ? 0 : $row['fd'],
                    "fc" => empty($row['fc']) ? 0 : $row['fc'],
                    "url_tr" => $url . "/campaign/" . $row['id'],
                    "url_ta" => $url . "/campaign/" . $row['id'] . "/status/1/parked-code/null",
                    "url_tp" => $url . "/campaign/" . $row['id'] . "/status/1/parked-code/null:not",
                    "url_va" => $url . "/campaign/" . $row['id'] . "/dials/0/status/1/parked-code/null",
                    "url_vp" => $url . "/campaign/" . $row['id'] . "/dials/0/status/1/parked-code/null:not",
                    "url_wa" => $url . "/campaign/" . $row['id'] . "/dials/0:more/parked-code/null/status/1",
                    "url_wp" => $url . "/campaign/" . $row['id'] . "/dials/0:more/parked-code/null:not/status/1",
                    "url_fd" => $url . "/campaign/" . $row['id'] . "/status/3",
                    "url_fc" => $url . "/campaign/" . $row['id'] . "/status/4",
                );
                $data['colour'] = substr(dechex(crc32($row['name'])), 0, 6);
                $data_array[] = $data;
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data_array,
                "total" => number_format($total, 0)
            ));
        }
    }


    /* data counts for the data report */
    public function data_counts_by_pot()
    {
        if ($this->input->is_ajax_request()) {
            $data = array();
            $data_array = array();
            $total = 0;
            $results = $this->Report_model->get_data_counts_by_pot($this->input->post());
            $date_from = $this->input->post("date_from");
            $date_to = $this->input->post("date_to");
            $users = $this->input->post("agents");
            $campaigns = $this->input->post("campaigns");
            $teams = $this->input->post("teams");
            $pots = $this->input->post("pots");
            $campaign = $this->input->post("campaign");

            $overall_array = array();
            $post = $this->input->post();
            if ($this->input->post('teams') || $this->input->post('agents')) {
                $colname = $this->input->post('colname');
                unset($post['teams']);
                unset($post['agents']);
                $overall = $this->Report_model->get_data_counts($post);
                $overall_array = array();
                foreach ($overall as $k => $row) {
                    $overall_array[$row['name']]["overall_total"] = $row['total'];
                    $overall_array[$row['name']]["overall"] = (isset($row['total']) ? number_format(($row['count'] / $row['total']) * 100, 1) : "-");
                }
            }

            foreach ($results as $k => $row) {
                $url = base_url() . "search/custom/records";
                $url .= (!empty($campaigns) ? "/campaign/" . implode("_", $campaigns) . (count($campaigns) > 1 ? ":in" : "") : "");
                $url .= (!empty($users) ? "/user/" . implode("_", $users) . (count($users) > 1 ? ":in" : "") : "");
                $url .= (!empty($date_from) ? "/updated-from/$date_from" : "");
                $url .= (!empty($date_to) ? "/updated-to/$date_to" : "");
                $url .= (!empty($teams) ? "/team/" . implode("_", $teams) . (count($teams) > 1 ? ":in" : "") : "");
                $url .= (!empty($sources) ? "/hsource/" . implode("_", $sources) . (count($sources) > 1 ? ":in" : "") : "");
                //tr,ta,tp,va,vp,wa,wp,fd,fc
                $total += $row['tr'];
                $data = array(
                    "name" => $row['name'],
                    "id" => $row['id'],
                    "status" => $row['campaign_status'],
                    "tr" => empty($row['tr']) ? 0 : $row['tr'],
                    "ta" => empty($row['ta']) ? 0 : $row['ta'],
                    "tp" => empty($row['tp']) ? 0 : $row['tp'],
                    "va" => empty($row['va']) ? 0 : $row['va'],
                    "vp" => empty($row['vp']) ? 0 : $row['vp'],
                    "wa" => empty($row['wa']) ? 0 : $row['wa'],
                    "wp" => empty($row['wp']) ? 0 : $row['wp'],
                    "fd" => empty($row['fd']) ? 0 : $row['fd'],
                    "fc" => empty($row['fc']) ? 0 : $row['fc'],
                    "url_tr" => $url . "/pot/" . $row['id'] . "/campaign/" . $campaign,
                    "url_ta" => $url . "/pot/" . $row['id'] . "/campaign/" . $campaign . "/status/1/parked-code/null",
                    "url_tp" => $url . "/pot/" . $row['id'] . "/campaign/" . $campaign . "/status/1/parked-code/null:not",
                    "url_va" => $url . "/pot/" . $row['id'] . "/campaign/" . $campaign . "/dials/0/status/1/parked-code/null",
                    "url_vp" => $url . "/pot/" . $row['id'] . "/campaign/" . $campaign . "/dials/0/status/1/parked-code/null:not",
                    "url_wa" => $url . "/pot/" . $row['id'] . "/campaign/" . $campaign . "/dials/0:more/parked-code/null/status/1",
                    "url_wp" => $url . "/pot/" . $row['id'] . "/campaign/" . $campaign . "/dials/0:more/parked-code/null:not/status/1",
                    "url_fd" => $url . "/pot/" . $row['id'] . "/campaign/" . $campaign . "/status/3",
                    "url_fc" => $url . "/pot/" . $row['id'] . "/campaign/" . $campaign . "/status/4",
                );
                $data['colour'] = substr(dechex(crc32($row['name'])), 0, 6);
                $data_array[] = $data;
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data_array,
                "total" => number_format($total, 0)
            ));
        }
    }

	public function appointment_summary(){
			$title="Appointment Summary";
		
		     $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Reports',
            'title' =>$title,
            'page' => "appointment_summary",
            'javascript' => array(
                'charts.js',
                'report/appointment_summary.js',
               
                'lib/daterangepicker.js'
            ),
            'css' => array(
                'dashboard.css',
                'daterangepicker-bs3.css'
            ),
			'submenu' => array("file"=>'default_submenu.php',"title"=>$title)
        );
        $this->template->load('default', 'reports/appointment_summary.php', $data);
		
		
	}

}
