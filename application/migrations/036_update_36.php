<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_36 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 36");
		$this->db->query("ALTER TABLE `appointment_types` DROP `campaign_id`");
		$this->db->query("ALTER TABLE `appointment_types` ADD `is_default` tinyint(1) NOT NULL DEFAULT '0'");
		$this->db->query("UPDATE `appointment_types` SET `is_default` = '1' WHERE `appointment_types`.`appointment_type_id` =1");
		$this->db->query("UPDATE `appointment_types` SET `is_default` = '1' WHERE `appointment_types`.`appointment_type_id` =2");
		$this->db->query("ALTER TABLE `appointment_types` ADD `icon` VARCHAR( 50 ) NOT NULL");
		$this->db->query("ALTER TABLE `export_forms` CHANGE `order_by` `order_by` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `campaign_appointment_types` (
  `campaign_id` int(11) NOT NULL,
  `appointment_type_id` int(11) NOT NULL,
  UNIQUE KEY `campaign_id` (`campaign_id`,`appointment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
		
	}
	
}
?>