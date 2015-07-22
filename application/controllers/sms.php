<?php

require_once(APPPATH . 'libraries/textlocal.class.php');

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
        $this->load->model('Form_model');

        $this->textlocal = new textlocal("jon-man@121customerinsight.co.uk","95288bf9c5c1c5339426fc9178bebd025e850c76");
    }

    //this function returns a json array of sms data for a given record id
    public function get_sms_by_urn()
    {
        user_auth_check();
        if ($this->input->is_ajax_request()) {
            $urn = intval($this->input->post('urn'));
            $limit = (intval($this->input->post('limit'))) ? intval($this->input->post('limit')) : NULL;

            $sms = $this->Sms_model->get_sms_by_urn($urn, $limit, 0);

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
            $sms = $this->Sms_model->get_sms_by_filter($form);

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

        $urn = intval($this->uri->segment(3));

        $template_id = intval($this->uri->segment(4));

        $template = null;
        if ($template_id) {
            $template = $this->Sms_model->get_template($template_id);
        }

        $last_comment = $this->Records_model->get_last_comment($urn);

        $sms_senders = $this->Form_model->get_sms_senders();

        $contact_numbers = $this->Contacts_model->get_mobile_numbers($urn);
        $aux = array();
        foreach($contact_numbers as $number) {
            array_push($aux, format_mobile($number));
        }
        $contact_numbers = array_unique($aux);


        if ($template) {
            $placeholder_data = $this->Sms_model->get_placeholder_data($urn);
            $placeholder_data[0]['comments'] = $last_comment;
            if (count($placeholder_data)) {
                foreach ($placeholder_data[0] as $key => $val) {
                    if ($key == "fullname") {
                        $val = str_replace("Mr ", "", $val);
                        $val = str_replace("Mrs ", "", $val);
                        $val = str_replace("Miss ", "", $val);
                        $val = str_replace("Ms ", "", $val);
                    }
                    if (strpos($template['template_text'], "[$key]") !== false) {
                        $template['template_text'] = str_replace("[$key]", $val, $template['template_text']);
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
            'sms_senders' => $sms_senders,
            'contact_numbers' => $contact_numbers,
            'css' => array(
                'dashboard.css',
                'plugins/fontAwesome/css/font-awesome.css',
                'plugins/jqfileupload/jquery.fileupload.css',
            ),
            'javascript' => array(
                'sms.js',
                'plugins/jqfileupload/vendor/jquery.ui.widget.js',
                'plugins/jqfileupload/jquery.iframe-transport.js',
                'plugins/jqfileupload/jquery.fileupload.js',
                'plugins/jqfileupload/jquery.fileupload-process.js',
                'plugins/jqfileupload/jquery.fileupload-validate.js'
            ),
        );

        $this->template->load('default', 'sms/new_sms.php', $data);

    }

    public function unsubscribe()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $data['ip_address'] = $_SERVER['REMOTE_ADDR'];
            //check the sms address

            if (preg_match('/^[0-9().-]+$/', $data['sms_address'])) {
                echo json_encode(array("success" => false, "msg" => "That is not a valid sms address"));
                exit;
            }

            if ($this->Sms_model->unsubscribe($data)) {
                echo json_encode(array("success" => true, "msg" => "Your sms address has been removed from the mailing list"));
            } else {
                echo json_encode(array("success" => false, "msg" => "There was a problem removing your sms. Please make sure you are using the unbsubscribe link in the sms you recieved"));
            }
        } else {
            $template_id = base64_decode($this->uri->segment(3));
            $urn = base64_decode($this->uri->segment(4));

            $client_id = $this->Records_model->get_client_from_urn($urn);
            $check = $this->Sms_model->check_sms_history($template_id, $urn);
            if ($check) {
                $data = array(
                    'urn' => $urn,
                    'title' => 'Unsubscribe',
                    'client_id' => $client_id,
                    'urn' => $urn);
                $this->template->load('default', 'sms/unsubscribe.php', $data);
            } else {
                $data = array(
                    'msg' => "The company you tried to unsubscribe from does not exist on our system",
                    'title' => 'Invalid sms');
                $this->template->load('default', 'errors/display.php', $data);
            }
        }
    }

    //Send an sms
    public function send_sms()
    {
        user_auth_check();
        $form = $this->input->post();

        $sender = ($form['template_sender_id']?$form['template_sender']:null);
        $numbers = $form['sent_to'];
        $message = $form['template_text'];

        //Check if the sender selected exist
        if ($this->check_sender_name($sender)) {
            //Check if the message is empty
            if (strlen(trim($message)) <= 0) {
                echo json_encode(array(
                    "data" => array("status" => "error", "msg" => "ERROR: The message is empty")
                ));
            }
            else {
                //Send the sms
                $response = $this->send($numbers, $message, $sender);

                if ($response) {
                    //Save the sms in the history table
                    $sms_ar = array();
                    $user_id = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;
                    $status_id = (($response->status === "success")&&($response->test_mode !== TRUE)?SMS_STATUS_SENT:SMS_STATUS_ERROR);
                    foreach ($numbers as $number) {
                        array_push($sms_ar, array(
                            "text" => $message,
                            "send_to" => $number,
                            "sender_id" => $form['template_sender_id'],
                            "user_id" => $user_id,
                            "urn" => $form['urn'],
                            "template_id" => $form['template_id'],
                            "status_id" => $status_id
                        ));
                    }

                    $this->Sms_model->add_sms_histories($sms_ar);
                }

                echo json_encode(array(
                    "data" => $response
                ));
            }
        }
        else {
            echo json_encode(array(
                "data" => array("status" => "error", "msg" => "ERROR: The sender selected does not exist")
            ));
        }
    }

    /**
     *
     * Send pending smss
     *
     *
     */
    public function send_pending_sms()
    {

        $output = "";
        $output .= "Sending pending sms... \n\n";
        $limit = 50;
        if (intval($this->uri->segment(3)) > 0) {
            $limit = $this->uri->segment(3);
        }

        //Get the oldest 50 mails pending to be sent
        $pending_sms = $this->Sms_model->get_pending_sms($limit);
        if (!empty($pending_sms)) {
            //Check if we have enough credits
            $status = 2;
            $currentCredits = $this->textlocal->getBalance();
            if ($currentCredits['sms'] < count($pending_sms)) {
                $status = 1;
            }

            //Build the messages array to send
            $messages = array();
            $sms_histories = array();
            $numbers = array();
            foreach ($pending_sms as $sms) {

                array_push($messages, array(
                    'sms_number' => $sms['send_to'],
                    'send_from' => $sms['sender_id'],
                    'sms_text' => $sms['text'],
                    'id' => $sms['sms_id'],
                ));

                array_push($sms_histories, array(
                    "sms_id" => $sms['sms_id'],
                    "sent_date" => $sms['sent_date'],
                    "status_id" => $status
                ));

                array_push($numbers,$sms['send_to']);
            }
            if (!empty($messages)) {
                if ($status!=SMS_STATUS_PENDING) {
                    $response = $this->sendBulkSms($messages);
                    //Save the sms_history
                    if ($response == "OK") {
                        $this->Sms_model->update_sms_histories($sms_histories);

                        $output .= "SMS sent to " . implode(',',$numbers) . "\n\n";
                    } else {
                        $output .= "SMS not sent from the sms server \n\n";
                    }
                }
                else {
                    //No enough credits
                    $response = "There is not enough credits: ".$currentCredits['sms']."\n\n";
                }
            }

            $output .= $response."\n\n";

        } else {
            $output .= "No pending sms to be sent. \n\n";
        }

        echo $output;
    }

    /**
     *
     * Send appointment remind sms
     *
     *
     */
    public function send_appointment_remind_sms()
    {
        $output = "";
        $output .= "Sending appointment remind sms...";

        $template_id = null;
        $test = null;

        if (intval($this->uri->segment(3)) > 0) {
            $template_id = $this->uri->segment(3);
        }

        if (intval($this->uri->segment(4)) >= 0) {
            $test = $this->uri->segment(4);
        }

        if ($template_id) {
            //Get the appointments and the contact number where the appointment date is tomorrow or the day after tomorrow
            $remind_appointments = $this->Sms_model->get_remind_appointments($template_id);

            //Check if we have enough credits
            $status = 2;
            $currentCredits = $this->textlocal->getBalance();
            if ($currentCredits['sms'] < count($remind_appointments)) {
                $status = 1;
                $msg = "There is not enough credits: ".$currentCredits['sms']."\n\n";
            }
            //Check if more than 50 sms will be sent
            else if(count($remind_appointments) > 50) {
                $status = 1;
                $msg = "You are trying to send more than 50 text messages. The sms will not be sent and will be stored in the sms_history as pending. Please review the process and run the send_pending_sms process manually.\n\n";
            }

            if (!empty($remind_appointments)) {
                //Build the messages array to send
                $messages = array();
                $sms_histories = array();
                $numbers = array();
                foreach($remind_appointments as $remind_appointment) {

                    //Check the variables inside [] in the template and change them with a value
                    preg_match_all('#\[(.*?)\]#', $remind_appointment['sms_text'], $matches);
                    foreach($matches[0] as $key => $match) {
                        $remind_appointment['sms_text'] = str_replace($match,$remind_appointment[$matches[1][$key]],$remind_appointment['sms_text']);
                    }

                    array_push($messages, array(
                        'sms_number' => format_mobile($remind_appointment['sms_number']),
                        'send_from' => $remind_appointment['sms_from'],
                        'sms_text' => $remind_appointment['sms_text'],
                        'id' => $remind_appointment['appointment_id'],
                    ));

                    array_push($sms_histories, array(
                        "text" => $remind_appointment['sms_text'],
                        "sender_id" => $remind_appointment['sender_id'],
                        "send_to" => format_mobile($remind_appointment['sms_number']),
                        "user_id" => null,
                        "urn" => $remind_appointment['urn'],
                        "template_id" => $remind_appointment['template_id'],
                        "status_id" => $status,
                        "template_unsubscribe" => 0,
                        "text_local_id" => null
                    ));

                    array_push($numbers,format_mobile($remind_appointment['sms_number']));
                }

                if (!empty($messages)) {

                    if ($status!=SMS_STATUS_PENDING) {
                        $response = $this->sendBulkSms($messages, $test);
                        //Save the sms_history
                        if ($response == "OK") {
                            $this->Sms_model->add_sms_histories($sms_histories);

                            $output .= "SMS sent to " . implode(',',$numbers) . "\n\n";
                        } else {
                            $output .= "SMS not sent from the sms server \n\n";
                        }
                    }
                    else {
                        //Save the sms_history as pending
                        $this->Sms_model->add_sms_histories($sms_histories);
                        $response = $msg;
                        echo $output.$response;
                        throw new Exception($response);
                    }
                }

                $output .= $response."\n\n";

            } else {
                $output .= "No remind appointments sms to be send. \n\n";
            }
        }
        else {
            $output .= "No template id defined. \n\n";
        }

        $current_balance = $this->textlocal->getBalance();
        $output .= "Credits available: ".$current_balance['sms'];

        echo $output;
    }

    /**
     *
     * Send sms for records (bulk sms)
     *
     *
     */
    public function send_sms_records_by_source_id_and_template()
    {
        $output = "";
        $output .= "Sending records sms...";

        $template_id = null;
        $source_id = null;
        $test = null;

        if (intval($this->uri->segment(3)) > 0) {
            $template_id = $this->uri->segment(3);
        }
        if (intval($this->uri->segment(4)) > 0) {
            $source_id = $this->uri->segment(4);
        }

        if (intval($this->uri->segment(5)) > 0) {
            $test = $this->uri->segment(5);
        }

        if ($template_id && $source_id) {
            //Get the records and the contact number where the records status is 1 for a particular template and source_id
            $records = $this->Sms_model->get_records_by_source_id_and_template($template_id, $source_id);

            //Check if we have enough credits
            $status = 2;
            $currentCredits = $this->textlocal->getBalance();
            if ($currentCredits['sms'] < count($records)) {
                $status = 1;
                $msg = "There is not enough credits: ".$currentCredits['sms']."\n\n";
            }

            if (!empty($records)) {
                //Build the messages array to send
                $messages = array();
                $sms_histories = array();
                $numbers = array();
                foreach($records as $record) {

                    //Check the variables inside [] in the template and change them with a value
                    preg_match_all('#\[(.*?)\]#', $record['sms_text'], $matches);
                    foreach($matches[0] as $key => $match) {
                        if (isset($record[$matches[1][$key]])) {
                            $record['sms_text'] = str_replace($match,$record[$matches[1][$key]],$record['sms_text']);
                        }
                    }

                    array_push($messages, array(
                        'sms_number' => format_mobile($record['sms_number']),
                        'send_from' => $record['sms_from'],
                        'sms_text' => $record['sms_text'],
                        'id' => $record['urn'],
                    ));

                    array_push($sms_histories, array(
                        "text" => $record['sms_text'],
                        "sender_id" => $record['sender_id'],
                        "send_to" => format_mobile($record['sms_number']),
                        "user_id" => null,
                        "urn" => $record['urn'],
                        "template_id" => $record['template_id'],
                        "status_id" => $status,
                        "template_unsubscribe" => 0,
                        "text_local_id" => null
                    ));

                    array_push($numbers,format_mobile($record['sms_number']));
                }

                if (!empty($messages)) {

                    if ($status!=SMS_STATUS_PENDING) {
                        $response = $this->sendBulkSms($messages, $test);
                        //Save the sms_history
                        if ($response == "OK") {
                            $this->Sms_model->add_sms_histories($sms_histories);
                            $output .= "SMS sent to " . count($numbers) . " numbers \n\n";
                        } else {
                            $output .= "SMS not sent from the sms server \n\n";
                        }
                    }
                    else {
                        //Save the sms_history as pending
                        $this->Sms_model->add_sms_histories($sms_histories);
                        $response = $msg;
                        echo $output.$response;
                        throw new Exception($response);
                    }
                }

                $output .= $response."\n\n";

            } else {
                $output .= "No remind appointments sms to be send. \n\n";
            }
        }
        else if (!$template_id) {
            $output .= "No template id defined. \n\n";
        }
        else if (!$source_id) {
            $output .= "No source id defined. \n\n";
        }

        $current_balance = $this->textlocal->getBalance();
        $output .= "Credits available: ".$current_balance['sms'];

        echo $output;
    }

    //SMS Delivery Receipt
    public function sms_delivery_receipt()
    {

    }

    //Check if the sender name exist
    private function check_sender_name($sender) {
        //If the sender is null or does not exist on the list, get the default one
        $sender_names = $this->textlocal->getSenderNames();
        if (!$sender || !in_array($sender,$sender_names->sender_names)) {
            return false;
        }
        else {
            return true;
        }
    }

    //Get the default sender name
    private function get_default_sender_name() {

        return $this->textlocal->getSenderNames()->default_sender_name;
    }

    private function send($numbers,$message, $sender = null)
    {
        $test = (ENVIRONMENT !== "production");

        if (!$sender) {
            $sender = $this->get_default_sender_name();
        }

        $response = $this->textlocal->sendSms($numbers, $message, $sender, null, $test);

        return $response;
    }

    /**
     * @param $messages
     * @param $test false to send texts other case for testing
     *
     */
    private function sendBulkSms($messages, $test)
    {
        $test = ($test == 'false'?0:1);

        //Build the xml data
        $xmlData = '
        <SMS>
            <Account Name="jon-man@121customerinsight.co.uk" Password="x983kkdi" Test="'.$test.'" Info="0" JSON="0">
                <Sender From="'.$messages[0]['send_from'].'" rcpurl="https://121system.com/sms/sms_delivery_receipt" ID="Appointment">
                    <Messages>';

        foreach($messages as $message) {
            $xmlData .= '
                <Msg ID="'.$message['id'].'" Number="'.$message['sms_number'].'">
                    <Text>'.$message['sms_text'].'</Text>
                </Msg>';
        }

        $xmlData .= '
                        </Messages>
                    </Sender>
                </Account>
            </SMS>';

        $post = 'data='. urlencode($xmlData);
        $url = "http://www.txtlocal.com/xmlapi.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS ,$post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1);
        $data = curl_exec($ch);
        curl_close($ch);
        return str_replace("<br />","",$data);

    }

    //Delete sms from the history
    public function delete_sms()
    {
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