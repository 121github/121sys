<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_5 extends CI_Migration
{
    public function up()
    {
		$this->db->query("ALTER TABLE `teams` ADD `group_id` INT NULL DEFAULT NULL , ADD INDEX (`group_id`)");
		$this->db->query("ALTER TABLE `users` DROP `team_id`");
		$this->db->query("CREATE TABLE IF NOT EXISTS `team_managers` (
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `team_id_2` (`team_id`,`user_id`),
  KEY `team_id` (`team_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

    }
    public function down()
    {
        $this->db->query("ALTER TABLE `teams` drop group_id");
		$this->db->query("ALTER TABLE `users` ADD `team_id` INT NULL DEFAULT NULL , ADD INDEX (`team_id`)");
		$this->db->query("drop table team_managers");
    }
}


