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

define('PRIVATE_ALL_RECORDS', '3000718983');
define('PRIVATE_INFORM_INELIGIBLE', '3000719207');
define('PRIVATE_BOOK_SURVEY', '3000719204');
define('PRIVATE_REBOOK', '3000719206');
define('PRIVATE_SURVEY_SLOTS', '3000719481');


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trackvia extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Form_model');
        $this->load->model('Trackvia_model');
 		$this->load->model('Locations_model');
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
        echo "\nChecking the SOUTHWAY_BOOK_SURVEY(".SOUTHWAY_BOOK_SURVEY.") view";
        $this->checkView(
            SOUTHWAY_BOOK_SURVEY,
            array(
                'campaign_id' => 22,
                'urgent' => NULL,
                'status' => 1,
                'outcome_id' => NULL,
                'appointment_creation' => false,
				'appointment_cancelled' => false,
            )
        );

        //Rebook View
        echo "\nChecking the SOUTHWAY_REBOOK(".SOUTHWAY_REBOOK.") view";
        $this->checkView(
            SOUTHWAY_REBOOK,
            array(
                'campaign_id' => 22,
                'urgent' => 1,
                'status' => 1,
                'outcome_id' => NULL,
                'appointment_creation' => true,
				'appointment_cancelled' => true,
            )
        );

        //Survey Slots View
        echo "\nChecking the SOUTHWAY_SURVEY_SLOTS(".SOUTHWAY_SURVEY_SLOTS.") view";
        $this->checkView(
            SOUTHWAY_SURVEY_SLOTS,
            array(
                'campaign_id' => 22,
                'urgent' => NULL,
                'status' => 4,
                'outcome_id' => 72,
                'appointment_creation' => true,
				'appointment_cancelled' => false
            )
        );
			
//
//        //PRIVATE TABLE
//
        //Private Residential View
        $this->checkView(
            PRIVATE_BOOK_SURVEY,
           array(
               'campaign_id' => 29,
                'urgent' => NULL,
                'status' => 1,
                'outcome_id' => NULL,
              'appointment_creation' => false,
			  'appointment_cancelled' => false

            )
       );
	   
	    //Private Residential View
        $this->checkView(
            PRIVATE_REBOOK,
           array(
               'campaign_id' => 29,
                'urgent' => 1,
                'status' => 1,
                'outcome_id' => NULL,
              'appointment_creation' => true,
			  'appointment_cancelled' => true
            )
       );
	   
	    //Private Residential View
        $this->checkView(
            PRIVATE_SURVEY_SLOTS,
           array(
               'campaign_id' => 29,
                'urgent' => NULL,
                'status' => 4,
                'outcome_id' => 72,
              'appointment_creation' => true,
			   'appointment_cancelled' => false
            )
       );

//        //Private Ineligible View/      
		  $this->checkView(
            PRIVATE_INFORM_INELIGIBLE,
            array(
                'campaign_id' => 28,
                'urgent' => NULL,
                'status' => 1,
                'outcome_id' => NULL,
                'appointment_creation' => false
           )
       );

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
		$appointment_cancelled = $options['appointment_cancelled'];

        //Get the trackvia records for this view
        $view = $this->tv->getView($view_id);
	
        $tv_records = $view['records'];
		$this->firephp->log($tv_records);
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
            if (($record['campaign_id'] != $campaign_id) || ($record['parked_code'] == 2 || $record['urgent'] <> 1)) {
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
                    $this->addUpdateAppointment($fields, $record, $appointment_cancelled);
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

           //TODO Add new records with insert batch
            $this->firephp->log("Create new records");
			$this->firephp->log($tv_records);

    }

    /**
     * Add/Update appointment in our system
     */
    public function addUpdateAppointment($fields, $record, $appointment_cancelled = false) {
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
			$this->firephp->log("Creating appointment");
			$this->firephp->log($record);
			$this->Trackvia_model->create_appointment($fields,$record,$planned_survey_datetime);
			$this->Locations_model->set_location_id($fields['PostCode']);
        }
		if($appointment_cancelled){
			$this->firephp->log("Cancelling appointment that needs rebooking");
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
	 
	 public function test_update(){
		 //2nd june 2015 eve
		 $data = array(
		"Planned Survey Date"=>"2015-06-04T12:00:00-0600",
		"Survey appt" => "pm",
		"Survey Booking Confirmed" => "Y",
		"Survey booked by" => "121",
		"Survey Appointment Comments" => "These are test comments"
	 );
		 $this->tv->updateRecord('3062956934',$data);

	 }
	 
    public function add_appointment() {
		
		$urn = $this->input->post('urn');
        //Get the record data
        $app = $this->Trackvia_model->get_appointment($urn);

        $data = array(
		"Planned Survey Date"=>$app['date']."T12:00:00-0600",
		"Survey appt" => $app['slot'],
		"Survey Booking Confirmed" => "Y",
		"Survey booked by" => "121",
		"Survey Appointment Comments" => $app['title'].' : '.$app['text']
			 );

        //Update the record
        $response = $this->tv->updateRecord($app['client_ref'],$data);
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$app['client_ref']));

    }

	public function no_contact(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $app = $this->Trackvia_model->get_record($urn);
		$data = array("Customer not contactable" => "Customer not contactable");
	
		$response = $this->tv->updateRecord($record['client_ref'],$data);
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref']));
	}
	
		public function not_interested(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("Customer Refused"=>"Y","Refusal Reason" => $record['outcome_reason']);
	
		$response = $this->tv->updateRecord($record['client_ref'],$data);
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref']));
	}

		public function reject_notification(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("Date Owner / Tenant Informed of Rejection"=>date('Y-m-d')."T12:00:00-0600",
		"Owner / Tenant Informed of Rejection" => "Y");
	
		$response = $this->tv->updateRecord($record['client_ref'],$data);
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref']));
	}
	
		public function data_captured(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("Owner / Rented"=>$record['a2'],
		"Is the property mortgaged" => $record['a6'],
		"Who is the Mortgage provider" => $record['a7'],
		"Owner / Tenant Name 1" => $record['contact'],
		"Is ownership in Joint Names" => $record['a4'],
		"Owner / Tenant Name 2" => $record['a5'],
		"Primary Contact (Landline)" => $record['telephone_number'],
		"Primary Contact (Mobile)" => $record['mobile_number'],
		"Email address" => $record['email'],
		"House No." => $record['house_number'],
		"Address 1" => $record['address_1'],
		"Address 2" => $record['address_2'],
		"City" => $record['city'],
		"PostCode" => $record['PostCode'],
		"Enquiry Type" => $record['city'],
		"Date of Enquiry" => $record['date_added'],
		"Where did you hear about us" => $record['a8'],
		"Asset Type" => $record['a1'],
		);
	
		$response = $this->tv->updateRecord($record['client_ref'],$data);
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref']));
	}

}
