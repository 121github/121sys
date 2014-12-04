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
    //functions to update missing postcodes from google api
    public function get_missing_contact_postcodes()
    {
        return $this->db->query("select postcode,contact_id id from contact_addresses left join uk_postcodes using(postcode) where uk_postcodes.lat is null")->result_array();
    }
    public function get_missing_company_postcodes()
    {
        return $this->db->query("select postcode,company_id id from company_addresses left join uk_postcodes using(postcode) where uk_postcodes.lat is null")->result_array();
    }
    public function update_missing($postcode, $location)
    {
        return $this->db->query("insert into uk_postcodes set postcode = '$postcode',lat='" . $location['lat'] . "',lng='" . $location['lng'] . "'");
        
    }
    //functions to format postcodes without spaces
    public function get_unformatted_company_postcodes()
    {
        return $this->db->query("select postcode,company_id id from company_addresses where postcode not like '% %' and postcode is not null")->result_array();
    }
    public function get_unformatted_contact_postcodes()
    {
        return $this->db->query("select postcode,contact_id id from contact_addresses where postcode not like '% %' and postcode is not null")->result_array();
    }
	    public function get_unformatted_appointment_postcodes()
    {
        return $this->db->query("select postcode,appointment_id id from appointments where postcode not like '% %' and postcode is not null")->result_array();
    }
    public function format_company_postcode($postcode, $id)
    {
        return $this->db->query("update company_addresses set postcode = '$postcode' where company_id = '$id'");
    }
    public function format_contact_postcode($postcode, $id)
    {
        return $this->db->query("update contact_addresses set postcode = '$postcode' where contact_id = '$id'");
    }
	    public function format_appointment_postcode($postcode, $id)
    {
        return $this->db->query("update appointments set postcode = '$postcode' where appointment_id = '$id'");
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

    public function get_renewald_date_field($campaign_id) {

        $qry = "select field
                from record_details_fields
                where field_name = 'Renewal Date'
                and campaign_id = ".$campaign_id;

        $result = $this->db->query($qry)->result_array();
        return $result[0]['field'];
    }

    public function set_daily_ration_records($campaign_id, $renewal_date_field,$daily_data, $min_quote_days, $max_quote_days) {

        $where = "";
        if ($min_quote_days) {
            $where .= " and rd.".$renewal_date_field." >= DATE_ADD(CURDATE(),INTERVAL ".$min_quote_days." DAY)";
        }
        if ($max_quote_days) {
            $where .= " and rd.".$renewal_date_field." <= DATE_ADD(CURDATE(),INTERVAL ".$max_quote_days." DAY)";
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
                        where rd.".$renewal_date_field." is not null
                        and r.parked_code = 1
                        and r.campaign_id = ".$campaign_id
                        .$where."
                        limit ".$daily_data.")
                    as urn)";

        $update = $this->db->query($qry);

        return $this->db->affected_rows();
    }
}