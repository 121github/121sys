<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_128 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `role_data_access` (
  `role_id` int(11) NOT NULL,
  `all_campaigns` tinyint(1) NOT NULL DEFAULT '0',
  `mix_campaigns` tinyint(1) NOT NULL DEFAULT '0',
  `user_records` tinyint(1) NOT NULL DEFAULT '1',
  `unassigned_user` tinyint(1) NOT NULL DEFAULT '1',
  `team_records` tinyint(1) NOT NULL DEFAULT '1',
  `unassigned_team` tinyint(1) NOT NULL DEFAULT '1',
  `group_records` tinyint(1) NOT NULL DEFAULT '0',
  `unassigned_group` tinyint(1) NOT NULL DEFAULT '1',
  `branch_records` tinyint(1) NOT NULL DEFAULT '0',
  `unassigned_branch` tinyint(1) NOT NULL DEFAULT '1',
  `region_records` tinyint(1) NOT NULL DEFAULT '0',
  `unassigned_region` tinyint(1) NOT NULL DEFAULT '1',
  `pending` tinyint(1) NOT NULL DEFAULT '0',
  `dead` tinyint(1) NOT NULL DEFAULT '0',
  `complete` tinyint(1) NOT NULL DEFAULT '0',
  `parked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

	$this->db->query("insert into role_data_access (role_id) select role_id from user_roles");


	}
	
}