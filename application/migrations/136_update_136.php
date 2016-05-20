<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_136 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $check = $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`, `description`) VALUES
('', 'favourites dash', 'Dashboards', ''),
('', 'overview dash', 'Dashboards', ''),
('', 'callback dash', 'Dashboards', ''),
('', 'campaign override', 'Override', '')");

$this->db->query("insert ignore into role_permissions (select role_id,(select permission_id from permissions where permission_name = 'favourites dash') from user_roles)");
$this->db->query("insert ignore into role_permissions (select role_id,(select permission_id from permissions where permission_name = 'overview dash') from user_roles)");
$this->db->query("insert ignore into role_permissions (select role_id,(select permission_id from permissions where permission_name = 'campaign override') from user_roles)");
	}
	
}