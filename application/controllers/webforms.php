<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Webforms extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
		$this->load->model('Webform_model');
            //$this->load->model('User_model');
            //$this->User_model->validate_login('admin', md5('pass123'));
			//user_auth_check();del');
    }
    
    
	public function remote(){
		
		$campaign_id =  intval($this->uri->segment(3));
		$urn = intval($this->uri->segment(4));
		$form = intval($this->uri->segment(5));
		$email_id = intval($this->uri->segment(6));
		
		if(!$this->Webform_model->check_form_permission($email_id,$urn,$campaign_id)){
		echo "Sorry you have not come to a valid link";	
		exit;
		}	
				
		if($this->input->post('save')=="1"){
			$data['answers'] = $this->input->post('answer');
			$data['urn'] = $urn;
			$data['id'] = $form;
			$data['complete'] = $this->input->post('complete');	
			$this->Webform_model->save_answer($data);
			exit;
		}
		
		$this->edit("remote");
	}
	
	
    //this is the default controller for search, its specified in application/config/routes.php.  
    public function edit($remote=false)
    { 
		
		$campaign_id =  intval($this->uri->segment(3));
		$urn = intval($this->uri->segment(4));
		$form = intval($this->uri->segment(5));
		$path = $this->Webform_model->get_path($form);
		if(intval($campaign_id)&&intval($urn)&&intval($form)){
		if($this->input->post('save')=="1"){
			$answers = $this->input->post('answers');
			if($this->input->post('contact')){
			$this->Webform_model->update_contact($contact);	
			}
			foreach($answers as $question_name=>$input){
				$list = "";
			if(is_array($input)){
				foreach($input as $item){
					if(!empty($item)){
				$list .= $item.",";
					}
				}
				$answers[$question_name] = rtrim($list,",");
			}
			}
			$data['answers'] = $answers;
			$data['urn'] = $urn;
			$data['id'] = $form;
			$data['complete'] = $this->input->post('complete');	
			$this->Webform_model->save_answer($data);
			echo "Saved";
			exit;
		}
			
			$all_data = $this->Webform_model->get_all_data($urn,$campaign_id,$form);
			//$this->firephp->log($all_data);
				//if the customer is viewing the form we can make some fields read only
	if($remote=="remote"){
	$all_data['remote'] = true;	
	} else {
	$all_data['remote'] = false;		
	}
			
        $this->template->load('empty','webforms/'.$path, $all_data);
			
		}
	}



}