<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_35 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 35");
		$this->db->query("ALTER TABLE `record_planner` CHANGE `urn` `urn` INT(11) NULL");
		$this->db->query("ALTER TABLE `record_planner` ADD `planner_type` INT(11) NULL");
	}
	
}
?>