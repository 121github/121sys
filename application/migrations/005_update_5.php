
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_5 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 5");

        //add new permissions
        $this->db->query("alter table record_planner add unique(user_id,urn)"); 
		 $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'use timer', 'System')");
		 	 $this->db->query("INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('5', '129')");
	}
	
}