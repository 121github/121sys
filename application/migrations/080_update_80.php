<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_80 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 80");

        $this->db->query("ALTER TABLE outcomes ADD contact_made tinyint DEFAULT 0 NOT NULL");

    }

}