<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_65 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 65");
$this->db->query("ALTER TABLE `locations` CHANGE `lat` `lat` DECIMAL(18,12) NULL DEFAULT NULL");
$this->db->query("ALTER TABLE `locations` CHANGE `lng` `lng` DECIMAL(18,12) NULL DEFAULT NULL");

$this->db->query("insert ignore into uk_postcodes.PostcodeIo (postcode,latitude,longitude) select postcode,lat,lng from uk_postcodes");

$this->db->query("delete from locations");
$this->db->query("update contact_addresses set location_id = null");
$this->db->query("update company_addresses set location_id = null");
$this->db->query("update appointments set location_id = null");
$this->db->query("update record_planner set location_id = null");
$this->load->helper('remotefile');
//run the updater
$path = base_url()."cron/update_location_ids";
$response = loadFile($path);

	}
	
}