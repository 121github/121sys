<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_install extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        if (!$this->db->query("CREATE DATABASE IF NOT EXISTS `" . $this->db->database . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci")) {
            echo "There was an error creating the database";
        }
        $this->db->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");
        $this->db->query("SET time_zone = '+00:00'");

        //access_log table
        $this->db->query("CREATE TABLE IF NOT EXISTS `access_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL,
  `logon` datetime NOT NULL,
  `lastaction` datetime NOT NULL,
  `logoff` datetime DEFAULT NULL,
  `ip_address` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `answers_to_options`
        $this->db->query("CREATE TABLE IF NOT EXISTS `answers_to_options` (
  `answer_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  UNIQUE KEY `answer_id2` (`answer_id`,`option_id`),
  KEY `answer_id` (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `answer_notes`
        $this->db->query("CREATE TABLE IF NOT EXISTS `answer_notes` (
  `answer_id` int(11) NOT NULL,
  `notes` mediumtext CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `appointments`
        $this->db->query("CREATE TABLE IF NOT EXISTS `appointments` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `text` mediumtext CHARACTER SET utf8 NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `urn` int(11) NOT NULL,
  `postcode` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  `cancellation_reason` VARCHAR(255) NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` INT,
  `date_updated` datetime DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`appointment_id`),
  KEY `postcode` (`postcode`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `appointment_attendees`
        $this->db->query("CREATE TABLE IF NOT EXISTS `appointment_attendees` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `appointment_id_2` (`appointment_id`,`user_id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `attachements`
        $this->db->query("CREATE TABLE IF NOT EXISTS `attachments` (
  `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`attachment_id`),
  KEY `FK_urn` (`urn`),
  KEY `FK_users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Record attachments' AUTO_INCREMENT=1 ;
");


        //Table structure for table `backup_by_campaign`
        $this->db->query("CREATE TABLE IF NOT EXISTS `backup_by_campaign` (
  `backup__by_campaign_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `months_ago` int(11) NOT NULL DEFAULT '0',
  `months_num` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`backup__by_campaign_id`),
  KEY `FK_backup_by_campaign_id` (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Backup by campaign settings' AUTO_INCREMENT=1");


        //Table structure for table `backup_campaign_history`
        $this->db->query("CREATE TABLE IF NOT EXISTS `backup_campaign_history` (
  `backup_campaign_id` int(11) NOT NULL AUTO_INCREMENT,
  `backup_date` datetime NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `update_date_from` date DEFAULT NULL,
  `update_date_to` date DEFAULT NULL,
  `renewal_date_from` date DEFAULT NULL,
  `renewal_date_to` date DEFAULT NULL,
  `num_records` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `restored` tinyint(4) NOT NULL DEFAULT '0',
  `restored_date` datetime DEFAULT NULL,
  PRIMARY KEY (`backup_campaign_id`),
  KEY `FK_backup_campaign_id` (`campaign_id`),
  KEY `FK_backup_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Backup history for the campaigns' AUTO_INCREMENT=1");


        //Table structure for table `campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaigns` (
  `campaign_id` int(5) NOT NULL AUTO_INCREMENT,
  `campaign_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `record_layout` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '2col.php',
  `logo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `campaign_type_id` varchar(50) CHARACTER SET utf8 NOT NULL,
  `client_id` tinyint(4) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `campaign_status` int(1) NOT NULL,
  `email_recipients` mediumtext CHARACTER SET utf8,
  `reassign_to` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `custom_panel_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `min_quote_days` int(11) DEFAULT NULL,
  `max_quote_days` int(11) DEFAULT NULL,
  `daily_data` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`campaign_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");


        //Table structure for table `campaigns_to_features`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaigns_to_features` (
  `campaign_id` int(3) NOT NULL,
  `feature_id` int(11) NOT NULL,
  KEY `campaign_id` (`campaign_id`,`feature_id`),
  KEY `feature_id` (`feature_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");


        //Table structure for table `campaign_features`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaign_features` (
  `feature_id` int(3) NOT NULL AUTO_INCREMENT,
  `feature_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `panel_path` varchar(50) CHARACTER SET utf8 NOT NULL,
  `permission_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`feature_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");


        //Table structure for table `campaign_managers`
        $this->db->query("		CREATE TABLE IF NOT EXISTS `campaign_managers` (
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");


        //Table structure for table `campaign_permissions`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaign_permissions` (
  `campaign_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `permission_state` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `campaign_id` (`campaign_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        //Table structure for table `campaign_types`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaign_types` (
  `campaign_type_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `campaign_type_desc` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`campaign_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3");

        //Table structure for table `campaign_xfers`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaign_xfers` (
  `campaign_id` int(3) NOT NULL,
  `xfer_campaign` int(3) NOT NULL,
  UNIQUE KEY `campxfer` (`campaign_id`,`xfer_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `clients`
        $this->db->query("CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `client_name` (`client_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `client_refs`
        $this->db->query("CREATE TABLE IF NOT EXISTS `client_refs` (
  `urn` int(11) NOT NULL AUTO_INCREMENT,
  `client_ref` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`urn`),
  UNIQUE KEY `client_ref2` (`urn`,`client_ref`),
  KEY `client_ref` (`client_ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `companies`
        $this->db->query("CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `description` varchar(290) CHARACTER SET utf8 DEFAULT NULL,
  `conumber` VARCHAR(11) NULL DEFAULT NULL,
  `turnover` int(11) DEFAULT NULL,
  `employees` int(11) DEFAULT NULL,
  `website` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` DATETIME NULL DEFAULT NULL,
  `status` VARCHAR(50) NULL,
  `date_of_creation` DATE NULL,
  PRIMARY KEY (`company_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `company_addresses`
        $this->db->query("CREATE TABLE IF NOT EXISTS `company_addresses` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `add1` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `add2` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `add3` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `county` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `country` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `postcode` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `primary` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  KEY `company_id` (`company_id`),
  KEY `postcode` (`postcode`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `company_subsectors`
        $this->db->query("CREATE TABLE IF NOT EXISTS `company_subsectors` (
  `company_id` int(11) NOT NULL,
  `subsector_id` int(11) NOT NULL,
  UNIQUE KEY `company_id_2` (`company_id`,`subsector_id`),
  KEY `company_id` (`company_id`),
  KEY `subsector_id` (`subsector_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `company_telephone`
        $this->db->query("CREATE TABLE IF NOT EXISTS `company_telephone` (
  `telephone_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `telephone_number` varchar(20) CHARACTER SET utf8 NOT NULL,
  `description` varchar(150) CHARACTER SET utf8 NOT NULL,
  `ctps` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`telephone_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `configuration`
        $this->db->query("CREATE TABLE IF NOT EXISTS `configuration` (
  `use_fullname` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `contacts`
        $this->db->query("
CREATE TABLE IF NOT EXISTS `contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `fullname` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `firstname` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `lastname` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `gender` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `position` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `email_optout` tinyint(1) DEFAULT NULL,
  `website` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `linkedin` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `facebook` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `notes` varchar(350) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` DATETIME NULL DEFAULT NULL,
  `primary` tinyint(1) DEFAULT NULL,
  `sort` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `contact_addresses`
        $this->db->query("CREATE TABLE IF NOT EXISTS `contact_addresses` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `add1` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `add2` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `add3` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `county` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `country` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `postcode` varchar(11) CHARACTER SET utf8 DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `primary` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  KEY `contact_id` (`contact_id`),
  KEY `postcode` (`postcode`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `contact_status`
        $this->db->query("CREATE TABLE IF NOT EXISTS `contact_status` (
  `contact_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_status_name` mediumtext CHARACTER SET utf8,
  `score_threshold` int(11) DEFAULT NULL,
  `colour` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`contact_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `contact_telephone`
        $this->db->query("CREATE TABLE IF NOT EXISTS `contact_telephone` (
  `telephone_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `telephone_number` varchar(20) CHARACTER SET utf8 NOT NULL,
  `description` varchar(150) CHARACTER SET utf8 NOT NULL,
  `tps` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`telephone_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");


        //Table structure for table `cross_transfers`
        $this->db->query("CREATE TABLE IF NOT EXISTS `cross_transfers` (
  `history_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  UNIQUE KEY `history_id` (`history_id`,`campaign_id`),
  KEY `history_id_2` (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        //Table structure for table `data_sources`
        $this->db->query("CREATE TABLE IF NOT EXISTS `data_sources` (
  `source_id` int(11) NOT NULL AUTO_INCREMENT,
  `source_name` varchar(150) CHARACTER SET utf8 NOT NULL,
  `cost_per_record` float DEFAULT NULL,
  PRIMARY KEY (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `default_hours`
        $this->db->query("CREATE TABLE IF NOT EXISTS `default_hours` (
  `default_hours_id` int(5) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL DEFAULT '0',
  `campaign_id` int(5) NOT NULL DEFAULT '0',
  `duration` int(20) NOT NULL DEFAULT '0' COMMENT 'in minutes',
  PRIMARY KEY (`default_hours_id`),
  KEY `FK_default_hours_users` (`user_id`),
  KEY `FK_default_hours_campaigns_` (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Default working time by agent for a particular campaign' AUTO_INCREMENT=1");

        //Table structure for table `default_time`
        $this->db->query("CREATE TABLE IF NOT EXISTS `default_time` (
  `default_time_id` int(5) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL DEFAULT '0',
  `start_time` time NOT NULL DEFAULT '00:00:00',
  `end_time` time NOT NULL DEFAULT '230:59:59',
  PRIMARY KEY (`default_time_id`),
  KEY `FK__users` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Default time defined for an agent' AUTO_INCREMENT=1");


        //Table structure for table `email_history`
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_history` (
  `email_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sent_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject` varchar(100) CHARACTER SET utf8 NOT NULL,
  `body` mediumtext CHARACTER SET utf8 NOT NULL,
  `send_from` varchar(255) CHARACTER SET utf8 NOT NULL,
  `send_to` varchar(255) CHARACTER SET utf8 NOT NULL,
  `cc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `bcc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `read_confirmed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 if the user read the email',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `read_confirmed_date` timestamp NULL DEFAULT NULL,
  `template_unsubscribe` TINYINT(1) NOT NULL DEFAULT '0',
  `pending` TINYINT DEFAULT 0 NOT NULL,
  `cron_code` INT NULL DEFAULT NULL,
  PRIMARY KEY (`email_id`),
  KEY `FK2_user_id` (`user_id`),
  KEY `FK3_record_urn` (`urn`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `email_history_attachments`
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_history_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(11) unsigned NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_email_id` (`email_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `email_templates`
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `template_subject` varchar(100) CHARACTER SET utf8 NOT NULL,
  `template_body` mediumtext CHARACTER SET utf8 NOT NULL,
  `template_from` varchar(255) CHARACTER SET utf8 NOT NULL,
  `template_to` varchar(255) CHARACTER SET utf8 NOT NULL,
  `template_cc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `template_bcc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `template_hostname` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `template_port` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `template_username` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `template_password` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `template_encryption` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `template_unsubscribe` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `email_template_attachments`
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_template_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `path` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `email_template_to_campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_template_to_campaigns` (
  `template_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  UNIQUE KEY `tempcamp` (`template_id`,`campaign_id`),
  KEY `template_id` (`template_id`),
  KEY `campanign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `email_triggers`
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_triggers` (
  `trigger_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`trigger_id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `email_triggers`
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_trigger_recipients` (
  `trigger_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` VARCHAR( 5 ) NULL DEFAULT NULL,
  UNIQUE KEY `trigger_id` (`trigger_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `favorites`
        $this->db->query("CREATE TABLE IF NOT EXISTS `favorites` (
  `urn` int(11) NOT NULL,
  `user_id` tinyint(3) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `freedata`
        $this->db->query("CREATE TABLE IF NOT EXISTS `freedata` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `coname` varchar(150) DEFAULT NULL,
  `local` int(1) NOT NULL,
  `phone` varchar(17) DEFAULT NULL,
  `mobile` varchar(17) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `add1` varchar(100) DEFAULT NULL,
  `add2` varchar(100) DEFAULT NULL,
  `add3` varchar(100) DEFAULT NULL,
  `postcode` varchar(15) DEFAULT NULL,
  `user_id` int(3) DEFAULT NULL,
  `sector_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`data_id`),
  UNIQUE KEY `coname` (`coname`,`phone`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        //Table structure for table `history`
        $this->db->query("CREATE TABLE IF NOT EXISTS `history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(3) DEFAULT NULL,
  `urn` int(11) NOT NULL,
  `loaded` datetime DEFAULT NULL,
  `contact` datetime NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 NOT NULL,
  `outcome_id` int(11) DEFAULT NULL,
  `comments` longtext CHARACTER SET utf8,
  `nextcall` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `group_id` int(11) NOT NULL DEFAULT '1',
  `contact_id` int(11) DEFAULT NULL,
  `progress_id` int(1) DEFAULT NULL,
  `last_survey` int(11) DEFAULT NULL,
  PRIMARY KEY (`history_id`),
  KEY `urn` (`urn`),
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`),
  KEY `repgroup_id` (`group_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `history_log`
        $this->db->query("CREATE TABLE IF NOT EXISTS `history_log` (
  `id` int(11) NOT NULL,
  `history_id` int(11) NOT NULL,
  `table_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `col_name` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `old_val` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `new_val` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `history_id` (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `hours`
        $this->db->query("CREATE TABLE IF NOT EXISTS `hours` (
  `hours_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL COMMENT 'The Agent',
  `campaign_id` int(5) NOT NULL,
  `duration` int(20) NOT NULL COMMENT 'in seconds',
  `time_logged` int(20) NOT NULL COMMENT 'in seconds',
  `date` datetime NOT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `updated_id` int(5) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`hours_id`),
  KEY `user_id` (`user_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Agent duration by campaign and day' AUTO_INCREMENT=1");

        //Table structure for table `hours_logged`
        $this->db->query("CREATE TABLE IF NOT EXISTS `hours_logged` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `start_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `importcsv`
        $this->db->query("CREATE TABLE IF NOT EXISTS `importcsv` (
  `subsector_name` varchar(255) DEFAULT NULL,
  `sector_name` varchar(255) DEFAULT NULL,
  `sector_id` varchar(255) DEFAULT NULL,
  `subsector_id` varchar(255) DEFAULT NULL,
  `urn` varchar(255) DEFAULT NULL,
  `campaign_name` varchar(255) DEFAULT NULL,
  `newurn` int(11) DEFAULT NULL,
  KEY `newurn` (`newurn`),
  KEY `urn` (`urn`),
  KEY `sector_id` (`sector_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        //Table structure for table `locations`
        $this->db->query("CREATE TABLE IF NOT EXISTS `locations` (
  `location_id` int(11) NOT NULL,
  `lat` float DEFAULT NULL,
  `lng` float DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  KEY `lat` (`lat`,`lng`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        //Table structure for table `migrations`
        $this->db->query("CREATE TABLE IF NOT EXISTS `migrations` (
  `version` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `outcomes`
        $this->db->query("CREATE TABLE IF NOT EXISTS `outcomes` (
  `outcome_id` int(11) NOT NULL AUTO_INCREMENT,
  `outcome` varchar(35) CHARACTER SET utf8 NOT NULL,
  `set_status` int(1) DEFAULT NULL,
  `set_progress` tinyint(1) DEFAULT NULL,
  `positive` int(1) DEFAULT NULL,
  `dm_contact` int(1) DEFAULT NULL,
  `sort` int(2) DEFAULT NULL,
  `enable_select` int(1) DEFAULT NULL,
  `force_comment` int(1) DEFAULT NULL,
  `force_nextcall` int(1) DEFAULT NULL,
  `delay_hours` tinyint(4) DEFAULT NULL,
  `no_history` tinyint(1) DEFAULT NULL,
  `disabled` tinyint(4) DEFAULT NULL,
  `keep_record` int(11) DEFAULT NULL,
  PRIMARY KEY (`outcome_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `outcomes_to_campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `outcomes_to_campaigns` (
  `outcome_id` int(3) NOT NULL,
  `campaign_id` int(3) NOT NULL,
  UNIQUE KEY `campaign_id` (`campaign_id`,`outcome_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `outcomes_to_roles`
        $this->db->query("CREATE TABLE IF NOT EXISTS `outcomes_to_roles` (
  `outcome_id` int(5) NOT NULL,
  `role_id` int(5) NOT NULL,
  UNIQUE KEY `role_id` (`role_id`,`outcome_id`),
  KEY `outcome_id` (`outcome_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `ownership`
        $this->db->query("CREATE TABLE IF NOT EXISTS `ownership` (
  `urn` int(12) NOT NULL,
  `user_id` int(4) NOT NULL,
  UNIQUE KEY `urn_user` (`urn`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `ownership_triggers`
        $this->db->query("CREATE TABLE IF NOT EXISTS `ownership_triggers` (
  `trigger_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  PRIMARY KEY (`trigger_id`),
  UNIQUE KEY `trigger_id2` (`campaign_id`,`outcome_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `ownership_trigger_users`
        $this->db->query("CREATE TABLE IF NOT EXISTS `ownership_trigger_users` (
  `trigger_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `trigger_id` (`trigger_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `park_codes`
        $this->db->query("CREATE TABLE IF NOT EXISTS `park_codes` (
  `parked_code` int(11) NOT NULL AUTO_INCREMENT,
  `park_reason` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`parked_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        $this->db->query("ALTER TABLE `park_codes` ADD UNIQUE (`park_reason`)");

        //Table structure for table `progress_description`
        $this->db->query("CREATE TABLE IF NOT EXISTS `progress_description` (
		  `progress_id` int(11) NOT NULL AUTO_INCREMENT,
		  `description` varchar(100) CHARACTER SET utf8 NOT NULL,
		  `progress_color` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
		  PRIMARY KEY (`progress_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");

        //Table structure for table `permissions`
        $this->db->query("CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `permission_group` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        $this->db->query("ALTER TABLE `permissions` ADD UNIQUE(`permission_name`)");

        //Table structure for table `progress_description`
        $this->db->query("CREATE TABLE IF NOT EXISTS `progress_description` (
  `progress_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) CHARACTER SET utf8 NOT NULL,
  `progress_color` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`progress_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `questions`
        $this->db->query("CREATE TABLE IF NOT EXISTS `questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `question_script` varchar(300) CHARACTER SET utf8 NOT NULL,
  `question_guide` varchar(300) CHARACTER SET utf8 NOT NULL,
  `other` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `question_cat_id` int(11) DEFAULT NULL,
  `sort` int(2) NOT NULL DEFAULT '5',
  `nps_question` tinyint(1) DEFAULT NULL,
  `multiple` tinyint(1) DEFAULT NULL,
  `survey_info_id` tinyint(3) DEFAULT NULL,
  `trigger_score` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`question_id`),
  KEY `survey_info_id` (`survey_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `questions_to_categories`
        $this->db->query("CREATE TABLE IF NOT EXISTS `questions_to_categories` (
  `question_cat_id` int(3) NOT NULL AUTO_INCREMENT,
  `question_cat_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`question_cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `question_options`
        $this->db->query("CREATE TABLE IF NOT EXISTS `question_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(150) CHARACTER SET utf8 NOT NULL,
  `question_id` int(11) NOT NULL,
  `trigger_email` tinyint(11) DEFAULT NULL,
  `sort` INT NULL DEFAULT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `records`
        $this->db->query("CREATE TABLE IF NOT EXISTS `records` (
  `urn` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(3) DEFAULT NULL,
  `outcome_id` int(3) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `nextcall` datetime DEFAULT NULL,
  `dials` int(2) DEFAULT '0',
  `record_status` tinyint(1) NOT NULL DEFAULT '1',
  `parked_code` int(11) DEFAULT NULL,
  `parked_date` TIMESTAMP NULL DEFAULT NULL,
  `progress_id` tinyint(1) DEFAULT NULL,
  `urgent` int(11) DEFAULT NULL,
  `date_added` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL,
  `reset_date` date DEFAULT NULL,
  `last_survey_id` int(11) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `urn_copied` INT NULL DEFAULT NULL,
  `company_copied` INT NULL DEFAULT NULL,
  `contact_copied` INT NULL DEFAULT NULL,
  PRIMARY KEY (`urn`),
  KEY `campaign_id` (`campaign_id`),
  KEY `outcome_id` (`outcome_id`),
  KEY `group_id` (`team_id`),
  KEY `progress_id` (`progress_id`),
  KEY `last_survey_id` (`last_survey_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `record_details`
        $this->db->query("CREATE TABLE IF NOT EXISTS `record_details` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `c1` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `c2` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `c3` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `c4` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `c5` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `d1` date DEFAULT NULL,
  `d2` date DEFAULT NULL,
  `d3` date DEFAULT NULL,
  `dt1` datetime DEFAULT NULL,
  `dt2` datetime DEFAULT NULL,
  `n1` int(11) DEFAULT NULL,
  `n2` int(11) DEFAULT NULL,
  `n3` int(11) DEFAULT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `record_details_fields`
        $this->db->query("CREATE TABLE IF NOT EXISTS `record_details_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `field` varchar(3) CHARACTER SET utf8 NOT NULL,
  `field_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `is_select` int(1) DEFAULT NULL,
  `sort` int(3) DEFAULT NULL,
  `is_visible` int(11) DEFAULT NULL,
  `is_renewal` int(11) DEFAULT NULL,
  `format` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `record_details_options`
        $this->db->query("CREATE TABLE IF NOT EXISTS `record_details_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `field` varchar(3) CHARACTER SET utf8 NOT NULL,
  `option` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`,`field`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `reminders`
        $this->db->query("CREATE TABLE IF NOT EXISTS `reminders` (
  `urn` int(11) NOT NULL,
  `ignore` int(1) NOT NULL DEFAULT '0',
  `snooze` int(1) NOT NULL,
  PRIMARY KEY (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `role_permissions`
        $this->db->query("CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  UNIQUE KEY `role_id` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `scripts`
        $this->db->query("CREATE TABLE IF NOT EXISTS `scripts` (
  `script_id` int(11) NOT NULL AUTO_INCREMENT,
  `expandable` tinyint(4) DEFAULT NULL,
  `script_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `script` mediumtext CHARACTER SET utf8 NOT NULL,
  `sort` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`script_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3");

        //Table structure for table `scripts_to_campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `scripts_to_campaigns` (
  `script_id` tinyint(4) NOT NULL,
  `campaign_id` tinyint(4) NOT NULL,
  UNIQUE KEY `script_id2` (`script_id`,`campaign_id`),
  KEY `script_id` (`script_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `sectors`
        $this->db->query("CREATE TABLE IF NOT EXISTS `sectors` (
  `sector_id` int(11) NOT NULL AUTO_INCREMENT,
  `sector_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`sector_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `status_list`
        $this->db->query("CREATE TABLE IF NOT EXISTS `status_list` (
  `record_status_id` int(1) NOT NULL,
  `status_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`record_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `sticky_notes`
        $this->db->query("CREATE TABLE IF NOT EXISTS `sticky_notes` (
  `urn` int(11) NOT NULL,
  `note` varchar(250) CHARACTER SET utf8 NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` tinyint(4) NOT NULL,
  PRIMARY KEY (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `subsectors`
        $this->db->query("CREATE TABLE IF NOT EXISTS `subsectors` (
  `subsector_id` int(11) NOT NULL AUTO_INCREMENT,
  `subsector_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `sector_id` int(11) NOT NULL,
  PRIMARY KEY (`subsector_id`),
  KEY `sector_id` (`sector_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `suppression`
        $this->db->query("CREATE TABLE IF NOT EXISTS `suppression` (
  `suppression_id` int(11) NOT NULL AUTO_INCREMENT,
  `telephone_number` varchar(20) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` TIMESTAMP NULL,
  `reason` TEXT NULL,
  PRIMARY KEY (`suppression_id`),
  KEY `telephone_number` (`telephone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        //Create suppression_by_campaign table
        $this->db->query("CREATE TABLE suppression_by_campaign
                      (
                          suppression_id INT NOT NULL,
                          campaign_id INT NOT NULL,
                          PRIMARY KEY (suppression_id, campaign_id)
                      );");

        //Table structure for table `surveys`
        $this->db->query("CREATE TABLE IF NOT EXISTS `surveys` (
  `survey_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `survey_updated` datetime DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL,
  `completed` int(1) NOT NULL DEFAULT '0',
  `contact_id` int(9) DEFAULT NULL,
  `user_id` int(5) NOT NULL,
  `survey_info_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`survey_id`),
  UNIQUE KEY `urn` (`urn`,`date_created`),
  UNIQUE KEY `urn_2` (`urn`,`completed_date`,`contact_id`),
  KEY `urn_3` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `surveys_to_campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `surveys_to_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_info_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `default` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `survey_info_id` (`survey_info_id`,`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `surveys_to_questions`
        $this->db->query("CREATE TABLE IF NOT EXISTS `surveys_to_questions` (
  `question_id` tinyint(1) NOT NULL,
  `survey_info_id` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `survey_answers`
        $this->db->query("CREATE TABLE IF NOT EXISTS `survey_answers` (
  `answer_id` int(5) NOT NULL AUTO_INCREMENT,
  `survey_id` int(5) DEFAULT NULL,
  `question_id` int(5) DEFAULT NULL,
  `answer` int(11) DEFAULT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `survey_id` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `survey_info`
        $this->db->query("CREATE TABLE IF NOT EXISTS `survey_info` (
  `survey_info_id` int(11) NOT NULL,
  `survey_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `survey_status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`survey_info_id`),
  UNIQUE KEY `survey_ref` (`survey_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `teams`
        $this->db->query("CREATE TABLE IF NOT EXISTS `teams` (
  `team_id` int(11) NOT NULL AUTO_INCREMENT,
  `team_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`team_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `team_managers`
        $this->db->query("CREATE TABLE IF NOT EXISTS `team_managers` (
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `team_id_2` (`team_id`,`user_id`),
  KEY `team_id` (`team_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        //Table structure for table `time`
        $this->db->query("CREATE TABLE IF NOT EXISTS `time` (
  `time_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL COMMENT 'The Agent',
  `start_time` time NOT NULL DEFAULT '00:00:00',
  `end_time` time NOT NULL DEFAULT '230:59:59',
  `date` datetime NOT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `updated_id` int(5) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`time_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Agent time by campaign and day' AUTO_INCREMENT=1");

        //Table structure for table `time_exception`
        $this->db->query("CREATE TABLE IF NOT EXISTS `time_exception` (
  `exception_id` int(11) NOT NULL AUTO_INCREMENT,
  `time_id` int(5) NOT NULL,
  `exception_type_id` int(5) NOT NULL,
  `duration` int(20) NOT NULL DEFAULT '0' COMMENT 'in minutes',
  PRIMARY KEY (`exception_id`),
  KEY `time_id` (`time_id`),
  KEY `exception_type_id` (`exception_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Exception time' AUTO_INCREMENT=1");

        //Table structure for table `time_exception_type`
        $this->db->query("CREATE TABLE IF NOT EXISTS `time_exception_type` (
  `exception_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `exception_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `paid` tinyint(1) NOT NULL,
  PRIMARY KEY (`exception_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Exception types' AUTO_INCREMENT=1");

        //Table structure for table `uk_postcodes`
        $this->db->query("CREATE TABLE IF NOT EXISTS `uk_postcodes` (
  `postcode_id` int(11) NOT NULL AUTO_INCREMENT,
  `postcode` varchar(8) NOT NULL,
  `lat` float(10,8) NOT NULL,
  `lng` float(10,8) NOT NULL,
  PRIMARY KEY (`postcode_id`),
  UNIQUE KEY `postcode` (`postcode`),
  UNIQUE KEY `postcode_2` (`postcode`),
  KEY `lat` (`lat`),
  KEY `lng` (`lng`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");


        //Table structure for table `users`
        $this->db->query("CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(2) NOT NULL AUTO_INCREMENT,
  `role_id` int(2) NOT NULL,
  `group_id` int(2) NOT NULL DEFAULT '1',
  `team_id` int(11) DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `user_status` tinyint(1) DEFAULT NULL,
  `login_mode` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `user_telephone` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `user_email` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `phone_un` VARCHAR(50) NULL DEFAULT NULL,
  `phone_pw` VARCHAR(50) NULL DEFAULT NULL,
  `ext` int(3) DEFAULT NULL,
  `token` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `pass_changed` datetime DEFAULT NULL,
  `failed_logins` tinyint(4) NOT NULL,
  `last_failed_login` datetime DEFAULT NULL,
  `reload_session` tinyint(1) NOT NULL DEFAULT '0',
  `attendee` tinyint(1) NOT NULL DEFAULT '0',
  `reset_pass_token` TEXT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `group_id` (`role_id`),
  KEY `repgroup_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        $this->db->query("ALTER TABLE `users` ADD UNIQUE (`phone_un`)");

        //Table structure for table `users_to_campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `users_to_campaigns` (
  `user_id` int(5) NOT NULL,
  `campaign_id` int(5) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`campaign_id`),
  KEY `user_id_2` (`user_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `user_groups`
        $this->db->query("CREATE TABLE IF NOT EXISTS `user_groups` (
  `group_id` int(3) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `theme_folder` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `user_roles`
        $this->db->query("CREATE TABLE IF NOT EXISTS `user_roles` (
  `role_id` int(3) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Table structure for table `webforms`
        $this->db->query("CREATE TABLE IF NOT EXISTS `webforms` (
  `webform_id` int(11) NOT NULL AUTO_INCREMENT,
  `webform_path` varchar(100) DEFAULT NULL,
  `webform_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`webform_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        //Table structure for table `webforms_to_campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `webforms_to_campaigns` (
  `webform_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  UNIQUE KEY `webform_id_2` (`webform_id`,`campaign_id`),
  KEY `webform_id` (`webform_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        //Table structure for table `webforms_to_campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `webform_answers` (
  `webform_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `updated_on` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` INT NULL DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        //Add export_forms table
        $this->db->query("CREATE TABLE export_forms
                      (
                          export_forms_id INT NOT NULL AUTO_INCREMENT,
                          name VARCHAR(50) NOT NULL,
                          description VARCHAR(255) NOT NULL,
                          header TEXT NOT NULL,
                          query TEXT NOT NULL,
                          order_by VARCHAR(25) NULL,
                          group_by VARCHAR(25) NULL,
                          date_filter VARCHAR(25),
                          campaign_filter VARCHAR(25),
                      PRIMARY KEY (export_forms_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Add export_to_users table
        $this->db->query("CREATE TABLE export_to_users
                      (
                          export_forms_id INT NOT NULL,
                          user_id INT NOT NULL,
                      PRIMARY KEY (export_forms_id, user_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `files` (
        `file_id` int(11) NOT NULL AUTO_INCREMENT,
        `filename` varchar(100) NOT NULL,
        `filesize` int(11) DEFAULT NULL,
        `folder_id` int(11) NOT NULL,
        `user_id` int(11) DEFAULT NULL,
        `email_sent` VARCHAR(50) NULL DEFAULT NULL,
        `doc_hash` VARCHAR(50) NULL DEFAULT NULL,
        `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `deleted_on` timestamp NULL DEFAULT NULL,
        `deleted_by` int(11) DEFAULT NULL,
        PRIMARY KEY (`file_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        $this->db->query("CREATE TABLE IF NOT EXISTS `folders` (
        `folder_id` int(11) NOT NULL AUTO_INCREMENT,
        `folder_name` varchar(100) NOT NULL,
        `accepted_filetypes` varchar(200) NOT NULL,
        PRIMARY KEY (`folder_id`),
        UNIQUE KEY `folder_name` (`folder_name`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        $this->db->query("CREATE TABLE IF NOT EXISTS `folder_permissions` (
        `user_id` int(11) NOT NULL,
        `folder_id` int(11) NOT NULL,
        `read` tinyint(4) DEFAULT '1',
        `write` tinyint(4) DEFAULT NULL,
        UNIQUE KEY `user_id` (`user_id`,`folder_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        //Adding call_log_file table
        $this->db->query("CREATE TABLE IF NOT EXISTS `call_log_file` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `file_date` DATE NOT NULL,
                `unit` INT(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");


        //Adding call_log table
        $this->db->query("CREATE TABLE IF NOT EXISTS `call_log` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `file_id` INT(11) NULL DEFAULT NULL,
                `call_date` DATETIME NOT NULL,
                `duration` TIME NOT NULL,
                `ring_time` INT(11) NOT NULL,
                `call_id` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
                `call_from` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `name_from` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `ref_from` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `call_to` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `call_to_ext` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `name_to` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `ref_to` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
                `inbound` TINYINT(1) NOT NULL,
                PRIMARY KEY (`id`),
                INDEX `IDX_D663C42E93CB796C` (`file_id`),
                CONSTRAINT `FK_D663C42E93CB796C` FOREIGN KEY (`file_id`) REFERENCES `call_log_file` (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        //Adding search actions permissions and edit export permissions
        $this->db->query("CREATE TABLE `function_triggers`
                        (
                           `trigger_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                           `campaign_id` INT NOT NULL,
                           `outcome_id` INT NOT NULL,
                           `path` VARCHAR(255) NOT NULL,
                           CONSTRAINT `fk_FT_Campaign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`),
                           CONSTRAINT `fk_FT_Outcome` FOREIGN KEY (`outcome_id`) REFERENCES `outcomes` (`outcome_id`)
                        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        //Create tps table if it does not exist
        $this->db->query("CREATE TABLE IF NOT EXISTS `tps` (
              `telephone` varchar(20) NOT NULL,
              `date_updated` TIMESTAMP NOT NULL,
              `tps` TINYINT DEFAULT 0 NOT NULL,
              `ctps` TINYINT DEFAULT 0 NOT NULL,
              PRIMARY KEY (`telephone`),
              UNIQUE KEY `telephone` (`telephone`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ");

        //Set the current date to the numbers that already exists in the tps table
        $this->db->query("UPDATE `tps` SET `date_updated`=NOW()");

        //Add the appointment rule reasons table
        $this->db->query("CREATE TABLE IF NOT EXISTS `appointment_rule_reasons` (
            `reason_id` INT NOT NULL AUTO_INCREMENT,
            `reason` VARCHAR(255) NULL DEFAULT '0',
            PRIMARY KEY (`reason_id`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Add the appointment rules table
        $this->db->query("CREATE TABLE IF NOT EXISTS `appointment_rules` (
            `appointment_rules_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL DEFAULT '0',
            `reason_id` INT NOT NULL DEFAULT '0',
            `other_reason` VARCHAR(255) NULL DEFAULT '0',
            `block_day` DATE NULL,
            PRIMARY KEY (`appointment_rules_id`),
            CONSTRAINT `appointment_rules_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `appointment_rules_reason_id` FOREIGN KEY (`reason_id`) REFERENCES `appointment_rule_reasons` (`reason_id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        $this->db->query("CREATE TABLE IF NOT EXISTS `email_unsubscribe` (
        `unsubscribe_id` int(11) NOT NULL AUTO_INCREMENT,
        `email_address` varchar(100) NOT NULL,
        `client_id` int(11) NOT NULL,
        `urn` int(11) NOT NULL,
        `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `ip_address` varchar(20) NOT NULL,
        PRIMARY KEY (`unsubscribe_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        $this->db->query("alter table email_unsubscribe add unique(email_address,client_id)");

        $this->db->query("CREATE TABLE IF NOT EXISTS `audit` (
          `audit_id` int(11) NOT NULL AUTO_INCREMENT,
          `urn` int(11) DEFAULT NULL,
          `table_name` varchar(100) DEFAULT NULL,
          `reference` int(11) DEFAULT NULL,
          `change_type` varchar(20) DEFAULT NULL,
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `user_id` int(11) DEFAULT NULL,
          PRIMARY KEY (`audit_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");


        $this->db->query("CREATE TABLE IF NOT EXISTS `audit_values` (
          `audit_id` int(11) NOT NULL,
          `column_name` varchar(100) DEFAULT NULL,
          `oldval` varchar(255) DEFAULT NULL,
          `newval` varchar(255) DEFAULT NULL,
          UNIQUE KEY `audit_id_2` (`audit_id`,`column_name`),
          KEY `audit_id` (`audit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        $this->db->query("ALTER TABLE `audit_values` ADD CONSTRAINT `audit_values_ibfk_1` FOREIGN KEY (`audit_id`) REFERENCES `audit` (`audit_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `role_permissions`
        $this->db->query("ALTER TABLE `role_permissions` ADD FOREIGN KEY (`role_id`) REFERENCES `permissions`(`permission_id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        //Constraints for table `answers_to_options`
        $this->db->query("ALTER TABLE `answers_to_options`
  ADD CONSTRAINT `answers_to_options_ibfk_1` FOREIGN KEY (`answer_id`) REFERENCES `survey_answers` (`answer_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `answer_notes`
        $this->db->query("ALTER TABLE `answer_notes`
  		ADD CONSTRAINT `answer_notes_ibfk_1` FOREIGN KEY (`answer_id`) REFERENCES `survey_answers` (`answer_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `appointment_attendees`
        $this->db->query("ALTER TABLE `appointment_attendees`
  		ADD CONSTRAINT `appointment_attendees_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  		ADD CONSTRAINT `appointment_attendees_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`)");

        //Constraints for table `campaigns_to_features`
        $this->db->query("ALTER TABLE `campaigns_to_features`
  		ADD CONSTRAINT `campaigns_to_features_ibfk_2` FOREIGN KEY (`feature_id`) REFERENCES `campaign_features` (`feature_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  		ADD CONSTRAINT `campaigns_to_features_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `client_refs`
        $this->db->query("ALTER TABLE `client_refs`
  		ADD CONSTRAINT `client_refs_ibfk_1` FOREIGN KEY (`urn`) REFERENCES `records` (`urn`)");

        //Constraints for table `companies`
        $this->db->query("ALTER TABLE `companies`
  		ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`urn`) REFERENCES `records` (`urn`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `company_addresses`
        $this->db->query("ALTER TABLE `company_addresses`
		ADD CONSTRAINT `company_addresses_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `company_telephone`
        $this->db->query("ALTER TABLE `company_telephone`
  		ADD CONSTRAINT `company_telephone_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `contacts`
        $this->db->query("ALTER TABLE `contacts`
  		ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`urn`) REFERENCES `records` (`urn`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `email_history`
        $this->db->query("ALTER TABLE `email_history`
  		ADD CONSTRAINT `FK1_template_id` FOREIGN KEY (`template_id`) REFERENCES `email_templates` (`template_id`),
  		ADD CONSTRAINT `FK2_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  		ADD CONSTRAINT `FK3_record_urn` FOREIGN KEY (`urn`) REFERENCES `records` (`urn`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `email_history_attachments`
        $this->db->query("ALTER TABLE `email_history_attachments`
  		ADD CONSTRAINT `FK_email_id` FOREIGN KEY (`email_id`) REFERENCES `email_history` (`email_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `email_template_attachments`
        $this->db->query("ALTER TABLE `email_template_attachments`
  		ADD CONSTRAINT `FK_template_attachment` FOREIGN KEY (`template_id`) REFERENCES `email_templates` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `email_template_to_campaigns`
        $this->db->query("ALTER TABLE `email_template_to_campaigns`
  		ADD CONSTRAINT `FK_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  		ADD CONSTRAINT `FK_template` FOREIGN KEY (`template_id`) REFERENCES `email_templates` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `history`
        $this->db->query("ALTER TABLE `history`
  		ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`urn`) REFERENCES `records` (`urn`) ON DELETE CASCADE ON UPDATE CASCADE,
  		ADD CONSTRAINT `history_ibfk_2` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`)");

        //Constraints for table `history_log`
        $this->db->query("ALTER TABLE `history_log`
  		ADD CONSTRAINT `history_log_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`history_id`)");

        //Constraints for table `records`
        $this->db->query("ALTER TABLE `records`
  		ADD CONSTRAINT `records_ibfk_4` FOREIGN KEY (`last_survey_id`) REFERENCES `surveys` (`survey_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  		ADD CONSTRAINT `records_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`),
  		ADD CONSTRAINT `records_ibfk_2` FOREIGN KEY (`outcome_id`) REFERENCES `outcomes` (`outcome_id`),
  		ADD CONSTRAINT `records_ibfk_3` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`)");

        //Constraints for table `record_details`
        $this->db->query("ALTER TABLE `record_details`
		ADD CONSTRAINT `record_details_ibfk_1` FOREIGN KEY (`urn`) REFERENCES `records` (`urn`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `surveys`
        $this->db->query("ALTER TABLE `surveys`
  		ADD CONSTRAINT `surveys_ibfk_1` FOREIGN KEY (`urn`) REFERENCES `records` (`urn`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `survey_answers`
        $this->db->query("ALTER TABLE `survey_answers`
  		ADD CONSTRAINT `survey_answers_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`survey_id`) ON DELETE CASCADE ON UPDATE CASCADE");

        //Constraints for table `users_to_campaigns`
        $this->db->query("ALTER TABLE `users_to_campaigns`
  		ADD CONSTRAINT `users_to_campaigns_ibfk_2` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`),
  		ADD CONSTRAINT `users_to_campaigns_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)");


        $this->db->query("ALTER TABLE `contact_telephone` ADD INDEX `telephone_number` (`telephone_number`);");
        $this->db->query("ALTER TABLE `company_telephone` ADD INDEX `telephone_number` (`telephone_number`);");

        //this trigger makes the parked date update when ever a park code changes on a record
        $this->db->query("CREATE TRIGGER `records_before_update` BEFORE UPDATE ON `records`\r\n
      FOR EACH ROW BEGIN \r\n
      if new.parked_code or new.parked_code is null then\r\n
        SET NEW.parked_date = CURRENT_TIMESTAMP;\r\n
       end if;\r\n
		END");


        //Dump the init data
        $this->Database_model->init_data();
    }

    public function down()
    {
        //cannot roll back initial install
    }
}

?>