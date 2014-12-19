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
            $renewal_date_field =  $this->Form_model->get_renewald_date_field($campaign['id']);
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
	
	
	/* The following functions can be used to format postcodes and update geocoordinates in the uk_postcodes/locations table */
	public function update_all_locations(){
		 $this->Cron_model->update_locations_table();
		 $this->Cron_model->update_location_ids();
		 $this->Cron_model->update_locations_with_google();
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
}