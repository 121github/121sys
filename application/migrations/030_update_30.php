<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_30 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 30");
		$this->db->query("DROP TABLE IF EXISTS role_outcomes");

		
$this->db->query("CREATE TABLE IF NOT EXISTS `role_outcomes` (
  `role_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  UNIQUE KEY `role_id_2` (`role_id`,`outcome_id`,`campaign_id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `outcome_id` (`outcome_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

	}
	
}