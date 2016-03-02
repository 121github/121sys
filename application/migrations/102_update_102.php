
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
	}
	
	public function down()
    {
		
	}
}