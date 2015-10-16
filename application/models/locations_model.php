	<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*  This page contains functiosn to populate dropdown menus on forms and filters. The queries simply return each id and value in the table in the format id=>name */
class Locations_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }
    
    /* 
	This function just validates a postcode and sets the location ids on all the required tables. If a location_id does not exist, it uses google to get the co-ordinates and then adds a new row to both the uk postcode table and the location table and then sets the location ID from the newly generated row.
	*/
    public function set_location_id($postcode)
    {
        $formatted_postcode = postcodeCheckFormat($postcode);
        if (!empty($formatted_postcode)) {
            $check = "select id,latitude lat,longitude lng from uk_postcodes.PostcodeIo where postcode = '$formatted_postcode'";
            if ($this->db->query($check)->num_rows()) {
                $row = $this->db->query($check)->row_array();
                $qry = "insert ignore into locations set location_id = '{$row['id']}',lat = '{$row['lat']}',lng = '{$row['lng']}'";
                $this->db->query($qry);
                $this->set_postcode_ids($formatted_postcode, $row['id']);
            } else {
                $response = postcode_to_coords($postcode);
                if (!isset($response['error']) && isset($response['lng'])) {
                    $this->db->query("insert ignore into uk_postcodes.PostcodeIo set postcode='$formatted_postcode',lat = '{$response['lat']}',lng = '{$response['lng']}'");
                    $id = $this->db->insert_id();
					$this->db->query("insert ignore into locations set location_id = '$id',lat = '{$response['lat']}',lng = '{$response['lng']}'");
                    $this->set_postcode_ids($formatted_postcode, $id); 
                }
            }
        } else {
		//postcode is invalid
		return false;	
		}
    }
    
    public function set_postcode_ids($postcode, $id)
    {
        $this->db->query("update contact_addresses set location_id = $id where postcode = '$postcode'");
        $this->db->query("update company_addresses set location_id =$id where postcode = '$postcode'");
        $this->db->query("update appointments set location_id = $id where postcode = '$postcode'");
		$this->db->query("update record_planner set location_id = $id where postcode = '$postcode'");
    }
    
    
}