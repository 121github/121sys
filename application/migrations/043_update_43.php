<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_43 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
			
		
        $this->firephp->log("starting migration 43");

        //add new permissions
		$this->db->query("drop table IF EXISTS branch");
		$this->db->query("drop table IF EXISTS branch_user");
		$this->db->query("drop table IF EXISTS branch_campaign");
		$this->db->query("drop table IF EXISTS branch_area");
		$this->db->query("drop table IF EXISTS branch_addresses");
		$this->db->query("drop table IF EXISTS branch_campaigns");
		$this->db->query("drop table IF EXISTS branch_users");
		$this->db->query("drop table IF EXISTS branch_regions");
		$this->db->query("drop table IF EXISTS branch_region_users");
		$this->db->query("drop table IF EXISTS branch");
		
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'set call direction', 'Records')");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `branch` (
  `branch_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(100) NOT NULL,
  `branch_email` varchar(250) NOT NULL,
  `branch_status` tinyint(1) NOT NULL DEFAULT '1',
  `region_id` int(11) DEFAULT NULL,
   `map_icon` varchar(50) NULL,
  PRIMARY KEY (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");


// Table structure for table `branch_address`

		$this->db->query("CREATE TABLE IF NOT EXISTS `branch_addresses` (
  `branch_id` int(11) NOT NULL,
  `add1` varchar(150) NOT NULL,
  `add2` varchar(150) NOT NULL,
  `add3` varchar(150) NOT NULL,
  `add4` varchar(150) NOT NULL,
  `county` varchar(50) NOT NULL,
  `postcode` varchar(50) NOT NULL,
  `location_id` int(11) NOT NULL,
  `covered_area` int(11) DEFAULT NULL,
  PRIMARY KEY (`branch_id`),
  KEY `FK_branch_area_location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");


// Table structure for table `branch_campaign`


		$this->db->query("CREATE TABLE IF NOT EXISTS `branch_campaigns` (
  `branch_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  UNIQUE KEY `branch_id_2` (`branch_id`,`campaign_id`),
  KEY `branch_id` (`branch_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");



// Table structure for table `branch_regions`


		$this->db->query("CREATE TABLE IF NOT EXISTS `branch_regions` (
  `region_id` int(11) NOT NULL AUTO_INCREMENT,
  `region_name` varchar(100) NOT NULL,
  `region_email` varchar(100) NOT NULL,
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1") ;


// Table structure for table `branch_region_users`


		$this->db->query("CREATE TABLE IF NOT EXISTS `branch_region_users` (
  `region_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `region_id` (`region_id`,`user_id`),
  KEY `region_id_2` (`region_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");




// Table structure for table `branch_user`


		$this->db->query("CREATE TABLE IF NOT EXISTS `branch_user` (
  `branch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`branch_id`,`user_id`),
  KEY `branch_id` (`branch_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");


// Constraints for dumped tables



// Constraints for table `branch_address`

		$this->db->query("ALTER TABLE `branch_addresses`
  ADD CONSTRAINT `FK_branch_area_location_id` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`)");


// Constraints for table `branch_campaign`

		$this->db->query("ALTER TABLE `branch_campaigns`
  ADD CONSTRAINT `FKbc_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`),
  ADD CONSTRAINT `FKbc_campaign_id` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`)");


// Constraints for table `branch_user`

		$this->db->query("ALTER TABLE `branch_user`
  ADD CONSTRAINT `branch_user_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FKbu_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE");

		
    }

}