<?php

require_once(APPPATH . 'libraries/trackvia_api/Api.php');
require_once(APPPATH . 'libraries/trackvia_api/Log.php');

use Trackvia\Api;
use Trackvia\Log;

define('CLIENT_ID', '252_50je1r05y5wc8w4kcws04g08scoo0o8k4kwwok0wgkkw8ggw4w');
define('CLIENT_SECRET', 'qhhgy6bbdc0w8gc0kc0kc0k88gw0ko0oskocock0wc8gw48w8');
define('USERNAME', 'ghsAPI');
define('PASSWORD', 'global123');

define('DARLINGTON_ALL_RECORDS', '3000735550');
define('DARLINGTON_BOOK_SURVEY', '3000735546');
define('DARLINGTON_REBOOK', '3000735547');
define('DARLINGTON_SURVEY_SLOTS', '3000735549');
define('DARLINGTON_BOOK_INSTALLATION', '3000735601');
define('DARLINGTON_INSTALLATION_SLOTS', '3000735622');


define('SOUTHWAY_ALL_RECORDS', '3000719193');
define('SOUTHWAY_BOOK_SURVEY', '3000719114');
define('SOUTHWAY_REBOOK', '3000719115');
define('SOUTHWAY_SURVEY_SLOTS', '3000719175');
define('SOUTHWAY_BOOK_INSTALLATION', '3000724696');
define('SOUTHWAY_INSTALLATION_SLOTS', '3000723374');

define('PRIVATE_ALL_RECORDS', '3000719185');
define('PRIVATE_BOOK_SURVEY', '3000718982');
define('PRIVATE_INFORM_INELIGIBLE', '3000718985');
define('PRIVATE_REBOOK', '3000718984');
define('PRIVATE_SURVEY_SLOTS', '3000719187');

define('CITYWEST_ALL_RECORDS', '3000725653');
define('CITYWEST_BOOK_SURVEY', '3000725763');
//define('CITYWEST_INFORM_INELIGIBLE', '3000718985');
define('CITYWEST_REBOOK', '3000725650');
define('CITYWEST_SURVEY_SLOTS', '3000725652');


if ($_SESSION['environment'] == "acceptance" || $_SESSION['environment'] == "test" || $_SESSION['environment'] == "development") {
define('DARLINGTON_TABLE', '3000283398');
define('PRIVATE_TABLE', '3000283421');
define('SOUTHWAY_TABLE', '3000283398');
define('CITYWEST_TABLE', '3000283398');

} else if ($_SESSION['environment'] == "production") {
//Live tables
 
  define('DARLINGTON_TABLE', '3000284891');
    define('PRIVATE_TABLE', '3000282959');
    define('SOUTHWAY_TABLE', '3000283129');
    define('CITYWEST_TABLE', '3000284157');

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
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'username' => USERNAME,
            'password' => PASSWORD
        ));

        $this->tv_tables = array(
            "GHS Southway" => SOUTHWAY_TABLE,
            "GHS Private" => PRIVATE_TABLE,
			"GHS Citywest" => CITYWEST_TABLE,
			"GHS Darlington" => DARLINGTON_TABLE
        );

		$this->tv_sources = array("GHS Bought Data"=>"CC-121Set1-OB","GHS Ginger Peterborough"=>"CC-Ginger1PB-OB","GHS Ginger Manchester"=>"CC-Ginger1MA-OB");

        $this->tv_views = array(
            "GHS Southway Total" => SOUTHWAY_ALL_RECORDS,
            "GHS Citywest Total" => CITYWEST_ALL_RECORDS,
            "GHS Private Total" => PRIVATE_ALL_RECORDS,
            "GHS Southway survey" => SOUTHWAY_BOOK_SURVEY,
            "GHS Southway rebook" => SOUTHWAY_REBOOK,
            "GHS Southway booked" => SOUTHWAY_SURVEY_SLOTS,
            "GHS Southway installation" => SOUTHWAY_BOOK_INSTALLATION,
            "GHS Southway installation booked" => SOUTHWAY_INSTALLATION_SLOTS,
			            "GHS Darlington survey" => DARLINGTON_BOOK_SURVEY,
            "GHS Darlington rebook" => DARLINGTON_REBOOK,
            "GHS Darlington booked" => DARLINGTON_SURVEY_SLOTS,
            "GHS Darlington installation" => DARLINGTON_BOOK_INSTALLATION,
            "GHS Darlington installation booked" => DARLINGTON_INSTALLATION_SLOTS,
            "GHS Citywest survey" => CITYWEST_BOOK_SURVEY,
            "GHS Citywest rebook" => CITYWEST_REBOOK,
            "GHS Citywest booked" => CITYWEST_SURVEY_SLOTS,
            "GHS Private survey" => PRIVATE_BOOK_SURVEY,
            "GHS Private rebook" => PRIVATE_REBOOK,
            "GHS Private booked" => PRIVATE_SURVEY_SLOTS,
            "GHS Private" => "",
			 "GHS Southway" => "",
			  "GHS Citywest" => "",
			    "GHS New Leads" => "");
		
        $this->pot_config = array(
            "GHS Southway survey" => 34,
            "GHS Southway rebook" => 35,
            "GHS Southway booked" => 37,
            "GHS Southway installation" => 51,
            "GHS Southway installation booked" => 52,
			"GHS Darlington survey" => 58,
            "GHS Darlington rebook" => 60,
            "GHS Darlington booked" => 59,
            "GHS Darlington installation" => 61,
            "GHS Darlington installation booked" => 62,
            "GHS Private survey" => 39,
            "GHS Private rebook" => 38,
            "GHS Private booked" => 36,
            "GHS Private not viable" => 40,
            "GHS Citywest survey" => 46,
            "GHS Citywest rebook" => 47,
            "GHS Citywest booked" => 48,
			"GHS Citywest" => 49,
            "GHS Southway" => 28,
            "GHS Private" => 41,
			"GHS New Leads" => 55);
        $this->headers = "From: noreply@121system.com" . "\r\n" .
            "CC: steve.prior@globalheatsource.com";
    }

    public function check_voicemail()
    {
        $urn = $this->input->post('urn');
        $qry = "select urn from history join outcomes using(outcome_id) where delay_hours > 0 and urn = $urn";
        if ($this->db->query($qry)->num_rows() == 4) {
            echo json_encode(array("success" => true));
        }

    }

    public function get_counts()
    {
        $pots = $this->pot_config;
        $tables = $this->tv_views;
        $data = array();
        foreach ($tables as $name => $view_id) {
            if ($view_id <> SOUTHWAY_ALL_RECORDS && $view_id <> PRIVATE_ALL_RECORDS && $view_id <> CITYWEST_ALL_RECORDS) {
                $data[$name] = array("pot" => $pots[$name], "one2one" => $this->Trackvia_model->get_121_counts($name));				
				if(!empty($view_id)){
                if ($this->input->post('tv') || $this->uri->segment(3) == "tv") {
                    $data[$name]["trackvia"] = 0;
                    for ($page = 1; $page < 15; $page++) {
                        $view = $this->getAllViewRecords($view_id, $page);
                        if (isset($view['record_count'])) {
                            if ($this->uri->segment(4) == "debug") {
                                echo "<pre>";
                                print_r($view);
                                echo "</pre>";
                                echo "<br>";
                            }
                            $data[$name]["trackvia"] += $view['record_count'];
                        } else {
                            continue;
                        }
                    }
                } else {
                    $data[$name]["trackvia"] = false;
                }
				}

            }
        }
        echo json_encode($data);

    }

    public function fix_non_contacts()
    {
        $query = "select urn from records where outcome_id = 137 and campaign_id in (22,29,32)";
        foreach ($this->db->query($query)->result_array() as $row) {
            $urn = $row['urn'];
            $this->unable_to_contact($urn);
        }
    }

    public function get_rebookings()
    {
        if (isset($_POST['campaign']) && @$_POST['campaign']) {
            $campaign = $_POST['campaign'];
        } else {
            $campaign = "";
        }
        $result = $this->Trackvia_model->get_rebookings($campaign);
        echo json_encode(array("success" => true, "data" => $result));
    }
