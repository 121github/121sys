<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_137 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $check = $this->db->query("SHOW COLUMNS FROM `google_calendar` LIKE 'no_title_events'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE google_calendar ADD no_title_events TINYINT(1) DEFAULT 1 NOT NULL");
        }
	}
	
}