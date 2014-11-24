<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_4 extends CI_Migration
{
    
    public function __construct()
    {
        $this->load->model('Database_model');
    }
    
    public function up()
    {
		$this->db->query("INSERT INTO `campaign_features` (`feature_id`, `feature_name`, `panel_path`, `permission_id`) VALUES
(14, 'Webform', 'webform.php', NULL)");


		$this->db->query("CREATE TABLE IF NOT EXISTS `webforms` (
  `webform_id` int(11) NOT NULL AUTO_INCREMENT,
  `webform_path` varchar(100) DEFAULT NULL,
  `webform_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`webform_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2
");


		/*$this->db->query("INSERT INTO `webforms` (`webform_id`, `webform_path`, `webform_name`) VALUES
(1, 'tenalps.php', 'Tenalps Order Form')
");*/

		$this->db->query("CREATE TABLE IF NOT EXISTS `webforms_to_campaigns` (
  `webform_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  UNIQUE KEY `webform_id_2` (`webform_id`,`campaign_id`),
  KEY `webform_id` (`webform_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
");
		$this->db->query("CREATE TABLE IF NOT EXISTS `webform_answers` (
  `webform_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `a1` varchar(255) NOT NULL,
  `a2` varchar(255) NOT NULL,
  `a3` varchar(255) NOT NULL,
  `a4` varchar(255) NOT NULL,
  `a5` varchar(255) NOT NULL,
  `a6` varchar(255) NOT NULL,
  `a7` varchar(255) NOT NULL,
  `a8` varchar(255) NOT NULL,
  `a9` varchar(255) NOT NULL,
  `a10` varchar(255) NOT NULL,
  `a11` varchar(255) NOT NULL,
  `a12` varchar(255) NOT NULL,
  `a13` varchar(255) NOT NULL,
  `a14` varchar(255) NOT NULL,
  `a15` varchar(255) NOT NULL,
  `a16` varchar(255) NOT NULL,
  `a17` varchar(255) NOT NULL,
  `a18` varchar(255) NOT NULL,
  `a19` varchar(255) NOT NULL,
  `a20` varchar(255) NOT NULL,
  `a21` varchar(255) NOT NULL,
  `a22` varchar(255) NOT NULL,
  `a23` varchar(255) NOT NULL,
  `a24` varchar(255) NOT NULL,
  `a25` varchar(255) NOT NULL,
  `a26` varchar(255) NOT NULL,
  `a27` varchar(255) NOT NULL,
  `a28` varchar(255) NOT NULL,
  `a29` varchar(255) NOT NULL,
  `a30` varchar(255) NOT NULL,
  UNIQUE KEY `webform_id_2` (`webform_id`,`urn`),
  KEY `urn` (`urn`),
  KEY `webform_id` (`webform_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
");


	}
	    public function down()
    {
		$this->db->query("delete from `campaign_features` where feature_name = 'Webform'");
	}
	
}