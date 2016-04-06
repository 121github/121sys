<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_117 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {


		$check = $this->db->query("SHOW COLUMNS FROM `datafields` LIKE 'modal_keys'");
        if (!$check->num_rows()) {
		$this->db->query("ALTER TABLE `datafields` ADD `modal_keys` TINYINT( 1 ) NOT NULL DEFAULT '1'");
		}
	}
	
	 public function down()
    {
	}

}