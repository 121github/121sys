<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_94 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 94");

        $check = $this->db->query("SHOW TABLES like 'dashboard_reports'");
        if(!$check->num_rows()){
            $this->db->query("
                CREATE TABLE dashboard_reports
                (
                    dashboard_id int NOT NULL,
                    report_id int NOT NULL,
                    column_size int DEFAULT 1 NOT NULL,
                    position int NOT NULL,
                    PRIMARY KEY (dashboard_id,report_id),
                    FOREIGN KEY (dashboard_id) REFERENCES dashboards (dashboard_id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY (report_id) REFERENCES export_forms (export_forms_id) ON DELETE CASCADE ON UPDATE CASCADE
                )
			");

            $this->db->query("CREATE UNIQUE INDEX dashboard_reports_order_uindex ON dashboard_reports (`position`)");
        }
		
        $this->db->query("CREATE TABLE IF NOT EXISTS `apis` (
              `api_id` int(11) NOT NULL AUTO_INCREMENT,
              `api_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
              `api_token` smallint(6) NOT NULL,
              `user_id` int(11) NOT NULL,
              `date_added` datetime NOT NULL,
              `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`api_id`),
              UNIQUE KEY `api_name` (`api_name`,`user_id`),
              KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        $this->db->query("INSERT ignore INTO `permissions` (
            `permission_id` ,
            `permission_name` ,
            `permission_group` ,
            `description`
            )
            VALUES (
            NULL , 'dashboard viewers', 'Dashboards', 'Can the user set the access for the custom dashboards'
        )");
        $id = $this->db->insert_id();
        if($id){
            $this->db->query("insert ignore into role_permissions VALUES (1,$id)");
        }
		
    }
    public function down(){

    }

}