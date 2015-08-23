<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_46 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
			
		
      $this->firephp->log("starting migration 46");

      $this->db->query("CREATE TABLE appointments_ics
                        (
                            appointments_ics_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                            appointment_id INT NOT NULL,
                            start_date DATETIME NOT NULL,
                            duration INT NOT NULL,
                            title VARCHAR(50) NOT NULL,
                            description TEXT NOT NULL,
                            location VARCHAR(255) NOT NULL,
                            method VARCHAR(50) DEFAULT 'REQUEST' NOT NULL,
                            uid VARCHAR(50) NOT NULL,
                            sequence INT DEFAULT 0 NOT NULL,
                            send_to VARCHAR(255) NOT NULL,
                            send_date TIMESTAMP NOT NULL,
                            CONSTRAINT `FK_appointments_ics_appointment_id` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1
        ");


		$check = $this->db->query("SHOW COLUMNS FROM `users` LIKE 'home_postcode'");
		if(!$check->num_rows()){
        $this->db->query("ALTER TABLE users ADD home_postcode VARCHAR(10) NOT NULL");
		}
		
		$check = $this->db->query("SHOW COLUMNS FROM `users` LIKE 'vehicle_reg'");
		if(!$check->num_rows()){
        $this->db->query("ALTER TABLE users ADD vehicle_reg VARCHAR(10) NOT NULL");
		}	
		
		$check = $this->db->query("SHOW COLUMNS FROM `users` LIKE 'custom'");
			if(!$check->num_rows()){
        $this->db->query("ALTER TABLE users ADD custom VARCHAR(50) NOT NULL");
			}
				$check = $this->db->query("SHOW COLUMNS FROM `users` LIKE 'ics'");
				if(!$check->num_rows()){
        $this->db->query("ALTER TABLE users ADD ics TINYINT DEFAULT 0 NOT NULL");
				}
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'import ics', 'Appointments')");
        $id = $this->db->insert_id();
        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', $id)");
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'export ics', 'Appointments')");
        $id = $this->db->insert_id();
        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', $id)");

    }

}