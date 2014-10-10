<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_7 extends CI_Migration
{
    public function up()
    {
    	//Create hours table
    	$this->db->query("CREATE TABLE IF NOT EXISTS `hours` (
			`hours_id` int(11) NOT NULL AUTO_INCREMENT,
			`user_id` int(5) NOT NULL COMMENT 'The Agent',
			`campaign_id` int(5) NOT NULL,
			`duration` int(20) NOT NULL COMMENT 'in seconds',
			`date` datetime NOT NULL,
			`updated_id` int(5),
			`updated_date` datetime,
			PRIMARY KEY (`hours_id`)
		) COMMENT 'Agent duration by campaign and day'
		ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
    	
    	$this->db->query("ALTER TABLE `hours` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    	$this->db->query("ALTER TABLE `hours` ADD FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`campaign_id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    	
    }
    public function down()
    {
		$this->db->query("drop table hours");
    }
}


