<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_34 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 34");
		$this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'take ownership', 'System')");
	}
	
}

?>