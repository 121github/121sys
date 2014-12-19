<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_10 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->db->query("CREATE TABLE `backup_campaign_history`
            (
                `backup_campaign_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `backup_date` DATETIME NOT NULL,
                `campaign_id` INT NOT NULL,
                `user_id` INT NOT NULL,
                `update_date_from` DATE,
                `update_date_to` DATE,
                `renewal_date_from` DATE,
                `renewal_date_to` DATE,
                `num_records` INT DEFAULT 0 NOT NULL,
                `name` VARCHAR(100) NOT NULL,
                `path` VARCHAR(255) NOT NULL,
                `restored` TINYINT DEFAULT 0 NOT NULL,
                `restored_date` DATETIME NULL,
                KEY `FK_backup_campaign_id` (`campaign_id`),
                CONSTRAINT `FK_backup_campaign_id` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                KEY `FK_backup_user_id` (`user_id`),
                CONSTRAINT `FK_backup_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Backup history for the campaigns' COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        $this->db->query("CREATE TABLE `backup_by_campaign`
            (
                `backup__by_campaign_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `campaign_id` INT NOT NULL,
                `months_ago` INT DEFAULT 0 NOT NULL,
                `months_num` INT DEFAULT 0 NOT NULL,
                KEY `FK_backup_by_campaign_id` (`campaign_id`),
                CONSTRAINT `FK_backup_by_campaign_id` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Backup by campaign settings' COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
    }

    public function down()
    {
        $this->db->query("DROP TABLE 121sys.backup_campaign_history;");
        $this->db->query("DROP TABLE 121sys.backup_by_campaign;");
    }

}