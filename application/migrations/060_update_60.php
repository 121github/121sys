
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_60 extends CI_Migration 
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		  $this->firephp->log("starting migration 60");
		$check = $this->db->query("SHOW COLUMNS FROM `records` LIKE 'added_by'");
		if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `records` ADD `added_by` INT NULL DEFAULT NULL AFTER `date_added`");
		}

	}
}
