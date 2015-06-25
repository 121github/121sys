<?php

require_once(APPPATH.'libraries/trackvia_api/Api.php');
require_once(APPPATH.'libraries/trackvia_api/Log.php');

use Trackvia\Api;
use Trackvia\Log;

define('CLIENT_ID', '252_50je1r05y5wc8w4kcws04g08scoo0o8k4kwwok0wgkkw8ggw4w');
define('CLIENT_SECRET', 'qhhgy6bbdc0w8gc0kc0kc0k88gw0ko0oskocock0wc8gw48w8');
define('USERNAME', 'ghsAPI');
define('PASSWORD', 'global123');

/*
//Test tables
define('SOUTHWAY_ALL_RECORDS', '3000718568');
define('SOUTHWAY_BOOK_SURVEY', '3000718751');
define('SOUTHWAY_REBOOK', '3000718753');
define('SOUTHWAY_SURVEY_SLOTS', '3000718736');

define('PRIVATE_ALL_RECORDS', '3000718983');
define('PRIVATE_BOOK_SURVEY', '3000719204');
define('PRIVATE_INFORM_INELIGIBLE', '3000719207');
define('PRIVATE_REBOOK', '3000719206');
define('PRIVATE_SURVEY_SLOTS', '3000719481');
*/
//Live tables
define('SOUTHWAY_ALL_RECORDS', '3000719193');
define('SOUTHWAY_BOOK_SURVEY', '3000719114');
define('SOUTHWAY_REBOOK', '3000719115');
define('SOUTHWAY_SURVEY_SLOTS', '3000719175');

