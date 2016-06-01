<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_149 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model'); 
    }

    public function up()
    {

      $this->db->query("delete from permissions where permission_name = 'search parked'");
	  $this->db->query("delete from permissions where permission_name = 'search any owner'");
	  $this->db->query("delete from permissions where permission_name = 'search unassigned'");
	  $this->db->query("delete from permissions where permission_name = 'search groups'");
	  $this->db->query("delete from permissions where permission_name = 'search teams'");
	  $this->db->query("delete from permissions where permission_name = 'search dead'");
	  $this->db->query("delete from permissions where permission_name = 'search surveys'");
	  $this->db->query("update permissions set permission_name = 'quick search' where permission_name = 'search records'");	  
	  $this->db->query("insert ignore into permissions set permission_name = 'advanced search',permission_group='Search'");
	  
	  $this->db->query("delete from permissions where permission_name = 'files only'");
	  $this->db->query("delete from permissions where permission_name = 'survey only'");
	    $this->db->query("delete from permissions where permission_name = 'mix campaigns'");
		  $this->db->query("delete from permissions where permission_name = 'all campaigns'");
	  
	   $this->db->query("update permissions set description = 'Do the campaign permissions affect users with this role' where permission_name = 'campaign override'");
	   
	   $this->db->query("DELETE FROM role_permissions WHERE permission_id NOT IN (
SELECT permission_id
FROM permissions");


$this->db->query("DELETE FROM campaign_permissions WHERE permission_id NOT IN (
SELECT permission_id
FROM permissions)");
	  

    }
}

