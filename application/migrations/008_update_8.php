<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_8 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->db->query("ALTER TABLE `outcomes` ADD `force_nextcall` INT(1) NULL DEFAULT NULL AFTER `force_comment`;");
    }

    public function down()
    {
        $this->db->query("drop `force_nextcall` from outcomes");
    }

}