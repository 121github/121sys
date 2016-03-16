<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_110 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()

    {
        $check = $this->db->query("SHOW COLUMNS FROM `email_history` LIKE 'visible'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE email_history ADD visible TINYINT(1) DEFAULT 1 NOT NULL");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `email_templates` LIKE 'history_visible'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE email_templates ADD history_visible TINYINT(1) DEFAULT 1 NOT NULL");
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS `appointments_ics_status` (
                          `appointments_ics_status_id` int(11) NOT NULL AUTO_INCREMENT,
                          `status` varchar(50) NOT NULL,
                          PRIMARY KEY (`appointments_ics_status_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
        );

        $this->db->query("INSERT IGNORE INTO `appointments_ics_status` (`appointments_ics_status_id`, `status`) VALUES (1, 'SENT')");
        $this->db->query("INSERT IGNORE INTO `appointments_ics_status` (`appointments_ics_status_id`, `status`) VALUES (2, 'PENDING')");
        $this->db->query("INSERT IGNORE INTO `appointments_ics_status` (`appointments_ics_status_id`, `status`) VALUES (3, 'ABORTED')");

        $check = $this->db->query("SHOW COLUMNS FROM `appointments_ics` LIKE 'status_id'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE appointments_ics ADD status_id INT NOT NULL DEFAULT 1");
        }

        $this->db->query("ALTER TABLE `appointments_ics` ADD FOREIGN KEY ( `status_id` ) REFERENCES `appointments_ics_status` (`appointments_ics_status_id`)
                          ON DELETE CASCADE ON UPDATE CASCADE"
        );

        $check = $this->db->query("SHOW COLUMNS FROM `appointments_ics` LIKE 'email_id'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE appointments_ics ADD email_id INT NULL");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `email_history_attachments` LIKE 'disposition'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE email_history_attachments ADD disposition VARCHAR(50) NULL DEFAULT 'attachment'");
        }


    }

    public function down()
    {

    }
}