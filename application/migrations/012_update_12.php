<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_12 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
		
    }

    public function up(){
		 $this->firephp->log("starting migration 12");
	$this->db->query("ALTER TABLE `appointments` ADD `contact_id` INT NULL DEFAULT NULL , ADD INDEX (`contact_id`)");
	}
}