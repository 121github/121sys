<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function __construct()
    {

parent::__construct();
$this->load->model('Database_model');	
$this->load->library('migration');
}

public function index(){
if ( !$this->migration->current())
{
	$this->firephp->log($this->migration->error_string());
} else {
      echo json_encode(array("success"=>true,"version"=>$this->Database_model->get_version()));
}	
}

public function force_version(){
	$version = $this->uri->segment(3);
	$this->migration->version($version);	
}

public function rollback(){
	$rollback = $this->Database_model->current();
if ( !$this->migration->version($rollback))
{
	$this->firephp->log($this->migration->error_string());
} else {
      echo json_encode(array("success"=>true,"version"=>$this->Database_model->get_version()));
}	
}

}