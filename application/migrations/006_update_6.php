<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_6 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->db->query("ALTER TABLE campaigns ADD min_quote_days INT NULL");
        $this->db->query("ALTER TABLE campaigns ADD max_quote_days INT NULL");

        $this->db->query("ALTER TABLE campaigns ADD daily_data INT NOT NULL DEFAULT 0");

    }

    public function down()
    {
        $this->db->query("ALTER TABLE campaigns DROP min_quote_days");
        $this->db->query("ALTER TABLE campaigns DROP max_quote_days");

        $this->db->query("ALTER TABLE campaigns DROP daily_data");
    }

}