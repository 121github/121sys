<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_118 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

		$check = $this->db->query("SHOW COLUMNS FROM `apis` LIKE 'refresh_token'");
        if (!$check->num_rows()) {
		$this->db->query("ALTER TABLE `apis` ADD `refresh_token` VARCHAR(255) NOT NULL");
		}

//        Add google_calendar_id
        $check = $this->db->query("SHOW COLUMNS FROM `apis` LIKE 'calendar_id'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE `apis` ADD `calendar_id` VARCHAR(255) NULL");
        }
	}
	
	 public function down()
    {
	}

}