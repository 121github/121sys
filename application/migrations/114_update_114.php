<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_114 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()

    {

            $this->db->query("insert ignore into permissions values('','change layout','System','The use can change their own record detail layout/view')");
			$id = $this->db->insert_id();
			 $this->db->query("insert ignore into role_permissions (select role_id,$id from user_roles)");

	}
	 public function down()

    {
	}
}