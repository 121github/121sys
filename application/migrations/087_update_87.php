<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_87 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 87");
 $check = $this->db->query("SHOW COLUMNS FROM `user_roles` LIKE 'landing_page'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE `user_roles` ADD `landing_page` VARCHAR( 150 ) NOT NULL DEFAULT 'dashboard'");
		}
		
		$this->db->query("INSERT ignore INTO `permissions` (
`permission_id` ,
`permission_name` ,
`permission_group` ,
`description`
)
VALUES (
NULL , 'view record', 'Records', 'Can the user access the record details page to update and edit the record'
)");
$id = $this->db->insert_id();
if($id){
$this->db->query("insert ignore into role_permissions select role_id,$id from user_roles");
}
$this->db->query("INSERT ignore INTO `permissions` (
`permission_id` ,
`permission_name` ,
`permission_group` ,
`description`
)
VALUES (
NULL , 'slot availability', 'Admin', 'Allow the user to manage the availability of attendees for appointments'
)");
$id2 = $this->db->insert_id();
if($id2){
$this->db->query("insert ignore into role_permissions select 1,$id2 from user_roles");
}


$this->db->query("INSERT IGNORE INTO role_permissions(
SELECT role_id, (

SELECT permission_id
FROM permissions
WHERE permission_name = 'view dashboard'
)
FROM user_roles
WHERE role_name <> 'calendar only'
AND role_name <> 'files only' ) ");

	}
	
}