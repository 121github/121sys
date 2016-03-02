
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_101 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {	
		$this->db->query("CREATE TABLE IF NOT EXISTS `custom_panels` (
  `custom_panel_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `display` varchar(10) NOT NULL DEFAULT 'table',
  `modal_cols` INT NOT NULL DEFAULT '1',
  `table_class` VARCHAR( 100 ) NOT NULL,
  PRIMARY KEY (`custom_panel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

		$this->db->query("CREATE TABLE IF NOT EXISTS `custom_panel_fields` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `custom_panel_id` int(11) NOT NULL,
  `format` VARCHAR( 20 ) NOT NULL DEFAULT 'd/m/y',
  `modal_column` INT NOT NULL DEFAULT '1',
   `tooltip` VARCHAR( 250 ) NOT NULL,
  PRIMARY KEY (`field_id`),
  KEY (`custom_panel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

$this->db->query("CREATE TABLE IF NOT EXISTS `custom_panel_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subtext` varchar(100) NOT NULL,
  PRIMARY KEY (`option_id`),
  KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
		
		
$this->db->query("CREATE TABLE IF NOT EXISTS `campaign_custom_panels` (
  `campaign_id` int(11) NOT NULL,
  `custom_panel_id` int(11) NOT NULL,
  UNIQUE KEY `campaign_id` (`campaign_id`,`custom_panel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

	$this->db->query("CREATE TABLE IF NOT EXISTS `custom_panel_values` (
  `id` int(11) NOT NULL  AUTO_INCREMENT ,
  `data_id` INT NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `urn` (`data_id`,`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

	$this->db->query("CREATE TABLE IF NOT EXISTS `custom_panel_data` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` INT NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

	$this->db->query("ALTER TABLE `custom_panel_values` ADD FOREIGN KEY ( `data_id` ) REFERENCES `custom_panel_data` (
`data_id`
) ON DELETE CASCADE ON UPDATE CASCADE");
	}


	 public function down()
    {
		
	}
	
}