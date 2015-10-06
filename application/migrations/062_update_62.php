<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_62 extends CI_Migration 
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		  $this->firephp->log("starting migration 62");
		  
		  		$this->db->query("CREATE TABLE IF NOT EXISTS `data_pots` (
  `pot_id` int(11) NOT NULL AUTO_INCREMENT,
  `pot_name` varchar(50) NOT NULL,
  PRIMARY KEY (`pot_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

		$check = $this->db->query("SHOW COLUMNS FROM `records` LIKE 'pot_id'");
		if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `records` ADD `pot_id` INT NULL AFTER `source_id`, ADD INDEX (`pot_id`)");
		}
		
				$check = $this->db->query("SHOW COLUMNS FROM `history` LIKE 'pot_id'");
		if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `history` ADD `pot_id` INT NULL AFTER `source_id`, ADD INDEX (`pot_id`)");
		}
		
			$check = $this->db->query("SHOW COLUMNS FROM `history` LIKE 'pot_id'");
		if($check->num_rows()){
		//$this->db->query("update history set pot_id = source_id");
		}
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `campaign_groups` (
  `campaign_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_group_name` varchar(100) NOT NULL,
  PRIMARY KEY (`campaign_group_id`),
  UNIQUE KEY `campaign_group_name` (`campaign_group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
$check = $this->db->query("SHOW COLUMNS FROM `campaigns` LIKE 'campaign_group_id'");
		if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `campaigns` ADD `campaign_group_id` INT NULL AFTER `campaign_id`, ADD INDEX (`campaign_group_id`)");
		}

	}
}