<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_17 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        //Adding search actions permissions and edit export permissions
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES
                      (118, 'database', 'Admin')");

        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES
                    (1, 118)");
    }

    public function down()
    {
        $this->db->query("DELETE FROM `permissions` WHERE `permission_id` IN (118)");
        $this->db->query("DELETE FROM `role_permissions` WHERE `permission_id` IN (118)");
    }

}