<?php
require('upload.php');

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Audit extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->_campaigns = campaign_access_dropdown();
        $this->load->model('User_model');
        $this->load->model('Records_model');
        $this->load->model('Survey_model');
        $this->load->model('Form_model');
		$this->load->model('Audit_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
    }
public function index(){
	 $data = array(
            'campaign_access' => $this->_campaigns,
            'page' => 'audit',
            'title' => 'System Audit',
			'javascript' => array('plugins/DataTables/js/jquery.dataTables.min.js',
				'plugins/DataTables/js/dataTables.bootstrap.js')
        );
        $this->template->load('default', 'audit/audit_list.php', $data);
}

    public function audit_data()
    {
        if ($this->input->is_ajax_request()) {

            $records = $this->Audit_model->audit_data(false,$this->input->post());
            $count = $this->Audit_model->audit_data(true,$this->input->post());
            
            $data = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "data" => $records
            );
            echo json_encode($data);
        }
    }

	public function audit_modal(){
	$id = $this->input->post('id');
    $audit = $this->Audit_model->audit_modal($id);
	$values = $this->Audit_model->audit_values($id);
	$formatted_date = date('D jS M Y',strtotime($audit['timestamp']));
	$config = array("company_addresses"=>"A company address","contact_addresses"=>"A contact address","company_telephone"=>"A company phone number","contact_telephone"=>"A contact phone number", "appointments"=>"An appointment","companies"=>"A company","contacts"=>"A contact", "delete"=>"deleted", "update"=>"updated","insert"=>"inserted");
	$audit['title'] =  $config[$audit['table_name']] . " was " . $config[$audit['change_type']] . " by " . $audit['name'];
	$audit['date_formatted'] = $formatted_date;
	$result['audit'] = $audit;
	$result['values'] = $values;
	echo json_encode(array("success"=>true,"data"=>	$result));
	}
	
}