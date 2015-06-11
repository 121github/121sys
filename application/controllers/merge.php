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
		if(isset($preview['companies']['updated'])){
		foreach($preview['companies']['updated'] as $k=>$company){
		$preview['company telephone numbers'] = $this->Merge_model->merge_cotel_preview($this->_options,$company['company_id']);
		unset($preview['companies']['updated'][$k]['company_id']);
		if(count($preview['companies']['updated'][$k])==0){
			unset($preview['companies']['updated'][$k]);
		};
		}
		}
		}
		if($this->_options['contact_details']>1){
		$preview['contacts'] = $this->Merge_model->merge_contact_preview($this->_options);
		$preview['contact telephone numbers']['added'] = array();
		if(isset($preview['contacts']['updated'])){
		foreach($preview['contacts']['updated'] as $k=>$contact){
			foreach($this->Merge_model->merge_tel_preview($this->_options,$contact['source_contact'],$contact['contact_id']) as $number){
				array_push($preview['contact telephone numbers']['added'],$number);	
			}
		unset($preview['contacts']['updated'][$k]['source_contact']);
		unset($preview['contacts']['updated'][$k]['contact_id']);
		if(count($preview['contacts']['updated'][$k])==0){
			unset($preview['contacts']['updated'][$k]);
		};
		}
		}
		
		if(isset($preview['contacts']['added'])){
		foreach($preview['contacts']['added'] as $k=>$contact){
			foreach($this->Merge_model->merge_tel_preview($this->_options,$contact['source_contact']) as $number){
		array_push($preview['contact telephone numbers']['added'],$number);
			}
		unset($preview['contacts']['added'][$k]['source_contact']);


		}
		}
		
		}
		//clean up the mess
	foreach($preview as $element=>$inner){
		foreach($inner as $f => $item){
			foreach($item as $k=>$v){
				if(empty($v)){ unset($preview[$element][$f][$k]); }	
			}
			if(count($preview[$element][$f])<1){
				unset($preview[$element][$f]);
			}
		}
		
		if(count($preview[$element])<1){
				unset($preview[$element]);
			}
	}
		echo json_encode($preview);
	}
	
		 public function merge_launch()
    {
			$preview = array();
		//company details preview
	if($this->_options['company_details']>1){
		$this->Merge_model->merge_company($this->_options);
		$preview['companies'] = $this->Merge_model->merge_company_preview($this->_options);
		if(isset($preview['companies']['updated'])){
		foreach($preview['companies']['updated'] as $company){
		$this->Merge_model->merge_cotel($this->_options,$company['company_id']);
		}
		}
		}
		if($this->_options['contact_details']>1){
		//this will merge contacts and numbers
		$this->Merge_model->merge_contact($this->_options);
		}
		echo json_encode(array("success"=>true));
	}
}