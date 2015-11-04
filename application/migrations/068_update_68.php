
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_68 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 68");
$check = $this->db->query("select * from permissions where permission_name = 'record options'");
		if(!$check->num_rows()){
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'record options', 'Record Options')");
		   $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'change color', 'Record Options')");
		      $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'change pot', 'Record Options')");
			     $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'change source', 'Record Options')");
				    $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'change icon', 'Record Options')");
					   $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'change campaign', 'Record Options')");
		}

    }
}