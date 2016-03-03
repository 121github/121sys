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
$this->db->query("ALTER TABLE `datafields` ADD `campaign` INT NULL DEFAULT NULL");
	}
	
	public function down()
    {
		
	}
	
}