<?php

require_once(APPPATH.'libraries/trackvia_api/Api.php');
require_once(APPPATH.'libraries/trackvia_api/Log.php');

use Trackvia\Api;
use Trackvia\Log;

define('CLIENT_ID', '252_50je1r05y5wc8w4kcws04g08scoo0o8k4kwwok0wgkkw8ggw4w');
define('CLIENT_SECRET', 'qhhgy6bbdc0w8gc0kc0kc0k88gw0ko0oskocock0wc8gw48w8');
define('USERNAME', 'ghsAPI');
define('PASSWORD', 'global123');


if($_SESSION['environment']=="acceptance"||$_SESSION['environment']=="test"||$_SESSION['environment']=="development"){

//Test tabless
define('SOUTHWAY_ALL_RECORDS', '3000718568');
define('SOUTHWAY_BOOK_SURVEY', '3000718751');
define('SOUTHWAY_REBOOK', '3000718753');
define('SOUTHWAY_SURVEY_SLOTS', '3000718736');

define('PRIVATE_ALL_RECORDS', '3000718983');
define('PRIVATE_BOOK_SURVEY', '3000719204');
define('PRIVATE_INFORM_INELIGIBLE', '3000719207');
define('PRIVATE_REBOOK', '3000719206');
define('PRIVATE_SURVEY_SLOTS', '3000719481');

define('PRIVATE_TABLE', '3000283421');
define('SOUTHWAY_TABLE', '3000283398');

} else if($_SESSION['environment']=="production"){
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

define('PRIVATE_TABLE', '3000282959');
define('SOUTHWAY_TABLE', '3000283129');

}

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
		"GHS Southway"=>SOUTHWAY_TABLE,
		"GHS Private"=>PRIVATE_TABLE
    );

	$this->tv_views = array(
	"GHS Southway Total"=>SOUTHWAY_ALL_RECORDS,
	"GHS Private Total"=>PRIVATE_ALL_RECORDS,
	"GHS Southway survey"=>SOUTHWAY_BOOK_SURVEY,
		"GHS Southway rebook"=>SOUTHWAY_REBOOK,
		"GHS Southway booked"=>SOUTHWAY_SURVEY_SLOTS,
		"GHS Private survey"=>PRIVATE_BOOK_SURVEY,
		"GHS Private rebook"=>PRIVATE_REBOOK,
		"GHS Private booked"=>PRIVATE_SURVEY_SLOTS,
		"GHS Private not viable"=>PRIVATE_INFORM_INELIGIBLE);
		
	$this->source_config = array(
	"GHS Southway survey"=>34,
		"GHS Southway rebook"=>35,
		"GHS Southway booked"=>37,
		"GHS Private survey"=>39,
		"GHS Private rebook"=>38,
		"GHS Private booked"=>36,
		"GHS Private not viable"=>40);	
		$this->headers = "From: noreply@121system.com" . "\r\n" .
						"CC: steve.prior@globalheatsource.com";
	}

	public function get_counts(){
		$sources =$this->source_config;
		$tables = $this->tv_views;
		$data = array();
		foreach($tables as $name => $view_id){
		if($view_id<>SOUTHWAY_ALL_RECORDS&&$view_id<>PRIVATE_ALL_RECORDS){
			$data[$name]= array("source"=>$sources[$name],"one2one"=>$this->Trackvia_model->get_121_counts($name));
		if($this->input->post('tv')){
		$view = $this->tv->getView($view_id);
		$data[$name]["trackvia"]=$view['record_count'];
		} else {
		$data[$name]["trackvia"] = false;	
		}
		
		}
		}
		echo json_encode($data);
		
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

    public function check_trackvia() {
		   //SOUTHWAY TABLE	   
   $this->db->query("update records set parked_code=2,source_id = 28 where campaign_id = 22");
        //Book View
        echo "<br>Checking the SOUTHWAY_BOOK_SURVEY(".SOUTHWAY_BOOK_SURVEY.") view";
        $this->checkView(
            SOUTHWAY_BOOK_SURVEY,
            array(
                'campaign_id' => 22,
                'urgent' => NULL,
                'status' => 1,
                'outcome_id' => 131,
                'appointment_creation' => false,
				'appointment_cancelled' => false,
				'record_color'=>'0066FF',
				'source_id' => 34,
				'savings_per_panel' => 20
            )
        );

        //Rebook View
        echo "<br>Checking the SOUTHWAY_REBOOK(".SOUTHWAY_REBOOK.") view";
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
				'source_id' => 35,
				'savings_per_panel' => 20
            )
        );

        //Survey Slots View
        echo "<br>Checking the SOUTHWAY_SURVEY_SLOTS(".SOUTHWAY_SURVEY_SLOTS.") view";
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
				'source_id' => 37,
				'savings_per_panel' => 20
            )
        );

