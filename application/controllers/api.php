<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Api extends REST_Controller
{

    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  USERS  ***********************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    function user_get()
    {
        $this->load->model('User_model');
        $user = $this->User_model->get_user_by_id( $this->get('id') );

        if($user)
        {
            $this->response($user, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }

    function users_get()
    {
        $this->load->model('User_model');
        $users = $this->User_model->get_users($this->get('limit') );

        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }

    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  RECORDS  *********************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    function record_get()
    {
        $this->load->model('Records_model');
        $this->load->model('Contacts_model');
        $this->load->model('Company_model');

        $record = $this->Records_model->get_records_by_urn( $this->get('urn'), array("2"));
        $record_details = $this->Records_model->get_record_details_by_urn( $this->get('urn'));
        $record_contacts = $this->Contacts_model->get_contacts( $this->get('urn'));
        $record_companies = $this->Company_model->get_companies( $this->get('urn'));

        $record['contacts'] = $record_contacts;
        $record['companies'] = $record_companies;
        $record['record_details'] = $record_details;

        if($record)
        {
            $this->response($record, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }

    function record_post()
    {
        $this->load->model('Records_model');

        $record = $this->post();

        //Insert record
        if (!isset($record['urn']) || $record['urn'] == '') {
            $record_urn = $this->Records_model->save_record($record);

            $message = array(
                'success' => ($record_urn?true:false),
                'urn' => ($record_urn?$record_urn:''),
				 'id' => ($record_urn?$record_urn:''),
                'message' => ($record_urn?'INSERTED!':'ERROR: The records was not inserted successfully!')
            );
        }
        //Update record
        else {
            //$result = $this->Records_model->update_record($record);

            $message = array(
                'success' => false,
                'urn' => $this->get('urn'),
                'message' => 'The update is not available yet!'
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

//    function record_delete()
//    {
//        //$this->some_model->deletesomething( $this->get('id') );
//        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');
//
//        $this->response($message, 200); // 200 being the HTTP response code
//    }

    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  RECORD DETAILS  **************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    function record_details_get()
    {
        $this->load->model('Records_model');

        $record_details = $this->Records_model->get_record_details_by_urn( $this->get('urn'));


        if($record_details)
        {
            $this->response($record_details, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }

    function record_details_post()
    {
        $this->load->model('Records_model');

        $record_details = $this->post();

        if (!isset($record_details['urn']) || $record_details['urn'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'detail_id' => '',
                'message' => "ERROR: The urn is undefined or it doesn't exists!"
            );

        }
        else {

            $record_details_id = $this->Records_model->save_additional_info($record_details);

            $message = array(
                'success' => ($record_details_id?true:false),
                'urn' => $record_details['urn'],
                'detail_id' => ($record_details_id?$record_details_id:''),
			    'id' => ($record_details_id?$record_details_id:''),
                'message' => ($record_details_id?'SAVED!':'ERROR: The record details were saved successfully!')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  RECORD CONTACT ***************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    function record_contact_get()
    {
        $this->load->model('Contacts_model');

        $record_contact = $this->Contacts_model->get_contact( $this->get('id'));


        if($record_contact)
        {
            $this->response($record_contact, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }


    function record_contact_post()
    {
        $this->load->model('Contacts_model');

        $record_contact = $this->post();

        if (!isset($record_contact['urn']) || $record_contact['urn'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'contact_id' => '',
                'message' => "ERROR: The urn is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert record company
            if (!isset($record_contact['contact_id']) || $record_contact['contact_id'] == '') {
                $record_contact_id = $this->Contacts_model->save_contact($record_contact);

                $message = array(
                    'success' => ($record_contact_id?true:false),
                    'urn' => $record_contact['urn'],
                    'contact_id' => ($record_contact_id?$record_contact_id:''),
					'id' => ($record_contact_id?$record_contact_id:''),
                    'message' => ($record_contact_id?'INSERTED!':'ERROR: The record contact was NOT inserted successfully!')
                );
            }
            //Update record contact
            else {
                $result = $this->Contacts_model->update_contact($record_contact);

                $message = array(
                    'success' => ($result?true:false),
                    'urn' => $record_contact['urn'],
                    'contact_id' => $record_contact['contact_id'],
					'id' => $record_contact['contact_id'],
                    'message' => ($result?'UPDATED!':'ERROR: The record contact was NOT updated successfully!')
                );
            }
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    //Insert/Update contact address
    function contact_address_post()
    {
        $this->load->model('Contacts_model');

        $contact_address = $this->post();

        if (!isset($contact_address['contact_id']) || $contact_address['contact_id'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'address_id' => '',
                'message' => "ERROR: The contact_id is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert/Update contact address
            $contact_address_id = $this->Contacts_model->save_contact_address($contact_address);

            $message = array(
                'success' => ($contact_address_id?true:false),
                'contact_id' => $contact_address['contact_id'],
                'address_id' => ($contact_address_id?$contact_address_id:''),
				 'id' => ($contact_address_id?$contact_address_id:''),
                'message' => ($contact_address_id?'INSERTED!':'ERROR: The contact address was NOT inserted successfully!')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    //Insert/Update contact telephone
    function contact_telephone_post()
    {
        $this->load->model('Contacts_model');

        $contact_telephone = $this->post();

        if (!isset($contact_telephone['contact_id']) || $contact_telephone['contact_id'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'telephone_id' => '',
                'message' => "ERROR: The contact_id is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert/Update contact address
            $contact_telephone_id = $this->Contacts_model->save_contact_telephone($contact_telephone);

            $message = array(
                'success' => ($contact_telephone_id?true:false),
                'contact_id' => $contact_telephone['contact_id'],
                'telephone_id' => ($contact_telephone_id?$contact_telephone_id:''),
                'message' => ($contact_telephone_id?'INSERTED!':'ERROR: The contact telephone was NOT inserted successfully!')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  RECORD COMPANY  **************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    /**
     * Get company details by id
     */
    function record_company_get()
    {
        $this->load->model('Company_model');

        $record_company = $this->Company_model->get_company( $this->get('id'));


        if($record_company)
        {
            $this->response($record_company, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }

    //Insert/Update company record
    function record_company_post()
    {
        $this->load->model('Company_model');

        $record_company = $this->post();

        if (!isset($record_company['urn']) || $record_company['urn'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'company_id' => '',
                'message' => "ERROR: The urn is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert record company
            if (!isset($record_company['company_id']) || $record_company['company_id'] == '') {
                $record_company_id = $this->Company_model->save_company($record_company);

                $message = array(
                    'success' => ($record_company_id?true:false),
                    'urn' => $record_company['urn'],
                    'company_id' => ($record_company_id?$record_company_id:''),
					 'id' => ($record_company_id?$record_company_id:''),
                    'message' => ($record_company_id?'INSERTED!':'ERROR: The record comapny was inserted successfully!')
                );
            }
            //Update record contact
            else {
                $result = $this->Company_model->update_company($record_company);

                $message = array(
                    'success' => ($result?true:false),
                    'urn' => $record_company['urn'],
                    'company_id' => $record_company['company_id'],
					'id' => $record_company['company_id'],
                    'message' => ($result?'UPDATED!':'ERROR: The record comapny was updated successfully!')
                );
            }
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    //Insert/Update company address
    function company_address_post()
    {
        $this->load->model('Company_model');

        $company_address = $this->post();

        if (!isset($company_address['company_id']) || $company_address['company_id'] == '') {
            $message = array(
                'success' => false,
                'company_id' => '',
                'address_id' => '',
                'message' => "ERROR: The company_id is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert/Update company address
            $company_address_id = $this->Company_model->save_company_address($company_address);

            $message = array(
                'success' => ($company_address_id?true:false),
                'company_id' => $company_address['company_id'],
                'address_id' => ($company_address_id?$company_address_id:''),
				'id' => ($company_address_id?$company_address_id:''),
                'message' => ($company_address_id?'SAVED!':'ERROR: The comapny address was saved successfully!')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    //Insert/Update company telephone
    function company_telephone_post()
    {
        $this->load->model('Company_model');

        $company_telephone = $this->post();

        if (!isset($company_telephone['company_id']) || $company_telephone['company_id'] == '') {
            $message = array(
                'success' => false,
                'company_id' => '',
                'telephone_id' => '',
                'message' => "ERROR: The company_id is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert/Update company telephone
            $company_telephone_id = $this->Company_model->save_company_telephone($company_telephone);

            $message = array(
                'success' => ($company_telephone_id?true:false),
                'company_id' => $company_telephone['company_id'],
                'telephone_id' => ($company_telephone_id?$company_telephone_id:''),
				'id' => ($company_telephone_id?$company_telephone_id:''),
                'message' => ($company_telephone_id?'SAVED!':'ERROR: The comapny telephone was saved successfully!')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }


    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  EMAILS  **********************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    function email_get()
    {
        $this->load->model('Email_model');

        $email = $this->Email_model->get_email_by_id( $this->get('id'));

        if($email)
        {
            $this->response($email, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find an email with this id!'), 404);
        }
    }

    function emails_get()
    {
        $this->load->model('Email_model');
        $emails = $this->Email_model->get_emails_by_urn($this->get('urn'),$this->get('limit'),$this->get('offset'));

        if($emails)
        {
            $this->response($emails, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any email!'), 404);
        }
    }

	 function recieve_email_post(){
		    $this->load->model('Email_model');
		$email_history = $this->input->post();	
		 $this->Email_model->add_new_email_history($email_history);
		 
	 }

    function send_email_post()
    {
        $this->load->model('Email_model');
        $this->load->model('Records_model');

        $message = array(
            'success' => false,
            "message" => "ERROR: Email not sent successfuly"
        );

        $urn = intval($this->input->post('urn'));
        $template_id = intval($this->input->post('template_id'));
        $recipients_to = $this->input->post('recipients_to');
        $recipients_to_name = $this->input->post('recipients_to_name');
        $recipients_cc = $this->input->post('recipients_cc');
        $recipients_bcc = $this->input->post('recipients_bcc');

        if ($template_id && $urn) {
            //create the form structure to pass to the send function
            $template = $this->Email_model->get_template($template_id);
            if ($template) {
                $form = array();
                $form['template_id'] = $template['template_id'];
                $form['subject'] = $template['template_subject'];
                $form['body'] = $template['template_body'];
                $form['send_from'] = $template['template_from'];
                $form['template_unsubscribe'] = $template['template_unsubscribe'];
                $form['send_to'] = $template['template_to'].(strlen($recipients_to)>0?",":"").$recipients_to;
                $form['cc'] = $template['template_cc'].(strlen($recipients_cc)>0?",":"").$recipients_cc;
                $form['bcc'] = $template['template_bcc'].(strlen($recipients_bcc)>0?",":"").$recipients_bcc;
                $form['urn'] = $urn;



                $last_comment = $this->Records_model->get_last_comment($urn);
                $placeholder_data = $this->Email_model->get_placeholder_data($urn);
                $placeholder_data[0]['comments'] = $last_comment;
                $placeholder_data['recipient_name'] = $recipients_to_name;
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
                if ($form['send_to'] != '' && $form['cc'] != '' && $form['bcc'] != '') {
                    if ($this->send($form)) {
                        $email_history = array(
                            'body' => $form['body'],
                            'subject' => $form['subject'],
                            'send_from' => $form['send_from'],
                            'send_to' => $form['send_to'],
                            'cc' => $form['cc'],
                            'bcc' => $form['bcc'],
                            'user_id' => $_SESSION['user_id'],
                            'urn' => $form['urn'],
                            'template_id' => $form['template_id'],
                            'template_unsubscribe' => $form['template_unsubscribe'],
                            'status' => 1,
                            'pending' => 0,
                            'visible' => $template['history_visible']
                        );
                        $email_id = $this->Email_model->add_new_email_history($email_history);

                        $message = array(
                            'success' => true,
                            "urn" => $form['urn'],
                            "email_history_id" => $email_id,
                            'message' => 'Email sent successfully!'
                        );
                    }
                    else {
                        $message = array(
                            'success' => false,
                            "message" => "ERROR: Email not sent successfuly. Error during the sent process"
                        );
                    }
                }
                else {
                    $message = array(
                        'success' => false,
                        "message" => "ERROR: Email not sent successfuly. The send_to field is empty"
                    );
                }

            }
            else {
                $message = array(
                    'success' => false,
                    "message" => "ERROR: Email not sent successfuly. The template doesn't exist"
                );
            }

        }
        else {
            $message = array(
                'success' => false,
                "message" => "ERROR: Email not sent successfuly. URN, template not found"
            );
        }

        //RESPONSE
        if ($message['success']) {
            $this->response($message, 200); // 200 being the HTTP response code
        }
        else {
            $this->response($message, 404); // 404 being the HTTP response code
        }
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

        //If the environment is different than production, send the email to the user (if exists)
        if ((ENVIRONMENT !== "production")) {
            if (isset($_SESSION['email']) && $_SESSION['email'] != '') {
                $form['send_to'] = $_SESSION['email'];
                $form['cc'] = "";
                $form['bcc'] = "";
                $this->email->to($_SESSION['email']);
                $this->email->cc("");
                $this->email->bcc("");
            } else {
                $form['send_to'] = "";
                $form['cc'] = "";
                $form['bcc'] = "";
                $this->email->to("");
                $this->email->cc("");
                $this->email->bcc("");
                $this->email->clear(TRUE);
                return true;
            }
        }
        $result = $this->email->send();
        //print_r($this->email->print_debugger());
        //$this->firephp->log($this->email->print_debugger([$include = array('headers', 'subject', 'body')]));

        //Write on log
        log_message('info', '[EMAIL] Email sent from '.$form['send_from'].' to '.$form['send_to'].'. Title: '.$form['subject']);

        $this->email->clear(TRUE);

        //Remove tmp dir
        if (file_exists($tmp_path)) {
            $this->removeDirectory($tmp_path);
        }

        return $result;
    }
	
	/*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  CUSTOM/DYNAMIC PANELS **************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    function custom_panel_get()
    {
        $this->load->model('Records_model');

        $custom_panel = $this->Records_model->get_custom_panel_data($this->get('urn'),$this->get('panel_id'));


        if($custom_panel)
        {
            $this->response($custom_panel, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }

    function custom_panel_post()
    {
        $this->load->model('Records_model');

        $custom_panel = $this->post();

        if (!isset($custom_panel['urn']) || $custom_panel['urn'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'detail_id' => '',
                'message' => "ERROR: The urn is undefined or it doesn't exist!"
            );

        }
        else {

              $now = date('Y-m-d H:i:s');
        $id = $custom_panel['data_id'];
        $urn =  $custom_panel['urn'];

        if (!empty($id)) {
            //update existing data set
            $data = array("updated_on" => $now);
            $this->db->where(array("data_id" => $id));
            $this->db->update("custom_panel_data", $data);
        }

        //add in the values
        foreach ( $custom_panel as $field => $val) {
            if ($field <> "urn" && $field <> "data_id") {
                if (is_array($val)) {
                    $val = implode(",", $val);
                }
                $values[] = array("data_id" => $id, "field_id" => $field, "value" => $val);
            }
        }

        $this->db->insert_update_batch("custom_panel_values", $values);

            $message = array(
                'success' => ($id?true:false),
                'urn' => $urn,
                'custom_id' => ($id?$id:''),
			    'id' => ($id?$id:''),
                'message' => ($id?'SAVED!':'ERROR: There was a problem adding the custom data')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }


}