public function check_darlington(){
	$this->db->query("update records set parked_code=2,pot_id = 57 where campaign_id in(61)");
	/*
 echo "<br>Checking the DARLINGTON_ALL_RECORDS(" . DARLINGTON_ALL_RECORDS . ") view";
       echo "<br>";
       $this->checkView(
           DARLINGTON_ALL_RECORDS,
           array(
               'campaign_id' => 61,
               'urgent' => NULL,
               'status' => 1,
               'appointment_creation' => false,
               'appointment_cancelled' => false,
               'record_color' => '000000',
               'parked_code' => 2,
               'pot_id' => 57,
               'savings_per_panel' => 20,
			   'source_id' => 60

           )
        );
*/
        //Book View
        echo "<br>Checking the DARLINGTON_BOOK_SURVEY(" . DARLINGTON_BOOK_SURVEY . ") view";
        echo "<br>";
        $this->checkView(
            DARLINGTON_BOOK_SURVEY,
            array(
                'campaign_id' => 61,
                'urgent' => NULL,
                'status' => 1,
                'appointment_creation' => false,
                'appointment_cancelled' => false,
                'record_color' => '0066FF',
                'pot_id' => 58,
                'savings_per_panel' => 20,
                'attendee' => 122,
				'source_id' => 60
            )
        );

        //Rebook View
        echo "<br>Checking the DARLINGTON_REBOOK(" . DARLINGTON_REBOOK . ") view";
        echo "<br>";
        $this->checkView(
            DARLINGTON_REBOOK,
            array(
                'campaign_id' => 61,
                'urgent' => 1,
                'status' => 1,
				'parked_code' => NULL,
                'appointment_creation' => true,
                'appointment_cancelled' => true,
                'record_color' => '0066FF',
                'pot_id' => 60,
                'savings_per_panel' => 20,
                'attendee' => 122,
				'source_id' => 60
            )
        );

        //Survey Slots View
        echo "<br>Checking the DARLINGTON_SURVEY_SLOTS(" . DARLINGTON_SURVEY_SLOTS . ") view";
        echo "<br>";
        $this->checkView(
            DARLINGTON_SURVEY_SLOTS,
            array(
                'campaign_id' => 61,
                'urgent' => NULL,
                'status' => 4,
				'parked_code' => NULL,
                'outcome_id' => 72,
                'appointment_creation' => true,
                'appointment_cancelled' => false,
                'record_color' => '00CC00',
                'pot_id' => 37,
                'savings_per_panel' => 20,
                'attendee' => 122,
				'source_id' => 60
            )
        );

        //update sw survey campaign records
    $this->check_trackvia(61);
}

 public function check_da_installs()
    {
        $this->db->query("update records set parked_code=2,pot_id = 57 where campaign_id in(62)");

        //Installation Book View
        echo "<br>Checking the DARLINGTON_BOOK_INSTALLATION(" . DARLINGTON_BOOK_INSTALLATION . ") view";
        echo "<br>";
        $this->checkView(
            DARLINGTON_BOOK_INSTALLATION,
            array(
                'campaign_id' => 62,
                'urgent' => NULL,
				'parked_code' => NULL,
                'status' => 1,
                'appointment_creation' => false,
                'appointment_cancelled' => false,
                'record_color' => '0066FF',
                'pot_id' => 61,
                'savings_per_panel' => 20,
                'attendee' => 139,
				'source_id' => 60,
				'dials'=>0
            )
        );

        //Installation Slots View
        echo "<br>Checking the DARLINGTON_INSTALLATION_SLOTS(" . DARLINGTON_INSTALLATION_SLOTS . ") view";
        echo "<br>";
        $this->checkView(
            DARLINGTON_INSTALLATION_SLOTS,
            array(
                'campaign_id' => 62,
                'urgent' => NULL,
                'status' => 4,
				'parked_code' => NULL,
                'outcome_id' => 72,
                'appointment_creation' => true,
                'appointment_cancelled' => false,
                'record_color' => '00CC00',
                'pot_id' => 62,
                'savings_per_panel' => 20,
                'attendee' => 139,
				'source_id' => 60
            )
        );
        //update sw install campaign records
        $this->check_trackvia(62);

    }


    public function check_southway()
    {
        //SOUTHWAY DATA
        $this->db->query("update records set parked_code=2,pot_id = 28 where campaign_id in(22)");

//        //Southway All records View
//        echo "<br>Checking the SOUTHWAY_ALL_RECORDS(" . SOUTHWAY_ALL_RECORDS . ") view";
//        echo "<br>";
//        $this->checkView(
//            SOUTHWAY_ALL_RECORDS,
//            array(
//                'campaign_id' => 22,
//                'urgent' => NULL,
//                'status' => 1,
//                'appointment_creation' => false,
//                'appointment_cancelled' => false,
//                'record_color' => '000000',
//                'parked_code' => 2,
//                'pot_id' => 28,
//                'savings_per_panel' => 20
//
//            )
//        );

        //Book View
        echo "<br>Checking the SOUTHWAY_BOOK_SURVEY(" . SOUTHWAY_BOOK_SURVEY . ") view";
        echo "<br>";
        $this->checkView(
            SOUTHWAY_BOOK_SURVEY,
            array(
                'campaign_id' => 22,
                'urgent' => NULL,
                'status' => 1,
                'appointment_creation' => false,
                'appointment_cancelled' => false,
                'record_color' => '0066FF',
                'pot_id' => 34,
                'savings_per_panel' => 20,
                'attendee' => 122,
				'source_id' => 28
            )
        );

        //Rebook View
        echo "<br>Checking the SOUTHWAY_REBOOK(" . SOUTHWAY_REBOOK . ") view";
        echo "<br>";
        $this->checkView(
            SOUTHWAY_REBOOK,
            array(
                'campaign_id' => 22,
                'urgent' => 1,
                'status' => 1,
				'parked_code' => NULL,
                'appointment_creation' => true,
                'appointment_cancelled' => true,
                'record_color' => '0066FF',
                'pot_id' => 35,
                'savings_per_panel' => 20,
                'attendee' => 122,
				'source_id' => 28
            )
        );

        //Survey Slots View
        echo "<br>Checking the SOUTHWAY_SURVEY_SLOTS(" . SOUTHWAY_SURVEY_SLOTS . ") view";
        echo "<br>";
        $this->checkView(
            SOUTHWAY_SURVEY_SLOTS,
            array(
                'campaign_id' => 22,
                'urgent' => NULL,
                'status' => 4,
				'parked_code' => NULL,
                'outcome_id' => 72,
                'appointment_creation' => true,
                'appointment_cancelled' => false,
                'record_color' => '00CC00',
                'pot_id' => 37,
                'savings_per_panel' => 20,
                'attendee' => 122,
				'source_id' => 28
            )
        );

        //update sw survey campaign records
        $this->check_trackvia(22);
    }

    public function check_sw_installs()
    {
        $this->db->query("update records set parked_code=2,pot_id = 28 where campaign_id in(52)");

        //Installation Book View
        echo "<br>Checking the SOUTHWAY_BOOK_INSTALLATION(" . SOUTHWAY_BOOK_INSTALLATION . ") view";
        echo "<br>";
        $this->checkView(
            SOUTHWAY_BOOK_INSTALLATION,
            array(
                'campaign_id' => 52,
                'urgent' => NULL,
				'parked_code' => NULL,
                'status' => 1,
                'appointment_creation' => false,
                'appointment_cancelled' => false,
                'record_color' => '0066FF',
                'pot_id' => 51,
                'savings_per_panel' => 20,
                'attendee' => 139,
				'source_id' => 28,
				'dials'=>0
            )
        );

        //Installation Slots View
        echo "<br>Checking the SOUTHWAY_INSTALLATION_SLOTS(" . SOUTHWAY_INSTALLATION_SLOTS . ") view";
        echo "<br>";
        $this->checkView(
            SOUTHWAY_INSTALLATION_SLOTS,
            array(
                'campaign_id' => 52,
                'urgent' => NULL,
                'status' => 4,
				'parked_code' => NULL,
                'outcome_id' => 72,
                'appointment_creation' => true,
                'appointment_cancelled' => false,
                'record_color' => '00CC00',
                'pot_id' => 52,
                'savings_per_panel' => 20,
                'attendee' => 139,
				'source_id' => 28
            )
        );
        //update sw install campaign records
        $this->check_trackvia(52);

    }

    public function check_citywest()
    {
        //CITYWEST DATA
        $this->db->query("update records set parked_code=2,pot_id = 49 where campaign_id = 32");
        //CITYWEST
        /*        echo "<br>Checking the CITYWEST_ALL_RECORDS(" . CITYWEST_ALL_RECORDS . ") view";
        echo "<br>";
        $this->checkView(
            CITYWEST_ALL_RECORDS,
            array(
                'campaign_id' => 32,
                'urgent' => NULL,
                'status' => 1,
                'appointment_creation' => false,
                'appointment_cancelled' => false,
                'record_color' => '000000',
                'parked_code' => 2,
                'pot_id' => 49,
                'savings_per_panel' => 30

            )
        );
        */

        //Book View
        echo "<br>Checking the CITYWEST_BOOK_SURVEY(" . CITYWEST_BOOK_SURVEY . ") view";
        echo "<br>";
        $this->checkView(
            CITYWEST_BOOK_SURVEY,
            array(
                'campaign_id' => 32,
                'urgent' => NULL,
				'parked_code' => NULL,
                'status' => 1,
                'appointment_creation' => false,
                'appointment_cancelled' => false,
                'record_color' => '0066FF',
                'pot_id' => 46,
                'savings_per_panel' => 20,
                'attendee' => 137,
				'source_id' => 49
            )
        );

        //Rebook View
        echo "<br>Checking the CITYWEST_REBOOK(" . CITYWEST_REBOOK . ") view";
        echo "<br>";
        $this->checkView(
            CITYWEST_REBOOK,
            array(
                'campaign_id' => 32,
                'urgent' => 1,
				'parked_code' => NULL,
                'status' => 1,
                'appointment_creation' => true,
                'appointment_cancelled' => true,
                'record_color' => '0066FF',
                'pot_id' => 47,
                'savings_per_panel' => 20,
                'attendee' => 137,
				'source_id' => 49
            )
        );

        //Survey Slots View
        echo "<br>Checking the CITYWEST_SURVEY_SLOTS(" . CITYWEST_SURVEY_SLOTS . ") view";
        echo "<br>";
        $this->checkView(
            CITYWEST_SURVEY_SLOTS,
            array(
                'campaign_id' => 32,
                'urgent' => NULL,
                'status' => 4,
                'outcome_id' => 72,
				'parked_code' => NULL,
                'appointment_creation' => true,
                'appointment_cancelled' => false,
                'record_color' => '00CC00',
                'pot_id' => 48,
                'savings_per_panel' => 20,
                'attendee' => 137,
				'source_id' => 49
            )
        );
        $this->check_trackvia(32);
    }

    public function check_private()
    {
        //PRIVATE DATA
        $this->db->query("update records set parked_code=2,pot_id = 41 where campaign_id in(28,29) and record_status = 3");

        //
//        //PRIVATE TABLE
//
        //Private Residential View
        echo "<br>Checking the PRIVATE_ALL_RECORDS(" . PRIVATE_ALL_RECORDS . ") view";
        echo "<br>";
        $this->checkView(
            PRIVATE_ALL_RECORDS,
            array(
                'campaign_id' => 29,
                'urgent' => NULL,
                'status' => 1,
				'parked_code' => NULL,
                'appointment_creation' => false,
                'appointment_cancelled' => false,
                'record_color' => '000000',
                'parked_code' => 2,
                'pot_id' => 41,
                'savings_per_panel' => 30,
                'attendee' => 121,
				'source_id' => 41
            )
        );

        //Private Residential View
        echo "<br>Checking the PRIVATE_BOOK_SURVEY(" . PRIVATE_BOOK_SURVEY . ") view";
        echo "<br>";
        $this->checkView(
            PRIVATE_BOOK_SURVEY,
            array(
                'campaign_id' => 29,
                'urgent' => NULL,
				'parked_code' => NULL,
                'status' => 1,
                'appointment_creation' => false,
                'appointment_cancelled' => false,
                'record_color' => '0066FF',
                'pot_id' => 39,
                'savings_per_panel' => 30,
                'attendee' => 121,
				'source_id' => 41

            )
        );

        //Private Residential View
        echo "<br>Checking the PRIVATE_REBOOK(" . PRIVATE_REBOOK . ") view";
        echo "<br>";
        $this->checkView(
            PRIVATE_REBOOK,
            array(
                'campaign_id' => 29,
                'urgent' => 1,
				'parked_code' => NULL,
                'status' => 1,
                'appointment_creation' => true,
                'appointment_cancelled' => true,
                'record_color' => '6600FF',
                'pot_id' => 38,
                'savings_per_panel' => 30,
                'attendee' => 121,
				'source_id' => 41
            )
        );

        //Private Residential View
        echo "<br>Checking the PRIVATE_SURVEY_SLOTS(" . PRIVATE_SURVEY_SLOTS . ") view";
        echo "<br>";
        $this->checkView(
            PRIVATE_SURVEY_SLOTS,
            array(
                'campaign_id' => 29,
                'urgent' => NULL,
				'parked_code' => NULL,
                'status' => 4,
                'outcome_id' => 72,
                'appointment_creation' => true,
                'appointment_cancelled' => false,
                'record_color' => '00CC00',
                'pot_id' => 36,
                'savings_per_panel' => 30,
                'attendee' => 121,
				'source_id' => 41
            )
        );

//        //Private Ineligible View/    
        echo "<br>Checking the PRIVATE_INFORM_INELIGIBLE(" . PRIVATE_INFORM_INELIGIBLE . ") view";
        echo "<br>";
        $this->checkView(
            PRIVATE_INFORM_INELIGIBLE,
            array(
                'campaign_id' => 28,
				'parked_code' => NULL,
                'urgent' => NULL,
                'status' => 1,
                'appointment_creation' => false,
                'appointment_cancelled' => false,
                'record_color' => '990000',
                'pot_id' => 40,
                'savings_per_panel' => 30,
                'attendee' => 121,
				'source_id' => 41
            )
        );
        $this->check_trackvia(29);
        //remove mortgage approval, landlord approval and general queries. These are handles by GHS
        $this->db->query("update records set parked_code=2,pot_id = 41 where campaign_id = 29 and outcome_id in (135,125,127)");
        //park any that have a refusal reason
        $this->db->query("update
            `record_details`
            JOIN records
            USING ( urn ) set parked_code = 2
            WHERE campaign_id =29
            AND (
            c6 IS NOT NULL
            OR c6 = ''
            )
            AND record_status = 1
            AND parked_code IS NULL");
			
			//there are currently 2 area in private so they seperating into the respective sources using the postcode
			 $this->db->query("update
            `records` 
            JOIN contacts using(urn) join contact_addresses using(contact_id)
            set source_id = 68
            WHERE campaign_id =29
            and postcode like 'CA%'");
			 $this->db->query("update
            `records` 
            JOIN contacts using(urn) join contact_addresses using(contact_id)
            set source_id = 41
            WHERE campaign_id =29
            and postcode like 'PE%'");
			
			$this->db->query("update records join campaigns using(campaign_id) join contacts using(records) join contact_telephone using(contact_id) set parked_code=2 where client_id = 12 and (telephone_number is null or telephone_number='')");
			
    }


    public function check_trackvia($campaign_id)
    {
        //queries we may want to run after the updates can go here
        $this->db->query("update records set map_icon ='fa-home' where campaign_id = '$campaign_id'");
        $this->db->query("update contact_addresses left join contacts using(contact_id) left join records using(urn) set contact_addresses.`primary` = 1 where campaign_id = '$campaign_id'");
        $this->db->query("update contacts inner join records using(urn) inner join data_pots using(pot_id) set notes = pot_name where campaign_id = '$campaign_id' and records.pot_id is not null");

        $query = "select urn from records join campaigns using(campaign_id) where outcome_id in(select outcome_id from outcomes where delay_hours is not null) and dials > max_dials and campaign_id = '$campaign_id'";
        foreach ($this->db->query($query)->result_array() as $row) {
            $urn = $row['urn'];
            $this->db->query("update records left join campaigns using(campaign_id) set outcome_id = 137,outcome_reason_id=NULL, record_status=3 where urn = '$urn'");
            $this->unable_to_contact($urn);
        }
        //clear virgin records ownership
        $this->db->query("delete from ownership where urn in(select urn
                FROM records
                WHERE 1
                AND campaign_id = '$campaign_id'
                AND record_status =1
                AND parked_code IS NULL
                AND progress_id IS NULL
                AND outcome_id IS NULL
                AND dials = 0  )");


        //unset anything marked as urgent that is not in the rebook data pots
        $this->db->query("update records set urgent = null where pot_id not in(38,35,47) and campaign_id = '$campaign_id'");
        //Update appointmentes without history associated
        echo "<br>Checking if exists appointments without history associated...";
        $this->db->query("
            INSERT INTO history
            (campaign_id, urn, contact, description, outcome_id, nextcall, user_id, role_id, team_id, group_id)
            SELECT
                r.campaign_id,
                r.urn,
                a.date_added AS contact,
                'Record was updated' AS description,
                72 AS outcome_id,
                a.date_added AS nextcall,
                a.created_by AS user_id,
                u.role_id,
                u.team_id,
                u.group_id
            FROM appointments a
            INNER JOIN records r USING (urn)
            INNER JOIN users u ON (a.created_by = u.user_id)
            LEFT JOIN (
                SELECT history_id,urn, contact
                FROM history
                WHERE outcome_id = 72) h ON (h.urn = r.urn AND DATE(h.contact) = DATE(a.date_added))
            WHERE u.name <> 'GHS User' AND h.history_id IS NULL
        ");
        $afftectedRows = $this->db->affected_rows();
        echo $afftectedRows . " rows inserted in the history table.<br>";

        //Cancel the appointments that are still live, with a future date and are not in the right view (booked surveys or installs)
        echo "<br>Cancel appointments that are still live, with a future date and are not in the right view (booked surveys or installs)... ";
        $qry = "
            SELECT
              GROUP_CONCAT(a.appointment_id SEPARATOR ',') as app
            FROM appointments a
              INNER JOIN records r USING (urn)
              INNER JOIN data_pots d USING (pot_id)
              INNER JOIN contacts c USING (urn)
              INNER JOIN client_refs cf USING (urn)
            WHERE pot_id IN (28, 41, 49)
                  AND campaign_id = " . $campaign_id . "
                  AND a.start >= NOW()
                  AND a.status != 0";
        $appointments = $this->db->query($qry)->result_array();
        if (!empty($appointments[0]['app'])) {
            $appointment_list = $appointments[0]['app'];
            echo " appointment ids (" . $appointment_list . ")<br>";
            //Set appointment status as 0 -> cancel
            $this->db->query("UPDATE appointments app SET app.status=0, app.cancellation_reason='Cancelled by GHS' WHERE app.appointment_id IN (" . $appointment_list . ")");
            $afftectedRows = $this->db->affected_rows();
            echo "<br>" . $afftectedRows . " appointments cancelled.<br>";

            //Set the outcome on the record as Appointment Cancelled (132)
            $this->db->query("
              UPDATE records r SET r.outcome_id=132 WHERE r.urn IN (
                  SELECT a.urn
                  FROM appointments a
                  WHERE a.appointment_id IN (" . $appointment_list . ")
              )
            ");
            $afftectedRows = $this->db->affected_rows();
            echo $afftectedRows . " records updated with outcome as Appointment Cancelled<br>";
        } else {
            echo " Nothing changed<br>";
        }
    }

    public function list_view()
    {
        $view_id = $this->uri->segment(3);
        $records = array();
        for ($page = 1; $page < 15; $page++) {
            $result = $this->getAllViewRecords($view_id, $page);
            if (isset($result['records'])) {
                $records = array_merge($result['records'], $records);
            } else {
                break;
            }
        }
        echo "<pre>";
        print_r($records);
        echo "</pre>";
    }

    private function getAllViewRecords($view_id, $page = 1)
    {
        //$page = 1;
        $limit = null;
        $search = true;
        $view = array();
        //TRACKVIA API MODIFIED (added page and limit variables) !!!!!!!!!!!!!!!!!!!!!!!!!!
        $result = $this->tv->getView($view_id, $page, $limit);

        //$result = array();
        if (isset($result['records'])) {
            $view['id'] = $result['id'];
            $view['name'] = $result['name'];
            $view['description'] = $result['description'];
            $view['table_id'] = $result['table_id'];
            $view['record_count'] = (isset($view['record_count']) ? ($result['record_count'] + $view['record_count']) : $result['record_count']);
            $view['records'] = (isset($view['records']) ? array_merge($result['records'], $view['records']) : $result['records']);

        } else {
            return false;
        }

        return $view;
    }

	public function send_enquiry_to_tv($urn){
		$urn = $this->input->post('urn');
		$client_ref = $this->Trackvia_model->get_client_ref_from_urn($urn);
		$this->where("urn",$urn);
		$query = $this->db->get("record_details");
		if($query->num_rows()){
		$data = array("Date of Enquiry"=>$query->row()->d1);
		if(!empty($date)){
			 $this->tv->updateRecord($client_ref, $data);
		}
		}
	}

    public function update_record_details($array = false,$urn=false)
    {
		if($this->input->post('id')){
            $array = array();
            $array[] = $this->input->post('id'); 
        } else if($this->input->post('urn')){
			 $urn = $this->input->post('urn');
			 $array[] = $this->Trackvia_model->get_client_ref_from_urn($urn); 
		}

        foreach ($array as $id) {
			$urn = $this->Trackvia_model->get_urn_from_client_ref($id);
			$data = array("urn" => $urn);
            $response = $this->tv->getRecord($id);
            $fields = $response['fields'];
            if (!empty($fields['GHS UPRN']) && isset($fields['GHS UPRN'])) {
                $data["c1"] = $fields['GHS UPRN'];
            }
            if (!empty($fields['Referred by']) && isset($fields['Referred by'])) {
                $data["c2"] = $fields['Referred by'];
            }
            if (!empty($fields['Enquiry Type']) && isset($fields['Enquiry Type'])) {
                $data["c3"] = $fields['Enquiry Type'];
            }
            if (!empty($fields['Property Status']) && isset($fields['Property Status'])) {
                $data["c5"] = $fields['Property Status'];
            }
            if (!empty($fields['Reason for Desktop Fail']) && isset($fields['Reason for Desktop Fail'])) {
                $data["c6"] = $fields['Reason for Desktop Fail'];
            }
        }
        $this->Trackvia_model->update_record_from_trackvia($data);
        echo json_encode($data);
    }

    /*
        /**
         * Test
         */
    public function checkView($view_id, $options)
    {
        $attendee = isset($options['attendee']) ? $options['attendee'] : "121";
        $campaign_id = $options['campaign_id'];
        $urgent = $options['urgent'];
        $status = $options['status'];
        $outcome_id = isset($options['outcome_id']) ? $options['outcome_id'] : NULL;
		$source_id = isset($options['source_id']) ? $options['source_id'] : NULL;
        $parked_code = isset($options['parked_code']) ? $options['parked_code'] : "";
		$parked_code = isset($options['dials']) ? $options['dials'] : "";
        $appointment_creation = $options['appointment_creation'];
        $appointment_cancelled = $options['appointment_cancelled'];
        $record_color = $options['record_color'];
        $pot = $options['pot_id'];
        $savings = $options['savings_per_panel'];

        //Get the trackvia records for this view
        for ($page = 1; $page < 15; $page++) {
            echo "Page: .$page";
            echo "<br>";
            $view = $this->getAllViewRecords($view_id, $page);

            //$view = $this->tv->getView($view_id);


            if (isset($view['records'])) {
                $tv_records = $view['records'];
                print_r($view_id);
                /*echo "<pre>";
                print_r($view);
                echo "</pre>";*/
            } else {
                $tv_records = array();
                print_r($view_id);
                print_r($view);
                return false;
            }


            //Get the locator ids (client_ref in our system
            $tv_record_ids = array();
            $aux = array();
            foreach ($tv_records as $tv_record) {
                array_push($tv_record_ids, $tv_record['id']);
                $aux[md5($tv_record['id'])] = $tv_record;
            }
            $tv_records = $aux;

            echo "<br>Total track via records in this view... " . count($tv_records);
            echo "<br>";
            //Get the records to be updated in our system
            $records = $this->Trackvia_model->getRecordsByTVIds($tv_record_ids);
            echo "<br>Matching records found in the calling system... " . count($records);
            echo "<br>";
            //Update the record campaign if it is needed (different campaign) and create a new one if it does not exist yet
            $update_records = array();
            $update_extra = array();
            $update_notes = array();
            $new_records_ids = $tv_record_ids;
            $records_found = 0;
            $appointments_cancelled_count = 0;
            $appointments_created_count = 0;

            foreach ($records as $record) {
                $property_status = "";
                $fields = $tv_records[md5($record['client_ref'])]['fields'];
                foreach ($fields as $k => $v) {
                    if (strpos($k, "Property Stat") !== false) {
                        $property_status = $v;
                    }
                }

                //If the campaign had changed or the park_code is "Not Working"

                if ($record['campaign_id'] != $campaign_id || $record['parked_code'] == 7 || $record['parked_code'] == 2 || $record['record_color'] != $record_color || $record['pot_id'] != $pot) {
                    if ($record['record_status'] <> "3" || in_array($pot, array(37, 52, 36, 48))) {
                        $update_array = array(
                            'urn' => $record['urn'],
                            'campaign_id' => $campaign_id,
                            'parked_code' => NULL,
                            'urgent' => $urgent,
                            'record_status' => $status,
                            'record_color' => $record_color,
                            'pot_id' => $pot
                        );
                        if (!empty($outcome_id)) {
                            $update_array['outcome_id'] = $outcome_id;
                        }
						if (!empty($dials)) {
                            $update_array['dials'] = $dials;
                        }
                        if (!empty($parked_code)) {
                            $update_array['parked_code'] = $parked_code;
                        }
                        //organising the record update data

                        array_push($update_records, $update_array);

                    }
                }
                //Create appointment if it is needed
                if ($appointment_creation) {
                    if ($appointment_cancelled) {
                        $appointments_cancelled_count++;
                    } else {
                        $appointments_created_count++;
                    }
                    $this->addUpdateAppointment($fields, $record, $appointment_cancelled, $attendee);
                }

                if (!empty($fields['Contact Notes'])) {
                    array_push($update_notes, array("urn" => $record['urn'], "note" => str_replace("<!--tvia_br--><br><!--tvia_br-->", "\n", $fields['Contact Notes']), "updated_by" => 1));
                }
                //organise the record_details update
                $extra = array();
                if (!empty($fields['No. Panels (Desktop)']) && isset($fields['No. Panels (Desktop)'])) {
                    $extra["n1"] = $fields['No. Panels (Desktop)'];
                    $extra["n2"] = $fields['No. Panels (Desktop)'] * $savings;
                }
                if (!empty($fields['GHS UPRN']) && isset($fields['GHS UPRN'])) {
                    $extra["c1"] = $fields['GHS UPRN'];
                    //echo $fields['GHS UPRN']."<br>";
                }
                if (!empty($fields['Referred by']) && isset($fields['Referred by'])) {
                    $extra["c2"] = $fields['Referred by'];
                }
                if (!empty($fields['Enquiry Type']) && isset($fields['Enquiry Type'])) {
                    $extra["c3"] = $fields['Enquiry Type'];
                    //echo $fields['Enquiry type']."<br>";
                }
                if (@!empty($property_status)) {
                    $extra["c5"] = $property_status;
                }
                if (!empty($fields['Reason for Desktop Fail']) && isset($fields['Reason for Desktop Fail']) && $campaign_id <> 22) {
                    $extra["c6"] = $fields['Reason for Desktop Fail'];
                } else if (!empty($fields['Int Survey Priority']) && isset($fields['Reason for Desktop Fail']) && $campaign_id == 22) {
                    $extra["c6"] = $fields['Int Survey Priority'];
                }
                if (!empty($extra)) {
                    $extra['urn'] = $record['urn'];
                    array_push($update_extra, $extra);
                }
                //Remove from the new_record_ids array the records that already exist on our system
                if (in_array($record['client_ref'], $new_records_ids)) {
                    unset($tv_records[md5($record['client_ref'])]);
                }
            }

            //Update the records which campaign was changed
            echo "\n<br>Records updated in our system... " . count($update_records) . "\n";
            echo "<br>";

            if (!empty($update_records)) {
                $this->Trackvia_model->updateRecords($update_records);
            }
            if (!empty($update_notes)) {
                echo("Updating Notes");
                echo "<br>";
                //print_r($update_notes);
                echo $this->Trackvia_model->updateNotes($update_notes);
            }
            //update the record details
            if (!empty($update_extra)) {
                echo("Updating Details");
                echo "<br>";
                //print_r($update_extra);
                $this->Trackvia_model->update_extra($update_extra);
            }

            echo "<br>Records left to create in our system... " . count($tv_records) . "\n";
            echo "<br>";
            $new = array();
            //Add new records if there are any left in the $tv_records array
            if (count($tv_records) > 0) {
                echo("Creating new records #Source-ID: [$pot]");
                echo "<br>";
                //print_r($tv_records);
                foreach ($tv_records as $record) {
                    //organise the new record data
                    $data = array("campaign_id" => $campaign_id,
                        "date_added" => date('Y-m-d H:i:s'),
                        "record_status" => $status,
                        "record_color" => $record_color,
                        "outcome_id" => $outcome_id,
                        "urgent" => $urgent,
                        "pot_id" => $pot,
						"source_id" => $source_id,
                        "parked_code" => 2
                    );
                    $urn = $this->Trackvia_model->add_record($data);
                    //catch the newly created urns
                    $new[] = $urn;
                    //prepare the new client refs
                    $data = array("urn" => $urn,
                        "client_ref" => $record['id']
                    );
                    //insert the client refs
                    $this->Trackvia_model->add_client_refs($data);
                    //prepare the record_details
                    $data = array();
                    if (!empty($fields['GHS UPRN']) && isset($fields['GHS UPRN'])) {
                        $data["c1"] = $fields['GHS UPRN'];
                    }
                    if (!empty($fields['Referred by']) && isset($fields['Referred by'])) {
                        $data["c2"] = $fields['Referred by'];
                    }
                    if (!empty($fields['Enquiry Type']) && isset($fields['Enquiry Type'])) {
                        $data["c3"] = $fields['Enquiry Type'];
                    }
                    if (!empty($fields['Property Status']) && isset($fields['Property Status'])) {
                        $data["c5"] = $fields['Property Status'];
                    }
                    if (!empty($fields['Reason for Desktop Fail']) && isset($fields['Reason for Desktop Fail'])) {
                        $data["c6"] = $fields['Reason for Desktop Fail'];
                    }
                    if (!empty($data)) {
                        $data["urn"] = $urn;
                        $this->Trackvia_model->add_record_details($data);
                    }
                    //prepare any new contacts
                    $data = array("urn" => $urn,
                        "fullname" => isset($record['fields']['Owner / Tenant Name 1']) ? $record['fields']['Owner / Tenant Name 1'] : '',
                        "email" => isset($record['fields']['Email address']) ? $record['fields']['Email address'] : NULL,
                        "date_created" => date('Y-m-d H:i:s'),
                        "notes" => "Ref# " . $record['id'],
                        "primary" => 1);
                    $contact = $this->Trackvia_model->add_contact($data);
                    //prepare any new telephone numbers
                    if (isset($record['fields']['Primary Contact (Landline)']) && !empty($record['fields']['Primary Contact (Landline)'])) {
                        $data = array("contact_id" => $contact,
                            "description" => "Landline",
                            "telephone_number" => $record['fields']['Primary Contact (Landline)']
                        );
                        $this->Trackvia_model->add_telephone($data);
                    }
                    //prepare any new mobile telephone numbers
                    if (isset($record['fields']['Primary Contact (Mobile)']) && !empty($record['fields']['Primary Contact (Mobile)'])) {
                        $data = array("contact_id" => $contact,
                            "description" => "Mobile",
                            "telephone_number" => $record['fields']['Primary Contact (Mobile)']
                        );
                        $this->Trackvia_model->add_telephone($data);
                    }
                    //add transfer
                    $data = array("contact_id" => $contact,
                        "description" => "Transfer to GHS",
                        "telephone_number" => "01228819810"
                    );
                    $this->Trackvia_model->add_telephone($data);
                    //prepare any new telephone addresses
                    if (!isset($record['fields']['PostCode'])) {
                        $tv_record = $this->tv->getRecord($record['id']);
                        $record['fields']['House No.'] = $tv_record['fields']['House No.'];
                        $record['fields']['Address 1'] = $tv_record['fields']['Address 1'];
                        $record['fields']['Address 2'] = $tv_record['fields']['Address 2'];
                        $record['fields']['City'] = $tv_record['fields']['City'];
                        $record['fields']['PostCode'] = $tv_record['fields']['PostCode'];
                    }
                    $data = array(
                        "contact_id" => $contact,
                        "add1" => $record['fields']['House No.'] . " " . $record['fields']['Address 1'],
                        "add2" => $record['fields']['Address 2'],
                        "add3" => @$record['fields']['City'],
                        "postcode" => $record['fields']['PostCode'],
                        "primary" => 1
                    );
                    $this->Trackvia_model->add_address($data);


                }
                //show the new urns
                echo "<pre>";
                print_r($new);
                echo "</pre>";
            }
        }


    }

    /**
     * Add/Update appointment in our system
     */
    public function addUpdateAppointment($fields, $record, $appointment_cancelled = false, $attendee = "121")
    {
        //Survey
        if (isset($fields['Planned Survey Date'])) {
            $sd = explode("T", $fields['Planned Survey Date']);
            $planned_appointment_date = $sd[0];
            $planned_appointment_time = (isset($fields['Survey appt']) ? $fields['Survey appt'] : '');
            $planned_appointment_type = APPOINTMENT_TYPE_SURVEY;
            $title = "Appointment for survey";

            $app_data = "<br>";
            foreach ($fields as $k => $v) {
                if (!empty($v)) {
                    $app_data .= "$k: $v<br>";
                }
            }
            $text = "Appointment set for GHS Survey $app_data";
        } //Installation
        else if (isset($fields['Commissioning date (customer needs to be in)'])) {
            $sd = explode("T", $fields['Commissioning date (customer needs to be in)']);
            $planned_appointment_date = $sd[0];
            $planned_appointment_time = (isset($fields['Commissioning appt']) ? $fields['Commissioning appt'] : '');
            $planned_appointment_type = APPOINTMENT_TYPE_INSTALLATION;
            $title = "Appointment for installation";
            $app_data = (isset($fields['Installation comments']) ? '<br>' . $fields['Installation comments'] : '');
            $text = "Appointment set for GHS Installation $app_data";
        } else {
            $planned_appointment_date = '';
            $planned_appointment_type = NULL;
            $title = "Appointment";
        }

        switch ($planned_appointment_time) {
            case "am":
                $planned_appointment_time = "09:00:00";
                break;
            case "pm":
                $planned_appointment_time = "13:00:00";
                break;
            case "eve":
                $planned_appointment_time = "18:00:00";
                break;
            default:
                $planned_appointment_time = "09:00:00";
                break;
        }

        $planned_survey_datetime = $planned_appointment_date . " " . $planned_appointment_time;

        //Add appointment if the appointment_date (same for surveys and installations) is different in both systems
        if ($record['survey_date'] != $planned_appointment_date) {
            //Create a new appointment if it is needed
            //Check if the postcode exist on this field, or get from the all_view in other case
            if (!isset($fields['PostCode'])) {
                $tv_record = $this->tv->getRecord($record['client_ref']);
                $fields['PostCode'] = $tv_record['fields']['PostCode'];
            }
            $this->Trackvia_model->create_appointment($fields, $record, $planned_survey_datetime, $title, $text, $planned_appointment_type, $attendee);
            $this->Locations_model->set_location_id($fields['PostCode']);
        } else {
            echo("Uncancelling appointment that was set:" . $record['urn']);
            echo "<br>";
            $this->Trackvia_model->uncancel_appointment($record['urn'], $planned_appointment_date);
        }
        if ($appointment_cancelled) {
            echo("Cancelling appointment that needs rebooking:" . $record['urn']);
            echo "<br>";
            $this->Trackvia_model->cancel_appointment($record['urn'], $planned_appointment_date);
        }
    }


    public function add_appointment()
    {
        $urn = $this->input->post('urn');

        log_message('info', 'Starting Trackvia appointment:' . $urn);

        //Get the record data
        $app = $this->Trackvia_model->get_appointment($urn);
        //if its a private record then we need to do a few extra bits
        $update_record = array();
        if ($app['campaign_id'] == "29" && $app['pot_id'] <> "55") {
            $update_record = array("pot_id" => 36, "record_color" => "00CC00");
            //if it doesnt exist on trackvia we should add it first
            if (empty($app['client_ref'])) {
                $response = $this->add_tv_record($urn);
                $app['client_ref'] = $response['records'][0]['id'];
            } else {
                $this->update_tv_record($urn);
            }
        } else if ($app['campaign_id'] == "22") {
            //$update_record = array("pot_id" => 37, "record_color" => "00CC00");
        } else if ($app['campaign_id'] == "52") {
            //$update_record = array("pot_id" => 52, "record_color" => "00CC00");
        } else if ($app['campaign_id'] == "32") {
            //$update_record = array("pot_id" => 48, "record_color" => "00CC00");
        }

        //Survey 
        if ($app['appointment_type_id'] == APPOINTMENT_TYPE_SURVEY) {
			$tv_sources = $this->tv_sources; 
            $data = array(
                "Planned Survey Date" => $app['date'] . "T12:00:00-0600",
                "Survey appt" => $app['slot'],
                "Survey Booking Confirmed" => "Y",
                "Survey booked by" => "121",
                "Survey Appointment Comments" => $app['title'] . ' : ' . $app['text']
				
            );
            if (isset($tv_sources[$app['source_name']])) {
                $data["Data Source"] = $tv_sources[$app['source_name']];
            }
        } //Installation
        else if ($app['appointment_type_id'] == APPOINTMENT_TYPE_INSTALLATION) {
            if (date('l', strtotime($app['date'])) == "Monday") {
                $scaffold = " - 4";
                $install = "- 3";
                $scaffold_down = "+ 1";
            } else if (date('l', strtotime($app['date'])) == "Tuesday") {
                $scaffold = " - 4";
                $install = " - 1";
                $scaffold_down = "+ 1";
            } else if (date('l', strtotime($app['date'])) == "Friday") {
                $scaffold = " - 2";
                $install = " - 1";
                $scaffold_down = "+ 3";
            } else {
                $scaffold = " - 2";
                $install = " - 1";
                $scaffold_down = "+ 1";
            }

            $data = array(
                "Scaffold Up" => date('Y-m-d', strtotime($app['date'] . " $scaffold day")) . "T12:00:00-0600",
                "Scaffold Down" => date('Y-m-d', strtotime($app['date'] . " $scaffold_down day")) . "T12:00:00-0600",
                "Planned Installation date" => date('Y-m-d', strtotime($app['date'] . " $install day")) . "T12:00:00-0600",
                "Commissioning date (customer needs to be in)" => $app['date'] . "T12:00:00-0600",
                "Commissioning appt" => "am",//$app['slot'],
                "Installation comments" => $app['title'] . ' : ' . $app['text'],
                "Installation Date Confirmed" => "Y"
            );
			 if (isset($tv_sources[$app['source_name']])) {
                $data["Data Source"] = $tv_sources[$app['source_name']];
            }
        }

        if ($app['campaign_id'] == 29) {
            $data["Owner Consent to proceed"] = "Y";
        }
        //Update the record

        $response = $this->tv->updateRecord($app['client_ref'], $data);

        if (!empty($response)) {
            log_message('info', 'Appointment was added:' . $urn . ':' . $response . ':' . $app['client_ref']);
            $this->db->where("urn", $urn);
            $this->db->update("records", $update_record);
            echo json_encode(array("success" => true, "response" => $response, "ref" => $app['client_ref'], "data" => $data));
            $this->db->query("update records set urgent=null where urn = '$urn'");
        } else {
            if ($app['pot_id'] <> "55") {
                log_message('info', 'No response from trackvia:' . $urn);
                $message = "An error occured while saving an appointment \r\n	";
                $message .= "  URN: $urn \r\n";
                $message .= "Record ID: " . $app['client_ref'] . " \r\n	";
                $message .= "Sent Data \r\n	";
                foreach ($data as $k => $v) {
                    $message .= "$k: $v \r\n	";
                }
                mail("bradf@121customerinsight.co.uk", "Trackvia Update Error", $message, $this->headers);
            }
        }

    }

    public function unable_to_contact($urn = false)
    {
        if (!$urn) {
            $urn = $this->input->post('urn');
        }
        //Get the record data
        $record = $this->get_record($urn);
        if ($record['pot_id'] <> 55) {
            $data = array("Customer not contactable" => "Customer not contactable");

            $response = $this->tv->updateRecord($record['client_ref'], $data);
            if (!empty($response)) {
                echo json_encode(array("success" => true, "response" => $response, "ref" => $record['client_ref']));
            } else {
                $message = " An error occured while updating a record \r\n";
                $message .= "  URN: $urn \r\n";
                $message .= " Record ID: " . $record['client_ref'] . " \r\n";
                $message .= " Sent Data \r\n";
                foreach ($data as $k => $v) {
                    $message .= " $k: $v \r\n";
                }
                mail("bradf@121customerinsight.co.uk", "Trackvia Update Error", $message, $this->headers);
            }
        }
    }

    public function unable_to_contact_installs($urn = false)
    {
        if (!$urn) {
            $urn = $this->input->post('urn');
        }
        //Get the record data
        $record = $this->get_record($urn);
        if ($record['pot_id'] <> 55) {
            $data = array("Cannot Contact for Installation" => "Cannot Contact for Installation");

            $response = $this->tv->updateRecord($record['client_ref'], $data);
            if (!empty($response)) {
                echo json_encode(array("success" => true, "response" => $response, "ref" => $record['client_ref']));
            } else {
                $message = " An error occured while updating a record \r\n";
                $message .= "  URN: $urn \r\n";
                $message .= " Record ID: " . $record['client_ref'] . " \r\n";
                $message .= " Sent Data \r\n";
                foreach ($data as $k => $v) {
                    $message .= " $k: $v \r\n";
                }
                mail("bradf@121customerinsight.co.uk", "Trackvia Update Error", $message, $this->headers);
            }
        }
    }

    public function survey_refused()
    {
        $urn = $this->input->post('urn');
        //Get the record data
        $record = $this->get_record($urn);
        if ($record['pot_id'] <> 55) {
            $data = array("Planned Survey Date" => "", "Survey appt" => "", "Survey Booking Confirmed" => "", "Survey booked by" => "", "Survey Appointment Comments" => "", "Customer Cancellation" => "declined", "Customer Cancellation notes" => !empty($record['outcome_reason']) ? $record['outcome_reason'] : $record['comments'], "Cancelled by" => "121", "Date of Cancellation" => date('Y-m-d') . "T12:00:00-0600");

            if ($record['campaign_id'] == "29") {
                $data["Owner Consent to proceed"] = "N";
                $data["Date Tenant Notified"] = "today";
            }
if(!empty($record['client_ref'])){
            $response = $this->tv->updateRecord($record['client_ref'], $data);
            if (!empty($response)) {
                echo json_encode(array("success" => true, "response" => $response, "ref" => $record['client_ref'], "data" => $data));
            } else {
                $message = " An error occured while updating a record \r\n";
                $message .= "  URN: $urn \r\n";
                $message .= " Record ID: " . $record['client_ref'] . " \r\n";
                $message .= " Sent Data \r\n";
                foreach ($data as $k => $v) {
                    $message .= " $k: $v \r\n";
                }
                mail("bradf@121customerinsight.co.uk", "Trackvia Update Error", $message, $this->headers);
            }
}
        }
    }

    //TODO the fields we update here need confirming, there doesnt appear to be any cancel install fields for us.
    public function install_refused()
    {

        $urn = $this->input->post('urn');
        //Get the record data
        $record = $this->get_record($urn);
        $data = array("Commissioning date (customer needs to be in)" => "", "Commissioning appt" => "", "Customer Cancellation" => "declined", "Customer Cancellation notes" => !empty($record['outcome_reason']) ? $record['outcome_reason'] : $record['comments'], "Cancelled by" => "121", "Date of Cancellation" => date('Y-m-d') . "T12:00:00-0600");

        $response = $this->tv->updateRecord($record['client_ref'], $data);
        if (!empty($response)) {
            echo json_encode(array("success" => true, "response" => $response, "ref" => $record['client_ref'], "data" => $data));
        } else {
            $message = " An error occured while updating a record \r\n";
            $message .= "  URN: $urn \r\n";
            $message .= " Record ID: " . $record['client_ref'] . " \r\n";
            $message .= " Sent Data \r\n";
            foreach ($data as $k => $v) {
                $message .= " $k: $v \r\n";
            }
            mail("bradf@121customerinsight.co.uk", "Trackvia Update Error", $message, $this->headers);
        }
    }

    public function notified_not_eligible()
    {
        $urn = $this->input->post('urn');
        //Get the record data
        $record = $this->get_record($urn);
        $data = array("Date Owner / Tenant Informed of Rejection" => date('Y-m-d') . "T12:00:00-0600",
            "Owner / Tenant Informed of Rejection" => "Y");

        $response = $this->tv->updateRecord($record['client_ref'], $data);
        if (!empty($response)) {
            echo json_encode(array("success" => true, "response" => $response, "ref" => $record['client_ref'], "data" => $data));
        } else {
            $message = " An error occured while updating a record \r\n";
            $message .= "  URN: $urn \r\n";
            $message .= " Record ID: " . $record['client_ref'] . " \r\n";
            $message .= " Sent Data \r\n";
            foreach ($data as $k => $v) {
                $message .= " $k: $v \r\n";
            }
            mail("bradf@121customerinsight.co.uk", "Trackvia Update Error", $message, $this->headers);
        }
    }


    public function already_had_survey()
    {
        $urn = $this->input->post('urn');
        //Get the record data
        $record = $this->get_record($urn);
        if ($record['pot_id'] <> 55) {
            $data = array("External Survey Completed" => "Y",
                "Internal Survey Completed" => "Y");

            $response = $this->tv->updateRecord($record['client_ref'], $data);
            if (!empty($response)) {
                echo json_encode(array("success" => true, "response" => $response, "ref" => $record['client_ref'], "data" => $data));
            } else {
                $message = " An error occured while updating a record \r\n";
                $message .= "  URN: $urn \r\n";
                $message .= " Record ID: " . $record['client_ref'] . " \r\n";
                $message .= " Sent Data \r\n";
                foreach ($data as $k => $v) {
                    $message .= "$k: $v\r\n";
                }
                mail("bradf@121customerinsight.co.uk", "Trackvia Update Error", $message, $this->headers);
            }
        }
    }

    //TODO the fields we update here need confirming
    public function already_had_installation()
    {
        $urn = $this->input->post('urn');
        //Get the record data
        $record = $this->get_record($urn);
        $data = array("External Survey Completed" => "Y",
            "Internal Survey Completed" => "Y");

        $response = $this->tv->updateRecord($record['client_ref'], $data);
        if (!empty($response)) {
            echo json_encode(array("success" => true, "response" => $response, "ref" => $record['client_ref'], "data" => $data));
        } else {
            $message = " An error occured while updating a record \r\n";
            $message .= "  URN: $urn \r\n";
            $message .= " Record ID: " . $record['client_ref'] . " \r\n";
            $message .= " Sent Data \r\n";
            foreach ($data as $k => $v) {
                $message .= "$k: $v\r\n";
            }
            mail("bradf@121customerinsight.co.uk", "Trackvia Update Error", $message, $this->headers);
        }
    }

    //Get the record data
    private function get_record($urn)
    {
        $record = $this->Trackvia_model->get_record($urn);
        $this->load->model('Records_model');
        $record['comments'] = $this->Records_model->get_last_comment($urn);

        return $record;
    }

    public function get_urn_from_ghs()
    {
        $this->db->where(array("c1" => $this->input->post('ghsurn')));
        echo trim($this->db->get("record_details")->row()->urn);
    }

    public function review_required()
    {
        $urn = $this->input->post('urn');
        //Get the record data
        $this->db->where(array("urn" => $urn));
        //if the record has TV id then we can update or we need to create it
        if ($this->db->get("client_refs")->num_rows()) {
            $this->update_tv_record($urn);
        } else {
            $this->add_tv_record($urn);
        }
    }


    /**
     * Add a trackvia record
     */
    public function add_tv_record($urn)
    {
        if ($this->input->post('urn')) {
            $urn = $this->input->post('urn');
        }
        $tv_tables = $this->tv_tables;
        $tv_table = $tv_tables['GHS Private'];
        $data = $this->get_record_array($urn);
        unset($data['client_ref']);
        print_r($data);
        print_r($tv_table);


        //Update the record
        $response = $this->tv->addRecord($tv_table, $data);
        print_r($response);
        if (!empty($response)) {
            $new_client_ref = $response['records'][0]['id'];
            $data = array("urn" => $urn,
                "client_ref" => $new_client_ref
            );
            $this->Trackvia_model->add_client_refs($data);
            echo json_encode(array("success" => true, "msg" => $response));
        } else {
            $message = "  An error occured when adding a new trackvia record \r\n";
            $message .= "  URN: $urn \r\n";
            $message .= "  Table ID: " . $tv_table . " \r\n";
            $message .= "  Sent Data \r\n";
            foreach ($data as $k => $v) {
                $message .= "  $k: $v \r\n";
            }
            mail("bradf@121customerinsight.co.uk", "Trackvia Update Error", $message, $this->headers);
            echo json_encode(array("success" => true, "msg" => $response['messsage']));
        }
    }


    public function update_tv_record($urn)
    {
        if ($this->input->post('urn')) {
            $urn = $this->input->post('urn');
        }
        //Get the record data
        $data = $this->get_record_array($urn);
        $client_ref = $data['client_ref'];
        unset($data['client_ref']);
        $response = $this->tv->updateRecord($client_ref, $data);
        if (!empty($response)) {
            echo json_encode(array("success" => true, "response" => $response, "ref" => $client_ref, "data" => $data));
        } else {
            $message = "  An error occured when updating a trackvia record  \r\n";
            $message .= "  URN: $urn \r\n";
            $message .= "  Client ref: " . $client_ref . "  \r\n";
            $message .= "  Sent Data  \r\n";
            foreach ($data as $k => $v) {
                $message .= "  $k: $v  \r\n";
            }
            mail("bradf@121customerinsight.co.uk", "Trackvia Update Error", $message, $this->headers);
            echo json_encode(array("success" => true, "msg" => $response['messsage']));
        }
    }

    public function get_record_array($urn)
    {
        $record = $this->Trackvia_model->get_record_rows($urn);
        $mobile = "";
        $landline = "";
        $alt_mob = "";
        foreach ($record as $k => $row) {
            $details = $row;
            if (!preg_match('/^447|^\+447^00447|^07/', $row['telephone_number']) && $row['telephone_number'] <> '01228819810') {
                $landline = $row['telephone_number'];
            }
            if ($row['description'] == "Mobile" || preg_match('/^447|^\+447^00447|^07/', $row['telephone_number'])) {
                if (empty($mobile)) {
                    $mobile = $row['telephone_number'];
                } else {
                    $alt_mob = $row['telephone_number'];
                }
            }
            $add1 = trim(preg_replace('/[0-9]/', '', $row['add1']));
            $house_number = trim(preg_replace('/[a-zA-Z]/', '', $row['add1']));
        }
        $data = array("Date of Enquiry" => date('Y-m-d') . "T12:00:00-0600");
        $data['client_ref'] = $details['client_ref'];
        if (!empty($alt_mob)) {
            $data["Alternative Contact (Mobile)"] = $alt_mob;
        }
        if (!empty($details['a2'])) {
            $data["Owner / Rented"] = $details['a2'];
        }
        if (!empty($details['a6'])) {
            $data["Is the property mortgaged"] = $details['a6'];
        }
        if (!empty($details['a7'])) {
            $data["Who is the Mortgage provider"] = htmlentities($details['a7']);
        }
        if (!empty($details['fullname'])) {
            $data["Owner / Tenant Name 1"] = $details['fullname'];
        }
        if (!empty($details['a4'])) {
            $data["Is ownership in Joint Names"] = $details['a4'];
        }
        if (!empty($details['a5'])) {
            $data["Owner / Tenant Name 2"] = $details['a5'];
        }
        if (!empty($landline)) {
            $data["Primary Contact (Landline)"] = $landline;
        }
        if (!empty($details['email'])) {
            $data["Email address"] = $details['email'];
        }
        if (!empty($house_number)) {
            $data["House No."] = $house_number;
        }
        if (!empty($add1)) {
            $data["Address 1"] = $add1;
        }
        if (!empty($details['add2'])) {
            $data["Address 2"] = $details['add2'];
        }
        if (!empty($details['add3'])) {
            $data["City"] = $details['add3'];
        }
        if (!empty($details['postcode'])) {
            $data["PostCode"] = $details['postcode'];
        }
        if (!empty($details["c3"])) {
            $data["Enquiry Type"] = $details["c3"];
        }
        if (!empty($details['date_added'])) {
            $data["Date of Enquiry"] = date('Y-m-d', strtotime($details['date_added'])) . "T12:00:00-0600";
        }
        if (!empty($details['a8'])) {
            $data["Where did you hear about us"] = $details['a8'];
        }
        if (!empty($details['a1'])) {
            $data["Asset Type"] = $details['a1'];
        }
        if (!empty($details['a9'])) {
            $data["If Other Mortgage Provider, please Input"] = $details['a9'];
        }
        if (!empty($mobile)) {
            $data["Primary Contact (Mobile)"] = $mobile;
        }
        if (!empty($details['c2'])) {
            $data["Referred by"] = $details['c2'];
        }
		if (!empty($details['d1'])) {
            $data["Date of Enquiry"] = $details['d1'];
        }
        //GHS data pot - this cod eis for the New lead pot. ie the 500 solar records bought by 121
        if ($details['pot_id'] == 55) {
            $data["Data Source"] = "CC-121Set1-IB";
        }
        return $data;

    }


    public function find_dupes()
    {
        $table = $this->uri->segment(3);
        $field1 = $this->uri->segment(4);
        $field2 = $this->uri->segment(5);
        $field3 = $this->uri->segment(6);
        $concat = array();
        if (!empty($field1)) {
            $concat[] = $field1;
        }
        if (!empty($field2)) {
            $concat[] = $field2;
        }
        if (!empty($field3)) {
            $concat[] = $field3;
        }


        $fields = implode(",", $concat);
        $query = "SELECT urn, concat( $fields ) ref , count( * ) count
FROM `$table` left join contacts using(contact_id) left join records using(urn) where campaign_id in(22,28,29,32)
GROUP BY concat( $fields )
HAVING count( concat( $fields ) ) >1";
        $result = $this->db->query($query)->result_array();
        foreach ($result as $row) {
            echo $row['urn'];
            echo "<br>";
            $remove = $row['count'] - 1;
            echo $delete = "delete from $table where concat($fields) = '" . addslashes($row['ref']) . "' and urn in(select urn from client_refs where client_ref is null) limit $remove";
            echo ";<br>";
        }
    }

public function update_contact_names(){
	//get all contacts without phone numbers or with no names
	$qry = "SELECT urn,client_ref,contact_id
FROM client_refs
JOIN records
USING ( urn )
JOIN campaigns
USING ( campaign_id )
LEFT JOIN contacts
USING ( urn )
WHERE fullname = ''
and client_id = 12
";
	
	//loop through each record and get the updated trackvia info
	foreach($this->db->query($qry)->result_array() as $row){
			if(!in_array($row['urn'],$_SESSION['checked_names'])){
		$response = $this->tv->getRecord($row['client_ref']);
		$fields = $response['fields'];
		$name = addslashes($fields['Owner / Tenant Name 1']);
		if(!empty($name)){
		$this->db->where("contact_id",$row['contact_id']);
		$this->db->update("contacts",array("fullname"=>$name));
		}
		$_SESSON['checked_names'][]=$row['urn'];
			}
			
	}
}

public function update_contact_telephone(){
	//get all contacts without phone numbers or with no names
	$qry = "SELECT urn,client_ref,contact_id
FROM client_refs
JOIN records
USING ( urn )
JOIN campaigns
USING ( campaign_id )
LEFT JOIN contacts
USING ( urn )
left join contact_telephone ct using(contact_id)
where pot_id = 46 and client_id = 12 group by ct.contact_id having count(ct.contact_id) = 1 limit 25
";
	
	//loop through each record and get the updated trackvia info
	foreach($this->db->query($qry)->result_array() as $row){
		
		if(!in_array($row['urn'],$_SESSION['checked_nums'])){
		$response = $this->tv->getRecord($row['client_ref']);
		$fields = $response['fields'];
		$landline = preg_replace("/[^0-9]/", "", $fields['Primary Contact (Landline)']);
		$mobile = preg_replace("/[^0-9]/", "", $fields['Primary Contact (Mobile)']);
		$alt = preg_replace("/[^0-9]/", "", $fields['Alternative Contact (Mobile)']);
		if(!empty($alt)){
		$this->db->insert("contact_telephone",array("contact_id"=>$row['contact_id'],"telephone_number"=>$landline,"description"=>"Other"));
		}
		
		if(!empty($landline)){
		$this->db->insert("contact_telephone",array("contact_id"=>$row['contact_id'],"telephone_number"=>$landline,"description"=>"Landline"));
		}
			if(!empty($mobile)){
		$this->db->insert("contact_telephone",array("contact_id"=>$row['contact_id'],"telephone_number"=>$mobile,"description"=>"Mobile"));
		}
		$_SESSION['checked_nums'][]=$row['urn'];
		}
	}
}
}

