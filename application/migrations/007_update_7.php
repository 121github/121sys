
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_7 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 7");

        //Create record planner table route
        $this->db->query("CREATE TABLE if not exists `record_planner_route` (
          `record_planner_route_id` int(11) NOT NULL AUTO_INCREMENT,
          `record_planner_id` int NOT NULL,
          `start_add` VARCHAR(255) NOT NULL,
          `start_lat` REAL NOT NULL,
          `start_lng` REAL NOT NULL,
          `end_add` VARCHAR(255) NOT NULL,
          `end_lat` REAL NOT NULL,
          `end_lng` REAL NOT NULL,
          `distance` int NOT NULL,
          `duration` int NOT NULL,
          PRIMARY KEY (`record_planner_route_id`),
          KEY `record_planner_id` (`record_planner_id`),
          CONSTRAINT `record_planner_route_ibfk_1` FOREIGN KEY (`record_planner_id`) REFERENCES `record_planner` (`record_planner_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
	}

    public function down()
    {
        $this->db->query("DROP TABLE `record_planner_route`");
    }
	
}