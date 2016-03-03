<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_102 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		$check = $this->db->query("SHOW COLUMNS FROM `dashboards` LIKE 'type'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE `dashboards` ADD `type` VARCHAR(50) DEFAULT 'Dashboard' NOT NULL");
		}

        $check = $this->db->query("SHOW COLUMNS FROM `export_forms` LIKE 'team_filter'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE `export_forms` ADD `team_filter` VARCHAR(255) NULL");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `export_forms` LIKE 'agent_filter'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE `export_forms` ADD `agent_filter` VARCHAR(255) NULL");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `export_forms` LIKE 'outcome_filter'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE `export_forms` ADD `outcome_filter` VARCHAR(255) NULL");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `export_forms` LIKE 'user_filter'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE `export_forms` ADD `user_filter` VARCHAR(255) NULL");
        }

	}
	
	public function down()
    {
		
	}

}