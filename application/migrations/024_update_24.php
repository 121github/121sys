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
        $this->firephp->log("starting migration 24");

        //Add the tps and ctps field to the tps table
        $this->db->query("ALTER TABLE `tps` ADD `tps` TINYINT DEFAULT 0 NOT NULL");
        $this->db->query("ALTER TABLE `tps` ADD `ctps` TINYINT DEFAULT 0 NOT NULL");

    }
    public function down()
    {
        $this->db->query("ALTER TABLE `tps` DROP `tps`");
        $this->db->query("ALTER TABLE `tps` DROP `ctps`");
    }
}