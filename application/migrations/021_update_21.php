<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_21 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 21");
        //Adding company status field in the companies table
        $this->db->query("ALTER TABLE `companies` ADD `status` VARCHAR(50) NULL");
        //Adding company date of creation field in the companies table
        $this->db->query("ALTER TABLE `companies` ADD `date_of_creation` DATE NULL");

    }

    public function down()
    {
        $this->db->query("ALTER TABLE `companies` DROP COLUMN `status`");
        $this->db->query("ALTER TABLE `companies` DROP COLUMN `date_of_creation`");
    }

}