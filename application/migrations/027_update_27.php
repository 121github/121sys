<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_27 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 27");

        $this->db->query("ALTER TABLE `sms_templates` ADD `template_from` VARCHAR(100) NOT NULL");
    }
}