<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
		$this->load->model('User_model');
        $this->load->model('Records_model');
        $this->load->model('Contacts_model');
        $this->load->model('Email_model');
    }
    
    //this function returns a json array of email data for a given record id
    public function get_emails()
    {
        user_auth_check();
    	if ($this->input->is_ajax_request()) {
    		$urn = intval($this->input->post('urn'));
            $limit = (intval($this->input->post('limit')))?intval($this->input->post('limit')):NULL;
    		
    		$emails = $this->Email_model->get_emails($urn,$limit,0);
    		
    		echo json_encode(array(
    			"success" => true,
    			"data" => $emails
    		));
    	}
    }

    //this function returns a json array of email data for a given filter parameters
    public function get_emails_by_filter()
    {
        user_auth_check();
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $emails = $this->Email_model->get_emails_by_filter($form);

            echo json_encode(array(
                "success" => true,
                "data" => $emails
            ));
        }
    }
    
    //this function returns a json array of email data for a given record id
    public function get_email()
    {
        user_auth_check();
    	if ($this->input->is_ajax_request()) {
    		$email_id = intval($this->input->post('email_id'));
    
    		$email = $this->Email_model->get_email_by_id($email_id);
            $attachments = $this->Email_model->get_attachments_by_email_id($email_id);
    
    		echo json_encode(array(
    				"success" => true,
    				"data" => $email,
                    "attachments" => $attachments
    		));
    	}
    }
    
    //load all the fields into a new email form
    public function create()
    {
        user_auth_check();
		$this->_campaigns = campaign_access_dropdown();
    	$urn             = intval($this->uri->segment(4));
    	$template_id     = intval($this->uri->segment(3));
    	$placeholder_data = $this->Email_model->get_placeholder_data($urn);
    	$template = $this->Email_model->get_template($template_id);
		$last_comment = $this->Records_model->get_last_comment($urn);
		
		$placeholder_data = $this->Email_model->get_placeholder_data($urn);
		$placeholder_data[0]['comments'] = $last_comment;
		
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
                'pageId' => 'Create-survey',
    			'title' => 'Send new email',
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
    					'email.js',
    					'plugins/summernote/summernote.min.js',
    					'plugins/jqfileupload/vendor/jquery.ui.widget.js',
    					'plugins/jqfileupload/jquery.iframe-transport.js',
    					'plugins/jqfileupload/jquery.fileupload.js',
                        'plugins/jqfileupload/jquery.fileupload-process.js',
                        'plugins/jqfileupload/jquery.fileupload-validate.js'
    			),
    	);
    
    	$this->template->load('default', 'email/new_email.php', $data);
  
    }
    
	public function unsubscribe(){
		if ($this->input->is_ajax_request()) {
			$data = $this->input->post();
			$data['ip_address'] = $_SERVER['REMOTE_ADDR'];
			//check the email address
			$this->load->helper('email');
			if(!valid_email($data['email_address'])){
			echo json_encode(array("success"=>false,"msg"=>"That is not a valid email address"));
			exit;	
			}
			
			if($this->Email_model->unsubscribe($data)){
			echo json_encode(array("success"=>true,"msg"=>"Your email address has been removed from the mailing list"));
			} else {
			echo json_encode(array("success"=>false,"msg"=>"There was a problem removing your email. Please make sure you are using the unbsubscribe link in the email you recieved"));	
			}
		} else 
		{
	$template_id = base64_decode($this->uri->segment(3));
	$urn = 	base64_decode($this->uri->segment(4));
	
	$client_id = $this->Records_model->get_client_from_urn($urn);
	$check = $this->Email_model->check_email_history($template_id,$urn);
	if($check){
	  $data = array(
    			'urn' => $urn,
    			'title' => 'Unsubscribe',
				'client_id'=>$client_id,
				'urn'=>$urn);
			$this->template->load('default', 'email/unsubscribe.php', $data);
	} else {
		  $data = array(
    			'msg' => "The company you tried to unsubscribe from does not exist on our system",
    			'title' => 'Invalid email');
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
    			$aux[$key]["email"] = $contact['visible']['Email address'];
				}
    		}
    		$contacts = $aux;
    		
    		echo json_encode(array(
    				"success" => true,
    				"data" => $contacts
    		));
    	}
    }
    
    //Send an email
    public function send_email() {
        user_auth_check();
    	$form = $this->input->post();

		
    	$form['body'] = base64_decode($this->input->post('body'));

        //Status false by default, before sent the email
        $form['status'] = false;
				
		$urn = intval($this->input->post('urn'));
		$last_comment = $this->Records_model->get_last_comment($urn);
		$placeholder_data = $this->Email_model->get_placeholder_data($urn);
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
                "msg" => "You have empty placeholders in the email contents. Please edit it first!"
    	));
		exit;
		}
		
    	//Delete duplicates email addresses
    	$from = array_unique(explode(",", $form['send_from']));
    	$form['send_from'] = implode(",", $from);
    	$to = array_unique(explode(",", $form['send_to']));
    	$form['send_to'] = implode(",", $to);
    	$cc = array_unique(explode(",", $form['cc']));
    	$form['cc'] = implode(",", $cc);
    	$bcc = array_unique(explode(",", $form['bcc']));
    	$form['bcc'] = implode(",", $bcc);
		
        //Attachments
        $attachmentsForm = array();
        if (!empty($form['template_attachments'])) {
            $attachmentsForm = $form['template_attachments'];
            $attachmentsForm = explode(",", $attachmentsForm);
            $aux = array();
            foreach ($attachmentsForm as $attachment) {
                if (strripos($attachment, "?")) {
                    $name = substr($attachment, strripos($attachment, "?") + 1);
                    $path = substr($attachment, 0, strripos($attachment, "?"));
                    $element = array("name" => $name, "path" => $path);
                }
                else {
                    $name = substr($attachment, strripos($attachment, "/") + 1);
                    $element = array("name" => $name, "path" => $attachment);
                }
                array_push($aux, $element);
            }
            $attachmentsForm = $aux;
        }
        //Add the template attachments to the list
        if ($templateAttachList = $this->Email_model->get_attachments_by_template_id($form['template_id'])) {
            foreach ($templateAttachList as $attach) {
                //If the attachment is checked we add it to the form
                if(isset($form[$attach['id']])){
                    array_push($attachmentsForm, $attach);
                    unset($form[$attach['id']]);
                }
            }
        }

        $msg = "";
		$client = $this->Records_model->get_client_from_urn($form['urn']);
		if($this->Email_model->check_unsubscribed($form['send_to'],$client)){
			echo json_encode(array(
    			"success" => false,
                "msg" => "One or more emails have unsubscribed, unable to send"
    	));
		exit;
		}

    	//Save the email in the history table
        unset($form['template_attachments']);
        $email_id = $this->Email_model->add_new_email_history($form);
        $response = ($email_id)?true:false;
		$form['email_id'] = $email_id;
        if (!$response) {
            $msg = "Error saving the email history. The email was not sent.";
        }
        else {
            if (!empty($attachmentsForm)) {
                //Save the new attachments in the email_history table
                $response = $this->save_attachment_by_email($attachmentsForm, $email_id);

                //Add the attachments to the form
                $form['template_attachments'] = $attachmentsForm;

                if (!$response) {
                    $msg = "Error saving the attachments in the email history table. The email was not sent.";
                }
            }

            if ($response) {
                //Add the tracking image to know if the email is read
                $form_to_send = $form;
             
                $webform = $this->Email_model->get_webform_id($placeholder_data[0]['campaign_id']);
                //if a webform placeholder is in the email create the link
                $url = "http://www.121system.com/webforms/remote/".$placeholder_data[0]['campaign_id']."/".$urn."/".$webform."/".$email_id;
                $form_to_send['body'] = str_replace("[webform]",$url,$form_to_send['body']);

                //Send the email
                $email_sent = $this->send($form_to_send);
                unset($form['template_attachments']);

                //Update the status in the Email History table
                if ($email_sent) {
                    $form['email_id'] = $email_id;
                    $form['status'] = true;
                    $this->Email_model->update_email_history($form);
                    $response = true;
                    $msg = "Email sent successfully!";
                }
                else {
                    $response = false;
                    $msg = "The email was not sent. Please, check that the attached are still in the server or talk with your Administrator";
                }
            }
        }
    	
    	echo json_encode(array(
    			"success" => $response,
                "msg" => $msg
    	));
    }
    
	public function trigger_email(){
        user_auth_check();
		if(isset($_SESSION['email_triggers'])){
			$urn = intval($this->input->post('urn'));
		
		foreach($_SESSION['email_triggers'] as $template_id => $recipients){
			foreach($recipients['email'] as $name => $email_address){
			//create the form structure to pass to the send function
			$form = $this->Email_model->template_to_form($template_id);
			$form['send_to'] = $email_address;
			$form['bcc'] = $recipients['bcc'];
			$form['cc'] = $recipients['cc'];
			$form['urn'] = $urn;
		$last_comment = $this->Records_model->get_last_comment($urn);
		$placeholder_data = $this->Email_model->get_placeholder_data($urn);
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
		unset($_SESSION['email_triggers']);
	}

	/**
	 *
	 * Send pending emails
	 *
	 *
	 */
	public function send_pending_emails() {

		$output = "";
		$output .= "Sending pending emails... \n\n";
		$limit = 50;
		$campaign = false;
		if(intval($this->uri->segment(3))>0){
		$limit = $this->uri->segment(3);
		}
		if(intval($this->uri->segment(4))>0){
		$croncode = $this->uri->segment(4);
		}
		
		//Get the oldest 50 mails pending to be sent
		$pending_emails = $this->Email_model->get_pending_emails($limit,$croncode);
		if (!empty($pending_emails)) {
			foreach($pending_emails as $email) {

				$client = $this->Records_model->get_client_from_urn($email['urn']);
				$email_id = $email['email_id'];

				$output .= $email['urn']."... ";
				//Remove the addresses that are unsubscribed for this client
				$email_addresses = explode(',',str_replace(' ', '', $email['send_to']));
				$send_to_ar = array();
				foreach($email_addresses as $value) {
					//Check if this email is unsubscribed for the client before send the email
					if(!$this->Email_model->check_unsubscribed($value,$client)){
						array_push($send_to_ar,$value);
					}
				}

				if (!empty($send_to_ar)) {
					$email['send_to'] = implode(',',$send_to_ar);

					$attachments = $this->Email_model->get_attachments_by_template_id($email['template_id']);
					$email['template_attachments'] = $attachments;

					$result = $this->send($email);

					//Save the email_history
					if ($result) {
						//Update the email_history status to 1 and the pending field to 0
						$email_history = array(
							'email_id' => $email_id,
							'send_to' => $email['send_to'],
							'status' => 1,
							'pending' => 0
						);
						//If the status was 0, don't update the sent_date
						if (!$email['status']) {
							$nowDate = new \DateTime('now');
							$email_history['sent_date'] = $nowDate->format('Y-m-d H:i:s');
						}

						$this->Email_model->update_email_history($email_history);

						//If the status was 1, create a new email_history
						if ($email['status']) {
							$email_history = array(
								'body' => $email['body'],
								'subject' => $email['subject'],
								'send_from' => $email['send_from'],
								'send_to' => $email['send_to'],
								'cc' => $email['cc'],
								'bcc' => $email['bcc'],
								'user_id' => $email['user_id'],
								'urn' => $email['urn'],
								'template_id' => $email['template_id'],
								'template_unsubscribe' => $email['template_unsubscribe'],
								'status' => 1,
								'pending' => 0
							);
							$email_id = $this->Email_model->add_new_email_history($email_history);
						}
						$this->Email_model->set_record_history($email);
						$this->Email_model->set_email_outcome($email['urn']);
						//Add the attachments to the email_history_attachments table
						foreach($attachments as $attachment) {
							$this->Email_model->insert_attachment_by_email_id($email_id, $attachment);
						}
						$output .= "sent to ".$email['send_to']."\n\n";
					}
					else {
						$output .= "not sent: ERROR from the email server \n\n";
					}
				}
				else {
					//Update the email_history the pending field to 0. We did not send the email because is unsubscribed for this client
					$email_history = array(
						'email_id' => $email_id,
						'pending' => 0,
					);
					$this->Email_model->update_email_history($email_history);
					$output .= "not sent: email addresses unsubscribed \n\n";
				}
			}
		}
		else {
			$output .= "No pending emails to be sent. \n\n";
		}


		echo $output;
	}

    private function send ($form) {
    	
		
    	$this->load->library('email');
    	
    	$config = array("smtp_host"=>"mail.121system.com",
		"smtp_user"=>"mail@121system.com",
		"smtp_pass"=>"L3O9QDirgUKXNE7rbNkP",
		"smtp_port"=>25);
    	
    	//Get the server conf if exist
    	if ($template = $this->Email_model->get_template($form['template_id'])) {
    		if($template['template_hostname']) {$config['smtp_host'] =  $template['template_hostname']; }
    		if ($template['template_port']) { $config['smtp_port'] = $template['template_port']; }
    		if ($template['template_username']) { $config['smtp_user'] = $template['template_username']; }
    		if ($template['template_password']) { $config['smtp_pass'] = $template['template_password']; }
    		if ($template['template_username']) { $config['smtp_user'] = $template['template_username']; }
    		//$encryption = $template['template_encryption'];
    	}
    	$config['mailtype'] = 'html';
    	
		//unsubscribe link
		if(@$form['template_unsubscribe'] == "1"){
			$form['body'] .= "<hr><p style='font-family:calibri,arial;font-size:10px;color:#666'>If you no longer wish to recieve emails from us please click here to <a href='http://www.121system.com/email/unsubscribe/".base64_encode($form['template_id'])."/".base64_encode($form['urn'])."'>unsubscribe</a></p>";
		};
		if(isset($form['email_id'])){
		$form['body'] .= "<br><img style='display:none;' src='http://www.121system.com/email/image?id=".$form['email_id']."'>";
		}
		    	$this->email->initialize($config);

		$this->email->from($form['send_from']);
    	$this->email->to($form['send_to']);
    	$this->email->cc($form['cc']);
    	$this->email->bcc($form['bcc']);
    	$this->email->subject($form['subject']);
    	$this->email->message($form['body']);


		
        $tmp_path = '';
        $user_id = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;
		if(isset($form['template_attachments'])&&count($form['template_attachments'])>0){
			foreach ($form['template_attachments'] as $attachment) {
				if (strlen($attachment['path'])>0) {
					$tmp_path = substr($attachment['path'], 0, strripos($attachment['path'], "/")+1)."tmp_".$user_id.uniqid()."/";
					$tmp_path = strstr('./'.$tmp_path, 'upload');
					$file_path = strstr('./'.$attachment['path'], 'upload');
					//Create tmp dir if it does not exist
					if (@!file_exists($tmp_path)) {
						mkdir($tmp_path, 0777, true);
					}

					if (@!copy($file_path,$tmp_path.$attachment['name'])) {
						return false;
					}
					else {
						$this->email->attach($tmp_path.$attachment['name']);
					}
				}
			}
		}
        $result = $this->email->send();
    	$this->email->print_debugger();
		$this->email->clear(TRUE);

		//Remove tmp dir
        if (file_exists($tmp_path)) {
            $this->removeDirectory($tmp_path);
        }

    	return $result;
    }

    private function removeDirectory($path) {

        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);

        return;
    }

    /**
     * Insert new attachments for a email history
     *
     * @param unknown $attachment_list
     * @param unknown $email_id
     * @return boolean
     */
    public function save_attachment_by_email ($attachment_list, $email_id) {
    	$response = true;
    
    	foreach ($attachment_list as $attachment) {
    		if (!($this->Email_model->insert_attachment_by_email_id($email_id, $attachment))) {
    			$response = false;
    		}
    	}
    
    	return $response;
    }
    
    //Delete email from the history
    public function delete_email() {
        user_auth_check();
    	if ($this->input->is_ajax_request()) {
    		$email_id = intval($this->input->post('email_id'));
    	
    		$result = $this->Email_model->delete_email($email_id);
    	
    		echo json_encode(array(
    				"success" => $result
    		));
    	}
    }

    //Check if the email was received and opened
    public function image()
    {

       // Create an image, 1x1 pixel in size
        $im=imagecreate(1,1);

        // Set the background colour
        $white=imagecolorallocate($im,255,255,255);

        // Allocate the background colour
        imagesetpixel($im,1,1,$white);

        // Set the image type
        header("content-type:image/jpg");

        // Create a JPEG file from the image
        imagejpeg($im);

        // Free memory associated with the image
        imagedestroy($im);


        if(isset($_GET['id'])) {
            //save to database
            $email_id = $_GET['id'];
            $email = $this->Email_model->get_email_by_id($email_id);
            if (!$email['read_confirmed']) {
                $form = array();
                $form['email_id'] = $email_id;
                $form['read_confirmed'] = 1;
                $datestring = "Y-m-d H:i:s";
                $time = time();
                $form['read_confirmed_date'] = date($datestring, $time);
                $result = $this->Email_model->update_email_history($form);
            }
        }

        $this->User_model->close_hours();
        session_destroy();
    }
}