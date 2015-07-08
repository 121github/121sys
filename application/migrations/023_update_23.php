
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_23 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 23");

$this->db->query("ALTER TABLE `record_details` ADD UNIQUE (
`urn`
)");

        $this->db->query("CREATE TABLE IF NOT EXISTS `record_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `task_status_id` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urn_2` (`urn`,`task_id`),
  KEY `urn` (`urn`,`task_id`,`user_id`,`task_status_id`),
  KEY `task_status_id` (`task_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        $this->db->query("
CREATE TABLE IF NOT EXISTS `campaign_tasks` (
  `campaign_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

	
	        $this->db->query("CREATE TABLE IF NOT EXISTS `tasks` (
`task_id` int(11) NOT NULL,
  `task_name` varchar(250) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1");

	
	        $this->db->query("CREATE TABLE IF NOT EXISTS `task_status` (
`task_status_id` int(11) NOT NULL,
  `task_status` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1");

	        $this->db->query("ALTER TABLE `campaign_tasks`
 ADD UNIQUE KEY `campaign_id` (`campaign_id`,`task_id`)");

	        $this->db->query("ALTER TABLE `tasks`
 ADD PRIMARY KEY (`task_id`)");

	        $this->db->query("ALTER TABLE `task_status`
 ADD PRIMARY KEY (`task_status_id`)");
 
     $this->db->query("ALTER TABLE `tasks`
MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1");

     $this->db->query("ALTER TABLE `task_status`
MODIFY `task_status_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1");
	 

    }

}