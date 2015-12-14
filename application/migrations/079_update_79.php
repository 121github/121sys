<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_79 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 79");

        $this->db->query("INSERT IGNORE INTO  `permissions` (`permission_id`, `permission_name`, `permission_group`, `description`) VALUES (NULL, 'client report', 'Reports', 'The user can view the client reports')");

        $this->db->query("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', (select permission_id from permissions where permission_name = 'client report'))");

    }

}