<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_116 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

        $this->db->query("CREATE TABLE IF NOT EXISTS `export_to_viewers` (
                          export_forms_id INT(11) NOT NULL,
                          user_id INT(11) NOT NULL,
                          CONSTRAINT `PRIMARY` PRIMARY KEY (export_forms_id, user_id)
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
        );

        $check = $this->db->query("SHOW COLUMNS FROM `dashboards` LIKE 'type'");
        if ($check->num_rows()) {
            $this->db->query("ALTER TABLE dashboards CHANGE type dash_type VARCHAR(50) NOT NULL DEFAULT 'Dashboard'");
        }
	}
	
	 public function down()
    {
	}

}