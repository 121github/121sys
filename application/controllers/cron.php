<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Form_model');
        $this->load->model('Cron_model');
        $this->load->model('Export_model');
        $this->load->model('Data_model');
    }

    public function update_hours()
    {
        $agents = $this->Form_model->get_agents();
        $this->Cron_model->update_hours($agents);

    }

    public function clear_hours()
    {
        $this->Cron_model->clear_hours();
    }


    //################################################################################################
    //################################### DAILY RATION functions #####################################
    //################################################################################################

    /**
     * Daily Ration
     */
    public function daily_ration()
    {
        echo "Daily Ration...\n\n";
        $update_records = 0;
        $campaigns = $this->Form_model->get_campaigns();
        foreach ($campaigns as $campaign) {
            $renewal_date_field = $this->Form_model->get_renewal_date_field($campaign['id']);
            //we only add in daily data if an amount as been set
            if ($campaign['daily_data'] > 0) {

                if ($renewal_date_field) {
                    //if the data contains a renewal date we only want to call records that are within the allowed quoting days
                    $renewal_date_field = $renewal_date_field[0]['field'];
                    $update_records = $this->Cron_model->set_daily_ration_renewals($campaign['id'], $renewal_date_field, $campaign['daily_data'], $campaign['min_quote_days'], $campaign['max_quote_days']);
                } else {
                    //if data has no renewal we just add in a fixed amount
                    $update_records = $this->Cron_model->set_daily_ration_records($campaign['id'], $campaign['daily_data']);
                }

                if (intval($update_records) >= 0) {
                    echo $update_records . " records from campaign " . $campaign['name'] . " were made available for calling\n";
                } else {
                    echo "No records available for dialing in " . $campaign['name'] . " !\n";
                }
            } else {
                echo "No records updated for the campaign " . $campaign['name'] . ". The renewal_date field is not set for the campaign " . $campaign['name'] . " !\n";
            }
        }
    }


    //################################################################################################
    //################################### LOCATION functions #########################################
    //################################################################################################

    /* The following functions can be used to format postcodes and update geocoordinates in the uk_postcodes/locations table */
    public function update_all_locations()
    {
        $this->Cron_model->update_locations_table();
        $this->Cron_model->update_location_ids();
        $this->Cron_model->update_locations_with_google();
        echo json_encode(array(
            "success" => true
        ));
    }

    public function update_locations_table()
    {
        //sets invalid postcodes to null and looks up valid postcodes in the uk_postcodes table and copies them to the locations table
        $this->Cron_model->update_locations_table();

    }

    public function update_location_ids()
    {
        //sets the location ids on in the contact/company address and appointment table using the id from uk_postcodes where it has a matching postcode
        $this->Cron_model->update_location_ids();

    }

    public function update_locations_with_google()
    {
        //if we have any records left over that dont have a location_id we add the postcode to the uk_postcodes table using google maps api
        $this->Cron_model->update_locations_with_google();
    }

    /* end location functions */


    //################################################################################################
    //################################### EXPORT REPORT functions ####################################
    //################################################################################################

    /**
     * Send export reports to the users selected
     */
    public function send_exports_to_users()
    {
        $export_users = $this->Export_model->get_export_users();

        $exports = array();
        foreach ($export_users as $export_user) {
            if (!isset($exports[$export_user['export_forms_id']])) {
                $exports[$export_user['export_forms_id']]['users'] = array();
                $export_form = $this->Export_model->get_export_forms_by_id($export_user['export_forms_id']);
                $exports[$export_user['export_forms_id']]['export_form'] = $export_form;
            }
            array_push($exports[$export_user['export_forms_id']]['users'], $export_user);
        }

        foreach ($exports as $export) {
            $export_form = $export['export_form'];

            $filename = date("YmdHsi") . "_" . str_replace(" ", "", $export_form['name']);
            $dirname = dirname($_SERVER['SCRIPT_FILENAME']) . '/upload/tmp/exports/';
            $headers = explode(";", $export_form['header']);

            $result = $this->Export_model->get_data($export_form, array());

            //Export the data to a csv file
            if ($this->save_export_to_csv($result, $dirname, $filename, $headers)) {
                echo "The file " . $filename . ".csv was exported to a csv file" . " !<br>";
                //Send the file to the users
                if ($this->send_file_exported_by_email($dirname, $filename, $export['users'])) {
                    //Delete the temp file
                    if (unlink($dirname . $filename . ".csv")) {
                        echo "The file " . $filename . ".csv was sent to the user/s selected" . " !<br>";
                    } else {
                        echo "The file " . $filename . ".csv was sent to the user/s selected, but the temp file was not deleted" . " !<br>";
                    }
                } else {
                    echo "Error sending the file " . $filename . ".csv to the user/s selected" . " !<br>";
                }
            } else {
                echo "Error exporting the data to the csv file " . $filename . ".csv !<br>";
            }
        }

    }


    //################################################################################################
    //################################### MATRIX CV functions ########################################
    //################################################################################################

    public function matrix_cv_upload()
    {
        //first prepare all the files by checking for and removing dupes
        $this->add_hashes();
        $this->remove_dupe_files();

        $count = $this->db->query("select * from files where folder_id = 1")->num_rows();
        $sent = $this->db->query("select * from files where folder_id = 1 and email_sent = 1")->num_rows();
        $qry = "select * from files left join folders using(folder_id) where folder_name = 'cv' and date(date_added)=curdate() and email_sent=0 limit 50";
        $q = $this->db->query($qry);

        $result = $q->result_array();
        $i = ($sent > 0 ? $sent + 1 : 1);
        foreach ($result as $k => $row) {
            sleep(1);
            echo $row['file_id'];
            $file = FCPATH . "/upload/cv/" . date("Y-m-d", strtotime($row['date_added'])) . "/" . $row['filename'];
            $subject = "New CV File";
            $body = "The attached CV was uploaded on " . date("d/m/Y", strtotime($row['date_added'])) . "<br>Filename: " . $row['filename'] . "<br>File ID: " . $row['file_id'] . "<br>Progress: " . $i . " of " . $count;
            if ($this->send_email($file, "cvmanu@matrix.eu.com", $subject, $body)) {
                $this->db->where(array("file_id=" => $row['file_id']));
                $this->db->update("files", array(
                    "email_sent" => "1"
                ));
            } else {
                mail("bradf@121customerinsight.co.uk", "Matrix email failed", $row['file_id'] . " failed to send for an unknown reason :(");
            }
            $i++;
        }

    }

    //################################################################################################
    //################################### HASHES functions ##########################################
    //################################################################################################

    public function add_hashes()
    {
        $this->load->model('Docscanner_model');
        $this->load->helper('scan');

        $qry = "select * from files where doc_hash is null and folder_id = 1 order by file_id";
        $q = $this->db->query($qry);
        $result = $q->result_array();
        foreach ($result as $k => $row) {
            $hash = '';
            $update = '';
            $file = FCPATH . "/upload/cv/" . date("Y-m-d", strtotime($row['date_added'])) . "/" . $row['filename'];
            echo $file;
            echo "<br>";
            $doctxt = $this->Docscanner_model->convertToText($file);
            //echo $doctxt;
            echo $hash = md5($doctxt);

            $update = "update files set doc_hash = '$hash' where file_id = " . $row['file_id'];
            $this->db->query($update);
            echo "<hr><hr>";
        }
        echo "Hashed Added";
    }


    //################################################################################################
    //################################### DUPLICATES functions #######################################
    //################################################################################################

    public function remove_dupe_files()
    {

        $qry = "SELECT doc_hash, count( * ) count
                FROM `files` where folder_id = 1 and doc_hash is not null and doc_hash <> ''
                GROUP BY doc_hash
                HAVING count( doc_hash ) >1";
        $q = $this->db->query($qry);
        $result = $q->result_array();
        $docs = array();
        $i = 0;
        foreach ($result as $k => $row) {
            $i++;
            $remove = $row['count'] - 1;
            echo $delete = "delete from files where doc_hash = '" . $row['doc_hash'] . "' order by date_added desc limit $remove;";
            echo "<br>";
            $this->db->query($delete);
        }
        echo $i . " Duplicates can be deleted";

    }

    //################################################################################################
    //################################### TIDY FILES functions #######################################
    //################################################################################################

    public function tidy_files()
    {
        //delete files that dont exist in the database
        $directory = FCPATH . "upload/cv";
        $folders = array_diff(scandir($directory), array(
            '..',
            '.'
        ));

        foreach ($folders as $folder) {
            $files = array_diff(scandir($directory . "/" . $folder), array(
                '..',
                '.'
            ));
            //print_r($files);
            foreach ($files as $file) {
                $check = "select file_id from files where filename='" . addslashes($file) . "'";
                if ($this->db->query($check)->num_rows() == 0) {
                    $fullpath = $directory . "/$folder/$file";
                    if (unlink($fullpath)) {
                        echo "deleted $fullpath";
                        echo "<br>";
                    }
                }
            }
        }
    }


    //################################################################################################
    //################################### WRONG TELEPHONE NUMBERS functions ##########################
    //################################################################################################

    /**
     * Check and fix the contact telephone numbers
     */
    public function check_contact_telephone_numbers()
    {
        $output = "";
        $output .= "\nChecking and fixing wrong contact telephone numbers... \n\n";

        //Get the wrong telephone numbers
        $wrong_telephone_numbers = $this->Cron_model->get_wrong_contact_telephone_numbers();
        $aux = array();

        //Reformat the telephone numbers
        $update_telephone_numbers = array();
        $delete_telephone_numbers = array();
        foreach ($wrong_telephone_numbers as $contact) {
            $aux[$contact['contact_id']] = $contact['telephone_number'];

            $new_telephone_number = $contact['telephone_number'];
            $new_telephone_number = str_replace('+44', '0', $new_telephone_number);
            $new_telephone_number = str_replace('+', '00', substr($new_telephone_number, 0, 2)) . substr($new_telephone_number, 2);
            $new_telephone_number = str_replace('(0)', '', $new_telephone_number);

            $new_telephone_number = preg_replace('/[^0-9]/', '', $new_telephone_number);


            if (strlen($new_telephone_number) < 7) {
                $contact['telephone_number'] = '';
                array_push($delete_telephone_numbers, $contact);
            } else {
                if ($new_telephone_number != $contact['telephone_number']) {
                    $contact['telephone_number'] = $new_telephone_number;
                    array_push($update_telephone_numbers, $contact);
                }
            }
        }

        $wrong_telephone_numbers = $aux;

        //Delete the telephone numbers with length less than 7
        $output .= "\tDeleting wrong contact telephone numbers (length less than 7)... ";
        if (!empty($delete_telephone_numbers)) {
            $result = $this->Cron_model->update_contact_telephone_numbers($delete_telephone_numbers);
            $output .= ($result ? "OK" : "KO") . "--> " . count($delete_telephone_numbers) . " deleted \n\n";
            foreach ($delete_telephone_numbers as $val) {
                $output .= "\t\tContact ID: " . $val['contact_id'] . " - " . $wrong_telephone_numbers[$val['contact_id']] . " deleted \n";
            }
            $output .= "\n";
        } else {
            $output .= "OK --> 0 deleted \n\n";
        }


        //Update the copmany with the new telephone_numbers
        $output .= "\tUpdating wrong contact telephone numbers... ";
        if (!empty($update_telephone_numbers)) {
            $result = $this->Cron_model->update_contact_telephone_numbers($update_telephone_numbers);
            $output .= ($result ? "OK" : "KO") . "--> " . count($update_telephone_numbers) . " updated \n\n";
            foreach ($update_telephone_numbers as $val) {
                $output .= "\t\tContact ID: " . $val['contact_id'] . " - " . $wrong_telephone_numbers[$val['contact_id']] . " updated with " . $val['telephone_number'] . " \n";
            }
            $output .= "\n";
        } else {
            $output .= "OK --> 0 updated \n\n";
        }

        $output .= "Finished!! \n\n";

        //Send email if something was changed
        if (!empty($update_telephone_numbers) || !empty($delete_telephone_numbers)) {
            $email_output = $output;
            $email_output = str_replace("\n", "<br>", $email_output);
            $output .= "Sending email...";
            $this->send_email('', 'estebanc@121customerinsight.co.uk,bradf@121customerinsight.co.uk', '[121SYS][CRON] Checking contact telephone numbers', $email_output);
            $output .= "OK\n\n";
        }

        echo $output;
    }

    /**
     * Check and fix the company telephone numbers
     */
    public function check_company_telephone_numbers()
    {
        $output = "";
        $output .= "Checking and fixing wrong company telephone numbers... \n\n";

        //Get the wrong telephone numbers
        $wrong_telephone_numbers = $this->Cron_model->get_wrong_company_telephone_numbers();
        $aux = array();

        //Reformat the telephone numbers
        $update_telephone_numbers = array();
        $delete_telephone_numbers = array();
        foreach ($wrong_telephone_numbers as $company) {
            $aux[$company['company_id']] = $company['telephone_number'];

            $new_telephone_number = $company['telephone_number'];
            $new_telephone_number = str_replace('+44', '0', $new_telephone_number);
            $new_telephone_number = str_replace('+', '00', substr($new_telephone_number, 0, 2)) . substr($new_telephone_number, 2);
            $new_telephone_number = str_replace('(0)', '', $new_telephone_number);

            $new_telephone_number = preg_replace('/[^0-9]/', '', $new_telephone_number);

            if (strlen($new_telephone_number) < 7) {
                $company['telephone_number'] = '';
                array_push($delete_telephone_numbers, $company);
            } else {
                if ($new_telephone_number != $company['telephone_number']) {
                    $company['telephone_number'] = $new_telephone_number;
                    array_push($update_telephone_numbers, $company);
                }
            }
        }

        $wrong_telephone_numbers = $aux;

        //Delete the telephone numbers with length less than 7
        $output .= "\tDeleting wrong company telephone numbers (length less than 7)... ";
        if (!empty($delete_telephone_numbers)) {
            $result = $this->Cron_model->update_company_telephone_numbers($delete_telephone_numbers);
            $output .= ($result ? "OK" : "KO") . " --> " . count($delete_telephone_numbers) . " deleted \n\n";
            foreach ($delete_telephone_numbers as $val) {
                $output .= "\t\tCompany ID: " . $val['company_id'] . " - " . $wrong_telephone_numbers[$val['company_id']] . " updated \n";
            }
            $output .= "\n";
        } else {
            $output .= "OK --> 0 deleted \n\n";
        }
        $output .= "\n";


        //Update the copmany with the new telephone_numbers
        $output .= "\tUpdating wrong company telephone numbers... ";
        if (!empty($update_telephone_numbers)) {
            $result = $this->Cron_model->update_company_telephone_numbers($update_telephone_numbers);
            $output .= ($result ? "OK" : "KO") . " --> " . count($update_telephone_numbers) . " updated \n\n";
            foreach ($update_telephone_numbers as $val) {
                $output .= "\t\tCompany ID: " . $val['company_id'] . " - " . $wrong_telephone_numbers[$val['company_id']] . " updated with " . $val['telephone_number'] . " \n";
            }
            $output .= "\n";
        } else {
            $output .= "OK --> 0 updated \n\n";
        }

        $output .= "Finished!! \n\n";

        //Send email if something was changed
        if (!empty($update_telephone_numbers) || !empty($delete_telephone_numbers)) {
            $email_output = $output;
            $email_output = str_replace("\n", "<br>", $email_output);
            $output .= "Sending email...";
            $this->send_email('', 'estebanc@121customerinsight.co.uk,bradf@121customerinsight.co.uk', '[121SYS][CRON] Checking company telephone numbers', $email_output);
            $output .= "OK\n\n";
        }

        echo $output;
    }


    //################################################################################################
    //################################### TPS/CTPS functions #########################################
    //################################################################################################

    /**
     * Check the tps for an specific telephone number
     */
    public function check_tps()
    {

        //Ajax request (Check the tps table and the API if it is not in tps table)
//        if ($this->input->is_ajax_request()) {
//
//            $form = $this->input->post();
//            $telephone_number = $form['telephone_number'];
//            $type = $form['type'];
//
//            if ($telephone_number && $type) {
//                //Check if the telephone number is in the tps table
//                $tps = $this->check_tps_table($telephone_number, $type);
//
//                $success = false;
//                if (!empty($tps)) {
//                    if ($tps[0][$type]) {
//                        $success = true;
//                    }
//                    else {
//                        $success = false;
//                    }
//
//                }
//                else {
//                    //Check if the api (selectabase API) return successfull data
//                    $api_response = $this->check_tps_api($telephone_number);
//                    $api_response = json_decode($api_response,true);
//
//                    if ($api_response[$type]) {
//                        $success = true;
//                        //Update this telephone_number in the tps table with tps or ctps as true
//                        $this->Cron_model->update_number_to_tps_table(array("telephone" => $telephone_number, $type => 1));
//                    }
//                    else {
//                        $success = false;
//                        //Update this telephone_number in the tps table with tps or ctps as false
//                        $this->Cron_model->update_number_to_tps_table(array("telephone" => $telephone_number, $type => 0));
//                    }
//                }
//                //Update company
//                if (isset($form['company_id'])) {
//                    if (isset($form['telephone_id'])) {
//                        $company = array(
//                            "company_id" => $form['company_id'],
//                            "telephone_id" => $form['telephone_id'],
//                            "telephone_number" => $form['telephone_number'],
//                            "ctps" => ($success?1:0)
//                        );
//                        $company_response = $this->Cron_model->update_company_telephone_numbers(array($company));
//                    }
//                    echo json_encode(array(
//                        "success" => ($tps),
//                        "msg" => ($success?"This number IS ".$type." registerd":"This number (".$telephone_number.") is NOT ".$type." registerd"),
//                        "api_response" => (isset($api_response)?$api_response:"No api request. Founded on the tps table"),
//                        "ctps" => ($success?1:0)
//                    ));
//                }
//                //Update contact
//                else if (isset($form['contact_id'])) {
//                    if (isset($form['telephone_id'])) {
//                        $contact = array(
//                            "contact_id" => $form['contact_id'],
//                            "telephone_id" => $form['telephone_id'],
//                            "telephone_number" => $form['telephone_number'],
//                            "tps" => ($success ? 1 : 0)
//                        );
//                        $contact_response = $this->Cron_model->update_contact_telephone_numbers(array($contact));
//                    }
//                    echo json_encode(array(
//                        "success" => ($tps),
//                        "msg" => ($success?"This number IS ".$type." registerd":"This number (".$telephone_number.") is NOT ".$type." registerd"),
//                        "api_response" => (isset($api_response)?$api_response:"No api request. Founded on the tps table"),
//                        "tps" => ($success ? 1 : 0)
//                    ));
//                }
//            }
//            else {
//                echo json_encode(array(
//                    "success" => false,
//                    "msg" => "The telephone_number (".$telephone_number.") or the type (".$type.") does not exist in the request"
//                ));
//            }
//        }
//        else {
//            echo json_encode(array(
//                "success" => false,
//                "msg" => "Error in the request",
//            ));
//        }

        echo json_encode(array(
            "success" => false,
            "msg" => "Sorry, this functionality is disabled for now!",
        ));

    }

    /**
     * Check the tps for all the telephone numbers (company and contact)
     * This function just check the tps table not the api
     *
     */
    public function check_all_tps()
    {

        //COMPANIES

        //TODO: Reset companies ctps with the value in the ctps older than 6 months

        //Get the company telephone numbers with tps field NULL
        $company_telephone_numbers = $this->Cron_model->get_no_ctps_company_telephone_numbers_in_tps_table();
        //Update the companies
        $company_response = true;
        if (!empty($company_telephone_numbers)) {
            $company_response = $this->Cron_model->update_company_telephone_numbers($company_telephone_numbers);
        }

        //CONTACTS

        //TODO: Reset contacts tps with the value in the ctps older than 6 months

        //Get the contact telephone numbers with tps field NULL
        $contact_telephone_numbers = $this->Cron_model->get_no_tps_contact_telephone_numbers_in_tps_table();
        //Update the contacts
        $contact_response = true;
        if (!empty($contact_telephone_numbers)) {
            $contact_response = $this->Cron_model->update_contact_telephone_numbers($contact_telephone_numbers);
        }

        echo json_encode(array(
            "success" => ($contact_response && $company_response),
            "count_companies" => count($company_telephone_numbers),
            "count_contacts" => count($contact_telephone_numbers)
        ));
    }


    //################################################################################################
    //################################### SUPPRESSION functions ######################################
    //################################################################################################

    /**
     * Check and change the parkcode to suppress for the records with a number in the suppression table
     */
    public function check_suppressed_records()
    {
        $output = "";
        $output .= "\nChecking suppressed telephone numbers... ";


        //Suppress the records with this telephone number in their contacts or company details
        $num_records_suppressed = $this->Cron_model->suppress_records();

        $output .= $num_records_suppressed." Records affected \n\n";

        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                "success" => true,
                "num_records_suppressed" => $num_records_suppressed,
                "msg" => $num_records_suppressed." records suppressed"
            ));
        }
        else {
            echo $output;
        }
    }


    //################################################################################################
    //################################### PRIVATE functions ##########################################
    //################################################################################################

    private function check_tps_table($telephone_number, $type)
    {
        return $this->Cron_model->check_tps_by_telephone_number($telephone_number, $type);
    }

    private function check_tps_api($telephone_number)
    {

        $username = 'dougf@121customerinsight.co.uk';
        $api_key = 'LPO892jh@@nmUJ76298';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.selectabase.co.uk/api/v1/tps/' . $telephone_number . '/?format=json&username=' . $username . '&api_key=' . $api_key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Generate and save the export data to an csv file temporally
     */
    private function save_export_to_csv($data, $dirname, $filename, $headers)
    {

        $path = $dirname . $filename . ".csv";

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$filename}.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $outputBuffer = fopen($path, 'w');

        fputcsv($outputBuffer, $headers);
        foreach ($data as $val) {
            fputcsv($outputBuffer, $val);
        }
        if (fclose($outputBuffer)) {
            return true;
        } else {
            return false;
        }
    }

    private function send_file_exported_by_email($dirname, $filename, $users)
    {

        if (!empty($users)) {
            $email_address = "";
            foreach ($users as $user) {
                if (strlen($user['user_email']) > 0) {
                    $email_address .= $user['user_email'] . ",";
                }
            }
            if (strlen($email_address) > 0) {
                $email_address = substr($email_address, 0, strlen($email_address) - 1);
            }

            $subject = "[121System] New export data received - " . $filename;
            $body = "You have received a new export data - " . $filename;

            echo "The file " . $filename . " is going to be sent to the user/s: " . $email_address . " " . " !<br>";

            if ($this->send_email($dirname . $filename . ".csv", $email_address, $subject, $body)) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function send_email($filePath, $email_address, $subject, $body)
    {

        //var_dump($email_address);

        $this->load->library('email');

        $config = array(
            "smtp_host" => "mail.121system.com",
            "smtp_user" => "mail@121system.com",
            "smtp_pass" => "L3O9QDirgUKXNE7rbNkP",
            "smtp_port" => 25
        );

        $config['mailtype'] = 'html';

        $this->email->initialize($config);

        $this->email->from('noreply@121customerinsight.co.uk');
        $this->email->to($email_address);
        $this->email->bcc("bradf@121customerinsight.co.uk");
        $this->email->subject($subject);
        $this->email->message($body);


        //Attach the file
        if ($filePath && strlen($filePath) > 0) {
            $this->email->attach($filePath);
        }

        $result = $this->email->send();
        //echo $this->email->print_debugger();
        $this->email->clear(TRUE);


        return $result;
    }

}