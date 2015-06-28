<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_19 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 19");

$this->db->query("ALTER TABLE `record_details_fields` ADD `editable` TINYINT NOT NULL DEFAULT '1'");
$this->db->query("ALTER TABLE `webform_answers` CHANGE `updated_on` `updated_on` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
$this->db->query("ALTER TABLE `webform_answers` ADD `completed_on` DATETIME NULL DEFAULT NULL AFTER `updated_by`");
$this->db->query("ALTER TABLE `webform_answers` ADD `completed_by` INT NULL DEFAULT NULL AFTER `competed_on`") ;
$this->db->query("update webform_answers set updated_on = null where updated_on = '0000-00-00 00:00:00'");
$this->db->query("ALTER TABLE `webform_answers` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
	}
	
}