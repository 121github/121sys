<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_28 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 28");

        //Add the appointment rule reasons table
        $this->db->query("CREATE TABLE IF NOT EXISTS `appointment_rule_reasons` (
            `reason_id` INT NOT NULL AUTO_INCREMENT,
            `reason` VARCHAR(255) NULL DEFAULT '0',
            PRIMARY KEY (`reason_id`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Insert reasons
        $this->db->query("INSERT ignore INTO `appointment_rule_reasons` (`reason_id`,`reason`) VALUES (1, 'Holiday')");
        $this->db->query("INSERT ignore INTO `appointment_rule_reasons` (`reason_id`,`reason`) VALUES (2, 'Bank holiday')");
        $this->db->query("INSERT ignore INTO `appointment_rule_reasons` (`reason_id`,`reason`) VALUES (3, 'Other')");

        //Add the appointment rules table
        $this->db->query("CREATE TABLE IF NOT EXISTS `appointment_rules` (
            `appointment_rules_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL DEFAULT '0',
            `reason_id` INT NOT NULL DEFAULT '0',
            `other_reason` VARCHAR(255) NULL DEFAULT '0',
            `block_day` DATE NULL,
            PRIMARY KEY (`appointment_rules_id`),
            CONSTRAINT `appointment_rules_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `appointment_rules_reason_id` FOREIGN KEY (`reason_id`) REFERENCES `appointment_rule_reasons` (`reason_id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

    }
    public function down()
    {
        $this->db->query("DELETE FROM `appointment_rule_reasons` WHERE `reason` IN ('Holiday')");
        $this->db->query("DELETE FROM `appointment_rule_reasons` WHERE `reason` IN ('Bank holiday')");
        $this->db->query("DELETE FROM `appointment_rule_reasons` WHERE `reason` IN ('Other')");
	}
    
}