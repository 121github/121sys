<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_44 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
				
		$this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'apps to planner', 'Appointments')");
		$id = $this->db->insert_id();
		$this->db->query("INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('7', $id)");
	}
	
}
