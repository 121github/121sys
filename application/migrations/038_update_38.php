<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_38 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up(){
		  $this->firephp->log("starting migration 38");
		  $this->db->query("ALTER TABLE `campaigns` ADD `virgin_order_1` VARCHAR(150) NOT NULL");
		  $this->db->query("ALTER TABLE `campaigns` ADD `virgin_order_2` VARCHAR(150) NOT NULL");
	}
	
}
