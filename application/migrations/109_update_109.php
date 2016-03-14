<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_109 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()

    {
				 $check = $this->db->query("SHOW COLUMNS FROM `custom_panel_data` LIKE 'appointment_id'");
        if(!$check->num_rows()){
        $this->db->query("ALTER TABLE `custom_panel_data` ADD `appointment_id` INT NULL DEFAULT NULL AFTER `urn`");
		}
		
		 $this->db->query("INSERT IGNORE INTO `permissions` (
`permission_id` ,
`permission_name` ,
`permission_group` ,
`description`
)
VALUES (
NULL , 'add custom data', 'System', 'The user can add new custom data entries into the dynamic panels'
), (
NULL , 'edit custom data', 'System', 'The user can edit custom data entries into the dynamic panels'
)");

		
	}

    public function down()
    {
		
	}
}