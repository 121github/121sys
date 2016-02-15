<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_95 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 95");

        $check = $this->db->query("SHOW TABLES like 'export_graphs'");
        if(!$check->num_rows()){
            $this->db->query("
                CREATE TABLE export_graphs
                (
                    graph_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    export_forms_id INT NOT NULL,
                    name VARCHAR(50) NOT NULL,
                    type VARCHAR(25) NOT NULL,
                    x_value VARCHAR(50) NOT NULL,
                    y_value VARCHAR(50),
                    z_value VARCHAR(50),
                    CONSTRAINT export_graphs_fk_export_id FOREIGN KEY (export_forms_id) REFERENCES export_forms (export_forms_id) ON DELETE CASCADE ON UPDATE CASCADE
                )
			");
        }
		
    }
    public function down(){

    }

}