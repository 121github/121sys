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
    	
    	//Dumping structure for table 121sys.default_hours
    	$this->db->query("CREATE TABLE IF NOT EXISTS `default_hours` (
    			`default_hours_id` int(5) NOT NULL AUTO_INCREMENT,
    			`user_id` int(5) NOT NULL DEFAULT '0',
    			`campaign_id` int(5) NOT NULL DEFAULT '0',
    			`duration` int(20) NOT NULL DEFAULT '0' COMMENT 'in minutes',
    			PRIMARY KEY (`default_hours_id`),
    			KEY `FK_default_hours_users` (`user_id`),
    			KEY `FK_default_hours_campaigns_` (`campaign_id`),
    			CONSTRAINT `FK_default_hours_campaigns_` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    			CONSTRAINT `FK_default_hours_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
    	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Default working time by agent for a particular campaign'");



        //Create time table
        $this->db->query("CREATE TABLE IF NOT EXISTS `time` (
			`time_id` int(11) NOT NULL AUTO_INCREMENT,
			`user_id` int(5) NOT NULL COMMENT 'The Agent',
			`start_time` time NOT NULL DEFAULT '00:00:00',
    		`end_time` time NOT NULL DEFAULT '230:59:59',
    		`date` datetime NOT NULL,
    		`comment` text,
			`updated_id` int(5),
			`updated_date` datetime,
			PRIMARY KEY (`time_id`)
		) COMMENT 'Agent time by campaign and day'
		ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        $this->db->query("ALTER TABLE `hours` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `hours` ADD FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`campaign_id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

    	//Dumping structure for table 121sys.default_time
    	$this->db->query("CREATE TABLE IF NOT EXISTS `default_time` (
    			`default_time_id` int(5) NOT NULL AUTO_INCREMENT,
    			`user_id` int(5) NOT NULL DEFAULT '0',
    			`start_time` time NOT NULL DEFAULT '00:00:00',
    			`end_time` time NOT NULL DEFAULT '230:59:59',
    			PRIMARY KEY (`default_time_id`),
    			KEY `FK__users` (`user_id`),
    			CONSTRAINT `FK__users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
    	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Default time defined for an agent'");


        //Dumping structure for table time_exception_type
        $this->db->query("CREATE TABLE IF NOT EXISTS `time_exception_type` (
    			`exception_type_id` int(11) NOT NULL AUTO_INCREMENT,
    			`exception_name` varchar(50) CHARACTER SET utf8 NOT NULL,
    			`paid` tinyint(1) NOT NULL,
    			PRIMARY KEY (`exception_type_id`)
    	) COMMENT 'Exception types'
    	ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Dumping structure for table time_exception
        $this->db->query("CREATE TABLE IF NOT EXISTS `time_exception` (
    			`exception_id` int(11) NOT NULL AUTO_INCREMENT,
    			`time_id` int(5) NOT NULL,
    			`exception_type_id` int(5) NOT NULL,
    			`duration` int(20) NOT NULL DEFAULT '0' COMMENT 'in minutes',
    			PRIMARY KEY (`exception_id`)
    	) COMMENT 'Exception time'
    	ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        $this->db->query("ALTER TABLE `time_exception` ADD FOREIGN KEY (`time_id`) REFERENCES `time`(`time_id`) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE `time_exception` ADD FOREIGN KEY (`exception_type_id`) REFERENCES `time_exception_type`(`exception_type_id`) ON DELETE CASCADE ON UPDATE CASCADE");
    }
    public function down()
    {
		$this->db->query("drop table hours");
        $this->db->query("drop table default_hours");
        $this->db->query("drop table time");
        $this->db->query("drop table default_time");
    }
}


