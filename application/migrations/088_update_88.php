<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_88 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 88");
		$check = $this->db->query("SHOW COLUMNS FROM `webform_answers` LIKE 'appointment_id'");
        if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `webform_answers` ADD `appointment_id` INT NULL DEFAULT NULL AFTER `urn`");
		}
		$check = $this->db->query("SHOW COLUMNS FROM `webforms` LIKE 'appointment_type_id'");
        if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `webforms` ADD `appointment_type_id` INT NULL DEFAULT NULL ,
ADD INDEX ( `appointment_type_id` )");
		}
		$check = $this->db->query("SHOW COLUMNS FROM `user_roles` LIKE 'timeout'");
        if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `user_roles` ADD `timeout` INT NULL DEFAULT '120'");
		}
		$check = $this->db->query("SHOW COLUMNS FROM `webforms` LIKE 'btn_text'");
        if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `webforms` ADD `btn_text` VARCHAR( 50 ) NOT NULL");
		}
	}
	
}