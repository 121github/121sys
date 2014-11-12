<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_7 extends CI_Migration
{
    public function up()
    {
    	//Create hours table
    	$this->db->query("CREATE TABLE IF NOT EXISTS `attachments`
        (
            `attachment_id` int PRIMARY KEY NOT NULL,
            `urn` int NOT NULL,
            `name` VARCHAR(50) NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `path` VARCHAR(255) NOT NULL,
            `date` DATETIME NOT NULL,
            `user_id` int NOT NULL,
          KEY `FK_urn` (`urn`),
          CONSTRAINT `FK_urn` FOREIGN KEY (`urn`) REFERENCES `records` (`urn`) ON DELETE CASCADE ON UPDATE CASCADE,
          KEY `FK_users` (`user_id`),
          CONSTRAINT `FK_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) COMMENT 'Record attachments'
          ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1
        ;");
    }
    public function down()
    {
		$this->db->query("drop table attachments");
    }
}


