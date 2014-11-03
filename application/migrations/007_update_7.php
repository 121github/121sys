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
    		`time_logged` int(20) NOT NULL COMMENT 'in seconds',
			`date` datetime NOT NULL,
    		`comment` text,
			`updated_id` int(5),
			`updated_date` datetime,
			PRIMARY KEY (`hours_id`)
		) COMMENT 'Agent duration by campaign and day'
		ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
    	
    	$this->db->query("ALTER TABLE `hours` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    	$this->db->query("ALTER TABLE `hours` ADD FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`campaign_id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    	
    	//Dumping structure for table hours_exception_type
    	$this->db->query("CREATE TABLE IF NOT EXISTS `hours_exception_type` (
    			`exception_type_id` int(11) NOT NULL AUTO_INCREMENT,
    			`exception_name` varchar(50) CHARACTER SET utf8 NOT NULL,
    			`paied` tinyint(1) NOT NULL,
    			PRIMARY KEY (`exception_type_id`)
    	) COMMENT 'Exception types'
    	ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
    	
    	//Dumping structure for table hours_exception
    	$this->db->query("CREATE TABLE IF NOT EXISTS `hours_exception` (
    			`exception_id` int(11) NOT NULL AUTO_INCREMENT,
    			`hours_id` int(5) NOT NULL,
    			`exception_type_id` int(5) NOT NULL,
    			PRIMARY KEY (`exception_id`)
    	) COMMENT 'Exception by campaign and agent'
    	ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
    	
    	$this->db->query("ALTER TABLE `hours_exception` ADD FOREIGN KEY (`hours_id`) REFERENCES `hours`(`hours_id`) ON DELETE CASCADE ON UPDATE CASCADE");
    	$this->db->query("ALTER TABLE `hours_exception` ADD FOREIGN KEY (`exception_type_id`) REFERENCES `hours_exception_type`(`exception_type_id`) ON DELETE CASCADE ON UPDATE CASCADE");
    	
    }
    public function down()
    {
		$this->db->query("drop table hours");
    }
}


