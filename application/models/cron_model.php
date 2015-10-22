<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*  This page contains functiosn to populate dropdown menus on forms and filters. The queries simply return each id and value in the table in the format id=>name */

class Cron_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('uk_postcodes', true);
    }
    public function unassign_owners()
    {
        $this->db->query("select * from ownership where user_id not in(select user_id from users join role_permissions using(role_id) join permissions using(permission_id) where permission_name <> 'keep records') and urn not in (select urn from history join outcomes using(outcome_id)  join records using(urn) where keep_record = 1) and urn in(select urn from records where record_status = 1 and date(date_updated) < curdate())");
    }
    
    public function remove_leavers()
    {
        $this->db->query("update 121sys.users set user_status = 0 where name in(SELECT agent_name FROM attendance.`tbl_employees` where campaign = 'leaver') and group_id = 1");
        $qry = "delete from ownership join users using(user_id) where user_status = 0";
    }
    
    public function unassign_records()
    {
        $qry = "delete from ownership join users using(user_id) where user_status = 0";
        $this->db->query($qry);
        
    }
    
    public function clear_planner()
    {
        //no point having entries in the route planner for days that have lapsed!
        $qry = "update record_planner set planner_status = 0 where date(start_date) < curdate()";
        $this->db->query($qry);
    }
    
    public function clear_hours()
    {
        $this->db->query("truncate table hours_logged");
    }
    
    public function update_hours($agents)
    {
        foreach ($agents as $agent) {
            $qry   = "select sum(TIME_TO_SEC(TIMEDIFF(if(end_time is null,now(),end_time),start_time))) duration,campaign_id from hours_logged where user_id = '{$agent['id']}' and date(`start_time`)=curdate() group by campaign_id having duration > 1";
            $query = $this->db->query($qry);
            if ($query->num_rows()) {
                $campaigns = $query->result_array();
                foreach ($campaigns as $row) {
                    $qry    = "update hours set time_logged = '{$row['duration']}',`date`=curdate() where user_id = '{$agent['id']}' and campaign_id = {$row['campaign_id']} and date(`date`)=curdate()";
                    $update = $this->db->query($qry);
                    if ($this->db->affected_rows() == 0) {
                        $qry    = "insert ignore into hours set time_logged = '{$row['duration']}',user_id = '{$agent['id']}',campaign_id = {$row['campaign_id']},`date`=curdate(),updated_date=now()";
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
                set parked_code = 6 where r.campaign_id = " . $campaign_id . " and (r.parked_code is null or r.parked_code=1)";
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
    
    //this function formats the postcodes or sets them as null if they are found to be invalid
    public function update_address_tables()
    {
        $qry       = "select postcode from company_addresses where location_id is null and postcode is not null limit 1000 union
		select postcode from contact_addresses where location_id is null and postcode is not null limit 1000 union
		select postcode from appointments where location_id is null and postcode is not null limit 1000 union
		select postcode from record_planner where location_id is null and postcode is not null limit 1000 union select postcode from branch_addresses where location_id is null and postcode is not null limit 1000 ";
        $postcodes = $this->db->query($qry)->result_array();
        
        $status = "Postcodes found: " . count($postcodes) . "\r\n";
        file_put_contents($file, $status);
        foreach ($postcodes as $row) {
            //check valid uk format
              if (validate_postcode($row['postcode'])) {
				$formatted_postcode = postcodeFormat($row['postcode']);
				if($formatted_postcode<>$row['postcode']){
                $qry = "update company_addresses set postcode = '$formatted_postcode' where postcode = '{$row['postcode']}'";
                $this->db->query($qry);
                $qry = "update contact_addresses set postcode = '$formatted_postcode' where postcode = '{$row['postcode']}'";
                $this->db->query($qry);
                $qry = "update appointments set postcode = '$formatted_postcode' where postcode = '{$row['postcode']}'";
                $this->db->query($qry);
                $qry = "update record_planner set postcode = '$formatted_postcode' where postcode = '{$row['postcode']}'";
                $this->db->query($qry);
                $qry = "update branch_addresses set postcode = '$formatted_postcode' where postcode = '{$row['postcode']}'";
                $this->db->query($qry);
				}
            } else {
				$qry = "update company_addresses set postcode = null where postcode = '{$row['postcode']}'";
                $this->db->query($qry);
                $qry = "update contact_addresses set postcode = null where postcode = '{$row['postcode']}'";
                $this->db->query($qry);
                $qry = "update appointments set postcode = null where postcode = '{$row['postcode']}'";
                $this->db->query($qry);
                $qry = "update record_planner set postcode = null where postcode = '{$row['postcode']}'";
                $this->db->query($qry);
                $qry = "update branch_addresses set postcode = null where postcode = '{$row['postcode']}'";
                $this->db->query($qry);
            }
        }
        
    }
    
    public function update_location_ids()
    {
        //1.1 update location ids
        $qry       = "select postcode from company_addresses where location_id is null and postcode is not null limit 1000 union
		select postcode from contact_addresses where location_id is null and postcode is not null limit 1000 union
		select postcode from appointments where location_id is null and postcode is not null limit 1000 union
		select postcode from record_planner where location_id is null and postcode is not null limit 1000 union select postcode from branch_addresses where location_id is null and postcode is not null limit 1000 ";
        $postcodes = $this->db->query($qry)->result_array();
		$status = "Postcodes found: " . count($postcodes) . "<br>\r\n";
        $postcode_array = array();
        foreach ($postcodes as $row) {
            if (validate_postcode($row['postcode'])) {
                $postcode_array[$row['postcode']] = postcodeFormat($row['postcode']);
            }
        }
        if (count($postcode_array) > 0) {
            $postcode_list = implode("','", $postcode_array);
        } else {
            $postcode_list = "";
        }
        $qry                = "select id,postcode, latitude lat,longitude lng from uk_postcodes.PostcodeIo where postcode in('$postcode_list')";
        $postcode_locations = $this->db2->query($qry)->result_array();
        
        foreach ($postcode_locations as $pc) {
            $insert_locations = "replace into locations set location_id='{$pc['id']}',lat='{$pc['lat']}',lng='{$pc['lng']}'";
            $this->db->query($insert_locations);
            $company_locations = "update company_addresses set location_id = {$pc['id']} where postcode = '{$pc['postcode']}'";
            $this->db->query($company_locations);
            $contact_locations = "update contact_addresses set location_id = {$pc['id']} where postcode = '{$pc['postcode']}'";
            $this->db->query($contact_locations);
            $appointment_locations = "update appointments set location_id = {$pc['id']} where postcode = '{$pc['postcode']}'";
            $this->db->query($appointment_locations);
            $planner_locations = "update record_planner set location_id = {$pc['id']} where postcode = '{$pc['postcode']}'";
            $this->db->query($planner_locations);
            $planner_locations = "update branch_addresses set location_id = {$pc['id']} where postcode = '{$pc['postcode']}'";
            $this->db->query($planner_locations);
            
        }
    }
    
    public function update_locations_from_api()
    {
        
        $qry       = "select postcode from company_addresses where location_id is null and postcode is not null limit 1000 union select postcode from contact_addresses where location_id is null and postcode is not null limit 1000 union
		select postcode from appointments where location_id is null and postcode is not null limit 1000 union
		select postcode from record_planner where location_id is null and postcode is not null limit 1000 union select postcode from branch_addresses where location_id is null and postcode is not null limit 1000";
        $postcodes = $this->db->query($qry)->result_array();
        echo $status = "Postcodes found: " . count($postcodes) . "<br>\r\n";
        $postcode_array = array();
        foreach ($postcodes as $row) {
            if (validate_postcode($row['postcode'])) {
                $postcode_array[$row['postcode']] = postcodeFormat($row['postcode']);
            }
        }
        foreach ($postcode_array as $pc) {
            if (validate_postcode($pc)) {
                $response = get_postcode_data($pc);
                if (isset($response['latitude'])) {
                    $this->db2->query("insert ignore into uk_postcodes.PostcodeIo set postcode='{$response['postcode']}',latitude = '{$response['latitude']}',longitude = '{$response['longitude']}'");
                }
            }
        }
        $this->update_location_ids();
    }
    
    public function update_locations_with_google()
    {
        $file = dirname($_SERVER['SCRIPT_FILENAME']) . "/datafiles/location_progress.txt";
        
        $qry       = "select postcode from company_addresses where location_id is null and postcode is not null limit 1000 union select postcode from contact_addresses where location_id is null and postcode is not null limit 1000 union
		select postcode from appointments where location_id is null and postcode is not null limit 1000 union
		select postcode from record_planner where location_id is null and postcode is not null limit 1000 union select postcode from branch_addresses where location_id is null and postcode is not null limit 1000";
        $postcodes = $this->db->query($qry)->result_array();
        echo $status = "Postcodes found: " . count($postcodes) . "<br>\r\n";
        $postcode_array = array();
        foreach ($postcodes as $row) {
            if (validate_postcode($row['postcode'])) {
                $postcode_array[$row['postcode']] = postcodeFormat($row['postcode']);
            }
        }
        foreach ($postcode_array as $pc) {
            if (validate_postcode($pc)) {
                $response = postcode_to_coords($pc);
                $postcode = postcodeFormat($pc);
                if (isset($response['lat'])) {
                    $this->db2->query("insert ignore into uk_postcodes.PostcodeIo set postcode='$postcode',latitude = '{$response['lat']}',longitude = '{$response['lng']}'");
                }
            }
        }
        $this->update_location_ids();
    }
    
    /**
     * Get the wrong contact telephone numbers
     */
    public function get_wrong_contact_telephone_numbers()
    {
        $qry = "select contact_id, telephone_id, telephone_number
                  from contact_telephone
                  where
				  	  telephone_number like '04%' or
					  telephone_number like '44%' or
                      telephone_number like '%+%' or
                      telephone_number like '%/%' or
                      telephone_number like '%(%' or
                      telephone_number like '%)%' or
					  telephone_number like '% %' or
                      telephone_number like '%-%' ";
        
        $result = $this->db->query($qry)->result_array();
        
        return $result;
    }
    
    /**
     * Update the contact telephone numbers with the right numbers
     */
    public function update_contact_telephone_numbers($contacts)
    {
        
        $result = $this->db->update_batch("contact_telephone", $contacts, 'telephone_id');
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    /**
     * Get the wrong company telephone numbers
     */
    public function get_wrong_company_telephone_numbers()
    {
        $qry = "select company_id, telephone_id, telephone_number
                  from company_telephone
                  where
				  	  telephone_number like '04%' or
					  telephone_number like '44%' or
                      telephone_number like '%+%' or
                      telephone_number like '%/%' or
                      telephone_number like '%(%' or
                      telephone_number like '%)%' or
					  telephone_number like '% %' or
                      telephone_number like '%-%' ";
        
        $result = $this->db->query($qry)->result_array();
        
        return $result;
    }
    
    /**
     * Update the company telephone numbers with the right numbers
     */
    public function update_company_telephone_numbers($companies)
    {
        
        $this->db->update_batch("company_telephone", $companies, 'telephone_id');
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    /**
     * Check tps by telephone_number.
     *
     * return true if the telephone number exist in the table or false in other case
     */
    public function check_tps_by_telephone_number($telephone_number, $type)
    {
        $qry = "select *
                  from tps
                  where telephone like '%" . $telephone_number . "%'
                  and tps.date_updated >= NOW()-INTERVAL 6 MONTH";
        
        $result = $this->db->query($qry)->result_array();
        
        return $result;
    }
    
    /**
     * Get the contact telephone_numbers
     */
    public function get_no_tps_contact_telephone_numbers_in_tps_table()
    {
        $qry = "select ct.contact_id, ct.telephone_id, ct.telephone_number, tps.tps
                  from contact_telephone ct
                  inner join tps ON (ct.telephone_number = tps.telephone)
                  where ct.tps is null
                  and tps.date_updated >= NOW()-INTERVAL 6 MONTH";
        
        $result = $this->db->query($qry)->result_array();
        
        return $result;
    }
    
    /**
     * Get the company telephone_numbers
     */
    public function get_no_ctps_company_telephone_numbers_in_tps_table()
    {
        $qry = "select ct.company_id, ct.telephone_id, ct.telephone_number, tps.ctps
                  from company_telephone ct
                  inner join tps ON (ct.telephone_number = tps.telephone)
                  where ct.ctps is null
                  and tps.date_updated >= NOW()-INTERVAL 6 MONTH";
        
        $result = $this->db->query($qry)->result_array();
        
        return $result;
    }
    
    /**
     * Add telephone_number to the tps table (if already exist update the date_updated
     */
    public function update_number_to_tps_table($data)
    {
        
        $update = "";
        foreach ($data as $key => $val) {
            $update .= "," . $key . "='" . $val . "'";
        }
        
        $sql = $this->db->insert_string('tps', $data) . ' ON DUPLICATE KEY UPDATE date_updated=NOW()' . $update;
        $this->db->trans_complete();
        
        $result = $this->db->query($sql);
        
        return $result;
    }
    
    /**
     * Suppress the records with this telephone number in their contacts or company details
     */
    public function suppress_records()
    {
        $qry = "update records rec
                  LEFT JOIN companies com USING (urn)
                  LEFT JOIN company_telephone comt USING (company_id)
                  LEFT JOIN contacts con USING (urn)
                  LEFT JOIN contact_telephone cont USING (contact_id)
                  INNER JOIN suppression sup ON (sup.telephone_number = cont.telephone_number or sup.telephone_number = comt.telephone_number)
                  LEFT JOIN suppression_by_campaign supc ON (sup.suppression_id = supc.suppression_id)
                set parked_code = 4
                  WHERE (supc.campaign_id = rec.campaign_id OR supc.campaign_id IS NULL) AND (rec.parked_code <> 4 or rec.parked_code is NULL)";
        
        $update = $this->db->query($qry);
        
        return $this->db->affected_rows();
    }
    
    
}