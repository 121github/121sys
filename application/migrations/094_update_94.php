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

        $check = $this->db->query("SHOW TABLES like 'dashboard_reports';");
        if(!$check->num_rows()){
            $this->db->query("
                CREATE TABLE dashboard_reports
                (
                    dashboard_id int NOT NULL,
                    report_id int NOT NULL,
                    column_size int DEFAULT 1 NOT NULL,
                    PRIMARY KEY (dashboard_id,report_id),
                    FOREIGN KEY (dashboard_id) REFERENCES dashboards (dashboard_id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY (report_id) REFERENCES export_forms (export_forms_id) ON DELETE CASCADE ON UPDATE CASCADE
                )
			");
        }
    }
    public function down(){

    }

}