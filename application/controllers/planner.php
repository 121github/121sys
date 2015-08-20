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
		$planner_users = $this->Form_model->get_drivers();
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'System planner',
            'title' => 'Planner',
            'page' => array('dashboard' => 'planner'),
            'campaign_branch_users' => $campaign_branch_users,
            'user_id' => $user_id,
			'planner_users' => $planner_users,
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
        $aux['Others'] = array();
        foreach ($campaign_branch_users as $campaign_branch_user) {
            if (isset($campaign_branch_user['campaign_id'])) {
                if (!isset($aux['Campaigns'][$campaign_branch_user['campaign_name']][$campaign_branch_user['branch_name']])) {
                    $aux['Campaigns'][$campaign_branch_user['campaign_name']][$campaign_branch_user['branch_name']] = array();
                }
                $aux['Campaigns'][$campaign_branch_user['campaign_name']][$campaign_branch_user['branch_name']][$campaign_branch_user['user_id']] = $campaign_branch_user['name'];
            } else {
                $aux['Others'][$campaign_branch_user['user_id']] = $campaign_branch_user['name'];
            }
        }

        if (!in_array($_SESSION['user_id'],$aux['Others'])) {
            $aux['Others'][$_SESSION['user_id']] = $_SESSION['name'];
        }

        $campaign_branch_users = $aux;

        return $campaign_branch_users;
    }

    public function showBranches() {
        if ($this->input->is_ajax_request()) {
            $user_id = $this->input->post('user_id');

            $campaign_branch_users = $this->Planner_model->getCampaignBranchUsers();

            $aux = array();
            foreach ($campaign_branch_users as $campaign_branch_user) {
                if (isset($campaign_branch_user['campaign_id'])) {
                    if (!isset($aux[$campaign_branch_user['branch_id']]['current_branch']) || !$aux[$campaign_branch_user['branch_id']]['current_branch']) {
                        $campaign_branch_user['current_branch'] = ($campaign_branch_user['user_id'] === $user_id?true:false);
                    }
                    else {
                        $campaign_branch_user['current_branch'] = $aux[$campaign_branch_user['branch_id']]['current_branch'];
                    }
					if(strpos($campaign_branch_user["map_icon"],".png")!==false){
					 $campaign_branch_user["map_icon_type"] = "image";	
					} else {
						$campaign_branch_user["map_icon_type"] = "icon";	
                    $campaign_branch_user["map_icon"] = ($campaign_branch_user["map_icon"]?str_replace("FA_","",str_replace("-","_",strtoupper($campaign_branch_user["map_icon"]))):NULL);
					}
					
                    if (!isset($aux[$campaign_branch_user['branch_id']])) {
                        $campaign_branch_user['user_id'] = array($campaign_branch_user['user_id']);
                    }
                    else {
                        $branch_user_id = $campaign_branch_user['user_id'];
                        $campaign_branch_user['user_id'] = $aux[$campaign_branch_user['branch_id']]['user_id'];
                        array_push($campaign_branch_user['user_id'],$branch_user_id);
                    }
                    $aux[$campaign_branch_user['branch_id']] = $campaign_branch_user;
                }
            }

            $campaign_branch_users = $aux;

            echo json_encode(array(
                "data" => $campaign_branch_users
            ));
        }
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
			if(count($records)==0&&$this->input->post('user_id')){
				$user_postcode = $this->Planner_model->get_user_postcode($this->input->post('user_id'));
				$data['user_postcode'] = $user_postcode;
			}
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

    /**
     * Add appointment to the planner, assigned to the attendee and to all the users that belong to the branch region if that exists on the planner
     */
    public function add_appointment_to_the_planner()
    {

        $appointment_id = $this->input->post('appointment_id');

        if ($appointment_id) {

            //get the info for the planner
            $appointment_data = $this->Planner_model->getPlannerInfoByAppointment($appointment_id);

            $users = array_unique(array_merge(explode(',', $appointment_data['attendees']), explode(',', $appointment_data['region_users'])));

            //Create the planner data for every user
            $planner_data = array();
            foreach ($users as $user_id) {
                $planner = array(
                    "urn" => $appointment_data['urn'],
                    "user_id" => $user_id,
                    "start_date" => $appointment_data['start'],
                    "postcode" => $appointment_data['postcode'],
                    "location_id" => $appointment_data['location_id'],
                    "order_num" => 20,
                    "planner_status" => PLANNER_STATUS_LIVE,
                    "planner_type" => PLANNER_TYPE_WAYPOINT,
                );

                array_push($planner_data, $planner);
            }

            //Add or update the record planner for each user
            foreach ($planner_data as $planner) {
                //Check if the planner already exist for this urn
                $planner_id = $this->Planner_model->check_planner($planner['urn'], $planner['user_id']);
                //If the status of the appointment is not cancellation, we add or update the appointment
                if ($appointment_data['status'] != APPOINTMENT_STATUS_CANCEL) {
                    if ($planner_id) {
                        //Update the planner
                        $this->Planner_model->update_record_planner($planner_id, $planner);
                    } else {
                        //Add the planner for that user
                        $this->Planner_model->add_record($planner['urn'], $planner['start_date'], $planner['postcode'], $planner['user_id']);
                    }
                } else if ($planner_id) {
                    //Cancel the planner if already exist
                    $this->Planner_model->remove_record($planner['urn'], $planner['user_id']);
                }
            }

            if ($appointment_data['status'] != APPOINTMENT_STATUS_CANCEL) {
                echo json_encode(array(
                    "success" => true,
                    "msg" => "Appointment added or updated on the planner"
                ));
            } else {
                echo json_encode(array(
                    "success" => true,
                    "msg" => "Appointment cancelled on the planner"
                ));
            }
        }
        else {
            echo json_encode(array(
                "success" => false,
                "msg" => "The appointment_id doesn't exist"
            ));
        }
    }

}