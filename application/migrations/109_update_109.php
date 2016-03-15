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
 $check = $this->db->query("SHOW COLUMNS FROM `custom_panels` LIKE 'linked_appointment_type_ids'");
        if(!$check->num_rows()){
	$this->db->query("ALTER TABLE `custom_panels` ADD `linked_appointment_type_ids` VARCHAR( 10 ) NULL DEFAULT NULL AFTER `table_class`");	
		}
		
		$check = $this->db->query("SHOW COLUMNS FROM `custom_panel_fields` LIKE 'is_appointment_id'");
        if(!$check->num_rows()){
	$this->db->query("ALTER TABLE `custom_panel_fields` ADD `is_appointment_id` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `custom_panel_id`");	
		}
		
		
	}

    public function down()
    {
		
	}
}