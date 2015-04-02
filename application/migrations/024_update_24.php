<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_24 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 23");

        //Add the tps and ctps field to the tps table
        $this->db->query("ALTER TABLE `tps` ADD `tps` TINYINT NOT NULL");
        $this->db->query("ALTER TABLE `tps` ADD `ctps` TINYINT NOT NULL");

    }
    public function down()
    {
        $this->db->query("ALTER TABLE `tps` DROP `tps`");
        $this->db->query("ALTER TABLE `tps` DROP `ctps`");
    }
}