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

            $this->db->query("ALTER TABLE call_log ADD CONSTRAINT call_log_campaign_id_fk1 FOREIGN KEY (campaign_id)
                              REFERENCES campaigns (campaign_id) ON DELETE RESTRICT ON UPDATE RESTRICT");
        }
    }
    public function down()
    {
    }
}