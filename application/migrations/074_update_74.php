<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_74 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 74");
$this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'admin shop', 'Admin')");
	$this->db->query("insert ignore into role_permissions values(1,(select permission_id from permissions where permission_name = 'admin shop'))");
	}
	
}