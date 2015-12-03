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
        $this->project_version = $this->config->item('project_version');

        $this->load->model('User_model');
        $this->load->model('Records_model');
        $this->load->model('Survey_model');
        $this->load->model('Form_model');
        $this->load->model('Appointments_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
    }

	public function get_apps(){
		$user_id = $this->input->post('user_id');
		$date = $this->input->post('date');
	$qry = "select title, postcode, date_format(start,'%H:%i') `start`, date_format(end,'%H:%i')	`end`  from appointments join appointment_attendees using(appointment_id) where date(`start`) = '$date' and user_id = '$user_id' and `status` = 1 order by `start`";
	$result = $this->db->query($qry)->result_array();
	echo json_encode(array("success"=>true,"data"=>$result));	
	}


	public function show_slot(){
	$id = $this->uri->segment(3);
	$slot = $this->Appointments_model->get_slot_id('2015-11-30 09:00:00',157);
	$this->firephp->log($slot);	
	}
//TODO - need to get the available attendees for a specified date
	public function get_available_attendees(){
		$urn = $this->input->post('urn');
		$datetime = $this->input->post('datetime');
		$campaign = $this->Records_model->get_campaign_from_urn($urn);
		$user_slots=array();
		//get the number of slots available per user by day
		$day = date('l',strtotime($datetime));
		$qry = "select *,user_id from appointment_slot_assignment join appointment_slots using(appointment_slot_id) where day is null or day = '$day' and time($datetime) between slot_start and slot_end order by day desc"; 
		foreach($this->db->query($qry)->result_array() as $row){
		$user_slots[$row['appointment_slot_id']][$row['user_id']]=$row;
		}
		//get the number of slots available per user by date (override)
		$qry = "select * from appointment_slot_override join appointment_slots using(appointment_slot_id)				where `date` is null or `date` = date('$datetime') and time($datetime) between slot_start and slot_end group by user_id order by day desc";
		$query = $this->db->query($qry);
		foreach($this->db->query($qry)->result_array() as $row){
		$user_slots[$row['appointment_slot_id']][$row['user_id']]=$row;
		}
		//get the slots taken per user in this slot
		foreach($user_slots as $slot => $user){

		}
		
		//if slots taken < slots available return the users
	}

	public function slot_availability(){
		$urn = $this->input->post('urn');
		$app_type = $this->input->post('app_type');
		/* we are setting slots by user, the campaign/source isn't in use at the moment. If you need different slots for a campaign or source just create a different user and use js to set the user based on the campaign/source
		$campaign_id = $this->Records_model->get_campaign_from_urn($urn);
		$source = $this->Records_model->get_source($urn);
		*/
		$campaign_id = false;
		$source =  false; 
		$user_id = intval($this->input->post('user_id'));
		$postcode = $this->input->post('postcode');
		if(!empty($postcode)){
		 if(validate_postcode($postcode)){
					  $postcode = postcodeFormat($postcode); 	 
				 } else {
					 $error = "Postcode " . strtoupper($postcode) . " is not valid";
					 echo json_encode(array("success"=>false,"error"=>$error));
					 exit;
				 }
		}
		$distance = intval($this->input->post('distance'));
	 $data = $this->Appointments_model->slot_availability($campaign_id,$user_id,$postcode,$distance,$source['source_id'],$app_type);
	 if(isset($data['error'])){
	  echo json_encode(array("success"=>false,"error"=>$data['error']));		
	 } else {
	 echo json_encode(array("success"=>true,"data"=>$data));
	 }
	}

    public function index()
    {
		 //this array contains data for the visible columns in the table on the view page
		$this->load->model('Datatables_model');
		$visible_columns = $this->Datatables_model->get_visible_columns(3);
		if(!$visible_columns){
		 $this->load->model('Admin_model');
		$this->Datatables_model->set_default_columns($_SESSION['user_id']);	
		$visible_columns = $this->Datatables_model->get_visible_columns(3);
		}
		
		
        $data = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'System appointment',
            'title' => 'Appointments',
            'page' => 'appointments',
			'columns' => $visible_columns,
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
                "modals.js?v" . $this->project_version,
                "location.js?v" . $this->project_version,
                "appointment.js?v" . $this->project_version,
                "map.js?v" . $this->project_version,
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
                'plugins/fontawesome-markers/fontawesome-markers.min.js',
				'plugins/DataTables/datatables.min.js',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.2.0.min.js',
                'plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js',
                'lib/moment.js',
                'lib/daterangepicker.js'
            )
        );
        $this->template->load('default', 'dashboard/appointments.php', $data);
    }

    public function appointment_data()
    {
        if ($this->input->is_ajax_request()) {
			$this->load->model('Datatables_model');
			$visible_columns = $this->Datatables_model->get_visible_columns(3);
			$options = $this->input->post();
			$options['visible_columns'] = $visible_columns;
			//check the options
			foreach($options['columns'] as $k=>$column){
				if($column['data']=="color_icon"&&$column['search']['value']=="Icon"){
					$options['columns'][$k]['search']['value']="";
				}
			}
			
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
                $records[$k]["record_color"] = ($options['group']?genColorCodeFromText($records[$k][$options['group']]):($records[$k]["record_color"]?'#'.$records[$k]["record_color"]:genColorCodeFromText($records[$k]["urn"])));
                $records[$k]["record_color_map"] = $records[$k]["record_color"];

                //Add the icon to the record color
                $map_icon = ((in_array("planner", $_SESSION['permissions']) && $records[$k]['record_planner_id'])?'fa-flag':($records[$k]['map_icon']?$records[$k]['map_icon']:($records[$k]['campaign_map_icon']?$records[$k]['campaign_map_icon']:'fa-map-marker')));
                $records[$k]["color_icon"] = '<span class="fa '.$map_icon.'" style="font-size:20px; color: '.$records[$k]["record_color"].'">&nbsp;</span>';
				// color dot
				  $records[$k]["color_dot"] = '<span class="fa fa-circle" style="font-size:20px; color: '.$records[$k]["record_color"].'">&nbsp;</span>';
				
                //Map Icon
                $records[$k]["map_icon"] = ($records[$k]['map_icon']?str_replace("FA_","",str_replace("-","_",strtoupper($records[$k]['map_icon']))):NULL);
                $records[$k]["campaign_map_icon"] = ($records[$k]['campaign_map_icon']?str_replace("FA_","",str_replace("-","_",strtoupper($records[$k]['campaign_map_icon']))):NULL);

                //Planner addresses options
                $records[$k]["planner_addresses"] = array(
                    $records[$k]["location_id"] => $records[$k]["postcode"],
                    //$records[$k]["appointment_location_id"] => $records[$k]["appointment_postcode"]
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