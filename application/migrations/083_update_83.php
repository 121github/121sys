<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_83 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 83");
		
		$this->db->query("CREATE TABLE hour_exception_type
                        (
                            exception_type_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                            exception_name varchar(50),
                            paid tinyint DEFAULT 0
                        )");

        $this->db->query("CREATE TABLE `121sys_accept`.hour_exception
                        (
                            exception_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                            hour_id int NOT NULL,
                            exception_type_id int NOT NULL,
                            duration int DEFAULT 0 NOT NULL,
                            FOREIGN KEY (hour_id) REFERENCES hours (hours_id),
                            FOREIGN KEY (exception_type_id) REFERENCES hour_exception_type (exception_type_id)
                        )");

	}
	 public function down()
    {
	}
}