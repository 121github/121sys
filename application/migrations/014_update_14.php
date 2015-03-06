<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_14 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 14");
        $this->db->query("ALTER TABLE `park_codes` ADD UNIQUE (`park_reason`)");
        $this->db->query("ALTER TABLE `records` ADD `parked_date` TIMESTAMP NULL DEFAULT NULL AFTER `parked_code`");
        //this trigger makes the parked date update when ever a park code changes on a record
        $this->db->query("CREATE TRIGGER `records_before_update` BEFORE UPDATE ON `records`\r\n
      FOR EACH ROW BEGIN \r\n 
      if new.parked_code or new.parked_code is null then\r\n
        SET NEW.parked_date = CURRENT_TIMESTAMP;\r\n
       end if;\r\n
		END");

        $this->db->query("CREATE TABLE IF NOT EXISTS `audit` (
          `audit_id` int(11) NOT NULL AUTO_INCREMENT,
          `urn` int(11) DEFAULT NULL,
          `table_name` varchar(100) DEFAULT NULL,
          `reference` int(11) DEFAULT NULL,
          `change_type` varchar(20) DEFAULT NULL,
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `user_id` int(11) DEFAULT NULL,
          PRIMARY KEY (`audit_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");


        $this->db->query("CREATE TABLE IF NOT EXISTS `audit_values` (
          `audit_id` int(11) NOT NULL,
          `column_name` varchar(100) DEFAULT NULL,
          `oldval` varchar(255) DEFAULT NULL,
          `newval` varchar(255) DEFAULT NULL,
          UNIQUE KEY `audit_id_2` (`audit_id`,`column_name`),
          KEY `audit_id` (`audit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        #drop trigger records_before_update
        $this->db->query("ALTER TABLE `audit_values` ADD CONSTRAINT `audit_values_ibfk_1` FOREIGN KEY (`audit_id`) REFERENCES `audit` (`audit_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        $this->db->query("ALTER TABLE `companies` CHANGE `company_number` `conumber` INT");
        $this->db->query("ALTER TABLE `files` ADD `email_sent` VARCHAR(50) NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `files` ADD `doc_hash` VARCHAR(50) NULL DEFAULT NULL");

    }

    public function down()
    {
        $this->db->query("ALTER TABLE `companies` CHANGE `conumber` `company_number` INT");
        $this->db->query("ALTER TABLE `files` DROP `email_sent`");
        $this->db->query("ALTER TABLE `files` DROP `doc_hash`");
    }


}