//
//        //PRIVATE TABLE
//
 //Private Residential View
   echo "<br>Checking the PRIVATE_ALL_RECORDS(".PRIVATE_ALL_RECORDS.") view";
        $this->checkView(
            PRIVATE_ALL_RECORDS,
           array(
               'campaign_id' => 29,
                'urgent' => NULL,
                'status' => 1,
                'outcome_id' => NULL,
              'appointment_creation' => false,
			  'appointment_cancelled' => false,
			  'record_color'=>'000000',
				'source_id' => 41,
				'savings_per_panel' => 30

            )
       );

        //Private Residential View
		 echo "<br>Checking the PRIVATE_BOOK_SURVEY(".PRIVATE_BOOK_SURVEY.") view";
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
				'source_id' => 39,
				'savings_per_panel' => 30

            )
       );

	    //Private Residential View
		 echo "<br>Checking the PRIVATE_REBOOK(".PRIVATE_REBOOK.") view";
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
				'source_id' => 38,
				'savings_per_panel' => 30
            )
       );

	    //Private Residential View
				 echo "<br>Checking the PRIVATE_SURVEY_SLOTS(".PRIVATE_SURVEY_SLOTS.") view";
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
				'source_id' => 36,
				'savings_per_panel' => 30
            )
       );

//        //Private Ineligible View/    
				 echo "<br>Checking the PRIVATE_INFORM_INELIGIBLE(".PRIVATE_INFORM_INELIGIBLE.") view";  
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
				'source_id' => 40,
				'savings_per_panel' => 30
           )
       );

	   //queries we may want to run after the updates can go here
	   $this->db->query("update records set map_icon ='fa-home' where campaign_id in(22,28,29)");
