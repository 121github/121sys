<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
$this->_campaigns = campaign_access_dropdown();;
        $this->load->model('Records_model');
        $this->load->model('Contacts_model');
        $this->load->model('Email_model');
    }
    
    //this function returns a json array of email data for a given record id
    public function get_emails()
    {
    	if ($this->input->is_ajax_request()) {
    		$urn = intval($this->input->post('urn'));
    		
    		$emails = $this->Email_model->get_emails($urn,5,0);
    		
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
    	$urn             = intval($this->uri->segment(4));
    	$template_id     = intval($this->uri->segment(3));
    	
    	$template = $this->Email_model->get_template($template_id);


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

        if (!empty($attachmentsForm)) {
            //Add the attachments to the form
            $form['template_attachments'] = $attachmentsForm;
        }

    	
    	//Send the email
    	$email_sent = $this->send($form);
        unset($form['template_attachments']);
    	
    	//Save the email in the Email History table
    	if ($email_sent) {
    		
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
    	
    	$this->email->from($form['send_from']);
    	$this->email->to($form['send_to']);
    	$this->email->cc($form['cc']);
    	$this->email->bcc($form['bcc']);
    	$this->email->subject($form['subject']);
    	$this->email->message($form['body']);

        $tmp_path = '';
        $user_id = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;

        foreach ($form['template_attachments'] as $attachment) {
            if (strlen($attachment['path'])>0) {
                $tmp_path = substr($attachment['path'], 0, strripos($attachment['path'], "/")+1)."tmp_".$user_id."/";
                $tmp_path = strstr('./'.$tmp_path, 'upload');
                $file_path = strstr('./'.$attachment['path'], 'upload');
                //Create tmp dir if it does not exist
                if (!file_exists($tmp_path)) {
                    mkdir($tmp_path, 0777, true);
                }

                if (!copy($file_path,$tmp_path.$attachment['name'])) {
                    echo "failed to copy file...";
                }
                else {
                    $this->email->attach($tmp_path.$attachment['name']);
                }
            }
        }

        $result = $this->email->send();
    	//$this->email->print_debugger();
    	$this->email->clear();

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
    	if ($this->input->is_ajax_request()) {
    		$email_id = intval($this->input->post('email_id'));
    	
    		$result = $this->Email_model->delete_email($email_id);
    	
    		echo json_encode(array(
    				"success" => $result
    		));
    	}
    }
}