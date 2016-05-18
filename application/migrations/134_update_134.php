<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_134 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $check = $this->db->query("SHOW COLUMNS FROM `email_templates` LIKE 'people_destination'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE email_templates ADD people_destination VARCHAR(255) NULL");
        }
	}
	
}