$this->db->query("update contact_addresses left join contacts using(contact_id) left join records using(urn) set contact_addresses.`primary` = 1 where campaign_id in(22,28,29)");

   $this->db->query("update contacts inner join records using(urn) inner join data_sources using(source_id) set notes = source_name where campaign_id in(22,28,29) and records.source_id is not null");
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
		$savings = $options['savings_per_panel'];
        //Get the trackvia records for this view
        $view = $this->tv->getView($view_id);


		if(isset($view['records'])){
        $tv_records = $view['records'];
		$this->firephp->log($view_id);
		$this->firephp->log($view);

		} else {
		$this->firephp->log($view_id);
		$this->firephp->log($view);
		return false;
		}



        //Get the locator ids (client_ref in our system
        $tv_record_ids = array();
        $aux = array();
        foreach($tv_records as $tv_record) {
            array_push($tv_record_ids,$tv_record['id']);
            $aux[md5($tv_record['id'])] = $tv_record;
        }
        $tv_records = $aux;

        echo "<br>Total track via records in this view... ".count($tv_records);
        //Get the records to be updated in our system
        $records = $this->Trackvia_model->getRecordsByTVIds($tv_record_ids);
		 echo "<br>Matching records found in the calling system... ".count($records);
        //Update the record campaign if it is needed (different campaign) and create a new one if it does not exist yet
        $update_records = array();
		$update_extra = array();
        $new_records_ids = $tv_record_ids;
		$records_found=0;
		$appointments_cancelled_count = 0;
		$appointments_created_count = 0;
        foreach($records as $record) {		
			$fields = $tv_records[md5($record['client_ref'])]['fields'];
            //If the campaign had changed or the park_code is "Not Working"

            if ($record['campaign_id'] != $campaign_id || $record['parked_code'] == 7 ||$record['parked_code'] == 2 || $record['record_status'] != $status || $record['record_color'] != $record_color || $record['source_id'] != $source ) {


				//organising the record update data
                array_push($update_records, array(
                        'urn' => $record['urn'],
                        'campaign_id' => $campaign_id,
                        'parked_code' => NULL,
                        'urgent' => $urgent,
                        'record_status' => $status,
                        'outcome_id' => $outcome_id,
						'record_color' => $record_color,
						'source_id' => $source
                    )
                );

			}
				//Create appointment if it is needed
                if ($appointment_creation) {
					if($appointment_cancelled){
					$appointments_cancelled_count++;	
					} else {
					$appointments_created_count++;
					}
                    $this->addUpdateAppointment($fields, $record, $appointment_cancelled);
                }

				//organise the record_details update
				$extra = array();
				if(!empty($fields['No. Panels (Desktop)'])){
				$extra["n1"]=$fields['No. Panels (Desktop)'];
				$extra["n2"]=$fields['No. Panels (Desktop)']*$savings;
				}
				if(!empty($fields['GHS UPRN'])){
				$extra["c1"]=$fields['GHS UPRN'];
				//echo $fields['GHS UPRN']."<br>";
				}
				if(!empty($fields['Referred by'])){
				$extra["c2"]=$fields['Referred by'];
				} else {
				$extra["c2"] = NULL;
				}
				if(!empty($fields['Enquiry type'])){
				$extra["c3"]=$fields['Enquiry type'];
				echo $fields['Enquiry type']."<br>";
				} else {
				$extra["c3"] = NULL;
				}
				if(!empty($fields['Property Viable'])){
				$extra["c5"]=$fields['Property Viable'];
				} else {
				$extra["c5"] = NULL;
				}
				if(!empty($fields['Reason for Desktop Fail'])){
				$extra["c6"]=$fields['Reason for Desktop Fail'];
				} else {
				$extra["c6"] = NULL;
				}
				if(!empty($extra)){
				$extra['urn'] = $record['urn'];
				array_push($update_extra, $extra);
				}
            //Remove from the new_record_ids array the records that already exist on our system
            if (in_array($record['client_ref'],$new_records_ids)) {
				unset($tv_records[md5($record['client_ref'])]);
			}
        }

        //Update the records which campaign was changed
        echo "\n<br>Records updated in our system... ".count($update_records)."\n";
		
        if (!empty($update_records)) {
            $this->Trackvia_model->updateRecords($update_records);
        }
		//update the record details
		if(!empty($extra)){
			$this->firephp->log("Updating Details");
			$this->firephp->log($update_extra);
			$this->Trackvia_model->update_extra($update_extra);
		}
		
		echo "\n<br>Records left to create in our system... ".count($tv_records)."\n";
			$new=array();
           //Add new records if there are any left in the $tv_records array
		   if(count($tv_records)>0){
            $this->firephp->log("Creating new records #Source-ID: [$source]");
			$this->firephp->log($tv_records);
			foreach($tv_records as $record){
				//organise the new record data
				$data = array("campaign_id"=>$campaign_id,
				"date_added"=>date('Y-m-d H:i:s'),
				"record_status"=>$status,
				"record_color"=>$record_color,
				"outcome_id"=>$outcome_id,
				"urgent"=>$urgent,
				"source_id"=>$source,
				"parked_code"=>2
				);
				$urn = $this->Trackvia_model->add_record($data);
				//catch the newly created urns
				$new[]=$urn;
				//prepare the new client refs
				$data = array("urn"=>$urn,
				"client_ref"=>$record['id']
				);
				//insert the client refs
				$this->Trackvia_model->add_client_refs($data);
				//prepare the record_details
				$data=array();
				if(!empty($fields['GHS UPRN'])){
				$data["c1"]=$fields['GHS UPRN'];
				}
				if(!empty($fields['Referred by'])){
				$data["c2"]=$fields['Referred by'];
				}
				if(!empty($fields['Enquiry type'])){
				$data["c3"]=$fields['Enquiry type'];
				}
				if(!empty($fields['Property Viable'])){
				$data["c5"]=$fields['Property Viable'];
				}
				if(!empty($fields['Reason for Desktop Fail'])){
				$data["c6"]=$fields['Reason for Desktop Fail'];
				}
				if(!empty($data)){
				$data["urn"]=$urn;
				$this->Trackvia_model->add_record_details($data);
				}
				//prepare any new contacts
				$data = array("urn"=>$urn,
				"fullname"=>isset($record['fields']['Owner / Tenant Name 1'])?$record['fields']['Owner / Tenant Name 1']:'',
				"email"=>isset($record['fields']['Email address'])?$record['fields']['Email address']:NULL,
				"date_created"=>date('Y-m-d H:i:s'),
				"notes"=>"Ref# ".$record['id'],
				"primary"=>1);
				$contact = $this->Trackvia_model->add_contact($data);
				//prepare any new telephone numbers
				if(isset($record['fields']['Primary Contact (Landline)'])&&!empty($record['fields']['Primary Contact (Landline)'])){
				$data = array("contact_id"=>$contact,
				"description"=>"Landline",
				"telephone_number"=>$record['fields']['Primary Contact (Landline)']
				);
				$this->Trackvia_model->add_telephone($data);
				}
				//prepare any new mobile telephone numbers
				if(isset($record['fields']['Primary Contact (Mobile)'])&&!empty($record['fields']['Primary Contact (Mobile)'])){
				$data = array("contact_id"=>$contact,
				"description"=>"Mobile",
				"telephone_number"=>$record['fields']['Primary Contact (Mobile)']
				);
				$this->Trackvia_model->add_telephone($data);
				}
				//add transfer
				$data = array("contact_id"=>$contact,
				"description"=>"Transfer to GHS",
				"telephone_number"=>"01228819810"
				);
				$this->Trackvia_model->add_telephone($data);
				//prepare any new telephone addresses
				$data = array("contact_id"=>$contact,
				"add1"=>$record['fields']['House No.']." ".$record['fields']['Address 1'],
				"add2"=>$record['fields']['Address 2'],
				"add3"=>@$record['fields']['City'],
				"postcode"=>$record['fields']['PostCode'],
				"primary"=>1);
				$this->Trackvia_model->add_address($data);


			}
			//show the new urns
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
                $planned_survey_time = "13:00:00";
                break;
            case "eve":
                $planned_survey_time = "18:00:00";
                break;
            default:
                $planned_survey_time = "09:00:00";
                break;
        }

        $planned_survey_datetime = $planned_survey_date." ".$planned_survey_time;
		
        //TODO Add appointment if the survey_date is different in both systems
        if ($record['survey_date']!=$planned_survey_date) {
            //Create a new appointment if it is needed
			$created_appointments++;
			$this->Trackvia_model->create_appointment($fields,$record,$planned_survey_datetime);
			$this->Locations_model->set_location_id($fields['PostCode']);
        }
		if($appointment_cancelled){
			$this->firephp->log("Cancelling appointment that needs rebooking:". $record['urn']);
			$this->Trackvia_model->cancel_appointment($record['urn'],$planned_survey_date);
		}
    }




    public function add_appointment() {

		$urn = $this->input->post('urn');
		
        //Get the record data
        $app = $this->Trackvia_model->get_appointment($urn);
		if($app['campaign_id']=="29"){
		//if it doesnt exist on trackvia we should add it first
		if(empty($app['client_ref'])){
			$response = $this->add_tv_record($urn);
			$app['client_ref'] = $response['records'][0]['id'];
		} else {
			$this->update_tv_record($urn);	
		}
		}
        $data = array(
		"Planned Survey Date"=>$app['date']."T12:00:00-0600",
		"Survey appt" => $app['slot'],
		"Survey Booking Confirmed" => "Y",
		"Survey booked by" => "121",
		"Survey Appointment Comments" => $app['title'].' : '.$app['text']
			 );
		
		if($app['campaign_id']==29){
			$data["Owner Consent to proceed"]="Y";
		}
        //Update the record
        $response = $this->tv->updateRecord($app['client_ref'],$data);
		if(!empty($response)){
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$app['client_ref'],"data"=>$data));
		$this->db->query("update records set urgent=null where urn = '$urn'");
		} else {
			$message = "An error occured while saving an appointment \r\n	";
			$message .= "  URN: $urn \r\n";
			$message .= "Record ID: ". $app['client_ref']." \r\n	";
			$message .= "Sent Data \r\n	";
			foreach($data as $k=>$v){
			$message .= "$k: $v \r\n	";
			}
			mail("bradf@121customerinsight.co.uk","Trackvia Update Error",$message,$this->headers );
		}

    }

	public function unable_to_contact(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("Customer not contactable" => "Customer not contactable");

		$response = $this->tv->updateRecord($record['client_ref'],$data);
		if(!empty($response)){
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref']));
		} else {
			$message = " An error occured while updating a record \r\n";
			$message .= "  URN: $urn \r\n";
			$message .= " Record ID: ". $record['client_ref']." \r\n";
			$message .= " Sent Data \r\n";
			foreach($data as $k=>$v){
			$message .= " $k: $v \r\n";
			}
			mail("bradf@121customerinsight.co.uk","Trackvia Update Error",$message,$this->headers );
		}
	}

		public function survey_refused(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("Planned Survey Date"=>"","Survey appt"=>"","Survey Booking Confirmed"=>"","Survey booked by"=>"","Survey Appointment Comments"=>"","Customer Cancellation"=>"declined","Customer Cancellation notes" => $record['outcome_reason'],"Cancelled by"=>"121","Date of Cancellation"=>date('Y-m-d')."T12:00:00-0600");
		
		if($record['campaign_id']=="29"){
		$data["Owner Consent to proceed"]="N";
		$data["Date Tenant Notified"]="today";	
		}

		$response = $this->tv->updateRecord($record['client_ref'],$data);
		if(!empty($response)){
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref'],"data"=>$data));
		} else {
			$message = " An error occured while updating a record \r\n";
			$message .= "  URN: $urn \r\n";
			$message .= " Record ID: ". $record['client_ref']." \r\n";
			$message .= " Sent Data \r\n";
			foreach($data as $k=>$v){
			$message .= " $k: $v \r\n";
			}
			mail("bradf@121customerinsight.co.uk","Trackvia Update Error",$message,$this->headers );
		}
	}

	//the fields we update here need confirming, there doesnt appear to be any cancel install fields for us.
	public function install_refused(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("Planned Installation date"=>"","Installation Date Confirmed"=>"","Customer Cancellation"=>"declined","Customer Cancellation notes" => $record['outcome_reason'],"Cancelled by"=>"121","Date of Cancellation"=>date('Y-m-d')."T12:00:00-0600");
		if($record['campaign_id']=="29"){
		$data["Owner Consent to proceed"]="N";
		$data["Date Tenant Notified"]="today";	
		}
		$response = $this->tv->updateRecord($record['client_ref'],$data);
		if(!empty($response)){
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref'],"data"=>$data));
		} else {
			$message = " An error occured while updating a record \r\n";
			$message .= "  URN: $urn \r\n";
			$message .= " Record ID: ". $record['client_ref']." \r\n";
			$message .= " Sent Data \r\n";
			foreach($data as $k=>$v){
			$message .= " $k: $v \r\n";
			}
			mail("bradf@121customerinsight.co.uk","Trackvia Update Error",$message,$this->headers );
		}
	}

		public function notified_not_eligible(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("Date Owner / Tenant Informed of Rejection"=>date('Y-m-d')."T12:00:00-0600",
		"Owner / Tenant Informed of Rejection" => "Y");

		$response = $this->tv->updateRecord($record['client_ref'],$data);
		if(!empty($response)){
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref'],"data"=>$data));
		} else {
				$message = " An error occured while updating a record \r\n";
				$message .= "  URN: $urn \r\n";
			$message .= " Record ID: ". $record['client_ref']." \r\n";
			$message .= " Sent Data \r\n";
			foreach($data as $k=>$v){
			$message .= " $k: $v \r\n";
			}
			mail("bradf@121customerinsight.co.uk","Trackvia Update Error",$message,$this->headers );
		}
	}


			public function already_had_survey(){
		$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		$data = array("External Survey Completed"=>"Y",
		"Internal Survey Completed" => "Y");

		$response = $this->tv->updateRecord($record['client_ref'],$data);
		if(!empty($response)){
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$record['client_ref'],"data"=>$data));
		} else {
			$message = " An error occured while updating a record \r\n";
			$message .= "  URN: $urn \r\n";
			$message .= " Record ID: ". $record['client_ref']." \r\n";
			$message .= " Sent Data \r\n";
			foreach($data as $k=>$v){
			$message .= "$k: $v\r\n";
			}
			mail("bradf@121customerinsight.co.uk","Trackvia Update Error",$message,$this->headers );
		}
	}

	public function get_urn_from_ghs(){
		$this->db->where(array("c1"=>$this->input->post('ghsurn')));
		echo trim($this->db->get("record_details")->row()->urn);
	}

	public function review_required(){
	$urn = $this->input->post('urn');
		 //Get the record data
        $record = $this->Trackvia_model->get_record($urn);
		//if the record has TV id then we can update or we need to create it
		if(isset($record['client_ref'])){
			$this->firephp->log("update tv record");
			$this->update_tv_record($urn);
		} else {
			$this->firephp->log("creating tv record");
			$this->add_tv_record($urn);
		}

	}

	public function test_update(){
	$response = $this->tv->updateRecord(false,false);
	}

	  /**
     * Add a trackvia record
     */
    public function add_tv_record($urn) {
		if($this->input->post('urn')){
		$urn = $this->input->post('urn');
		}
		$tv_tables = $this->tv_tables;
		$tv_table = $tv_tables['GHS Private'];
		$data = $this->get_record_array($urn);
		unset($data['client_ref']);
		$this->firephp->log($data);
		$this->firephp->log($tv_table);


        //Update the record
        $response = $this->tv->addRecord($tv_table,$data);
		$this->firephp->log($response);
		if(!empty($response)){
			$new_client_ref = $response['records'][0]['id'];
			$data = array("urn"=>$urn,
				"client_ref"=>$new_client_ref
				);
				$this->Trackvia_model->add_client_refs($data);
			echo json_encode(array("success"=>true,"msg"=>$response));
		} else {
			$message = "  An error occured when adding a new trackvia record \r\n";
			$message .= "  URN: $urn \r\n";
			$message .= "  Table ID: ". $tv_table." \r\n";
			$message .= "  Sent Data \r\n";
			foreach($data as $k=>$v){
			$message .= "  $k: $v \r\n";
			}
			mail("bradf@121customerinsight.co.uk","Trackvia Update Error",$message,$this->headers );
		echo json_encode(array("success"=>true,"msg"=>$response['messsage']));
		}
    }


		public function update_tv_record($urn){
		if($this->input->post('urn')){
		$urn = $this->input->post('urn');
		}
		 //Get the record data
		$data = $this->get_record_array($urn);
		$client_ref=$data['client_ref'];
		unset($data['client_ref']);
		$response = $this->tv->updateRecord($client_ref,$data);
		if(!empty($response)){
		echo json_encode(array("success"=>true,"response"=>$response,"ref"=>$client_ref,"data"=>$data));
		} else {
			$message = "  An error occured when updating a trackvia record  \r\n";
			$message .= "  URN: $urn \r\n";
			$message .= "  Client ref: ". $client_ref."  \r\n";
			$message .= "  Sent Data  \r\n";
			foreach($data as $k=>$v){
			$message .= "  $k: $v  \r\n";
			}
			mail("bradf@121customerinsight.co.uk","Trackvia Update Error",$message,$this->headers );
		echo json_encode(array("success"=>true,"msg"=>$response['messsage']));
		}
	}

	public function get_record_array($urn){
			$record = $this->Trackvia_model->get_record_rows($urn);
			$mobile ="";
		foreach($record as $k=>$row){
			$details = $row;
			if($row['description']=="Mobile"||preg_match('/^447|^\+447^00447|^07/',$row['telephone_number'])){
				$mobile = $row['telephone_number'];
			}
			$add1= trim(preg_replace('/[0-9]/','',$row['add1']));
			$house_number= trim(preg_replace('/[a-zA-Z]/','',$row['add1']));
		}
		$data = array("UPRN Pre-fix"=>"PR",
		"Date of Enquiry"=>date('Y-m-d')."T12:00:00-0600");
		$data['client_ref'] = $details['client_ref'];
		if(!empty($details['a2'])){
		$data["Owner / Rented"]=$details['a2'];
		}
		if(!empty($details['a6'])){
		$data["Is the property mortgaged"]=$details['a6'];
		}
		if(!empty($details['a7'])){
		$data["Who is the Mortgage provider"]=$details['a7'];
		}
		if(!empty($details['fullname'])){
		$data["Owner / Tenant Name 1"]=$details['fullname'];
		}
		if(!empty($details['a4'])){
		$data["Is ownership in Joint Names"]=$details['a4'];
		}
		if(!empty($details['a5'])){
		$data["Owner / Tenant Name 2"]=$details['a5'];
		}
		if(!empty($details['telephone_number'])){
		$data["Primary Contact (Landline)"]=$details['telephone_number'];
		}
		if(!empty($details['email'])){
		$data["Email address"]=$details['email'];
		}
		if(!empty($house_number)){
		$data["House No."]=$house_number;
		}
		if(!empty($add1)){
		$data["Address 1"]=$add1;
		}
		if(!empty($details['add2'])){
		$data["Address 2"]=$details['add2'];
		}
		if(!empty($details['add3'])){
		$data["City"]=$details['add3'];
		}
		if(!empty($details['postcode'])){
		$data["PostCode"]=$details['postcode'];
		}
		if(!empty($details["c3"])){
		$data["Enquiry Type"]=$details["c3"];
		}
		if(!empty($details['date_added'])){
		$data["Date of Enquiry"]=date('Y-m-d',strtotime($details['date_added']))."T12:00:00-0600";
		}
		if(!empty($details['a8'])){
		$data["Where did you hear about us"]=$details['a8'];
		}
		if(!empty($details['a1'])){
		$data["Asset Type"]=$details['a1'];
		}
		if(!empty($details['a9'])){
		$data["If Other Mortgage Provider, please Input"]=$details['a9'];
		}
		if(!empty($mobile)){
		$data["Primary Contact (Mobile)"] = $mobile;
		}
		if(!empty($details['c2'])){
		$data["Referred by"] = $details['c2'];
		}

		return $data;

	}


	public function find_dupes(){
		$table=$this->uri->segment(3);
			$field1=$this->uri->segment(4);
			$field2=$this->uri->segment(5);
			$field3=$this->uri->segment(6);
			$concat=array();
			if(!empty($field1)){
			$concat[]=$field1;
			}
			if(!empty($field2)){
			$concat[]=$field2;
			}
			if(!empty($field3)){
			$concat[]=$field3;
			}


			$fields = implode(",",$concat);
			$query = "SELECT urn, concat( $fields ) ref , count( * ) count
FROM `$table` left join contacts using(contact_id) left join records using(urn) where campaign_id in(22,28,29)
GROUP BY concat( $fields )
HAVING count( concat( $fields ) ) >1";
$result = $this->db->query($query)->result_array();
foreach($result as $row){
	echo $row['urn'];
	echo "<br>";
$remove = $row['count']-1;
echo $delete = "delete from $table where concat($fields) = '".addslashes($row['ref'])."' and urn in(select urn from client_refs where client_ref is null) limit $remove";
echo ";<br>";
}
	}



}
