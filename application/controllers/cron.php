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
    }

    public function format_postcodes()
    {
        $unformatted = $this->Cron_model->get_unformatted_contact_postcodes();
        foreach ($unformatted as $row) {
            $formatted_postcode = postcodeFormat($row['postcode'], $row['id']);
            $this->Cron_model->format_contact_postcode($formatted_postcode);
            echo "updated postcode " . $row['postcode'] . " to " . $formatted_postcode . " on contact_id " . $row['id'] . "<br>";
        }
        $unformatted = $this->Cron_model->get_unformatted_company_postcodes();
        foreach ($unformatted as $row) {
            $formatted_postcode = postcodeFormat($row['postcode']);
            $this->Cron_model->format_company_postcode($formatted_postcode, $row['id']);
            echo "updated postcode " . $row['postcode'] . " to " . $formatted_postcode . " on company_id " . $row['id'] . "<br>";
        }
    }

    public function add_missing_postcodes()
    {
        $missing = $this->Cron_model->get_missing_company_postcodes();
        foreach ($missing as $row) {
            $response = postcode_to_coords($row['postcode']);
            if (!isset($response['error'])) {
                $this->Cron_model->update_missing($row['postcode'], $response);
                echo $row['postcode'] . " updated!<br>";
            } else {
                echo $response['error'] . "<br>";
            }
        }
        $missing = $this->Cron_model->get_missing_contact_postcodes();
        foreach ($missing as $row) {
            $response = postcode_to_coords($row['postcode']);
            if (!isset($response['error'])) {
                $this->Cron_model->update_missing($row['postcode'], $response);
                echo $row['postcode'] . " updated!<br>";
            } else {
                echo $response['error'] . "<br>";
            }
        }
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


    public function daily_ration()
    {
        $campaigns = $this->Form_model->get_campaigns();
        foreach ($campaigns as $campaign) {
            $renewal_date_field =  $this->Cron_model->get_renewald_date_field($campaign['id']);
            if ($renewal_date_field) {
                $renewal_date_field = $renewal_date_field[0]['field'];
                $update_records = $this->Cron_model->set_daily_ration_records($campaign['id'], $renewal_date_field, $campaign['daily_data'], $campaign['min_quote_days'], $campaign['max_quote_days']);
                if (intval($update_records) >= 0) {
                    echo $update_records . " records from campaign " . $campaign['name'] . " updated!<br>";
                } else {
                    echo "ERROR updating the records from " . $campaign['name'] . " !<br>";
                }
            }
            else {
                echo "No records updated for the campaign " . $campaign['name'] . ". The renewal_date field is not set for the campaign " . $campaign['name'] . " !<br>";
            }
        }
    }
}