<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_84 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 84");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS user_address
                            (
                                address_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                                user_id int NOT NULL,
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
                                `primary` tinyint(1),
                                FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
                            )");

	}
	 public function down()
    {
	}
}