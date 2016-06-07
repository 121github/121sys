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
        $check = $this->db->query("INSERT ignore INTO `permissions` (`permission_name`, `permission_group`, `description`) VALUES
('enable all filters', 'Dashboards', 'Enable all filters for editing')");
	}
	
}