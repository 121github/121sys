<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_132 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		$this->db->query("ALTER TABLE attachments ADD version INT DEFAULT 1 NOT NULL");
	}
	
}