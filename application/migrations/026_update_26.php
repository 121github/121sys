<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_26 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 26");

        $this->db->query("ALTER TABLE `campaigns` ADD `max_dials` INT NULL DEFAULT NULL");
    }
}