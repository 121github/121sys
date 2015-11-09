
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


        $this->db->query("ALTER TABLE `sms_history` CHANGE `send_from` `sender_id` INT NOT NULL");

        $this->db->query("ALTER TABLE `sms_templates` CHANGE `template_from` `template_sender_id` INT NOT NULL");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `sms_sender`
            (
                `sender_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL
            )
            ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
        ");

        $this->db->query("INSERT IGNORE INTO `sms_sender` (`sender_id`,`name`) VALUES (1,'one2one')");
        $this->db->query("INSERT IGNORE INTO `sms_sender` (`sender_id`,`name`) VALUES (2,'FreeSolar')");
        $this->db->query("INSERT IGNORE INTO `sms_sender` (`sender_id`,`name`) VALUES (3,'South way')");



        //Constraints for table `sms_history`
        $this->db->query("ALTER TABLE `sms_history` ADD CONSTRAINT `sms_history_senderfk_1` FOREIGN KEY (`sender_id`) REFERENCES `sms_sender` (`sender_id`)");

        //Constraints for table `sms_templates`
        $this->db->query("ALTER TABLE `sms_templates` ADD CONSTRAINT `sms_template_senderfk_1` FOREIGN KEY (`template_sender_id`) REFERENCES `sms_sender` (`sender_id`)");

    }

}