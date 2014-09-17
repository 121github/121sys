<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();;
        $this->load->model('Records_model');
        $this->load->model('Contacts_model');
        $this->load->model('Email_model');
    }
    
    //this function returns a json array of email data for a given record id
    public function get_emails()
    {
    	if ($this->input->is_ajax_request()) {
    		$record_urn = intval($this->input->post('record_urn'));
    		
    		$emails = $this->Email_model->get_emails($record_urn,5,0);
    		
    		echo json_encode(array(
    			"success" => true,
    			"data" => $emails
    		));
    	}
    }
    
    //this function returns a json array of email data for a given record id
    public function get_email()
    {
    	if ($this->input->is_ajax_request()) {
    		$email_id = intval($this->input->post('email_id'));
    
    		$email = $this->Email_model->get_email_by_id($email_id);
    
    		echo json_encode(array(
    				"success" => true,
    				"data" => $email
    		));
    	}
    }
    
    //load all the fields into a new email form
    public function create()
    {
    	$urn             = intval($this->uri->segment(4));
    	$template_id     = intval($this->uri->segment(3));
    	
    	$template = $this->Email_model->get_template($template_id);
    	
    	$data = array(
    			'urn' => $urn,
    			'pageId' => 'Create-survey',
    			'title' => 'Send new email',
    			'record_urn' => $urn,
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
    			),
    	);
    
    	$this->template->load('default', 'email/new_email.php', $data);
    
    }
    
    //Get the contacts
    public function get_contacts() {
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
    	$form = $this->input->post();
    	$form['body'] = base64_decode($this->input->post('body'));
    	
    	//Delete duplicates email addresses
    	$from = array_unique(explode(",", $form['from']));
    	$form['from'] = implode(",", $from);
    	$to = array_unique(explode(",", $form['to']));
    	$form['to'] = implode(",", $to);
    	$cc = array_unique(explode(",", $form['cc']));
    	$form['cc'] = implode(",", $cc);
    	$bcc = array_unique(explode(",", $form['bcc']));
    	$form['bcc'] = implode(",", $bcc);
    	
    	//Send the email
    	$email_sent = $this->send($form);
    	//$email_sent = true;
    	
    	//Save the email in the Email History table
    	if ($email_sent) {
    		//Check if the user selected any attachment for this template
    		$attachmentsForm = array();
    		if (!empty($form['template_attachments'])) {
    			$attachmentsForm = $form['template_attachments'];
    			$attachmentsForm = explode(",", $attachmentsForm);
    			$aux = array();
    			foreach ($attachmentsForm as $attachment) {
    				$name = substr($attachment, strripos($attachment, "/") + 1);
    				$element = array("name" => $name, "path" => $attachment);
    				array_push($aux, $element);
    			}
    			$attachmentsForm = $aux;
    		}
    		unset($form['template_attachments']);
    		//Add the template attachments to the list
    		if ($templateAttachList = $this->Email_model->get_attachments_by_template_id($form['template_id'])) {
    			foreach ($templateAttachList as $attach) {
    				array_push($attachmentsForm, $attach);
    			}
    		}
    		
    		$insert_id = $this->Email_model->add_new_email_history($form);
    		$response = ($insert_id)?true:false;
    		
    		if ($response && !empty($attachmentsForm)) {
    			//Save the new attachments in the email_history table
    			$response = $this->save_attachment_by_email($attachmentsForm, $insert_id);
    		}
    	}
    	
    	echo json_encode(array(
    			"success" => $response,
    	));
    }
    
    private function send ($form) {
    	
    	$this->load->library('email');
    	
    	$config = array();
    	
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
    	
    	$this->email->initialize($config);
    	
    	$this->email->from($form['from']);
    	$this->email->to($form['to']);
    	$this->email->cc($form['cc']);
    	$this->email->bcc($form['bcc']);
    	$this->email->subject($form['subject']);
    	$this->email->message($form['body']);

    	$result = $this->email->send();
    	//$this->email->print_debugger();
    	$this->email->clear();
    	
    	return $result;
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
    	if ($this->input->is_ajax_request()) {
    		$email_id = intval($this->input->post('email_id'));
    	
    		$result = $this->Email_model->delete_email($email_id);
    	
    		echo json_encode(array(
    				"success" => $result
    		));
    	}
    }
}