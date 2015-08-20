<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_45 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
				
		$this->db->query("ALTER TABLE `appointments` ADD `branch_id` INT NULL DEFAULT NULL , ADD INDEX (`branch_id`)");
		
	}
	
}
