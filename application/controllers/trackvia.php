<?php

require_once(APPPATH.'libraries/trackvia_api/Api.php');
require_once(APPPATH.'libraries/trackvia_api/Log.php');

use Trackvia\Api;
use Trackvia\Log;

define('CLIENT_ID', '252_50je1r05y5wc8w4kcws04g08scoo0o8k4kwwok0wgkkw8ggw4w');
define('CLIENT_SECRET', 'qhhgy6bbdc0w8gc0kc0kc0k88gw0ko0oskocock0wc8gw48w8');
define('USERNAME', 'ghsAPI');
define('PASSWORD', 'global123');

define('SOUTHWAY_ALL_RECORDS', '3000718568');
define('SOUTHWAY_BOOK_SURVEY', '3000718751');
define('SOUTHWAY_REBOOK', '3000718753');
define('SOUTHWAY_SURVEY_SLOTS', '3000718736');

define('PRIVATE_INFORM_INELIGIBLE', '');
define('PRIVATE_RESIDENTIAL', '');


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trackvia extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
        $this->load->model('Form_model');
        $this->load->model('Trackvia_model');

        $this->_campaigns = campaign_access_dropdown();

        // Create a TrackviaApi object with your clientId and secret.
        // The client_id and secret are only used when you need to request a new access token.
        $this->tv = new Api(array(
            'client_id'     => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'username'      => USERNAME,
            'password'      => PASSWORD
        ));
    }

    public function checkTrackviaSystem() {

        //SOUTHWAY TABLE

        //Book View
        $this->checkView(
            SOUTHWAY_BOOK_SURVEY,
            array(
                'campaign_id' => 22,
                'urgent' => NULL,
                'status' => NULL,
                'appointment_creation' => false
            )
        );

        //Rebook View
        $this->checkView(
            SOUTHWAY_REBOOK,
            array(
                'campaign_id' => 25,
                'urgent' => 1,
                'status' => NULL,
                'appointment_creation' => false
            )
        );

        //Survey Slots View
        $this->checkView(
            SOUTHWAY_SURVEY_SLOTS,
            array(
                'campaign_id' => 26,
                'urgent' => NULL,
                'status' => 4,
                'appointment_creation' => true
            )
        );
//
//        //PRIVATE TABLE
//
//        //Private Residential View
//        $this->checkView(
//            PRIVATE_RESIDENTIAL,
//            array(
//                'campaign_id' => 27,
//                'urgent' => NULL,
//                'status' => NULL,
//                'appointment_creation' => false
//            )
//        );
//
//        //Private Ineligible View
//        $this->checkView(
//            PRIVATE_INFORM_INELIGIBLE,
//            array(
//                'campaign_id' => 28,
//                'urgent' => NULL,
//                'status' => NULL,
//                'appointment_creation' => false
//            )
//        );

    }

    /**
     * Test
     */
    public function checkView($view_id,$options) {

        $campaign_id = $options['campaign_id'];
        $urgent = $options['urgent'];
        $status = $options['status'];
        $appointment_creation = $options['appointment_creation'];


        //Get the trackvia records for this view
        $view = $this->tv->getView($view_id);
        $tv_records = $view['records'];

        //Get the locator ids (client_ref in our system
        $tv_record_ids = array();
        $aux = array();
        foreach($tv_records as $tv_record) {
            array_push($tv_record_ids,$tv_record['id']);
            $aux[$tv_record['id']] = $tv_record;
        }
        $tv_records = $aux;

        $this->firephp->log(count($tv_records));

        //Get the records to be updated in our system
        $records = $this->Trackvia_model->getRecordsByTVIds($tv_record_ids);

        //Update the record campaign if it is needed (different campaign) and create a new one if it does not exist yet
        $update_records = array();
        $new_records_ids = $tv_record_ids;
        foreach($records as $record) {

            //If the campaign had changed or the park_code is "Not Working"
            if (($record['campaign_id'] != $campaign_id) || ($record['parked_code'] == 2)) {
                array_push($update_records, array(
                        'urn' => $record['urn'],
                        'campaign_id' => $campaign_id,
                        'parked_code' => NULL,
                        'urgent' => $urgent,
                        'record_status' => $status
                    )
                );

                //Create appointment if it is needed
                $fields = $tv_records[$record['client_ref']]['fields'];
                $planned_survey_date = (isset($fields['Survey date'])?$fields['Survey date']:'');
                $planned_survey_time = $fields['Survey appt'];
                switch ($planned_survey_time) {
                    case "am":
                        $planned_survey_time = "09:00:00";
                        break;
                    case "pm":
                        $planned_survey_time = "12:00:00";
                        break;
                    case "eve":
                        $planned_survey_time = "16:00:00";
                        break;
                    default:
                        $planned_survey_time = "";
                        break;
                }

                $planned_survey_datetime = $planned_survey_date." ".$planned_survey_time;

                //TODO Add appointment if the survey_date is different in both systems
                if ($appointment_creation && $record['survey_date']!=$planned_survey_datetime) {
                    //Create a new appointment if it is needed

                }
            }
            //Remove from the new_record_ids array the records that already exist on our system
            unset($new_records_ids[array_search($record['client_ref'],$tv_record_ids)]);
        }

        //Update the records which campaign was changed
        $this->firephp->log(count($update_records).' updated');
        $this->Trackvia_model->updateRecords($update_records);

        //Create the new records that not exist in our system yet
        $new_records = array();
        foreach($tv_records as $tv_record) {
            if (array_search($tv_record['id'],$new_records_ids))
            array_push($new_records,$tv_record);
        }
        //TODO Add the new records in our system
        //$this->firephp->log(count($new_records_ids));
    }

    /**
     * Add a trackvia record
     */
    public function addTVRecords($record_ids) {

        //Get the record data
        //$records = $this->Trackvia_model->getRecords($record_ids);

        //Track via records
        //$data = array(
        //    '' => $record[]
        //);

        $table_id = '';
        $data = array();

        //Update the record
        $this->tv->addRecord($table_id,$data);

    }

    /**
     * Update a trackvia record
     */
    public function updateTVRecord($record_id) {

        //Get the record data
        $record = $this->Trackvia_model->getRecord($record_id);

        //Track via records
        //$data = array(
        //    '' => $record[]
        //);

        $data = array();

        //Update the record
        $this->tv->updateRecord($record['customer_ref'],$data);

    }




}
