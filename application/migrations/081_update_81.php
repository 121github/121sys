<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_81 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 81");

        $this->db->query("INSERT IGNORE INTO  `permissions` (`permission_id`, `permission_name`, `permission_group`, `description`) VALUES (NULL, 'edit recent history', 'History', 'The user can edit the recent history entry (created on the same day) ')");

        $this->db->query("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('5', (select permission_id from permissions where permission_name = 'edit recent history'))");

    }

}