<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
		session_write_close();
        $this->_campaigns = campaign_access_dropdown();
        $this->project_version = $this->config->item('project_version');
		check_page_permissions('view dashboard');
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Dashboard_model');
        $this->load->model('Export_model');
        $this->load->model('User_model');
        unset($_SESSION['navigation']);
    }
	

public function index(){
  $this->user_dash();
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
            'page' => array("dashboard"=>"overview"),
            'javascript' => array(
                'charts.js?v' . $this->project_version,
                'dashboard.js?v' . $this->project_version,
                'lib/moment.js',
                'lib/daterangepicker.js',
                'dashboards/ghs.js?v' . $this->project_version,
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
            'page' => array("dashboard"=>"eldon"),
            'javascript' => array(
                'charts.js?v' . $this->project_version,
                'dashboard.js?v' . $this->project_version,
                'lib/moment.js',
                'lib/daterangepicker.js',
                'dashboards/eldon.js?v' . $this->project_version,
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
             'page' => array("dashboard"=>"hsl_dash"),
            'javascript' => array(
                'charts.js?v' . $this->project_version,
                'dashboard.js?v' . $this->project_version,
                'lib/moment.js',
                'lib/daterangepicker.js',
                'dashboards/hsl.js?v' . $this->project_version
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
        $email_campaigns = $this->Form_model->get_user_email_campaigns();
        $sms_campaigns = $this->Form_model->get_user_sms_campaigns();
        $surveys = $this->Form_model->get_surveys();
        $agents = $this->Form_model->get_agents();
        $teamManagers = $this->Form_model->get_teams();
        $sources = $this->Form_model->get_sources();


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
            "positive" => array(),
            "No_positive" => array(),
        );
        foreach ($campaign_outcomes as $outcome) {
            if ($outcome['positive']) {
                array_push($aux['positive'], $outcome);
            } else {
                array_push($aux['No_positive'], $outcome);
            }
        }
        $campaign_outcomes = $aux;

        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Dashboard',
            'title' => 'Dashboard',
             'page' => array("dashboard"=>"overview"),
            'javascript' => array(
                'charts.js?v' . $this->project_version,
                'dashboard.js?v' . $this->project_version,
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
            'agents' => $agents,
            'team_managers' => $teamManagers,
            'sources' => $sources,
            'email_campaigns' => $email_campaigns,
            'sms_campaigns' => $sms_campaigns,
            'campaigns_by_group' => $campaigns_by_group,
            'campaign_outcomes' => $campaign_outcomes,
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
            'page' => array('dashboard'=>'callback_dash'),
            'javascript' => array(
                'charts.js?v' . $this->project_version,
                'dashboard.js?v' . $this->project_version,
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
            "positive" => array(),
            "No_positive" => array(),
        );
        foreach ($campaign_outcomes as $outcome) {
            if ($outcome['positive']) {
                array_push($aux['positive'], $outcome);
            } else {
                array_push($aux['No_positive'], $outcome);
            }
        }
        $campaign_outcomes = $aux;

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
               'page' => array('dashboard'=>'favorites_dash'),
            'javascript' => array(
                'charts.js?v' . $this->project_version,
                'dashboard.js?v' . $this->project_version,
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
            'date_from' => $date_from,
            'date_to' => $date_to,
            'btntext' => $btntext,
            'campaigns_by_group' => $campaigns_by_group,
            'campaign_outcomes' => $campaign_outcomes,
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
               'page' => array('dashboard'=>'client_dash'),
            'javascript' => array(
                'charts.js?v' . $this->project_version,
                'dashboard.js?v' . $this->project_version
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
                'page' => array('dashboard'=>'nbf_dash'),
            'campaigns' => $campaigns,
            'javascript' => array(
                'charts.js?v' . $this->project_version,
                'dashboard.js?v' . $this->project_version
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
               'page' => array('dashboard'=>'management_dash'),
            'javascript' => array(
                'charts.js?v' . $this->project_version,
                'dashboard.js?v' . $this->project_version,
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

            if (!$filter['date_from']) {
                $date_form = new \DateTime("now");
            } else {
                $date_form = new \DateTime($filter['date_from']);
            }

            //Get the las monday
            $date_form = clone $date_form->modify(('Sunday' == $date_form->format('l')) ? 'Monday last week' : 'Monday this week');

            $date_to = clone $date_form;
            $date_to->add(new DateInterval('P5W'));

            $filter['date_from'] = $date_form->format("Y-m-d");
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

            for ($i = 0; $i < 5; $i++) {
                $date = clone $date_form;
                $date->add(new DateInterval('P' . $i . 'W'));

                //$this->firephp->log($date->format('Y/W'));
                $date_last_day = clone $date;
                $date_last_day->modify('Sunday this week');
                //$this->firephp->log($date->format('dmY'));
                if (!isset($weeks[$date->format("Y/W")])) {
                    //$weeks[$date->format("Y/W")][0] = $date->format("d/m/Y");
                    //$weeks[$date->format("Y/W")][1] = $date_last_day->format("d/m/Y");
                    //$weeks[$date->format("Y/W")][2] = $date->format("Y-m-d");
                    //$weeks[$date->format("Y/W")][3] = $date_last_day->format("Y-m-d");
                    $weeks[$date->format("Y/W")] = getStartAndEndDateByMonth($date->format("W"), $date->format("Y"));
                }
            }
            ksort($weeks);

            //$this->firephp->log($data);
            //$this->firephp->log($weeks);

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
                "weeks" => $weeks
            ));
        }

    }

    public function get_hsl_webform_data() {

        if ($this->input->is_ajax_request()) {

            $filter = $this->input->post();

            $webform_id = 1;
            $webform_data = $this->Dashboard_model->get_webform_data($webform_id, $filter);

            $webform_completed = array("completed" => 0, "uncompleted" => 0, "total" => count($webform_data));
            $webform_hear = array();
            $webform_source = array();

            $subhear_options = array(
                "Newspaper" => array("Daily Mail Weekend", "Daily Mail Midweek", "Daily Mail Saturday", "Mail on Sunday Event", "Mail on Sunday / You Mag", "Saturday Express Mag", "Daily Express Saturday", "Daily Express Midweek", "Sunday Express", "Daily Telegraph Saturday", "Daily Telegraph Midweek", "Saturday Telegraph Magazine", "Sunday Telegraph", "The Sun", "Sun TV Mag", "Sun on Sunday / TV Soap", "The Times Saturday", "The Times Midweek", "The Times Mag", "The Sunday Times", "The Sunday Times Mag", "Sunday Times Culture", "Daily Mirror", "We Love TV", "Sunday Mirror", "Sunday Mirror Notebook", "The People", "Love Sunday", "Guardian Weekend Mag", "The Guardian", "Observer Magazine", "The Observer", "Daily Mail Scotland Saturday", "Mail on Sunday Scotland", "Daily Mail Scotland Midweek", "Scottish Daily Express Saturday", "Scottish daily express midweek", "Scottish Sunday Express", "Daily Record", "Daily Record Saturday Plus", "Sunday Mail", "Sunday Post", "The Herald Magazine", "Sunday Herald"),
                "Magazine" => array("Tv times code", "Radio times code", "Peoples Friend", "Peoples Friend Special", "Peoples friend Pocket Novels", "My Weekly", "My Weekly Special", "My Weekly Pocket Novels", "Radio Times", "radio times Extra", "Womens Weekly", "Womens Weekly Special", "Puzzler Big Brands", "Puzzler Q Range", "Puzzler Chat Pack", "Take A Break", "Take A Break Special", "Womans Own", "Womans Own Special", "Woman", "Woman Special", "TV Choice", "Total TV Guide", "Whats on TV", "TV and Satelite Week", "Weekly News", "Yours", "Amateur Gardening ", "Garden News", "Saga", "Choice Magazine", "BBC Countrylife", "BBC Gardeners World", "Take A Puzzle", "Take A Crossword", "Arrowwords", "Sudoku Selection", "Best OF British", "Card Making and Papercraft", "Bella", "That\'s Life", "Chat", "Readers Digest ", "Woman & Home", "Good Housekeeping", "Caravan Club Magazine", "The Garden", "CSMA Magazine", "NFOP Magazine", "Eye to Eye Puzzles", "Motability Lifestyle", "Arthritis Digest", "Arthritis Today", "Stroke News", "WI Life", "National Trust Magazine", "National Trust Scotland Magazine", "Nature\'s Home", "The Legion", "The Legion Scotland", "House Beautiful", "Candis", "Gardens Illustrated ", "Homes & Antiques", "Prima", "OT Magazine"),
                "TV Sponsorship" => array("Channel itv3 ", "ITV3 Morning ", "ITV3 Late Peak", "Dickinson\'s Real Deal", "UKTV")
            );

            foreach ($webform_data as $data) {
                if ($data['completed_on']) {
                    $webform_completed['completed'] = $webform_completed['completed'] + 1;
                } else {
                    $webform_completed['uncompleted'] = $webform_completed['uncompleted'] + 1;
                }

                //Hear about
                if ($data['a24'] == '') {
                    $data['a24'] = 'No answer';
                }

                //If is multiselected
                $data_hear_ar = explode(",", $data['a24']);
                foreach ($data_hear_ar as $data_hear) {
                    if (!isset($webform_hear[$data_hear])) {
                        $webform_hear[$data_hear]['count'] = 0;
                        $webform_hear[$data_hear]['sub_hear'] = array();
                    }
                    $webform_hear[$data_hear]['count']++;
                }

                //Check the secondary option for the hear about question
                $data_hear_subhear_ar = (strlen($data['a28']) > 0 ? explode(",", $data['a28']) : array());
                foreach ($data_hear_subhear_ar as $data_hear_subhear) {
                    foreach ($subhear_options as $hear_opt => $subhear_option) {
                        if (array_search($data_hear_subhear, $subhear_option) !== false) {
                            $data_hear_subhear = str_replace("\\", '', $data_hear_subhear);
                            if (!isset($webform_hear[$hear_opt]['sub_hear'][$data_hear_subhear])) {
                                $webform_hear[$hear_opt]['sub_hear'][$data_hear_subhear] = 0;
                            }
                            $webform_hear[$hear_opt]['sub_hear'][$data_hear_subhear]++;
                            break;
                        }
                    }
                }

                //Source
                if ($data['a25'] == '') {
                    $data['a25'] = 'No answer';
                }
                if (!isset($webform_source[$data['a25']])) {
                    $webform_source[$data['a25']] = 0;
                }
                $webform_source[$data['a25']]++;
            }

            echo json_encode(array(
                "success" => true,
                "date_from" => mysql_to_uk_date($filter['date_from']),
                "date_to" => mysql_to_uk_date($filter['date_to']),
                'webform_completed' => $webform_completed,
                'webform_hear' => $webform_hear,
                'webform_source' => $webform_source
            ));
        }
    }

    //this controller sends the favorites records back the page in JSON format. It ran when the javascript function "favorites_panel" is executed
    public function get_favorites()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
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
            $stats = $this->Dashboard_model->get_email_stats($filter);
            echo json_encode(array("success" => true, "data" => $stats));
            exit;
        }
    }

    public function get_sms_stats()
    {
        if ($this->input->is_ajax_request()) {
            $filter = $this->input->post();
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

    //the dashboards settings
    public function settings()
    {
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Dashboard',
            'title' => 'Dashboard-Settings',
                'page' => array('dashboard'=>'dash_settings'),
            'javascript' => array(
                'dashboard.js?v' . $this->project_version
            ),
            'css' => array(
                'dashboard.css'
            )
        );
        $this->template->load('default', 'dashboard/settings.php', $data);
    }


    /**
     * Get the custom dashboards
     */
    public function get_dashboards() {
        $dashboards = $this->Dashboard_model->get_dashboards();

        echo json_encode(array(
            "success" => (!empty($dashboards)),
            "dashboards" => $dashboards)
        );
    }

    /**
     * Get the dashboard viewers
     */
    public function get_dash_viewers() {
        $viewers = $this->Dashboard_model->get_viewers();
        $aux = array();
        foreach ($viewers as $viewer) {
            if (!isset($aux[$viewer['role_name']])) {
                $aux[$viewer['role_name']] = array();
            }
            array_push($aux[$viewer['role_name']],array(
                "id" => [$viewer['id']],
                "name" => $viewer['name']
            ));
        }
        $viewers = $aux;

        echo json_encode(array(
                "success" => (!empty($viewers)),
                "viewers" => $viewers
        ));
    }

    /**
     * Save a dashboard
     */
    public function save_dashboard() {

        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            if (isset($form['name']) && $form['name']!= "" && isset($form['description']) && $form['description'] != "") {
                $viewers = (isset($form['viewers'])?$form['viewers']:array());

                if (!in_array($_SESSION['user_id'],$viewers)) {
                    array_push($viewers, $_SESSION['user_id']);
                }

                if (isset($form['viewers'])) {
                    unset($form['viewers']);
                }
                $dashboard_id = $this->Dashboard_model->save_dashboard($form);

                if ($dashboard_id) {
                    //Save the viewers
                    $viewers_result = $this->Dashboard_model->save_dashboard_viewers($dashboard_id, $viewers);

                    if (!$viewers_result) {
                        echo json_encode(array(
                                "success" => false,
                                "msg" => "ERROR: Error saving the user dashboards!"
                            )
                        );
                    }
                }

                echo json_encode(array(
                        "success" => (!$dashboard_id?false:true),
                        "dashboard_id" => $dashboard_id,
                        "msg" => "Dashboard saved successfully!"
                    )
                );
            }
            else {
                echo json_encode(array(
                        "success" => false,
                        "msg" => "ERROR: Please set the name and the description!"
                    )
                );
            }
        }
        else {
            echo json_encode(array(
                    "success" => false,
                    "msg" => "ERROR: It's not an ajax request!"
                )
            );
        }
    }


    /**
     * View custom dashboard
     */
    public function view() {
        $dashboard_id = $this->uri->segment(3);

        if ($dashboard_id !== FALSE && is_numeric($dashboard_id))
        {
            $agents = $this->Form_model->get_agents();
            $teamManagers = $this->Form_model->get_teams();
            $sources = $this->Form_model->get_sources();
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
                "positive" => array(),
                "No_positive" => array(),
            );
            foreach ($campaign_outcomes as $outcome) {
                if ($outcome['positive']) {
                    array_push($aux['positive'], $outcome);
                } else {
                    array_push($aux['No_positive'], $outcome);
                }
            }
            $campaign_outcomes = $aux;

            //Get the dashboard details
            $dashboard = $this->Dashboard_model->get_dashboard_by_id($dashboard_id);
            if (!empty($dashboard)) {

                $dashboard = $dashboard[0];
                $data = array(
                    'campaign_access' => $this->_campaigns,
                    'pageId' => 'Dashboard',
                    'title' => 'Dashboard - '.$dashboard['name'],
                   'page' => array('dashboard'=>$dashboard['name']),
                    'dashboard' => $dashboard,
                    'agents' => $agents,
                    'team_managers' => $teamManagers,
                    'sources' => $sources,
                    'campaigns_by_group' => $campaigns_by_group,
                    'campaign_outcomes' => $campaign_outcomes,
                    'javascript' => array(
                        'charts.js?v' . $this->project_version,
                        'dashboard.js?v' . $this->project_version,
                        'lib/moment.js',
                        'lib/daterangepicker.js',
                        'export.js?v' . $this->project_version,
                        'plugins/DataTables/datatables.min.js'
                    ),
                    'css' => array(
                        'dashboard.css',
                        'plugins/morris/morris-0.4.3.min.css',
                        'daterangepicker-bs3.css'
                    )
                );

                $this->template->load('default', 'dashboard/view.php', $data);
            }
            else {
                $data = array(
                    'campaign_access' => $this->_campaigns,
                    'pageId' => 'Dashboard',
                    'title' => 'Dashboard - ERROR',
                       'page' => array('dashboard'=>'dashboard-error'),
                    'error' => "ERROR => There is no dashboard with this id or you don't have permissions to access!",
                    'javascript' => array(
                        'dashboard.js?v' . $this->project_version
                    ),
                    'css' => array(
                        'dashboard.css'
                    )
                );

                $this->template->load('default', 'dashboard/view_err.php', $data);
            }
        }
        else
        {

            $data = array(
                'campaign_access' => $this->_campaigns,
                'pageId' => 'Dashboard',
                'title' => 'Dashboard - ERROR',
                  'page' => array('dashboard'=>'dashboard-error'),
                'error' => "ERROR => There is an error on the url!",
                'javascript' => array(
                    'dashboard.js?v' . $this->project_version
                ),
                'css' => array(
                    'dashboard.css'
                )
            );

            $this->template->load('default', 'dashboard/view_err.php', $data);

        }



    }


    /**
     * Add a report to the dashboard
     */
    public function add_report() {

        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            if (isset($form['dashboard_id']) && $form['dashboard_id']!= "" && isset($form['report_id']) && $form['report_id'] != "") {

                $result = $this->Dashboard_model->add_report($form);

                echo json_encode(array(
                        "success" => (!$result?false:true),
                        "dashboard_id" => $form['dashboard_id'],
                        "msg" => (!$result?"ERROR: The report panel couldn't be added!":"Report panel added to the dashboard successfully!")
                    )
                );
            }
            else {
                echo json_encode(array(
                        "success" => false,
                        "msg" => "ERROR: The dashboard or the report selected don't exist"
                    )
                );
            }
        }
        else {
            echo json_encode(array(
                    "success" => false,
                    "msg" => "ERROR: It's not an ajax request!"
                )
            );
        }
    }

    /**
     * Remove a report from the dashboard
     */
    public function remove_report() {

        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            if (isset($form['dashboard_id']) && $form['dashboard_id']!= "" && isset($form['report_id']) && $form['report_id'] != "") {

                $result = $this->Dashboard_model->remove_report($form['dashboard_id'],$form['report_id']);

                //Reorder positions
                $dash_reports = $this->Dashboard_model->get_dashboard_reports_by_id($form['dashboard_id']);
                $aux = array();
                $position = 0;
                foreach ($dash_reports as $dash_report) {
                    unset($dash_report['name']);
                    $dash_report['position'] = $position;
                    $position++;
                    array_push($aux, $dash_report);
                }
                $dash_reports = $aux;

                $result = $this->Dashboard_model->update_reports($dash_reports);

                echo json_encode(array(
                        "success" => (!$result?false:true),
                        "dashboard_id" => $form['dashboard_id'],
                        "msg" => (!$result?"ERROR: The report panel couldn't be removed!":"Report panel removed from the dashboard successfully!")
                    )
                );
            }
            else {
                echo json_encode(array(
                        "success" => false,
                        "msg" => "ERROR: The dashboard or the report selected don't exist"
                    )
                );
            }
        }
        else {
            echo json_encode(array(
                    "success" => false,
                    "msg" => "ERROR: It's not an ajax request!"
                )
            );
        }
    }

    /**
     * Move a report on the dashboard, chane its position
     */
    public function move_report() {

        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            if (isset($form['dashboard_id']) && $form['dashboard_id']!= "" && isset($form['report_id']) && $form['report_id'] != "" && isset($form['current_position']) && isset($form['next_position'])) {

                $dash_reports = $this->Dashboard_model->get_dashboard_reports_by_id($form['dashboard_id']);
                $aux = array();
                foreach ($dash_reports as $dash_report) {
                    unset($dash_report['name']);
                    if (($dash_report['dashboard_id'] == $form['dashboard_id']) && ($dash_report['report_id'] == $form['report_id'])) {
                        $dash_report['position'] = $form['next_position'];
                    }
                    else {
                        if ($dash_report['position'] < $form['current_position']) {
                            if ($dash_report['position'] >= $form['next_position']) {
                                $dash_report['position'] = $dash_report['position']+1;
                            }
                        }
                        else if ($dash_report['position'] > $form['current_position']) {
                            if ($dash_report['position'] <= $form['next_position']) {
                                $dash_report['position'] = $dash_report['position']-1;
                            }
                        }
                    }
                    array_push($aux, $dash_report);
                }
                $dash_reports = $aux;

                //Change report position
                $result = $this->Dashboard_model->reorder_reports($form['dashboard_id'], $dash_reports);

                echo json_encode(array(
                        "success" => (!$result?false:true),
                        "dashboard_id" => $form['dashboard_id'],
                        "msg" => (!$result?"ERROR: The report panel couldn't be moved!":"Report panel moved from the dashboard successfully!")
                    )
                );
            }
            else {
                echo json_encode(array(
                        "success" => false,
                        "msg" => "ERROR: The dashboard or the report selected don't exist"
                    )
                );
            }
        }
        else {
            echo json_encode(array(
                    "success" => false,
                    "msg" => "ERROR: It's not an ajax request!"
                )
            );
        }
    }

    public function get_export_forms() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            $reports = $this->Export_model->get_export_forms();

            $dash_reports = $this->Dashboard_model->get_dashboard_reports_by_id($form['dashboard_id']);
            $dash_reports_ids = array();
            foreach ($dash_reports as $dash_report) {
                array_push($dash_reports_ids, $dash_report['report_id']);
            }

            //Remove the reports that are already added to the dashboard
            $aux = array();
            foreach($reports as $report) {
                if (!in_array($report['export_forms_id'],$dash_reports_ids)) {
                    array_push($aux,$report);
                }
            }
            $reports = $aux;

            echo json_encode(array(
                "success" => (!empty($reports)),
                "data" => (!empty($reports) ? $reports : "No export forms were created yet!"),
                "position" => count($dash_reports),
                "edit_permission" => (in_array("edit export", $_SESSION['permissions']))
            ));
        }
        else {
            echo json_encode(array(
                    "success" => false,
                    "msg" => "ERROR: It's not an ajax request!"
                )
            );
        }
    }


    /**
     * Get a dashboard reports by id
     */
    public function get_dashboard_reports_by_id() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            $reports = $this->Dashboard_model->get_dashboard_reports_by_id($form['dashboard_id']);

            echo json_encode(array(
                "success" => (!empty($reports)),
                "reports" => $reports,
                "msg" => (!empty($reports)?"":"There are no panels to be loaded on this dashboard!")
            ));
        }
        else {
            echo json_encode(array(
                    "success" => false,
                    "msg" => "ERROR: It's not an ajax request!"
                )
            );
        }

    }

}
