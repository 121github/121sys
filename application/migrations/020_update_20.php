<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_20 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 20");

        $this->db->query("ALTER TABLE `campaigns` MODIFY `map_icon` VARCHAR(50) DEFAULT 'fa-map-marker'");

    }

}