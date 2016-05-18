<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_133 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		$this->db->query("ALTER TABLE `appointments` ADD `address_table` VARCHAR( 20 ) NULL AFTER `address` ,
ADD `address_id` INT NULL AFTER `address_table` ,
ADD INDEX ( `address_id` )");

$this->db->query("ALTER TABLE `appointments` ADD `access_address_id` INT NULL AFTER `access_postcode` ,
ADD `access_address_table` VARCHAR( 20 ) NULL AFTER `access_address_id` ,
ADD INDEX ( `access_address_id` )") ;
	}
	
}