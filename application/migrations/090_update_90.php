<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_90 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		 $check = $this->db->query("SHOW COLUMNS FROM `record_details` LIKE 'c7'");
        if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `record_details` ADD `c7` VARCHAR(255) NULL AFTER `c6`, ADD `c8` VARCHAR(255) NULL AFTER `c7`, ADD `c9` VARCHAR(255) NULL AFTER `c8`, ADD `c10` VARCHAR(255) NULL AFTER `c9`");
			$this->db->query("ALTER TABLE `record_details` ADD `d4` DATE NULL DEFAULT NULL AFTER `d3`, ADD `d5` DATE NULL DEFAULT NULL AFTER `d4`, ADD `d6` DATE NULL DEFAULT NULL AFTER `d5`, ADD `d7` DATE NULL DEFAULT NULL AFTER `d6`, ADD `d8` DATE NULL DEFAULT NULL AFTER `d7`, ADD `d9` DATE NULL DEFAULT NULL AFTER `d8`, ADD `d10` DATE NULL DEFAULT NULL AFTER `d9`");
		}
			 $check = $this->db->query("SHOW COLUMNS FROM `record_details` LIKE 'dt3'");
        if(!$check->num_rows()){
			$this->db->query("ALTER TABLE `record_details` ADD `dt3` DATETIME NULL AFTER `dt2`, ADD `dt4` DATETIME NULL AFTER `dt3`, ADD `dt5` DATETIME NULL AFTER `dt4`, ADD `dt6` DATETIME NULL AFTER `dt5`, ADD `dt7` DATETIME NULL AFTER `dt6`, ADD `dt8` DATETIME NULL AFTER `dt7`, ADD `dt9` DATETIME NULL AFTER `dt8`, ADD `dt10` DATETIME NULL AFTER `dt9`");
		}
		
		 $check = $this->db->query("SHOW COLUMNS FROM `record_details` LIKE 'n4'");
        if(!$check->num_rows()){
			$this->db->query("ALTER TABLE `record_details` ADD `n4` DECIMAL(20,2) NULL AFTER `n3`, ADD `n5` DECIMAL(20,2) NULL AFTER `n4`, ADD `n6` DECIMAL(20,2) NULL AFTER `n5`, ADD `n7` DECIMAL(20,2) NULL AFTER `n6`, ADD `n8` DECIMAL(20,2) NULL AFTER `n7`, ADD `n9` DECIMAL(20,2) NULL AFTER `n8`, ADD `n10` DECIMAL(20,2) NULL AFTER `n9`");
			
			$this->db->query("ALTER TABLE `record_details` CHANGE `n1` `n1` DECIMAL(20,2) NULL DEFAULT NULL, CHANGE `n2` `n2` DECIMAL(20,2) NULL DEFAULT NULL, CHANGE `n3` `n3` DECIMAL(20,2) NULL DEFAULT NULL");
		}
		 $check = $this->db->query("SHOW COLUMNS FROM `record_details_fields` LIKE 'is_decimal'");
		  if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `record_details_fields` ADD `is_decimal` BOOLEAN NULL DEFAULT NULL") ;
		  }
	}
	    public function down(){
			
		}
}
