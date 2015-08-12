<?php
require('upload.php');

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Appointments extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check();
		check_page_permissions('view appointments');
        $this->_campaigns = campaign_access_dropdown();
        $this->load->model('User_model');
        $this->load->model('Records_model');
        $this->load->model('Survey_model');
        $this->load->model('Form_model');
        $this->load->model('Appointments_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
    }

	public function slot_availability(){
		$urn = $this->input->post('urn');
		$campaign_id = $this->Records_model->get_campaign_from_urn($urn);
		$user_id = false;
	 $slots = $this->Appointments_model->slot_availability($urn);	
	 echo json_encode(array("success"=>true,"data"=>$slots));
	}

    public function index()
    {
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'System appointment',
            'title' => 'Appointments',
            'page' => 'appointments',
            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.css',
                'map.css',
                'plugins/bootstrap-iconpicker/icon-fonts/font-awesome-4.2.0/css/font-awesome.min.css',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css'
            ),
            'javascript' => array(
                "modals.js",
                "location.js",
                "appointment.js",
				"map.js",
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
                'plugins/fontawesome-markers/fontawesome-markers.min.js',
				'plugins/DataTables/js/jquery.dataTables.min.js',
				'plugins/DataTables/js/dataTables.bootstrap.js',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.2.0.min.js',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js'
            )
        );
        $this->template->load('default', 'dashboard/appointments.php', $data);
    }

    public function appointment_data()
    {
        if ($this->input->is_ajax_request()) {

            $options = $this->input->post();

            $records = $this->Appointments_model->appointment_data(false, $options);
            $count = $this->Appointments_model->appointment_data(true, $options);

            foreach ($records as $k => $v) {

                //Location
                if ($records[$k]["company_location"]) {
                    $location_ar = explode(',',$records[$k]["company_location"]);
                }
                else if ($records[$k]["contact_location"]) {
                    $location_ar = explode(',',$records[$k]["contact_location"]);
                }
                if (!empty($location_ar)) {
                    $postcode_ar = explode("|",$location_ar[0]);
                    $postcode = substr($postcode_ar[0],0,stripos($postcode_ar[0],'('));
                    $location = explode('/',substr($postcode_ar[0],stripos($postcode_ar[0],'(')));
                    $records[$k]["record_postcode"] = $postcode;
                    $records[$k]["record_lat"] = substr($location[0],1);
                    $records[$k]["record_lng"] = substr($location[1],0,strlen($location[1])-1);
                    $records[$k]["record_location_id"] = $postcode_ar[1];
                }
                else {
                    $records[$k]["record_postcode"] = NULL;
                    $records[$k]["record_lat"] = NULL;
                    $records[$k]["record_lng"] = NULL;
                    $records[$k]["record_location_id"] = NULL;
                }

                //Record color
                $records[$k]["record_color"] = ($options['group']?genColorCodeFromText($records[$k][$options['group']]):($records[$k]["record_color"]?'#'.$records[$k]["record_color"]:genColorCodeFromText($records[$k]["urn"].$records[$k]['name'])));
                $records[$k]["record_color_map"] = $records[$k]["record_color"];
                //Add the icon to the record color
                $map_icon = ($records[$k]['map_icon']?$records[$k]['map_icon']:($records[$k]['campaign_map_icon']?$records[$k]['campaign_map_icon']:'fa-map-marker'));
                $records[$k]["record_color"] .= '/'.$map_icon;

                //Map Icon
                $records[$k]["map_icon"] = ($records[$k]['map_icon']?str_replace("FA_","",str_replace("-","_",strtoupper($records[$k]['map_icon']))):NULL);
                $records[$k]["campaign_map_icon"] = ($records[$k]['campaign_map_icon']?str_replace("FA_","",str_replace("-","_",strtoupper($records[$k]['campaign_map_icon']))):NULL);

                //Planner addresses options
                $records[$k]["planner_addresses"] = array(
                    $records[$k]["location_id"] => $records[$k]["postcode"],
                    $records[$k]["record_location_id"] => $records[$k]["record_postcode"]
                );
            }

            $data = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "data" => $records,
                "planner_permission" => (in_array("planner", $_SESSION['permissions']))
            );
            echo json_encode($data);
        }
    }

    public function appointment_modal()
    {
        $id = $this->input->post('id');
        $appointment = $this->Appointments_model->appointment_modal($id);
        $attendees = $this->Appointments_model->appointment_attendees($id);
        $formatted_date = date('D jS M Y', strtotime($appointment['date_added']));
        $appointment['date_formatted'] = $formatted_date;
        $result['appointment'] = $appointment;
        $result['attendees'] = $attendees;
        echo json_encode(array("success" => true, "data" => $result));
    }

	public function get_contacts(){
		if ($this->input->is_ajax_request()) {
			$urn = $this->input->post('urn');
			$result = $this->Form_model->get_contacts($urn);
			echo json_encode($result);
		}
	}

}