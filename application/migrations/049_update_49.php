
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_49 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
			
		
      $this->firephp->log("starting migration 49");

	    $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES ('', 'add custom items', 'System')");
			$check = $this->db->query("SHOW COLUMNS FROM `record_details` LIKE 'added_by'");
		if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `record_details` ADD `added_by` INT NULL  DEFAULT NULL,
ADD `added_on` TIMESTAMP NULL  DEFAULT NULL,
ADD `updated_by` INT NULL  DEFAULT NULL,
ADD `updated_on` TIMESTAMP NULL  DEFAULT NULL,
ADD INDEX ( `added_by` , `updated_by` )");
		}
	}
	
}