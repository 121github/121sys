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
    }

    public function index()
    {
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'System planner',
            'title' => 'Planner',
            'page' => array('dashboard' => 'planner'),
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
                'plugins/DataTables/extensions/Scroller/js/dataTables.scroller.min.js',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
                'lib/moment.js',
                'lib/daterangepicker.js',
                'plugins/touch-punch/jquery-ui-touch-punch.js',
                'location.js'
            )
        );
        $this->template->load('default', 'dashboard/planner.php', $data);
    }

    public function planner_data()
    {
        if ($this->input->is_ajax_request()) {

            $records = $this->Planner_model->planner_data(false, $this->input->post());

            foreach ($records as $k => $v) {
                //Website
                $records[$k]["website"] = ($records[$k]['company_website']?$records[$k]['company_website']:($records[$k]['contact_website']?$records[$k]['contact_website']:''));
            }

            $data = array(
                "data" => $records
            );
            echo json_encode($data);
        }
    }
	
	public function add_record(){
		 if ($this->input->is_ajax_request()&&$this->_access) {
			 $postcode = $this->input->post('postcode');
			 if(validate_postcode($postcode)){
			 $urn = $this->input->post('urn');
			 $date = to_mysql_datetime($this->input->post('date'));
			 if(strtotime($date)<strtotime('today')){
			 echo json_encode(array("success"=>false,"msg"=>"You can only plan for the future!"));	
			 exit; 
			 }
			 $this->Planner_model->add_record($urn,$date,$postcode);
			 echo json_encode(array("success"=>true,"msg"=>"Planner was updated"));
			  exit; 
			 } else {
			 echo json_encode(array("success"=>false,"msg"=>"Postcode is invalid")); 
			  exit; 
			 }
		 } else {
			echo "denied"; 
 			exit;
		 }
	}
	
    public function remove_record(){
		 if ($this->input->is_ajax_request()&&$this->_access) {
			 $urn = $this->input->post('urn');
			 $this->Planner_model->remove_record($urn); 
			 echo json_encode(array("success"=>true,"msg"=>"Planner was updated"));
		 } else {
			echo "denied"; 
 			exit;
		 }
	}

    /**
     * Save the record route and the order selected/optimized
     */
    public function save_record_route() {
        if ($this->input->is_ajax_request()) {

            $record_list = $this->input->post('record_list');
            $date = $this->input->post('date');
            $user_id = $_SESSION['user_id'];

            $this->Planner_model->save_record_order($record_list, $user_id, $date);
            $this->Planner_model->save_record_route($record_list, $user_id, $date);

            echo json_encode(array(
                "success"=>true,
                "msg"=>"Planner was updated"
            ));

        } else {

            echo "denied";
            exit;
        }
    }
}