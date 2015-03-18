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
    
    public function set_daily_ration_renewals($campaign_id, $renewal_date_field, $daily_data, $min_quote_days, $max_quote_days)
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
        
		//first we set them all out of date
		$qry = "update records r left join record_details rd using(urn)
                set parked_code = 6 where r.campaign_id = " . $campaign_id ." and (r.parked_code is null or r.parked_code=1)";
		$this->db->query($qry);
		
		//now we set all records that are in date to rationed	
        $qry = "update records
                set parked_code = 1 where urn in
                    (select * from
                      (select r.urn
                        from records r
                        inner join record_details rd ON (r.urn = rd.urn)
                        where rd." . $renewal_date_field . " is not null
                        and (r.parked_code is null or r.parked_code=6)
                        and r.campaign_id = " . $campaign_id . $where . ")
                    as urn)";
		
		//now we unpark the daily amount that are rationed so they are available for calling		
        $qry = "update records
                set parked_code = null where urn in
                    (select * from
                      (select r.urn
                        from records r
                        inner join record_details rd ON (r.urn = rd.urn)
                        where rd." . $renewal_date_field . " is not null
                        and r.parked_code = 1
						and r.record_status = 1
                        and r.campaign_id = " . $campaign_id . $where . "
                        limit " . $daily_data . ")
                    as urn)";
        
        $update = $this->db->query($qry);
        
        return $this->db->affected_rows();
    }
	
	  public function set_daily_ration_records($campaign_id, $daily_data)
    {
		//if there is no renewal date we dont need to remove any records, we just add in the daily amount	
        $qry = "update records
                set parked_code = null where urn in
                    (select * from
                      (select r.urn
                        from records r
                        inner join record_details rd ON (r.urn = rd.urn)
                        where r.parked_code = 1 and r.record_status = 1
                        and r.campaign_id = " . $campaign_id . "
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
		$this->firephp->log("Find all company postcodes without a location ID:" . $this->db->last_query());
        $status    = "Company Postcodes found: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            //check valid format
            $formatted_postcode = postcodeCheckFormat($row['postcode']);
			 file_put_contents($file, $formatted_postcode . ": " . $row['postcode'] . "\r\n");
            if ($formatted_postcode == NULL) {
                $qry = "update company_addresses set postcode = null where company_id = '{$row['company_id']}'";
                $this->db->query($qry);
            } else {
				$qry = "update company_addresses set postcode = '$formatted_postcode' where company_id = '{$row['company_id']}'";
                $this->db->query($qry);
            }
        }
        
        //2. insert all contact locations
        $this->db->select("postcode,contact_id");
        $this->db->join("locations", "locations.location_id=contact_addresses.location_id", "LEFT");
        $this->db->where("locations.location_id is null and postcode is not null");
        $postcodes = $this->db->get("contact_addresses")->result_array();
		$this->firephp->log("Find all contact postcodes without a location ID:" . $this->db->last_query());
        $status .= "Contact Postcodes found: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            //check valid format
            $formatted_postcode = postcodeCheckFormat($row['postcode']);
			  file_put_contents($file, $formatted_postcode . ": " . $row['postcode'] . "\r\n");
            if ($formatted_postcode == NULL) {
                $qry = "update contact_addresses set postcode = null where contact_id = '{$row['contact_id']}'";
                $this->db->query($qry);
            } else {
				$qry = "update contact_addresses set postcode = '$formatted_postcode' where contact_id = '{$row['contact_id']}'";
                $this->db->query($qry);
            }
        }
        
        
        //3. insert all appointment location
        $this->db->select("postcode,appointment_id");
        $this->db->join("locations", "locations.location_id=appointments.location_id", "LEFT");
        $this->db->where("locations.location_id is null and postcode is not null");
        $postcodes = $this->db->get("appointments")->result_array();
        $status .= "Appointment Postcodes found: " . count($postcodes) . "\r\n";
		$this->firephp->log($status);
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            //check valid format
            $formatted_postcode = postcodeCheckFormat($row['postcode']);
			 file_put_contents($file, $formatted_postcode . ": " . $row['postcode'] . "\r\n");
            if ($formatted_postcode == NULL) {
                $qry = "update appointments set postcode = null where appointment_id = '{$row['appointment_id']}'";
                $this->db->query($qry);
            } else {
				$qry = "update appointments set postcode = '$formatted_postcode' where appointment_id = '{$row['appointment_id']}'";
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
		$this->firephp->log($status);
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
			$qry = "select postcode_id,lat,lng from uk_postcodes where postcode = '{$row['postcode']}'";
			if($this->db->query($qry)->num_rows()){
            $pc = $this->db->query($qry)->row_array();
			$q1 = "insert ignore into locations set location_id='{$pc['postcode_id']}',lat='{$pc['lat']}',lng='{$pc['lng']}'";
			$this->firephp->log($q1);
			$this->db->query($q1);	
			$q2 = "update company_addresses set location_id = {$pc['postcode_id']} where postcode = '{$row['postcode']}'";
            $this->firephp->log($q2);
			$this->db->query($q2);
			}
        }
        
        //2.1 update location ids
        $qry       = "select postcode from contact_addresses where location_id is null and postcode is not null";
        $postcodes = $this->db->query($qry)->result_array();
        $status .= "NULL Contact IDs found: " . count($postcodes) . "\r\n";
		$this->firephp->log($status);
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
			$qry = "select postcode_id,lat,lng from uk_postcodes where postcode = '{$row['postcode']}'";
			if($this->db->query($qry)->num_rows()){
   $pc = $this->db->query($qry)->row_array();	
						$this->db->query("insert ignore into locations set location_id = {$pc['postcode_id']},lat='{$pc['lat']}',lng='{$pc['lng']}'");
            $this->db->query("update contact_addresses set location_id = {$pc['postcode_id']} where postcode = '{$row['postcode']}'");
			}
        }
        
        
        //3.1 update location ids
        $qry       = "select postcode from appointments where location_id is null and postcode is not null";
        $postcodes = $this->db->query($qry)->result_array();
        $status .= "NULL Appointment IDs found: " . count($postcodes) . "\r\n";
		$this->firephp->log($status);
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
           	$qry = "select postcode_id,lat,lng from uk_postcodes where postcode = '{$row['postcode']}'";
			if($this->db->query($qry)->num_rows()){
   $pc = $this->db->query($qry)->row_array();
			$this->db->query("insert ignore into locations set location_id = {$pc['postcode_id']},lat='{$pc['lat']}',lng='{$pc['lng']}'");
            $this->db->query("update appointments set location_id = {$pc['postcode_id']} where postcode = '{$row['postcode']}'");
        }
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
		$this->firephp->log($status);
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            $response = postcode_to_coords($row['postcode']);
            
            if (!isset($response['error'])) {
				file_put_contents($file, $status . $response['lat']);
                $this->db->query("insert ignore into uk_postcodes set postcode='{$row['postcode']}',lat = '{$response['lat']}',lng = '{$response['lng']}'");
            }
        }
        
        
        
        //2.2 use google to update the rest
        $this->db->select("postcode,contact_id");
        $this->db->join("locations", "locations.location_id=contact_addresses.location_id", "LEFT");
        $this->db->where("locations.location_id is null and postcode is not null");
        $postcodes = $this->db->get("contact_addresses")->result_array();
        $status .= "Contact Postcodes found [google search]: " . count($postcodes) . "\r\n";
		$this->firephp->log($status);
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            $response = postcode_to_coords($row['postcode']);
			 
            if (!isset($response['error'])) {
				 file_put_contents($file, $status . $response['lat']);
                $this->db->query("insert ignore into uk_postcodes set postcode='{$row['postcode']}',lat = '{$response['lat']}',lng = '{$response['lng']}'");
            }
        }
        
        //3.2 use google to update the rest
        $this->db->select("postcode,appointment_id");
        $this->db->join("locations", "locations.location_id=appointments.location_id", "LEFT");
        $this->db->where("locations.location_id is null and postcode is not null");
        $postcodes = $this->db->get("appointments")->result_array();
        $status .= "Appointment Postcodes found [google search]: " . count($postcodes) . "\r\n";
		$this->firephp->log($status);
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            file_put_contents($file, $status . ": " . $row['postcode'] . "\r\n");
            $response = postcode_to_coords($row['postcode']);
			 
            if (!isset($response['error'])) {
				 file_put_contents($file, $status . $response['lat']);
                $this->db->query("insert ignore into uk_postcodes set postcode='{$row['postcode']}',lat = '{$response['lat']}',lng = '{$response['lng']}'");
            }
        }
        
    }
    
    
}