<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_31 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 31");

        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES
                        (130, 'sms', 'Reports')");

        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES
                        (1, 130)");

    }

}