<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_9 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->db->query("ALTER TABLE uk_postcodes DROP PRIMARY KEY");
		$this->db->query("ALTER TABLE `uk_postcodes` ADD UNIQUE(`postcode`)");
		 $this->db->query("ALTER TABLE `uk_postcodes` ADD `postcode_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
		  $this->db->query("ALTER TABLE `appointments` ADD `location_id` INT NULL DEFAULT NULL AFTER `postcode`, ADD INDEX (`location_id`) ");
		   $this->db->query("ALTER TABLE `company_addresses` ADD `location_id` INT NULL DEFAULT NULL AFTER `postcode`, ADD INDEX (`location_id`) ");
		   $this->db->query("ALTER TABLE `contact_addresses` ADD `location_id` INT NULL DEFAULT NULL AFTER `postcode`, ADD INDEX (`location_id`)  ");
		  
		 $this->db->query("CREATE TABLE IF NOT EXISTS `locations` (
  `location_id` int(11) NOT NULL,
  `lat` float DEFAULT NULL,
  `lng` float DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  KEY `lat` (`lat`,`lng`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    }

    public function down()
    {
        
    }

}