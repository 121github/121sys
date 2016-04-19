<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_120 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

        $this->db->query("CREATE TABLE IF NOT EXISTS `google_calendar` (
                          google_calendar_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                          user_id INT(11) NOT NULL,
                          campaign_id INT(11) NOT NULL,
                          calendar_id VARCHAR(255) NOT NULL,
                          calendar_name VARCHAR(255) NOT NULL,
                          api_id INT(11) NOT NULL,
                          UNIQUE KEY `user_id` (`user_id`,`campaign_id`),
                          FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
                          FOREIGN KEY (campaign_id) REFERENCES campaigns (campaign_id) ON DELETE CASCADE ON UPDATE CASCADE,
                          FOREIGN KEY (api_id) REFERENCES apis (api_id) ON DELETE CASCADE ON UPDATE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
        );

        $check = $this->db->query("SHOW COLUMNS FROM `apis` LIKE 'calendar_id'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE `apis` DROP `calendar_id`");
        }


        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'google sync', 'Calendar')");
        $id = $this->db->insert_id();
        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', $id)");

	}
	
	 public function down()
    {
	}

}