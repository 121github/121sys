<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_61 extends CI_Migration 
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		  $this->firephp->log("starting migration 61");
		$check = $this->db->query("SHOW COLUMNS FROM `campaigns` LIKE 'timeout'");
		if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `campaigns` ADD `timeout` INT NULL DEFAULT NULL");
		}

	}
}