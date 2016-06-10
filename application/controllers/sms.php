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
        $this->load->model('Email_model');
        $this->load->model('Form_model');

        $this->textlocal = new textlocal("jon-man@121customerinsight.co.uk", "95288bf9c5c1c5339426fc9178bebd025e850c76");
    }


    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** GETTERS ********************************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/

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
            $sms['credits'] = $this->get_credits($sms['text']);

            echo json_encode(array(
                "success" => true,
                "data" => $sms
            ));
        }
    }

    //Get the credits used to send an sms for a particular text
    private function get_credits($text)
    {
        $count = strlen($text);

        switch (TRUE) {
            case ($count <= 160):
                return 1;
            case ($count > 160 && $count <= 306):
                return 2;
            case ($count > 306 && $count <= 459):
                return 3;
            case ($count > 459 && $count <= 612):
                return 4;
            case ($count > 612 && $count <= 766):
                return 5;
            case ($count > 766):
                return "Message was truncated to 765 characters (5 credits).";
        }
    }


    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /******CREATE A NEW SMS ************************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
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
        foreach ($contact_numbers as $number) {
            array_push($aux, format_mobile($number));
        }
        $contact_numbers = array_unique($aux);


        if ($template) {
            $placeholder_data = $this->Email_model->get_placeholder_data($urn);
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
                    if ($template['name'] == "Automatic") {
                        if ($template['custom_sender'] == "$key" || $template['custom_sender'] == "[$key]") {
                            $template['name'] = $val;
                            $this->firephp->log($val);
                        }
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


    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** UNSUBSCRIBE ****************************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
//    public function unsubscribe()
//    {
//        if ($this->input->is_ajax_request()) {
//            $data = $this->input->post();
//            $data['ip_address'] = $_SERVER['REMOTE_ADDR'];
//            //check the sms address
//
//            if (preg_match('/^[0-9().-]+$/', $data['sms_address'])) {
//                echo json_encode(array("success" => false, "msg" => "That is not a valid sms address"));
//                exit;
//            }
//
//            if ($this->Sms_model->unsubscribe($data)) {
//                echo json_encode(array("success" => true, "msg" => "Your sms address has been removed from the mailing list"));
//            } else {
//                echo json_encode(array("success" => false, "msg" => "There was a problem removing your sms. Please make sure you are using the unbsubscribe link in the sms you recieved"));
//            }
//        } else {
//            $template_id = base64_decode($this->uri->segment(3));
//            $urn = base64_decode($this->uri->segment(4));
//
//            $client_id = $this->Records_model->get_client_from_urn($urn);
//            $check = $this->Sms_model->check_sms_history($template_id, $urn);
//            if ($check) {
//                $data = array(
//                    'urn' => $urn,
//                    'title' => 'Unsubscribe',
//                    'client_id' => $client_id,
//                    'urn' => $urn);
//                $this->template->load('default', 'sms/unsubscribe.php', $data);
//            } else {
//                $data = array(
//                    'msg' => "The company you tried to unsubscribe from does not exist on our system",
//                    'title' => 'Invalid sms');
//                $this->template->load('default', 'errors/display.php', $data);
//            }
//        }
//    }

    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** SEND MANUAL SMS ************************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    //Send an sms
    public function send_sms()
    {
        user_auth_check();
        $form = $this->input->post();

        $sender = ($form['template_sender'] ? $form['template_sender'] : null);
        $numbers = $form['sent_to'];
        $message = $form['template_text'];

        //Check if the sender selected exist
        if (!$this->check_sender_name($sender)) {
            echo json_encode(array(
                "data" => array("status" => "error", "msg" => "The selected sender name has not been configuired correctly")
            ));
            exit;
        }
        //Check if the message is empty
        if (strlen(trim($message)) <= 0) {
            echo json_encode(array(
                "data" => array("status" => "error", "msg" => "The message is empty")
            ));
            exit;
        }

        //Send the sms
        $customID = uniqid($form['urn'] . date('now'));
        $response = $this->send($customID, $numbers, $message, $sender);

        if ($response) {
            //Save the sms in the history table
            $sms_ar = array();
            $user_id = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : NULL;
            $status_id = (($response->status === "success") && ($response->test_mode !== TRUE) ? SMS_STATUS_UNKNOWN : SMS_STATUS_ERROR);
            $comments = (($response->status === "success") && ($response->test_mode !== TRUE) ? "Unknown, we have not received a delivery status from the networks." : "Test mode enabled, please disabled it in order to send the text");
            foreach ($numbers as $number) {
                array_push($sms_ar, array(
                    "text" => $message,
                    "send_to" => $number,
                    "sender_id" => $form['template_sender_id'],
                    "user_id" => $user_id,
                    "urn" => $form['urn'],
                    "template_id" => $form['template_id'],
                    "status_id" => $status_id,
                    "text_local_id" => $customID,
                    "comments" => $comments
                ));
            }

            $this->Sms_model->add_sms_histories($sms_ar);
        }

        echo json_encode(array(
            "data" => $response
        ));

    }

    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** SEND PENDING SMS ***********************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    public function send_pending_sms()
    {

        $output = "";
        $output .= "Sending pending sms... \n\n";
        $limit = 100;
        if (intval($this->uri->segment(3)) > 0) {
            $limit = $this->uri->segment(3);
        }

        //TEST
        if (intval($this->uri->segment(4)) > 0) {
            $test = $this->uri->segment(4);
        }

        //Get the oldest 100 mails pending to be sent
        $pending_sms = $this->Sms_model->get_pending_sms($limit);
        if (!empty($pending_sms)) {
            //Check if we have enough credits
            $status = SMS_STATUS_UNKNOWN;
            $comments = "Unknown, we have not received a delivery status from the networks.";
            $currentCredits = $this->textlocal->getBalance();
            if ($currentCredits['sms'] < count($pending_sms)) {
                $status = SMS_STATUS_PENDING;
                $comments = "There is no enough credits to send the texts. Get more credits and run the send_pending_sms process.";
            }

            //Build the messages array to send
            $messages = array();
            $sms_histories = array();
            $numbers = array();
            foreach ($pending_sms as $sms) {

                array_push($messages, array(
                    'sms_number' => $sms['send_to'],
                    'send_from' => $sms['sender_from'],
                    'sms_text' => $sms['text'],
                    'id' => $sms['text_local_id'],
                ));

                array_push($sms_histories, array(
                    "sms_id" => $sms['sms_id'],
                    "sent_date" => $sms['sent_date'],
                    "status_id" => $status,
                    "comments" => $comments
                ));

                array_push($numbers, $sms['send_to']);
            }
            if (!empty($messages)) {
                if ($status != SMS_STATUS_PENDING) {
                    if (!isset($test)) {
                        $test = (ENVIRONMENT !== "production") ? "true" : "false";
                    }
                    $response = $this->sendBulkSms($messages, $test);
                    //Save the sms_history
                    if ($response == "OK") {
                        $this->Sms_model->update_sms_histories($sms_histories);

                        $output .= "SMS sent to " . implode(',', $numbers) . "\n\n";
                    } else {
                        $output .= "SMS not sent from the sms server \n\n";
                    }
                } else {
                    //No enough credits
                    $response = "There is not enough credits: " . $currentCredits['sms'] . "\n\n";
                }
            }

            $output .= $response . "\n\n";

        } else {
            $output .= "No pending sms to be sent. \n\n";
        }

        echo $output;
    }


    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** SEND APPOINTMENT REMIND SMS ************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    public function send_appointment_remind_sms()
    {
        $output = "";
        $output .= "Sending appointment remind sms...";

        $template_id = null;
        $test = null;
        $app_type = null;

        //Template ID
        if (intval($this->uri->segment(3)) > 0) {
            $template_id = $this->uri->segment(3);
        }

        //TEST
        if (intval($this->uri->segment(4)) >= 0) {
            $test = $this->uri->segment(4);
        }

        //Appointment type
        if (intval($this->uri->segment(5)) >= 0) {
            $app_type_id = $this->uri->segment(5);
        }

        if ($template_id) {
            //Get the appointments and the contact number where the appointment date is tomorrow or the day after tomorrow
            $remind_appointments = $this->Sms_model->get_remind_appointments($template_id, $app_type_id);

            //Check if we have enough credits
            $status = SMS_STATUS_UNKNOWN;
            $comments = "Unknown, we have not received a delivery status from the networks.";
            $currentCredits = $this->textlocal->getBalance();
            if ($currentCredits['sms'] < count($remind_appointments)) {
                $status = SMS_STATUS_PENDING;
                $comments = "There is no enough credits to send the texts. Get more credits and run the send_pending_sms process.";
                $msg = "There is not enough credits: " . $currentCredits['sms'] . "\n\n";
            } //Check if more than 100 sms will be sent
            else if (count($remind_appointments) > 100) {
                $status = 1;
                $msg = "You are trying to send more than 100 text messages. The sms will not be sent and will be stored in the sms_history as pending. Please review the process and run the send_pending_sms process manually.\n\n";
            }

            if (!empty($remind_appointments)) {
                //Build the messages array to send
                $messages = array();
                $sms_histories = array();
                $numbers = array();
                foreach ($remind_appointments as $remind_appointment) {

                    //Check the variables inside [] in the template and change them with a value
                    preg_match_all('#\[(.*?)\]#', $remind_appointment['sms_text'], $matches);
                    foreach ($matches[0] as $key => $match) {
                        $remind_appointment['sms_text'] = str_replace($match, $remind_appointment[$matches[1][$key]], $remind_appointment['sms_text']);
                    }

                    $customID = uniqid($remind_appointment['urn'] . date('now'));

                    array_push($messages, array(
                        'sms_number' => format_mobile($remind_appointment['sms_number']),
                        'send_from' => $remind_appointment['sms_from'],
                        'sms_text' => $remind_appointment['sms_text'],
                        'id' => $customID,
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
                        "text_local_id" => $customID,
                        "comments" => $comments
                    ));

                    array_push($numbers, format_mobile($remind_appointment['sms_number']));
                }

                if (!empty($messages)) {

                    if ($status != SMS_STATUS_PENDING) {
                        $response = $this->sendBulkSms($messages, $test);
                        //Save the sms_history
                        if ($response == "OK") {
                            $this->Sms_model->add_sms_histories($sms_histories);

                            $output .= "SMS sent to " . implode(',', $numbers) . "\n\n";
                        } else {
                            $output .= "SMS not sent from the sms server \n\n";
                        }
                    } else {
                        //Save the sms_history as pending
                        $this->Sms_model->add_sms_histories($sms_histories);
                        $response = $msg;
                        echo $output . $response;
                        throw new Exception($response);
                    }
                }

                $output .= $response . "\n\n";

            } else {
                $output .= "No remind appointments sms to be send. \n\n";
            }
        } else {
            $output .= "No template id defined. \n\n";
        }

        $current_balance = $this->textlocal->getBalance();
        $output .= "Credits available: " . $current_balance['sms'];

        echo $output;
    }


    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** SEND SMS FOR RECORDS (Bulk Sms) ********************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
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
            $status = SMS_STATUS_UNKNOWN;
            $comments = "Unknown, we have not received a delivery status from the networks.";
            $currentCredits = $this->textlocal->getBalance();
            if ($currentCredits['sms'] < count($records)) {
                $status = SMS_STATUS_PENDING;
                $comments = "There is no enough credits to send the texts. Get more credits and run the send_pending_sms process.";
                $msg = "There is not enough credits: " . $currentCredits['sms'] . "\n\n";
            }

            if (!empty($records)) {
                //Build the messages array to send
                $messages = array();
                $sms_histories = array();
                $numbers = array();
                foreach ($records as $record) {

                    //Check the variables inside [] in the template and change them with a value
                    preg_match_all('#\[(.*?)\]#', $record['sms_text'], $matches);
                    foreach ($matches[0] as $key => $match) {
                        if (isset($record[$matches[1][$key]])) {
                            $record['sms_text'] = str_replace($match, $record[$matches[1][$key]], $record['sms_text']);
                        }
                    }

                    $customID = uniqid($record['urn'] . date('now'));

                    array_push($messages, array(
                        'sms_number' => format_mobile($record['sms_number']),
                        'send_from' => $record['sms_from'],
                        'sms_text' => $record['sms_text'],
                        'id' => $customID,
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
                        "text_local_id" => $customID,
                        "comments" => $comments
                    ));

                    array_push($numbers, format_mobile($record['sms_number']));
                }

                if (!empty($messages)) {

                    if ($status != SMS_STATUS_PENDING) {
                        $response = $this->sendBulkSms($messages, $test);
                        //Save the sms_history
                        if ($response == "OK") {
                            $this->Sms_model->add_sms_histories($sms_histories);
                            $output .= "SMS sent to " . count($numbers) . " numbers \n\n";
                        } else {
                            $output .= "SMS not sent from the sms server \n\n";
                        }
                    } else {
                        //Save the sms_history as pending
                        $this->Sms_model->add_sms_histories($sms_histories);
                        $response = $msg;
                        echo $output . $response;
                        throw new Exception($response);
                    }
                }

                $output .= $response . "\n\n";

            } else {
                $output .= "No remind appointments sms to be send. \n\n";
            }
        } else if (!$template_id) {
            $output .= "No template id defined. \n\n";
        } else if (!$source_id) {
            $output .= "No source id defined. \n\n";
        }

        $current_balance = $this->textlocal->getBalance();
        $output .= "Credits available: " . $current_balance['sms'];

        echo $output;
    }


    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** Check if the sender name exist *********************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    private function check_sender_name($sender)
    {
        //If the sender is null or does not exist on the list, get the default one
        $sender_names = $this->textlocal->getSenderNames();
        if (!$sender || !in_array($sender, $sender_names->sender_names)) {
            return false;
        } else {
            return true;
        }
    }

    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** Get the default sender name ************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    private function get_default_sender_name()
    {

        return $this->textlocal->getSenderNames()->default_sender_name;
    }


    public function send_from_url()
    {
        $numbers = array("+447814401867");
        $message = "Motion detected!";
        $sender = "one2One";
        $this->textlocal->sendSms($numbers, $message, $sender, null, null, null, null);
    }

    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** Send one sms ***************************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    private function send($customID, $numbers, $message, $sender = null)
    {

        $test = (ENVIRONMENT !== "production");
        $receiptUrl = "https://121system.com/sms/sms_delivery_receipt";

        if (!$sender) {
            $sender = $this->get_default_sender_name();
        }

        $response = $this->textlocal->sendSms($numbers, $message, $sender, null, $test, $receiptUrl, $customID);

        return $response;
    }


    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** Send bulk sms **************************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /**
     * @param $messages
     * @param $test false to send texts other case for testing
     *
     */
    private function sendBulkSms($messages, $test)
    {
        $test = ($test == 'false' ? 0 : 1);

        //Build the xml data
        $xmlData = '
        <SMS>
            <Account Name="jon-man@121customerinsight.co.uk" Password="x983kkdi" Test="' . $test . '" Info="0" JSON="0">
                <Sender From="' . $messages[0]['send_from'] . '" rcpurl="https://121system.com/sms/sms_delivery_receipt" ID="">
                    <Messages>';

        foreach ($messages as $message) {
            $xmlData .= '
                <Msg ID="' . $message['id'] . '" Number="' . $message['sms_number'] . '">
                    <Text>' . $message['sms_text'] . '</Text>
                </Msg>';
        }

        $xmlData .= '
                        </Messages>
                    </Sender>
                </Account>
            </SMS>';

        $post = 'data=' . urlencode($xmlData);
        $url = "http://www.txtlocal.com/xmlapi.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return str_replace("<br />", "", $data);

    }

    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** SMS Delivery Receipt *******************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    public function sms_delivery_receipt()
    {
        $post = $_POST;
        $post_content = "";
        foreach ($post as $key => $val) {
            $post_content .= $key . " => " . $val . ", ";
        }
        $post_content = (!empty($post) ? " POST: " . substr($post_content, 0, strlen($post_content) - 2) : " POST: empty");

        if (!isset($_POST['status']) || !isset($_POST['customID'])) {
            log_message('error', '[Sms Integration TextLocal][Delivery Receipt] Some variable (status or customID) did not contain a value.' . $post_content);
        } else {

            $status = $_POST['status'];
            $customID = $_POST['customID'];

            $response = $this->translateStatus($status);

            $status_id = $response['status_id'];
            $comments = $response['comments'];

            //Update the status for the sms_history record
            $sms_history = array(
                "text_local_id" => $customID,
                "status_id" => $status_id,
                "comments" => $comments
            );
            $this->Sms_model->update_sms_history_by_text_local_id($sms_history);
        }
    }

    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** Update sms_history status **************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    public function synchronize_sms_history()
    {

        echo "Sync sms history messages... \n";

        //$max_time = time(); // Get sends between now
        $max_time = strtotime('-5 minutes'); // and a month ago
        $min_time = strtotime('-7 days'); // and a month ago
        $limit = 1000;
        $start = 0;


        //Get the messages from text_local
        $response = $this->textlocal->getAPIMessageHistory($start, $limit, $min_time, $max_time);


        if (!empty($response->messages)) {

            echo "\t Found " . $response->total . " messages";
            echo " from " . gmdate("Y-m-d", $min_time) . " to " . gmdate("Y-m-d", $max_time) . " \n\n";

            //Get the records in our system

            $text_local_ids = array();
            $sms_message_history_ar = array();
            foreach ($response->messages as $key => $val) {
                if (isset($val->customID)) {
                    array_push($text_local_ids, $val->customID);
                    $translatedStatus = $this->translateStatus($val->status);
                    $val->status = $translatedStatus['status_id'];
                    $val->comments = $translatedStatus['comments'];
                    $sms_message_history_ar[$val->customID] = $val;
                }
            }

            $sms_history_ar = $this->Sms_model->get_sms_history_by_text_local_list($text_local_ids);

            //Update the status if it is needed
            $sms_history_to_update = array();
            foreach ($sms_history_ar as $sms_history) {
                if ($sms_history['status_id'] != $sms_message_history_ar[$sms_history['text_local_id']]->status) {
                    $sms_history['status_id'] = $sms_message_history_ar[$sms_history['text_local_id']]->status;
                    $sms_history['comments'] = $sms_message_history_ar[$sms_history['text_local_id']]->comments;

                    array_push($sms_history_to_update, $sms_history);
                }
            }
            //Update
            if (!empty($sms_history_to_update)) {
                $result = $this->Sms_model->update_sms_histories($sms_history_to_update);
                echo "\t" . count($sms_history_to_update) . " messages updated from sms history \n";
                foreach ($sms_history_to_update as $sms) {
                    echo "\t\t - " . $sms['send_to'] . "[" . $sms['sent_date'] . "] -> status_id = " . $sms['status_id'] . " , comments -> " . $sms['comments'] . " \n";
                }
            } else {
                echo "Nothing to update.\n";
            }
        } else {
            echo "No messages found.\n";
        }

    }


    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** translateStatus from the texlocal status to the status_id in our sysmte ****************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    private function translateStatus($status)
    {
        switch ($status) {
            case "D":
                $status_id = SMS_STATUS_SENT;
                $comments = "Message was delivered successfully.";
                break;
            case "U":
                $status_id = SMS_STATUS_UNDELIVERED;
                $comments = "The message was undelivered.";
                break;
            case "P":
                $status_id = SMS_STATUS_UNKNOWN;
                $comments = "Message pending, the message is en route.";
                break;
            case "I":
                $status_id = SMS_STATUS_ERROR;
                $comments = "The number was invalid.";
                break;
            case "E":
                $status_id = SMS_STATUS_ERROR;
                $comments = "The message has expired.";
                break;
            case "?":
                $status_id = SMS_STATUS_UNKNOWN;
                $comments = "Unknown, we have not received a delivery status from the networks.";
                break;
            default:
                $status_id = SMS_STATUS_UNKNOWN;
                $comments = "Unknown, we have not received a delivery status from the networks.";
                break;
        }

        return array(
            "status_id" => $status_id,
            "comments" => $comments
        );
    }

    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** trigger_sms ****************************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    public function trigger_sms()
    {
        //TODO this isn't working yet it needs finishing

    }

    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /****** Bulk sms Tool **************************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/
    public function bulk_sms()
    {
        if ($this->input->is_ajax_request() && $this->input->post('list')) {
            $lines = lines_to_list($this->input->post('list'));
            echo json_encode(array("count" => count($lines), "urns" => "(" . implode(",", $lines) . ")"));
            exit;
        }
        user_auth_check();
        $this->load->model('Form_model');
        $templates = $this->Form_model->get_sms_templates();
        $sms_senders = $this->Form_model->get_sms_senders();
        $data = array(
            'title' => 'Bulk Sms',
            'page' => 'bulk-sms',
            'templates' => $templates,
            'sms_senders' => $sms_senders
        );

        $this->template->load('default', 'sms/bulk_sms.php', $data);

    }

    public function send_bulk_sms()
    {

        if ($this->input->is_ajax_request()) {

            $template_id = intval($this->input->post('template_id'));
            $sender = $this->input->post('sender');
            $urns = $this->input->post('urns');
			$sender_field = $this->input->post('sender_field');

            //Validate the urn list format (urn1. urn2, ..., urnN)
            if (preg_match('/^[,0-9]*$/', $urns)) {

                $urn_list = explode(",", $urns);

                if ($template_id) {
                    $sender_id = intval($this->input->post('template_sender_id'));
                    $original_text = $this->input->post('template_text');
                } else {
                    $sender_id = intval($this->input->post('sender_id'));
                    $original_text = $this->input->post('text');
                }

                //Get the records and the contact number where the records status is 1 for a particular urn list
                $records = $this->Sms_model->get_records_numbers_by_urn_list($urn_list);

                //Check if we have enough credits
                $status = SMS_STATUS_UNKNOWN;
                $comments = "Unknown, we have not received a delivery status from the networks.";
                $currentCredits = $this->textlocal->getBalance();
                if ($currentCredits['sms'] < count($records)) {
                    $status = SMS_STATUS_PENDING;
                    $comments = "There is no enough credits to send the texts. Get more credits and run the send_pending_sms process.";
                    $msg = "There is not enough credits: " . $currentCredits['sms'] . "\n\n";
                }

                if (!empty($records)) {
                    //Build the messages array to send
                    $messages = array();
                    $sms_histories = array();
                    $numbers = array();
                    $user_id = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : NULL;

                    foreach ($records as $record) {

                        $customID = uniqid($record['urn'] . date('now'));

                        //Check the variables inside [] in the template and change them with a value if the template_id exist
                        $text = $original_text;
                        $placeholder_data = $this->Email_model->get_placeholder_data($record['urn']);
                        if (count($placeholder_data)) {
                            foreach ($placeholder_data[0] as $key => $val) {
                                if (strpos($text, "[$key]") !== false) {
                                    if (empty($val)) {
                                        setcookie("placeholder_error", $key, time() + (60), "/");
                                        if ($key == "start") {
                                            $text = str_replace("[$key]", "<span style=\"color:red\">** NO APPOINTMENT FOUND **</span>", $text);
                                        } else {
                                            $text = str_replace("[$key]", "<span style=\"color:red\">** [$key] WAS EMPTY **</span>", $text);
                                        }
                                    } else {
                                        $text = str_replace("[$key]", $val, $text);
										
						if ($sender == "") {
                        if ($sender_field == "$key" || $sender_field == "[$key]") {
                            $sender = $val;
                        }
                    }
                                    }
                                }
                            }
                        }

                        array_push($messages, array(
                            'sms_number' => format_mobile($record['sms_number']),
                            'send_from' => $sender,
                            'sms_text' => $text,
                            'id' => $customID,
                        ));

                        array_push($sms_histories, array(
                            "text" => $text,
                            "sender_id" => $sender_id,
                            "send_to" => format_mobile($record['sms_number']),
                            "user_id" => $user_id,
                            "urn" => $record['urn'],
                            "template_id" => $template_id,
                            "status_id" => $status,
                            "template_unsubscribe" => 0,
                            "text_local_id" => $customID,
                            "comments" => $comments
                        ));

                        array_push($numbers, format_mobile($record['sms_number']));
                    }

                    if (!empty($messages)) {

                        if ($status != SMS_STATUS_PENDING) {
                            $test = (ENVIRONMENT !== "production") ? "true" : "false";
                            $response = $this->sendBulkSms($messages, $test);
                            //Save the sms_history
                            if ($response == "OK") {
                                $this->Sms_model->add_sms_histories($sms_histories);
                                //$output .= "SMS sent to " . count($numbers) . " numbers \n\n";
                                echo json_encode(array(
                                    "success" => true,
                                    "msg" => ($test == 'false' ? "SMS sent to " . count($numbers) . " numbers" : "Test mode enabled, please disabled it in order to send the text. Texts should be sent to " . count($numbers) . " numbers"),
                                    "sms" => $sms_histories,
                                    "test" => ($test == 'false' ? 0 : 1)
                                ));
                            } else {
                                echo json_encode(array(
                                    "success" => false,
                                    "msg" => "SMS not sent from the sms server",
                                    "sms" => array()
                                ));
                            }
                        } else {
                            //Save the sms_history as pending
                            $this->Sms_model->add_sms_histories($sms_histories);
                            echo json_encode(array(
                                "success" => false,
                                "msg" => $msg,
                                "sms" => array()
                            ));
                        }
                    }

                } else {
                    //$output .= "No remind appointments sms to be send. \n\n";
                    echo json_encode(array(
                        "success" => false,
                        "msg" => "There is no numbers for this urn list",
                        "sms" => array()
                    ));
                }
            } else {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "Error in the urn list format (urn1. urn2, ..., urnN)",
                    "sms" => array()
                ));
            }

        }
    }

}