<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_115 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

            $this->db->query("insert ignore into permissions values('','google calendar','Calendar','Enable the google calendar API features')");
		$check = $this->db->query("SHOW COLUMNS FROM `apis` LIKE 'access_token'");
        if (!$check->num_rows()) {
		$this->db->query("ALTER TABLE apis ADD access_token VARCHAR(255) NULL");
        $this->db->query("ALTER TABLE apis ADD token_type VARCHAR(50) NULL");
        $this->db->query("ALTER TABLE apis ADD expires_in INT NULL");
        $this->db->query("ALTER TABLE apis ADD created INT NULL");
        $this->db->query("ALTER TABLE apis ADD id_token TEXT NULL");
        $this->db->query("ALTER TABLE apis DROP api_token");
		}
	}
	
	 public function down()
    {
	}

}