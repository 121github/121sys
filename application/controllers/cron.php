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
    
    
    /**
     * Daily Ration
     */
    public function daily_ration()
    {
        $campaigns = $this->Form_model->get_campaigns();
        foreach ($campaigns as $campaign) {
            $renewal_date_field = $this->Form_model->get_renewald_date_field($campaign['id']);
            if ($renewal_date_field) {
                $renewal_date_field = $renewal_date_field[0]['field'];
                $update_records     = $this->Cron_model->set_daily_ration_records($campaign['id'], $renewal_date_field, $campaign['daily_data'], $campaign['min_quote_days'], $campaign['max_quote_days']);
                if (intval($update_records) >= 0) {
                    echo $update_records . " records from campaign " . $campaign['name'] . " updated!<br>";
                } else {
                    echo "ERROR updating the records from " . $campaign['name'] . " !<br>";
                }
            } else {
                echo "No records updated for the campaign " . $campaign['name'] . ". The renewal_date field is not set for the campaign " . $campaign['name'] . " !<br>";
            }
        }
    }
    
    
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
        $postcode = $this->uri->segment(3);
        if (!empty($postcode)) {
            $this->Cron_model->update_locations_table($postcode);
        }
        
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
    
    
    /**
     * Send export reports to the users selected
     */
    public function send_exports_to_users()
    {
        $export_users = $this->Export_model->get_export_users();
        
        $exports = array();
        foreach ($export_users as $export_user) {
            if (!isset($exports[$export_user['export_forms_id']])) {
                $exports[$export_user['export_forms_id']]['users']       = array();
                $export_form                                             = $this->Export_model->get_export_forms_by_id($export_user['export_forms_id']);
                $exports[$export_user['export_forms_id']]['export_form'] = $export_form;
            }
            array_push($exports[$export_user['export_forms_id']]['users'], $export_user);
        }
        
        foreach ($exports as $export) {
            $export_form = $export['export_form'];
            
            $filename = date("YmdHsi") . "_" . str_replace(" ", "", $export_form['name']);
            $dirname  = dirname($_SERVER['SCRIPT_FILENAME']) . '/upload/tmp/exports/';
            $headers  = explode(";", $export_form['header']);
            
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
            $body    = "You have received a new export data - " . $filename;
            
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
        
        var_dump($email_address);
        
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
        $this->email->subject($subject);
        $this->email->message($body);
        
        
        //Attach the file
        $this->email->attach($filePath);
        
        $result = $this->email->send();
        //echo $this->email->print_debugger();
        $this->email->clear();
        
        return $result;
    }
}