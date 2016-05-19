<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_135 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $check = $this->db->query("SHOW COLUMNS FROM `call_log` LIKE 'ext'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE call_log ADD ext VARCHAR(3) NULL");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `call_log` LIKE 'user'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE call_log ADD user VARCHAR(3) NULL");
        }
	}
	
}