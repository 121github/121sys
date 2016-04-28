<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_122 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
  $this->db->query("insert ignore into role_permissions select role_id,176 from user_roles");
        $this->db->query("insert ignore into role_permissions select role_id,177 from user_roles");
		
	}
	
}