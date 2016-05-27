<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_147 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $check = $this->db->query("SHOW COLUMNS FROM `google_calendar` LIKE 'cancelled_events'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE google_calendar ADD cancelled_events TINYINT(1) DEFAULT 0 NOT NULL");
        }
    }
}

