<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_125 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		
		$this->db->query("INSERT ignore INTO `permissions` (
`permission_id` ,
`permission_name` ,
`permission_group` ,
`description`
)
VALUES (
NULL , 'own appointments', 'Calendar', 'The user can only see their own appointments'
)");

	}
	
}