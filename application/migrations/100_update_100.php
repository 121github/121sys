
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_100 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

        $this->db->query("CREATE TABLE IF NOT EXISTS `referral` (
                              `referral_id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                              `title` varchar(10) NOT NULL,
                              `firstname` varchar(50) NOT NULL,
                              `lastname` varchar(50) NOT NULL,
                              `telephone_number` varchar(15),
                              `mobile_number` varchar(15),
                              `other_number` varchar(15),
                              `email` varchar(50),
                              `user_id` INT,
                              `urn` INT NOT NULL,
                              FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
                              FOREIGN KEY (urn) REFERENCES records (urn) ON DELETE CASCADE ON UPDATE CASCADE
                            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
        );

        $this->db->query("CREATE TABLE IF NOT EXISTS referral_address
                            (
                                address_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                                referral_id int NOT NULL,
                                location_id int,
                                add1 varchar(100),
                                add2 varchar(100),
                                add3 varchar(100),
                                add4 varchar(100),
                                locality varchar(100),
                                city varchar(100),
                                postcode varchar(11),
                                county varchar(100),
                                country varchar(100),
                                description varchar(255),
                                `primary` TINYINT(1) DEFAULT 0 NOT NULL,
                                `visible` TINYINT(1) DEFAULT 0 NOT NULL,
                                FOREIGN KEY (referral_id) REFERENCES referral (referral_id) ON DELETE CASCADE ON UPDATE CASCADE
                            )");

        $this->db->query("INSERT ignore INTO `campaign_features` (feature_id, feature_name, panel_path, permission_id) VALUES (NULL, 'Referral', 'referral.php', NULL)");

        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'add referral', 'Referrals')");
        $id = $this->db->insert_id();
        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', $id)");

        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'delete referral', 'Referrals')");
        $id = $this->db->insert_id();
        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', $id)");

        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'edit referral', 'Referrals')");
        $id = $this->db->insert_id();
        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', $id)");
	
	}
	
	 public function down()
    {
		
	}
}