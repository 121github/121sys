<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_124 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
$this->db->query("CREATE TABLE IF NOT EXISTS `user_appointment_types` (
  `user_id` int(11) NOT NULL,
  `appointment_type_id` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`appointment_type_id`),
  KEY `appointment_type_id` (`appointment_type_id`),
  KEY `user_id_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
	}
	
}