<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Database_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the version
     *
     * @return string
     */
    public function get_version()
    {
        $name = $this->db->database;
        $qry = "show tables from `$name` where Tables_in_$name = 'migrations'";
        if ($this->db->query($qry)->num_rows()) {
            return $this->db->get('migrations')->row()->version;
        } else {
            return "Unknown";
        }
    }

    /**
     * Drop all tables
     */
    public function drop_tables()
    {
        //drop database because it's easier/faster than dropping all the tables individually
        $name = $this->db->database;
        $qry = "drop database `$name`";
        $this->db->query($qry);
        //recreate database
        $qry = "create database `$name`";
        return $this->db->query($qry);
    }

    /**
     * Truncate all the tables in the database (except the migrations table)
     */
    private function truncate_data()
    {
        $tables = $this->db->list_tables();

        $this->db->query("SET foreign_key_checks = 0");

        foreach ($tables as $table) {
            //If the table is migrations, don't truncate
            if ($table != 'migrations') {
                $this->db->empty_table($table);
                $this->db->query("ALTER TABLE `" . $table . "` AUTO_INCREMENT = 1");
            }
        }

        $this->db->query("SET foreign_key_checks = 1");
    }

    /****************************************************************************************************************************/
    /*********************************** INIT DATA ******************************************************************************/
    /****************************************************************************************************************************/

    /**
     * Dump the init data
     */
    public function init_data()
    {

        //Truncate all the tables
        $this->truncate_data();

        $this->firephp->log("inserting campaign_features");

        //Dumping data for table `campaign_features`
        $this->db->query("INSERT INTO `campaign_features` (`feature_name`, `panel_path`) VALUES
		('Contacts', 'contacts.php'),
		('Company', 'company.php'),
		('Update Record', 'record_update.php'),
		('Sticky Note', 'sticky.php'),
		('Ownership Changer', 'ownership.php'),
		('Scripts', 'scripts.php'),
		('History', 'history.php'),
		('Custom Info', 'custom_info.php'),
		('Emails', 'emails.php'),
		('Appointment Setting', 'appointments.php'),
		('Surveys', 'survey.php'),
		('Recordings', 'recordings.php'),
		('Attachments', 'attachments.php'),
		('Webform', 'webform.php')");


        if ($this->db->_error_message()) {
            return "campaign_features";
        }

        $this->firephp->log("inserting campaign_types");

        //dumping into campaign types table
        $this->db->query("INSERT INTO `campaign_types` (`campaign_type_desc`) VALUES
		('B2C'),
		('B2B')");

        if ($this->db->_error_message()) {
            return "campaign_types";
        }

        $this->firephp->log("inserting configuration");

        //Dumpingdata for table `configuration`
        $this->db->query("INSERT INTO `configuration` (`use_fullname`) VALUES
		(1)");

        if ($this->db->_error_message()) {
            return "configuration";
        }

        $this->firephp->log("inserting contact_status");

        //Dumpingdata for table `contact_status`
        $this->db->query("INSERT INTO `contact_status` (`contact_status_name`, `score_threshold`, `colour`) VALUES
		('Detractor', 6, '#FF0000'),
		('Passive', 7, '#FF9900'),
		('Promoter', 8, '#00FF00')");


        if ($this->db->_error_message()) {
            return "contact_status";
        }

        $this->firephp->log("inserting status_list");

        //Dumpingdata for table `status_list`
        $this->db->query("INSERT INTO `status_list` (`record_status_id`, `status_name`) VALUES
		(1, 'Live'),
		(2, 'Parked'),
		(3, 'Dead'),
		(4, 'Completed')");

        if ($this->db->_error_message()) {
            return "record_status_id";
        }

        $this->firephp->log("inserting outcomes");
        //Dumpingdata for table `outcomes`
        $this->db->query("INSERT INTO `outcomes` (`outcome_id`, `outcome`, `set_status`, `set_progress`, `positive`, `dm_contact`, `sort`, `enable_select`, `force_comment`, `force_nextcall`, `delay_hours`, `no_history`, `disabled`, `keep_record`) VALUES
		(1, 'Call Back', 1, NULL, NULL, NULL, 4, 1, NULL, 1, NULL, NULL, NULL, NULL),
		(2, 'Call Back DM', 1, NULL, NULL, 1, 1, 1, NULL, 1, NULL, NULL, NULL, 1),
		(3, 'Answer Machine', 1, NULL, NULL, NULL, 9, 1, NULL, NULL, 4, NULL, NULL, NULL),
		(4, 'Dead Line', 3, NULL, NULL, NULL, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(5, 'Engaged', 1, NULL, NULL, NULL, 9, 1, NULL, NULL, 4, NULL, NULL, NULL),
		(7, 'No Answer', 1, NULL, NULL, NULL, 9, 1, NULL, NULL, 4, NULL, NULL, NULL),
		(12, 'Not Interested', 3, NULL, NULL, 1, 9, 1, 1, NULL, NULL, NULL, NULL, NULL),
		(13, 'Not Eligible', 3, NULL, NULL, NULL, 9, 1, 1, NULL, NULL, NULL, NULL, NULL),
		(17, 'Unavailable', 1, NULL, NULL, NULL, 9, 1, NULL, NULL, 4, NULL, NULL, NULL),
		(30, 'Deceased', 3, NULL, NULL, NULL, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(32, 'Moved', 3, NULL, NULL, NULL, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(33, 'Slammer', 3, NULL, NULL, NULL, 9, 1, NULL, NULL, 4, NULL, NULL, NULL),
		(60, 'Survey Complete', 4, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(63, 'Wrong Number', 3, NULL, NULL, NULL, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(64, 'Duplicate', 3, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(65, 'Fax Machine', 3, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(66, 'Survey Refused', 3, NULL, NULL, NULL, 9, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(67, 'Adding additional notes', NULL, NULL, NULL, NULL, 10, 1, 1, NULL, NULL, NULL, NULL, NULL),
		(68, 'Changing next action date', NULL, NULL, NULL, NULL, 2, 1, NULL, NULL, NULL, 1, NULL, NULL),
		(69, 'No Number', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
		(70, 'Transfer', 4, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, 1),
		(71, 'Cross Transfer', 4, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, 1),
		(72, 'Appointment', 4, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, 1, NULL),
		(73, 'Not in business', 3, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(74, 'Remove from records', 3, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(76, 'Not required', 3, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL),
		(78, 'Head Office Deals', 3, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(79, 'Gatekeeper Refusal', 3, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(80, 'Language Barrier', 3, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(81, 'No Sale', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
		(82, 'Existing Customer', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
		(83, 'Sale', 4, NULL, 1, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(84, 'Email Sent', 1, NULL, 1, 1, NULL, 1, NULL, 1, NULL, NULL, NULL, 1),
		(85, 'Interest Now', 1, NULL, NULL, 1, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
		(86, 'Interest Future', 1, NULL, NULL, 1, NULL, 1, NULL, 1, NULL, NULL, NULL, 1),
		(87, 'Website enquiry', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
		(88, 'Research Required', 1, NULL, NULL, NULL, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
		(89, 'Data Captured', NULL, NULL, 1, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1),
		(90, 'Soft Email', 3, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(91, 'Under Warrenty', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, NULL, NULL, NULL, NULL),
		(92, 'Not Eligible:Rented Property', 3, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL),
		(93, 'Not Eligible:Helplink', 3, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(94, 'Meeting booked - face to face', 4, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(95, 'Meeting booked - remote', 4, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
		(96, 'Telephone Appointment – Consultant', 4, 1, 1, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL)");


        $this->db->query("ALTER TABLE `outcomes` AUTO_INCREMENT = 73");

        if ($this->db->_error_message()) {
            return "outcomes";
        }

        $this->firephp->log("inserting park_codes");

        //Dumpingdata for table `park_codes`
        $this->db->query("INSERT INTO `park_codes` (`parked_code`, `park_reason`) VALUES
		(5, 'Duplicated'),
		(8, 'No Numbers'),
		(6, 'Not in date'),
		(2, 'Not Working'),
		(10, 'Onion'),
		(7, 'Out of Area'),
		(1, 'Rationing'),
		(9, 'Reached max dials'),
		(4, 'Suppressed'),
		(11, 'Unity'),
		(3, 'Unknown')");

        if ($this->db->_error_message()) {
            return "park_codes";
        }


        $this->firephp->log("inserting progress_description");
        //Dumpingdata for table `progress_description`
        $this->db->query("INSERT INTO `progress_description` (`description`, `progress_color`) VALUES
		('Pending', 'red'),
		('In Progress', 'orange'),
		('Complete', 'green')");

        if ($this->db->_error_message()) {
            return "progress_description";
        }


        //Dumpingdata for table `sectors` and the `subsectors`
        $sectors = array(
            'Other' => array(),
            'Basic Materials' => array(
                'Basic Materials, Unknown',
                'Aluminum',
                'Chemicals - Major Diversified',
                'Copper',
                'Gold',
                'Independent Oil & Gas',
                'Industrial Metals & Minerals',
                'Major Integrated Oil & Gas',
                'Nonmetallic Mineral Mining',
                'Oil & Gas Drilling & Exploration',
                'Oil & Gas Equipment & Services',
                'Oil & Gas Pipelines',
                'Oil & Gas Refining & Marketing',
                'Silver',
                'Specialty Chemicals',
                'Steel & Iron',
                'Synthetics',
            ),
            'Consumer Goods' => array(
                'Consumer Goods, Unknown',
                'Appliances',
                'Auto Manufacturers - Major',
                'Auto Parts',
                'Beverages - Brewers',
                'Beverages - Soft Drinks',
                'Beverages - Wineries & Distillers',
                'Business Equipment',
                'Cigarettes',
                'Cleaning Products',
                'Confectioners',
                'Dairy Products',
                'Electronic Equipment',
                'Farm Products',
                'Food - Major Diversified',
                'Home Furnishings & Fixtures',
                'Housewares & Accessories',
                'Meat Products',
                'Office Supplies',
                'Packaging & Containers',
                'Paper & Paper Products',
                'Personal Products',
                'Photographic Equipment & Supplies',
                'Processed & Packaged Goods',
                'Recreational Goods, Unknown',
                'Recreational Vehicles',
                'Rubber & Plastics',
                'Textile - Apparel Clothing',
                'Textile - Apparel Footwear & Accessories',
                'Tobacco Products, Unknown',
                'Toys & Games',
                'Trucks & Other Vehicles',
                'Lighting',
                'Manufacturing',
                'Speciality Retailers',
                'Beauty Products',
                'Mail Order Retail',
                'Online Retail',
            ),
            'Financial' => array(
                'Financial, Unknown',
                'Asset Management',
                'Closed-End Fund - Debt',
                'Closed-End Fund - Equity',
                'Closed-End Fund - Foreign',
                'Credit Services',
                'Diversified Investments',
                'Foreign Money Center Banks',
                'Foreign Regional Banks',
                'Investment Brokerage - National',
                'Investment Brokerage - Regional',
                'Banking',
                'Mortgage Investment',
                'Property Management',
                'REIT - Diversified',
                'REIT - Healthcare Facilities',
                'REIT - Hotel/Motel',
                'REIT - Industrial',
                'REIT - Office',
                'REIT - Residential',
                'REIT - Retail',
                'Real Estate Development',
                'Savings & Loans',
                'Accountants',
                'ATM Services',
                'Pensions',
                'Charity',
                'Dept',
            ),
            'Healthcare' => array(
                'Healthcare, Unknown',
                'Biotechnology',
                'Diagnostic Substances',
                'Drug Delivery',
                'Drug Manufacturers - Major',
                'Drug Manufacturers, Other',
                'Drug Related Products',
                'Drugs - Generic',
                'Health Care Plans',
                'Home Health Care',
                'Pharmaceuticals',
                'Hospitals',
                'Long-Term Care Facilities',
                'Medical Appliances & Equipment',
                'Medical Instruments & Supplies',
                'Medical Laboratories & Research',
                'Medical Practitioners',
                'Specialized Health Services',
            ),
            'Industrial Goods' => array(
                'Industrial Goods, Unknown',
                'Aerospace/Defense - Major Diversified',
                'Aerospace/Defense Products & Services',
                'Cement',
                'Diversified Machinery',
                'Farm & Construction Machinery',
                'General Building Materials',
                'General Contractors',
                'Heavy Construction',
                'Industrial Electrical Equipment',
                'Industrial Equipment & Components',
                'Lumber, Wood Production',
                'Machine Tools & Accessories',
                'Manufactured Housing',
                'Metal Fabrication',
                'Pollution & Treatment Controls',
                'Residential Construction',
                'Small Tools & Accessories',
                'Textile Industrial',
                'Waste Management',
            ),
            'Services' => array(
                'Services, Unknown',
                'Advertising Agencies',
                'Air Delivery & Freight Services',
                'Air Services, Other',
                'Apparel Stores',
                'Auto Dealerships',
                'Auto Parts Stores',
                'Auto Parts Wholesale',
                'Basic Materials Wholesale',
                'Broadcasting - Radio',
                'Broadcasting - TV',
                'Building Materials Wholesale',
                'Business Services',
                'Recruitment',
                'CATV Systems',
                'Catalog & Mail Order Houses',
                'Computers Wholesale',
                'Consumer Services',
                'Department Stores',
                'Discount, Variety Stores',
                'Drug Stores',
                'Drugs Wholesale',
                'Education & Training Services',
                'Electronics Stores',
                'Electronics Wholesale',
                'Entertainment - Diversified',
                'Food Wholesale',
                'Gaming Activities',
                'General Entertainment',
                'Grocery Stores',
                'Home Furnishing Stores',
                'Home Improvement Stores',
                'Industrial Equipment Wholesale',
                'Jewelry Stores',
                'Legal Services',
                'Lodging',
                'Major Airlines',
                'Management Services',
                'Marketing Services',
                'Medical Equipment Wholesale',
                'Movie Production, Theaters',
                'Music & Video Stores',
                'Personal Services',
                'Railroads',
                'Regional Airlines',
                'Rental & Leasing Services',
                'Research Services',
                'Transport, Other',
                'Resorts & Casinos',
                'Restaurants',
                'Security & Protection Services',
                'Logistics',
                'Specialty Eateries',
                'Staffing & Outsourcing Services',
                'Technical Services',
                'Toy & Hobby Stores',
                'Trucking',
                'Wholesale, Unknown',
                'Catering',
                'Consultancy',
                'Leisure Activites',
                'Property Maintenance',
                'Media and PR',
                'Publishing',
                'Travel',
                'Vending',
                'Leasing',
            ),
            'Technology' => array(
                'Application Software',
                'Business Software & Services',
                'Communication Equipment',
                'Computer Based Systems',
                'Computer Peripherals',
                'Data Storage Devices',
                'Data Services',
                'Diversified Communication Services',
                'Diversified Computer Systems',
                'Diversified Electronics',
                'Healthcare Information Services',
                'Information & Delivery Services',
                'Information Technology Services',
                'Internet Information Providers',
                'Internet Service Providers',
                'Internet Software & Services',
                'Long Distance Carriers',
                'Multimedia & Graphics Software',
                'Networking & Communication Devices',
                'Personal Computers',
                'Printed Circuit Boards',
                'Processing Systems & Products',
                'Scientific & Technical Instruments',
                'Security Software & Services',
                'Semiconductor - Broad Line',
                'Semiconductor - Integrated Circuits',
                'Semiconductor - Specialized',
                'Semiconductor Equipment & Materials',
                'Semiconductor- Memory Chips',
                'Technical & System Software',
                'Telecom Services',
                'Wireless Communications',
                'Technology, Unknown',
                'Computer Software',
            ),
            'Utilities' => array(
                'Diversified Utilites',
                'Electric Utilities',
                'Foreign Utilities',
                'Gas Utilities',
                'Water Utilities',
                'Utilities, Unknown',
                'Renewable Energy',
            ),
            'Sports and Fitness' => array(
                'Sporting Goods',
                'Football Clubs',
                'Sporting Activities',
                'Sporting Goods Stores',
            ),
            'Insurance' => array(
                'Accident & Health Insurance',
                'Insurance Brokers',
                'Life Insurance',
                'Property & Casualty Insurance',
                'Surety & Title Insurance',
                'Insurance, Unknown',
            ),
            'Transport' => array(
                'Car Dealerships',
            ),
        );
        $this->firephp->log("inserting sectors");
        foreach ($sectors as $sector => $subsectorList) {
            //Dumpingdata for table `sector`
            $this->db->query("INSERT INTO `sectors` (`sector_name`) VALUES ('" . $sector . "')");
            if ($this->db->_error_message()) {
                return "sectors";
            }

            $sector_id = $this->db->insert_id();
            foreach ($subsectorList as $subsector) {
                //Dumpingdata for table `subsectors` for this sector
                $this->db->query("INSERT INTO `subsectors` (`subsector_name`, `sector_id`) VALUES ('" . $subsector . "'," . $sector_id . ")");
                if ($this->db->_error_message()) {
                    return "subsectors";
                }
            }
        }

        $this->firephp->log("inserting user_groups");
        //Dumpingdata for table `user_groups`
        $this->db->query("INSERT INTO `user_groups` (`group_name`) VALUES
		('121')");

        if ($this->db->_error_message()) {
            return "user_groups";
        }
        $this->firephp->log("inserting user_roles");
        //Dumpingdata for table `user_roles`
        $this->db->query("INSERT INTO `user_roles` (`role_id`, `role_name`) VALUES
			(1, 'Administrator'),
			(2, 'Team Leader'),
			(3, 'Team Senior'),
			(4, 'Client'),
			(5, 'Agent'),
			(6, 'Client Services')");

        if ($this->db->_error_message()) {
            return "user_roles";
        }
        $this->firephp->log("inserting users");
        //Dumping the administrator user
        $this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
		(1, 1, NULL, 'admin', '32250170a0dca92d53ec9624f336ca24', 'Administrator', 1, NULL, NULL, '', '2014-09-19 10:16:23', NULL, NULL, NULL, 0, '2014-09-09 10:25:27')");

        if ($this->db->_error_message()) {
            return "users";
        }
        $this->firephp->log("inserting permissions");
        //dumping data for permissions
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES
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
                        (12, 'add records', 'Records'),
                        (13, 'reset records', 'Records'),
                        (14, 'park records', 'Records'),
                        (15, 'view ownership', 'Ownership'),
                        (16, 'change ownership', 'Ownership'),
                        (17, 'view appointments', 'Appointments'),
                        (18, 'add appointments', 'Appointments'),
                        (19, 'edit appointments', 'Appointments'),
                        (20, 'delete appointments', 'Appointments'),
                        (21, 'view history', 'History'),
                        (22, 'delete history', 'History'),
                        (23, 'edit history', 'History'),
                        (24, 'view recordings', 'Recordings'),
                        (25, 'delete recordings', 'Recordings'),
                        (26, 'search records', 'Search'),
                        (27, 'send email', 'Email'),
                        (28, 'view email', 'Email'),
                        (29, 'all campaigns', 'System'),
                        (30, 'agent dash', 'Dashboards'),
                        (31, 'client dash', 'Dashboards'),
                        (32, 'management dash', 'Dashboards'),
                        (34, 'search campaigns', 'Search'),
                        (35, 'search surveys', 'Search'),
                        (36, 'log hours', 'System'),
                        (37, 'edit scripts', 'Admin'),
                        (38, 'edit templates', 'Admin'),
                        (39, 'reassign data', 'Data'),
                        (40, 'view logs', 'Admin'),
                        (41, 'view hours', 'Admin'),
                        (42, 'show footer', 'System'),
                        (43, 'admin menu', 'Admin'),
                        (44, 'campaign menu', 'Admin'),
                        (45, 'view attachments', 'Attachments'),
                        (46, 'add attachment', 'Attachments'),
                        (47, 'full calendar', 'Calendar'),
                        (48, 'mini calendar', 'Calendar'),
                        (49, 'delete email', 'Email'),
                        (52, 'by agent', 'Reports'),
                        (56, 'nbf dash', 'Dashboards'),
                        (57, 'mix campaigns', 'System'),
                        (59, 'search parked', 'Search'),
                        (60, 'search unassigned', 'Search'),
                        (61, 'search any owner', 'Search'),
                        (62, 'search groups', 'Search'),
                        (63, 'search dead', 'Search'),
                        (64, 'view own records', 'Default view'),
                        (65, 'view own group', 'Default view'),
                        (66, 'search teams', 'Search'),
                        (67, 'view own team', 'Default view'),
                        (69, 'by group', 'Reports'),
                        (70, 'by team', 'Reports'),
                        (71, 'email', 'Reports'),
                        (72, 'outcomes', 'Reports'),
                        (73, 'activity', 'Reports'),
                        (74, 'Transfers', 'Reports'),
                        (75, 'survey answers', 'Reports'),
                        (76, 'urgent flag', 'Records'),
                        (77, 'urgent dropdown', 'Records'),
                        (78, 'search recordings', 'Recordings'),
                        (79, 'reports menu', 'Reports'),
                        (80, 'data menu', 'Data'),
                        (81, 'import data', 'Data'),
                        (82, 'export data', 'Data'),
                        (83, 'archive data', 'Data'),
                        (84, 'ration data', 'Data'),
                        (85, 'use callpot', 'System'),
                        (86, 'view unassigned', 'Default view'),
                        (87, 'view parked', 'Default view'),
                        (88, 'view dead', 'Default view'),
                        (89, 'view completed', 'Default view'),
                        (90, 'view live', 'Default view'),
                        (91, 'view pending', 'Default view'),
                        (92, 'keep records', 'System'),
                        (93, 'files menu', 'Files'),
                        (94, 'view files', 'Files'),
                        (98, 'admin files', 'Admin'),
                        (99, 'list records', 'Records'),
                        (102, 'delete files', 'Files'),
                        (103, 'add files', 'Files'),
                        (104, 'search files', 'Files'),
                        (108, 'view dashboard', 'Dashboards'),
                        (110, 'search actions', 'Search'),
                        (111, 'edit export', 'Data'),
                        (112, 'edit outcomes', 'Data'),
                        (113, 'triggers', 'Data'),
                        (114, 'duplicates', 'Data'),
                        (115, 'suppression', 'Data'),
                        (116, 'parkcodes', 'Data'),
                        (117, 'productivity', 'Reports'),
                        (118, 'database', 'Admin'),
                        (119, 'campaign access', 'Admin'),
                        (120, 'campaign setup', 'Admin'),
                        (121, 'planner', 'Records')");

        if ($this->db->_error_message()) {
            return "permissions";
        }
        $this->firephp->log("inserting role_permissions");
        $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES
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
                        (1, 22),
                        (1, 23),
                        (1, 24),
                        (1, 25),
                        (1, 26),
                        (1, 27),
                        (1, 28),
                        (1, 29),
                        (1, 30),
                        (1, 31),
                        (1, 32),
                        (1, 34),
                        (1, 35),
                        (1, 37),
                        (1, 38),
                        (1, 39),
                        (1, 40),
                        (1, 41),
                        (1, 43),
                        (1, 44),
                        (1, 45),
                        (1, 46),
                        (1, 47),
                        (1, 48),
                        (1, 49),
                        (1, 52),
                        (1, 56),
                        (1, 57),
                        (1, 59),
                        (1, 60),
                        (1, 61),
                        (1, 62),
                        (1, 63),
                        (1, 66),
                        (1, 69),
                        (1, 70),
                        (1, 71),
                        (1, 72),
                        (1, 73),
                        (1, 74),
                        (1, 75),
                        (1, 76),
                        (1, 79),
                        (1, 80),
                        (1, 81),
                        (1, 82),
                        (1, 83),
                        (1, 84),
                        (1, 86),
                        (1, 90),
                        (1, 93),
                        (1, 94),
                        (1, 95),
                        (1, 96),
                        (1, 97),
                        (1, 99),
                        (1, 110),
                        (1, 111),
                        (1, 112),
                        (1, 113),
                        (1, 114),
                        (1, 115),
                        (1, 116),
                        (1, 117),
                        (1, 118),
                        (1, 119),
                        (1, 120),
                        (1, 121),
                        (2, 1),
                        (2, 3),
                        (2, 4),
                        (2, 5),
                        (2, 6),
                        (2, 7),
                        (2, 8),
                        (2, 9),
                        (2, 10),
                        (2, 11),
                        (2, 12),
                        (2, 13),
                        (2, 14),
                        (2, 15),
                        (2, 16),
                        (2, 17),
                        (2, 18),
                        (2, 19),
                        (2, 20),
                        (2, 21),
                        (2, 22),
                        (2, 23),
                        (2, 24),
                        (2, 25),
                        (2, 26),
                        (2, 27),
                        (2, 28),
                        (2, 29),
                        (2, 32),
                        (2, 34),
                        (2, 35),
                        (2, 38),
                        (2, 39),
                        (2, 40),
                        (2, 41),
                        (2, 43),
                        (2, 44),
                        (2, 47),
                        (2, 48),
                        (2, 49),
                        (2, 52),
                        (2, 60),
                        (2, 61),
                        (2, 63),
                        (2, 67),
                        (2, 69),
                        (2, 70),
                        (2, 71),
                        (2, 72),
                        (2, 73),
                        (2, 74),
                        (2, 75),
                        (2, 76),
                        (2, 79),
                        (2, 92),
                        (2, 99),
                        (2, 108),
                        (2, 119),
                        (2, 120),
                        (3, 1),
                        (3, 3),
                        (3, 4),
                        (3, 5),
                        (3, 6),
                        (3, 7),
                        (3, 8),
                        (3, 9),
                        (3, 10),
                        (3, 11),
                        (3, 12),
                        (3, 13),
                        (3, 14),
                        (3, 15),
                        (3, 17),
                        (3, 18),
                        (3, 19),
                        (3, 20),
                        (3, 21),
                        (3, 22),
                        (3, 23),
                        (3, 24),
                        (3, 25),
                        (3, 27),
                        (3, 28),
                        (3, 30),
                        (3, 32),
                        (3, 35),
                        (3, 36),
                        (3, 40),
                        (3, 41),
                        (3, 42),
                        (3, 43),
                        (3, 45),
                        (3, 46),
                        (3, 47),
                        (3, 48),
                        (3, 61),
                        (3, 63),
                        (3, 64),
                        (3, 66),
                        (3, 71),
                        (3, 72),
                        (3, 73),
                        (3, 79),
                        (3, 85),
                        (3, 90),
                        (3, 108),
                        (4, 2),
                        (4, 3),
                        (4, 4),
                        (4, 5),
                        (4, 7),
                        (4, 8),
                        (4, 10),
                        (4, 11),
                        (4, 12),
                        (4, 13),
                        (4, 15),
                        (4, 16),
                        (4, 17),
                        (4, 18),
                        (4, 19),
                        (4, 21),
                        (4, 23),
                        (4, 26),
                        (4, 27),
                        (4, 28),
                        (4, 31),
                        (4, 45),
                        (4, 47),
                        (4, 60),
                        (4, 63),
                        (4, 64),
                        (4, 72),
                        (4, 73),
                        (4, 75),
                        (4, 76),
                        (4, 79),
                        (4, 89),
                        (5, 1),
                        (5, 3),
                        (5, 4),
                        (5, 5),
                        (5, 7),
                        (5, 8),
                        (5, 10),
                        (5, 11),
                        (5, 12),
                        (5, 13),
                        (5, 15),
                        (5, 17),
                        (5, 18),
                        (5, 19),
                        (5, 21),
                        (5, 27),
                        (5, 28),
                        (5, 30),
                        (5, 35),
                        (5, 36),
                        (5, 42),
                        (5, 45),
                        (5, 46),
                        (5, 47),
                        (5, 48),
                        (5, 60),
                        (5, 64),
                        (5, 71),
                        (5, 72),
                        (5, 73),
                        (5, 79),
                        (5, 85),
                        (5, 90),
                        (5, 99),
                        (5, 108),
                        (6, 2),
                        (6, 4),
                        (6, 5),
                        (6, 7),
                        (6, 8),
                        (6, 9),
                        (6, 10),
                        (6, 11),
                        (6, 12),
                        (6, 13),
                        (6, 14),
                        (6, 15),
                        (6, 16),
                        (6, 17),
                        (6, 18),
                        (6, 19),
                        (6, 20),
                        (6, 21),
                        (6, 22),
                        (6, 23),
                        (6, 24),
                        (6, 25),
                        (6, 26),
                        (6, 27),
                        (6, 28),
                        (6, 29),
                        (6, 30),
                        (6, 31),
                        (6, 32),
                        (6, 34),
                        (6, 35),
                        (6, 37),
                        (6, 38),
                        (6, 39),
                        (6, 40),
                        (6, 41),
                        (6, 43),
                        (6, 44),
                        (6, 45),
                        (6, 46),
                        (6, 47),
                        (6, 48),
                        (6, 52),
                        (6, 57),
                        (6, 59),
                        (6, 60),
                        (6, 61),
                        (6, 62),
                        (6, 63),
                        (6, 66),
                        (6, 69),
                        (6, 70),
                        (6, 71),
                        (6, 72),
                        (6, 73),
                        (6, 74),
                        (6, 75),
                        (6, 78),
                        (6, 79),
                        (6, 89),
                        (6, 90),
                        (6, 108)");

        $this->firephp->log("inserting data_sources");
        //Dumpingdata for table `data_sources` for Manual
        $this->db->query("INSERT INTO `data_sources` (`source_name`, `cost_per_record`) VALUES
		('Manual', NULL)");

        if ($this->db->_error_message()) {
            return "data_sources";
        }


        $this->firephp->log("inserting appointment_rule_reasons");
        //Insert appointment_rule_reasons
        $this->db->query("INSERT ignore INTO `appointment_rule_reasons` (`reason_id`,`reason`) VALUES (1, 'Holiday')");
        $this->db->query("INSERT ignore INTO `appointment_rule_reasons` (`reason_id`,`reason`) VALUES (3, 'Other')");

        if ($this->db->_error_message()) {
            return "appointment_rule_reasons";
        }

        $this->firephp->log("inserting uk_postcodes");
        //Dumping the uk postcodes
        $db = $this->db->database;
        $username = $this->db->username;
        $password = $this->db->password;
        exec("mysql -u " . $username . " -p" . $password . " -h localhost $db < " . $_SERVER['DOCUMENT_ROOT'] . "/upload/uk_postcodes.sql", $output);

        //Dumping data for table `time_exception_type`
        $this->db->query("INSERT INTO `time_exception_type` (`exception_name`, `paid`) VALUES
				('Lunch', 0),
				('Break', 1),
				('Training', 1)");

        if ($this->db->_error_message()) {
            return "time_exception_type";
        }

        //Dumping data for table `appointment_types`
        $this->db->query("INSERT INTO `appointment_types` (`appointment_type`, `campaign_id`) VALUES
				('Face to face', NULL),
				('Telephone', NULL)");

        if ($this->db->_error_message()) {
            return "appointment_types";
        }

        return "success";
    }

    /****************************************************************************************************************************/
    /****************************************************************************************************************************/
    /*********************************** DEMO DATA ******************************************************************************/
    /****************************************************************************************************************************/
    /****************************************************************************************************************************/

    /**
     * Load demo data
     *
     * @return string
     */
    public function demo_data()
    {
        $this->firephp->log("inserting clients");
        //dumping data sample into clients
        $this->db->query("INSERT INTO `clients` (`client_name`) VALUES
		('121'),
		('Sample Client')");


        if ($this->db->_error_message()) {
            return "clients";
        }

        //create sample campaign
        $i = 1;
        $this->firephp->log("inserting campaigns");
        foreach ($this->db->get('clients')->result() as $client) {
            foreach ($this->db->get('campaign_types')->result() as $campaign_type) {
                $this->db->query("INSERT INTO `campaigns` (`campaign_name`, `campaign_type_id`, `client_id`, `start_date`, `end_date`, `campaign_status`, `email_recipients`, `reassign_to`, `custom_panel_name`) VALUES
				('Sample " . $campaign_type->campaign_type_desc . " Campaign_" . $i++ . "', '" . $campaign_type->campaign_type_id . "', " . $client->client_id . ", '2014-09-30', NULL, 1, NULL, NULL, '')");


                $campaign_id = $this->db->insert_id();
                $this->firephp->log("inserting record_details_fields");
                $this->db->query("INSERT INTO `record_details_fields` (`campaign_id`, `field`, `field_name`) VALUES
				($campaign_id,'c1','Product'),
				($campaign_id,'d1','Renewal Date'),
				($campaign_id,'n1','Premium'),
				($campaign_id,'c2','Notes'),
				($campaign_id,'c3','Other')
				");

                if ($this->db->_error_message()) {
                    return "campaigns";
                }
                $this->firephp->log("inserting outcomes_to_campaigns");
                //Dump the outcomes to each campaign
                foreach ($this->db->get('outcomes')->result() as $outcome) {

                    $this->db->query("INSERT INTO `outcomes_to_campaigns` (`outcome_id`, `campaign_id`) VALUES
					(" . $outcome->outcome_id . ", " . $campaign_id . ")");

                    if ($this->db->_error_message()) {
                        return "outcomes_to_campaigns";
                    }
                }
            }
        }
        $this->firephp->log("inserting campaigns_to_features");
        //Dumping sample into campaign features
        foreach ($this->db->get('campaigns')->result() as $campaign) {
            $where = "";
            if ($campaign->campaign_type_id == "1") {
                $where .= ' and feature_name <> "Company"';
            }
            //$this->db->where('feature_name not in ("Surveys","Emails")' . $where);
            $this->db->where('feature_name not in ("")' . $where);
            foreach ($this->db->get('campaign_features')->result() as $campaign_feature) {
                $this->db->query("INSERT INTO `campaigns_to_features` (`campaign_id`, `feature_id`) VALUES (" . $campaign->campaign_id . ", " . $campaign_feature->feature_id . ")");

                if ($this->db->_error_message()) {
                    return "campaigns_to_features";
                }
            }
        }
        $this->firephp->log("inserting data_sources");
        //Dumpingdata for table `data_sources`
        $this->db->query("INSERT INTO `data_sources` (`source_name`, `cost_per_record`) VALUES
		('Source 1', NULL),
		('Source 2', NULL)");

        if ($this->db->_error_message()) {
            return "data_sources";
        }
        $this->firephp->log("inserting teams");
        //Dumpingdata for table `teams`
        for ($i = 1; $i <= 7; $i++) {
            $this->db->query("INSERT INTO `teams` (`team_name`) VALUES ('Team " . $i . "')");
            if ($this->db->_error_message()) {
                return "teams";
            }
        }

        $campaignList = $this->db->get('campaigns')->result();
        $outcomeList = $this->db->get('outcomes')->result();
        $teamsList = $this->db->get('teams')->result();
        $sourcesList = $this->db->get('data_sources')->result();
        $appointmentTypeList = $this->db->get('appointment_types')->result();

        $datestring = "Y-m-d H:i:s";


        $agentRole = $this->db->get_where('user_roles', array('role_name' => 'Agent'))->result();
        $groupList = $this->db->get('user_groups')->result();

        $names = array('Jennifer', 'Martha', 'Nicholas', 'AshleyHernandez', 'AlbertSimmons', 'Thomas', 'Janice', 'Stephen', 'Sharon', 'Nicholas', 'Philip', 'Robin', 'Tina', 'Harry', 'Annie', 'Jonathan', 'Jimmy', 'Janet', 'Brenda', 'Walter', 'Earl', 'Ronald', 'Rose', 'Jennifer', 'Linda', 'Margaret', 'Joshua', 'Phillip', 'Martin', 'Joseph', 'Frances', 'Jane', 'Bonnie', 'Cynthia', 'Maria', 'Susan', 'Gregory', 'Katherine', 'Keith', 'Cheryl', 'Sandra', 'Robin', 'Daniel', 'Melissa', 'David', 'Albert', 'Ruth', 'Edward', 'Christine', 'Lawrence', 'Peter', 'Katherine', 'Samuel', 'Michael', 'Cheryl', 'Henry', 'Earl', 'Russell', 'Beverly', 'Roy', 'Betty', 'Elizabeth', 'Alice', 'William', 'Chris', 'Wanda', 'Susan', 'Brian', 'Daniel', 'Kelly', 'Jessica', 'Alan', 'Gary', 'Jerry');

        $surnames = array('Lynch', 'Frazier', 'Jordan', 'Hernandez', 'Simmons', 'Gordon', 'Butler', 'Carpenter', 'Hawkins', 'Clark', 'Hunt', 'Russell', 'Ford', 'Parker', 'Hernandez', 'Meyer', 'Howard', 'Cole', 'Hall', 'Stone', 'Mills', 'Ward', 'Foster', 'Foster', 'Pierce', 'Thompson', 'Evans', 'Carpenter', 'Davis', 'Brown', 'Ramos', 'Carr', 'Wilson', 'Lynch', 'Gomez', 'Stone', 'Ellis', 'Harris', 'Matthews', 'Jones', 'Bishop', 'Andrews', 'Hamilton', 'Parker', 'Willis', 'Wheeler', 'Welch', 'Watson', 'Hamilton', 'Mason', 'Bell', 'Fuller', 'Hudson', 'Burton', 'Medina', 'Peterson', 'Hall', 'Rivera', 'Fernandez', 'Walker', 'Ferguson', 'Romero', 'Gordon', 'Sanders', 'Carpenter', 'Simpson', 'Oliver', 'Perry', 'Martin', 'Murray', 'Edwards', 'Dean', 'Andrews', 'Ferguson');

        $addresses = array('36 Hauk Road', '658 Loeprich Court', '55717 Warrior Road', '6 Continental Pass', '8993 Garrison Junction', '64 Marcy Alley', '9 Troy Alley', '8176 Kropf Center', '5652 Orin Center', '1 Redwing Parkway', '013 6th Court', '92274 Twin Pines Hill', '26 Vermont Avenue', '2330 Oak Valley Center', '290 Elgar Alley', '4248 Service Terrace', '0688 Tomscot Road', '7 Spenser Center', '9059 Cottonwood Crossing', '183 Valley Edge Way', '70 Bobwhite Pass', '984 Michigan Park', '87 Londonderry Point', '97 Doe Crossing Trail', '611 Tomscot Alley', '3 Truax Center', '13690 Valley Edge Trail', '41 Larry Lane', '66617 Crownhardt Way', '48 Debs Street', '97438 Ridge Oak Place', '86163 Randy Point', '13 Kropf Road', '9 Crescent Oaks Avenue', '9 Springs Road', '40 Dryden Trail', '2 Oneill Plaza', '6751 Corben Circle', '32434 Elmside Terrace', '06226 Farwell Center', '50 Thompson Junction', '43399 East Park', '383 Leroy Point', '8 Scoville Junction', '3 Katie Drive', '26 Tomscot Pass', '3 Forest Park', '443 Eagle Crest Center', '2 Main Center', '6 Sutteridge Way', '20787 Lyons Parkway', '5781 Golf Course Terrace', '1 Dwight Crossing', '426 Trailsway Park', '73420 Merchant Pass', '0322 Union Trail', '6714 Eagle Crest Hill', '9045 Park Meadow Junction', '9568 Rusk Alley', '5278 Londonderry Point', '71 Brickson Park Pass', '7724 Norway Maple Avenue', '8 Hagan Way', '3 Rutledge Trail', '769 Colorado Center', '35 Carey Road', '907 Brown Park', '06 Duke Plaza', '0290 Hudson Alley', '62402 Elka Hill', '765 Express Street', '51420 Center Lane', '2 Brickson Park Court', '6204 Nancy Avenue', '15798 Haas Alley', '89360 Fieldstone Center', '46 Pleasure Road', '3288 Dahle Terrace', '6 High Crossing Crossing', '76 Namekagon Alley', '354 Kingsford Circle', '7823 Pearson Park', '598 Muir Terrace', '9546 Summit Terrace', '68 Gateway Road', '8 Dorton Alley', '27 Thompson Avenue', '4848 Anderson Point', '6 Hooker Center', '74491 Monument Pass', '47513 Judy Park', '05 Village Green Circle', '763 Elka Avenue', '70868 Wayridge Parkway', '012 Farmco Circle', '0564 Lakewood Pass', '5 Fallview Street', '07867 Old Gate Way', '45 Tennessee Junction', '54317 6th Circle', '99 Schmedeman Lane', '0089 2nd Hill', '77854 Blackbird Point', '88770 Forest Dale Circle', '8 Green Alley', '5469 Armistice Lane', '54471 Truax Alley', '2 Kipling Parkway', '519 Manitowish Lane', '8 Dapin Road', '87 Cambridge Junction', '08498 Kingsford Avenue', '087 Sage Hill', '46 Charing Cross Circle', '80 Pleasure Terrace', '25 Forest Drive', '0120 Canary Parkway', '430 Utah Place', '0 Helena Alley', '168 Cambridge Alley', '1 Clove Road', '38 Clyde Gallagher Junction', '34165 Coolidge Point', '1 Columbus Lane', '69890 Esch Street', '4956 Quincy Crossing', '0747 Mifflin Point', '2 Linden Plaza', '216 Barnett Street', '5261 Nancy Junction', '140 Carey Way', '009 Fairfield Plaza', '9 Waywood Alley', '9060 Graedel Pass', '973 Westport Pass', '31 Drewry Circle', '2 Rigney Center', '04 Meadow Valley Point', '40582 La Follette Terrace', '83 Mayfield Place', '615 Manitowish Place', '6 Kedzie Plaza', '022 Canary Circle', '35 Badeau Pass', '42504 Little Fleur Center', '6 Drewry Parkway', '972 Northport Pass', '89 Jay Drive', '9 Fulton Lane', '94488 Independence Crossing', '6 Carpenter Parkway', '9225 Walton Drive', '56 American Ash Center', '4 Bluestem Junction', '02348 Pawling Crossing', '66296 Ronald Regan Lane', '2774 Forest Run Court', '5676 Cody Drive', '6 Oak Way', '7 Buhler Park', '08 Johnson Circle', '04148 Meadow Valley Alley', '78 Kipling Hill', '4 Brown Pass', '50 Stuart Lane', '94521 Sundown Place', '5078 Scott Center', '85 Delladonna Pass', '0 Armistice Avenue', '7 Fulton Court', '17 Butternut Plaza', '49313 Coolidge Junction', '8 Orin Way', '3 Homewood Point', '435 Maryland Road', '046 Haas Alley', '5757 Meadow Valley Lane', '71362 Gateway Terrace', '67395 Leroy Court', '06456 Marquette Parkway', '5 Cambridge Lane', '2 Johnson Parkway', '5110 Moulton Road', '5 Pankratz Place', '4548 Packers Place', '232 Onsgard Plaza', '62628 Graedel Road', '854 Rockefeller Center', '1441 Iowa Alley', '1 Ludington Pass', '416 Welch Crossing', '755 Summit Point', '7486 John Wall Junction', '54 Hermina Circle', '915 Kinsman Lane', '4 Browning Road', '39815 Superior Trail', '3834 Nova Avenue', '59 Cardinal Pass', '9 Memorial Hill', '2 Rowland Lane', '2799 Lighthouse Bay Trail', '6 Norway Maple Hill', '9 Ohio Hill', '443 Kipling Pass', '1 Sachs Alley', '65045 Meadow Vale Crossing', '42521 Daystar Place', '1 Bashford Pass', '9 Hintze Court', '2 Fulton Hill', '2 Crest Line Avenue', '31921 Loomis Point', '8601 Lighthouse Bay Place', '11 Farwell Avenue', '3 Lien Drive', '48 Memorial Point', '48 Arapahoe Crossing', '6825 Johnson Trail', '939 Cordelia Plaza', '468 Pepper Wood Point', '779 Vahlen Point', '1 Canary Crossing', '91 Loomis Parkway', '468 High Crossing Road', '1621 Goodland Point', '45 Dakota Junction', '76 Riverside Pass', '5 Meadow Vale Alley', '2706 Roxbury Crossing', '50166 Lindbergh Junction', '78 Longview Way', '8 Rusk Avenue', '01521 Dayton Street', '06 Green Terrace', '292 Armistice Circle', '7928 Moulton Trail', '5 Fordem Avenue', '18 Mandrake Road', '377 American Ash Way', '81406 Twin Pines Alley', '776 Kensington Crossing', '19138 Hermina Crossing', '69 Merry Hill', '5667 Rutledge Center', '5 1st Center', '703 Menomonie Court', '670 Jenna Place', '50732 Stang Court', '66461 American Ash Center', '8 Maryland Street', '24477 Golden Leaf Court', '2 Continental Point', '57475 Warrior Alley', '901 Dayton Avenue', '0 Cambridge Court', '11 Manitowish Junction', '54115 Muir Way', '65 Delladonna Park', '99 Harbort Circle', '74 Forster Parkway', '33767 Dovetail Crossing', '8793 Hanson Plaza', '00467 Marquette Way', '13 Autumn Leaf Lane', '50 Roth Street', '0604 Russell Center', '89065 Manley Junction', '33 Hazelcrest Road', '7651 5th Center', '59375 Katie Road', '8407 Mayer Lane', '65675 Pond Pass', '4 Fallview Parkway', '7 Mayfield Drive', '936 Clarendon Lane', '43443 Mockingbird Road', '52455 Blue Bill Park Plaza', '6 Bay Crossing', '59934 Welch Crossing', '63 School Crossing', '3 7th Center', '76769 Merry Lane', '7991 Truax Street', '61369 Springview Lane', '1 Farwell Pass', '0742 Sycamore Avenue', '3794 Florence Road', '021 Almo Drive', '158 Ramsey Plaza', '33 Marquette Way', '625 Sage Street', '1376 Melody Place', '12 Melby Center', '42 Brentwood Plaza', '7997 Hintze Park', '89 Hoffman Plaza', '920 Magdeline Drive', '24193 Westend Road', '5488 Graceland Road', '569 Beilfuss Lane', '8 Linden Drive', '864 Sutteridge Plaza', '4 Garrison Avenue', '90753 Tennessee Court', '35076 Canary Junction', '3 Tomscot Center', '85 Londonderry Center', '22 Lakewood Lane', '83 Florence Hill', '34 Charing Cross Terrace', '3521 Garrison Street', '3 Tomscot Hill', '3706 Vermont Circle', '46181 Cambridge Point', '1723 Lunder Place', '2621 Schiller Pass', '64 Grover Terrace', '5 Prairieview Park', '510 Grayhawk Avenue', '415 Roxbury Plaza', '14 Sutherland Crossing', '67 Hudson Circle', '00023 Hansons Junction', '096 Summerview Way', '2581 Wayridge Court', '9 Hagan Trail', '38 Loeprich Pass', '4388 4th Junction', '63388 Northridge Alley', '8365 Bowman Point', '858 Lakewood Pass', '08 Straubel Lane', '525 Blaine Center', '827 Charing Cross Avenue', '5 8th Court', '06 Sommers Way', '704 Canary Place', '12 Laurel Circle', '31 Kennedy Trail', '53250 Lawn Street', '7 Paget Avenue', '169 Montana Alley', '1542 Mandrake Avenue', '262 Redwing Terrace', '682 International Drive', '561 John Wall Lane', '8 Canary Lane', '3374 Morrow Lane');

//        $telephones = array('9-(691)168-4061', '5-(759)428-2283', '0-(201)575-1430', '1-(278)562-1339', '1-(814)772-8745', '8-(379)624-7002', '2-(305)960-1522', '9-(510)288-5526', '5-(694)311-0299', '4-(703)747-4251', '0-(738)024-3464', '2-(545)437-5155', '3-(327)222-2593', '7-(060)503-9750', '8-(006)500-6240', '6-(408)874-9185', '2-(516)171-2958', '0-(386)020-1315', '7-(983)325-2492', '6-(323)245-9282', '9-(338)164-3508', '1-(157)506-7874', '8-(295)266-8906', '7-(976)302-9646', '0-(178)616-9109', '9-(184)631-8090', '5-(094)471-2200', '9-(386)287-1181', '2-(483)612-9202', '9-(423)967-4558', '3-(855)684-1652', '7-(454)640-9920', '5-(034)850-8081', '8-(398)700-4420', '8-(949)809-4503', '9-(051)158-4214', '5-(102)393-6615', '1-(292)187-8664', '0-(951)713-3266', '4-(693)829-0357', '2-(851)964-4264', '3-(693)953-4706', '5-(606)954-9703', '1-(045)373-0457', '1-(743)313-6556', '9-(026)674-9072', '5-(677)773-7700', '3-(935)093-9315', '0-(583)394-9016', '5-(782)067-6271', '1-(982)640-8874', '2-(245)222-8698', '1-(504)804-2850', '5-(075)319-4915', '7-(642)062-2946', '8-(870)061-7964', '3-(591)121-4535', '7-(290)117-1021', '3-(225)406-0536', '1-(570)607-8943', '4-(858)157-9655', '4-(271)581-3103', '5-(989)947-3039', '9-(175)719-9279', '4-(158)926-1624', '9-(058)602-4184', '9-(603)513-8818', '8-(899)364-1933', '3-(849)365-1265', '7-(933)176-9448', '0-(123)226-7516', '9-(801)001-8206', '5-(945)136-5242', '8-(585)117-8729', '0-(861)797-8057', '0-(283)864-1143', '4-(001)449-9017', '0-(960)320-9642', '9-(825)043-8284', '4-(876)599-4223', '1-(954)004-0552', '9-(974)750-9529', '1-(749)257-5937', '5-(140)591-3704', '0-(347)076-5879', '8-(937)837-6376', '7-(318)430-8369', '7-(385)429-2814', '2-(056)243-8303', '5-(251)602-8425', '9-(002)150-8751', '7-(975)389-4198', '0-(424)228-6241', '1-(616)637-8297', '8-(285)191-8168', '7-(188)522-2368', '1-(388)160-0610', '4-(218)256-3358', '7-(519)342-2271', '8-(906)654-6117', '9-(398)694-8741', '5-(927)129-6767', '5-(597)079-9022', '2-(746)269-8924', '4-(470)164-2351', '0-(894)713-7650', '4-(416)965-2181', '4-(109)331-3054', '1-(276)947-1842', '1-(508)421-2767', '1-(257)353-1979', '3-(855)835-9860', '0-(790)121-5119', '9-(229)423-1595', '9-(147)615-1533', '4-(312)318-2379', '6-(739)476-4879', '3-(420)521-7622', '7-(688)242-4382', '0-(157)728-6867', '8-(089)499-2728', '6-(265)705-8969', '7-(273)346-2732', '6-(245)846-9662', '5-(453)583-0740', '0-(504)537-0002', '4-(146)788-8947', '8-(346)125-3857', '7-(562)508-0571', '0-(615)676-7736', '5-(425)184-2954', '8-(511)945-1584', '1-(564)655-3075', '8-(118)077-1467', '9-(358)579-5117', '5-(067)275-3400', '0-(815)593-0117', '9-(148)538-8967', '9-(757)007-6937', '3-(992)341-7657', '5-(254)572-4677', '8-(341)082-0004', '1-(826)177-4832', '2-(541)874-1092', '4-(355)243-1221', '6-(337)787-8712', '6-(091)928-5163', '8-(776)909-1982', '9-(679)521-9413', '9-(103)258-1175', '1-(412)084-6069', '7-(949)867-9555', '7-(458)364-3717', '4-(581)577-2567', '7-(529)409-1296', '9-(206)191-2714', '2-(299)276-1062', '8-(701)194-4303', '6-(117)776-9074', '8-(914)158-7198', '5-(627)731-3866', '7-(216)161-0990', '4-(334)851-8045', '5-(346)349-7562', '3-(761)201-2050', '8-(295)541-7422', '0-(044)648-2500', '8-(894)134-8106', '7-(976)780-6077', '7-(271)858-3838', '6-(389)408-8756', '2-(235)435-1209', '5-(406)581-3244', '6-(305)718-2534', '1-(853)125-5626', '1-(407)091-0487', '6-(711)148-4168', '6-(126)812-1590', '1-(430)890-3312', '3-(074)109-4148', '0-(277)087-9437', '7-(505)276-2135', '7-(220)410-6409', '8-(085)151-3404', '5-(734)276-4620', '5-(250)392-2108', '5-(913)493-4739', '4-(083)127-3099', '6-(736)008-9927', '7-(840)293-0118', '9-(321)782-9759', '1-(495)839-7619', '3-(439)467-4925', '3-(781)678-3333', '4-(048)645-0469', '6-(318)087-7463', '4-(225)673-5033', '6-(894)672-5693', '7-(701)518-7253', '0-(135)084-2496', '2-(021)602-8123', '5-(420)686-2738', '4-(631)316-0552', '1-(188)303-0712', '1-(508)873-7486', '8-(525)063-2548', '5-(123)843-0563', '9-(492)552-7005', '3-(981)334-9861', '0-(265)297-9590', '7-(450)079-3381', '4-(163)104-1621', '5-(445)098-7483', '1-(079)220-8540', '1-(726)194-1611', '9-(277)480-4858', '4-(708)538-2556', '4-(375)803-8665', '6-(126)663-9690', '8-(781)418-1251', '4-(676)961-8835', '3-(627)108-5100', '5-(808)506-8539', '3-(074)582-5916', '6-(920)832-3284', '5-(411)221-6653', '4-(501)844-1354', '9-(891)630-6218', '8-(625)911-3438', '0-(807)844-7205', '1-(523)960-3291', '1-(274)933-5887', '0-(170)678-3266', '1-(822)592-3003', '6-(111)262-6829', '6-(375)479-8114', '7-(301)694-0086', '0-(522)376-8618', '5-(598)936-3448', '2-(065)708-2624', '6-(951)370-5483', '9-(742)527-9860', '9-(855)173-9509', '0-(883)905-3715', '9-(155)344-1053', '2-(311)826-0492', '3-(781)777-3593', '2-(984)123-3938', '2-(155)821-1489', '0-(939)020-7961', '6-(994)555-2360', '5-(612)119-6770', '2-(399)851-2995', '7-(015)421-5085', '5-(094)876-0175', '6-(407)978-4175', '7-(059)589-9688', '0-(987)641-3861', '2-(613)641-8285', '3-(237)820-3949', '3-(654)073-9342', '8-(039)847-8259', '9-(959)293-6372', '5-(014)197-2858', '1-(304)274-2138', '1-(474)234-2751', '0-(418)368-8468', '8-(365)831-3022', '7-(260)396-9345', '6-(402)092-5150', '1-(459)166-6468', '0-(861)912-1863', '9-(813)659-5828', '4-(229)488-6911', '6-(396)505-5583', '5-(889)074-1500', '3-(867)598-1700', '0-(185)432-5360', '4-(767)921-8398', '6-(121)627-8871', '9-(348)601-4396', '4-(549)127-7036', '9-(048)620-2606', '8-(129)689-6064', '2-(934)090-1361', '7-(258)123-4499', '2-(048)222-8313', '6-(584)082-5685', '6-(863)106-7922', '3-(390)619-6943', '5-(387)002-7134', '4-(020)458-1624', '4-(498)758-7575', '6-(000)567-3707', '1-(893)852-1854', '6-(183)304-6890', '7-(339)078-9608', '2-(023)598-0042', '6-(773)421-8881', '0-(997)390-2467', '2-(095)087-9989', '9-(955)560-2509', '3-(403)431-8579', '5-(782)081-4258', '4-(929)915-1944', '9-(450)249-3484', '5-(311)797-7454', '8-(091)566-7828', '7-(725)579-3935', '3-(568)266-3856', '4-(066)569-0050', '7-(169)817-7008', '2-(297)770-5054', '8-(803)071-4563', '3-(651)382-6817', '7-(470)221-7546', '9-(417)380-1903', '9-(119)971-5484', '8-(319)054-2356', '7-(883)289-7467', '5-(462)676-3098', '9-(852)477-5344', '3-(469)649-3311', '3-(708)495-6803', '2-(874)931-8767', '3-(352)197-6141', '1-(604)892-6535', '1-(114)766-8486', '0-(754)016-5611', '0-(741)223-7345', '0-(425)112-4742', '5-(886)574-9366', '7-(924)180-9885', '2-(299)129-3199', '3-(166)950-7523', '7-(138)423-6132', '0-(333)052-7443', '7-(242)828-0596', '8-(154)630-7504', '8-(352)475-2909', '3-(843)121-1768', '7-(940)110-6754', '1-(117)800-9651', '2-(506)143-3801', '8-(492)638-6502', '1-(903)761-3991', '6-(627)102-8196', '8-(618)201-0438', '1-(611)381-1161');

        $telephones = array('01234567890');

        $postcodes = array(
            array('postcode' => 'WA3 3RR','location_id' => 1990359,'lng' => -2.60274,'lat' => 53.4777),
            array('postcode' => 'WA3 3GR','location_id' => 1990222,'lng' => -2.59717,'lat' => 53.4796),
            array('postcode' => 'WA3 2SJ','location_id' => 1990077,'lng' => -2.5844,'lat' => 53.4686),
            array('postcode' => 'WA2 7DD','location_id' => 1988853,'lng' => -2.59257,'lat' => 53.3963),
            array('postcode' => 'WA12 8NG','location_id' => 1982171,'lng' => -2.63287,'lat' => 53.4507),
            array('postcode' => 'WA11 9TG','location_id' => 1981763,'lng' => -2.64449,'lat' => 53.4726),
            array('postcode' => 'WA11 0AH','location_id' => 1980651,'lng' => -2.69664,'lat' => 53.4648),
            array('postcode' => 'WA10 4HA','location_id' => 1979889,'lng' => -2.75256,'lat' => 53.4531),
            array('postcode' => 'WA6 9BQ','location_id' => 1996513,'lng' => -2.76891,'lat' => 53.2665),
            array('postcode' => 'WA6 0QJ','location_id' => 1995698,'lng' => -2.75604,'lat' => 53.2784),
            array('postcode' => 'WA6 0DJ','location_id' => 1995563,'lng' => -2.77729,'lat' => 53.2658),
            array('postcode' => 'WA5 9YZ','location_id' => 1995463,'lng' => -2.61785,'lat' => 53.4057),
            array('postcode' => 'WA5 7ZR','location_id' => 1995215,'lng' => -2.62608,'lat' => 53.4157),
            array('postcode' => 'WA5 7YU','location_id' => 1995209,'lng' => -2.63027,'lat' => 53.4137),
            array('postcode' => 'WA5 7YH','location_id' => 1995203,'lng' => -2.6194,'lat' => 53.4163),
            array('postcode' => 'WA5 7WH','location_id' => 1995171,'lng' => -2.63128,'lat' => 53.4154),
            array('postcode' => 'WA5 7TN','location_id' => 1995156,'lng' => -2.61182,'lat' => 53.4149),
            array('postcode' => 'WA5 7FH','location_id' => 1995151,'lng' => -2.6315,'lat' => 53.4133),
            array('postcode' => 'WA5 3XA','location_id' => 1994598,'lng' => -2.65949,'lat' => 53.4069),
            array('postcode' => 'WA5 2UT','location_id' => 1994265,'lng' => -2.68793,'lat' => 53.373),
            array('postcode' => 'WA5 1TF','location_id' => 1993902,'lng' => -2.60802,'lat' => 53.3902),
            array('postcode' => 'WA5 1ST','location_id' => 1993892,'lng' => -2.60762,'lat' => 53.3916),
            array('postcode' => 'WA5 1DF','location_id' => 1993679,'lng' => -2.62121,'lat' => 53.3816),
            array('postcode' => 'WA4 6TT','location_id' => 1993175,'lng' => -2.62912,'lat' => 53.3622),
            array('postcode' => 'WA4 6QP','location_id' => 1993111,'lng' => -2.58562,'lat' => 53.3779),
            array('postcode' => 'WA4 6QD','location_id' => 1993103,'lng' => -2.58826,'lat' => 53.3817),
            array('postcode' => 'WA4 6ES','location_id' => 1992966,'lng' => -2.59637,'lat' => 53.3716),
            array('postcode' => 'WA4 6ED','location_id' => 1992955,'lng' => -2.59094,'lat' => 53.3711),
            array('postcode' => 'WA4 5JT','location_id' => 1992780,'lng' => -2.57149,'lat' => 53.3584),
            array('postcode' => 'WA4 4ST','location_id' => 1992608,'lng' => -2.53223,'lat' => 53.351),
            array('postcode' => 'WA4 4RG','location_id' => 1992579,'lng' => -2.52216,'lat' => 53.3499),
            array('postcode' => 'WA4 4QT','location_id' => 1992568,'lng' => -2.53241,'lat' => 53.3562),
            array('postcode' => 'WA4 4AR','location_id' => 1992380,'lng' => -2.62463,'lat' => 53.3405),
            array('postcode' => 'WA4 1PL','location_id' => 1991726,'lng' => -2.56774,'lat' => 53.3825),
            array('postcode' => 'WA4 1NN','location_id' => 1991707,'lng' => -2.54586,'lat' => 53.3821),
            array('postcode' => 'WA3 6HL','location_id' => 1991009,'lng' => -2.46483,'lat' => 53.4096),
            array('postcode' => 'WA3 6BE','location_id' => 1990906,'lng' => -2.53056,'lat' => 53.4392),
            array('postcode' => 'WA3 4HP','location_id' => 1990568,'lng' => -2.53543,'lat' => 53.4617),
            array('postcode' => 'WA3 1DU','location_id' => 1989707,'lng' => -2.54582,'lat' => 53.4755),
            array('postcode' => 'WA3 1AB','location_id' => 1989658,'lng' => -2.55541,'lat' => 53.4729),
            array('postcode' => 'WA2 8UD','location_id' => 1989372,'lng' => -2.6049,'lat' => 53.4207),
            array('postcode' => 'WA2 8QW','location_id' => 1989306,'lng' => -2.60758,'lat' => 53.4199),
            array('postcode' => 'WF6 1TN','location_id' => 2039078,'lng' => -1.3885,'lat' => 53.7059),
            array('postcode' => 'WA2 8HJ','location_id' => 1989200,'lng' => -2.59482,'lat' => 53.4025),
            array('postcode' => 'WA7 3EZ','location_id' => 1997463,'lng' => -2.70294,'lat' => 53.3087),
            array('postcode' => 'WA2 7PZ','location_id' => 1989004,'lng' => -2.59672,'lat' => 53.3988),
            array('postcode' => 'WA2 7NY','location_id' => 1988983,'lng' => -2.59502,'lat' => 53.3987),
            array('postcode' => 'WA2 7NE','location_id' => 1988968,'lng' => -2.59465,'lat' => 53.3968),
            array('postcode' => 'WA1 4RW','location_id' => 1978762,'lng' => -2.53503,'lat' => 53.408),
            array('postcode' => 'WA1 4RT','location_id' => 1978760,'lng' => -2.52592,'lat' => 53.4024),
            array('postcode' => 'WA1 4RQ','location_id' => 1978757,'lng' => -2.51894,'lat' => 53.3997),
            array('postcode' => 'WA1 4RJ','location_id' => 1978753,'lng' => -2.52266,'lat' => 53.4042),
            array('postcode' => 'WA1 4RA','location_id' => 1978746,'lng' => -2.53033,'lat' => 53.4086),
            array('postcode' => 'WA1 4GD','location_id' => 1978632,'lng' => -2.54927,'lat' => 53.4063),
            array('postcode' => 'WA1 4FE','location_id' => 1978619,'lng' => -2.54078,'lat' => 53.4081),
            array('postcode' => 'WA1 4AG','location_id' => 1978542,'lng' => -2.54355,'lat' => 53.3968),
            array('postcode' => 'WA1 4AE','location_id' => 1978540,'lng' => -2.54304,'lat' => 53.3984),
            array('postcode' => 'WA1 2HT','location_id' => 1978112,'lng' => -2.58044,'lat' => 53.3857),
            array('postcode' => 'WA1 2DZ','location_id' => 1978046,'lng' => -2.57832,'lat' => 53.389),
            array('postcode' => 'WA1 1QP','location_id' => 1977874,'lng' => -2.59582,'lat' => 53.39),
            array('postcode' => 'WA1 1QA','location_id' => 1977864,'lng' => -2.59547,'lat' => 53.3832),
            array('postcode' => 'WA1 1PJ','location_id' => 1977851,'lng' => -2.60433,'lat' => 53.3836),
            array('postcode' => 'WA6 8HQ','location_id' => 1996401,'lng' => -2.66036,'lat' => 53.2675),
            array('postcode' => 'WA5 7WG','location_id' => 1995170,'lng' => -2.63181,'lat' => 53.4169),
            array('postcode' => 'WA5 7UW','location_id' => 1995166,'lng' => -2.63446,'lat' => 53.4152),
            array('postcode' => 'WA5 4HX','location_id' => 1994695,'lng' => -2.65608,'lat' => 53.4323),
            array('postcode' => 'WA4 4TZ','location_id' => 1992625,'lng' => -2.52892,'lat' => 53.3497),
            array('postcode' => 'WA4 4TQ','location_id' => 1992623,'lng' => -2.52728,'lat' => 53.3497),
            array('postcode' => 'WA4 4LG','location_id' => 1992502,'lng' => -2.62021,'lat' => 53.2991),
            array('postcode' => 'WA4 2QU','location_id' => 1992082,'lng' => -2.54381,'lat' => 53.3771),
            array('postcode' => 'WA4 1HN','location_id' => 1991648,'lng' => -2.57137,'lat' => 53.3822),
            array('postcode' => 'WA3 6NJ','location_id' => 1991068,'lng' => -2.51731,'lat' => 53.428),
            array('postcode' => 'WA3 5NT','location_id' => 1990806,'lng' => -2.49608,'lat' => 53.4748),
            array('postcode' => 'WA3 3EL','location_id' => 1990195,'lng' => -2.58716,'lat' => 53.4808),
            array('postcode' => 'WA3 2BP','location_id' => 1989847,'lng' => -2.55718,'lat' => 53.4722),
            array('postcode' => 'WA2 8WA','location_id' => 1989378,'lng' => -2.60844,'lat' => 53.4284),
            array('postcode' => 'WA2 8UH','location_id' => 1989375,'lng' => -2.60019,'lat' => 53.4216),
            array('postcode' => 'WA2 7BT','location_id' => 1988845,'lng' => -2.5895,'lat' => 53.3948),
            array('postcode' => 'WA1 4AW','location_id' => 1978553,'lng' => -2.53795,'lat' => 53.3951),
            array('postcode' => 'WA1 3LR','location_id' => 1978416,'lng' => -2.55558,'lat' => 53.3969),
            array('postcode' => 'WA1 3AJ','location_id' => 1978277,'lng' => -2.58017,'lat' => 53.3939),
            array('postcode' => 'WA1 2DL','location_id' => 1978035,'lng' => -2.57187,'lat' => 53.391),
            array('postcode' => 'WA10 4HF','location_id' => 1979893,'lng' => -2.75336,'lat' => 53.4546),
            array('postcode' => 'WA10 2JL','location_id' => 1979338,'lng' => -2.74358,'lat' => 53.4567),
            array('postcode' => 'WA10 4RQ','location_id' => 1980017,'lng' => -2.76665,'lat' => 53.4594),
            array('postcode' => 'WA11 0RN','location_id' => 1980892,'lng' => -2.64761,'lat' => 53.4675),
            array('postcode' => 'WA10 5DP','location_id' => 1980080,'lng' => -2.7723,'lat' => 53.461),
            array('postcode' => 'WA10 1BQ','location_id' => 1978965,'lng' => -2.73867,'lat' => 53.4534),
            array('postcode' => 'WA2 8PA','location_id' => 1989270,'lng' => -2.57448,'lat' => 53.4056),
            array('postcode' => 'WA2 7XE','location_id' => 1989096,'lng' => -2.59021,'lat' => 53.3945),
            array('postcode' => 'WA11 7JU','location_id' => 1981131,'lng' => -2.79028,'lat' => 53.5185),
            array('postcode' => 'WA11 0AB','location_id' => 1980646,'lng' => -2.70168,'lat' => 53.4642),
            array('postcode' => 'WA3 7PB','location_id' => 1991440,'lng' => -2.53033,'lat' => 53.4304),
            array('postcode' => 'WA12 8BJ','location_id' => 1982048,'lng' => -2.61405,'lat' => 53.4538),
            array('postcode' => 'WA2 7HW','location_id' => 1988926,'lng' => -2.59113,'lat' => 53.3954),
            array('postcode' => 'WA3 7PG','location_id' => 1991444,'lng' => -2.52703,'lat' => 53.4141),
            array('postcode' => 'WA2 8NU','location_id' => 1989265,'lng' => -2.59572,'lat' => 53.4003),
            array('postcode' => 'WA2 7XA','location_id' => 1989093,'lng' => -2.59222,'lat' => 53.3942),
            array('postcode' => 'WA3 1EZ','location_id' => 1989731,'lng' => -2.55433,'lat' => 53.4741),
            array('postcode' => 'WA10 1BW','location_id' => 1978969,'lng' => -2.73651,'lat' => 53.4521),
            array('postcode' => 'WA2 0SU','location_id' => 1988716,'lng' => -2.5697,'lat' => 53.4223),
            array('postcode' => 'WA2 8HZ','location_id' => 1989211,'lng' => -2.59473,'lat' => 53.4057),
            array('postcode' => 'WA10 1AF','location_id' => 1978938,'lng' => -2.73497,'lat' => 53.4513),
            array('postcode' => 'WA3 7DE','location_id' => 1991310,'lng' => -2.55289,'lat' => 53.4398),
            array('postcode' => 'WA3 4AQ','location_id' => 1990474,'lng' => -2.53266,'lat' => 53.4476),
            array('postcode' => 'WA11 9UF','location_id' => 1981780,'lng' => -2.66794,'lat' => 53.4746),
            array('postcode' => 'WA12 9QU','location_id' => 1982485,'lng' => -2.6381,'lat' => 53.452),
            array('postcode' => 'WA10 6UU','location_id' => 1980478,'lng' => -2.74342,'lat' => 53.4666),
            array('postcode' => 'WA2 0SP','location_id' => 1988711,'lng' => -2.56519,'lat' => 53.4125),
            array('postcode' => 'WA12 8DU','location_id' => 1982073,'lng' => -2.61224,'lat' => 53.4437),
            array('postcode' => 'WA10 2TR','location_id' => 1979466,'lng' => -2.73935,'lat' => 53.4573),
            array('postcode' => 'WA11 0LF','location_id' => 1980807,'lng' => -2.65656,'lat' => 53.4693),
            array('postcode' => 'WA10 1BN','location_id' => 1978963,'lng' => -2.73673,'lat' => 53.452),
            array('postcode' => 'WA2 8JE','location_id' => 1989215,'lng' => -2.59544,'lat' => 53.4073),
            array('postcode' => 'WA2 8QP','location_id' => 1989300,'lng' => -2.60673,'lat' => 53.4214),
            array('postcode' => 'WA3 7NL','location_id' => 1991427,'lng' => -2.54105,'lat' => 53.4237),
            array('postcode' => 'WA3 3TA','location_id' => 1990387,'lng' => -2.60428,'lat' => 53.4816),
            array('postcode' => 'WA10 1HU','location_id' => 1979053,'lng' => -2.73467,'lat' => 53.4576),
            array('postcode' => 'WA10 1AU','location_id' => 1978949,'lng' => -2.73339,'lat' => 53.4492),
            array('postcode' => 'WA12 9YE','location_id' => 1982613,'lng' => -2.63938,'lat' => 53.4559),
            array('postcode' => 'WA2 8TW','location_id' => 1989366,'lng' => -2.5976,'lat' => 53.4088),
            array('postcode' => 'WA10 1RP','location_id' => 1979157,'lng' => -2.73561,'lat' => 53.4534),
            array('postcode' => 'WA10 1NW','location_id' => 1979108,'lng' => -2.73881,'lat' => 53.452),
            array('postcode' => 'WA3 5SW','location_id' => 1990870,'lng' => -2.51353,'lat' => 53.4527),
            array('postcode' => 'WA2 7PA','location_id' => 1988985,'lng' => -2.59587,'lat' => 53.398),
            array('postcode' => 'WA11 7AB','location_id' => 1981014,'lng' => -2.73572,'lat' => 53.4737),
            array('postcode' => 'WA10 3LF','location_id' => 1979606,'lng' => -2.74588,'lat' => 53.4499),
            array('postcode' => 'WA10 1TU','location_id' => 1979198,'lng' => -2.7344,'lat' => 53.4592),
            array('postcode' => 'WA12 9RB','location_id' => 1982491,'lng' => -2.63441,'lat' => 53.4523),
            array('postcode' => 'WA3 3NE','location_id' => 1990290,'lng' => -2.59511,'lat' => 53.4735),
            array('postcode' => 'WA10 1BU','location_id' => 1978968,'lng' => -2.73241,'lat' => 53.452),
            array('postcode' => 'WA2 7UW','location_id' => 1989078,'lng' => -2.59103,'lat' => 53.3938),
            array('postcode' => 'WA10 6SH','location_id' => 1980436,'lng' => -2.75502,'lat' => 53.4607),
            array('postcode' => 'WA2 8TL','location_id' => 1989358,'lng' => -2.60464,'lat' => 53.4159),
            array('postcode' => 'WA2 7LN','location_id' => 1988954,'lng' => -2.59858,'lat' => 53.3936),
            array('postcode' => 'WA12 9DJ','location_id' => 1982315,'lng' => -2.64296,'lat' => 53.4541),
            array('postcode' => 'WA10 2JN','location_id' => 1979339,'lng' => -2.74261,'lat' => 53.4561),
            array('postcode' => 'WA3 6QT','location_id' => 1991115,'lng' => -2.51637,'lat' => 53.425),
            array('postcode' => 'WA2 7TT','location_id' => 1989066,'lng' => -2.59338,'lat' => 53.3928),
            array('postcode' => 'WA2 8NT','location_id' => 1989264,'lng' => -2.5988,'lat' => 53.4018),
            array('postcode' => 'WA10 5AJ','location_id' => 1980038,'lng' => -2.78073,'lat' => 53.45),
            array('postcode' => 'WA11 9TE','location_id' => 1981761,'lng' => -2.66238,'lat' => 53.4798),
            array('postcode' => 'WA10 6JU','location_id' => 1980345,'lng' => -2.7541,'lat' => 53.467),
            array('postcode' => 'WA11 8NN','location_id' => 1981424,'lng' => -2.77328,'lat' => 53.5036),
            array('postcode' => 'WA2 8LG','location_id' => 1989235,'lng' => -2.59656,'lat' => 53.4311),
            array('postcode' => 'WA12 9AY','location_id' => 1982287,'lng' => -2.63871,'lat' => 53.4517),
            array('postcode' => 'WA3 5AW','location_id' => 1990676,'lng' => -2.49526,'lat' => 53.4457),
            array('postcode' => 'WA3 7AS','location_id' => 1991282,'lng' => -2.54588,'lat' => 53.4319),
            array('postcode' => 'WA11 9LU','location_id' => 1981653,'lng' => -2.72853,'lat' => 53.4697),
            array('postcode' => 'WA12 8EE','location_id' => 1982081,'lng' => -2.61243,'lat' => 53.4471),
            array('postcode' => 'WA10 2QF','location_id' => 1979407,'lng' => -2.74946,'lat' => 53.4581),
            array('postcode' => 'WA3 5AR','location_id' => 1990672,'lng' => -2.47583,'lat' => 53.4436),
            array('postcode' => 'WA11 0AE','location_id' => 1980648,'lng' => -2.70018,'lat' => 53.4638),
            array('postcode' => 'WA10 9FY','location_id' => 1980592,'lng' => -2.74192,'lat' => 53.4506),
            array('postcode' => 'WA2 7HH','location_id' => 1988920,'lng' => -2.58807,'lat' => 53.3974),
            array('postcode' => 'WA11 7QT','location_id' => 1981202,'lng' => -2.77425,'lat' => 53.4823),
            array('postcode' => 'WA11 7JR','location_id' => 1981129,'lng' => -2.81046,'lat' => 53.4987),
            array('postcode' => 'WA12 9SY','location_id' => 1982528,'lng' => -2.62009,'lat' => 53.4579),
            array('postcode' => 'WA3 6HH','location_id' => 1991007,'lng' => -2.4704,'lat' => 53.4145),
            array('postcode' => 'WA3 6LJ','location_id' => 1991048,'lng' => -2.45835,'lat' => 53.4156),
            array('postcode' => 'WA8 0SR','location_id' => 1998853,'lng' => -2.71718,'lat' => 53.3669),
            array('postcode' => 'WA3 6ZH','location_id' => 1991264,'lng' => -2.52713,'lat' => 53.4266),
            array('postcode' => 'WA10 3TT','location_id' => 1979735,'lng' => -2.74192,'lat' => 53.4506),
            array('postcode' => 'WA11 9UE','location_id' => 1981779,'lng' => -2.66392,'lat' => 53.477),
            array('postcode' => 'WA11 9XN','location_id' => 1981818,'lng' => -2.66595,'lat' => 53.4745),
            array('postcode' => 'WA3 5NZ','location_id' => 1990811,'lng' => -2.49956,'lat' => 53.4777),
            array('postcode' => 'WA11 8PU','location_id' => 1981448,'lng' => -2.78306,'lat' => 53.4926),
            array('postcode' => 'WA8 8UB','location_id' => 2000302,'lng' => -2.76544,'lat' => 53.3517),
            array('postcode' => 'WA11 0RW','location_id' => 1980899,'lng' => -2.64826,'lat' => 53.4674),
            array('postcode' => 'WA2 8JF','location_id' => 1989216,'lng' => -2.59541,'lat' => 53.4067),
            array('postcode' => 'WA8 0RR','location_id' => 1998833,'lng' => -2.71239,'lat' => 53.3663),
            array('postcode' => 'WA2 8RE','location_id' => 1989313,'lng' => -2.60035,'lat' => 53.4191),
            array('postcode' => 'WA2 8PR','location_id' => 1989282,'lng' => -2.59057,'lat' => 53.408),
            array('postcode' => 'WA3 6ZF','location_id' => 1991262,'lng' => -2.51438,'lat' => 53.4313),
            array('postcode' => 'WA11 9WG','location_id' => 1981801,'lng' => -2.64699,'lat' => 53.4739),
            array('postcode' => 'WA11 9SR','location_id' => 1981750,'lng' => -2.64292,'lat' => 53.4742),
            array('postcode' => 'WA8 0RE','location_id' => 1998824,'lng' => -2.71176,'lat' => 53.3645),
            array('postcode' => 'WA12 9XD','location_id' => 1982592,'lng' => -2.64997,'lat' => 53.4504),
            array('postcode' => 'WA8 0SW','location_id' => 1998857,'lng' => -2.71318,'lat' => 53.3638),
            array('postcode' => 'WA11 9TH','location_id' => 1981764,'lng' => -2.66393,'lat' => 53.4783),
            array('postcode' => 'WA8 8PT','location_id' => 2000215,'lng' => -2.77044,'lat' => 53.3632),
            array('postcode' => 'WA3 3PY','location_id' => 1990325,'lng' => -2.59678,'lat' => 53.4744),
            array('postcode' => 'WA3 3FN','location_id' => 1990215,'lng' => -2.59165,'lat' => 53.4686),
            array('postcode' => 'WA3 2AP','location_id' => 1989827,'lng' => -2.55589,'lat' => 53.4725),
            array('postcode' => 'WA3 6QU','location_id' => 1991116,'lng' => -2.51815,'lat' => 53.4301),
            array('postcode' => 'WA3 6JF','location_id' => 1991025,'lng' => -2.51525,'lat' => 53.4309),
            array('postcode' => 'WA2 8QY','location_id' => 1989308,'lng' => -2.60418,'lat' => 53.4206),
            array('postcode' => 'WA3 3AN','location_id' => 1990137,'lng' => -2.5968,'lat' => 53.4759),
            array('postcode' => 'WA3 7WF','location_id' => 1991492,'lng' => -2.53034,'lat' => 53.4314),
            array('postcode' => 'WA12 0HF','location_id' => 1981929,'lng' => -2.63128,'lat' => 53.4654),
            array('postcode' => 'WA11 9XE','location_id' => 1981812,'lng' => -2.6632,'lat' => 53.4772),
            array('postcode' => 'WA11 9TD','location_id' => 1981760,'lng' => -2.66066,'lat' => 53.4793),
            array('postcode' => 'WA11 9GA','location_id' => 1981597,'lng' => -2.65997,'lat' => 53.4784),
            array('postcode' => 'WA11 9XG','location_id' => 1981814,'lng' => -2.66203,'lat' => 53.4766),
            array('postcode' => 'WA8 0PH','location_id' => 1998787,'lng' => -2.75239,'lat' => 53.358),
            array('postcode' => 'WA8 0RJ','location_id' => 1998828,'lng' => -2.70631,'lat' => 53.3728),
            array('postcode' => 'WA10 1SN','location_id' => 1979175,'lng' => -2.73749,'lat' => 53.4544),
            array('postcode' => 'WA11 9UD','location_id' => 1981778,'lng' => -2.65688,'lat' => 53.4731),
            array('postcode' => 'WA2 8LT','location_id' => 1989244,'lng' => -2.60245,'lat' => 53.4185),
            array('postcode' => 'WA8 0NZ','location_id' => 1998780,'lng' => -2.74818,'lat' => 53.3591),
            array('postcode' => 'WA11 0JG','location_id' => 1980788,'lng' => -2.67938,'lat' => 53.4665),
            array('postcode' => 'WA3 6YN','location_id' => 1991249,'lng' => -2.52495,'lat' => 53.4274),
            array('postcode' => 'WA8 8FY','location_id' => 2000110,'lng' => -2.7814,'lat' => 53.3585),
            array('postcode' => 'WA10 3EG','location_id' => 1979546,'lng' => -2.75997,'lat' => 53.4413),
            array('postcode' => 'WA3 7QZ','location_id' => 1991474,'lng' => -2.53243,'lat' => 53.4123),
            array('postcode' => 'WA8 7UE','location_id' => 1999966,'lng' => -2.72622,'lat' => 53.3619),
            array('postcode' => 'WA2 0XP','location_id' => 1988791,'lng' => -2.5439,'lat' => 53.4147),
            array('postcode' => 'WA8 8LN','location_id' => 2000170,'lng' => -2.76854,'lat' => 53.3478),
            array('postcode' => 'WA3 6AX','location_id' => 1990900,'lng' => -2.52396,'lat' => 53.4317),
            array('postcode' => 'WA11 9UT','location_id' => 1981790,'lng' => -2.65922,'lat' => 53.4763),
            array('postcode' => 'WA3 6WU','location_id' => 1991215,'lng' => -2.5178,'lat' => 53.4249),
            array('postcode' => 'WA2 8JA','location_id' => 1989212,'lng' => -2.60067,'lat' => 53.4073),
            array('postcode' => 'WA8 8TL','location_id' => 2000289,'lng' => -2.76357,'lat' => 53.3599),
            array('postcode' => 'WA12 8DN','location_id' => 1982067,'lng' => -2.64386,'lat' => 53.4475),
            array('postcode' => 'WA3 6PL','location_id' => 1991089,'lng' => -2.51551,'lat' => 53.4282),
            array('postcode' => 'WA3 1AW','location_id' => 1989670,'lng' => -2.5541,'lat' => 53.4739),
            array('postcode' => 'WA8 0PP','location_id' => 1998791,'lng' => -2.75465,'lat' => 53.3578),
            array('postcode' => 'WA10 1AA','location_id' => 1978934,'lng' => -2.74192,'lat' => 53.4506),
            array('postcode' => 'WA11 0QU','location_id' => 1980878,'lng' => -2.64634,'lat' => 53.4715),
            array('postcode' => 'WA8 0TH','location_id' => 1998867,'lng' => -2.762,'lat' => 53.3587),
            array('postcode' => 'WA11 9XQ','location_id' => 1981820,'lng' => -2.66193,'lat' => 53.477),
            array('postcode' => 'WA8 0YZ','location_id' => 1998955,'lng' => -2.7056,'lat' => 53.367),
            array('postcode' => 'WA11 8LS','location_id' => 1981409,'lng' => -2.76876,'lat' => 53.4904),
            array('postcode' => 'WA8 8RE','location_id' => 2000244,'lng' => -2.77203,'lat' => 53.3635),
            array('postcode' => 'WA11 7PZ','location_id' => 1981187,'lng' => -2.75964,'lat' => 53.4785),
            array('postcode' => 'WA2 8QT','location_id' => 1989304,'lng' => -2.59975,'lat' => 53.4172),
            array('postcode' => 'WA8 0RD','location_id' => 1998823,'lng' => -2.73168,'lat' => 53.3633),
            array('postcode' => 'WA10 6FE','location_id' => 1980305,'lng' => -2.74192,'lat' => 53.4506),
            array('postcode' => 'WA11 9TF','location_id' => 1981762,'lng' => -2.64088,'lat' => 53.472),
            array('postcode' => 'WA11 9ST','location_id' => 1981752,'lng' => -2.64047,'lat' => 53.4735),
            array('postcode' => 'WA3 6BL','location_id' => 1990911,'lng' => -2.5178,'lat' => 53.4457),
            array('postcode' => 'WA8 0ZA','location_id' => 1998956,'lng' => -2.71667,'lat' => 53.3647),
            array('postcode' => 'WA2 7LP','location_id' => 1988955,'lng' => -2.59833,'lat' => 53.394),
            array('postcode' => 'WA8 0PG','location_id' => 1998786,'lng' => -2.74985,'lat' => 53.3591),
            array('postcode' => 'WA3 6PN','location_id' => 1991090,'lng' => -2.51544,'lat' => 53.4265),
            array('postcode' => 'WA11 9UY','location_id' => 1981794,'lng' => -2.66754,'lat' => 53.4749),
            array('postcode' => 'WA3 6DD','location_id' => 1990924,'lng' => -2.51494,'lat' => 53.4463),
            array('postcode' => 'WA11 9FT','location_id' => 1981596,'lng' => -2.66291,'lat' => 53.475),
            array('postcode' => 'WA8 8FZ','location_id' => 2000111,'lng' => -2.78108,'lat' => 53.3583),
            array('postcode' => 'WA3 3JD','location_id' => 1990249,'lng' => -2.59079,'lat' => 53.4696),
            array('postcode' => 'WA8 8UA','location_id' => 2000301,'lng' => -2.76925,'lat' => 53.359),
            array('postcode' => 'WA8 0SZ','location_id' => 1998860,'lng' => -2.73544,'lat' => 53.3554),
            array('postcode' => 'WA11 9SF','location_id' => 1981742,'lng' => -2.64338,'lat' => 53.4729),
            array('postcode' => 'WA2 8UF','location_id' => 1989374,'lng' => -2.60521,'lat' => 53.4169),
            array('postcode' => 'WA8 8XW','location_id' => 2000348,'lng' => -2.76955,'lat' => 53.3505),
            array('postcode' => 'WA8 0TG','location_id' => 1998866,'lng' => -2.73565,'lat' => 53.3548),
            array('postcode' => 'CV1 5QF','location_id' => 386252,'lng' => -1.50005,'lat' => 52.4104),
            array('postcode' => 'SM3 8BU','location_id' => 1637454,'lng' => -0.217554,'lat' => 51.3578),
            array('postcode' => 'SA1 1RH','location_id' => 2129016,'lng' => -3.93642,'lat' => 51.618),
            array('postcode' => 'LE11 3NP','location_id' => 895106,'lng' => -1.21037,'lat' => 52.7666),
            array('postcode' => 'BS21 7XN','location_id' => 228698,'lng' => -2.86057,'lat' => 51.4366),
            array('postcode' => 'CT10 2BL','location_id' => 367730,'lng' => 1.43211,'lat' => 51.3613),
            array('postcode' => 'UB5 6QD','location_id' => 1934201,'lng' => -0.39525,'lat' => 51.5372),
            array('postcode' => 'WS15 4UZ','location_id' => 2071577,'lng' => -1.87323,'lat' => 52.7392),
            array('postcode' => 'NE21 4HH','location_id' => 1111588,'lng' => -1.72839,'lat' => 54.9607),
            array('postcode' => 'BN27 2BY','location_id' => 186723,'lng' => 0.267639,'lat' => 50.8542),
            array('postcode' => 'G1 4AW','location_id' => 2129015,'lng' => -4.25611,'lat' => 55.8571),
            array('postcode' => 'TA1 1JR','location_id' => 1804490,'lng' => -3.10193,'lat' => 51.0169),
            array('postcode' => 'E7 0DL','location_id' => 566663,'lng' => 0.025038,'lat' => 51.5548),
            array('postcode' => 'DN2 5BW','location_id' => 502736,'lng' => -1.11789,'lat' => 53.5278),
            array('postcode' => 'EN8 8NJ','location_id' => 593483,'lng' => -0.034545,'lat' => 51.7001),
            array('postcode' => 'B98 9PA','location_id' => 70373,'lng' => -1.8924,'lat' => 52.3157),
            array('postcode' => 'MK41 8ZN','location_id' => 1066085,'lng' => -0.475586,'lat' => 52.1344),
            array('postcode' => 'L23 0RL','location_id' => 844838,'lng' => -3.02897,'lat' => 53.4831),
            array('postcode' => 'IG3 8BX','location_id' => 782182,'lng' => 0.09765,'lat' => 51.5638),
            array('postcode' => 'BT54 6AA','location_id' => 255178,'lng' => -6.24958,'lat' => 55.2004),
            array('postcode' => 'BT66 6JB','location_id' => 255484,'lng' => -6.33764,'lat' => 54.4655),
            array('postcode' => 'ST17 9AA','location_id' => 1743868,'lng' => -2.12458,'lat' => 52.8001),
            array('postcode' => 'BS36 2LZ','location_id' => 239630,'lng' => -2.4673,'lat' => 51.5262),
            array('postcode' => 'BN3 2EB','location_id' => 188099,'lng' => -0.171537,'lat' => 50.8276),
            array('postcode' => 'SA72 6BT','location_id' => 2129013,'lng' => -4.94055,'lat' => 51.6936),
            array('postcode' => 'PE7 1AE','location_id' => 1342850,'lng' => -0.127034,'lat' => 52.557),
            array('postcode' => 'LL29 7AA','location_id' => 924127,'lng' => -3.72887,'lat' => 53.2954),
            array('postcode' => 'MK2 2EH','location_id' => 1061593,'lng' => -0.72304,'lat' => 51.9962),
            array('postcode' => 'ST7 1LX','location_id' => 1755919,'lng' => -2.25892,'lat' => 53.0866),
            array('postcode' => 'CF82 7DU','location_id' => 2129012,'lng' => -3.23641,'lat' => 51.6409),
            array('postcode' => 'TA6 5AY','location_id' => 1815830,'lng' => -3.00074,'lat' => 51.1292),
            array('postcode' => 'NE8 4DP','location_id' => 1144225,'lng' => -1.60187,'lat' => 54.9564),
            array('postcode' => 'SA31 3AL','location_id' => 2129011,'lng' => -4.31211,'lat' => 51.8566),
            array('postcode' => 'ST1 1HR','location_id' => 1736468,'lng' => -2.17757,'lat' => 53.0239),
            array('postcode' => 'SK2 6QZ','location_id' => 1607677,'lng' => -2.15691,'lat' => 53.3977),
            array('postcode' => 'TS26 9EB','location_id' => 1901677,'lng' => -1.21618,'lat' => 54.6802),
            array('postcode' => 'BR1 1QQ','location_id' => 199199,'lng' => 0.016089,'lat' => 51.4074),
            array('postcode' => 'BT36 7UB','location_id' => 2129010,'lng' => -5.96055,'lat' => 54.6701),
            array('postcode' => 'CR7 7PA','location_id' => 362402,'lng' => -0.114673,'lat' => 51.3955),
            array('postcode' => 'ME14 4JG','location_id' => 1038824,'lng' => 0.571832,'lat' => 51.273),
            array('postcode' => 'ME10 1UD','location_id' => 1034320,'lng' => 0.716379,'lat' => 51.3466),
            array('postcode' => 'HU12 8EN','location_id' => 757302,'lng' => -0.200899,'lat' => 53.7397),
            array('postcode' => 'RM7 7PJ','location_id' => 1497607,'lng' => 0.173209,'lat' => 51.5834),
            array('postcode' => 'SM3 9QP','location_id' => 1637821,'lng' => -0.20885,'lat' => 51.3765),
            array('postcode' => 'SK11 8DL','location_id' => 1598862,'lng' => -2.13661,'lat' => 53.2598),
            array('postcode' => 'SE8 3NT','location_id' => 1576784,'lng' => -0.026606,'lat' => 51.4807),
            array('postcode' => 'CV34 4SL','location_id' => 399750,'lng' => -1.59095,'lat' => 52.2823),
            array('postcode' => 'B27 6DN','location_id' => 25727,'lng' => -1.82156,'lat' => 52.4478),
            array('postcode' => 'SR3 1AT','location_id' => 1713071,'lng' => -1.40111,'lat' => 54.8747),
            array('postcode' => 'B61 8LL','location_id' => 44532,'lng' => -2.06645,'lat' => 52.3411),
            array('postcode' => 'S74 9DU','location_id' => 1540102,'lng' => -1.4473,'lat' => 53.5005),
            array('postcode' => 'SM1 3EP','location_id' => 1635835,'lng' => -0.192319,'lat' => 51.3767),
            array('postcode' => 'BB5 3HS','location_id' => 101418,'lng' => -2.40015,'lat' => 53.7416),
            array('postcode' => 'CT2 8DP','location_id' => 376046,'lng' => 1.0656,'lat' => 51.2856),
            array('postcode' => 'BT30 6EH','location_id' => 253624,'lng' => -5.71644,'lat' => 54.3309),
            array('postcode' => 'DE23 6WE','location_id' => 448777,'lng' => -1.47868,'lat' => 52.9097),
            array('postcode' => 'DY1 1DU','location_id' => 529710,'lng' => -2.08676,'lat' => 52.5108),
            array('postcode' => 'BT40 1SX','location_id' => 254206,'lng' => -5.82348,'lat' => 54.8507),
            array('postcode' => 'BT8 6AW','location_id' => 2129009,'lng' => -5.92549,'lat' => 54.5487),
            array('postcode' => 'PR25 3ZJ','location_id' => 1405389,'lng' => -2.70077,'lat' => 53.7022),
            array('postcode' => 'NG24 2TN','location_id' => 1165841,'lng' => -0.791506,'lat' => 53.0776),
            array('postcode' => 'GU5 0RB','location_id' => 687457,'lng' => -0.523628,'lat' => 51.1917),
            array('postcode' => 'EH16 5QT','location_id' => 2129008,'lng' => -3.16631,'lat' => 55.9228),
            array('postcode' => 'PE13 1NJ','location_id' => 1314323,'lng' => 0.158433,'lat' => 52.666),
            array('postcode' => 'ST18 0WP','location_id' => 1744585,'lng' => -2.08737,'lat' => 52.8171),
            array('postcode' => 'BT34 1EJ','location_id' => 2129007,'lng' => -6.33275,'lat' => 54.1814),
            array('postcode' => 'NP18 1WY','location_id' => 2129006,'lng' => -2.94452,'lat' => 51.5721),
            array('postcode' => 'SE9 1TQ','location_id' => 1577668,'lng' => 0.055405,'lat' => 51.4511),
            array('postcode' => 'RG45 7AD','location_id' => 1454845,'lng' => -0.792192,'lat' => 51.3696),
            array('postcode' => 'SE25 4SL','location_id' => 1568184,'lng' => -0.065615,'lat' => 51.3906),
            array('postcode' => 'CM9 5EP','location_id' => 333496,'lng' => 0.680295,'lat' => 51.7313),
            array('postcode' => 'MK40 2QD','location_id' => 1064082,'lng' => -0.472905,'lat' => 52.1389),
            array('postcode' => 'CM17 0AP','location_id' => 317154,'lng' => 0.133083,'lat' => 51.784),
            array('postcode' => 'PA15 1YD','location_id' => 2129005,'lng' => -4.76309,'lat' => 55.952),
            array('postcode' => 'B13 8DD','location_id' => 14436,'lng' => -1.8884,'lat' => 52.4471),
            array('postcode' => 'BL1 8HF','location_id' => 154694,'lng' => -2.43085,'lat' => 53.5899),
            array('postcode' => 'BS3 5AY','location_id' => 234725,'lng' => -2.59426,'lat' => 51.4361),
            array('postcode' => 'MK40 3HZ','location_id' => 1064342,'lng' => -0.46454,'lat' => 52.1378),
            array('postcode' => 'GU34 1HH','location_id' => 683298,'lng' => -0.978041,'lat' => 51.1492),
            array('postcode' => 'RM15 5BE','location_id' => 1490452,'lng' => 0.296871,'lat' => 51.5235),
            array('postcode' => 'HP16 0AL','location_id' => 732989,'lng' => -0.70644,'lat' => 51.7028),
            array('postcode' => 'BT51 5QU','location_id' => 2129003,'lng' => -6.55407,'lat' => 54.9507),
            array('postcode' => 'BT9 6LL','location_id' => 2129002,'lng' => -5.95965,'lat' => 54.5718),
            array('postcode' => 'BT35 8SY','location_id' => 2129001,'lng' => -6.42441,'lat' => 54.0955),
            array('postcode' => 'BT2 8AA','location_id' => 253204,'lng' => -5.9293,'lat' => 54.5951),
            array('postcode' => 'DN36 5XE','location_id' => 512397,'lng' => 0.058063,'lat' => 53.5014),
            array('postcode' => 'BL7 9RA','location_id' => 162098,'lng' => -2.44229,'lat' => 53.6348),
            array('postcode' => 'DN1 3DJ','location_id' => 493921,'lng' => -1.13635,'lat' => 53.521),
            array('postcode' => 'BN3 1QQ','location_id' => 187894,'lng' => -0.149445,'lat' => 50.8326),
            array('postcode' => 'SS9 1AB','location_id' => 1734585,'lng' => 0.660313,'lat' => 51.5415),
            array('postcode' => 'NW4 1AP','location_id' => 1248127,'lng' => -0.224086,'lat' => 51.5952),
            array('postcode' => 'SA15 1UH','location_id' => 2129000,'lng' => -4.16249,'lat' => 51.6806),
            array('postcode' => 'SO23 9AP','location_id' => 1678414,'lng' => -1.31777,'lat' => 51.0634),
            array('postcode' => 'SS1 2FD','location_id' => 1721221,'lng' => 0.714104,'lat' => 51.5397),
            array('postcode' => 'N4 3RD','location_id' => 1096970,'lng' => -0.114021,'lat' => 51.5705),
            array('postcode' => 'HX1 2HZ','location_id' => 770147,'lng' => -1.85893,'lat' => 53.7195),
            array('postcode' => 'RG12 0XQ','location_id' => 1424050,'lng' => -0.728566,'lat' => 51.3981),
            array('postcode' => 'SE25 6DL','location_id' => 1568642,'lng' => -0.086772,'lat' => 51.4069),
            array('postcode' => 'RG4 9JT','location_id' => 1450701,'lng' => -0.980982,'lat' => 51.5108),
            array('postcode' => 'WF5 8AD','location_id' => 2038307,'lng' => -1.57946,'lat' => 53.679),
            array('postcode' => 'DA15 8PR','location_id' => 430715,'lng' => 0.100561,'lat' => 51.4512),
            array('postcode' => 'GY1 1AN','location_id' => 2128999,'lng' => -2.54273,'lat' => 49.4553),
            array('postcode' => 'E4 6AN','location_id' => 562325,'lng' => 0.009816,'lat' => 51.6339),
            array('postcode' => 'NR31 7BE','location_id' => 1224658,'lng' => 1.72605,'lat' => 52.5781),
            array('postcode' => 'B27 6BY','location_id' => 25717,'lng' => -1.81602,'lat' => 52.4454),
            array('postcode' => 'KY1 1LL','location_id' => 2128998,'lng' => -3.16047,'lat' => 56.1078),
            array('postcode' => 'HP6 5AE','location_id' => 745262,'lng' => -0.603451,'lat' => 51.6764),
            array('postcode' => 'B62 9LD','location_id' => 45534,'lng' => -2.03502,'lat' => 52.4714),
            array('postcode' => 'BB12 8BY','location_id' => 94654,'lng' => -2.31421,'lat' => 53.8018),
            array('postcode' => 'LU5 5BJ','location_id' => 972024,'lng' => -0.522671,'lat' => 51.9037),
            array('postcode' => 'CR2 6ED','location_id' => 355941,'lng' => -0.097607,'lat' => 51.3599),
            array('postcode' => 'SW19 1BD','location_id' => 1770930,'lng' => -0.186264,'lat' => 51.4161),
            array('postcode' => 'SO45 6AQ','location_id' => 1692314,'lng' => -1.39954,'lat' => 50.8691),
            array('postcode' => 'NE28 8QT','location_id' => 1118369,'lng' => -1.53438,'lat' => 54.9921),
            array('postcode' => 'LE1 6RX','location_id' => 892011,'lng' => -1.13084,'lat' => 52.6293),
            array('postcode' => 'GU22 0EU','location_id' => 675425,'lng' => -0.575399,'lat' => 51.3092),
            array('postcode' => 'WR15 8BB','location_id' => 2057189,'lng' => -2.59399,'lat' => 52.3116),
            array('postcode' => 'BN11 1JX','location_id' => 169006,'lng' => -0.36968,'lat' => 50.8182),
            array('postcode' => 'S63 9HJ','location_id' => 1531094,'lng' => -1.30106,'lat' => 53.5347),
            array('postcode' => 'DE45 1BX','location_id' => 454823,'lng' => -1.67543,'lat' => 53.2141),
            array('postcode' => 'NG2 7TH','location_id' => 1161767,'lng' => -1.15544,'lat' => 52.9192),
            array('postcode' => 'IG5 0LG','location_id' => 783237,'lng' => 0.051448,'lat' => 51.5913),
            array('postcode' => 'B16 8LA','location_id' => 17479,'lng' => -1.92438,'lat' => 52.4724),
            array('postcode' => 'DA14 5AE','location_id' => 429794,'lng' => 0.123253,'lat' => 51.4179),
            array('postcode' => 'TN2 9TT','location_id' => 2128997,'lng' => 0.307724,'lat' => 51.1425),
            array('postcode' => 'NW2 2JP','location_id' => 1243342,'lng' => -0.197396,'lat' => 51.5615),
            array('postcode' => 'BB3 2RG','location_id' => 98420,'lng' => -2.46572,'lat' => 53.6963),
            array('postcode' => 'RM1 1QX','location_id' => 1485237,'lng' => 0.184283,'lat' => 51.5726),
            array('postcode' => 'CO3 0RH','location_id' => 344081,'lng' => 0.837804,'lat' => 51.8855),
            array('postcode' => 'TR6 0BH','location_id' => 1884433,'lng' => -5.15204,'lat' => 50.3458),
            array('postcode' => 'DA17 5LJ','location_id' => 431919,'lng' => 0.144136,'lat' => 51.4818),
            array('postcode' => 'DL5 4NB','location_id' => 488954,'lng' => -1.57299,'lat' => 54.6219),
            array('postcode' => 'G82 1RB','location_id' => 2128995,'lng' => -4.5607,'lat' => 55.9437),
            array('postcode' => 'BD19 3JP','location_id' => 116134,'lng' => -1.71067,'lat' => 53.7252),
            array('postcode' => 'CO15 5EP','location_id' => 341441,'lng' => 1.18206,'lat' => 51.8025),
            array('postcode' => 'TA19 0AJ','location_id' => 1808178,'lng' => -2.90919,'lat' => 50.9267),
            array('postcode' => 'LL58 8NW','location_id' => 2128994,'lng' => -4.08723,'lat' => 53.2928),
            array('postcode' => 'SK1 3TH','location_id' => 1595809,'lng' => -2.15768,'lat' => 53.4041),
            array('postcode' => 'NE2 2HH','location_id' => 1110625,'lng' => -1.60033,'lat' => 54.9958),
            array('postcode' => 'SA11 1RN','location_id' => 2128993,'lng' => -3.80545,'lat' => 51.6639),
            array('postcode' => 'NR18 0AN','location_id' => 1214350,'lng' => 1.11353,'lat' => 52.5696),
            array('postcode' => 'NG5 2JN','location_id' => 1174160,'lng' => -1.14635,'lat' => 52.9818),
            array('postcode' => 'RG9 1AX','location_id' => 1460567,'lng' => -0.902183,'lat' => 51.5338),
            array('postcode' => 'LE67 2RD','location_id' => 916540,'lng' => -1.42362,'lat' => 52.6917),
            array('postcode' => 'CO10 9JB','location_id' => 337992,'lng' => 0.716757,'lat' => 52.0748),
            array('postcode' => 'TN9 1NP','location_id' => 1858389,'lng' => 0.276614,'lat' => 51.1987),
            array('postcode' => 'NG11 0HG','location_id' => 1149237,'lng' => -1.20421,'lat' => 52.8694),
            array('postcode' => 'G11 6NB','location_id' => 2128992,'lng' => -4.32792,'lat' => 55.872),
            array('postcode' => 'NE24 2BU','location_id' => 1114571,'lng' => -1.50629,'lat' => 55.1269),
            array('postcode' => 'DH3 3TJ','location_id' => 469536,'lng' => -1.57504,'lat' => 54.8601),
            array('postcode' => 'WD23 1SH','location_id' => 2017145,'lng' => -0.342818,'lat' => 51.6345),
            array('postcode' => 'SG18 0AP','location_id' => 1585408,'lng' => -0.267044,'lat' => 52.0891),
            array('postcode' => 'SA71 4NP','location_id' => 2128991,'lng' => -4.91647,'lat' => 51.676),
            array('postcode' => 'SN15 1HS','location_id' => 1648476,'lng' => -2.11748,'lat' => 51.4631),
            array('postcode' => 'EX23 8BU','location_id' => 608699,'lng' => -4.54577,'lat' => 50.8317),
            array('postcode' => 'BN11 1HU','location_id' => 168984,'lng' => -0.37252,'lat' => 50.8165),
            array('postcode' => 'EX39 3HN','location_id' => 614900,'lng' => -4.23383,'lat' => 51.0101),
            array('postcode' => 'KT21 1AA','location_id' => 825751,'lng' => -0.298604,'lat' => 51.3101),
            array('postcode' => 'SP1 1TF','location_id' => 1700799,'lng' => -1.7961,'lat' => 51.0692),
            array('postcode' => 'BH12 4QT','location_id' => 133850,'lng' => -1.95246,'lat' => 50.7438),
            array('postcode' => 'LL55 1AT','location_id' => 924187,'lng' => -4.27427,'lat' => 53.1414),
            array('postcode' => 'HU1 3EN','location_id' => 755610,'lng' => -0.33921,'lat' => 53.7463),
            array('postcode' => 'BB9 9AG','location_id' => 107518,'lng' => -2.21188,'lat' => 53.8334),
            array('postcode' => 'PL25 4EJ','location_id' => 1356401,'lng' => -4.78117,'lat' => 50.3585),
            array('postcode' => 'PR2 2LE','location_id' => 1402797,'lng' => -2.72995,'lat' => 53.7704),
            array('postcode' => 'BB18 6UN','location_id' => 95663,'lng' => -2.14431,'lat' => 53.9162),
            array('postcode' => 'N3 2TN','location_id' => 1095512,'lng' => -0.194497,'lat' => 51.6),
            array('postcode' => 'EC3A 7PJ','location_id' => 577682,'lng' => -0.080709,'lat' => 51.5155),
            array('postcode' => 'HX5 9DB','location_id' => 775059,'lng' => -1.82917,'lat' => 53.6927),
            array('postcode' => 'FY3 9DA','location_id' => 625174,'lng' => -3.03568,'lat' => 53.815),
            array('postcode' => 'NG10 4ER','location_id' => 1148671,'lng' => -1.28368,'lat' => 52.9018),
            array('postcode' => 'BT74 6HQ','location_id' => 2128990,'lng' => -7.63163,'lat' => 54.3434),
            array('postcode' => 'PL15 9EN','location_id' => 1351404,'lng' => -4.35649,'lat' => 50.6373),
            array('postcode' => 'B15 1RP','location_id' => 16193,'lng' => -1.91977,'lat' => 52.4694),
            array('postcode' => 'DE55 7BH','location_id' => 457932,'lng' => -1.38671,'lat' => 53.0973),
            array('postcode' => 'RM18 7BS','location_id' => 1493033,'lng' => 0.35383,'lat' => 51.4631),
            array('postcode' => 'PL17 7DL','location_id' => 1351805,'lng' => -4.31437,'lat' => 50.503),
            array('postcode' => 'IG11 8PJ','location_id' => 781047,'lng' => 0.080563,'lat' => 51.5406),
            array('postcode' => 'DG1 1DF','location_id' => 2128989,'lng' => -3.61184,'lat' => 55.0706),
            array('postcode' => 'GU4 8PH','location_id' => 686174,'lng' => -0.541289,'lat' => 51.2365),
            array('postcode' => 'YO1 7LA','location_id' => 2092529,'lng' => -1.08073,'lat' => 53.9596),
            array('postcode' => 'BT4 3BA','location_id' => 254163,'lng' => -5.88627,'lat' => 54.5977),
            array('postcode' => 'BT20 5BD','location_id' => 253235,'lng' => -5.66375,'lat' => 54.6627),
            array('postcode' => 'NP22 3DH','location_id' => 2128988,'lng' => -3.24651,'lat' => 51.7742),
            array('postcode' => 'KT19 8DH','location_id' => 823156,'lng' => -0.265725,'lat' => 51.3335),
            array('postcode' => 'GU14 6ET','location_id' => 665689,'lng' => -0.746044,'lat' => 51.275),
            array('postcode' => 'DH1 4DA','location_id' => 467116,'lng' => -1.58514,'lat' => 54.7776),
            array('postcode' => 'LS23 6BN','location_id' => 952800,'lng' => -1.34855,'lat' => 53.9067),
            array('postcode' => 'SA31 1DD','location_id' => 2128987,'lng' => -4.3071,'lat' => 51.8586),
            array('postcode' => 'WD23 3DH','location_id' => 2017504,'lng' => -0.361903,'lat' => 51.6442),
            array('postcode' => 'KT6 7AL','location_id' => 831923,'lng' => -0.291927,'lat' => 51.384),
            array('postcode' => 'SK22 3EL','location_id' => 1608195,'lng' => -2.00115,'lat' => 53.365),
            array('postcode' => 'SK22 4AE','location_id' => 1608251,'lng' => -2.00184,'lat' => 53.3675),
            array('postcode' => 'BS15 1SL','location_id' => 216146,'lng' => -2.51023,'lat' => 51.4664),
            array('postcode' => 'WV6 8QS','location_id' => 2089146,'lng' => -2.16786,'lat' => 52.5979),
            array('postcode' => 'FY8 1TJ','location_id' => 630338,'lng' => -3.02517,'lat' => 53.7523),
            array('postcode' => 'CR7 7EQ','location_id' => 362303,'lng' => -0.104359,'lat' => 51.395),
            array('postcode' => 'DT4 7SS','location_id' => 525735,'lng' => -2.44989,'lat' => 50.62),
            array('postcode' => 'YO31 9BR','location_id' => 2112325,'lng' => -1.06975,'lat' => 53.9793),
            array('postcode' => 'E7 8DF','location_id' => 567007,'lng' => 0.034465,'lat' => 51.5472),
            array('postcode' => 'DL15 8NE','location_id' => 484405,'lng' => -1.74525,'lat' => 54.7143),
            array('postcode' => 'KY12 7DT','location_id' => 2128986,'lng' => -3.45876,'lat' => 56.0682),
            array('postcode' => 'DY7 6BX','location_id' => 540824,'lng' => -2.235,'lat' => 52.4548),
            array('postcode' => 'SS0 9PF','location_id' => 1720735,'lng' => 0.685281,'lat' => 51.5457),
            array('postcode' => 'BT78 1ES','location_id' => 255855,'lng' => -7.29868,'lat' => 54.6),
            array('postcode' => 'BT9 7EW','location_id' => 256159,'lng' => -5.95094,'lat' => 54.579),
            array('postcode' => 'EH6 5QB','location_id' => 2128985,'lng' => -3.18685,'lat' => 55.9708),
            array('postcode' => 'BL9 6SQ','location_id' => 164452,'lng' => -2.29435,'lat' => 53.6178),
            array('postcode' => 'RM7 0JR','location_id' => 1497073,'lng' => 0.175275,'lat' => 51.5652),
            array('postcode' => 'TQ7 1BT','location_id' => 1869441,'lng' => -3.77565,'lat' => 50.2853),
            array('postcode' => 'DH1 2HX','location_id' => 466571,'lng' => -1.54433,'lat' => 54.7806),
            array('postcode' => 'LE10 1DY','location_id' => 892969,'lng' => -1.36601,'lat' => 52.5412),
            array('postcode' => 'PL7 2HN','location_id' => 1365065,'lng' => -4.04945,'lat' => 50.387),
            array('postcode' => 'KY7 5DW','location_id' => 2128984,'lng' => -3.14984,'lat' => 56.1905),
            array('postcode' => 'LL29 8NE','location_id' => 2128983,'lng' => -3.71623,'lat' => 53.2789),
            array('postcode' => 'KY11 8NT','location_id' => 2128982,'lng' => -3.43832,'lat' => 56.0545),
            array('postcode' => 'DY10 1HE','location_id' => 530895,'lng' => -2.2528,'lat' => 52.3805),
            array('postcode' => 'CV32 4PE','location_id' => 397754,'lng' => -1.53168,'lat' => 52.2915),
            array('postcode' => 'TN13 1EB','location_id' => 1831317,'lng' => 0.189267,'lat' => 51.2702),
            array('postcode' => 'NW1 4BT','location_id' => 1235097,'lng' => -0.144581,'lat' => 51.5273),
            array('postcode' => 'GU15 1ED','location_id' => 667100,'lng' => -0.705041,'lat' => 51.3337),
            array('postcode' => 'BS5 8AA','location_id' => 246219,'lng' => -2.54867,'lat' => 51.4596),
            array('postcode' => 'WD23 3LN','location_id' => 2017568,'lng' => -0.358655,'lat' => 51.6433),
            array('postcode' => 'BT2 8AR','location_id' => 2128981,'lng' => -5.93029,'lat' => 54.5943),
            array('postcode' => 'SG18 0BL','location_id' => 1585426,'lng' => -0.268031,'lat' => 52.0923),
            array('postcode' => 'WD6 1SL','location_id' => 2022938,'lng' => -0.278923,'lat' => 51.6535),
            array('postcode' => 'SN1 1SQ','location_id' => 1641742,'lng' => -1.78287,'lat' => 51.559),
            array('postcode' => 'BN21 4UT','location_id' => 182061,'lng' => 0.28079,'lat' => 50.7664),
            array('postcode' => 'YO19 5UP','location_id' => 2100391,'lng' => -1.02775,'lat' => 53.9616),
            array('postcode' => 'RH19 1HA','location_id' => 1476878,'lng' => -0.014976,'lat' => 51.1291),
            array('postcode' => 'CR0 4JA','location_id' => 353345,'lng' => -0.108287,'lat' => 51.3733),
            array('postcode' => 'ME7 3ND','location_id' => 1050734,'lng' => 0.570538,'lat' => 51.3253),
            array('postcode' => 'CT20 2BN','location_id' => 376760,'lng' => 1.17307,'lat' => 51.0775),
            array('postcode' => 'DA12 2BD','location_id' => 427932,'lng' => 0.373663,'lat' => 51.4444),
            array('postcode' => 'CF23 6EE','location_id' => 2128980,'lng' => -3.17817,'lat' => 51.5188),
            array('postcode' => 'CV2 4ED','location_id' => 390928,'lng' => -1.48335,'lat' => 52.4105),
            array('postcode' => 'BN3 3RJ','location_id' => 188657,'lng' => -0.17451,'lat' => 50.8322),
            array('postcode' => 'WD19 4BS','location_id' => 2014093,'lng' => -0.382354,'lat' => 51.6467),
            array('postcode' => 'BH22 9JG','location_id' => 143024,'lng' => -1.88938,'lat' => 50.8044),
            array('postcode' => 'B76 1QN','location_id' => 57295,'lng' => -1.7999,'lat' => 52.5406),
            array('postcode' => 'GU12 4NY','location_id' => 663408,'lng' => -0.753436,'lat' => 51.2455),
            array('postcode' => 'CV6 7AN','location_id' => 408684,'lng' => -1.48771,'lat' => 52.4429),
            array('postcode' => 'NR32 1PW','location_id' => 1225607,'lng' => 1.75236,'lat' => 52.4786),
            array('postcode' => 'BH24 1BT','location_id' => 145338,'lng' => -1.79348,'lat' => 50.8462),
            array('postcode' => 'OX2 0JL','location_id' => 1288327,'lng' => -1.29523,'lat' => 51.7537),
            array('postcode' => 'PO21 1EL','location_id' => 1382369,'lng' => -0.673066,'lat' => 50.7853),
            array('postcode' => 'IV2 3TP','location_id' => 2128978,'lng' => -4.20166,'lat' => 57.4824),
            array('postcode' => 'BN11 1TT','location_id' => 169142,'lng' => -0.374265,'lat' => 50.8113),
            array('postcode' => 'BH16 6NL','location_id' => 137140,'lng' => -2.07449,'lat' => 50.72),
            array('postcode' => 'WF13 1QZ','location_id' => 2029931,'lng' => -1.62722,'lat' => 53.6939),
            array('postcode' => 'EN4 8SS','location_id' => 588528,'lng' => -0.164111,'lat' => 51.6431),
            array('postcode' => 'SS0 9HL','location_id' => 1720661,'lng' => 0.68172,'lat' => 51.5455),
            array('postcode' => 'SY3 5AL','location_id' => 1798885,'lng' => -2.79978,'lat' => 52.7151),
            array('postcode' => 'CV1 3EH','location_id' => 385617,'lng' => -1.51818,'lat' => 52.4048)
        );

        $locations = array();
        foreach($postcodes as $location) {
            unset($location['postcode']);
            array_push($locations, $location);
        }
        $this->db->insert_batch('locations', $locations);

        $this->firephp->log("inserting users");
        //Dumpingdata for table `users` except agents
        foreach ($this->db->get('user_roles')->result() as $role) {
            $group = $this->db->get_where('user_groups', array('group_name' => '121'))->result();

            if ($role->role_name != 'Agent') {
                $this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
				(" . $role->role_id . ", " . $group[0]->group_id . ", NULL, '" . strtolower(str_replace(' ', '.', $role->role_name)) . "', '32250170a0dca92d53ec9624f336ca24', '" . $role->role_name . "', 1, NULL, NULL, NULL, '2014-08-12 12:02:55', 111, NULL, NULL, 0, '2014-08-12 12:02:48')");

                if ($this->db->_error_message()) {
                    return "users";
                }
            }
        }

        //Add new agents for each team
        $i = 200;
        foreach ($this->db->get('teams')->result() as $team) {
            $group = $groupList[array_rand($groupList)];

            $this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
			(" . $agentRole[0]->role_id . ", " . $group->group_id . ", " . $team->team_id . ", 'agent" . $i . "', '32250170a0dca92d53ec9624f336ca24', 'Agent " . $i . "', 1, NULL, NULL, NULL, '2014-08-12 12:02:55', " . $i . ", NULL, NULL, 0, '2014-08-12 12:02:48')");

            if ($this->db->_error_message()) {
                return "users";
            }
            $i++;
        }
        $agentList = $this->db->get_where('users', array('role_id' => $agentRole[0]->role_id))->result();

        $this->firephp->log("inserting records");
        //Dumping the records
        for ($i = 1; $i <= 300; $i++) {

            $days = rand(0, 90);
            $time = time() - $days * 24 * 3600;
            $date = date($datestring, $time);

            $campaign = $campaignList[array_rand($campaignList)];
            $outcome = $outcomeList[array_rand($outcomeList)];
            $team = $teamsList[array_rand($teamsList)];
            $source = $sourcesList[array_rand($sourcesList)];

            $this->db->query("INSERT INTO `records` (`campaign_id`, `outcome_id`, `team_id`, `nextcall`, `dials`, `record_status`, `parked_code`, `progress_id`, `urgent`, `date_added`, `date_updated`, `reset_date`, `last_survey_id`, `source_id`) VALUES
					(" . $campaign->campaign_id . ", " . $outcome->outcome_id . ", " . $team->team_id . ", '" . $date . "', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, " . $source->source_id . ")");

            $this->db->query("insert into record_details set urn=$i,c1 = 'Insurance',d1='$date'");

            if ($this->db->_error_message()) {
                return "records";
            }
        }
        $this->firephp->log("inserting contacts and history");
        $this->firephp->log("inserting companies for B2B campaigns");
        //Dumping the contacts and the history
        $i = 0;
        foreach ($this->db->get('records')->result() as $record) {
            $name = $names[array_rand($names)];
            $surname = $surnames[array_rand($surnames)];
            //$address = $addresses[array_rand($addresses)];
            $campaign_type = $this->db->query('select campaign_type_desc from campaigns inner join campaign_types using (campaign_type_id) where campaign_id = '.$record->campaign_id)->result_array();
            $agent = $agentList[array_rand($agentList)];

            $add1 = ($i+1)." Street";
            $add2 = "Apartment ".rand(1, 700);
            $add3 = "House ".rand(1, 23);
            //$randStr = substr( "ABCDEFGHIJKLMNOPQRSTUVWXYZ" ,mt_rand(0 , 25), 2);
            //$postcode = substr( "MOL" ,mt_rand(0 , 2), 1).rand(1, 15)." ".rand(1, 5).$randStr;
            $postcode = $postcodes[array_rand($postcodes)];

            $days = rand(1, 90);
            $time = time() - $days * 24 * 3600;
            $time_after = time() - $days * 24 * 3660;
            $date = date($datestring, $time);
            $date_after = date($datestring, $time_after);

            //Contact
            $this->db->query("INSERT INTO `contacts` (`urn`, `fullname`, `title`, `firstname`, `lastname`, `position`, `dob`, `fax`, `email`, `email_optout`, `website`, `linkedin`, `facebook`, `notes`, `date_created`, `date_updated`, `sort`) VALUES
					(" . $record->urn . ", '" . $name . " " . $surname . "', NULL, '" . $name . "', '" . $surname . "', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '" . $date_after . "', NULL)");

            $contact_id = $this->db->insert_id();

            if ($this->db->_error_message()) {
                return "contacts";
            }

            //Address
            $this->db->query("INSERT INTO `contact_addresses` (`contact_id`, `add1`, `add2`, `add3`, `county`, `country`, `postcode`, `location_id`, `primary`) VALUES
					($contact_id, '" . $add1 . "', '" . $add2 . "', '" . $add3 . "', NULL, 'UK', '" . $postcode['postcode'] . "', " . $postcode['location_id'] . ", 1)");

            if ($this->db->_error_message()) {
                return "contact addresses";
            }


            //Telephone
            $this->db->query("INSERT INTO `contact_telephone` (`contact_id`, `telephone_number`, `description`, `tps`) VALUES
					($contact_id, '" . $telephones[0] . "', 'Telephone', NULL)");


            if ($this->db->_error_message()) {
                return "contact telephone numbers";
            }

            if ($campaign_type[0] = 'B2B') {
                //Company
                $this->db->query("INSERT INTO `companies` (`urn`, `name`, `description`, `conumber`, `turnover`, `employees`, `website`, `email`, `status`)                                    VALUES
                                (" . $record->urn . ", '" . substr( "ABCDEFGHIJKLMNOPQRSTUVWXYZ" ,mt_rand(0 , 25), 1)." Company ".$i . "', 'Description...', NULL, NULL, NULL, NULL, NULL, NULL)");

                $company_id = $this->db->insert_id();

                if ($this->db->_error_message()) {
                    return "companies";
                }

                //Address
                $this->db->query("INSERT INTO `company_addresses` (`company_id`, `add1`, `add2`, `add3`, `county`, `country`, `postcode`,`location_id`, `primary`) VALUES
					($company_id, '" . $add1 . "', '" . $add2 . "', '" . $add2 . "', NULL, 'UK', '" . $postcode['postcode'] . "', " . $postcode['location_id'] . ", 1)");

                if ($this->db->_error_message()) {
                    return "company addresses";
                }


                //Telephone
                $this->db->query("INSERT INTO `company_telephone` (`company_id`, `telephone_number`, `description`, `ctps`) VALUES
					($company_id, '" . $telephones[0] . "', 'Telephone', NULL)");


                if ($this->db->_error_message()) {
                    return "comapny telephone numbers";
                }
            }


            //History
            $this->db->query("INSERT INTO `history` (`campaign_id`, `urn`, `loaded`, `contact`, `description`, `outcome_id`, `comments`, `nextcall`, `user_id`, `role_id`, `group_id`, `contact_id`, `progress_id`, `last_survey`) VALUES
            		($record->campaign_id, " . $record->urn . ", NULL, '" . $date . "', 'Record was updated', " . $record->outcome_id . ", 'Comment', '" . $date . "', " . $agent->user_id . ", " . $agent->role_id . ", " . $agent->group_id . ", NULL, NULL, NULL)");
            //update history campaigns to match the record campaigns
            $this->db->query("update history left join records using(urn) set history.campaign_id = records.campaign_id");
            if ($this->db->_error_message()) {
                return "history";
            }


            //Appointments
            $days = rand(0, 90);
            $time = time() + $days * 24 * 3600;
            $date = date($datestring, $time);
            $appointment = array(
                'title' => 'Title '.$i,
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'start' => $date,
                'urn' => $record->urn,
                'postcode' => $postcode['postcode'],
                'location_id' => $postcode['location_id'],
                'appointment_type_id' => $appointmentTypeList[array_rand($appointmentTypeList)]->appointment_type_id
            );
            $this->db->insert('appointments', $appointment);

            $i++;
        }

        $this->db->query("update history set team_id = (select team_id from users where user_id = history.user_id)");

        //Dumping the cross_transfers
        $cross_transfer = $this->db->get_where('outcomes', array('outcome' => 'Cross Transfer'))->result();
        foreach ($this->db->get_where('history', array('outcome_id' => $cross_transfer[0]->outcome_id))->result() as $history) {

            $campaign = $campaignList[array_rand($campaignList)];
            while ($campaign->campaign_id == $history->campaign_id) {
                $campaign = $campaignList[array_rand($campaignList)];
            }

            $this->db->query("INSERT INTO `cross_transfers` (`history_id`, `campaign_id`) VALUES
					($history->history_id, $campaign->campaign_id)");

            if ($this->db->_error_message()) {
                return "cross_transfers";
            }
        }
        $this->firephp->log("inserting questions");
        //dumping sample questionnaires

        $this->db->query("INSERT INTO `questions` (`question_id`, `question_name`, `question_script`, `question_guide`, `other`, `question_cat_id`, `sort`, `nps_question`, `multiple`, `survey_info_id`) VALUES
        (1, 'Sign up process', 'How easy did you find the sign up process?', '10 Being very easy<br>10 Being very difficult', '', NULL, 1, NULL, NULL, 1),
        (2, 'Where did you hear about us?', 'Where did you hear about us?', '', NULL, NULL, 5, NULL, 0, 1),
        (3, 'Why did you choose us?', 'Why did you choose us?', '', NULL, NULL, 5, NULL, NULL, 1),
        (4, 'How appealing did you find the website?', '', '10 Being very appealing<br>\n1 Being not appealing at all', NULL, NULL, 5, NULL, NULL, 1),
        (5, 'How competitive are our prices?', 'How competitive are our prices?', '10 Being very competitive <br>\r\n1 Being not competitive at all', NULL, NULL, 5, NULL, NULL, 1),
        (6, 'NPS Score', 'How likely would you be to recommend our services to a friend?', '10 being very likely<br>\r\n1 being never', NULL, NULL, 30, 1, NULL, 1),
        (7, 'Current service', 'How do you rate the service you have recieved so far?', '10 being very good<br>\r\n1 being poor', NULL, NULL, 5, NULL, NULL, 2),
        (8, 'Previous issues', 'Have you ever experienced any issues with the service in the past?', '', NULL, NULL, 10, NULL, NULL, 2),
        (9, 'Current service', 'Do you have any current issues with the service?', '', NULL, NULL, 15, NULL, NULL, 2),
        (10, 'Future useage', 'How likely are you to continue using our service in the future?', '1 being certain\r\n10 being definately not', NULL, NULL, 20, NULL, NULL, 2),
        (11, 'NPS Score', 'How likely would you be to recommend our services to a friend?', '10 being very likely<br>1 being never', NULL, NULL, 50, 1, NULL, 2),
        (12, 'Service recieved', 'How would you rate the service you recieved?', '10 being fantastic<br>\r\n1 being poor', NULL, NULL, 5, NULL, NULL, 3),
        (13, 'Reason for stopping', 'What are the main reason you stopped using the service?', '', NULL, NULL, 17, NULL, NULL, 3),
        (14, 'Future usage', 'How likely is it you would use us again in the future?', '10 being definately<br>\r\n1 being unlikely', NULL, NULL, 20, NULL, NULL, 3),
        (15, 'Improving the service', 'Are there any areas you feel could improve?', '', NULL, NULL, 5, NULL, 1, 3),
        (16, 'NPS Score', 'How likely would you be to recommend our services to a friend?', '10 being very likely<br>1 being never', NULL, NULL, 5, 1, NULL, 3)");

        $this->firephp->log("inserting question_options");
        $this->db->query("INSERT INTO `question_options` (`option_id`, `option_name`, `question_id`) VALUES
        (1, 'TV', 2),
        (2, 'Radio', 2),
        (3, 'Internet', 2),
        (4, 'Magazine', 2),
        (5, 'Other', 2),
        (6, 'Didn''t shop around', 3),
        (7, 'Good prices', 3),
        (8, 'I don''t know', 3),
        (9, 'Other', 3),
        (10, 'Good reviews', 3),
        (11, 'Yes', 8),
        (12, 'No', 8),
        (13, 'Problem with service', 13),
        (14, 'Pricing', 13),
        (15, 'No longer needed', 13),
        (16, 'Not used enough', 13),
        (17, 'Found better alternative', 13)");

        $this->firephp->log("inserting surveys_to_campaigns");
        $this->db->query("INSERT INTO `surveys_to_campaigns` (`id`, `survey_info_id`, `campaign_id`, `default`) VALUES
        (1, 1, 1, NULL),
        (2, 1, 2, NULL),
        (3, 1, 3, NULL),
        (4, 2, 1, NULL),
        (5, 2, 2, NULL),
        (6, 2, 3, NULL),
        (7, 3, 1, NULL),
        (8, 3, 2, NULL),
        (9, 3, 3, NULL),
        (10, 3, 4, NULL),
        (11, 4, 1, NULL),
        (12, 4, 2, NULL),
        (13, 4, 3, NULL),
        (14, 4, 4, NULL)");

        $this->firephp->log("inserting survey_info");
        $this->db->query("INSERT INTO `survey_info` (`survey_info_id`, `survey_name`, `survey_status`) VALUES
        (1, 'New Customer Survey', 1),
        (2, 'Review Survey', 1),
        (3, 'Cancellation Survey', 1)");

        $this->firephp->log("inserting users_to_campaigns");
        //Dumping the users_to_campaigns
        $campaign = $campaignList[array_rand($campaignList)];
        foreach ($agentList as $agent) {

            $this->db->query("INSERT INTO `users_to_campaigns` (`user_id`, `campaign_id`) VALUES
			(" . $agent->user_id . ", " . $campaign->campaign_id . ")");

            if ($this->db->_error_message()) {
                return "user campaign restrictions";
            }
        }
        $this->firephp->log("inserting ownership");
        //Dumping the ownership
        $agent = $agentList[array_rand($agentList)];
        for ($i = 0; $i == 2; $i++) {
            foreach ($this->db->get('records')->result() as $record) {
                $this->db->query("INSERT INTO `ownership` (`urn`, `user_id`) VALUES
				(" . $record->urn . ", " . $agent->user_id . ")");

                if ($this->db->_error_message()) {
                    return "ownership";
                }
            }
        }
        $this->firephp->log("inserting campaign_xfers");
        //dumping campaign xfers
        $this->db->query("INSERT INTO `campaign_xfers` (`campaign_id`, `xfer_campaign`) VALUES ('1', '2'), ('2', '1'),('1', '3'), ('2', '4'),('4', '1'), ('4', '2')");
        if ($this->db->_error_message()) {
            return "campaign_xfers";
        }

        $this->firephp->log("inserting hours");
        //If the hours table exist, dumping sample data
        $agentRole = $this->db->get_where('user_roles', array('role_name' => 'Agent'))->result();
        if ($this->db->table_exists('hours')) {
            $agentList = $this->db->get_where('users', array('role_id' => $agentRole[0]->role_id))->result();
            foreach ($agentList as $agent) {
                $userCampaignList = $this->db->get_where('users_to_campaigns', array('user_id' => $agent->user_id))->result();
                $campaign = $userCampaignList[array_rand($userCampaignList)];
                for ($i = 0; $i <= 60; $i++) {
                    $time = time() - $i * 24 * 3600;
                    $date = date($datestring, $time);
                    $duration = rand(3600, 14400);

                    $comment = "";
                    if (rand(0, 1)) {
                        $comment = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
                    }

                    $this->db->query("INSERT INTO `hours` (`user_id`, `campaign_id`, `duration`, `date`, `comment`, `updated_id`, `updated_date`) VALUES
							($agent->user_id, $campaign->campaign_id, $duration, '" . $date . "', '" . $comment . "', NULL, NULL)");

                    if ($this->db->_error_message()) {
                        return "hours";
                    }
                }
            }
        }
        $this->firephp->log("inserting time");
        //If the time table exist, dumping sample data
        $agentRole = $this->db->get_where('user_roles', array('role_name' => 'Agent'))->result();
        if ($this->db->table_exists('time')) {
            $timeExceptionTypes = $this->db->get('time_exception_type')->result();

            $agentList = $this->db->get_where('users', array('role_id' => $agentRole[0]->role_id))->result();
            foreach ($agentList as $agent) {
                for ($i = 0; $i <= 60; $i++) {
                    $time = time() - $i * 24 * 3600;
                    $date = date($datestring, $time);

                    $start_time = "09:00:00";
                    $end_time = "17:00:00";

                    $comment = "";
                    if (rand(0, 1)) {
                        $comment = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
                    }

                    $this->db->query("INSERT INTO `time` (`user_id`, `start_time`, `end_time`, `date`, `comment`, `updated_id`, `updated_date`) VALUES
							($agent->user_id, '" . $start_time . "', '" . $end_time . "', '" . $date . "', '" . $comment . "', NULL, NULL)");

                    if ($this->db->_error_message()) {
                        return "time";
                    }

                    $time_id = $this->db->insert_id();

                    //Dump the exception time
                    if (rand(0, 1)) {
                        $exception_type = $timeExceptionTypes[array_rand($timeExceptionTypes)];
                        $duration = rand(5, 60);
                        if (!empty($exception_type)) {
                            $this->db->query("INSERT INTO `time_exception` (`time_id`, `exception_type_id`, `duration`) VALUES
							($time_id, {$exception_type->exception_type_id}, $duration)");

                            if ($this->db->_error_message()) {
                                return "time_exception";
                            }
                        }
                    }
                }
            }
        }

        return "success";

    }

    /**
     * Load real data
     *
     * @return string
     */
    public function real_data()
    {

        $group = $this->db->get_where('user_groups', array('group_name' => '121'))->result();
        $this->firephp->log("inserting real teams");
        //Dumpingdata for table `teams`
        $this->db->query("INSERT INTO `teams` (`team_name`) VALUES
		('Jon Surrey'),
		('Wayne Brosnan'),
		('Dean Hibbert'),
		('Stacy Armitt'),
		('Craig Williams'),
		('David Kemp'),
		('Dave Whittaker')");

        if ($this->db->_error_message()) {
            return "teams";
        }

        $teamsList = $this->db->get('teams')->result();
        $agents = array();

        foreach ($teamsList as $team) {
            $agents[$team->team_name]['team_id'] = $team->team_id;
            $agents[$team->team_name]['agents'] = array();
        }

        array_push($agents['Craig Williams']['agents'], array('name' => 'Neyef Abed', 'pass' => '34bbc3086d259088ff5b50afeb3a87a9', 'ext' => '270'));
        array_push($agents['Craig Williams']['agents'], array('name' => 'Connor Moore', 'pass' => 'fe4d1e909ec91fd0faa2740f1a2dbb35', 'ext' => '273'));
        array_push($agents['Craig Williams']['agents'], array('name' => 'Jamie Aldred', 'pass' => 'b7016a5aae4c97c304a635831cfc24ef', 'ext' => '571'));
        array_push($agents['Craig Williams']['agents'], array('name' => 'Umar Mahmood', 'pass' => '27a63f8090673fed2a55d2ad63037890', 'ext' => '580'));
        array_push($agents['Craig Williams']['agents'], array('name' => 'Bethany Rogers', 'pass' => 'xxx', 'ext' => '586'));
        array_push($agents['Craig Williams']['agents'], array('name' => 'Chereen Collins', 'pass' => 'xxx', 'ext' => '587'));
        array_push($agents['David Kemp']['agents'], array('name' => 'Sinead Oliver', 'pass' => '71c61e2e5ebd3e96ce36aedebc59f70e', 'ext' => '265'));
        array_push($agents['David Kemp']['agents'], array('name' => 'Chantelle Taylor', 'pass' => '6784c4e53289ef17e50c01ddcfde6e7f', 'ext' => '415'));
        array_push($agents['David Kemp']['agents'], array('name' => 'Krystian Fouracre', 'pass' => 'ee137a9d7311a1f9052e531715c52816', 'ext' => '483'));
        array_push($agents['David Kemp']['agents'], array('name' => 'Jaswant Singh', 'pass' => '20a65f42cf715cc78b9d1968d740db88', 'ext' => '484'));
        array_push($agents['David Kemp']['agents'], array('name' => 'Paul Singh', 'pass' => '5090bb216ffb35348712b7b3b0d08927', 'ext' => '490'));
        array_push($agents['David Kemp']['agents'], array('name' => 'Hugh Blackwood', 'pass' => '680399f00de8b033cb308c77a01a8729', 'ext' => '524'));
        array_push($agents['David Kemp']['agents'], array('name' => 'Adam Cross', 'pass' => 'f561aaf6ef0bf14d4208bb46a4ccb3ad', 'ext' => '544'));
        array_push($agents['David Kemp']['agents'], array('name' => 'Paul Spencer', 'pass' => '3b3fedb08cca1b338381280e2ce5b5c4', 'ext' => '565'));
        array_push($agents['David Kemp']['agents'], array('name' => 'Romone Morgan', 'pass' => '860c84f47cdd9aa067183ebf8cdb8fa0', 'ext' => '576'));
        array_push($agents['David Kemp']['agents'], array('name' => 'Matthew Maddock', 'pass' => '9b7a94bc2eaf908538a2d486d2d86b18', 'ext' => '585'));
        array_push($agents['Dean Hibbert']['agents'], array('name' => 'Chantel Sweeney', 'pass' => '64449a99383ce737f807b44683184072', 'ext' => '258'));
        array_push($agents['Dean Hibbert']['agents'], array('name' => 'Paul Stoddard', 'pass' => '4427d49496d14c4649765ef78727786e', 'ext' => '413'));
        array_push($agents['Dean Hibbert']['agents'], array('name' => 'James Kemp', 'pass' => '12de9e5db31fd6cbd04020acf463bc2a', 'ext' => '429'));
        array_push($agents['Dean Hibbert']['agents'], array('name' => 'Halle White', 'pass' => '8bdb97c4b7682d62992c6381bd433a02', 'ext' => '445'));
        array_push($agents['Dean Hibbert']['agents'], array('name' => 'Carl Sweeney', 'pass' => '37b4e2d82900d5e94b8da524fbeb33c0', 'ext' => '448'));
        array_push($agents['Dean Hibbert']['agents'], array('name' => 'Sohail Arif', 'pass' => '81dc9bdb52d04dc20036dbd8313ed055', 'ext' => '450'));
        array_push($agents['Dean Hibbert']['agents'], array('name' => 'Gurmohan Singh', 'pass' => 'f2a995bf4805c2c9d426960e75b2887b', 'ext' => '485'));
        array_push($agents['Dean Hibbert']['agents'], array('name' => 'Kevin Farrell', 'pass' => '2419c459e9ad2d94f4a5c887b3ca18cb', 'ext' => '570'));
        array_push($agents['Dean Hibbert']['agents'], array('name' => 'Aimee Armsden', 'pass' => 'b246a03527858e8608a27f886ad14ef9', 'ext' => '579'));
        array_push($agents['Jon Surrey']['agents'], array('name' => 'Yasir Iqbal', 'pass' => '5f4dcc3b5aa765d61d8327deb882cf99', 'ext' => '235'));
        array_push($agents['Jon Surrey']['agents'], array('name' => 'Danny Gale', 'pass' => 'b8c5068a1d849579fea0a0b35c4c9da0', 'ext' => '292'));
        array_push($agents['Jon Surrey']['agents'], array('name' => 'Antonia Winward', 'pass' => '82e4010701956651c3f653309879aec4', 'ext' => '400'));
        array_push($agents['Jon Surrey']['agents'], array('name' => 'Aaron Jackson', 'pass' => '19aef1fabfb1187d5d9755813de4746e', 'ext' => '470'));
        array_push($agents['Jon Surrey']['agents'], array('name' => 'Waqar Hassan', 'pass' => 'a40cf1cb0c65e56016954db8104a841e', 'ext' => '494'));
        array_push($agents['Jon Surrey']['agents'], array('name' => 'Andre Bygrave', 'pass' => '203ba4eaa9c46f0825f3f6d8bb72eb32', 'ext' => '498'));
        array_push($agents['Jon Surrey']['agents'], array('name' => 'Mohammed Rashid', 'pass' => '214beeecb2eee659f521c273a192afec', 'ext' => '522'));
        array_push($agents['Jon Surrey']['agents'], array('name' => 'Rahman Aftab', 'pass' => '831c02f1c2862bdc3d0caf79a55d3263', 'ext' => '529'));
        array_push($agents['Jon Surrey']['agents'], array('name' => 'Shahnawaz Kaleem', 'pass' => '681570416eb952eac59c3a9d6476d28d', 'ext' => '582'));
        array_push($agents['Stacy Armitt']['agents'], array('name' => 'Marcel McKenzie', 'pass' => '73829030e312a5804492202b9a61c527', 'ext' => '277'));
        array_push($agents['Stacy Armitt']['agents'], array('name' => 'Amitava Ghosh', 'pass' => '44608134cbc1138fc0d0f562f0f84cf9', 'ext' => '410'));
        array_push($agents['Stacy Armitt']['agents'], array('name' => 'Akin Raheem', 'pass' => '5d554bc5f3d2cd182cdd0952b1fb87ca', 'ext' => '432'));
        array_push($agents['Stacy Armitt']['agents'], array('name' => 'James Johnson', 'pass' => '3db1a73a245aa55c61204c56c8d99f6d', 'ext' => '472'));
        array_push($agents['Stacy Armitt']['agents'], array('name' => 'Mohammed Ali', 'pass' => '2ec199f1e2de31576869a57488e919ad', 'ext' => '481'));
        array_push($agents['Stacy Armitt']['agents'], array('name' => 'Ben McDougall', 'pass' => '705a09da923ab9f47fbf539013f3065a', 'ext' => '547'));
        array_push($agents['Stacy Armitt']['agents'], array('name' => 'Alexander Jones', 'pass' => '8129b2301c4921e9bc63120feab1e8ac', 'ext' => '554'));
        array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Sarah Shannon', 'pass' => 'd579ba0fe35141ae95b0e380673be804', 'ext' => '237'));
        array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Aron James', 'pass' => '2c49ad8a57c62dcd1db2ad20be8fe02c', 'ext' => '489'));
        array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Osma Akhtar', 'pass' => '72d957b76f3b236c523d1d5e39397532', 'ext' => '492'));
        array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Liam McDowell', 'pass' => '4c8dac669c3d3f0e71b19883d76ac022', 'ext' => '535'));
        array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Gemma McGawley', 'pass' => '404fdd3c9ad06cb1f6ee3a6d8bd20120', 'ext' => '555'));
        array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Jack Bowcock', 'pass' => '2ea8e833c127b995e38265827d86ee8a', 'ext' => '561'));
        array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Amir Garizi', 'pass' => '7cb7b2d842355e5e2b7ecdc7cc9c5211', 'ext' => '562'));
        array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Jennifer Juwah', 'pass' => '5fe8d2bb4abd76ebcf6aaeedc0476595', 'ext' => '575'));
        array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Samuel Hardy', 'pass' => 'b13eb0f3726605825dd104feddcfbd1e', 'ext' => '583'));
        array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Warren Rimmer ', 'pass' => '80e5346badf46bb9be9f396765638c76', 'ext' => '584'));


        $this->firephp->log("inserting real users");
        //Add agents
        foreach ($agents as $teamAgents) {
            $agentRole = $this->db->get_where('user_roles', array('role_name' => 'Agent'))->result();
            $team_id = $teamAgents['team_id'];

            foreach ($teamAgents['agents'] as $agent) {
                $name = $agent['name'];
                $username = strtolower(str_replace(' ', '.', $agent['name']));
                $pass = $agent['pass'];
                $ext = $agent['ext'];

                $this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
				(" . $agentRole[0]->role_id . ", " . $group[0]->group_id . ", " . $team_id . ", '" . $username . "', '" . $pass . "', '" . $name . "', 1, NULL, NULL, NULL, '2014-08-12 12:02:55', " . $ext . ", NULL, NULL, 0, '2014-08-12 12:02:48')");


                if ($this->db->_error_message()) {
                    return "users";
                }
            }
        }


        //Add Administrators
        $adminRole = $this->db->get_where('user_roles', array('role_name' => 'Administrator'))->result();

        $this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
		(" . $adminRole[0]->role_id . ", " . $group[0]->group_id . ", NULL, 'brad.foster', '32250170a0dca92d53ec9624f336ca24', 'Brad Foster', 1, NULL, NULL, '', '2014-09-19 10:16:23', NULL, NULL, NULL, 0, '2014-09-09 10:25:27'),
		(" . $adminRole[0]->role_id . ", " . $group[0]->group_id . ", NULL, 'esteban.correa', '32250170a0dca92d53ec9624f336ca24', 'Esteban Correa', 1, NULL, NULL, '', '2014-09-19 10:16:23', NULL, NULL, NULL, 0, '2014-09-09 10:25:27'),
		(" . $adminRole[0]->role_id . ", " . $group[0]->group_id . ", NULL, 'doug.frost', '32250170a0dca92d53ec9624f336ca24', 'Doug Frost', 1, NULL, NULL, NULL, '2014-08-12 12:02:55', NULL, NULL, NULL, 0, '2014-08-12 12:02:48'),
		(" . $adminRole[0]->role_id . ", " . $group[0]->group_id . ", NULL, 'chris.norman', '32250170a0dca92d53ec9624f336ca24', 'Chris Norman', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', NULL, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $adminRole[0]->role_id . ", " . $group[0]->group_id . ", NULL, 'rob.wilkinson', '32250170a0dca92d53ec9624f336ca24', 'Rob Wilkinson', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', NULL, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $adminRole[0]->role_id . ", " . $group[0]->group_id . ", NULL, 'nicci.biernat', '32250170a0dca92d53ec9624f336ca24', 'Nicci Biernat', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', NULL, NULL, NULL, 0, '2014-08-05 08:18:06')");

        if ($this->db->_error_message()) {
            return "users";
        }

        //Add Team Leaders
        $teamLeaderRole = $this->db->get_where('user_roles', array('role_name' => 'Team Leader'))->result();

        $this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
		(" . $teamLeaderRole[0]->role_id . ", " . $group[0]->group_id . ", " . $agents['David Kemp']['team_id'] . ", 'david.kemp', '32250170a0dca92d53ec9624f336ca24', 'David Kemp', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 622, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $teamLeaderRole[0]->role_id . ", " . $group[0]->group_id . ", " . $agents['Jon Surrey']['team_id'] . ", 'jon.surrey', '32250170a0dca92d53ec9624f336ca24', 'Jon Surrey', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 620, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $teamLeaderRole[0]->role_id . ", " . $group[0]->group_id . ", " . $agents['Wayne Brosnan']['team_id'] . ", 'wayne.brosnan', '32250170a0dca92d53ec9624f336ca24', 'Wayne Brosnan', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 626, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $teamLeaderRole[0]->role_id . ", " . $group[0]->group_id . ", " . $agents['Dean Hibbert']['team_id'] . ", 'dean.hibbert', '32250170a0dca92d53ec9624f336ca24', 'Dean Hibbert', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 206, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $teamLeaderRole[0]->role_id . ", " . $group[0]->group_id . ", " . $agents['Stacy Armitt']['team_id'] . ", 'stacy.armitt', '32250170a0dca92d53ec9624f336ca24', 'Stacy Armitt', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 281, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $teamLeaderRole[0]->role_id . ", " . $group[0]->group_id . ", " . $agents['Craig Williams']['team_id'] . ", 'craig.williams', '32250170a0dca92d53ec9624f336ca24', 'Craig Williams', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 480, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $teamLeaderRole[0]->role_id . ", " . $group[0]->group_id . ", NULL, 'tim.martin', '32250170a0dca92d53ec9624f336ca24', 'Tim Martin', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 610, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $teamLeaderRole[0]->role_id . ", " . $group[0]->group_id . ", " . $agents['Dave Whittaker']['team_id'] . ", 'dave.whittaker', '32250170a0dca92d53ec9624f336ca24', 'Dave Whittaker', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 201, NULL, NULL, 0, '2014-08-05 08:18:06')");

        if ($this->db->_error_message()) {
            return "users";
        }

        //Add the team_leaders to the team_managers
        $teamLeaders = $this->db->get_where('users', array('role_id' => $teamLeaderRole[0]->role_id))->result();
        foreach ($teamLeaders as $teamLeader) {
            if ($teamLeader->team_id) {
                $this->db->query("INSERT INTO `team_managers` (`team_id`, `user_id`) VALUES
                (" . $teamLeader->team_id . ", " . $teamLeader->user_id . ")");

                if ($this->db->_error_message()) {
                    return "team_managers";
                }
            }
        }

        //Add Client Services
        $clientServicesRole = $this->db->get_where('user_roles', array('role_name' => 'Client Services'))->result();

        $this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
		(" . $clientServicesRole[0]->role_id . ", 1, NULL, 'emma.greenfield', '32250170a0dca92d53ec9624f336ca24', 'Emma Greenfield', 1, NULL, NULL, NULL, '2014-08-12 10:05:48', 616, NULL, NULL, 0, NULL),
		(" . $clientServicesRole[0]->role_id . ", 1, NULL, 'chris.peddie', '32250170a0dca92d53ec9624f336ca24', 'Chris Peddie', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 612, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $clientServicesRole[0]->role_id . ", 1, NULL, 'shahzad.hussain', '32250170a0dca92d53ec9624f336ca24', 'Shahzad Hussain', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 621, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $clientServicesRole[0]->role_id . ", 1, NULL, 'sami.elbalbisi', '32250170a0dca92d53ec9624f336ca24', 'Sami El-Balbisi', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 613, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(" . $clientServicesRole[0]->role_id . ", 1, NULL, 'kirsty.prince', '32250170a0dca92d53ec9624f336ca24', 'Kirsty Princes', 1, NULL, '', '', '2014-08-12 09:08:57', 557, NULL, NULL, 0, NULL)");

        if ($this->db->_error_message()) {
            return "users";
        }

        //Add Team Senior
        $teamSeniorRole = $this->db->get_where('user_roles', array('role_name' => 'Team Senior'))->result();

        $this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
		(" . $teamSeniorRole[0]->role_id . ", 1, NULL, 'roger.thornton', '32250170a0dca92d53ec9624f336ca24', 'Roger Thornton', 1, NULL, '', '', '2014-08-12 09:08:57', 207, NULL, NULL, 0, NULL)");

        if ($this->db->_error_message()) {
            return "users";
        }

        return "success";
    }
}
