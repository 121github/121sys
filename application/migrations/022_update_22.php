<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_22 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 22");
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'campaign access', 'Admin')");
		   $this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'campaign setup', 'Admin')");
		
		  $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '119')");
 		$this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '119')");

		  $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '120')");
 		$this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '120')"); 
    }


}