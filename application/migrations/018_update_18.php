<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_18 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 18");

$this->db->query("ALTER TABLE `record_details` ADD `c6` VARCHAR(50) NULL DEFAULT NULL AFTER `c5`");

	}
	
}