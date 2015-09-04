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
        $this->load->model('Appointments_model');
    }


    public function bulk_email()
    {
        if ($this->input->is_ajax_request() && $this->input->post('list')) {
            $lines = lines_to_list($this->input->post('list'));
            echo json_encode(array("count" => count($lines), "urns" => "(" . implode(",", $lines) . ")"));
            exit;
        }
        user_auth_check();
        $this->load->model('Form_model');
        $templates = $this->Form_model->get_templates();
        $data = array(
            'title' => 'Bulk Email',
            'page' => 'bulk-email',
            'templates' => $templates
        );

        $this->template->load('default', 'email/bulk_email.php', $data);

    }

    //this function returns a json array of email data for a given record id
    public function get_emails()
    {
        user_auth_check();
        if ($this->input->is_ajax_request()) {
            $urn = intval($this->input->post('urn'));
            $limit = (intval($this->input->post('limit'))) ? intval($this->input->post('limit')) : NULL;

            $emails = $this->Email_model->get_emails($urn, $limit, 0);

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
        $urn = intval($this->uri->segment(4));
        $template_id = intval($this->uri->segment(3));
        $placeholder_data = $this->Email_model->get_placeholder_data($urn);
        $template = $this->Email_model->get_template($template_id);
        $last_comment = $this->Records_model->get_last_comment($urn);

        $placeholder_data = $this->Email_model->get_placeholder_data($urn);
        $placeholder_data[0]['comments'] = $last_comment;
        $this->firephp->log($placeholder_data);
        if (count($placeholder_data)) {
            foreach ($placeholder_data[0] as $key => $val) {
                if ($key == "fullname") {
                    $val = str_replace("Mr ", "", $val);
                    $val = str_replace("Mrs ", "", $val);
                    $val = str_replace("Miss ", "", $val);
                    $val = str_replace("Ms ", "", $val);
                }
                if (strpos($template['template_body'], "[$key]") !== false) {
                    if (empty($val)) {
                        setcookie("placeholder_error", $key, time() + (60), "/");
                        if ($key == "start") {
                            $template['template_body'] = str_replace("[$key]", "<span style=\"color:red\">** NO APPOINTMENT FOUND **</span>", $template['template_body']);
                        } else {
                            $template['template_body'] = str_replace("[$key]", "<span style=\"color:red\">** [$key] WAS EMPTY **</span>", $template['template_body']);
                        }
                    } else {
                        $template['template_body'] = str_replace("[$key]", $val, $template['template_body']);
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
                'plugins/jqfileupload/vendor/jquery.ui.widget.js',
                'plugins/jqfileupload/jquery.iframe-transport.js',
                'plugins/jqfileupload/jquery.fileupload.js',
                'plugins/jqfileupload/jquery.fileupload-process.js',
                'plugins/jqfileupload/jquery.fileupload-validate.js'
            ),
        );

        $this->template->load('default', 'email/new_email.php', $data);

    }

    public function unsubscribe()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $data['ip_address'] = $_SERVER['REMOTE_ADDR'];
            //check the email address
            $this->load->helper('email');
            if (!valid_email($data['email_address'])) {
                echo json_encode(array("success" => false, "msg" => "That is not a valid email address"));
                exit;
            }

            if ($this->Email_model->unsubscribe($data)) {
                echo json_encode(array("success" => true, "msg" => "Your email address has been removed from the mailing list"));
            } else {
                echo json_encode(array("success" => false, "msg" => "There was a problem removing your email. Please make sure you are using the unbsubscribe link in the email you recieved"));
            }
        } else {
            $template_id = base64_decode($this->uri->segment(3));
            $urn = base64_decode($this->uri->segment(4));

            $client_id = $this->Records_model->get_client_from_urn($urn);
            $check = $this->Email_model->check_email_history($template_id, $urn);
            if ($check) {
                $data = array(
                    'urn' => $urn,
                    'title' => 'Unsubscribe',
                    'client_id' => $client_id,
                    'urn' => $urn);
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
    public function get_contacts()
    {
        user_auth_check();
        if ($this->input->is_ajax_request()) {
            $urn = intval($this->input->post('urn'));

            $contacts = $this->Contacts_model->get_contacts($urn);

            $aux = array();
            foreach ($contacts as $key => $contact) {
                if ($contact['visible']['Email address']) {
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
    public function send_email()
    {
        user_auth_check();
        $form = $this->input->post();


        $form['body'] = $this->input->post('template_body');
        unset($form['template_body']);
        //Status false by default, before sent the email
        $form['status'] = false;

        $urn = intval($this->input->post('urn'));
        $last_comment = $this->Records_model->get_last_comment($urn);
        $placeholder_data = $this->Email_model->get_placeholder_data($urn);
        $placeholder_data[0]['comments'] = $last_comment;
        if (count($placeholder_data)) {
            foreach ($placeholder_data[0] as $key => $val) {
                if ($key == "fullname") {
                    $val = str_replace("Mr ", "", $val);
                    $val = str_replace("Mrs ", "", $val);
                    $val = str_replace("Mrs ", "", $val);
                }
                $body = str_replace("[$key]", $val, $form['body']);
            }
        }

        if (strpos($body, "WAS EMPTY") !== false) {
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
                } else {
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
                if (isset($form[$attach['id']])) {
                    array_push($attachmentsForm, $attach);
                    unset($form[$attach['id']]);
                }
            }
        }

        $msg = "";
        $client = $this->Records_model->get_client_from_urn($form['urn']);
        if ($this->Email_model->check_unsubscribed($form['send_to'], $client)) {
            echo json_encode(array(
                "success" => false,
                "msg" => "One or more emails have unsubscribed, unable to send"
            ));
            exit;
        }

        //Save the email in the history table
        unset($form['template_attachments']);
        $email_id = $this->Email_model->add_new_email_history($form);
        $response = ($email_id) ? true : false;
        $form['email_id'] = $email_id;
        if (!$response) {
            $msg = "Error saving the email history. The email was not sent.";
        } else {
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
                $url = base_url()."webforms/remote/" . $placeholder_data[0]['campaign_id'] . "/" . $urn . "/" . $webform . "/" . $email_id;
                $form_to_send['body'] = str_replace("[webform]", $url, $form_to_send['body']);

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
                } else {
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

    public function trigger_email()
    {
        user_auth_check();
        if (isset($_SESSION['email_triggers'])) {
            $urn = intval($this->input->post('urn'));

            foreach ($_SESSION['email_triggers'] as $template_id => $recipients) {
                foreach ($recipients['email'] as $name => $email_address) {
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
                    if (count($placeholder_data)) {
                        foreach ($placeholder_data[0] as $key => $val) {
                            if ($key == "fullname") {
                                $val = str_replace("Mr ", "", $val);
                                $val = str_replace("Mrs ", "", $val);
                                $val = str_replace("Mrs ", "", $val);
                            }
                            $form['body'] = str_replace("[$key]", $val, $form['body']);
                        }
                    }
                    if ($this->send($form)) {
                        $email_history = array(
                            'body' => $form['body'],
                            'subject' => $form['subject'],
                            'send_from' => $form['send_from'],
                            'send_to' => $form['send_to'],
                            'cc' => implode(",", $form['cc']),
                            'bcc' => implode(",", $form['bcc']),
                            'user_id' => $_SESSION['user_id'],
                            'urn' => $form['urn'],
                            'template_id' => $form['template_id'],
                            'template_unsubscribe' => $form['template_unsubscribe'],
                            'status' => 1,
                            'pending' => 0
                        );
                        $email_id = $this->Email_model->add_new_email_history($email_history);

                    }
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
    public function send_pending_emails()
    {
        //$this->Email_model->remove_title();
        $output = "";
        $output .= "Sending pending emails... \n\n";
        $limit = 50;
        $croncode = false;
        $campaign = false;
        if (intval($this->uri->segment(3)) > 0) {
            $limit = $this->uri->segment(3);
        }
        if (intval($this->uri->segment(4)) > 0) {
            $croncode = $this->uri->segment(4);
        }

        //Get the oldest 50 mails pending to be sent
        $pending_emails = $this->Email_model->get_pending_emails($limit, $croncode);
        if (!empty($pending_emails)) {
            foreach ($pending_emails as $email) {

                $client = $this->Records_model->get_client_from_urn($email['urn']);
                $email_id = $email['email_id'];

                $output .= $email['urn'] . "... ";
                //Remove the addresses that are unsubscribed for this client
                $email_addresses = explode(',', str_replace(' ', '', $email['send_to']));
                $send_to_ar = array();
                foreach ($email_addresses as $value) {
                    //Check if this email is unsubscribed for the client before send the email
                    if (!$this->Email_model->check_unsubscribed($value, $client)) {
                        array_push($send_to_ar, $value);
                    }
                }

                if (!empty($send_to_ar)) {
                    $email['send_to'] = implode(',', $send_to_ar);

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
                        foreach ($attachments as $attachment) {
                            $this->Email_model->insert_attachment_by_email_id($email_id, $attachment);
                        }
                        $output .= "sent to " . $email['send_to'] . "\n\n";
                    } else {
                        $output .= "not sent: ERROR from the email server \n\n";
                    }
                } else {
                    //Update the email_history the pending field to 0. We did not send the email because is unsubscribed for this client
                    $email_history = array(
                        'email_id' => $email_id,
                        'pending' => 0,
                    );
                    $this->Email_model->update_email_history($email_history);
                    $output .= "not sent: email addresses unsubscribed \n\n";
                }
            }
        } else {
            $output .= "No pending emails to be sent. \n\n";
        }


        echo $output;
    }

    private function send($form)
    {
        $this->load->library('email');

        //Get the server conf if exist
        if (isset($form['template_id']) && $template = $this->Email_model->get_template($form['template_id'])) {
            if ($template['template_hostname']) {
                $config['smtp_host'] = $template['template_hostname'];
            }
            if ($template['template_port']) {
                $config['smtp_port'] = $template['template_port'];
            }
            if ($template['template_username']) {
                $config['smtp_user'] = $template['template_username'];
            }
            if ($template['template_password']) {
                $config['smtp_pass'] = $template['template_password'];
            }
            if ($template['template_username']) {
                $config['smtp_user'] = $template['template_username'];
            }
            //$encryption = $template['template_encryption'];
        }

        //unsubscribe link
        if (isset($form['template_unsubscribe']) && @$form['template_unsubscribe'] == "1") {
            $form['body'] .= "<hr><p style='font-family:calibri,arial;font-size:10px;color:#666'>If you no longer wish to recieve emails from us please click here to <a href='".base_url()."email/unsubscribe/" . base64_encode($form['template_id']) . "/" . base64_encode($form['urn']) . "'>unsubscribe</a></p>";
        };
        if (isset($form['email_id'])) {
            $form['body'] .= "<br><img style='display:none;' src='".base_url()."email/image?id=" . $form['email_id'] . "'>";
        }

        $config = $this->config->item('email');
        $this->email->initialize($config);
        $this->email->from($form['send_from']);
        $this->email->to($form['send_to']);
        $this->email->cc($form['cc']);
        $this->email->bcc($form['bcc']);
        $this->email->subject($form['subject']);
        $this->email->message($form['body']);

        $tmp_path = '';
        $user_id = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : NULL;
        if (isset($form['template_attachments']) && count($form['template_attachments']) > 0) {
            foreach ($form['template_attachments'] as $attachment) {
                if (strlen($attachment['path']) > 0) {
                    $tmp_path = substr($attachment['path'], 0, strripos($attachment['path'], "/") + 1) . "tmp_" . $user_id . uniqid() . "/";
                    $tmp_path = strstr('./' . $tmp_path, 'upload');
                    $file_path = strstr('./' . $attachment['path'], 'upload');
                    //Create tmp dir if it does not exist
                    if (@!file_exists($tmp_path)) {
                        mkdir($tmp_path, 0777, true);
                    }

                    if (@!copy($file_path, $tmp_path . $attachment['name'])) {
                        return false;
                    } else {
                        $disposition = (isset($attachment['disposition'])?$attachment['disposition']:"attachment");
                        $this->email->attach($tmp_path . $attachment['name'], $disposition);
                    }
                }
            }
        }

        $result = $this->email->send();
        //print_r($this->email->print_debugger());
        //$this->firephp->log($this->email->print_debugger([$include = array('headers', 'subject', 'body')]));
        $this->email->clear(TRUE);

        //Remove tmp dir
        if (file_exists($tmp_path)) {
            $this->removeDirectory($tmp_path);
        }

        return $result;
    }

    private function removeDirectory($path)
    {

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
    public function save_attachment_by_email($attachment_list, $email_id)
    {
        $response = true;

        foreach ($attachment_list as $attachment) {
            if (!($this->Email_model->insert_attachment_by_email_id($email_id, $attachment))) {
                $response = false;
            }
        }

        return $response;
    }

    //Delete email from the history
    public function delete_email()
    {
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
        $im = imagecreate(1, 1);

        // Set the background colour
        $white = imagecolorallocate($im, 255, 255, 255);

        // Allocate the background colour
        imagesetpixel($im, 1, 1, $white);

        // Set the image type
        header("content-type:image/jpg");

        // Create a JPEG file from the image
        imagejpeg($im);

        // Free memory associated with the image
        imagedestroy($im);


        if (isset($_GET['id'])) {
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

    public function book_appointment_ics() {
        $this->load->library('email');

        user_auth_check();
        if ($this->input->is_ajax_request()) {
            $appointment_id = $this->input->post('appointment_id');
            $description = $this->input->post('description');


            //Get the appointment info
            $appointment = $this->Appointments_model->getAppointmentById($appointment_id);

            //TODO select the users that will receive the ics file
            //Get the receivers, that sould be the attendees with ics field true and if the branch_id is set on the appointment, send to the
            //users related to the branch_region
            $ics_addresses = $this->Email_model->get_ics_email_addresses($appointment_id);

            $email_addresses = array_filter(array_unique(array_merge(
                explode(',', $ics_addresses['attendees']),
                explode(',', $ics_addresses['region_users']),
                explode(',', $ics_addresses['branch_users']),
                explode(',', $ics_addresses['branch_email']),
                explode(',', $ics_addresses['region_email'])
            )));

            if (!empty($email_addresses)) {
                $send_to = implode(", ",$email_addresses);

                $send_from = "appointments@121system.com";

                //Get contact info
                $contact = $this->Contacts_model->get_contact($appointment->contact_id);

                //Date
                $start_date = new \DateTime($appointment->start);
                $end_date = new \DateTime($appointment->end);
                $day = $start_date->format("jS F Y");
                $start_time = $start_date->format("G:ia");
                $end_time = $end_date->format("G:ia");

                $title = 'Appointment Booking - '.$appointment->title. ' #'.$appointment_id;

                $description = "<div><h2>Appointment booked</h2></div>";
                $description .= "<div>A new appointment have been booked for ".
                    $day." and the allocated time will be between ".$start_time." and ".$end_time.
                    ". The appointment have been booked for ".$contact['general']['fullname'].
                    " on the address: ".$appointment->address."</div><br />";

                //Contact telephones
                $contact_telephones = "";
                foreach($contact['telephone'] as $telephone) {
                    $contact_telephones .= "<tr><td>Contact Telephone (".$telephone['tel_name']."):</td><td>".$telephone['tel_num']."</td></tr>";
                }

                $appointment_table = "<table>
                    <thead><th><h3>Appointment</h3></th><th><a href='".base_url()."records/detail/".$appointment->urn."'>#".$appointment_id."</a></th>
                    <tbody>
                        <tr><td>Title:</td><td>".$appointment->title."</td></tr>
                        <tr><td>Type:</td><td>".$appointment->appointment_type."</td></tr>
                        <tr><td>Day:</td><td>".$day."</td></tr>
                        <tr><td>Time:</td><td>".$start_time." - ".$end_time."</td></tr>
                        <tr><td>Contact Name:</td><td>".$contact['general']['fullname']."</td></tr>
                        <tr><td>Contact Email:</td><td>".$contact['general']['email']."</td></tr>
                        ".$contact_telephones."
                        <tr><td>Address:</td><td>".$appointment->address."</td></tr>
                        <tr><td>Notes:</td><td>".$appointment->text."</td></tr>
                        ".($appointment->branch_id?"<tr><td>Branch:</td><td>".($appointment->branch_name?$appointment->branch_name:'')."</td></tr>":"")."
                    </tbody>
                </table>";
                $description .= "<div>".$appointment_table."</div><br />";

                //Get the webform answers for this urn
                $webform_answers = $this->Email_model->get_webform_answers_by_urn($appointment->urn);
                //Get the last one
                $webform = (isset($webform_answers[0])?$webform_answers[0]:array());
                if (!empty($webform)) {
                    $answers = "";
                    for($i=1;$i<=30;$i++) {
                        $answers .= "<tr><td>".$webform['q'.$i]."</td><td>".$webform['a'.$i]."</td></tr>";
                    }
                    $webform_table = "<table>
                    <thead style='margin-bottom: 2px;'><th colspan='2'><h3>".$webform['webform_name']."</h3></th>
                    <tbody>".$answers."</tbody>
                </table>";
                }
                else {
                    $webform_table = "<table>
                    <tbody>There is not webform for this record</tbody>
                </table>";
                }
                $description .= "<div>".$webform_table."</div><br />";

                $appointment_ics = array();
                if ($appointment) {
                    $appointment_ics['appointment_id'] = $appointment->appointment_id;
                    $appointment_ics['start_date'] = $appointment->start;
                    $appointment_ics['send_to'] = $send_to;
                    $appointment_ics['send_from'] = $send_from;
                    $appointment_ics['duration'] = strtotime($appointment->end) - strtotime($appointment->start);
                    $appointment_ics['title'] = $title;
                    $appointment_ics['location'] = $appointment->postcode;
                    $appointment_ics['uid'] = "Appointment_".$appointment->appointment_id;
                    $appointment_ics['description'] = $description;
                    $appointment_ics['sequence'] = 0;
                    $appointment_ics['method'] = 'REQUEST';
                }

                //Create the ical file to attach
                $ical = $this->createIcalFile($appointment_ics['uid'], $appointment_ics['method'], $appointment_ics['send_from'], $appointment_ics['send_to'], $appointment_ics['start_date'], $appointment_ics['duration'], $appointment_ics['title'], $appointment_ics['description'], $appointment_ics['location'], $appointment_ics['sequence']);

                //ATTACH the ics file
                //Create tmp dir if it does not exist
                $tmp_path = FCPATH . '/upload/attachments/ics';
                $filename = $appointment_ics['uid'].".ics";

                if (@!file_exists($tmp_path)) {
                    mkdir($tmp_path, 0777, true);
                }

                $handle = fopen($tmp_path."/".$filename , 'w+');
                if($handle)
                {
                    if(@!fwrite($handle, $ical )) {
                        return false;
                    }
                    else {
                        $attachments = array(
                            array(
                                "path" => $tmp_path."/".$filename,
                                "name" => $filename,
                                "disposition" => 'inline'
                            )
                        );
                    }
                }
                fclose($handle);

                //SEND MAIL
                $form_to_send = array(
                    "send_from" => "appointments@121system.com",
                    "send_to" => $appointment_ics['send_to'],
                    "cc" => null,
                    "bcc" => 'estebanc@121customerinsight.co.uk',
                    "subject" => $appointment_ics['title'],
                    "body" => $description,
                    "template_attachments" => $attachments
                );

                $email_sent = $this->send($form_to_send);

                //TODO save email_history or delete the attachment??
                //$email_history_id = $this->Email_model->add_new_email_history($form);

                //Save the appointment_ics sent
                if ($email_sent) {
                    $appointment_ics_id = $this->Appointments_model->saveAppointmentIcs($appointment_ics);
                    $appointment_ics['appointment_ics_id'] = $appointment_ics_id;
                }


                echo json_encode(array(
                    "success" => ($email_sent),
                    "appointment_ics" => $appointment_ics
                ));
            }
            else {
                echo json_encode(array(
                    "success" => "true",
                    "msg" => "no email addresses to send ics email"
                ));
            }
        }

    }

    public function update_appointment_ics() {
        $this->load->helper('email');

        $appointment_id = $this->input->post('appointment_id');
        $uid = 'Appointment_'.$appointment_id;
        $description = $this->input->post('description');
        $start_date = $this->input->post('start');
        $end_date = $this->input->post('end');
        $duration = strtotime($end_date) - strtotime($start_date);
        $location = $this->input->post('postcode');

        //Get the appointment ics info
        $last_appointment_ics = $this->Appointments_model->getLastAppointmentIcsByUid($uid);

        //If exist a previous ics sent
        if ($last_appointment_ics) {

            //Get the appointment info
            $appointment = $this->Appointments_model->getAppointmentById($appointment_id);

            //Get contact info
            $contact = $this->Contacts_model->get_contact($appointment->contact_id);

            //Date
            $start_date = ($start_date?$start_date:$last_appointment_ics->start_date);
            $start_day = new \DateTime($start_date);
            $end_date = ($end_date?$end_date:$last_appointment_ics->end_date);
            $end_day = new \DateTime($start_date);
            $day = $start_day->format("jS F Y");
            $start_time = $start_day->format("G:ia");
            $end_time = $end_day->format("G:ia");

            $description = "<div><h2>Appointment rescheduled</h2></div>";
            $description .= "An appointment have been rescheduled for ".
                $day." and the allocated time will be between ".$start_time." and ".$end_time.
                ". The appointment have been booked for ".$contact['general']['fullname'].
                " on the address: ".$appointment->address."</div><br />";

            $appointment_table = "<table>
                    <thead><th><h3>Appointment</h3></th><th><a href='".base_url()."records/detail/".$appointment->urn."'>#".$appointment_id."</a></th>
                    <tbody>
                        <tr><td>Title:</td><td>".$appointment->title."</td></tr>
                        <tr><td>Type:</td><td>".$appointment->appointment_type."</td></tr>
                        <tr><td>Day:</td><td>".$day."</td></tr>
                        <tr><td>Time:</td><td>".$start_time." - ".$end_time."</td></tr>
                        <tr><td>Contact Name:</td><td>".$contact['general']['fullname']."</td></tr>
                        <tr><td>Contact Email:</td><td>".$contact['general']['email']."</td></tr>
                        <tr><td>Address:</td><td>".$appointment->address."</td></tr>
                        <tr><td>Notes:</td><td>".$appointment->text."</td></tr>
                        ".($appointment->branch_id?"<tr><td>Branch:</td><td>".($appointment->branch_name?$appointment->branch_name:'')."</td></tr>":"")."
                    </tbody>
                </table>";
            $description .= "<div>".$appointment_table."</div><br />";

            //Get the webform answers for this urn
            $webform_answers = $this->Email_model->get_webform_answers_by_urn($appointment->urn);
            //Get the last one
            $webform = (isset($webform_answers[0])?$webform_answers[0]:array());
            if (!empty($webform)) {
                $answers = "";
                for($i=1;$i<=30;$i++) {
                    $answers .= "<tr><td>".$webform['q'.$i]."</td><td>".$webform['a'.$i]."</td></tr>";
                }
                $webform_table = "<table>
                    <thead style='margin-bottom: 2px;'><th colspan='2'><h3>".$webform['webform_name']."</h3></th>
                    <tbody>".$answers."</tbody>
                </table>";
            }
            else {
                $webform_table = "<table>
                    <tbody>There is not webform for this record</tbody>
                </table>";
            }
            $description .= "<div>".$webform_table."</div><br />";

            $appointment_ics = array();
            if ($last_appointment_ics) {
                $appointment_ics['appointment_id'] = $last_appointment_ics->appointment_id;
                $appointment_ics['start_date'] = $start_date;
                $appointment_ics['send_to'] = $last_appointment_ics->send_to;
                $appointment_ics['send_from'] = $last_appointment_ics->send_from;
                $appointment_ics['duration'] = ($duration?$duration:$last_appointment_ics->duration);
                $appointment_ics['title'] = 'Appointment Update - '.$appointment->title. ' #'.$appointment_id;
                $appointment_ics['location'] = ($location?$location:$last_appointment_ics->location);
                $appointment_ics['uid'] = $uid;
                $appointment_ics['description'] = ($description?$description:$last_appointment_ics->description);
                $appointment_ics['sequence'] = $last_appointment_ics->sequence + 1;
                $appointment_ics['method'] = 'REQUEST';
            }


            //Create the ical file to attach
            $ical = $this->createIcalFile($appointment_ics['uid'], $appointment_ics['method'], $appointment_ics['send_from'], $appointment_ics['send_to'], $appointment_ics['start_date'], $appointment_ics['duration'], $appointment_ics['title'], $appointment_ics['description'], $appointment_ics['location'], $appointment_ics['sequence']);

            //ATTACH the ics file
            //Create tmp dir if it does not exist
            $tmp_path = FCPATH . '/upload/attachments/ics';
            $filename = $appointment_ics['uid'].".ics";

            if (@!file_exists($tmp_path)) {
                mkdir($tmp_path, 0777, true);
            }

            $handle = fopen($tmp_path."/".$filename , 'w+');
            if($handle)
            {
                if(@!fwrite($handle, $ical )) {
                    return false;
                }
                else {
                    $attachments = array(
                        array(
                            "path" => $tmp_path."/".$filename,
                            "name" => $filename,
                            "disposition" => 'inline'
                        )
                    );
                }
            }
            fclose($handle);

            //SEND MAIL
            $form_to_send = array(
                "send_from" => "appointments@121system.com",
                "send_to" => $appointment_ics['send_to'],
                "cc" => null,
                "bcc" => 'estebanc@121customerinsight.co.uk',
                "subject" => $appointment_ics['title'],
                "body" => $appointment_ics['description'],
                "template_attachments" => $attachments
            );

            $email_sent = $this->send($form_to_send);

            //TODO save email_history or delete the attachment??
            //$email_history_id = $this->Email_model->add_new_email_history($form);

            //Save the appointment_ics sent
            if ($email_sent) {
                $appointment_ics_id = $this->Appointments_model->saveAppointmentIcs($appointment_ics);
                $appointment_ics['appointment_ics_id'] = $appointment_ics_id;
            }

            echo json_encode(array(
                "success" => ($email_sent),
                "appointment_ics" => $appointment_ics
            ));
        }
        else {
            echo json_encode(array(
                "success" => true,
                "msg" => "Nothing sent. There is no appointment ics sent before"
            ));
        }
    }

    public function cancel_appointment_ics() {
        $this->load->helper('email');

        $appointment_id = $this->input->post('appointment_id');
        $description = $this->input->post('description');

        $uid = 'Appointment_'.$appointment_id;

        //Get the appointment ics info
        $last_appointment_ics = $this->Appointments_model->getLastAppointmentIcsByUid($uid);

        //If exist a previous ics sent
        if ($last_appointment_ics) {

            //Get the appointment info
            $appointment = $this->Appointments_model->getAppointmentById($appointment_id);

            //Get contact info
            $contact = $this->Contacts_model->get_contact($appointment->contact_id);

            //Date
            $start_date = $last_appointment_ics->start_date;
            $end_date = $appointment->end;
            $start_day = new \DateTime($start_date);
            $day = $start_day->format("jS F Y");
            $end_day = new \DateTime($start_date);
            $start_time = $start_day->format("G:ia");
            $end_time = $end_day->format("G:ia");

            $description = "<div><h2>Appointment cancelled</h2></div>";
            $description .= "An appointment have been cancelled. It was booked for " .
                $day .
                " with " . $contact['general']['fullname'] .
                " on the address: " . $appointment->address . "</div><br />";

            $appointment_table = "<table>
                        <thead><th><h3>Appointment</h3></th><th><a href='".base_url()."records/detail/".$appointment->urn."'>#".$appointment_id."</a></th>
                        <tbody>
                            <tr><td>Title:</td><td>".$appointment->title."</td></tr>
                            <tr><td>Type:</td><td>".$appointment->appointment_type."</td></tr>
                            <tr><td>Day:</td><td>".$day."</td></tr>
                            <tr><td>Time:</td><td>".$start_time." - ".$end_time."</td></tr>
                            <tr><td>Contact Name:</td><td>".$contact['general']['fullname']."</td></tr>
                            <tr><td>Contact Email:</td><td>".$contact['general']['email']."</td></tr>
                            <tr><td>Address:</td><td>".$appointment->address."</td></tr>
                            <tr><td>Notes:</td><td>".$appointment->text."</td></tr>
                            ".($appointment->branch_id?"<tr><td>Branch:</td><td>".($appointment->branch_name?$appointment->branch_name:'')."</td></tr>":"")."
                            <tr><td>Cancellation reason:</td><td>" . $appointment->cancellation_reason . "</td></tr>
                        </tbody>
                    </table>";
            $description .= "<div>" . $appointment_table . "</div><br />";

            //Get the webform answers for this urn
            $webform_answers = $this->Email_model->get_webform_answers_by_urn($appointment->urn);
            //Get the last one
            $webform = (isset($webform_answers[0]) ? $webform_answers[0] : array());
            if (!empty($webform)) {
                $answers = "";
                for ($i = 1; $i <= 30; $i++) {
                    $answers .= "<tr><td>" . $webform['q' . $i] . "</td><td>" . $webform['a' . $i] . "</td></tr>";
                }
                $webform_table = "<table>
                        <thead style='margin-bottom: 2px;'><th colspan='2'><h3>" . $webform['webform_name'] . "</h3></th>
                        <tbody>" . $answers . "</tbody>
                    </table>";
            } else {
                $webform_table = "<table>
                        <tbody>There is not webform for this record</tbody>
                    </table>";
            }
            $description .= "<div>" . $webform_table . "</div><br />";

            $appointment_ics = array();
            if ($last_appointment_ics) {
                $appointment_ics['appointment_id'] = $last_appointment_ics->appointment_id;
                $appointment_ics['start_date'] = $last_appointment_ics->start_date;
                $appointment_ics['send_to'] = $last_appointment_ics->send_to;
                $appointment_ics['send_from'] = $last_appointment_ics->send_from;
                $appointment_ics['duration'] = $last_appointment_ics->duration;
                $appointment_title = explode(' - ', $last_appointment_ics->title)[1];
                $appointment_ics['title'] = 'Appointment Cancellation - ' . $appointment->title . ' #' . $appointment_id;
                $appointment_ics['location'] = $last_appointment_ics->location;
                $appointment_ics['uid'] = $uid;
                $appointment_ics['description'] = ($description ? $description : $last_appointment_ics->description);
                $appointment_ics['sequence'] = $last_appointment_ics->sequence + 1;
                $appointment_ics['method'] = 'CANCEL';
            }

            //Create the ical file to attach
            $ical = $this->createIcalFile($appointment_ics['uid'], $appointment_ics['method'], $appointment_ics['send_from'], $appointment_ics['send_to'], $appointment_ics['start_date'], $appointment_ics['duration'], $appointment_ics['title'], $appointment_ics['description'], $appointment_ics['location'], $appointment_ics['sequence']);

            //ATTACH the ics file
            //Create tmp dir if it does not exist
            $tmp_path = FCPATH . '/upload/attachments/ics';
            $filename = $appointment_ics['uid'] . ".ics";

            if (@!file_exists($tmp_path)) {
                mkdir($tmp_path, 0777, true);
            }

            $handle = fopen($tmp_path . "/" . $filename, 'w+');
            if ($handle) {
                if (@!fwrite($handle, $ical)) {
                    return false;
                } else {
                    $attachments = array(
                        array(
                            "path" => $tmp_path . "/" . $filename,
                            "name" => $filename,
                            "disposition" => 'inline'
                        )
                    );
                }
            }
            fclose($handle);

            //SEND MAIL
            $form_to_send = array(
                "send_from" => "appointments@121system.com",
                "send_to" => $appointment_ics['send_to'],
                "cc" => null,
                "bcc" => 'estebanc@121customerinsight.co.uk',
                "subject" => $appointment_ics['title'],
                "body" => $appointment_ics['description'],
                "template_attachments" => $attachments
            );

            $email_sent = $this->send($form_to_send);

            //TODO save email_history or delete the attachment??
            //$email_history_id = $this->Email_model->add_new_email_history($form);

            //Save the appointment_ics sent
            if ($email_sent) {
                $appointment_ics_id = $this->Appointments_model->saveAppointmentIcs($appointment_ics);
                $appointment_ics['appointment_ics_id'] = $appointment_ics_id;
            }

            echo json_encode(array(
                "success" => ($email_sent),
                "appointment_ics" => $appointment_ics
            ));
        }
        else {
            echo json_encode(array(
                "success" => true,
                "msg" => "Nothing sent. There is no appointment ics sent before"
            ));
        }

    }

    private function createIcalFile($uid, $method, $from, $to, $meeting_date, $meeting_duration, $subject, $meeting_description, $meeting_location, $sequence) {
        //Convert MYSQL datetime and construct iCal start, end and issue dates
        $meetingstamp = STRTOTIME($meeting_date . " -1 hour UTC");
        //        $dtstart = GMDATE("Ymd\THis\Z", $meetingstamp);
        //        $dtend = GMDATE("Ymd\THis\Z", $meetingstamp + $meeting_duration);
        //        $todaystamp = GMDATE("Ymd\THis\Z");
        $dtstart = GMDATE("Ymd\THis\Z", $meetingstamp);
        $dtend = GMDATE("Ymd\THis\Z", $meetingstamp + $meeting_duration);
        $todaystamp = GMDATE("Ymd\THis\Z");

        //Create unique identifier
        $cal_uid = $uid;

        $status = ($method === "CANCEL" ? "\n"."STATUS: CANCELLED"."\r\n" : "\n");

        $attendees = '';
        foreach (explode(',', $to) as $attendee) {
            $attendee_add = trim($attendee);
            $attendee_name = trim(strstr($attendee, '@', true));
            $attendees .= 'ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;CN="' . $attendee_name . '":mailto:' . $attendee_add ."\r\n";
        }

        $attendees = rtrim($attendees);


        //Create ICAL Content (Google rfc 2445 for details and examples of usage)
        $ical = 'BEGIN:VCALENDAR'."\r".'
PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN'."\r".'
VERSION:2.0'."\r".'
METHOD:' . $method . ''."\r".'' . $status .
'BEGIN:VEVENT'."\r".'
ORGANIZER:MAILTO:'.$from.''."\r".'
' . $attendees . ''."\r".'
DTSTART:' . $dtstart . ''."\r".'
DTEND:' . $dtend . ''."\r".'
LOCATION:' . $meeting_location . ''."\r".'
TRANSP:OPAQUE'."\r".'
SEQUENCE:' . $sequence . ''."\r".'
UID:' . $cal_uid . ''."\r".'
DTSTAMP:' . $todaystamp . ''."\r".'
DESCRIPTION:' . $meeting_description . ''."\r".'
SUMMARY:' . $subject . ''."\r".'
PRIORITY:5'."\r".'
X-MICROSOFT-CDO-IMPORTANCE:1'."\r".'
CLASS:PUBLIC'."\r".'
BEGIN:VALARM'."\r".'
TRIGGER:-PT1440M'."\r".'
ACTION:DISPLAY'."\r".'
DESCRIPTION:Reminder'."\r".'
END:VALARM'."\r".'
END:VEVENT'."\r".'
END:VCALENDAR';

        return $ical;
    }


    public function send_appointment_confirmation() {
        user_auth_check();
        if ($this->input->is_ajax_request()) {
            $appointment_id = $this->input->post('appointment_id');
            $branch_id = $this->input->post('branch_id');
            $state = $this->input->post('state');
            $send_to = $this->input->post('send_to');

            //Get the appointment info
            $appointment = $this->Appointments_model->getAppointmentById($appointment_id);

            $start_date = $appointment->start;
            $end_date = $appointment->end;

            $address = (isset($_SESSION['cover_letter_address'])?$_SESSION['cover_letter_address']:$appointment->address);

            //Remove the cover_letter_address after send the email
            unset($_SESSION['cover_letter_address']);

            //Get contact info
            $contact = $this->Contacts_model->get_contact($appointment->contact_id);
            $fullname = (isset($contact['general']['fullname'])?$contact['general']['fullname']:'');
//            $fullname = explode(" ",$fullname);
//            $name = (isset($contact['general']['firstname'])?       $contact['general']['firstname']:$fullname[0]);
//            $surname = (isset($contact['general']['lastname'])?$contact['general']['lastname']:$fullname[1]);
//            $title = null;
//
//            foreach($fullname as $value) {
//                if (in_array($value,array("Mr", "Miss", "Mrs", "Ms", "Dr", "Sr"))) {
//                    $title = $value;
//                    $name = (isset($contact['general']['firstname'])?$contact['general']['firstname']:$fullname[1]);
//                    $surname = (isset($contact['general']['lastname'])?$contact['general']['lastname']:$fullname[2]);
//                }
//            }

            $today_date = new \DateTime('now');
            $today = $today_date->format("YmdHis");

            $path = 'upload/attachments/cover_letter';
            $filename = "HSL_Appointment_" . $today."_".$appointment->appointment_id;

            $created_by_user = $this->User_model->get_user_by_id($appointment->created_by);
            $reference = (isset($created_by_user[0]['custom'])?'Our Ref '.$created_by_user[0]['custom']:'');

            //Create the hsl cover_letter
            //$complete_path = $this->create_hsl_cover_letter($path, $filename, $reference, $start_date, $end_date, $title, $name, $surname, $address, $postcode);
            $complete_path = $this->create_hsl_cover_letter($path, $filename, $reference, $start_date, $end_date, $fullname, $address);

            $start_date = new \DateTime($start_date);
            $end_date = new \DateTime($end_date);
            $day = $start_date->format("jS F Y");
            $start_time = $start_date->format("G:ia");
            $end_time = $end_date->format("G:ia");

            $subject = 'Appointment confirmation';
            $body = 'Appointment confirmation';
            if ($state == 'inserted') {
                $subject = 'Appointment Booking';
                $body = "A new appointment have been booked for ".
                    $day." and the allocated time will be between ".$start_time." and ".$end_time.
                    ". The appointment have been booked for ".$contact['general']['fullname'].
                    " on the address: ".$appointment->address;
            }
            else if ($state == 'updated') {
                $subject = 'Appointment Update';
                $body = "An appointment have been rescheduled for ".
                    $day." and the allocated time will be between ".$start_time." and ".$end_time.
                    ". The appointment have been booked for ".$contact['general']['fullname'].
                    " on the address: ".$appointment->address;
            }

            //SEND MAIL
            $attachments = array($complete_path);
            $form_to_send = array(
                "send_from" => "noreply@121system.com",
                "send_to" => $send_to,
                "cc" => null,
                "bcc" => 'estebanc@121customerinsight.co.uk',
                "subject" => $subject." - ".$appointment->title,
                "body" => $body,
                "template_attachments" => $attachments
            );

            //Send email
            $email_sent = $this->send($form_to_send);

            //TODO save email_history or delete the attachment??
            //$email_history_id = $this->Email_model->add_new_email_history($form);

            echo json_encode(array(
                "success" => ($email_sent)
            ));

        }
    }

    private function create_hsl_cover_letter($path, $filename, $reference, $start_date, $end_date, $fullname, $address) {
        require_once(APPPATH . 'libraries/PhpWord/Autoloader.php');

        $start_date = new \DateTime($start_date);
        $end_date = new \DateTime($end_date);
        $day = $start_date->format("jS F Y");
        $start_time = $start_date->format("G:ia");
        $end_time = $end_date->format("G:ia");
        $today_date = new \DateTime('now');
        $today_day = $today_date->format("jS F Y");

        \PhpOffice\PhpWord\Autoloader::register();

        // Creating the new document...
        $phpWord = new \PhpOffice\PhpWord\PhpWord();


        //Add document information
        $properties = $phpWord->getDocInfo();
        $properties->setCreator('Hsl');
        $properties->setCompany('Hsl');
        $properties->setTitle($filename);
        //$properties->setDescription('My description');
        //$properties->setCategory('My category');
        $properties->setLastModifiedBy('Hsl');
        //$properties->setCreated(mktime(0, 0, 0, 3, 12, 2014));
        //$properties->setModified(mktime(0, 0, 0, 3, 14, 2014));
        //$properties->setSubject('My subject');
        //$properties->setKeywords('my, key, word');

        /* Note: any element you append to a document must reside inside of a Section. */

        // Adding an empty Section to the document...
        $section = $phpWord->addSection();

        //HEADER
        //$header = $section->addHeader();
        //$header->addText(htmlspecialchars($reference), array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true));
        //$header->addImage('assets/themes/hsl/hsl_stacked_logo.png', array('width'=>100, 'height'=>100, 'align'=>'right'));

        //FOOTER
        //$footer = $section->addFooter();
        //$footer->addImage('assets/themes/hsl/hsl_stacked_logo.png', array('width'=>30, 'height'=>30, 'align'=>'right'));

        //CONTACT DETAILS
        // Adding Contact details font
        $fontStyleName = 'ContactDetailsStyle';
        $phpWord->addFontStyle(
            $fontStyleName,
            array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
        );
        // Adding Text element to the Section having font styled by default...
        //$section->addText(htmlspecialchars((isset($title)?$title.' ':'').$name.' '.$surname), "ContactDetailsStyle");
		$section->addTextBreak(6);
		$section->addText(htmlspecialchars($reference), "ContactDetailsStyle");
        $section->addText(htmlspecialchars($fullname), "ContactDetailsStyle");
        $addresses = explode(',',$address);
        foreach($addresses as  $address) {
            if (strlen(trim($address)) > 0) {
                $section->addText(htmlspecialchars(trim($address)), "ContactDetailsStyle", array("spaceAfter" => 0.3));
            }
        }
        $section->addTextBreak(1, "ContactDetailsStyle");
        $section->addText(htmlspecialchars($today_day), "ContactDetailsStyle");
        $section->addTextBreak(1, "ContactDetailsStyle");
        $section->addText(htmlspecialchars('Dear '.$fullname), "ContactDetailsStyle");
        $section->addTextBreak(0, "ContactDetailsStyle");

        //CONTENT

        // Adding Text element with font customized inline...
        $section->addText(
            htmlspecialchars('Thank you for your interest in HSL’s Home Consultation Service. We hope that you enjoy looking through your comfort catalogue and fabric card, offering a huge choice of chairs to make sitting, rising and relaxing easier in a wide choice of fabrics and leathers.'),
            array('name' => 'Tahoma', 'size' => 10)
        );
        $section->addTextBreak(0);

        $section->addText(
            htmlspecialchars('You won’t believe how comfortable our chairs are until you try them and our Home Consultation Service enables you to do this in the comfort of your own home. Our Home Consultants have expert knowledge and advice on all of our ranges and will be happy to help you choose the perfect chair to suit your individual needs.'),
            array('name' => 'Tahoma', 'size' => 10)
        );
        $section->addTextBreak(0);

        $section->addText(
            htmlspecialchars(
                'I confirm your appointment has been arranged for '.
                $day.' and the allocated time will be between '.$start_time.' and '.$end_time.'.'.
                ' As discussed our Home Consultant will contact you the day before your appointment to introduce themselves and to answer any questions you may have.'
            ),
            array('name' => 'Tahoma', 'size' => 10));
        $section->addTextBreak(0);

        $section->addText(htmlspecialchars('In the meantime should you have any queries please do not hesitate to contact the Home Consultation Team on '),array('name' => 'Tahoma', 'size' => 10));
        $section->addText(htmlspecialchars('01924 486900.'),array('name' => 'Tahoma', 'size' => 10, 'bold' => true));
        $section->addTextBreak(1);

        $section->addText(htmlspecialchars('Yours sincerely,'),array('name' => 'Tahoma', 'size' => 10));
        $section->addTextBreak(4);

        $section->addText(htmlspecialchars('Home Consultation Service Team'),array('name' => 'Tahoma', 'size' => 10));

        // Saving the document as docx file...
        $filename = $filename.".docx";

        if (@!file_exists($path)) {
            mkdir($path, 0777, true);
        }


        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($path."/".$filename);

        $complete_path = array(
            'path' => $path."/".$filename,
            'name' => $filename
        );

        return $complete_path;
    }
}