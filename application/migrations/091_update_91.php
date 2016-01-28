<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_91 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		 $check = $this->db->query("SHOW COLUMNS FROM `campaigns` LIKE 'custom_panel_format'");
        if(!$check->num_rows()){
			$this->db->query("ALTER TABLE `campaigns` ADD `custom_panel_format` TINYINT NOT NULL DEFAULT '1' AFTER `custom_panel_name`");
		}
	}
	 public function down(){
		 
	 }
	 
}