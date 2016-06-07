<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_151 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model'); 
    }

    public function up()
    {
		$this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`, `description`) VALUES (NULL, 'filter status', 'Global Filter', 'Show the status field in the global filter')");
		
		$this->db->query("INSERT INTO `role_permissions` set role_id = 1, permission_id = (select permission_id from permissions where permission_name = 'filter status')");
        $check = $this->db->query("INSERT ignore INTO `permissions` (`permission_name`, `permission_group`, `description`) VALUES
('enable all filters', 'Dashboards', 'Enable all filters for editing')");

	}
	
}