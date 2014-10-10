<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Database extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
$this->_campaigns = campaign_access_dropdown();
        $this->load->model('Database_model');		
    }
    

    public function index()
    {
		$this->load->library('migration');
		$version = $this->Database_model->get_version();
		        $data = array(
          	'page'=> array('admin'=>'Database'),
            'campaign_access' => $this->_campaigns,
'pageId' => 'Database-management',
			'version'=>$version,
            'title' => 'Database management',
			            'css' => array(
                'dashboard.css'
            )
        );
        
        $this->template->load('default', 'database/view_db.php', $data);
	}
	

    public function drop_tables()
    {
		if($this->Database_model->drop_tables()){
		echo json_encode(array("success"=>true,"msg"=>"Tables were dropped successfully"));
		} else {
		echo json_encode(array("success"=>false,"msg"=>"Tables could not be dropped"));	
		}

	}
	
	    public function add_data()
    {
		$status = $this->Database_model->demo_data();
		if($status=="success"){
		echo json_encode(array("success"=>true,"msg"=>"Sample data was added"));
		} else {
		echo json_encode(array("success"=>false,"msg"=>"Sample data could not be added. Failed on $status table"));	
		}

	}
	
}