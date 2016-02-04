<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_93 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

		$this->firephp->log("starting migration 93");
				 $check = $this->db->query("SHOW COLUMNS FROM `users` LIKE 'calendar'");
        if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `users` ADD `calendar` BOOLEAN NOT NULL DEFAULT TRUE");
		}
	
        $check = $this->db->query("SHOW TABLES like 'dashboards';");
        if(!$check->num_rows()){
            $this->db->query("
                CREATE TABLE dashboards
                (
                    dashboard_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                    name varchar(50) NOT NULL,
                    description text,
                    created_by int NOT NULL,
                    created_date timestamp NOT NULL,
                    updated_by int,
                    updated_date DATETIME
                )
			");
        }

        $check = $this->db->query("SHOW TABLES like 'dashboard_by_campaign';");
        if(!$check->num_rows()){
            $this->db->query("
                CREATE TABLE dashboard_by_campaign
                (
                    dashboard_id int NOT NULL,
                    campaign_id int NOT NULL,
                    FOREIGN KEY (dashboard_id) REFERENCES dashboards (dashboard_id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY (campaign_id) REFERENCES campaigns (campaign_id) ON DELETE CASCADE ON UPDATE CASCADE
                )
			");
        }

        $check = $this->db->query("SHOW TABLES like 'dashboard_by_user';");
        if(!$check->num_rows()){
            $this->db->query("
                CREATE TABLE dashboard_by_user
                (
                    dashboard_id int NOT NULL,
                    user_id int NOT NULL,
                    FOREIGN KEY (dashboard_id) REFERENCES dashboards (dashboard_id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
                )
			");
        }
    }
    public function down(){

    }

}