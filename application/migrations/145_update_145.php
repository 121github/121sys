<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_145 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		  $check = $this->db->query("select * from datatables_table_names where table_name = 'Surveys'");
        if (!$check->num_rows()) {	
$this->db->query("INSERT INTO `datatables_table_names` (`table_id`, `table_name`) VALUES
(5, 'Surveys')");
		}
		
		
        $check = $this->db->query("select * from datafields where datafield_title = 'NPS Score'");
        if (!$check->num_rows()) {
            $this->db->query("INSERT ignore INTO `datafields` (`datafield_id`, `datafield_title`, `datafield_alias`, `datafield_select`, `datafield_order`, `datafield_group`, `datafield_table`, `campaign`, `modal_keys`) VALUES
('', 'Survey ID', 'sid', 'surveys.survey_id', 'surveys.survey_id', 'Survey', 'surveys', NULL, 1),
('', 'Survey Name', '', 'survey_name', 'survey_name', 'Survey', 'surveys', NULL, 1),
('', 'Date Created', 'survey_created', 'date_format(surveys.date_created,''%d/%m/%Y %H:%i'')', 'surveys.date_created', 'Survey', 'surveys', NULL, 1),
('', 'Date Completed', 'survey_completed', 'date_format(surveys.completed_date,''%d/%m/%Y %H:%i'')', 'surveys.completed_date', 'Survey', 'surveys', NULL, 1),
('', 'NPS Score', 'nps_score', 'survey_nps.nps_score', 'survey_nps.nps_score', 'Survey', 'survey_nps', NULL, 1),
('', 'Completed by', 'completed_by', 'survey_user.name', 'survey_user.name', 'Survey', 'survey_user', NULL, 1),
('', 'Contact', 'survey_contacts', 'survey_contacts.fullname', 'survey_contacts.fullname', 'Contact', 'survey_contacts', NULL, 1)
");

$this->db->query("INSERT ignore INTO `datatables_table_fields` (`table_id`, `datafield_id`) (select 5,datafield_id from datafields where datafield_group='Survey')");
$this->db->query("INSERT ignore INTO `datatables_table_fields` (`table_id`, `datafield_id`) (select 5,datafield_id from datafields where datafield_group='Company')");
$this->db->query("INSERT ignore INTO `datatables_table_fields` (`table_id`, `datafield_id`) (select 5,datafield_id from datafields where datafield_group='Record')");
$this->db->query("INSERT ignore INTO `datatables_table_fields` (`table_id`, `datafield_id`) (select 5,datafield_id from datafields where datafield_group='Campaign')");
$this->db->query("INSERT ignore INTO `datatables_table_fields` (`table_id`, `datafield_id`) (select 5,datafield_id from datafields where datafield_group='Extra Fields')");
$this->db->query("INSERT ignore INTO `datatables_table_fields` (`table_id`, `datafield_id`) (select 5,datafield_id from datafields where datafield_table='survey_contacts')");
        }
				
		
    }

}

