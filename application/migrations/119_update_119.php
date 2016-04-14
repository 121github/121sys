<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_119 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

		$check = $this->db->query("SHOW COLUMNS FROM `appointments` LIKE 'google_id'");
        if (!$check->num_rows()) {
		$this->db->query("ALTER TABLE `appointments` ADD `google_id` VARCHAR(255) NOT NULL");
		}
	}
	
	 public function down()
    {
	}

}