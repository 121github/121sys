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
        $this->load->model('Form_model');
        $this->load->model('Trackvia_model');

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


		
				$test = $this->tv->getViewList();
				$this->firephp->log($test);
				exit;
		
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
//                'outcome_id' => NULL,
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
//                'outcome_id' => NULL,
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
        $outcome_id = $options['outcome_id'];
        $appointment_creation = $options['appointment_creation'];
		$cancelled_appointment = isset($options['cancelled_appointment'])?true:false;

        //Get the trackvia records for this view
        $view = $this->tv->getView($view_id);
        $tv_records = $view['records'];

        //Get the locator ids (client_ref in our system
        $tv_record_ids = array();
        $aux = array();
        foreach($tv_records as $tv_record) {
            array_push($tv_record_ids,$tv_record['id']);
            $aux[md5($tv_record['id'])] = $tv_record;
        }
        $tv_records = $aux;
		

        echo "\n\t\t Total track via records in this view... ".count($tv_records);

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
                        'record_status' => $status,
                        'outcome_id' => $outcome_id
                    )
                );
                //Create appointment if it is needed
                if ($appointment_creation) {
                    $fields = $tv_records[md5($record['client_ref'])]['fields'];
                    $this->addUpdateAppointment($fields, $record, $cancelled_appointment);
                }
            }
            //Remove from the new_record_ids array the records that already exist on our system
            if (in_array($record['client_ref'],$new_records_ids)) {
				unset($tv_records[md5($record['client_ref'])]);
			}
        }


        //Update the records which campaign was changed
        echo "\n\t\t Records updated in our system... ".count($update_records)."\n";
        if (!empty($update_records)) {
            $this->Trackvia_model->updateRecords($update_records);
        }
        //Create the new records that not exist in our system yet
        $new_records = array();


		  foreach($tv_records as $tv_record) {
				$record_data = array();
				
				$record = $this->tv->getViewRecord(SOUTHWAY_ALL_RECORDS,$tv_record['id']);
				$this->firephp->log($record);
				exit;
                //build the record array from the data in the view.
				$record = array(
                    'client_ref' => $tv_record['id'],
                    'survey_date' => ""
                );

                //Add the new records to an array
                array_push($new_records,$record_data);

                //Create appointment if it is needed
                if ($appointment_creation) {
                    $fields = $tv_record['fields'];
                    $this->addUpdateAppointment($fields, $record, $cancelled_appointment);
                }
            //TODO Add new records with insert batch
            //$this->firephp->log(($new_records));
        }

    }

    /**
     * Add/Update appointment in our system
     */
    public function addUpdateAppointment($fields, $record, $cancelled_appointment = false) {
		if(isset($fields['Planned Survey Date'])){
			$sd = explode("T",$fields['Planned Survey Date']);
		}
        $planned_survey_date = (isset($fields['Planned Survey Date'])?$sd[0]:'');
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
        if ($record['survey_date']!=$planned_survey_datetime) {
            //Create a new appointment if it is needed
			$this->Trackvia_model->create_appointment($fields,$record,$planned_survey_datetime);
        }
		if($cancelled_appointment){
			$this->Trackvia_model->cancel_appointment($record['urn'],$planned_survey_datetime);	
		}
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
