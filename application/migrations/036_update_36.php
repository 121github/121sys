<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_36 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 36");
        //Adding branch_area table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `branch_area`
            (
                `branch_area_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `postcode` VARCHAR(50) NOT NULL,
                `location_id` INT NOT NULL,
                `covered_area` INT,
                CONSTRAINT `FK_branch_area_location_id` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
        ");

        //Adding branch table
		$this->db->query("
		    CREATE TABLE IF NOT EXISTS `branch`
            (
                `branch_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `branch_name` VARCHAR(100) NOT NULL,
                `branch_area_id` INT,
                CONSTRAINT `FK_branch_area_id` FOREIGN KEY (`branch_area_id`) REFERENCES `branch_area` (`branch_area_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
		");

        //Adding branch_campaign table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `branch_campaign`
            (
                `branch_campaign_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `branch_id` INT NOT NULL,
                `campaign_id` INT NOT NULL,
                CONSTRAINT `FKbc_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`),
                CONSTRAINT `FKbc_campaign_id` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
        ");

        //Adding branch_user table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `branch_user`
            (
                `branch_campaign_id` INT NOT NULL,
                `user_id` INT NOT NULL,
                PRIMARY KEY (`branch_campaign_id`,`user_id`),
                UNIQUE KEY `branch_user_id` (`branch_campaign_id`,`user_id`),
                CONSTRAINT `FKbu_branch_campaign_id` FOREIGN KEY (`branch_campaign_id`) REFERENCES `branch_campaign` (`branch_campaign_id`),
                CONSTRAINT `FKbu_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
        ");

        //Add new permission for the planner
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'admin planner', 'Planner')");
	}
	
}
?>