<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_64 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 64");

        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'get address', 'Address')");
        $id = $this->db->insert_id();
        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', $id)");

        $this->db->query("ALTER TABLE `contact_addresses` ADD `add4` VARCHAR(100) NULL");
        $this->db->query("ALTER TABLE `contact_addresses` ADD `locality` VARCHAR(100) NULL");
        $this->db->query("ALTER TABLE `contact_addresses` ADD `city` VARCHAR(100) NULL");

        $this->db->query("ALTER TABLE `company_addresses` ADD `add4` VARCHAR(100) NULL");
        $this->db->query("ALTER TABLE `company_addresses` ADD `locality` VARCHAR(100) NULL");
        $this->db->query("ALTER TABLE `company_addresses` ADD `city` VARCHAR(100) NULL");

    }

}