<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_53 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {


        $this->firephp->log("starting migration 53");

        $this->db->query("CREATE TABLE IF NOT EXISTS `appointment_slot_override` (
  `slot_override_id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_slot_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `max_slots` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`slot_override_id`),
  KEY `slot_override_id` (`slot_override_id`,`campaign_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
    }

}