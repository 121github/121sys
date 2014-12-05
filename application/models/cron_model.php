	<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*  This page contains functiosn to populate dropdown menus on forms and filters. The queries simply return each id and value in the table in the format id=>name */
class Cron_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }
    
    public function clear_hours()
    {
        $this->db->query("truncate table hours_logged");
    }
    
    public function update_hours($agents)
    {
        foreach ($agents as $agent) {
            $qry   = "select sum(TIME_TO_SEC(TIMEDIFF(if(end_time is null,now(),end_time),start_time))) duration,campaign_id from hours_logged where user_id = '{$agent['id']}' and date(`start_time`)=curdate()  group by campaign_id having duration > 1";
            $query = $this->db->query($qry);
            if ($query->num_rows()) {
                
                $campaigns = $query->result_array();
                foreach ($campaigns as $row) {
                    $qry    = "update hours set duration = '{$row['duration']}',`date`=now() where user_id = '{$agent['id']}' and campaign_id = {$row['campaign_id']}";
                    $update = $this->db->query($qry);
                    if ($this->db->affected_rows() == 0) {
                        $qry    = "insert into hours set duration = '{$row['duration']}',user_id = '{$agent['id']}',campaign_id = {$row['campaign_id']},exception =0,`date`=now()";
                        $insert = $this->db->query($qry);
                    }
                }
            }
        }
    }
    
    public function get_renewald_date_field($campaign_id)
    {
        
        $qry = "select field
                from record_details_fields
                where field_name = 'Renewal Date'
                and campaign_id = " . $campaign_id;
        
        $result = $this->db->query($qry)->result_array();
        return $result;
    }
    
    public function set_daily_ration_records($campaign_id, $renewal_date_field, $daily_data, $min_quote_days, $max_quote_days)
    {
        
        $where = "";
        if ($min_quote_days) {
            $where .= " and rd." . $renewal_date_field . " >= DATE_ADD(CURDATE(),INTERVAL " . $min_quote_days . " DAY)";
        }
        if ($max_quote_days) {
            $where .= " and rd." . $renewal_date_field . " <= DATE_ADD(CURDATE(),INTERVAL " . $max_quote_days . " DAY)";
        }
        if (!$min_quote_days and !$max_quote_days) {
            $where .= "0";
        }
        
        $qry = "update records
                set parked_code = null where urn in
                    (select * from
                      (select r.urn
                        from records r
                        inner join record_details rd ON (r.urn = rd.urn)
                        where rd." . $renewal_date_field . " is not null
                        and r.parked_code = 1
                        and r.campaign_id = " . $campaign_id . $where . "
                        limit " . $daily_data . ")
                    as urn)";
        
        $update = $this->db->query($qry);
        
        return $this->db->affected_rows();
    }
    
    public function update_locations_table()
    {
        $file = dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/location_progress.txt";
        //1. add company locations
        $this->db->select("postcode,company_id");
        $this->db->join("locations", "locations.location_id=company_addresses.location_id", "LEFT");
        $this->db->where("locations.location_id is null and postcode is not null");
        $postcodes = $this->db->get("company_addresses")->result_array();
        $status    = "Company Postcodes found: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode']);
            //check valid format
            $formatted_postcode = postcodeCheckFormat($row['postcode']);
            if ($formatted_postcode == NULL) {
                $qry = "update company_addresses set postcode = null where company_id = '{$row['company_id']}'";
                $this->db->query($qry);
            } else {
                $qry = "insert into locations (select postcode_id,lat,lng from uk_postcodes where postcode = '$formatted_postcode')";
                $this->db->query($qry);
            }
        }
        
        //2. insert all contact locations
        $this->db->select("postcode,contact_id");
        $this->db->join("locations", "locations.location_id=contact_addresses.location_id", "LEFT");
        $this->db->where("locations.location_id is null and postcode is not null");
        $postcodes = $this->db->get("contact_addresses")->result_array();
        $status .= "Contact Postcodes found: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            //check valid format
            $formatted_postcode = postcodeCheckFormat($row['postcode']);
            if ($formatted_postcode == NULL) {
                $qry = "update contact_addresses set postcode = null where contact_id = '{$row['contact_id']}'";
                $this->db->query($qry);
            } else {
                $qry = "insert into locations (select postcode_id,lat,lng from uk_postcodes where postcode = '$formatted_postcode')";
                $this->db->query($qry);
            }
        }
        
        
        //3. insert all appointment location
        $this->db->select("postcode,appointment_id");
        $this->db->join("locations", "locations.location_id=appointments.location_id", "LEFT");
        $this->db->where("locations.location_id is null and postcode is not null");
        $postcodes = $this->db->get("appointments")->result_array();
        $status .= "Appointment Postcodes found: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            //check valid format
            $formatted_postcode = postcodeCheckFormat($row['postcode']);
            if ($formatted_postcode == NULL) {
                $qry = "update appointments set postcode = null where appointment_id = '{$row['appointment_id']}'";
                $this->db->query($qry);
            } else {
                $qry = "insert into locations (select postcode_id,lat,lng from uk_postcodes where postcode = '$formatted_postcode')";
                $this->db->query($qry);
            }
        }
        
    }
    public function update_location_ids()
    {
		 $file = dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/location_progress.txt";
        //1.1 update location ids
        $qry       = "select postcode from company_addresses where location_id is null and postcode is not null";
        $postcodes = $this->db->query($qry)->result_array();
        $status = "NULL Company IDs found: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            $this->db->query("update company_addresses set location_id = (select postcode_id from uk_postcodes where postcode = '{$row['postcode']}') where postcode = '{$row['postcode']}'");
        }
        
        //2.1 update location ids
        $qry       = "select postcode from contact_addresses where location_id is null and postcode is not null";
        $postcodes = $this->db->query($qry)->result_array();
        $status .= "NULL Contact IDs found: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            $this->db->query("update contact_addresses set location_id = (select postcode_id from uk_postcodes where postcode = '{$row['postcode']}') where postcode = '{$row['postcode']}'");
        }
        
        
        //3.1 update location ids
        $qry       = "select postcode from appointments where location_id is null and postcode is not null";
        $postcodes = $this->db->query($qry)->result_array();
        $status .= "NULL Appointment IDs found: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            $this->db->query("update appointments set location_id = (select postcode_id from uk_postcodes where postcode = '{$row['postcode']}') where postcode = '{$row['postcode']}'");
        }
        
    }
    
    
    public function update_locations_with_google()
    {
         $file = dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/location_progress.txt";
        //1.2 use google to update the rest
        $this->db->select("postcode,company_id");
        $this->db->join("locations", "locations.location_id=company_addresses.location_id", "LEFT");
        $this->db->where("locations.location_id is null and postcode is not null");
        $postcodes = $this->db->get("company_addresses")->result_array();
        $status = "Company Postcodes found [google search]: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            $response = postcode_to_coords($row['postcode']);
            file_put_contents($file, $status . $response['lat']);
            if (!isset($response['error'])) {
                $this->db->query("insert into uk_postcodes set lat = '{$response['lat']}',lng = '{$response['lng']}'");
            }
        }
        
        
        
        //2.2 use google to update the rest
        $this->db->select("postcode,contact_id");
        $this->db->join("locations", "locations.location_id=contact_addresses.location_id", "LEFT");
        $this->db->where("locations.location_id is null and postcode is not null");
        $postcodes = $this->db->get("contact_addresses")->result_array();
        $status .= "Contact Postcodes found [google search]: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            $response = postcode_to_coords($row['postcode']);
			  file_put_contents($file, $status . $response['lat']);
            if (!isset($response['error'])) {
                $this->db->query("insert into uk_postcodes set lat = '{$response['lat']}',lng = '{$response['lng']}'");
            }
        }
        
        //3.2 use google to update the rest
        $this->db->select("postcode,contact_id");
        $this->db->join("locations", "locations.location_id=appointments.location_id", "LEFT");
        $this->db->where("locations.location_id is null and postcode is not null");
        $postcodes = $this->db->get("appointments")->result_array();
        $status .= "Appointment Postcodes found [google search]: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            $response = postcode_to_coords($row['postcode']);
			  file_put_contents($file, $status . $response['lat']);
            if (!isset($response['error'])) {
                $this->db->query("insert into uk_postcodes set lat = '{$response['lat']}',lng = '{$response['lng']}'");
            }
        }
        
    }
    
    
}