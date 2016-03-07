<<<<<<< HEAD
=======

>>>>>>> 1ac52d2be3b39fc04ac46772cb9b2f937597bd8a
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_104 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {	
		
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`, `description`) VALUES (NULL, 'check overlap', 'Appointments', 'Check Overlap Appointments on save for the same record and attendee')");
        $id = $this->db->insert_id();
        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', $id)");

        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`, `description`) VALUES (NULL, 'overlap appointment', 'Appointments', 'Can Overlap Appointments on save for the same record and attendee')");
        $id = $this->db->insert_id();
        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', $id)");

	}
	
	 public function down()
    {
		
	}
}