<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_29 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 29");
		$this->db->query("DROP TABLE IF EXISTS record_tasks");
		$this->db->query("DROP TABLE IF EXISTS tasks");
		$this->db->query("DROP TABLE IF EXISTS tasks_to_options");
		$this->db->query("DROP TABLE IF EXISTS record_tasks");
		$this->db->query("DROP TABLE IF EXISTS task_history");
		$this->db->query("DROP TABLE IF EXISTS task_status_options");
		
$this->db->query("CREATE TABLE IF NOT EXISTS `record_tasks` (
  `record_task_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `task_status_id` int(11) NOT NULL,
  PRIMARY KEY (`record_task_id`),
  UNIQUE KEY `urn_2` (`urn`,`task_id`),
  UNIQUE KEY `urn_3` (`urn`,`task_id`),
  KEY `urn` (`urn`,`task_id`,`task_status_id`),
  KEY `task_status_id` (`task_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

$this->db->query("CREATE TABLE IF NOT EXISTS `tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(250) NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

$this->db->query("CREATE TABLE IF NOT EXISTS `tasks_to_options` (
  `task_id` int(11) NOT NULL,
  `task_status_id` int(11) NOT NULL,
  UNIQUE KEY `task_id` (`task_id`,`task_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

$this->db->query("CREATE TABLE IF NOT EXISTS `task_history` (
  `task_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `task_status_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`task_history_id`),
  KEY `task_id` (`task_id`,`task_status_id`,`user_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

$this->db->query("CREATE TABLE IF NOT EXISTS `task_status_options` (
  `task_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_status` varchar(100) NOT NULL,
  PRIMARY KEY (`task_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

$this->db->query("INSERT INTO `task_status_options` (`task_status_id`, `task_status`) VALUES
(1, 'n/a'),
(2, 'Pending'),
(3, 'Complete')");

$this->db->query("ALTER TABLE `record_details_fields` ADD `is_color` BOOLEAN NULL DEFAULT NULL , ADD `is_owner` BOOLEAN NULL DEFAULT NULL , ADD `is_client_ref` BOOLEAN NULL DEFAULT NULL,  ADD `is_radio` BOOLEAN NULL DEFAULT NULL ");

	}
	
}