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
        
        //Table structure for table `answers_to_options`
        $this->db->query("CREATE TABLE IF NOT EXISTS `answers_to_options` (
		  `answer_id` int(11) NOT NULL,
		  `option_id` int(11) NOT NULL,
		  UNIQUE KEY `answer_id2` (`answer_id`,`option_id`),
		  KEY `answer_id` (`answer_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
        
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
		  `status` int(11) DEFAULT '1',
		  `created_by` int(11) NOT NULL,
		  `date_updated` datetime DEFAULT NULL,
		  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`appointment_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
        
        //Table structure for table `appointment_attendees`
        $this->db->query("CREATE TABLE IF NOT EXISTS `appointment_attendees` (
		  `appointment_id` int(11) NOT NULL,
		  `user_id` int(11) NOT NULL,
		  UNIQUE KEY `appointment_id_2` (`appointment_id`,`user_id`),
		  KEY `appointment_id` (`appointment_id`),
		  KEY `user_id` (`user_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        
        //Table structure for table `campaigns`
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
		  PRIMARY KEY (`feature_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13");
        

        //Table structure for table `campaign_types`
        $this->db->query("CREATE TABLE IF NOT EXISTS `campaign_types` (
		  `campaign_type_id` tinyint(3) NOT NULL AUTO_INCREMENT,
		  `campaign_type_desc` varchar(100) CHARACTER SET utf8 NOT NULL,
		  PRIMARY KEY (`campaign_type_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ");

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
        
        //Table structure for table `progress_description`
        $this->db->query("CREATE TABLE IF NOT EXISTS `progress_description` (
		  `progress_id` int(11) NOT NULL AUTO_INCREMENT,
		  `description` varchar(100) CHARACTER SET utf8 NOT NULL,
		  `progress_color` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
		  PRIMARY KEY (`progress_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ");
        
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
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=488 ");
        
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
        
        //creating table permissions
        $this->db->query("CREATE TABLE IF NOT EXISTS `permissions` (
		  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
		  `permission_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		  `permission_group` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`permission_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29");
        
        //Table structure for table `user_roles`
        $this->db->query("CREATE TABLE IF NOT EXISTS `user_roles` (
		  `role_id` int(3) NOT NULL AUTO_INCREMENT,
		  `role_name` varchar(30) CHARACTER SET utf8 NOT NULL,
		  PRIMARY KEY (`role_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ");
        
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
        
//         $this->db->query("INSERT INTO `email_triggers` (`trigger_id`, `campaign_id`, `outcome_id`) VALUES
// 		(1, 6, 60)");
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_trigger_recipients` (
		  `trigger_id` int(11) NOT NULL,
		  `user_id` int(11) NOT NULL,
		  UNIQUE KEY `trigger_id` (`trigger_id`,`user_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        
//         $this->db->query("INSERT INTO `email_trigger_recipients` (`trigger_id`, `user_id`) VALUES
// 		(1, 127)");
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `ownership_triggers` (
		  `trigger_id` int(11) NOT NULL AUTO_INCREMENT,
		  `campaign_id` int(11) NOT NULL,
		  `outcome_id` int(11) NOT NULL,
		  PRIMARY KEY (`trigger_id`),
		  UNIQUE KEY `trigger_id2` (`campaign_id`,`outcome_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2");
        
//         $this->db->query("INSERT INTO `ownership_triggers` (`trigger_id`, `campaign_id`, `outcome_id`) VALUES
// 		(1, 6, 60)");

        $this->db->query("CREATE TABLE IF NOT EXISTS `ownership_trigger_users` (
		  `trigger_id` int(11) NOT NULL,
		  `user_id` int(11) NOT NULL,
		  UNIQUE KEY `trigger_id` (`trigger_id`,`user_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        
//         $this->db->query("INSERT INTO `ownership_trigger_users` (`trigger_id`, `user_id`) VALUES
// 		(1, 127)");

        //Dump the init data
        $this->Database_model->init_data();
    }
    public function down()
    {
        //cannot roll back initial install
    }
}
?>