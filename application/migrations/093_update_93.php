<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_93 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		$this->firephp->log("starting migration 93");
				 $check = $this->db->query("SHOW COLUMNS FROM `users` LIKE 'calendar'");
        if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `users` ADD `calendar` BOOLEAN NOT NULL DEFAULT TRUE");
		}
	}
	
}