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

  public function pending_tasks()
    {
		$tasks = $this->Dashboard_model->pending_tasks();
		echo json_encode(array("success"=>true,"data"=>$tasks));
	}

    //this laods the user dashboard view
    public function ghs()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
        $agents = $this->Form_model->get_agents();

        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => 'ghs_dash',
            'javascript' => array(
                'charts.js',
                'dashboard.js',
                'lib/moment.js',
                'lib/daterangepicker.js',
                'dashboards/ghs.js',
            ),
            'agents' => $agents,
            'campaigns' => $campaigns,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/ghs_dash.php', $data);
    }

    //this laods the user dashboard view
    public function eldon()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
        $agents = $this->Form_model->get_agents();

        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => 'Eldon',
            'javascript' => array(
                'charts.js',
                'dashboard.js',
                'lib/moment.js',
                'lib/daterangepicker.js',
                'dashboards/eldon.js',
            ),
            'agents' => $agents,
            'campaigns' => $campaigns,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/eldon_dash.php', $data);
    }

    //this laods the hsl dashboard view
    public function hsl()
    {
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => 'hsl_dash',
            'javascript' => array(
                'charts.js',
                'dashboard.js',
                'lib/moment.js',
                'lib/daterangepicker.js',
                'dashboards/hsl.js',
            ),
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/hsl_dash.php', $data);
    }


    //this laods the user dashboard view  
    public function user_dash()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
        $email_campaigns = $this->Form_model->get_user_email_campaigns();
        $sms_campaigns = $this->Form_model->get_user_sms_campaigns();
        $surveys = $this->Form_model->get_surveys();
        $agents = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        $sources = $this->Form_model->get_sources();

        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => 'overview',
            'javascript' => array(
                'charts.js',
                'dashboard.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
            'agents' => $agents,
            'team_managers' => $teamManagers,
            'sources' => $sources,
            'email_campaigns' => $email_campaigns,
            'sms_campaigns' => $sms_campaigns,
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
        $agents = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        $sources = $this->Form_model->get_sources();
        $campaigns = $this->Form_model->get_user_campaigns();
        $type = $this->uri->segment(3);
        if ($type == "missed") {
            $date_from = date('2014-07-02');
            $date_to = date('Y-m-d H:s');
            $btntext = "Missed";
        } else if ($type == "upcoming") {
            $date_from = date('Y-m-d H:s');
            $date_to = date('2020-01-01'); //if i'm not here in 5 years this might break :O
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
            'page' => 'callback_dash',
            'javascript' => array(
                'charts.js',
                'dashboard.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
            'date_from' => $date_from,
            'date_to' => $date_to,
            'btntext' => $btntext,
            'campaigns' => $campaigns,
            'sources' => $sources,
            'agents' => $agents,
            'team_managers' => $teamManagers,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/callbacks.php', $data);
    }

    //this is the controller loads the initial view for the favorites
    public function favorites()
    {
        $agents = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        $sources = $this->Form_model->get_sources();
        $campaigns = $this->Form_model->get_user_campaigns();
        $type = $this->uri->segment(3);
        if ($type == "missed") {
            $date_from = date('2014-07-02');
            $date_to = date('Y-m-d H:s');
            $btntext = "Missed";
        } else if ($type == "upcoming") {
            $date_from = date('Y-m-d H:s');
            $date_to = date('2020-01-01'); //if i'm not here in 5 years this might break :O
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
            'page' => 'favorites_dash',
            'javascript' => array(
                'charts.js',
                'dashboard.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
            'date_from' => $date_from,
            'date_to' => $date_to,
            'btntext' => $btntext,
            'campaigns' => $campaigns,
            'sources' => $sources,
            'agents' => $agents,
            'team_managers' => $teamManagers,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/favorites.php', $data);
    }

    //this is the controller loads the initial view for the activity dashboard
    public function agent()
    {
        header('location:' . base_url() . 'dashboard/callbacks');
    }

    //this is the controller loads the initial view for the client dashboard
    public function client()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
        $surveys = $this->Form_model->get_surveys();

        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => 'client_dash',
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
        $campaigns = $this->Form_model->get_user_campaigns();

        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => 'nbf_dash',
            'campaigns' => $campaigns,
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
        $agents = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        $sources = $this->Form_model->get_sources();
        $campaigns = $this->Form_model->get_user_campaigns();

        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => 'management_dash',
            'javascript' => array(
                'charts.js',
                'dashboard.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
            'campaigns' => $campaigns,
            'sources' => $sources,
            'agents' => $agents,
            'team_managers' => $teamManagers,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/management_dash.php', $data);
    }


    //this controller sends the history data back the page in JSON format. It ran when the javascript function "history_panel" is executed
    public function get_history()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
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
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
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
                "date" => date('Y-m-d')
            ));
        }
    }

    //this controller sends the system stats data back the page in JSON format. It ran when the javascript function "system_stats" is executed
    public function system_stats()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $data = $this->Dashboard_model->system_stats($filter);
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
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $data = $this->Dashboard_model->get_comments($filter);
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
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $data = $this->Dashboard_model->get_urgent($filter);
            foreach ($data as $k => $v) {
                $comment = $this->Records_model->get_last_comment($v['urn']);
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
            $data = $this->Dashboard_model->get_pending($filter);
            foreach ($data as $k => $v) {
                $comment = $this->Records_model->get_last_comment($v['urn']);
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
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $data = $this->Dashboard_model->get_appointments($filter);
            foreach ($data as $k => $v) {
                $comment = $this->Records_model->get_last_comment($v['urn']);
                $data[$k]['last_comment'] = (!empty($comment) ? $comment : "No Comment Found");
            }
            echo json_encode(array(
                "success" => true,
                "data" => $data,
                "msg" => "No records found"
            ));
        }

    }

    public function get_appointments_by_region_and_week()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->helper('date');

            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }

            $date_from = new \DateTime("now");
            $date_to = new \DateTime("now + 6 week - 1 day");
            $filter['date_from'] = $date_from->format("Y-m-d");
            $filter['date_to'] = $date_to->format("Y-m-d");

            //Get the appointments for the next 6 weeks grouped by region
            $num_appointments_set = $this->Dashboard_model->get_num_appointments_set($filter);

            $aux = array();
            $weeks = array();
            foreach ($num_appointments_set as $val) {
                if (!isset($aux[$val['region_id']])) {
                    $aux[$val['region_id']] = array(
                        'region_name' => $val['region_name']
                    );
                }

                $week = explode("/", $val['week']);
                $key = $val['week'];

                $aux[$val['region_id']][$key] = array(
                    'num_appointments' => $val['num_appointments']
                );

                $val['week'] = getStartAndEndDateByMonth($week[1], $week[0]);
                $weeks[$key] = $val['week'];
            }
            $data = $aux;


            //Get the rest weeks where there is no appointments
            for ($i = 0; $i < 6; $i++) {
                $date = new \DateTime(strtotime($date_from->format("U")) . " + " . $i . " week");
                if (!isset($weeks[$date->format("Y/W")])) {
                    $weeks[$date->format("Y/W")] = getStartAndEndDateByMonth($date->format("W"), $date->format("Y"));
                }
            }
            ksort($weeks);

            //Get regions
            $regions = $this->Dashboard_model->get_branch_regions();
            foreach ($regions as $region) {
                if (!isset($data[$region['region_id']])) {
                    $data[$region['region_id']] = array(
                        "region_name" => $region['region_name']
                    );
                }
            }

            echo json_encode(array(
                "success" => (!empty($num_appointments_set)),
                "data" => $data,
                "weeks" => $weeks,
                "msg" => "No records found"
            ));
        }

    }

    //this controller sends the favorites records back the page in JSON format. It ran when the javascript function "favorites_panel" is executed
    public function get_favorites()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $data = $this->Dashboard_model->get_favorites($filter);
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
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
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
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
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
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
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
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
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
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $results = $this->Dashboard_model->agent_activity($filter);
            $now = time();
            foreach ($results as $k => $row) {
                $results[$k]['when'] = timespan(strtotime($row['when']), $now) . " ago";
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
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $results = $this->Dashboard_model->agent_success($filter);
            $now = time();
            foreach ($results as $k => $row) {
                $results[$k]['rate'] = number_format(($results[$k]['positives'] / $results[$k]['dials']) * 100, 1) . "%";
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
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $results = $this->Dashboard_model->agent_data($filter);
            $now = time();
            foreach ($results as $k => $row) {
                $results[$k]['pc_virgin'] = number_format(($results[$k]['virgin'] / $results[$k]['total']) * 100, 1) . "%";
                $results[$k]['pc_in_progress'] = number_format(($results[$k]['in_progress'] / $results[$k]['total']) * 100, 1) . "%";
                $results[$k]['pc_completed'] = number_format(($results[$k]['completed'] / $results[$k]['total']) * 100, 1) . "%";
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
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
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
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
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
            $filter = $this->input->post();
            $agents = $this->Form_model->get_agents();

            foreach ($agents as $agent) {
                if (empty($campaign_form)) {
                    $campaigns = $this->Form_model->get_campaigns_by_user($agent['id']);
                    foreach ($campaigns as $campaign) {
                        $duration = $this->User_model->get_duration_working($campaign['id'], $agent['id']);
                        if ($duration) {
                            $results[$agent['name']]['duration'] = $duration->secs;
                            $results[$agent['name']]['campaign'] = $duration->campaign_name;

                            $worked = $this->User_model->get_worked($campaign['id'], $agent['id']);
                            $results[$agent['name']]['worked'] = $worked;

                            $transfers = $this->User_model->get_positive($campaign['id'], $agent['id'], "Transfers");
                            $cross_transfers = $this->User_model->get_cross_transfers_by_campaign_destination($campaign['id'], $agent['id']);
                            $results[$agent['name']]['transfers'] = $transfers + $cross_transfers;

                            $rate = ($duration->secs > 0) ? number_format(($transfers + $cross_transfers) / ($duration->secs / 60 / 60), 2) : 0;
                            $results[$agent['name']]['rate'] = $rate;
                        }
                    }
                } else {
                    $duration = $this->User_model->get_duration_working($filter['campaign'], $agent['id']);
                    if ($duration) {
                        $results[$agent['name']]['duration'] = $duration->secs;
                        $results[$agent['name']]['campaign'] = $duration->campaign_name;

                        $worked = $this->User_model->get_worked($filter['campaign'], $agent['id']);
                        $results[$agent['name']]['worked'] = $worked;

                        $transfers = $this->User_model->get_positive($filter['campaign'], $agent['id'], "Transfers");
                        $cross_transfers = $this->User_model->get_cross_transfers_by_campaign_destination($filter['campaign'], $agent['id']);
                        $results[$agent['name']]['transfers'] = $transfers + $cross_transfers;

                        $rate = ($duration->secs > 0) ? number_format(($transfers + $cross_transfers) / ($duration->secs / 60 / 60), 2) : 0;
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

    public function get_email_stats()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $stats = $this->Dashboard_model->get_email_stats($filter);
            echo json_encode(array("success" => true, "data" => $stats));
            exit;
        }
    }

    public function get_sms_stats()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $stats = $this->Dashboard_model->get_sms_stats($filter);

            //TODAY stats
            $today_sms = count($stats['today']);
            $today_url = $stats['today_url'];
            $today_delivered_sms = 0;
            $today_delivered_url = $stats['today_url'].'/sms-status/'.SMS_STATUS_SENT;
            $today_undelivered_sms = 0;
            $today_undelivered_url = $stats['today_url'].'/sms-status/'.SMS_STATUS_UNDELIVERED;
            $today_pending_sms = 0;
            $today_pending_url = $stats['today_url'].'/sms-status/'.SMS_STATUS_PENDING;
            $today_unknown_sms = 0;
            $today_unknown_url = $stats['today_url'].'/sms-status/'.SMS_STATUS_UNKNOWN;
            $today_error_sms = 0;
            $today_error_url = $stats['today_url'].'/sms-status/'.SMS_STATUS_ERROR;
            foreach($stats['today'] as $sms) {
                switch($sms['status_id']) {
                    case SMS_STATUS_SENT:
                        $today_delivered_sms++;
                        break;
                    case SMS_STATUS_UNDELIVERED:
                        $today_undelivered_sms++;
                        break;
                    case SMS_STATUS_PENDING:
                        $today_pending_sms++;
                        break;
                    case SMS_STATUS_UNKNOWN:
                        $today_unknown_sms++;
                        break;
                    case SMS_STATUS_ERROR:
                        $today_error_sms++;
                        break;
                }
            }

            //ALL stats
            $all_sms = count($stats['all']);
            $all_url = $stats['all_url'];
            $all_delivered_sms = 0;
            $all_delivered_url = $stats['all_url'].'/sms-status/'.SMS_STATUS_SENT;
            $all_undelivered_sms = 0;
            $all_undelivered_url = $stats['all_url'].'/sms-status/'.SMS_STATUS_UNDELIVERED;
            $all_pending_sms = 0;
            $all_pending_url = $stats['all_url'].'/sms-status/'.SMS_STATUS_PENDING;
            $all_unknown_sms = 0;
            $all_unknown_url = $stats['all_url'].'/sms-status/'.SMS_STATUS_UNKNOWN;
            $all_error_sms = 0;
            $all_error_url = $stats['all_url'].'/sms-status/'.SMS_STATUS_ERROR;
            foreach($stats['all'] as $sms) {
                switch($sms['status_id']) {
                    case SMS_STATUS_SENT:
                        $all_delivered_sms++;
                        break;
                    case SMS_STATUS_UNDELIVERED:
                        $all_undelivered_sms++;
                        break;
                    case SMS_STATUS_PENDING:
                        $all_pending_sms++;
                        break;
                    case SMS_STATUS_UNKNOWN:
                        $all_unknown_sms++;
                        break;
                    case SMS_STATUS_ERROR:
                        $all_error_sms++;
                        break;
                }
            }


            $stats = array(
                "today_sms" => $today_sms,
                "today_url" => $today_url,
                "today_delivered_sms" => $today_delivered_sms,
                "today_delivered_urn" => $today_delivered_url,
                "today_undelivered_sms" => $today_undelivered_sms,
                "today_undelivered_url" => $today_undelivered_url,
                "today_pending_sms" => $today_pending_sms,
                "today_pending_url" => $today_pending_url,
                "today_unknown_sms" => $today_unknown_sms,
                "today_unknown_url" => $today_unknown_url,
                "today_error_sms" => $today_error_sms,
                "today_error_url" => $today_error_url,
                "all_sms" => $all_sms,
                "all_url" => $all_url,
                "all_delivered_sms" => $all_delivered_sms,
                "all_delivered_url" => $all_delivered_url,
                "all_undelivered_sms" => $all_undelivered_sms,
                "all_undelivered_url" => $all_undelivered_url,
                "all_pending_sms" => $all_pending_sms,
                "all_pending_url" => $all_pending_url,
                "all_unknown_sms" => $all_unknown_sms,
                "all_unknown_url" => $all_unknown_url,
                "all_error_sms" => $all_error_sms,
                "all_error_url" => $all_error_url,
            );

            echo json_encode(array(
                "success" => true,
                "data" => $stats
            ));

            exit;
        }
    }

    public function overdue_visits()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign'] = $_SESSION['current_campaign'];
            }
            $data = $this->Dashboard_model->overdue_visits($filter);
            foreach ($data as $k => $row) {
                $data[$k]['last_update'] = time_elapsed_string(strtotime($row['date_updated']));
            }
            echo json_encode(array("success" => true, "data" => $data));
        }
    }

}
