
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_8 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 8");

        //add travelMode
        $this->db->query("ALTER TABLE `record_planner_route` ADD `travel_mode` VARCHAR(15) NULL DEFAULT NULL");
	}

    public function down()
    {
        $this->db->query("ALTER TABLE `record_planner_route` DROP `travel_mode`");
    }
	
}