
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_6 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 6");

        //add order number
        $this->db->query("ALTER TABLE `record_planner` ADD `order_num` INT NULL DEFAULT NULL");
	}

    public function down()
    {
        $this->db->query("ALTER TABLE `record_planner` DROP `order_num`");
    }
	
}