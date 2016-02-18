
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_98 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		 $check = $this->db->query("SHOW COLUMNS FROM `appointments` LIKE 'appointment_confirmed'");
        if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `appointments` ADD `appointment_confirmed` TINYINT( 1 ) NOT NULL DEFAULT '0'");
		$this->db->query("ALTER TABLE `campaigns` ADD `app_confirmation_days` TINYINT( 1 ) NULL DEFAULT NULL");
		}
		
		$this->db->query("INSERT ignore into `permissions` (`permission_id`, `permission_name`, `permission_group`, `description`) VALUES (NULL, 'confirm appointment', 'Appointments', 'Appointments are unconfirmed until somebody manually ticks the confirmed box within an appointment')");
		
        $check = $this->db->query("SHOW COLUMNS FROM `company_addresses` LIKE 'description'");
        if(!$check->num_rows()){
		    $this->db->query("ALTER TABLE `company_addresses` ADD `description` VARCHAR(100) DEFAULT 'Address'");
		}

        $check = $this->db->query("SHOW COLUMNS FROM `company_addresses` LIKE 'visible'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE `company_addresses` ADD `visible` TINYINT DEFAULT 1");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `contact_addresses` LIKE 'description'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE `contact_addresses` ADD `description` VARCHAR(100) DEFAULT 'Address'");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `contact_addresses` LIKE 'visible'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE `contact_addresses` ADD `visible` TINYINT DEFAULT 1");
        }
	}
}