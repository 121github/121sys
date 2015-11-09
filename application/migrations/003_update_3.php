<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_3 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 3");

        //Create record planner table
        $this->db->query("CREATE TABLE `record_planner` (
          `record_planner_id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `urn` int(11) NOT NULL,
          `start_date` datetime NOT NULL,
          `postcode` varchar(10) NOT NULL,
          `location_id` int(11) NOT NULL,
          PRIMARY KEY (`record_planner_id`),
          KEY `user_id` (`user_id`),
          KEY `urn` (`urn`),
          KEY `location_id` (`location_id`),
          CONSTRAINT `record_planner_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
          CONSTRAINT `record_planner_ibfk_2` FOREIGN KEY (`urn`) REFERENCES `records` (`urn`),
          CONSTRAINT `record_planner_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    }

    public function down()
    {
        $this->db->query("DROP TABLE `record_planner`");
    }
}

?>