<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function __construct()
    {

parent::__construct();
 user_auth_check();
$this->load->model('Database_model');	
}

public function index(){
	$this->load->library('migration');
if ( !$this->migration->latest())
{
	show_error($this->migration->error_string());
} else {
      echo json_encode(array("success"=>true,"version"=>$this->Database_model->get_version()));
}	
}


}