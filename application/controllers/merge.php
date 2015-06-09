<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Merge extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
		$this->_options = $this->input->post();
		$this->load->model('Merge_model');
    }
    

	 public function merge_preview()
    {
		$preview = array();
		//company details preview
		if($this->_options['company_details']>1){
		$preview['companies'] = $this->Merge_model->merge_company_preview($this->_options);
		}
		if($this->_options['company_details']>1){
		$preview['company telephone numbers'] = $this->Merge_model->merge_cotel_preview($this->_options);
		}
		
		if($this->_options['contact_details']>1){
		$preview['contacts'] = $this->Merge_model->merge_contact_preview($this->_options);
		}
		echo json_encode($preview);
		
		
	}
	
}