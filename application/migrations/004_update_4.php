<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_4 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 4");

        //add new permissions
        $this->db->query("INSERT ignore INTO `121sys`.`permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'admin groups', 'Admin')");
		        $this->db->query("INSERT ignore INTO `121sys`.`permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'campaign fields', 'Admin')");
				        $this->db->query("INSERT ignore INTO `121sys`.`permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'admin teams', 'Admin')");
						        $this->db->query("INSERT ignore INTO `121sys`.`permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'admin roles', 'Admin')");
								        $this->db->query("INSERT ignore INTO `121sys`.`permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'admin users', 'Admin')");
	}
	
}