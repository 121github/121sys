<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_130 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

		$this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`, `description`) VALUES (NULL, 'enable global filter', 'Global Filter', 'This must be checked to enable the global filter menu')");

        $this->db->query("insert ignore into role_permissions select role_id,(select permission_id from permissions where permission_name = 'enable global filter') from user_roles");
	}
	
}