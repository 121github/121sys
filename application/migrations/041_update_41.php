<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_41 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
		$this->load->dbforge();
    }

    public function up(){
        $this->firephp->log("starting migration 41");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `appointment_slots` (
  `appointment_slot_id` int(11) NOT NULL AUTO_INCREMENT,
  `slot_start` time DEFAULT NULL COMMENT '24h time of day',
  `slot_end` time DEFAULT NULL COMMENT '24h time of day',
  `slot_name` varchar(15) NOT NULL,
  `slot_description` varchar(100) NOT NULL COMMENT '1-7 monday - friday',
  PRIMARY KEY (`appointment_slot_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4") ;


$this->db->query("INSERT INTO `appointment_slots` (`appointment_slot_id`, `slot_start`, `slot_end`, `slot_name`, `slot_description`) VALUES
(1, '08:00:00', '01:00:00', 'AM', 'Morning slot between 8am and 1pm'),
(2, '13:00:00', '18:00:00', 'PM', 'Afternoon slot between 1pm and 6pm'),
(3, '18:00:01', '20:00:00', 'EVE', 'Evening slot between 6pm 8pm')");
		  
	}
	
}
