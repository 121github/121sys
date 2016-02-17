
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_97 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		 $check = $this->db->query("SHOW COLUMNS FROM `record_details_fields` LIKE 'is_buttons'");
        if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `record_details_fields` ADD `is_buttons` INT NULL DEFAULT NULL AFTER `is_select`");
		}
	}
}