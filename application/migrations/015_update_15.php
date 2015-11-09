
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_15 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 15");
		


$this->db->query("CREATE TABLE IF NOT EXISTS `sms_history` (
  `sms_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sent_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `body` mediumtext CHARACTER SET utf8 NOT NULL,
  `send_from` varchar(255) CHARACTER SET utf8 NOT NULL,
  `send_to` varchar(255) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `read_confirmed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 if the user read the sms',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `read_confirmed_date` timestamp NULL DEFAULT NULL,
  `template_unsubscribe` tinyint(1) NOT NULL DEFAULT '0',
  `pending` tinyint(4) NOT NULL DEFAULT '0',
  `cron_code` int(11) DEFAULT NULL,
  PRIMARY KEY (`sms_id`),
  KEY `FK2_user_id` (`user_id`),
  KEY `FK3_record_urn` (`urn`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

$this->db->query("CREATE TABLE IF NOT EXISTS `sms_templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `template_body` longtext CHARACTER SET utf8 NOT NULL,
  `template_from` varchar(255) CHARACTER SET utf8 NOT NULL,
  `template_unsubscribe` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

$this->db->query("CREATE TABLE IF NOT EXISTS `sms_template_to_campaigns` (
  `template_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  UNIQUE KEY `tempcamp` (`template_id`,`campaign_id`),
  KEY `template_id` (`template_id`),
  KEY `campanign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$this->db->query("CREATE TABLE IF NOT EXISTS `sms_triggers` (
  `trigger_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`trigger_id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");


$this->db->query("CREATE TABLE IF NOT EXISTS `sms_trigger_recipients` (
  `trigger_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `trigger_id` (`trigger_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$this->db->query("CREATE TABLE IF NOT EXISTS `sms_unsubscribe` (
  `unsubscribe_id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_address` varchar(100) NOT NULL,
  `client_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(20) NOT NULL,
  PRIMARY KEY (`unsubscribe_id`),
  UNIQUE KEY `sms_address` (`sms_address`,`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

/*
$this->db->query("ALTER TABLE `sms_template_to_campaigns`
  ADD CONSTRAINT `FK_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_template` FOREIGN KEY (`template_id`) REFERENCES `sms_templates` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE");
*/
$this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'send sms', 'SMS')");

$this->db->query("INSERT ignore INTO `campaign_features` (`feature_id`, `feature_name`, `panel_path`, `permission_id`) VALUES (NULL, 'SMS', 'sms.php', NULL)");

$this->db->query("INSERT ignore INTO `campaign_features` (`feature_id`, `feature_name`, `panel_path`, `permission_id`) VALUES (NULL, 'Slot Availability', 'availability.php', NULL)");

	}
	
}
