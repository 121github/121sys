<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_2 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {
    $this->firephp->log("starting migration 2");

    $this->db->query("ALTER TABLE `appointments` ADD `appointment_type_id` INT NULL DEFAULT 1");
    $this->db->query("ALTER TABLE `appointments` ADD `address` VARCHAR(255) DEFAULT ''");


    $this->db->query("CREATE TABLE IF NOT EXISTS `appointment_types` (
  `appointment_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_type` varchar(100) NOT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`appointment_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3") ;

    $this->db->query("INSERT ignore INTO `appointment_types` (`appointment_type_id`, `appointment_type`, `campaign_id`) VALUES
(1, 'Face to face', NULL),
(2, 'Telephone', NULL)");

  }

  public function down()
  {
    $this->db->query("ALTER TABLE `appointments` DROP `appointment_type_id`");
    $this->db->query("ALTER TABLE `appointments` DROP `address`");
  }
}
?>