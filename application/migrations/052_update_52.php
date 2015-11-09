<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_52 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {


        $this->firephp->log("starting migration 52");

        $this->db->query("ALTER TABLE appointment_slot_assignment ADD source_id INT NULL");
    }

}