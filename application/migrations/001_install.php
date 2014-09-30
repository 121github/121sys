<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_install extends CI_Migration
{
    public function up()
    {
        if (!$this->db->query("CREATE DATABASE IF NOT EXISTS `121sys` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci")) {
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
        // //Table structure for table `answers_to_options`
        $this->db->query("CREATE TABLE IF NOT EXISTS `answers_to_options` (
  `answer_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  UNIQUE KEY `answer_id2` (`answer_id`,`option_id`),
  KEY `answer_id` (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
        ////Table structure for table `answer_notes`
        $this->db->query("CREATE TABLE IF NOT EXISTS `answer_notes` (
  `answer_id` int(11) NOT NULL,
  `notes` mediumtext CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        ////Table structure for table `appointments`
        $this->db->query("CREATE TABLE IF NOT EXISTS `appointments` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `text` mediumtext CHARACTER SET utf8 NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `urn` int(11) NOT NULL,
  `postcode` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
        ////Table structure for table `appointment_attendees`
        $this->db->query("CREATE TABLE IF NOT EXISTS `appointment_attendees` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `appointment_id_2` (`appointment_id`,`user_id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `user_id` (`user_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        ////Table structure for table `campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaigns` (
  `campaign_id` int(5) NOT NULL AUTO_INCREMENT,
  `campaign_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `campaign_type_id` varchar(50) CHARACTER SET utf8 NOT NULL,
  `client_id` tinyint(4) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `campaign_status` int(1) NOT NULL,
  `email_recipients` mediumtext CHARACTER SET utf8,
  `reassign_to` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `custom_panel_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`campaign_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

//create sample campaign
$this->db->query("INSERT INTO `campaigns` (`campaign_id`, `campaign_name`, `campaign_type_id`, `client_id`, `start_date`, `end_date`, `campaign_status`, `email_recipients`, `reassign_to`, `custom_panel_name`) VALUES
(4, 'Sample B2C Campaign', '1', 5, '2014-09-30', NULL, 1, NULL, NULL, '')");


        ////Table structure for table `campaigns_to_features`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaigns_to_features` (
  `campaign_id` int(3) NOT NULL,
  `feature_id` int(11) NOT NULL,
  KEY `campaign_id` (`campaign_id`,`feature_id`),
  KEY `feature_id` (`feature_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

//add sample campaign features
$this->db->query("INSERT INTO `campaigns_to_features` (`campaign_id`, `feature_id`) VALUES
(4, 1),
(4, 3),
(4, 4),
(4, 5),
(4, 7)");
        ////Table structure for table `campaign_features`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaign_features` (
  `feature_id` int(3) NOT NULL AUTO_INCREMENT,
  `feature_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `panel_path` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`feature_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13");
        // //Dumpingdata for table `campaign_features`
        $this->db->query("INSERT INTO `campaign_features` (`feature_id`, `feature_name`, `panel_path`) VALUES
(1, 'Contacts', 'contacts.php'),
(2, 'Company', 'company.php'),
(3, 'Update Record', 'record_update.php'),
(4, 'Sticky Note', 'sticky.php'),
(5, 'Ownership Changer', 'ownership.php'),
(6, 'Scripts', 'scripts.php'),
(7, 'History', 'history.php'),
(8, 'Custom Info', 'custom_info.php'),
(9, 'Emails', 'emails.php'),
(10, 'Appointment Setting', 'appointments.php'),
(11, 'Surveys', 'survey.php'),
(12, 'Recordings', 'recordings.php')");
        //Table structure for table `campaign_types`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaign_types` (
  `campaign_type_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `campaign_type_desc` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`campaign_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ");

//dumping into campaign types table
$this->db->query("INSERT INTO `campaign_types` (`campaign_type_id`, `campaign_type_desc`) VALUES
(1, 'B2C'),
(2, 'B2B')");

        //Table structure for table `campaign_xfers`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaign_xfers` (
  `campaign_id` int(3) NOT NULL,
  `xfer_campaign` int(3) NOT NULL,
  UNIQUE `campxfer` (`campaign_id`,`xfer_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `clients`
        $this->db->query("CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `client_name` (`client_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");

//dumping data sample into clients
 $this->db->query("INSERT INTO `clients` (`client_id`, `client_name`) VALUES
(1, '121'),
(5, 'Sample Client')");

        //Table structure for table `client_refs`
        $this->db->query("CREATE TABLE IF NOT EXISTS `client_refs` (
  `urn` int(11) NOT NULL AUTO_INCREMENT,
  `client_ref` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`urn`),
  UNIQUE KEY `client_ref2` (`urn`,`client_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `companies`
        $this->db->query("CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `description` varchar(290) CHARACTER SET utf8 DEFAULT NULL,
  `company_number` int(11) DEFAULT NULL,
  `turnover` int(11) DEFAULT NULL,
  `employees` int(11) DEFAULT NULL,
  `website` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
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
  `latitude` int(11) DEFAULT NULL,
  `longitude` int(11) DEFAULT NULL,
  `primary` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `company_subsectors`
        $this->db->query("CREATE TABLE IF NOT EXISTS `company_subsectors` (
  `company_id` int(11) NOT NULL,
  `subsector_id` int(11) NOT NULL,
  UNIQUE KEY `company_id_2` (`company_id`,`subsector_id`),
  KEY `company_id` (`company_id`),
  KEY `subsector_id` (`subsector_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
        //Table structure for table `company_telephone`
        $this->db->query("CREATE TABLE IF NOT EXISTS `company_telephone` (
  `telephone_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `telephone_number` varchar(20) CHARACTER SET utf8 NOT NULL,
  `description` varchar(150) CHARACTER SET utf8 NOT NULL,
  `ctps` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`telephone_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `configuration`
        $this->db->query("CREATE TABLE IF NOT EXISTS `configuration` (
  `use_fullname` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        //Dumpingdata for table `configuration`
        $this->db->query("INSERT INTO `configuration` (`use_fullname`) VALUES
(1)");
        //Table structure for table `contacts`
        $this->db->query("CREATE TABLE IF NOT EXISTS `contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `urn` int(11) NOT NULL,
  `fullname` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `firstname` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `lastname` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `position` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `email_optout` tinyint(1) DEFAULT NULL,
  `website` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `linkedin` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `facebook` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `notes` varchar(350) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `date_created` timestamp NULL DEFAULT NULL,
  `date_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sort` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");


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
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `primary` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `contact_status`
        $this->db->query("CREATE TABLE IF NOT EXISTS `contact_status` (
  `contact_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_status_name` mediumtext CHARACTER SET utf8,
  `score_threshold` int(11) DEFAULT NULL,
  `colour` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`contact_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ");
        //Dumpingdata for table `contact_status`
        $this->db->query("INSERT INTO `contact_status` (`contact_status_id`, `contact_status_name`, `score_threshold`, `colour`) VALUES
(1, 'Detractor', 6, '#FF0000'),
(2, 'Passive', 7, '#FF9900'),
(3, 'Promoter', 8, '#00FF00')");
        //Table structure for table `contact_telephone`
        $this->db->query("CREATE TABLE IF NOT EXISTS `contact_telephone` (
  `telephone_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `telephone_number` varchar(20) CHARACTER SET utf8 NOT NULL,
  `description` varchar(150) CHARACTER SET utf8 NOT NULL,
  `tps` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`telephone_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `data_sources`
        $this->db->query("CREATE TABLE IF NOT EXISTS `data_sources` (
  `source_id` int(11) NOT NULL AUTO_INCREMENT,
  `source_name` varchar(150) CHARACTER SET utf8 NOT NULL,
  `cost_per_record` float DEFAULT NULL,
  PRIMARY KEY (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");

$this->db->query("INSERT INTO `data_sources` (`source_id`, `source_name`, `cost_per_record`) VALUES
(1, 'Dummy Data', NULL)");

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
  PRIMARY KEY (`email_id`),
  KEY `FK2_user_id` (`user_id`),
  KEY `FK3_record_urn` (`urn`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `email_history_attachments`
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_history_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(11) unsigned NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_email_id` (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
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
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `email_template_attachments`
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_template_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `path` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `email_template_to_campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_template_to_campaigns` (
  `template_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  UNIQUE KEY `tempcamp` (`template_id`,`campaign_id`),
  KEY `template_id` (`template_id`),
  KEY `campanign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `favorites`
        $this->db->query("CREATE TABLE IF NOT EXISTS `favorites` (
  `urn` int(11) NOT NULL,
  `user_id` tinyint(3) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `history`
        $this->db->query("CREATE TABLE IF NOT EXISTS `history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(3) DEFAULT NULL,
  `urn` int(12) NOT NULL,
  `loaded` datetime DEFAULT NULL,
  `contact` datetime NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 NOT NULL,
  `outcome_id` int(3) DEFAULT NULL,
  `comments` longtext CHARACTER SET utf8,
  `nextcall` datetime DEFAULT NULL,
  `user_id` int(4) DEFAULT NULL,
  `role_id` int(2) DEFAULT NULL,
  `group_id` int(3) NOT NULL DEFAULT '1',
  `contact_id` int(5) DEFAULT NULL,
  `progress_id` int(1) DEFAULT NULL,
  `last_survey` int(11) DEFAULT NULL,
  PRIMARY KEY (`history_id`),
  KEY `urn` (`urn`),
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`),
  KEY `repgroup_id` (`group_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
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
        //Table structure for table `import_temp`
        $this->db->query("CREATE TABLE IF NOT EXISTS `import_temp` (
  `urn` int(11) NOT NULL AUTO_INCREMENT,
  `Segment` varchar(12) CHARACTER SET utf8 DEFAULT NULL,
  `GP Ref` int(7) DEFAULT NULL,
  `Customer Name` varchar(49) CHARACTER SET utf8 DEFAULT NULL,
  `First line of address` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `Postcode` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `Home Tel` varchar(14) CHARACTER SET utf8 DEFAULT NULL,
  `Mobile Tel` varchar(17) CHARACTER SET utf8 DEFAULT NULL,
  `Partner Mobile Tel` varchar(14) CHARACTER SET utf8 DEFAULT NULL,
  `Initial Advisor` varchar(18) CHARACTER SET utf8 DEFAULT NULL,
  `PFM` varchar(19) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`urn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `mail_templates`
        $this->db->query("CREATE TABLE IF NOT EXISTS `mail_templates` (
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
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `migrations`
        $this->db->query("CREATE TABLE IF NOT EXISTS `migrations` (
  `version` int(3) NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        //Table structure for table `outcomes`
        $this->db->query("CREATE TABLE IF NOT EXISTS `outcomes` (
  `outcome_id` int(11) NOT NULL AUTO_INCREMENT,
  `outcome` varchar(35) CHARACTER SET utf8 NOT NULL,
  `set_status` int(1) DEFAULT NULL,
  `positive` int(1) DEFAULT NULL,
  `dm_contact` int(1) DEFAULT NULL,
  `sort` int(2) DEFAULT NULL,
  `enable_select` int(1) DEFAULT NULL,
  `force_comment` int(1) DEFAULT NULL,
  `delay_hours` tinyint(4) DEFAULT NULL,
  `no_history` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`outcome_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=72 ");
        //Dumpingdata for table `outcomes`
        $this->db->query("INSERT INTO `outcomes` (`outcome_id`, `outcome`, `set_status`, `positive`, `dm_contact`, `sort`, `enable_select`, `force_comment`, `delay_hours`, `no_history`) VALUES
(1, 'Call Back', NULL, NULL, NULL, 4, 1, NULL, NULL, NULL),
(2, 'Call Back DM', NULL, NULL, 1, 1, 1, NULL, NULL, NULL),
(3, 'Answer Machine', NULL, NULL, NULL, 9, 1, NULL, 4, NULL),
(4, 'Dead Line', 3, NULL, NULL, 9, 1, NULL, NULL, NULL),
(5, 'Engaged', NULL, NULL, NULL, 9, 1, NULL, 4, NULL),
(7, 'No Answer', NULL, NULL, NULL, 9, 1, NULL, 4, NULL),
(12, 'Not Interested', 3, NULL, 1, 9, 1, NULL, NULL, NULL),
(13, 'Not Eligible', 3, NULL, NULL, 9, 1, NULL, NULL, NULL),
(17, 'Unavailable', NULL, NULL, NULL, 9, 1, NULL, 4, NULL),
(30, 'Deceased', 3, NULL, NULL, 9, 1, NULL, NULL, NULL),
(32, 'Moved', 3, NULL, NULL, 9, 1, NULL, NULL, NULL),
(33, 'Slammer', 3, NULL, NULL, 9, 1, NULL, 4, NULL),
(60, 'Survey Complete', 4, 1, 1, 1, 1, NULL, NULL, NULL),
(63, 'Wrong Number', 3, NULL, NULL, 9, 1, NULL, NULL, NULL),
(64, 'Duplicate', 3, NULL, NULL, 0, 1, NULL, NULL, NULL),
(65, 'Fax Machine', 3, NULL, NULL, 0, 1, NULL, NULL, NULL),
(66, 'Survey Refused', 3, NULL, NULL, 9, 1, NULL, NULL, NULL),
(67, 'Adding additional notes', NULL, NULL, NULL, 10, 1, 1, NULL, NULL),
(68, 'Changing next action date', NULL, NULL, NULL, 2, 1, NULL, NULL, 1),
(69, 'No Number', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 'Transfer', 4, 1, 1, 1, 1, NULL, NULL, NULL),
(71, 'Cross Transfer', 4, 1, 1, 1, 1, NULL, NULL, NULL)");
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `park_codes`
        $this->db->query("CREATE TABLE IF NOT EXISTS `park_codes` (
  `parked_code` int(11) NOT NULL AUTO_INCREMENT,
  `park_reason` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`parked_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ");
        //Dumpingdata for table `park_codes`
        $this->db->query("INSERT INTO `park_codes` (`parked_code`, `park_reason`) VALUES
(1, 'Rationing'),
(2, 'Not calling')");
        //Table structure for table `progress_description`
        $this->db->query("CREATE TABLE IF NOT EXISTS `progress_description` (
  `progress_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) CHARACTER SET utf8 NOT NULL,
  `progress_color` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`progress_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ");
        //Dumpingdata for table `progress_description`
        $this->db->query("INSERT INTO `progress_description` (`progress_id`, `description`, `progress_color`) VALUES
(1, 'Pending', 'red'),
(2, 'In Progress', 'orange'),
(3, 'Complete', 'green')");
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
  PRIMARY KEY (`question_id`),
  KEY `survey_info_id` (`survey_info_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `questions_to_categories`
        $this->db->query("CREATE TABLE IF NOT EXISTS `questions_to_categories` (
  `question_cat_id` int(3) NOT NULL AUTO_INCREMENT,
  `question_cat_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`question_cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `question_options`
        $this->db->query("CREATE TABLE IF NOT EXISTS `question_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(150) CHARACTER SET utf8 NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `records`
        $this->db->query("CREATE TABLE IF NOT EXISTS `records` (
  `urn` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(3) DEFAULT NULL,
  `outcome_id` int(3) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `nextcall` datetime DEFAULT NULL,
  `dials` int(2) DEFAULT NULL,
  `record_status` tinyint(1) NOT NULL DEFAULT '1',
  `parked_code` int(11) DEFAULT NULL,
  `progress_id` tinyint(1) DEFAULT NULL,
  `urgent` int(11) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `reset_date` date DEFAULT NULL,
  `last_survey_id` int(11) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`urn`),
  KEY `campaign_id` (`campaign_id`),
  KEY `outcome_id` (`outcome_id`),
  KEY `group_id` (`team_id`),
  KEY `progress_id` (`progress_id`),
  KEY `last_survey_id` (`last_survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `record_details_fields`
        $this->db->query("CREATE TABLE IF NOT EXISTS `record_details_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `field` varchar(3) CHARACTER SET utf8 NOT NULL,
  `field_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `is_select` int(1) DEFAULT NULL,
  `sort` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `record_details_options`
        $this->db->query("CREATE TABLE IF NOT EXISTS `record_details_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `field` varchar(3) CHARACTER SET utf8 NOT NULL,
  `option` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`,`field`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `reminders`
        $this->db->query("CREATE TABLE IF NOT EXISTS `reminders` (
  `urn` int(11) NOT NULL,
  `ignore` int(1) NOT NULL DEFAULT '0',
  `snooze` int(1) NOT NULL,
  PRIMARY KEY (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        //Table structure for table `scripts`
        $this->db->query("CREATE TABLE IF NOT EXISTS `scripts` (
  `script_id` int(11) NOT NULL AUTO_INCREMENT,
  `expandable` tinyint(4) DEFAULT NULL,
  `script_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `script` mediumtext CHARACTER SET utf8 NOT NULL,
  `sort` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`script_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `scripts_to_campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `scripts_to_campaigns` (
  `script_id` tinyint(4) NOT NULL,
  `campaign_id` tinyint(4) NOT NULL,
  UNIQUE KEY `script_id2` (`script_id`,`campaign_id`),
  KEY `script_id` (`script_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `sectors`
        $this->db->query("CREATE TABLE IF NOT EXISTS `sectors` (
  `sector_id` int(11) NOT NULL AUTO_INCREMENT,
  `sector_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`sector_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=121 ");
        //Dumpingdata for table `sectors`
        $this->db->query("INSERT INTO `sectors` (`sector_id`, `sector_name`) VALUES
(39, 'Other'),
(109, 'Basic Materials'),
(111, 'Consumer Goods'),
(112, 'Financial'),
(113, 'Healthcare'),
(114, 'Industrial Goods'),
(115, 'Services'),
(116, 'Technology'),
(117, 'Utilities'),
(118, 'Sports and Fitness'),
(119, 'Insurance'),
(120, 'Transport')");
        //Table structure for table `status_list`
        $this->db->query("CREATE TABLE IF NOT EXISTS `status_list` (
  `record_status_id` int(1) NOT NULL,
  `status_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`record_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        //Dumpingdata for table `status_list`
        $this->db->query("INSERT INTO `status_list` (`record_status_id`, `status_name`) VALUES
(1, 'Live'),
(2, 'Parked'),
(3, 'Dead'),
(4, 'Completed')");
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=488 ");
        //Dumpingdata for table `subsectors`
        $this->db->query("INSERT INTO `subsectors` (`subsector_id`, `subsector_name`, `sector_id`) VALUES
(220, 'Basic Materials, Unknown', 109),
(221, 'Aluminum', 109),
(222, 'Chemicals - Major Diversified', 109),
(223, 'Copper', 109),
(224, 'Gold', 109),
(225, 'Independent Oil & Gas', 109),
(226, 'Industrial Metals & Minerals', 109),
(227, 'Major Integrated Oil & Gas', 109),
(228, 'Nonmetallic Mineral Mining', 109),
(229, 'Oil & Gas Drilling & Exploration', 109),
(230, 'Oil & Gas Equipment & Services', 109),
(231, 'Oil & Gas Pipelines', 109),
(232, 'Oil & Gas Refining & Marketing', 109),
(233, 'Silver', 109),
(234, 'Specialty Chemicals', 109),
(235, 'Steel & Iron', 109),
(236, 'Synthetics', 109),
(237, 'Conglomerates', 110),
(238, 'Consumer Goods, Unknown', 111),
(239, 'Appliances', 111),
(240, 'Auto Manufacturers - Major', 111),
(241, 'Auto Parts', 111),
(242, 'Beverages - Brewers', 111),
(243, 'Beverages - Soft Drinks', 111),
(244, 'Beverages - Wineries & Distillers', 111),
(245, 'Business Equipment', 111),
(246, 'Cigarettes', 111),
(247, 'Cleaning Products', 111),
(248, 'Confectioners', 111),
(249, 'Dairy Products', 111),
(250, 'Electronic Equipment', 111),
(251, 'Farm Products', 111),
(252, 'Food - Major Diversified', 111),
(253, 'Home Furnishings & Fixtures', 111),
(254, 'Housewares & Accessories', 111),
(255, 'Meat Products', 111),
(256, 'Office Supplies', 111),
(257, 'Packaging & Containers', 111),
(258, 'Paper & Paper Products', 111),
(259, 'Personal Products', 111),
(260, 'Photographic Equipment & Supplies', 111),
(261, 'Processed & Packaged Goods', 111),
(262, 'Recreational Goods, Unknown', 111),
(263, 'Recreational Vehicles', 111),
(264, 'Rubber & Plastics', 111),
(265, 'Sporting Goods', 118),
(266, 'Textile - Apparel Clothing', 111),
(267, 'Textile - Apparel Footwear & Accessories', 111),
(268, 'Tobacco Products, Unknown', 111),
(269, 'Toys & Games', 111),
(270, 'Trucks & Other Vehicles', 111),
(271, 'Financial, Unknown', 112),
(272, 'Accident & Health Insurance', 119),
(273, 'Asset Management', 112),
(274, 'Closed-End Fund - Debt', 112),
(275, 'Closed-End Fund - Equity', 112),
(276, 'Closed-End Fund - Foreign', 112),
(277, 'Credit Services', 112),
(278, 'Diversified Investments', 112),
(279, 'Foreign Money Center Banks', 112),
(280, 'Foreign Regional Banks', 112),
(281, 'Insurance Brokers', 119),
(282, 'Investment Brokerage - National', 112),
(283, 'Investment Brokerage - Regional', 112),
(284, 'Life Insurance', 119),
(285, 'Banking', 112),
(286, 'Mortgage Investment', 112),
(287, 'Property & Casualty Insurance', 119),
(288, 'Property Management', 112),
(289, 'REIT - Diversified', 112),
(290, 'REIT - Healthcare Facilities', 112),
(291, 'REIT - Hotel/Motel', 112),
(292, 'REIT - Industrial', 112),
(293, 'REIT - Office', 112),
(294, 'REIT - Residential', 112),
(295, 'REIT - Retail', 112),
(296, 'Real Estate Development', 112),
(297, 'Savings & Loans', 112),
(298, 'Surety & Title Insurance', 119),
(299, 'Healthcare, Unknown', 113),
(300, 'Biotechnology', 113),
(301, 'Diagnostic Substances', 113),
(302, 'Drug Delivery', 113),
(303, 'Drug Manufacturers - Major', 113),
(304, 'Drug Manufacturers, Other', 113),
(305, 'Drug Related Products', 113),
(306, 'Drugs - Generic', 113),
(307, 'Health Care Plans', 113),
(308, 'Home Health Care', 113),
(309, 'Pharmaceuticals', 113),
(310, 'Hospitals', 113),
(311, 'Long-Term Care Facilities', 113),
(312, 'Medical Appliances & Equipment', 113),
(313, 'Medical Instruments & Supplies', 113),
(314, 'Medical Laboratories & Research', 113),
(315, 'Medical Practitioners', 113),
(316, 'Specialized Health Services', 113),
(317, 'Industrial Goods, Unknown', 114),
(318, 'Aerospace/Defense - Major Diversified', 114),
(319, 'Aerospace/Defense Products & Services', 114),
(320, 'Cement', 114),
(321, 'Diversified Machinery', 114),
(322, 'Farm & Construction Machinery', 114),
(323, 'General Building Materials', 114),
(324, 'General Contractors', 114),
(325, 'Heavy Construction', 114),
(326, 'Industrial Electrical Equipment', 114),
(327, 'Industrial Equipment & Components', 114),
(328, 'Lumber, Wood Production', 114),
(329, 'Machine Tools & Accessories', 114),
(330, 'Manufactured Housing', 114),
(331, 'Metal Fabrication', 114),
(332, 'Pollution & Treatment Controls', 114),
(333, 'Residential Construction', 114),
(334, 'Small Tools & Accessories', 114),
(335, 'Textile Industrial', 114),
(336, 'Waste Management', 114),
(337, 'Services, Unknown', 115),
(338, 'Advertising Agencies', 115),
(339, 'Air Delivery & Freight Services', 115),
(340, 'Air Services, Other', 115),
(341, 'Apparel Stores', 115),
(342, 'Auto Dealerships', 115),
(343, 'Auto Parts Stores', 115),
(344, 'Auto Parts Wholesale', 115),
(345, 'Basic Materials Wholesale', 115),
(346, 'Broadcasting - Radio', 115),
(347, 'Broadcasting - TV', 115),
(348, 'Building Materials Wholesale', 115),
(349, 'Business Services', 115),
(350, 'Recruitment', 115),
(351, 'CATV Systems', 115),
(352, 'Catalog & Mail Order Houses', 115),
(353, 'Computers Wholesale', 115),
(354, 'Consumer Services', 115),
(355, 'Department Stores', 115),
(356, 'Discount, Variety Stores', 115),
(357, 'Drug Stores', 115),
(358, 'Drugs Wholesale', 115),
(359, 'Education & Training Services', 115),
(360, 'Electronics Stores', 115),
(361, 'Electronics Wholesale', 115),
(362, 'Entertainment - Diversified', 115),
(363, 'Food Wholesale', 115),
(364, 'Gaming Activities', 115),
(365, 'General Entertainment', 115),
(366, 'Grocery Stores', 115),
(367, 'Home Furnishing Stores', 115),
(368, 'Home Improvement Stores', 115),
(369, 'Industrial Equipment Wholesale', 115),
(370, 'Jewelry Stores', 115),
(371, 'Legal Services', 115),
(372, 'Lodging', 115),
(373, 'Major Airlines', 115),
(374, 'Management Services', 115),
(375, 'Marketing Services', 115),
(376, 'Medical Equipment Wholesale', 115),
(377, 'Movie Production, Theaters', 115),
(378, 'Music & Video Stores', 115),
(379, 'Personal Services', 115),
(383, 'Railroads', 115),
(384, 'Regional Airlines', 115),
(385, 'Rental & Leasing Services', 115),
(386, 'Research Services', 115),
(387, 'Transport, Other', 115),
(388, 'Resorts & Casinos', 115),
(389, 'Restaurants', 115),
(390, 'Security & Protection Services', 115),
(391, 'Logistics', 115),
(392, 'Specialty Eateries', 115),
(394, 'Sporting Activities', 118),
(395, 'Sporting Goods Stores', 118),
(396, 'Staffing & Outsourcing Services', 115),
(397, 'Technical Services', 115),
(398, 'Toy & Hobby Stores', 115),
(399, 'Trucking', 115),
(400, 'Wholesale, Unknown', 115),
(401, 'Application Software', 116),
(402, 'Business Software & Services', 116),
(403, 'Communication Equipment', 116),
(404, 'Computer Based Systems', 116),
(405, 'Computer Peripherals', 116),
(406, 'Data Storage Devices', 116),
(407, 'Data Services', 116),
(408, 'Diversified Communication Services', 116),
(409, 'Diversified Computer Systems', 116),
(410, 'Diversified Electronics', 116),
(411, 'Healthcare Information Services', 116),
(412, 'Information & Delivery Services', 116),
(413, 'Information Technology Services', 116),
(414, 'Internet Information Providers', 116),
(415, 'Internet Service Providers', 116),
(416, 'Internet Software & Services', 116),
(417, 'Long Distance Carriers', 116),
(418, 'Multimedia & Graphics Software', 116),
(419, 'Networking & Communication Devices', 116),
(420, 'Personal Computers', 116),
(421, 'Printed Circuit Boards', 116),
(422, 'Processing Systems & Products', 116),
(423, 'Scientific & Technical Instruments', 116),
(424, 'Security Software & Services', 116),
(425, 'Semiconductor - Broad Line', 116),
(426, 'Semiconductor - Integrated Circuits', 116),
(427, 'Semiconductor - Specialized', 116),
(428, 'Semiconductor Equipment & Materials', 116),
(429, 'Semiconductor- Memory Chips', 116),
(430, 'Technical & System Software', 116),
(431, 'Telecom Services', 116),
(433, 'Wireless Communications', 116),
(434, 'Technology, Unknown', 116),
(435, 'Diversified Utilites', 117),
(436, 'Electric Utilities', 117),
(437, 'Foreign Utilities', 117),
(438, 'Gas Utilities', 117),
(439, 'Water Utilities', 117),
(440, 'Utilities, Unknown', 117),
(441, 'Renewable Energy', 117),
(442, 'Accountants', 112),
(443, 'ATM Services', 112),
(444, 'Pensions', 112),
(445, 'Catering', 115),
(446, 'Consultancy', 115),
(447, 'Football Clubs', 118),
(448, 'Insurance, Unknown', 119),
(449, 'Leisure Activites', 115),
(450, 'Lighting', 111),
(451, 'Property Maintenance', 115),
(452, 'Manufacturing', 111),
(453, 'Media and PR', 115),
(454, 'Publishing', 115),
(455, 'Computer Software', 116),
(456, 'Speciality Retailers', 111),
(457, 'Travel', 115),
(458, 'Vending', 115),
(459, 'Beauty Products', 111),
(460, 'Basic Materials, Other', 122),
(461, 'Consumer Goods, Other', 124),
(462, 'Recreational Goods, Other', 124),
(463, 'Tobacco Products, Other', 124),
(464, 'Financial, Other', 125),
(465, 'Healthcare, Other', 126),
(466, 'Drug Manufacturers - Other', 126),
(467, 'Industrial Goods, Other', 127),
(468, 'Services, Other', 128),
(469, 'Publishing - Books', 128),
(470, 'Publishing - Newspapers', 128),
(471, 'Publishing - Periodicals', 128),
(472, 'Specialty Retail, Other', 128),
(473, 'Wholesale, Other', 128),
(474, 'Telecom Services - Domestic', 129),
(475, 'Telecom Services - Foreign', 129),
(476, 'Technology, Other', 129),
(477, 'Utilities, Other', 130),
(478, 'Solar Energy', 130),
(481, 'Online Retail', 111),
(483, 'Car Dealerships', 120),
(484, 'Charity', 112),
(485, 'Leasing', 115),
(486, 'Dept', 112),
(487, 'Mail Order Retail', 111)");
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
        //Table structure for table `surveys_to_campaigns`
        $this->db->query("CREATE TABLE IF NOT EXISTS `surveys_to_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_info_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `default` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `survey_info_id` (`survey_info_id`,`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
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
  PRIMARY KEY (`team_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ");
        //Dumpingdata for table `teams`
        $this->db->query("INSERT INTO `teams` (`team_id`, `team_name`) VALUES
(1, 'Jon Surrey'),
(2, 'Wayne Brosnan'),
(3, 'Dean Hibbert'),
(4, 'Stacy Armitt'),
(5, 'Craig Williams'),
(6, 'David Kemp'),
(7, 'Dave Whittaker')");
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
  `ext` int(3) DEFAULT NULL,
  `token` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `pass_changed` datetime DEFAULT NULL,
  `failed_logins` tinyint(4) NOT NULL,
  `last_failed_login` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `group_id` (`role_id`),
  KEY `repgroup_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ");
        //Dumpingdata for table `users`
        $this->db->query("INSERT INTO `users` (`user_id`, `role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
(1, 1, 1, NULL, 'brad.foster', '32250170a0dca92d53ec9624f336ca24', 'Brad Foster', 1, NULL, NULL, '', '2014-09-19 10:16:23', NULL, NULL, NULL, 0, '2014-09-09 10:25:27'),
(2, 1, 1, NULL, 'doug.frost', '32250170a0dca92d53ec9624f336ca24', 'Doug Frost', 1, NULL, NULL, NULL, '2014-08-12 12:02:55', NULL, NULL, NULL, 0, '2014-08-12 12:02:48'),
(126, 3, 1, NULL, 'demo', '32250170a0dca92d53ec9624f336ca24', 'Agent 492', 1, NULL, NULL, NULL, '2014-08-12 12:02:55', 237, NULL, NULL, 0, '2014-08-12 12:02:48'),
(3, 1, 1, NULL, 'emma.greenfield', '32250170a0dca92d53ec9624f336ca24', 'Emma Greenfield', 1, NULL, NULL, NULL, '2014-08-12 10:05:48', NULL, NULL, NULL, 0, NULL),
(4, 1, 1, NULL, 'david.kemp', '32250170a0dca92d53ec9624f336ca24', 'David Kemp', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', NULL, NULL, NULL, 0, '2014-08-05 08:18:06'),
(5, 1, 1, NULL, 'kirsty.prince', '32250170a0dca92d53ec9624f336ca24', 'Kirsty Princes', 1, NULL, '', '', '2014-08-12 09:08:57', NULL, NULL, NULL, 0, NULL)");
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
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ");
        //Dumpingdata for table `user_groups`
        $this->db->query("INSERT INTO `user_groups` (`group_id`, `group_name`) VALUES
(1, '121')");
        //creating table permissions
        $this->db->query("CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `permission_group` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29");
        //dumping data for permissions
        $this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES
(1, 'set call outcomes', 'Records'),
(2, 'set progress', 'Records'),
(3, 'add surveys', 'Surveys'),
(4, 'view surveys', 'Surveys'),
(5, 'edit surveys', 'Surveys'),
(6, 'delete surveys', 'Surveys'),
(7, 'add contacts', 'Contacts'),
(8, 'edit contacts', 'Contacts'),
(9, 'delete contacts', 'Contacts'),
(10, 'add companies', 'Companies'),
(11, 'edit companies', 'Companies'),
(12, 'add records', 'Companies'),
(13, 'reset records', 'Companies'),
(14, 'park records', 'Companies'),
(15, 'view ownership', 'Ownership'),
(16, 'change ownership', 'Ownership'),
(17, 'view appointments', 'Appointments'),
(18, 'add appointments', 'Appointments'),
(19, 'edit appointments', 'Appointments'),
(20, 'delete appointments', 'Appointments'),
(21, 'view history', 'History'),
(22, 'delete history', 'History'),
(23, 'edit history', 'History'),
(24, 'view call recordings', 'Recordings'),
(25, 'delete call recordings', 'Recordings'),
(26, 'search records', 'Search'),
(27, 'send email', 'Email'),
(28, 'view email', 'Email')");
        //Table structure for table `user_roles`
        $this->db->query("CREATE TABLE IF NOT EXISTS `user_roles` (
  `role_id` int(3) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ");
        //Dumpingdata for table `user_roles`
        $this->db->query("INSERT INTO `user_roles` (`role_id`, `role_name`) VALUES
(1, 'Administrator'),
(2, 'Team Leader'),
(3, 'Team Senior'),
(4, 'Client'),
(5, 'Agent')");
        //create table role permissions
        $this->db->query("CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  UNIQUE KEY `role_id` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

        //Constraints for table `role_permissions`
        $this->db->query("ALTER TABLE `role_permissions` ADD FOREIGN KEY (`role_id`) REFERENCES `121sys`.`permissions`(`permission_id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
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
  ADD CONSTRAINT `FK_template_attachment` FOREIGN KEY (`template_id`) REFERENCES `mail_templates` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE");
        //Constraints for table `email_template_to_campaigns`
        $this->db->query("ALTER TABLE `email_template_to_campaigns`
  ADD CONSTRAINT `FK_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_template` FOREIGN KEY (`template_id`) REFERENCES `mail_templates` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE");
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
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaign_managers` (
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_triggers` (
  `trigger_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  PRIMARY KEY (`trigger_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2");
        $this->db->query("INSERT INTO `email_triggers` (`trigger_id`, `campaign_id`, `outcome_id`) VALUES
(1, 6, 60)");
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_trigger_recipients` (
  `trigger_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `trigger_id` (`trigger_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        $this->db->query("INSERT INTO `email_trigger_recipients` (`trigger_id`, `user_id`) VALUES
(1, 127)");
        $this->db->query("CREATE TABLE IF NOT EXISTS `ownership_triggers` (
  `trigger_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  PRIMARY KEY (`trigger_id`),
  UNIQUE KEY `trigger_id2` (`campaign_id`,`outcome_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2");
        $this->db->query("INSERT INTO `ownership_triggers` (`trigger_id`, `campaign_id`, `outcome_id`) VALUES
(1, 6, 60)");
        $this->db->query("CREATE TABLE IF NOT EXISTS `ownership_trigger_users` (
  `trigger_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `trigger_id` (`trigger_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        $this->db->query("INSERT INTO `ownership_trigger_users` (`trigger_id`, `user_id`) VALUES
(1, 127)");
    }
    public function down()
    {
        //cannot roll back initial install
    }
}
?>