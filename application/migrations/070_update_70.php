<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_70 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 70");

$this->db->query("INSERT ignore  INTO `campaign_features` (`feature_id`, `feature_name`, `panel_path`, `permission_id`) VALUES (19, 'Orders', 'orders.php', NULL)");
$this->db->query("INSERT ignore INTO `campaign_features` (`feature_id`, `feature_name`, `panel_path`, `permission_id`) VALUES
(18, 'Tasks', 'tasks.php', NULL)");

$this->db->query("CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) DEFAULT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Note: This is the default CodeIgniter session table.'");
	}
	
}