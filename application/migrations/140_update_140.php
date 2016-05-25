<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_140 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
$this->db->query("INSERT ignore INTO `appointment_types` (`appointment_type_id`, `appointment_type`, `is_default`, `icon`) VALUES
('', 'Imported', 0, 'fa fa-google')");
	}
	
}

