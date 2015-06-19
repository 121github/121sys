<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sms extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
		$this->load->model('User_model');
        $this->load->model('Records_model');
        $this->load->model('Contacts_model');
        $this->load->model('Sms_model');
    }
    
    //this function returns a json array of sms data for a given record id
    public function get_sms_by_urn()
    {
        user_auth_check();
    	if ($this->input->is_ajax_request()) {
    		$urn = intval($this->input->post('urn'));
            $limit = (intval($this->input->post('limit')))?intval($this->input->post('limit')):NULL;
    		
    		$sms = $this->Sms_model->get_sms_by_urn($urn,$limit,0);
    		
    		echo json_encode(array(
    			"success" => true,
    			"data" => $sms
    		));
    	}
    }

    //this function returns a json array of sms data for a given filter parameters
    public function get_sms_by_filter()
    {
        user_auth_check();
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $sms = $this->Email_model->get_sms_by_filter($form);

            echo json_encode(array(
                "success" => true,
                "data" => $sms
            ));
        }
    }
    
    //this function returns a json array of sms data for a given record id
    public function get_sms()
    {
        user_auth_check();
    	if ($this->input->is_ajax_request()) {
    		$sms_id = intval($this->input->post('sms_id'));
    
    		$sms = $this->Sms_model->get_sms_by_id($sms_id);
    
    		echo json_encode(array(
    				"success" => true,
    				"data" => $sms
    		));
    	}
    }
    
    //load all the fields into a new sms form
    public function create()
    {
        user_auth_check();
		$this->_campaigns = campaign_access_dropdown();
    	$urn             = intval($this->uri->segment(4));
    	$template_id     = intval($this->uri->segment(3));
    	$placeholder_data = $this->Sms_model->get_placeholder_data($urn);
    	$template = $this->Sms_model->get_template($template_id);
		$last_comment = $this->Records_model->get_last_comment($urn);
		
		$placeholder_data = $this->Sms_model->get_placeholder_data($urn);
		$placeholder_data[0]['comments'] = $last_comment;
				$this->firephp->log($placeholder_data);
		if(count($placeholder_data)){
		foreach($placeholder_data[0] as $key => $val){
			if($key=="fullname"){ 
			$val = str_replace("Mr ","",$val);
			$val = str_replace("Mrs ","",$val);
			$val = str_replace("Miss ","",$val);
			$val = str_replace("Ms ","",$val);
			 }
						 if(strpos($template['template_body'],"[$key]")!==false){
				 if(empty($val)){
					setcookie("placeholder_error", $key, time() + (60), "/");
					if($key=="start"){
					$template['template_body'] = str_replace("[$key]","<span style=\"color:red\">** NO APPOINTMENT FOUND **</span>",$template['template_body']);	
					} else {
					$template['template_body'] = str_replace("[$key]","<span style=\"color:red\">** [$key] WAS EMPTY **</span>",$template['template_body']);
					}
				 } else {
			$template['template_body'] = str_replace("[$key]",$val,$template['template_body']);
				 }
					}
					}
		}
	
    	$data = array(
    			'urn' => $urn,
    			'campaign_access' => $this->_campaigns,
                'pageId' => 'Create-sms',
    			'title' => 'Send new sms',
    			'urn' => $urn,
    			'template_id' => $template_id,
    			'template' => $template,
    			'css' => array(
    					'dashboard.css',
    					'plugins/summernote/summernote.css',
    					'plugins/fontAwesome/css/font-awesome.css',
    					'plugins/jqfileupload/jquery.fileupload.css',
    			),
    			'javascript' => array(
    					'sms.js',
    					'plugins/summernote/summernote.min.js',
    					'plugins/jqfileupload/vendor/jquery.ui.widget.js',
    					'plugins/jqfileupload/jquery.iframe-transport.js',
    					'plugins/jqfileupload/jquery.fileupload.js',
                        'plugins/jqfileupload/jquery.fileupload-process.js',
                        'plugins/jqfileupload/jquery.fileupload-validate.js'
    			),
    	);
    
    	$this->template->load('default', 'sms/new_sms.php', $data);
  
    }
    
	public function unsubscribe(){
		if ($this->input->is_ajax_request()) {
			$data = $this->input->post();
			$data['ip_address'] = $_SERVER['REMOTE_ADDR'];
			//check the sms address

			if(preg_match('/^[0-9().-]+$/',$data['sms_address'])){
			echo json_encode(array("success"=>false,"msg"=>"That is not a valid sms address"));
			exit;	
			}
			
			if($this->Sms_model->unsubscribe($data)){
			echo json_encode(array("success"=>true,"msg"=>"Your sms address has been removed from the mailing list"));
			} else {
			echo json_encode(array("success"=>false,"msg"=>"There was a problem removing your sms. Please make sure you are using the unbsubscribe link in the sms you recieved"));	
			}
		} else 
		{
	$template_id = base64_decode($this->uri->segment(3));
	$urn = 	base64_decode($this->uri->segment(4));
	
	$client_id = $this->Records_model->get_client_from_urn($urn);
	$check = $this->Sms_model->check_sms_history($template_id,$urn);
	if($check){
	  $data = array(
    			'urn' => $urn,
    			'title' => 'Unsubscribe',
				'client_id'=>$client_id,
				'urn'=>$urn);
			$this->template->load('default', 'sms/unsubscribe.php', $data);
	} else {
		  $data = array(
    			'msg' => "The company you tried to unsubscribe from does not exist on our system",
    			'title' => 'Invalid sms');
		$this->template->load('default', 'errors/display.php', $data);
	}
		}
	}
	
    //Get the contacts
    public function get_contacts() {
        user_auth_check();
    	if ($this->input->is_ajax_request()) {
    		$urn = intval($this->input->post('urn'));
    		
    		$contacts = $this->Contacts_model->get_contacts($urn);
    		
    		$aux = array();
    		foreach ($contacts as $key => $contact) {
				if($contact['visible']['Email address']){	
    			$aux[$key]["name"] = $contact['name']['fullname'];
    			$aux[$key]["email"] ="";
				}
    		}
    		$contacts = $aux;
    		
    		echo json_encode(array(
    				"success" => true,
    				"data" => $contacts
    		));
    	}
    }
    
    //Send an sms
    public function send_sms() {
        user_auth_check();
    	$form = $this->input->post();

		
    	$form['body'] = base64_decode($this->input->post('body'));

        //Status false by default, before sent the sms
        $form['status'] = false;
				
		$urn = intval($this->input->post('urn'));
		$last_comment = $this->Records_model->get_last_comment($urn);
		$placeholder_data = $this->Sms_model->get_placeholder_data($urn);
		$placeholder_data[0]['comments'] = $last_comment;
		if(count($placeholder_data)){
		foreach($placeholder_data[0] as $key => $val){
			if($key=="fullname"){ 
			$val = str_replace("Mr ","",$val);
			$val = str_replace("Mrs ","",$val);
			$val = str_replace("Mrs ","",$val);
			 }
			$body = str_replace("[$key]",$val,$form['body']);
					}
		}
		
		if(strpos($body, "WAS EMPTY")!== false){
			echo json_encode(array(
    			"success" => false,
                "msg" => "You have empty placeholders in the sms contents. Please edit it first!"
    	));
		exit;
		}
		
    	//Delete duplicates sms addresses
    	$from = array_unique(explode(",", $form['send_from']));
    	$form['send_from'] = implode(",", $from);
    	$to = array_unique(explode(",", $form['send_to']));
    	$form['send_to'] = implode(",", $to);
    	$cc = array_unique(explode(",", $form['cc']));
    	$form['cc'] = implode(",", $cc);
    	$bcc = array_unique(explode(",", $form['bcc']));
    	$form['bcc'] = implode(",", $bcc);
		

        $msg = "";
		$client = $this->Records_model->get_client_from_urn($form['urn']);
		if($this->Sms_model->check_unsubscribed($form['send_to'],$client)){
			echo json_encode(array(
    			"success" => false,
                "msg" => "One or more sms have unsubscribed, unable to send"
    	));
		exit;
		}

    	//Save the sms in the history table
        unset($form['template_attachments']);
        $sms_id = $this->Sms_model->add_new_sms_history($form);
        $response = ($sms_id)?true:false;
		$form['sms_id'] = $sms_id;
        if (!$response) {
            $msg = "Error saving the sms history. The sms was not sent.";
        }
        else {

            if ($response) {
                //Add the tracking image to know if the sms is read
                $form_to_send = $form;
                //Send the sms
                $sms_sent = $this->send($form_to_send);

                //Update the status in the sms History table
                if ($sms_sent) {
                    $form['sms_id'] = $sms_id;
                    $form['status'] = true;
                    $this->Sms_model->update_sms_history($form);
                    $response = true;
                    $msg = "SMS sent successfully!";
                }
                else {
                    $response = false;
                    $msg = "The SMS was not sent";
                }
            }
        }
    	
    	echo json_encode(array(
    			"success" => $response,
                "msg" => $msg
    	));
    }
    
	public function trigger_sms(){
        user_auth_check();
		if(isset($_SESSION['sms_triggers'])){
			$urn = intval($this->input->post('urn'));
		
		foreach($_SESSION['sms_triggers'] as $template_id => $recipients){
			foreach($recipients['sms'] as $name => $sms_address){
			//create the form structure to pass to the send function
			$form = $this->Sms_model->template_to_form($template_id);
			$form['send_to'] = $sms_address;
			$form['bcc'] = $recipients['bcc'];
			$form['cc'] = $recipients['cc'];
			$form['urn'] = $urn;
		$last_comment = $this->Records_model->get_last_comment($urn);
		$placeholder_data = $this->Sms_model->get_placeholder_data($urn);
		$placeholder_data[0]['comments'] = $last_comment;
		$placeholder_data['recipient_name'] = $name;
		if(count($placeholder_data)){
		foreach($placeholder_data[0] as $key => $val){
						if($key=="fullname"){ 
			$val = str_replace("Mr ","",$val);
			$val = str_replace("Mrs ","",$val);
			$val = str_replace("Mrs ","",$val);
			 }
			$form['body'] = str_replace("[$key]",$val,$form['body']);
			}
		}		
		$this->firephp->log($form);
			$this->send($form);
		}	
		}
		}
		unset($_SESSION['sms_triggers']);
	}

	/**
	 *
	 * Send pending smss
	 *
	 *
	 */
	public function send_pending_sms() {

		$output = "";
		$output .= "Sending pending sms... \n\n";
		$limit = 50;
		$campaign = false;
		if(intval($this->uri->segment(3))>0){
		$limit = $this->uri->segment(3);
		}
		if(intval($this->uri->segment(4))>0){
		$croncode = $this->uri->segment(4);
		}
		
		//Get the oldest 50 mails pending to be sent
		$pending_sms = $this->Sms_model->get_pending_sms($limit,$croncode);
		if (!empty($pending_sms)) {
			foreach($pending_sms as $sms) {

				$client = $this->Records_model->get_client_from_urn($sms['urn']);
				$sms_id = $sms['sms_id'];

				$output .= $sms['urn']."... ";
				//Remove the addresses that are unsubscribed for this client
				$sms_addresses = explode(',',str_replace(' ', '', $sms['send_to']));
				$send_to_ar = array();
				foreach($sms_addresses as $value) {
					//Check if this sms is unsubscribed for the client before send the sms
					if(!$this->Sms_model->check_unsubscribed($value,$client)){
						array_push($send_to_ar,$value);
					}
				}

				if (!empty($send_to_ar)) {
					$sms['send_to'] = implode(',',$send_to_ar);

					$result = $this->send($sms);

					//Save the sms_history
					if ($result) {
						//Update the sms_history status to 1 and the pending field to 0
						$sms_history = array(
							'sms_id' => $sms_id,
							'send_to' => $sms['send_to'],
							'status' => 1,
							'pending' => 0
						);
						//If the status was 0, don't update the sent_date
						if (!$sms['status']) {
							$nowDate = new \DateTime('now');
							$sms_history['sent_date'] = $nowDate->format('Y-m-d H:i:s');
						}

						$this->Sms_model->update_sms_history($sms_history);

						//If the status was 1, create a new sms_history
						if ($sms['status']) {
							$sms_history = array(
								'body' => $sms['body'],
								'subject' => $sms['subject'],
								'send_from' => $sms['send_from'],
								'send_to' => $sms['send_to'],
								'cc' => $sms['cc'],
								'bcc' => $sms['bcc'],
								'user_id' => $sms['user_id'],
								'urn' => $sms['urn'],
								'template_id' => $sms['template_id'],
								'template_unsubscribe' => $sms['template_unsubscribe'],
								'status' => 1,
								'pending' => 0
							);
							$sms_id = $this->Sms_model->add_new_sms_history($sms_history);
						}
						$this->Sms_model->set_record_history($sms);
						$this->Sms_model->set_sms_outcome($sms['urn']);
						//Add the attachments to the sms_history_attachments table

						$output .= "sent to ".$sms['send_to']."\n\n";
					}
					else {
						$output .= "not sent: SMS from the sms server \n\n";
					}
				}
				else {
					//Update the sms_history the pending field to 0. We did not send the sms because is unsubscribed for this client
					$sms_history = array(
						'sms_id' => $sms_id,
						'pending' => 0,
					);
					$this->Sms_model->update_sms_history($sms_history);
					$output .= "not sent: sms number unsubscribed \n\n";
				}
			}
		}
		else {
			$output .= "No pending sms to be sms messages. \n\n";
		}


		echo $output;
	}

    private function send ($form) {
    	//send sms
    	return $result;
    }


    
    //Delete sms from the history
    public function delete_sms() {
        user_auth_check();
    	if ($this->input->is_ajax_request()) {
    		$sms_id = intval($this->input->post('sms_id'));
    	
    		$result = $this->Email_model->delete_sms($sms_id);
    	
    		echo json_encode(array(
    				"success" => $result
    		));
    	}
    }


}