<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_103 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
				 $check = $this->db->query("SHOW COLUMNS FROM `datafields` LIKE 'campaign'");
        if(!$check->num_rows()){
	$this->db->query("ALTER TABLE `datafields` ADD `campaign` INT NULL DEFAULT NULL");	
		}
	}
	
	public function down()
    {
		
	}

}