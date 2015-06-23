
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_16 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 16");

$this->db->query("CREATE TABLE IF NOT EXISTS `outcome_reasons` (
  `outcome_reason_id` int(11) NOT NULL AUTO_INCREMENT,
  `outcome_reason` varchar(100) NOT NULL,
  PRIMARY KEY (`outcome_reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

$this->db->query("CREATE TABLE IF NOT EXISTS `outcome_reason_campaigns` (
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `outcome_reason_id` int(11) NOT NULL,
  UNIQUE KEY `campaign_id` (`campaign_id`,`outcome_id`,`outcome_reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

$this->db->query("ALTER TABLE `records` ADD `outcome_reason_id` INT NULL DEFAULT NULL AFTER `outcome_id`, ADD INDEX (`outcome_reason_id`)") ;
$this->db->query("ALTER TABLE `history` ADD `outcome_reason_id` INT NULL DEFAULT NULL AFTER `outcome_id`, ADD INDEX (`outcome_reason_id`)") ;

$this->db->query("INSERT IGNORE INTO  `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'system menu', 'Admin')");

$this->db->query("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', (select permission_id from permissions where permission_name = 'system menu'))");


	}
	
}