
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_57 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		  $this->firephp->log("starting migration 57");
		$check = $this->db->query("select * from `permissions` where permission_name = 'edit outcome'");
		if(!$check->num_rows()){
		$this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'edit outcome', 'History')");
		}
	}
}
