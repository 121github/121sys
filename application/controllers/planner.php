<?php
require('upload.php');

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Planner extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->_campaigns = campaign_access_dropdown();
        $this->load->model('User_model');
        $this->load->model('Form_model');
        $this->load->model('Planner_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
        if (intval($this->input->post('user_id')) > 0) {
            $this->user_id = intval($this->input->post('user_id'));
        } else {
            $this->user_id = $_SESSION['user_id'];
        }
    }

    public function index()
    {

        $user_id = ($_SESSION['user_id'] ? $_SESSION['user_id'] : NULL);

        $campaign_branch_users = $this->getCampaignBranchUsers();

        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'System planner',
            'title' => 'Planner',
            'page' => array('dashboard' => 'planner'),
            'campaign_branch_users' => $campaign_branch_users,
            'user_id' => $user_id,
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css',
                '../js/plugins/DataTables/extensions/Scroller/css/dataTables.scroller.min.css',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.css',
                'map.css',
                'daterangepicker-bs3.css'
            ),
            'javascript' => array(
                'modals.js',
                'planner/planner.js',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
                'lib/moment.js',
                'lib/daterangepicker.js',
                'plugins/touch-punch/jquery-ui-touch-punch.js',
                'location.js',
                'map.js',
                'plugins/fontawesome-markers/fontawesome-markers.min.js'
            )
        );
        $this->template->load('default', 'dashboard/planner.php', $data);
    }

    public function getCampaignBranchUsers()
    {
        $campaign_branch_users = $this->Planner_model->getCampaignBranchUsers();

        $aux = array();
        $aux['Campaigns'] = array();
        foreach ($campaign_branch_users as $campaign_branch_user) {
            if (isset($campaign_branch_user['campaign_id'])) {
                if (!isset($aux['Campaigns'][$campaign_branch_user['campaign_name']][$campaign_branch_user['branch_name']])) {
                    $aux['Campaigns'][$campaign_branch_user['campaign_name']][$campaign_branch_user['branch_name']] = array();
                }
                $aux['Campaigns'][$campaign_branch_user['campaign_name']][$campaign_branch_user['branch_name']][$campaign_branch_user['user_id']] = $campaign_branch_user['name'];
            } else {
                if (!isset($aux['Others'])) {
                    $aux['Others'] = array();
                }
                $aux['Others'][$campaign_branch_user['user_id']] = $campaign_branch_user['name'];
            }
        }
        $campaign_branch_users = $aux;

        return $campaign_branch_users;
    }

    public function planner_data()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Records_model');
            $records = $this->Planner_model->planner_data(false, $this->input->post());
            $letters = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M");
            $i = 0;
            foreach ($records as $k => $v) {
                if ($v['planner_type'] == 1) {
                    $records[$k]["letter"] = "dd-start";
                } else if ($v['planner_type'] == 3) {
                    $records[$k]["letter"] = "dd-end";
                } else {
                    $records[$k]["letter"] = "marker" . $letters[$i];
                    $i++;
                }
                if (!empty($v['urn'])) {
                    $records[$k]["comments"] = $this->Records_model->get_last_comment($v['urn']);
                }

            }

            $data = array(
                "data" => $records
            );
            echo json_encode($data);
        }
    }

    public function add_record()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $postcode = $this->input->post('postcode');
            if (validate_postcode($postcode)) {
                $urn = $this->input->post('urn');
                $date = to_mysql_datetime($this->input->post('date'));
                if (strtotime($date) < strtotime('today')) {
                    echo json_encode(array("success" => false, "msg" => "You can only plan for the future!"));
                    exit;
                }
                if ($this->Planner_model->check_planner($urn, $this->user_id)) {
                    echo json_encode(array("success" => false, "msg" => "Record is already in planner"));
                    exit;
                }
                $this->Planner_model->add_record($urn, $date, $postcode, $this->user_id);
                echo json_encode(array("success" => true, "msg" => "Planner was updated"));
                exit;
            } else {
                echo json_encode(array("success" => false, "msg" => "Postcode is invalid"));
                exit;
            }
        } else {
            echo "denied";
            exit;
        }
    }

    public function remove_record()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $urn = $this->input->post('urn');
            $this->Planner_model->remove_record($urn, $this->user_id);
            echo json_encode(array("success" => true, "msg" => "Planner was updated"));
        } else {
            echo "denied";
            exit;
        }
    }

    /**
     * Save the record route and the order selected/optimized
     */
    public function save_record_route()
    {
        if ($this->input->is_ajax_request()) {

            $record_list = $this->input->post('record_list');
            $date = $this->input->post('date');
            $origin = postcodeFormat($this->input->post('origin'));
            $destination = postcodeFormat($this->input->post('destination'));
            if (postcodeCheckFormat($origin) && !$this->Planner_model->get_location_id($origin)) {
                echo json_encode(array("success" => false, "error" => "The origin postcode is not valid"));
                exit;
            }
            if (postcodeCheckFormat($destination) && !$this->Planner_model->get_location_id($destination)) {
                echo json_encode(array("success" => false, "error" => "The destination postcode is not valid"));
                exit;
            }
            $this->Planner_model->save_record_route($record_list, $this->user_id, $date, $origin, $destination);
            $this->Planner_model->save_record_order($record_list, $this->user_id, $date, $origin, $destination);

            echo json_encode(array(
                "success" => true,
                "msg" => "Planner was updated"
            ));

        } else {

            echo "denied";
            exit;
        }
    }
}