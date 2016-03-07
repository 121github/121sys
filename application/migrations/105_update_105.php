<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_105 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()

    {
				 $check = $this->db->query("SHOW COLUMNS FROM `custom_panel_fields` LIKE 'sort'");
        if(!$check->num_rows()){
	$this->db->query("ALTER TABLE `custom_panel_fields` ADD `sort` INT( 5 ) NOT NULL");	
		}

	}
	
	 public function down()
    {
		
	}
}