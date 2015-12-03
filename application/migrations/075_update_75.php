
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_75 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 75");
				$check = $this->db->query("SHOW COLUMNS FROM `appointment_slots` LIKE 'slot_group_id'");
		if(!$check->num_rows()){
$this->db->query("ALTER TABLE `appointment_slots` ADD `slot_group_id` INT NULL AFTER `appointment_slot_id`, ADD INDEX (`slot_group_id`)");

$this->db->query("CREATE TABLE IF NOT EXISTS `appointment_slot_groups` (
  `slot_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `slot_group_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`slot_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

$this->db->query("INSERT INTO `appointment_slot_groups` (`slot_group_id`, `slot_group_name`) VALUES
(1, 'AM/PM'),
(2, 'Hourly'),
(3, 'Morning/Afternoon/Evening'),
(4, 'All day')");

$this->db->query("ALTER TABLE `appointment_slot_groups` ADD UNIQUE(`slot_group_name`)");
	}
	
	$this->db->query("INSERT IGNORE INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'data counts', 'Reports')");

$this->db->query("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) select role_id,(select permission_id from permissions where permission_name = 'data counts') from user_roles");

$this->db->query("INSERT IGNORE INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'slot config', 'Admin')");
$this->db->query("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) select role_id,(select permission_id from permissions where permission_name = 'slot config') from user_roles where role_id < 3");	
	
	}
	
	
	
}