<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_146 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $check = $this->db->query("SHOW COLUMNS FROM `export_forms` LIKE 'agent_filter'");
        if ($check->num_rows()) {
            $this->db->query("ALTER TABLE export_forms CHANGE agent_filter branch_filter VARCHAR(255)");
        }
    }

}

