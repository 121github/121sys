<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_150 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model'); 
    }

    public function up()
    {
		$this->db->query("INSERT ignore INTO `datafields` (`datafield_id`, `datafield_title`, `datafield_alias`, `datafield_select`, `datafield_order`, `datafield_group`, `datafield_table`, `campaign`, `modal_keys`) VALUES
('', 'URN', 'urn', 'h.urn', 'h.urn', 'History', 'history', NULL, 1),
('', 'Outcome', 'houtcome', 'ho.outcome', 'ho.outcome', 'History', 'history_outcomes', NULL, 1),
('', 'Date', 'hcontact', 'date_format(h.contact,\'%d/%m/%Y %H:%i\')', 'h.contact', 'History', 'history', NULL, 1),
('', 'Description', 'hdescription', 'h.description', 'h.description', 'History', 'history', NULL, 1),
('', 'Campaign', 'hcampaign', 'hist_camp.campaign_name', 'hist_camp.campaign_name', 'History', 'history_campaign', NULL, 1),
('', 'Reason', 'houtcomereason', 'hor.outcome_reason', 'hor.outcome_reason', 'History', 'history_reasons', NULL, 1),
('', 'Comment', 'hcomments', 'h.comments', 'h.comments', 'History', 'history', NULL, 1),
('', 'User', 'huser', 'hu.name', 'hu.name', 'History', 'history_users', NULL, 1),
('', 'Team', 'hteam', 'ht.team_name', 'ht.team_name', 'History', 'history_teams', NULL, 1),
('', 'Group', 'hgroup', 'hg.group_name', 'hg.group_name', 'History', 'history_groups', NULL, 1)");

	  $check = $this->db->query("select * from datatables_table_names where table_name = 'History'");
        if (!$check->num_rows()) {	
$this->db->query("INSERT INTO `datatables_table_names` (`table_id`, `table_name`) VALUES
(2, 'History')");
		}
		
$this->db->query("INSERT ignore INTO `datatables_table_fields` (`table_id`, `datafield_id`) (select 2,datafield_id from datafields where datafield_group='Company')");
$this->db->query("INSERT ignore INTO `datatables_table_fields` (`table_id`, `datafield_id`) (select 2,datafield_id from datafields where datafield_group='History')");
$this->db->query("INSERT ignore INTO `datatables_table_fields` (`table_id`, `datafield_id`) (select 2,datafield_id from datafields where datafield_group='Extra Fields')");
$this->db->query("INSERT ignore INTO `datatables_table_fields` (`table_id`, `datafield_id`) (select 2,datafield_id from datafields where datafield_table='Contacts')");
		

	}
	
}