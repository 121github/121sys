<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_152 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model'); 
    }

    public function up()
    {
		$this->db->query("UPDATE contact_addresses co SET description = 'Survey Address' WHERE description = 'Address'");
		
		$this->db->query("UPDATE contact_addresses co SET description = 'Survey Address' WHERE description = 'Surveying Address'");
     
	 $this->db->query("UPDATE contacts SET `primary` =0 WHERE `primary` IS NULL ");
	 $this->db->query("ALTER TABLE `contacts` CHANGE `primary` `primary` TINYINT( 1 ) NOT NULL DEFAULT '0' ");
	}
	
}