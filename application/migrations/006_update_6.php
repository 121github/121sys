<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_6 extends CI_Migration
{
    public function up()
    {

		$this->db->query("CREATE TABLE IF NOT EXISTS `hours_logged` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `start_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=innoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

$this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'campaign search', 'System')");
$this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'show footer', 'System')");
$this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'keep_records', 'System')");
$this->db->query("ALTER TABLE `records` CHANGE `dials` `dials` INT(2) NULL DEFAULT '0'");
$this->db->query("update records set dials = 0 where dials is null");
    }
    public function down()
    {
		$this->db->query("drop table hours_logged");
    }
}




