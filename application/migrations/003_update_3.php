<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_3 extends CI_Migration
{
    
    public function __construct()
    {
        $this->load->model('Database_model');
    }
    
    public function up()
    {
		$this->db->query("INSERT INTO `121sys`.`permissions` (
`permission_id` ,
`permission_name` ,
`permission_group`
)
VALUES (
NULL , 'Full calendar', 'Calendar'
), (
NULL , 'Mini Calendar', 'Calendar'
)");


		$this->db->query("ALTER TABLE `outcomes` ADD `disabled` TINYINT NULL DEFAULT NULL");
	}
	    public function down()
    {
		$this->db->query("delete from `permissions` where permission_group = 'Calendar'");
	}
	
}