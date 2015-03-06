<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_16 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        //Adding search actions permissions and edit export permissions
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES
                      (117, 'productivity', 'Reports')");

        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES
                    (1, 117)");

        //Adding call_log_file table
        $this->db->query("CREATE TABLE IF NOT EXISTS `call_log_file` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `file_date` DATE NOT NULL,
                `unit` INT(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");


        //Adding call_log table
        $this->db->query("CREATE TABLE IF NOT EXISTS `call_log` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `file_id` INT(11) NULL DEFAULT NULL,
                `call_date` DATETIME NOT NULL,
                `duration` TIME NOT NULL,
                `ring_time` INT(11) NOT NULL,
                `call_id` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
                `call_from` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `name_from` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `ref_from` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `call_to` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `call_to_ext` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `name_to` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `ref_to` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `inbound` TINYINT(1) NOT NULL,
                PRIMARY KEY (`id`),
                INDEX `IDX_D663C42E93CB796C` (`file_id`),
                CONSTRAINT `FK_D663C42E93CB796C` FOREIGN KEY (`file_id`) REFERENCES `call_log_file` (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");


    }

    public function down()
    {
        $this->db->query("DELETE FROM `permissions` WHERE `permission_id` IN (117)");
        $this->db->query("DELETE FROM `role_permissions` WHERE `permission_id` IN (117)");
        $this->db->query("DROP TABLE `call_log_file`");
        $this->db->query("DROP TABLE `call_log`");
    }

}