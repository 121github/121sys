
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