<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_59 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		  $this->firephp->log("starting migration 59");
		$check = $this->db->query("SHOW COLUMNS FROM `outcomes` LIKE 'requires_callback'");
		if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `outcomes` ADD `requires_callback` INT NULL DEFAULT NULL ");
		}
		//callback dm and email sent requires a callback
		$this->db->query("update outcomes set requires_callback = 1 where outcome_id in(2,85) ");
		$this->db->query("alter table hours add unique(user_id,campaign_id,date)");
	}
}
