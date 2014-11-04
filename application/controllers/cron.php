<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Form_model');
		$this->load->model('Cron_model');
    }
    
	public function format_postcodes(){
		$unformatted = $this->Cron_model->get_unformatted_contact_postcodes();
		foreach($unformatted as $row){
		$formatted_postcode = postcodeFormat($row['postcode'],$row['id']);
		$this->Cron_model->format_contact_postcode($formatted_postcode);
		echo "updated postcode ".$row['postcode']." to ".$formatted_postcode." on contact_id ".$row['id']."<br>";
		}
		$unformatted = $this->Cron_model->get_unformatted_company_postcodes();
		foreach($unformatted as $row){
		$formatted_postcode = postcodeFormat($row['postcode']);
		$this->Cron_model->format_company_postcode($formatted_postcode,$row['id']);
		echo "updated postcode ".$row['postcode']." to ".$formatted_postcode." on company_id ".$row['id']."<br>";
		}
	}
	
		public function add_missing_postcodes(){
		$missing = $this->Cron_model->get_missing_company_postcodes();
		foreach($missing as $row){
		$response = postcode_to_coords($row['postcode']);
				if(!isset($response['error'])){
		$this->Cron_model->update_missing($row['postcode'],$response);
		echo $row['postcode']. " updated!<br>";	
		} else {
		echo $response['error']."<br>";
		}
		}
		$missing = $this->Cron_model->get_missing_contact_postcodes();
		foreach($missing as $row){
		$response = postcode_to_coords($row['postcode']);
		if(!isset($response['error'])){
		$this->Cron_model->update_missing($row['postcode'],$response);
		echo $row['postcode']. " updated!<br>";		
		} else {
		echo $response['error']."<br>";
		}
		}
	}
	
    public function update_hours()
    {
        $agents = $this->Form_model->get_agents();
        $this->Cron_model->update_hours($agents);
        
    }
	
	    public function clear_hours()
    {
        $this->Cron_model->clear_hours();
    }
    
}