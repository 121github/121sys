<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_50 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {


        $this->firephp->log("starting migration 50");

        $this->db->query("ALTER TABLE export_forms ADD source_filter VARCHAR(255) NULL");

    }

}