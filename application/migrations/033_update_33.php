 ;
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_33 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 33");

        $this->db->query("CREATE TABLE IF NOT EXISTS `datatables_columns` (
  `column_id` int(11) NOT NULL AUTO_INCREMENT,
  `column_title` varchar(100) NOT NULL,
  `column_alias` varchar(50) NOT NULL,
  `column_select` varchar(255) NOT NULL,
  `column_order` varchar(255) NOT NULL,
  `column_group` varchar(50) NOT NULL,
  `column_table` varchar(50) NOT NULL,
  PRIMARY KEY (`column_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42");
     $this->db->query("INSERT INTO `datatables_columns` (`column_id`, `column_title`, `column_alias`, `column_select`, `column_order`, `column_group`, `column_table`) VALUES
(1, 'URN', 'urn', 'r.urn', 'r.urn', 'Record', 'records'),
(2, 'Outcome', '', 'outcome', 'outcome', 'Record', 'outcomes'),
(3, 'Company Name', 'company_name', 'com.name', 'com.name', 'Company', 'companies'),
(4, 'Contact Name', 'contact_name', 'fullname', 'fullname', 'Contact', 'contacts'),
(5, 'Record Status', '', 'status_name', 'status_name', 'Record', 'status_list'),
(6, 'Parked Status', '', 'park_reason', 'park_reason', 'Record', 'park_codes'),
(7, 'Next Action', 'nextcall', 'date_format(r.nextcall,''%d/%m/%Y %H:%i'')', 'r.nextcall', 'Record', 'records'),
(8, 'Last Action', 'lastcall', 'date_format(r.date_updated,''%d/%m/%Y %H:%i'')', 'r.date_updated', 'Record', 'records'),
(9, 'Date Added', 'date_added', 'date_format(r.date_added,''%d/%m/%y %H:%i'')', 'r.date_added', 'Record', 'records'),
(10, 'Sector', '', 'sector_name', 'sector_name', 'Company', 'sectors'),
(11, 'Subector Name', '', 'subsector_name', 'subsector_name', 'Company', 'subsectors'),
(12, 'Company Phone', 'company_telephone', 'comt.telephone_number', 'comt.telephone_number', 'Company', 'company_telephone'),
(13, 'Contact Phone', 'contact_telephone', 'cont.telephone_number', 'cont.telephone_number', 'Contact', 'contact_telephone'),
(14, 'Company Postcode', 'company_postcode', 'coma.postcode', 'coma.postcode', 'Company', 'company_addresses'),
(15, 'Client Ref', '', 'client_ref', 'client_ref', 'Record', 'client_refs'),
(16, 'Icon', 'color_icon', 'CONCAT(IFNULL(r.map_icon,''''),IFNULL(camp.map_icon,''''))', 'r.map_icon', 'Record', 'records'),
(17, 'Campaign', '', 'campaign_name', 'campaign_name', 'Campaign', 'campaigns'),
(18, 'Campaign Type', '', 'campaign_type_desc', 'campaign_type_desc', 'Campaign', 'campaign_types'),
(19, 'Client', '', 'client_name', 'client_name', 'Campaign', 'clients'),
(20, 'Data Source', 'source_name', 'source_name', 'source_name', 'Record', 'data_sources'),
(21, 'c1', '', 'c1', 'c1', 'Extra Fields', 'record_details'),
(23, 'c2', '', 'c2', 'c2', 'Extra Fields', 'record_details'),
(25, 'c3', '', 'c3', 'c3', 'Extra Fields', 'record_details'),
(27, 'c4', '', 'c4', 'c4', 'Extra Fields', 'record_details'),
(29, 'c5', '', 'c5', 'c5', 'Extra Fields', 'record_details'),
(31, 'c6', '', 'c6', 'c6', 'Extra Fields', 'record_details'),
(33, 'n1', '', 'n1', 'n1', 'Extra Fields', 'record_details'),
(34, 'n2', '', 'n2', 'n2', 'Extra Fields', 'record_details'),
(35, 'd1', '', 'd1', 'd1', 'Extra Fields', 'record_details'),
(36, 'd2', '', 'd2', 'd2', 'Extra Fields', 'record_details'),
(37, 'dt1', '', 'dt1', 'dt1', 'Extra Fields', 'record_details'),
(38, 'dt2', '', 'dt2', 'dt2', 'Extra Fields', 'record_details'),
(39, 'Dials', '', 'r.dials', 'r.dials', 'Record', 'records'),
(41, 'Color', 'color_dot', 'r.record_color', 'r.record_color', 'Record', 'records')");

	 $this->db->query("CREATE TABLE IF NOT EXISTS `datatables_table_columns` (
  `table_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  UNIQUE KEY `table_id` (`table_id`,`column_id`),
  KEY `column_id` (`column_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
	 
	  $this->db->query("INSERT INTO `datatables_table_columns` (`table_id`, `column_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 23),
(1, 25),
(1, 27),
(1, 29),
(1, 31),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 41)");
	  
	   $this->db->query("CREATE TABLE IF NOT EXISTS `datatables_table_names` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) NOT NULL,
  PRIMARY KEY (`table_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5");
	   
	   
	    $this->db->query("INSERT INTO `datatables_table_names` (`table_id`, `table_name`) VALUES
(1, 'Records'),
(2, 'History'),
(3, 'Appointments'),
(4, 'Tasks')");
		
		 $this->db->query("CREATE TABLE IF NOT EXISTS `datatables_user_columns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `table_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_2` (`user_id`,`column_id`,`table_id`),
  KEY `column_id` (`column_id`),
  KEY `table_id` (`table_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
	

$this->db->query("ALTER TABLE `record_planner` ADD `planner_status` BOOLEAN NOT NULL DEFAULT TRUE");
	
	$this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'files only', 'Files')");
	
	$this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'survey only', 'Survey')");
	
    }

}