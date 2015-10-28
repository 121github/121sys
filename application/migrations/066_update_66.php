<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_66 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 66");

        $this->db->query("ALTER TABLE branch_user ADD is_manager TINYINT DEFAULT 0 NOT NULL");
        $this->db->query("ALTER TABLE branch_region_users ADD is_manager TINYINT DEFAULT 0 NOT NULL");

        $this->db->query("ALTER TABLE branch ADD ics TINYINT DEFAULT 0 NOT NULL");
        $this->db->query("ALTER TABLE branch_regions ADD ics TINYINT DEFAULT 0 NOT NULL");

        $this->db->query("ALTER TABLE branch MODIFY COLUMN branch_email VARCHAR(250) NULL");
        $this->db->query("ALTER TABLE branch_regions MODIFY COLUMN region_email VARCHAR(250) NULL");

        $this->db->query("ALTER TABLE branch_regions ADD default_branch_id INT NULL");
        $this->db->query("ALTER TABLE `branch_regions` ADD CONSTRAINT `branch_regions_ibfk_4` FOREIGN KEY (`default_branch_id`) REFERENCES `branch` (`branch_id`)");

    }
}