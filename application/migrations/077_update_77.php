<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_77 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 77");
		$check = $this->db->query("SHOW COLUMNS FROM `export_forms` LIKE 'pot_filter'");
		if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `export_forms` ADD `pot_filter` VARCHAR(255) NULL DEFAULT NULL ,
ADD INDEX ( `pot_filter` )");
		}

	}
	
}