define('PRIVATE_ALL_RECORDS', '3000719185');
define('PRIVATE_BOOK_SURVEY', '3000718982');
define('PRIVATE_INFORM_INELIGIBLE', '3000718985');
define('PRIVATE_REBOOK', '3000718984');
define('PRIVATE_SURVEY_SLOTS', '3000719187');

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
		
		$this->tv_tables = array(
		"GHS Southway"=>3000283398,
		"GHS Private"=>3000283421);
    }

	public function get_rebookings(){
		if(@$_POST['campaign']){
			$campaign = $_POST['campaign'];
		} else {
		$campaign = "";	
		}
	$result = $this->Trackvia_model->get_rebookings($campaign);
		echo json_encode(array("success"=>true,"data"=>$result));
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
				'record_color'=>' 	0066FF',
				'source_id' => 34
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
				'record_color'=>'0066FF',
				'source_id' => 35
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
				'appointment_cancelled' => false,
				'record_color'=>'00CC00',
				'source_id' => 37
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
			  'appointment_cancelled' => false,
			  'record_color'=>'0066FF',
				'source_id' => 39

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
			  'appointment_cancelled' => true,
			  'record_color'=>'6600FF',
				'source_id' => 38
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
			  'appointment_cancelled' => false,
			  'record_color'=>'00CC00',
				'source_id' => 36
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
                'appointment_creation' => false,
				'appointment_cancelled' => false,
			 	'record_color'=>'990000',
				'source_id' => 40
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
		$record_color = $options['record_color'];
		$source = $options['source_id'];
        //Get the trackvia records for this view
        $view = $this->tv->getView($view_id);
		
		if(isset($view['records'])){
        $tv_records = $view['records'];
		} else {
		$this->firephp->log($view);
		return false;		
		}
		/*
		if($view_id = 3000719175){
		$this->firephp->log($tv_records);
		return false;
		} else {
		return false;	
		}
		*/
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
			$fields = $tv_records[md5($record['client_ref'])]['fields'];			
            //If the campaign had changed or the park_code is "Not Working"
            if (($record['campaign_id'] != $campaign_id) || ($record['parked_code'] == 7 || $record['urgent'] <> 1 || $record['record_color'] <> "000000" )) {
                array_push($update_records, array(
                        'urn' => $record['urn'],
                        'campaign_id' => $campaign_id,
                        'parked_code' => NULL,
                        'urgent' => $urgent,
                        'record_status' => $status,
                        'outcome_id' => $outcome_id,
						'record_color' => $record_color
                    )
                );
				/*
				$update_extra = array("urn"=>$record['urn']);
				if(!empty($fields['No. Panels (Desktop)'])){
				$extra["n2"]=$fields['No. Panels (Desktop)'];
				}
				if(!empty($fields['GHS URN'])){
				$extra["c1"]=$fields['GHS URN'];
				}
				if(!empty($fields['Asset Type'])){
				$extra["c2"]=$fields['Asset Type'];
				}
				if(!empty($fields['Property Viable'])){
				$extra["n1"]=$fields['Property Viable'];
				}
				
				array_push($update_extra, $extra);
				$this->Trackvia_model->update_extra($update_extra);
				*/
				
                //Create appointment if it is needed
                if ($appointment_creation) {
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

			$new=array();
           //TODO Add new records with insert batch
		   if(count($tv_records)>0){
            $this->firephp->log("Creating new records #Source-ID: [$source]");
			$this->firephp->log($tv_records);
			foreach($tv_records as $record){
				//add record
				$data = array("campaign_id"=>$campaign_id,
				"date_added"=>date('Y-m-d H:i:s'),
				"record_status"=>$status,
				"record_color"=>$record_color,
				"outcome_id"=>$outcome_id,
				"urgent"=>$urgent,
				"source_id"=>$source
				);
				$urn = $this->Trackvia_model->add_record($data);
				$new[]=$urn;
				
				$data = array("urn"=>$urn,
				"client_ref"=>$record['id']
				);
				$this->Trackvia_model->add_client_refs($data);
				
				$data = array("urn"=>$urn,
				"c1"=>@!empty($record['fields']['GHS UPRN'])?$record['fields']['GHS UPRN']:NULL,
				"c2"=>@!empty($record['fields']['Asset Type'])?$record['fields']['Asset Type']:NULL,
				"c3"=>@!empty($record['fields']['Panel Location (Desktop)'])?$record['fields']['Panel Location (Desktop)']:NULL,
				"c4"=>@!empty($record['fields']['Referred by'])?$record['fields']['Referred by']:NULL,
				"n2"=>@!empty($record['fields']['No. Panels (Desktop)'])?$record['fields']['No. Panels (Desktop)']:NULL
				);
				
				$data = array("urn"=>$urn,
				"fullname"=>$record['fields']['Owner / Tenant Name 1'],
				"email"=>@$record['fields']['Email address'],
				"date_created"=>date('Y-m-d H:i:s'),
				"notes"=>"Ref# ".$record['id'],
				"primary"=>1);
				$contact = $this->Trackvia_model->add_contact($data);
				
				if(isset($record['fields']['Primary Contact (Landline)'])&&!empty($record['fields']['Primary Contact (Landline)'])){
				$data = array("contact_id"=>$contact,
				"description"=>"Landline",
				"telephone_number"=>$record['fields']['Primary Contact (Landline)']
				);
				$this->Trackvia_model->add_telephone($data);
				}
							if(isset($record['fields']['Primary Contact (Mobile)'])&&!empty($record['fields']['Primary Contact (Mobile)'])){
				$data = array("contact_id"=>$contact,
				"description"=>"Mobile",
				"telephone_number"=>$record['fields']['Primary Contact (Mobile)']
				);
				$this->Trackvia_model->add_telephone($data);
				}
					$data = array("contact_id"=>$contact,
				"add1"=>$record['fields']['House No.']." ".$record['fields']['Address 1'],
				"add2"=>$record['fields']['Address 2'],
				"add3"=>@$record['fields']['City'],
				"postcode"=>$record['fields']['PostCode'],
				"primary"=>1);
				$this->Trackvia_model->add_address($data);
				
				
			}
			$this->firephp->log($new);
		   }
			
			
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
     * Update a trackvia record
     */
	 
	 public function test_update(){
		 //2nd june 2015 eve
		 
$test = "5 oak street 8";		 
		$add1= preg_replace('/[^0-9]/','',$test);
			$this->firephp->log($add1);
			$house_number= preg_replace('/[0-9]/','',$test);
			$this->firephp->log($house_number);

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

	public function unable_to_contact(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("Customer not contactable" => "Customer not contactable");
	
		$response = $this->tv->updateRecord($record['client_ref'],$data);
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref']));
	}
	
		public function not_interested(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("Customer Cancellation"=>"declined","Customer Cancellation notes" => $record['outcome_reason'],"Cancelled by"=>"121","Date of Cancellation"=>date('Y-m-d')."T12:00:00-0600");
	
		$response = $this->tv->updateRecord($record['client_ref'],$data);
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref']));
	}

		public function notified_not_eligible(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("Date Owner / Tenant Informed of Rejection"=>date('Y-m-d')."T12:00:00-0600",
		"Owner / Tenant Informed of Rejection" => "Y");
	
		$response = $this->tv->updateRecord($record['client_ref'],$data);
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref']));
	}
	
	
			public function already_had_survey(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("External Survey Completed"=>"Y",
		"Internal Survey Completed" => "Y");
	
		$response = $this->tv->updateRecord($record['client_ref'],$data);
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref']));
	}
	
	public function review_required(){
	$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		//if the record has TV id then we can update or we need to create it
		if($record['client_ref']){
			$this->update_tv_record($urn);
		} else {
			$this->add_tv_record($urn);
		}
	
	}
	
	  /**
     * Add a trackvia record
     */
    public function add_tv_record($urn) {
		if($this->input->post('urn')){
		$urn = $this->input->post('urn');
		}
		$tv_tables = $this->tv_tables;
		$record = $this->Trackvia_model->get_record_rows($urn);

		foreach($record as $k=>$row){
			$details = $row;
			if($row['description']=="Mobile"||preg_match('/^447|^\+447^00447|^07/',$row['telephone_number'])){
				$mobile = $row['telephone_number'];
			} 
			$add1= preg_replace('/[0-9]/','',$row['add1']);
			$house_number= preg_replace('/^[0-9]/','',$row['add1']);
		}
		//table id
		$table_id = $tv_tables["GHS Private"];
		
		$details['mobile_number'] = $mobile;
		$data = array("UPRN Pre-fix"=>"PR",
		"created"=>date('Y-m-d')."T12:00:00-0600",
		"Date of Enquiry"=>date('Y-m-d')."T12:00:00-0600",
		//"Owner / Rented"=>$details['a2'],
		//"Is the property mortgaged" => $details['a6'],
		"Who is the Mortgage provider" => $details['a7'],
		"Owner / Tenant Name 1" => $details['fullname'],
		//"Is ownership in Joint Names" => $details['a4'],
		"Owner / Tenant Name 2" => $details['a5'],
		"Primary Contact (Landline)" => $details['telephone_number'],
		"Email address" => $details['email'],
		"House No." => $house_number,
		"Address 1" => $add1,
		"Address 2" => $details['add2'],
		"City" => $details['add3'],
		"PostCode" => $details['postcode'],
		//"Enquiry Type" => "Telephone Call-in",
		"Date of Enquiry" => date('Y-m-d',strtotime($details['date_added']))."T12:00:00-0600",
		//"Where did you hear about us" => "",//$details['a8'],
		"Asset Type" => $details['a1'],
		"If Other Mortgage Provider, please Input" => $details['a9']
		);
		if(isset($mobile)){
			$data["Primary Contact (Mobile)"] = $mobile;
		}
		if(!empty($details['c4'])){
		$data["Referred by"] = $details['c4'];
		}
		$this->firephp->log($data);
        //Update the record
        $response = $this->tv->addRecord($table_id,$data);
		echo $response;
    }

	
		public function update_tv_record($urn){
		if($this->input->post('urn')){
		$urn = $this->input->post('urn');
		}
		 //Get the record data
        $record = $this->Trackvia_model->get_record_rows($urn);
		foreach($record as $k=>$row){
			$details = $row;
			if($row['description']=="Mobile"||preg_match('/^447|^\+447^00447|^07|^0447/',$row['telephone_number'])){
				$mobile = $row['telephone_number'];
			}
			$add1= preg_replace('/[0-9]/','',$row['add1']);
			$house_number= preg_replace('/^[0-9]/','',$row['add1']);
		}
		$data = array(
		//"Owner / Rented"=>$details['a2'],
		//"Is the property mortgaged" => $details['a6'],
		"Who is the Mortgage provider" => $details['a7'],
		"Owner / Tenant Name 1" => $details['fullname'],
		//"Is ownership in Joint Names" => $details['a4'],
		"Owner / Tenant Name 2" => $details['a5'],
		"Primary Contact (Landline)" => $details['telephone_number'],
		"Email address" => $details['email'],
		"House No." => $house_number,
		"Address 1" => $add1,
		"Address 2" => $details['add2'],
		"City" => $details['add3'],
		"PostCode" => $details['postcode'],
		"Enquiry Type" => "Door knock",
		"Date of Enquiry" => date('Y-m-d',strtotime($details['date_added']))."T12:00:00-0600",
		//"Where did you hear about us" => "",//$details['a8'],
		"Asset Type" => $details['a1']
		);
	
		if(isset($mobile)){
			$data["Primary Contact (Mobile)"] = $mobile;
		}
		$response = $this->tv->updateRecord($details['client_ref'],$data);
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$details['client_ref'],"data"=>$data));
	}

}
