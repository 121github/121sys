
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_24 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 23");


        $this->db->query("ALTER TABLE `sms_history` CHANGE `body` `text` LONGTEXT");
        $this->db->query("ALTER TABLE `sms_history` DROP `read_confirmed`");
        $this->db->query("ALTER TABLE `sms_history` DROP `read_confirmed_date`");
        $this->db->query("ALTER TABLE `sms_history` DROP `pending`");
        $this->db->query("ALTER TABLE `sms_history` CHANGE `cron_code` `text_local_id` VARCHAR(255)");
        $this->db->query("ALTER TABLE `sms_history` MODIFY COLUMN `user_id` INT NULL");
        $this->db->query("ALTER TABLE `sms_history` CHANGE `status` `status_id` INT NULL");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `sms_status`
            (
                `sms_status_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `status_reason` VARCHAR(100) NOT NULL,
                PRIMARY KEY (`sms_status_id`)
            )
            ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
        ");

        $this->db->query("INSERT INTO `sms_status` (`status_reason`) VALUES ('PENDING')");
        $this->db->query("INSERT INTO `sms_status` (`status_reason`) VALUES ('SENT')");

        //Constraints for table `history_log`
        $this->db->query("ALTER TABLE `sms_history`
  		ADD CONSTRAINT `sms_history_statusfk_1` FOREIGN KEY (`status_id`) REFERENCES `sms_status` (`status_id`)");

    }

}