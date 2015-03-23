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
public function index(){
	 $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'System planner',
            'title' => 'Planner',
			'page'=> array('dashboard'=>'planner'),
			            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/planner.php', $data);
}

    public function planner_data()
    {
        if ($this->input->is_ajax_request()) {

            $records = $this->Planner_model->planner_data(false,$this->input->post());
            $count = $this->Planner_model->planner_data(true,$this->input->post());
            
            $data = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "data" => $records
            );
            echo json_encode($data);
        }
    }

	public function planner_modal(){
	$id = $this->input->post('id');
    $planner = $this->Planner_model->planner_modal($id);
	$attendees = $this->Planner_model->planner_attendees($id);
	$formatted_date = date('D jS M Y',strtotime($planner['date_added']));
	$planner['date_formatted'] = $formatted_date;
	$result['planner'] = $planner;
	$result['attendees'] = $attendees;
	echo json_encode(array("success"=>true,"data"=>	$result));
	}
	
}