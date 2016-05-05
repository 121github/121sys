<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_127 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		
		$this->db->query("ALTER TABLE  `records` ADD  `group_id` INT NULL DEFAULT NULL AFTER  `team_id` ,
ADD INDEX (  `group_id` )");

		$this->db->query("ALTER TABLE  `records` ADD  `branch_id` INT NULL DEFAULT NULL AFTER  `pot_id` ,
ADD  `region_id` INT NULL DEFAULT NULL AFTER  `branch_id` ,
ADD INDEX (  `branch_id` ,  `region_id` )");

$this->db->query("update permissions set permission_group = 'Global filter' where permission_name = 'filter outcomes'");
		$this->db->query("INSERT ignore INTO `permissions` (
`permission_id` ,
`permission_name` ,
`permission_group` ,
`description`
)
VALUES (
NULL , 'filter postcode', 'Global Filter', 'Show the postcode field in the global filter'
),(
NULL , 'filter user', 'Global Filter', 'Show the users field in the global filter'
),(
NULL , 'filter distance', 'Global Filter', 'Show the distance field in the global filter'
),(
NULL , 'filter group', 'Global Filter', 'Show the groups field in the global filter'
),(
NULL , 'filter branch', 'Global Filter', 'Show the branches field in the global filter'
),(
NULL , 'filter region', 'Global Filter', 'Show the regions field in the global filter'
),(
NULL , 'filter team', 'Global Filter', 'Show the team field in the global filter'
),(
NULL , 'filter source', 'Global Filter', 'Show the source field in the global filter'
),(
NULL , 'filter pot', 'Global Filter', 'Show the pot field in the global filter'
)");

$this->db->query("insert ignore into role_permissions select role_id,(select permission_id from permissions where permission_name = 'filter postcode') from user_roles");
$this->db->query("insert ignore into role_permissions select role_id,(select permission_id from permissions where permission_name = 'filter distance') from user_roles");
$this->db->query("insert ignore into role_permissions select role_id,(select permission_id from permissions where permission_name = 'filter pot') from user_roles");
$this->db->query("insert ignore into role_permissions select role_id,(select permission_id from permissions where permission_name = 'filter source') from user_roles");

	}
	
}