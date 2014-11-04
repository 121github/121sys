<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Database extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
		$this->_campaigns = campaign_access_dropdown();
        $this->load->model('Database_model');		
    }
    

    public function index()
    {
    	//get current version
    	$currentVersion = $this->Database_model->get_version();
    	
    	//Update the schema
		$this->load->library('migration');

		//If the version before update the schema did not exist, dump the init data
		if ($currentVersion == "Unknown") {
			$this->Database_model->init_data();
		} 
		
		//Get the version after update the schema
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
	
	public function add_real_data()
	{
		$status = $this->Database_model->real_data();
		if($status=="success"){
			echo json_encode(array("success"=>true,"msg"=>"Real data was added"));
		} else {
			echo json_encode(array("success"=>false,"msg"=>"Real data could not be added. Failed on $status table"));
		}
	
	}
	
	public function reset_data()
	{
		$status = $this->Database_model->init_data();
		$db = $this->db->database;
		if($status=="success"){
			exec("mysql -u root -p12183c -h localhost $db < /var/www/uk_postcodes.sql",$output);
			echo json_encode(array("success"=>true,"msg"=>"The default data was restored","postcode_status"=>$output));
		} else {
			echo json_encode(array("success"=>false,"msg"=>"Error restoring the default data. Failed on $status table"));
		}
	
	}
	
}