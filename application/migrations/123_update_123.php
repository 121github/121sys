<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_123 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
$this->db->query("update outcomes set contact_made = 1 where outcome_id in(1,12,60,66,70,71,72,81,82,83,85,86,89");
	}
	
}