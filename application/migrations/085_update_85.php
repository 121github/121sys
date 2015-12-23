<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_85 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 85");

        $check = $this->db->query("SHOW COLUMNS FROM `call_log` LIKE 'campaign_id'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE call_log ADD campaign_id int NULL");
        }
	}
	 public function down()
    {
	}
}