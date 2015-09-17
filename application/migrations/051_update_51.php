<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_51 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {


        $this->firephp->log("starting migration 51");

        $this->db->query("ALTER TABLE history ADD source_id INT NULL");
    }

}