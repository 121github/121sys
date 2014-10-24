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
	public function get_version(){	
		$name = $this->db->database;
		$qry = "show tables from `$name` where Tables_in_$name = 'migrations'";
		if($this->db->query($qry)->num_rows()){	
			return $this->db->get('migrations')->row()->version;
		} else { 
			return "Unknown";
		}
	}
	
	/**
	 * Drop all tables
	 */
	public function drop_tables(){
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
	private function truncate_data () {
		$tables = $this->db->list_tables();
		
		$this->db->query("SET foreign_key_checks = 0");
		
		foreach ($tables as $table)
		{
			//If the table is migrations, don't truncate
			if ($table != 'migrations') {
				$this->db->empty_table($table);
				$this->db->query("ALTER TABLE `".$table."` AUTO_INCREMENT = 1");
			}
		}
		
		$this->db->query("SET foreign_key_checks = 1");
	}
	
	/**
	 * Dump the init data
	 */
	public function init_data() {
		
		//Truncate all the tables
		$this->truncate_data();		
		
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
		('Recordings', 'recordings.php')");
		
		if ($this->db->_error_message()) {
			return "campaign_features";
		}
		
		//dumping into campaign types table
		$this->db->query("INSERT INTO `campaign_types` (`campaign_type_desc`) VALUES
		('B2C'),
		('B2B')");
		
		if ($this->db->_error_message()) {
			return "campaign_types";
		}
		
		//Dumpingdata for table `configuration`
		$this->db->query("INSERT INTO `configuration` (`use_fullname`) VALUES
		(1)");
		
		if ($this->db->_error_message()) {
			return "configuration";
		}
		
		//Dumpingdata for table `contact_status`
		$this->db->query("INSERT INTO `contact_status` (`contact_status_name`, `score_threshold`, `colour`) VALUES
		('Detractor', 6, '#FF0000'),
		('Passive', 7, '#FF9900'),
		('Promoter', 8, '#00FF00')");
		
		
		if ($this->db->_error_message()) {
			return "contact_status";
		}
		
		
		//Dumpingdata for table `status_list`
		$this->db->query("INSERT INTO `status_list` (`record_status_id`, `status_name`) VALUES
		(1, 'Live'),
		(2, 'Parked'),
		(3, 'Dead'),
		(4, 'Completed')");
		
		if ($this->db->_error_message()) {
			return "record_status_id";
		}
		
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
		(71, 'Cross Transfer', 4, 1, 1, 1, 1, NULL, NULL, NULL),
		(72, 'Appointment', 4, 1, 1, 1, 1, NULL, NULL, NULL)");
		
		$this->db->query("ALTER TABLE `outcomes` AUTO_INCREMENT = 73");
		
		if ($this->db->_error_message()) {
			return "outcomes";
		}
		
		
		//Dumpingdata for table `park_codes`
		$this->db->query("INSERT INTO `park_codes` (`park_reason`) VALUES
		('Rationing'),
		('Not calling')");
		
		
		if ($this->db->_error_message()) {
			return "park_codes";
		}
		
		//Dumpingdata for table `progress_description`
		$this->db->query("INSERT INTO `progress_description` (`description`, `progress_color`) VALUES
		('Pending', 'red'),
		('In Progress', 'orange'),
		('Complete', 'green')");
		
		if ($this->db->_error_message()) {
			return "progress_description";
		}
		
		
		//Dumpingdata for table `sectors` and the `subsectors`
		$sectors =  array(
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
		foreach ($sectors as $sector => $subsectorList) {
			//Dumpingdata for table `sector`
			$this->db->query("INSERT INTO `sectors` (`sector_name`) VALUES ('".$sector."')");
			if ($this->db->_error_message()) {
				return "sectors";
			}
				
			$sector_id = $this->db->insert_id();
			foreach ($subsectorList as $subsector) {
				//Dumpingdata for table `subsectors` for this sector
				$this->db->query("INSERT INTO `subsectors` (`subsector_name`, `sector_id`) VALUES ('".$subsector."',".$sector_id.")");
				if ($this->db->_error_message()) {
					return "subsectors";
				}
			}
		}
		
		
		//Dumpingdata for table `user_groups`
		$this->db->query("INSERT INTO `user_groups` (`group_name`) VALUES
		('121')");
		
		if ($this->db->_error_message()) {
			return "user_groups";
		}
		
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
		
		//Dumping the administrator user
		$this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
		(1, 1, NULL, 'admin', '32250170a0dca92d53ec9624f336ca24', 'Administrator', 1, NULL, NULL, '', '2014-09-19 10:16:23', NULL, NULL, NULL, 0, '2014-09-09 10:25:27')");
		
		if ($this->db->_error_message()) {
			return "users";
		}
		
		//dumping data for permissions
		$this->db->query("INSERT INTO `permissions` (`permission_name`, `permission_group`) VALUES
		('set call outcomes', 'Records'),
		('set progress', 'Records'),
		('add surveys', 'Surveys'),
		('view surveys', 'Surveys'),
		('edit surveys', 'Surveys'),
		('delete surveys', 'Surveys'),
		('add contacts', 'Contacts'),
		('edit contacts', 'Contacts'),
		('delete contacts', 'Contacts'),
		('add companies', 'Companies'),
		('edit companies', 'Companies'),
		('add records', 'Records'),
		('reset records', 'Records'),
		('park records', 'Records'),
		('view ownership', 'Ownership'),
		('change ownership', 'Ownership'),
		('view appointments', 'Appointments'),
		('add appointments', 'Appointments'),
		('edit appointments', 'Appointments'),
		('delete appointments', 'Appointments'),
		('view history', 'History'),
		('delete history', 'History'),
		('edit history', 'History'),
		('view call recordings', 'Recordings'),
		('delete call recordings', 'Recordings'),
		('search records', 'Search'),
		('send email', 'Email'),
		('view email', 'Email'),
		('all campaigns','System'),
		('agent dash','Dashboards'),
		('client dash','Dashboards'),
		('management dash','Dashboards'),
		('view reports','Reports'),
		('search campaigns','Search'),
		('search surveys','Search'),
		('log hours','System'),
		('edit scripts','Admin'),
		('edit templates','Admin'),
		('reassign data','Admin'),
		('view logs','Admin'),
		('view hours','Admin'),
		('show footer', 'System'),
		('admin nav','Admin'),
		('campaign access','Admin')");
		
		if ($this->db->_error_message()) {
			return "permissions";
		}
		
		$this->db->query("INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
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
(1, 33),
(1, 34),
(1, 35),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 43),
(1, 44),
(2, 2),
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
(2, 33),
(2, 34),
(2, 38),
(2, 39),
(2, 40),
(2, 41),
(2, 43),
(2, 44),
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
(3, 16),
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
(3, 33),
(3, 35),
(3, 36),
(3, 40),
(3, 41),
(3, 42),
(3, 43),
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
(4, 33),
(4, 34),
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
(5, 42)");
		
		
		//Dumpingdata for table `data_sources` for Manual
		$this->db->query("INSERT INTO `data_sources` (`source_name`, `cost_per_record`) VALUES
		('Manual', NULL)");
		
		if ($this->db->_error_message()) {
			return "data_sources";
		}
		
		
		return "success";
	}
	
	/**
	 * Load demo data
	 * 
	 * @return string
	 */
	public function demo_data() {

		//dumping data sample into clients
		$this->db->query("INSERT INTO `clients` (`client_name`) VALUES
		('121'),
		('Sample Client')");
		
		
		if ($this->db->_error_message()) {
			return "clients";
		}
		
		//create sample campaign
		$i = 1;
		foreach ($this->db->get('clients')->result() as $client) {
			foreach ($this->db->get('campaign_types')->result() as $campaign_type) {
				$this->db->query("INSERT INTO `campaigns` (`campaign_name`, `campaign_type_id`, `client_id`, `start_date`, `end_date`, `campaign_status`, `email_recipients`, `reassign_to`, `custom_panel_name`) VALUES
				('Sample ".$campaign_type->campaign_type_desc." Campaign_".$i++."', '".$campaign_type->campaign_type_id."', ".$client->client_id.", '2014-09-30', NULL, 1, NULL, NULL, '')");
			
				$this->db->query("INSERT INTO `record_details_fields` (`campaign_id`, `field`, `field_name`) VALUES
				($i,'c1','Product')");
				$this->db->query("INSERT INTO `record_details_fields` (`campaign_id`, `field`, `field_name`) VALUES
				($i,'d1','Renewal Date')");
			
				if ($this->db->_error_message()) {
					return "campaigns";
				}
				
				$campaign_id = $this->db->insert_id();
				
				//Dump the outcomes to each campaign
				foreach ($this->db->get('outcomes')->result() as $outcome) {
					
					$this->db->query("INSERT INTO `outcomes_to_campaigns` (`outcome_id`, `campaign_id`) VALUES
					(".$outcome->outcome_id.", ".$campaign_id.")");
				
					if ($this->db->_error_message()) {
						return "outcomes_to_campaigns";
					}
				}
			}
		}
		
		//Dumping sample into campaign features
		foreach ($this->db->get('campaigns')->result() as $campaign) {
			$where = "";
			if($campaign->campaign_type_id=="1"){
				$where .= ' and feature_name <> "Company"';
			}
			$this->db->where('feature_name not in ("Surveys","Appointment Setting","Emails") $where');
			foreach ($this->db->get('campaign_features')->result() as $campaign_feature)
			{
				$this->db->query("INSERT INTO `campaigns_to_features` (`campaign_id`, `feature_id`) VALUES (".$campaign->campaign_id.", ".$campaign_feature->feature_id.")");
					
				if ($this->db->_error_message()) {
					return "campaigns_to_features";
				}
			}
		}
		
		//Dumpingdata for table `data_sources`
		$this->db->query("INSERT INTO `data_sources` (`source_name`, `cost_per_record`) VALUES
		('Source 1', NULL),
		('Source 2', NULL)");
		
		if ($this->db->_error_message()) {
			return "data_sources";
		}
		
		//Dumpingdata for table `teams`
		for ($i = 1; $i<=7; $i++) {
			$this->db->query("INSERT INTO `teams` (`team_name`) VALUES ('Team ".$i."')");
			if ($this->db->_error_message()) {
				return "teams";
			}	
		}
		
		$campaignList = $this->db->get('campaigns')->result();
		$outcomeList = $this->db->get('outcomes')->result();
		$teamsList = $this->db->get('teams')->result();
		$sourcesList = $this->db->get('data_sources')->result();
		
		$datestring = "Y-m-d H:i:s";
		
		$agentRole = $this->db->get_where('user_roles', array('role_name' => 'Agent'))->result();
		$groupList = $this->db->get('user_groups')->result();
		
		$names = array('Jennifer','Martha','Nicholas','AshleyHernandez','AlbertSimmons','Thomas','Janice','Stephen','Sharon','Nicholas','Philip','Robin','Tina','Harry','Annie','Jonathan','Jimmy','Janet','Brenda','Walter','Earl','Ronald','Rose','Jennifer','Linda','Margaret','Joshua','Phillip','Martin','Joseph','Frances','Jane','Bonnie','Cynthia','Maria','Susan','Gregory','Katherine','Keith','Cheryl','Sandra','Robin','Daniel','Melissa','David','Albert','Ruth','Edward','Christine','Lawrence','Peter','Katherine','Samuel','Michael','Cheryl','Henry','Earl','Russell','Beverly','Roy','Betty','Elizabeth','Alice','William','Chris','Wanda','Susan','Brian','Daniel','Kelly','Jessica','Alan','Gary','Jerry');
		$surnames = array('Lynch','Frazier', 'Jordan', 'Hernandez','Simmons', 'Gordon','Butler', 'Carpenter','Hawkins','Clark','Hunt', 'Russell','Ford', 'Parker','Hernandez','Meyer', 'Howard', 'Cole', 'Hall','Stone','Mills', 'Ward', 'Foster', 'Foster','Pierce','Thompson', 'Evans', 'Carpenter', 'Davis', 'Brown','Ramos','Carr', 'Wilson', 'Lynch', 'Gomez', 'Stone', 'Ellis', 'Harris','Matthews','Jones', 'Bishop','Andrews', 'Hamilton', 'Parker','Willis', 'Wheeler','Welch', 'Watson','Hamilton','Mason', 'Bell', 'Fuller','Hudson','Burton','Medina', 'Peterson', 'Hall', 'Rivera', 'Fernandez','Walker', 'Ferguson', 'Romero','Gordon', 'Sanders', 'Carpenter', 'Simpson','Oliver', 'Perry', 'Martin', 'Murray', 'Edwards','Dean', 'Andrews', 'Ferguson');
		$addresses = array('36 Hauk Road', '658 Loeprich Court', '55717 Warrior Road', '6 Continental Pass', '8993 Garrison Junction', '64 Marcy Alley', '9 Troy Alley', '8176 Kropf Center', '5652 Orin Center', '1 Redwing Parkway', '013 6th Court', '92274 Twin Pines Hill', '26 Vermont Avenue', '2330 Oak Valley Center', '290 Elgar Alley', '4248 Service Terrace', '0688 Tomscot Road', '7 Spenser Center', '9059 Cottonwood Crossing', '183 Valley Edge Way', '70 Bobwhite Pass', '984 Michigan Park', '87 Londonderry Point', '97 Doe Crossing Trail', '611 Tomscot Alley', '3 Truax Center', '13690 Valley Edge Trail', '41 Larry Lane', '66617 Crownhardt Way', '48 Debs Street', '97438 Ridge Oak Place', '86163 Randy Point', '13 Kropf Road', '9 Crescent Oaks Avenue', '9 Springs Road', '40 Dryden Trail', '2 Oneill Plaza', '6751 Corben Circle', '32434 Elmside Terrace', '06226 Farwell Center', '50 Thompson Junction', '43399 East Park', '383 Leroy Point', '8 Scoville Junction', '3 Katie Drive', '26 Tomscot Pass', '3 Forest Park', '443 Eagle Crest Center', '2 Main Center', '6 Sutteridge Way', '20787 Lyons Parkway', '5781 Golf Course Terrace', '1 Dwight Crossing', '426 Trailsway Park', '73420 Merchant Pass', '0322 Union Trail', '6714 Eagle Crest Hill', '9045 Park Meadow Junction', '9568 Rusk Alley', '5278 Londonderry Point', '71 Brickson Park Pass', '7724 Norway Maple Avenue', '8 Hagan Way', '3 Rutledge Trail', '769 Colorado Center', '35 Carey Road', '907 Brown Park', '06 Duke Plaza', '0290 Hudson Alley', '62402 Elka Hill', '765 Express Street', '51420 Center Lane', '2 Brickson Park Court', '6204 Nancy Avenue', '15798 Haas Alley', '89360 Fieldstone Center', '46 Pleasure Road', '3288 Dahle Terrace', '6 High Crossing Crossing', '76 Namekagon Alley', '354 Kingsford Circle', '7823 Pearson Park', '598 Muir Terrace', '9546 Summit Terrace', '68 Gateway Road', '8 Dorton Alley', '27 Thompson Avenue', '4848 Anderson Point', '6 Hooker Center', '74491 Monument Pass', '47513 Judy Park', '05 Village Green Circle', '763 Elka Avenue', '70868 Wayridge Parkway', '012 Farmco Circle', '0564 Lakewood Pass', '5 Fallview Street', '07867 Old Gate Way', '45 Tennessee Junction', '54317 6th Circle', '99 Schmedeman Lane', '0089 2nd Hill', '77854 Blackbird Point', '88770 Forest Dale Circle', '8 Green Alley', '5469 Armistice Lane', '54471 Truax Alley', '2 Kipling Parkway', '519 Manitowish Lane', '8 Dapin Road', '87 Cambridge Junction', '08498 Kingsford Avenue', '087 Sage Hill', '46 Charing Cross Circle', '80 Pleasure Terrace', '25 Forest Drive', '0120 Canary Parkway', '430 Utah Place', '0 Helena Alley', '168 Cambridge Alley', '1 Clove Road', '38 Clyde Gallagher Junction', '34165 Coolidge Point', '1 Columbus Lane', '69890 Esch Street', '4956 Quincy Crossing', '0747 Mifflin Point', '2 Linden Plaza', '216 Barnett Street', '5261 Nancy Junction', '140 Carey Way', '009 Fairfield Plaza', '9 Waywood Alley', '9060 Graedel Pass', '973 Westport Pass', '31 Drewry Circle', '2 Rigney Center', '04 Meadow Valley Point', '40582 La Follette Terrace', '83 Mayfield Place', '615 Manitowish Place', '6 Kedzie Plaza', '022 Canary Circle', '35 Badeau Pass', '42504 Little Fleur Center', '6 Drewry Parkway', '972 Northport Pass', '89 Jay Drive', '9 Fulton Lane', '94488 Independence Crossing', '6 Carpenter Parkway', '9225 Walton Drive', '56 American Ash Center', '4 Bluestem Junction', '02348 Pawling Crossing', '66296 Ronald Regan Lane', '2774 Forest Run Court', '5676 Cody Drive', '6 Oak Way', '7 Buhler Park', '08 Johnson Circle', '04148 Meadow Valley Alley', '78 Kipling Hill', '4 Brown Pass', '50 Stuart Lane', '94521 Sundown Place', '5078 Scott Center', '85 Delladonna Pass', '0 Armistice Avenue', '7 Fulton Court', '17 Butternut Plaza', '49313 Coolidge Junction', '8 Orin Way', '3 Homewood Point', '435 Maryland Road', '046 Haas Alley', '5757 Meadow Valley Lane', '71362 Gateway Terrace', '67395 Leroy Court', '06456 Marquette Parkway', '5 Cambridge Lane', '2 Johnson Parkway', '5110 Moulton Road', '5 Pankratz Place', '4548 Packers Place', '232 Onsgard Plaza', '62628 Graedel Road', '854 Rockefeller Center', '1441 Iowa Alley', '1 Ludington Pass', '416 Welch Crossing', '755 Summit Point', '7486 John Wall Junction', '54 Hermina Circle', '915 Kinsman Lane', '4 Browning Road', '39815 Superior Trail', '3834 Nova Avenue', '59 Cardinal Pass', '9 Memorial Hill', '2 Rowland Lane', '2799 Lighthouse Bay Trail', '6 Norway Maple Hill', '9 Ohio Hill', '443 Kipling Pass', '1 Sachs Alley', '65045 Meadow Vale Crossing', '42521 Daystar Place', '1 Bashford Pass', '9 Hintze Court', '2 Fulton Hill', '2 Crest Line Avenue', '31921 Loomis Point', '8601 Lighthouse Bay Place', '11 Farwell Avenue', '3 Lien Drive', '48 Memorial Point', '48 Arapahoe Crossing', '6825 Johnson Trail', '939 Cordelia Plaza', '468 Pepper Wood Point', '779 Vahlen Point', '1 Canary Crossing', '91 Loomis Parkway', '468 High Crossing Road', '1621 Goodland Point', '45 Dakota Junction', '76 Riverside Pass', '5 Meadow Vale Alley', '2706 Roxbury Crossing', '50166 Lindbergh Junction', '78 Longview Way', '8 Rusk Avenue', '01521 Dayton Street', '06 Green Terrace', '292 Armistice Circle', '7928 Moulton Trail', '5 Fordem Avenue', '18 Mandrake Road', '377 American Ash Way', '81406 Twin Pines Alley', '776 Kensington Crossing', '19138 Hermina Crossing', '69 Merry Hill', '5667 Rutledge Center', '5 1st Center', '703 Menomonie Court', '670 Jenna Place', '50732 Stang Court', '66461 American Ash Center', '8 Maryland Street', '24477 Golden Leaf Court', '2 Continental Point', '57475 Warrior Alley', '901 Dayton Avenue', '0 Cambridge Court', '11 Manitowish Junction', '54115 Muir Way', '65 Delladonna Park', '99 Harbort Circle', '74 Forster Parkway', '33767 Dovetail Crossing', '8793 Hanson Plaza', '00467 Marquette Way', '13 Autumn Leaf Lane', '50 Roth Street', '0604 Russell Center', '89065 Manley Junction', '33 Hazelcrest Road', '7651 5th Center', '59375 Katie Road', '8407 Mayer Lane', '65675 Pond Pass', '4 Fallview Parkway', '7 Mayfield Drive', '936 Clarendon Lane', '43443 Mockingbird Road', '52455 Blue Bill Park Plaza', '6 Bay Crossing', '59934 Welch Crossing', '63 School Crossing', '3 7th Center', '76769 Merry Lane', '7991 Truax Street', '61369 Springview Lane', '1 Farwell Pass', '0742 Sycamore Avenue', '3794 Florence Road', '021 Almo Drive', '158 Ramsey Plaza', '33 Marquette Way', '625 Sage Street', '1376 Melody Place', '12 Melby Center', '42 Brentwood Plaza', '7997 Hintze Park', '89 Hoffman Plaza', '920 Magdeline Drive', '24193 Westend Road', '5488 Graceland Road', '569 Beilfuss Lane', '8 Linden Drive', '864 Sutteridge Plaza', '4 Garrison Avenue', '90753 Tennessee Court', '35076 Canary Junction', '3 Tomscot Center', '85 Londonderry Center', '22 Lakewood Lane', '83 Florence Hill', '34 Charing Cross Terrace', '3521 Garrison Street', '3 Tomscot Hill', '3706 Vermont Circle', '46181 Cambridge Point', '1723 Lunder Place', '2621 Schiller Pass', '64 Grover Terrace', '5 Prairieview Park', '510 Grayhawk Avenue', '415 Roxbury Plaza', '14 Sutherland Crossing', '67 Hudson Circle', '00023 Hansons Junction', '096 Summerview Way', '2581 Wayridge Court', '9 Hagan Trail', '38 Loeprich Pass', '4388 4th Junction', '63388 Northridge Alley', '8365 Bowman Point', '858 Lakewood Pass', '08 Straubel Lane', '525 Blaine Center', '827 Charing Cross Avenue', '5 8th Court', '06 Sommers Way', '704 Canary Place', '12 Laurel Circle', '31 Kennedy Trail', '53250 Lawn Street', '7 Paget Avenue', '169 Montana Alley', '1542 Mandrake Avenue', '262 Redwing Terrace', '682 International Drive', '561 John Wall Lane', '8 Canary Lane', '3374 Morrow Lane');
		$telephones = array('9-(691)168-4061', '5-(759)428-2283', '0-(201)575-1430', '1-(278)562-1339', '1-(814)772-8745', '8-(379)624-7002', '2-(305)960-1522', '9-(510)288-5526', '5-(694)311-0299', '4-(703)747-4251', '0-(738)024-3464', '2-(545)437-5155', '3-(327)222-2593', '7-(060)503-9750', '8-(006)500-6240', '6-(408)874-9185', '2-(516)171-2958', '0-(386)020-1315', '7-(983)325-2492', '6-(323)245-9282', '9-(338)164-3508', '1-(157)506-7874', '8-(295)266-8906', '7-(976)302-9646', '0-(178)616-9109', '9-(184)631-8090', '5-(094)471-2200', '9-(386)287-1181', '2-(483)612-9202', '9-(423)967-4558', '3-(855)684-1652', '7-(454)640-9920', '5-(034)850-8081', '8-(398)700-4420', '8-(949)809-4503', '9-(051)158-4214', '5-(102)393-6615', '1-(292)187-8664', '0-(951)713-3266', '4-(693)829-0357', '2-(851)964-4264', '3-(693)953-4706', '5-(606)954-9703', '1-(045)373-0457', '1-(743)313-6556', '9-(026)674-9072', '5-(677)773-7700', '3-(935)093-9315', '0-(583)394-9016', '5-(782)067-6271', '1-(982)640-8874', '2-(245)222-8698', '1-(504)804-2850', '5-(075)319-4915', '7-(642)062-2946', '8-(870)061-7964', '3-(591)121-4535', '7-(290)117-1021', '3-(225)406-0536', '1-(570)607-8943', '4-(858)157-9655', '4-(271)581-3103', '5-(989)947-3039', '9-(175)719-9279', '4-(158)926-1624', '9-(058)602-4184', '9-(603)513-8818', '8-(899)364-1933', '3-(849)365-1265', '7-(933)176-9448', '0-(123)226-7516', '9-(801)001-8206', '5-(945)136-5242', '8-(585)117-8729', '0-(861)797-8057', '0-(283)864-1143', '4-(001)449-9017', '0-(960)320-9642', '9-(825)043-8284', '4-(876)599-4223', '1-(954)004-0552', '9-(974)750-9529', '1-(749)257-5937', '5-(140)591-3704', '0-(347)076-5879', '8-(937)837-6376', '7-(318)430-8369', '7-(385)429-2814', '2-(056)243-8303', '5-(251)602-8425', '9-(002)150-8751', '7-(975)389-4198', '0-(424)228-6241', '1-(616)637-8297', '8-(285)191-8168', '7-(188)522-2368', '1-(388)160-0610', '4-(218)256-3358', '7-(519)342-2271', '8-(906)654-6117', '9-(398)694-8741', '5-(927)129-6767', '5-(597)079-9022', '2-(746)269-8924', '4-(470)164-2351', '0-(894)713-7650', '4-(416)965-2181', '4-(109)331-3054', '1-(276)947-1842', '1-(508)421-2767', '1-(257)353-1979', '3-(855)835-9860', '0-(790)121-5119', '9-(229)423-1595', '9-(147)615-1533', '4-(312)318-2379', '6-(739)476-4879', '3-(420)521-7622', '7-(688)242-4382', '0-(157)728-6867', '8-(089)499-2728', '6-(265)705-8969', '7-(273)346-2732', '6-(245)846-9662', '5-(453)583-0740', '0-(504)537-0002', '4-(146)788-8947', '8-(346)125-3857', '7-(562)508-0571', '0-(615)676-7736', '5-(425)184-2954', '8-(511)945-1584', '1-(564)655-3075', '8-(118)077-1467', '9-(358)579-5117', '5-(067)275-3400', '0-(815)593-0117', '9-(148)538-8967', '9-(757)007-6937', '3-(992)341-7657', '5-(254)572-4677', '8-(341)082-0004', '1-(826)177-4832', '2-(541)874-1092', '4-(355)243-1221', '6-(337)787-8712', '6-(091)928-5163', '8-(776)909-1982', '9-(679)521-9413', '9-(103)258-1175', '1-(412)084-6069', '7-(949)867-9555', '7-(458)364-3717', '4-(581)577-2567', '7-(529)409-1296', '9-(206)191-2714', '2-(299)276-1062', '8-(701)194-4303', '6-(117)776-9074', '8-(914)158-7198', '5-(627)731-3866', '7-(216)161-0990', '4-(334)851-8045', '5-(346)349-7562', '3-(761)201-2050', '8-(295)541-7422', '0-(044)648-2500', '8-(894)134-8106', '7-(976)780-6077', '7-(271)858-3838', '6-(389)408-8756', '2-(235)435-1209', '5-(406)581-3244', '6-(305)718-2534', '1-(853)125-5626', '1-(407)091-0487', '6-(711)148-4168', '6-(126)812-1590', '1-(430)890-3312', '3-(074)109-4148', '0-(277)087-9437', '7-(505)276-2135', '7-(220)410-6409', '8-(085)151-3404', '5-(734)276-4620', '5-(250)392-2108', '5-(913)493-4739', '4-(083)127-3099', '6-(736)008-9927', '7-(840)293-0118', '9-(321)782-9759', '1-(495)839-7619', '3-(439)467-4925', '3-(781)678-3333', '4-(048)645-0469', '6-(318)087-7463', '4-(225)673-5033', '6-(894)672-5693', '7-(701)518-7253', '0-(135)084-2496', '2-(021)602-8123', '5-(420)686-2738', '4-(631)316-0552', '1-(188)303-0712', '1-(508)873-7486', '8-(525)063-2548', '5-(123)843-0563', '9-(492)552-7005', '3-(981)334-9861', '0-(265)297-9590', '7-(450)079-3381', '4-(163)104-1621', '5-(445)098-7483', '1-(079)220-8540', '1-(726)194-1611', '9-(277)480-4858', '4-(708)538-2556', '4-(375)803-8665', '6-(126)663-9690', '8-(781)418-1251', '4-(676)961-8835', '3-(627)108-5100', '5-(808)506-8539', '3-(074)582-5916', '6-(920)832-3284', '5-(411)221-6653', '4-(501)844-1354', '9-(891)630-6218', '8-(625)911-3438', '0-(807)844-7205', '1-(523)960-3291', '1-(274)933-5887', '0-(170)678-3266', '1-(822)592-3003', '6-(111)262-6829', '6-(375)479-8114', '7-(301)694-0086', '0-(522)376-8618', '5-(598)936-3448', '2-(065)708-2624', '6-(951)370-5483', '9-(742)527-9860', '9-(855)173-9509', '0-(883)905-3715', '9-(155)344-1053', '2-(311)826-0492', '3-(781)777-3593', '2-(984)123-3938', '2-(155)821-1489', '0-(939)020-7961', '6-(994)555-2360', '5-(612)119-6770', '2-(399)851-2995', '7-(015)421-5085', '5-(094)876-0175', '6-(407)978-4175', '7-(059)589-9688', '0-(987)641-3861', '2-(613)641-8285', '3-(237)820-3949', '3-(654)073-9342', '8-(039)847-8259', '9-(959)293-6372', '5-(014)197-2858', '1-(304)274-2138', '1-(474)234-2751', '0-(418)368-8468', '8-(365)831-3022', '7-(260)396-9345', '6-(402)092-5150', '1-(459)166-6468', '0-(861)912-1863', '9-(813)659-5828', '4-(229)488-6911', '6-(396)505-5583', '5-(889)074-1500', '3-(867)598-1700', '0-(185)432-5360', '4-(767)921-8398', '6-(121)627-8871', '9-(348)601-4396', '4-(549)127-7036', '9-(048)620-2606', '8-(129)689-6064', '2-(934)090-1361', '7-(258)123-4499', '2-(048)222-8313', '6-(584)082-5685', '6-(863)106-7922', '3-(390)619-6943', '5-(387)002-7134', '4-(020)458-1624', '4-(498)758-7575', '6-(000)567-3707', '1-(893)852-1854', '6-(183)304-6890', '7-(339)078-9608', '2-(023)598-0042', '6-(773)421-8881', '0-(997)390-2467', '2-(095)087-9989', '9-(955)560-2509', '3-(403)431-8579', '5-(782)081-4258', '4-(929)915-1944', '9-(450)249-3484', '5-(311)797-7454', '8-(091)566-7828', '7-(725)579-3935', '3-(568)266-3856', '4-(066)569-0050', '7-(169)817-7008', '2-(297)770-5054', '8-(803)071-4563', '3-(651)382-6817', '7-(470)221-7546', '9-(417)380-1903', '9-(119)971-5484', '8-(319)054-2356', '7-(883)289-7467', '5-(462)676-3098', '9-(852)477-5344', '3-(469)649-3311', '3-(708)495-6803', '2-(874)931-8767', '3-(352)197-6141', '1-(604)892-6535', '1-(114)766-8486', '0-(754)016-5611', '0-(741)223-7345', '0-(425)112-4742', '5-(886)574-9366', '7-(924)180-9885', '2-(299)129-3199', '3-(166)950-7523', '7-(138)423-6132', '0-(333)052-7443', '7-(242)828-0596', '8-(154)630-7504', '8-(352)475-2909', '3-(843)121-1768', '7-(940)110-6754', '1-(117)800-9651', '2-(506)143-3801', '8-(492)638-6502', '1-(903)761-3991', '6-(627)102-8196', '8-(618)201-0438', '1-(611)381-1161');
		
		
		//Dumpingdata for table `users` except agents
		foreach ($this->db->get('user_roles')->result() as $role) {
			$group = $this->db->get_where('user_groups', array('group_name' => '121'))->result();
			
			if ($role->role_name != 'Agent') {
				$this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
				(".$role->role_id.", ".$group[0]->group_id.", NULL, '".strtolower(str_replace(' ', '.', $role->role_name))."', '32250170a0dca92d53ec9624f336ca24', '".$role->role_name."', 1, NULL, NULL, NULL, '2014-08-12 12:02:55', 111, NULL, NULL, 0, '2014-08-12 12:02:48')");
			
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
			(".$agentRole[0]->role_id.", ".$group->group_id.", ".$team->team_id.", 'agent".$i."', '32250170a0dca92d53ec9624f336ca24', 'Agent ".$i."', 1, NULL, NULL, NULL, '2014-08-12 12:02:55', ".$i.", NULL, NULL, 0, '2014-08-12 12:02:48')");
		
			if ($this->db->_error_message()) {
				return "users";
			}
			$i++;
		}
		$agentList = $this->db->get_where('users', array('role_id' => $agentRole[0]->role_id))->result();
		
		//Dumping the records
		for($i=1;$i<=300;$i++){
			
			$days = rand(1, 90);
			$time = time() - $days*24*3600;
			$date = date($datestring, $time);
			
			$campaign = $campaignList[array_rand($campaignList)];
			$outcome = $outcomeList[array_rand($outcomeList)];
			$team = $teamsList[array_rand($teamsList)];
			$source = $sourcesList[array_rand($sourcesList)];
			
			$this->db->query("INSERT INTO `records` (`campaign_id`, `outcome_id`, `team_id`, `nextcall`, `dials`, `record_status`, `parked_code`, `progress_id`, `urgent`, `date_added`, `date_updated`, `reset_date`, `last_survey_id`, `source_id`) VALUES
					(".$campaign->campaign_id.", ".$outcome->outcome_id.", ".$team->team_id.", '".$date."', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ".$source->source_id.")");
			
			$this->db->query("insert into record_details set urn=$i,c1 = 'Insurance',d1='$date'");
			
			if ($this->db->_error_message()) {
				return "records";
			}
		}
		 
		//Dumping the contacts and the history
		$i = 0;
		foreach ($this->db->get('records')->result() as $record) {
			$name = $names[array_rand($names)];
			$surname = $surnames[array_rand($surnames)];
			$address = $addresses[array_rand($addresses)];
			$campaign = $campaignList[array_rand($campaignList)];
			$agent = $agentList[array_rand($agentList)];
			
			$days = rand(1, 90);
			$time = time() - $days*24*3600;
			$time_after = time() - $days*24*3660;
			$date = date($datestring, $time);
			$date_after = date($datestring, $time_after);
			
			//Contact
			$this->db->query("INSERT INTO `contacts` (`urn`, `fullname`, `title`, `firstname`, `lastname`, `position`, `dob`, `fax`, `email`, `email_optout`, `website`, `linkedin`, `facebook`, `notes`, `date_created`, `date_updated`, `sort`) VALUES
					(".$record->urn.", '".$name." ".$surname."', NULL, '".$name."', '".$surname."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '".$date_after."', NULL)");
			
			$contact_id = $this->db->insert_id();
			
			if ($this->db->_error_message()) {
				return "contacts";
			}
			
			//Address
			$this->db->query("INSERT INTO `contact_addresses` (`contact_id`, `add1`, `add2`, `add3`, `county`, `country`, `postcode`, `latitude`, `longitude`, `primary`) VALUES
					($contact_id, '".$address."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1)");
			
			if ($this->db->_error_message()) {
				return "contact addresses";
			}
			
			
			//Telephone
			$this->db->query("INSERT INTO `contact_telephone` (`contact_id`, `telephone_number`, `description`, `tps`) VALUES
					($contact_id, '".$telephones[$i]."', 'Telephone', NULL)");
					
					
			if ($this->db->_error_message()) {
				return "contact telephone numbers";
            }
            
            
            //History
            $this->db->query("INSERT INTO `history` (`campaign_id`, `urn`, `loaded`, `contact`, `description`, `outcome_id`, `comments`, `nextcall`, `user_id`, `role_id`, `group_id`, `contact_id`, `progress_id`, `last_survey`) VALUES
            		($campaign->campaign_id, ".$record->urn.", NULL, '".$date."', 'Record was updated', ".$record->outcome_id.", 'Comment', '".$date."', ".$agent->user_id.", ".$agent->role_id.", ".$agent->group_id.", NULL, NULL, NULL)");
					//update history campaigns to match the record campaigns
            $this->db->query("update history left join records using(urn) set history.campaign_id = records.campaign_id");
            if ($this->db->_error_message()) {
            	return "history";
            }
            
            $i++;
		}
		
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
		
		
		//Dumping the users_to_campaigns
		$campaign = $campaignList[array_rand($campaignList)];
		foreach ($agentList as $agent) {
			
			$this->db->query("INSERT INTO `users_to_campaigns` (`user_id`, `campaign_id`) VALUES
			(".$agent->user_id.", ".$campaign->campaign_id.")");
			
			if ($this->db->_error_message()) {
				return "user campaign restrictions";
			}
		}
		
		//Dumping the ownership
		$agent = $agentList[array_rand($agentList)];
		for ($i=0;$i==2;$i++) {
			foreach ($this->db->get('records')->result() as $record) {
				$this->db->query("INSERT INTO `ownership` (`urn`, `user_id`) VALUES
				(".$record->urn.", ".$agent->user_id.")");
				
				if ($this->db->_error_message()) {
					return "ownership";
				}
			}
		}
		
		//dumping campaign xfers
		$this->db->query("INSERT INTO `campaign_xfers` (`campaign_id`, `xfer_campaign`) VALUES ('1', '2'), ('2', '1')");
		if ($this->db->_error_message()) {
					return "campaign_xfers";
				}
		
		
		//If the hours table exist, dumping sample data
		$agentRole = $this->db->get_where('user_roles', array('role_name' => 'Agent'))->result();
		if ($this->db->table_exists('hours')) {
			$agentList = $this->db->get_where('users', array('role_id' => $agentRole[0]->role_id))->result();
			foreach ($agentList as $agent) {
				$campaign = $campaignList[array_rand($campaignList)];
				for ($i=0;$i<=60;$i++) {
					$time = time() - $i*24*3600;
					$date = date($datestring, $time);
					$duration = rand(3600, 14400);
					
					$exception = ($duration*60)%60;
					
					$comment = "";
					if (rand(0,1)) {
						$comment = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
					}
					
					$this->db->query("INSERT INTO `hours` (`user_id`, `campaign_id`, `duration`, `exception`, `date`, `comment`, `updated_id`, `updated_date`) VALUES
							($agent->user_id, $campaign->campaign_id, $duration, $exception, '".$date."', '".$comment."', NULL, NULL)");
						
					if ($this->db->_error_message()) {
						return "ownership";
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
	public function real_data() {
	
		$group = $this->db->get_where('user_groups', array('group_name' => '121'))->result();
	
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
	
		array_push($agents['Craig Williams']['agents'], array('name' => 'Neyef Abed', 'pass' =>'34bbc3086d259088ff5b50afeb3a87a9', 'ext' =>'270'));
		array_push($agents['Craig Williams']['agents'], array('name' => 'Connor Moore', 'pass' =>'fe4d1e909ec91fd0faa2740f1a2dbb35', 'ext' =>'273'));
		array_push($agents['Craig Williams']['agents'], array('name' => 'Jamie Aldred', 'pass' =>'b7016a5aae4c97c304a635831cfc24ef', 'ext' =>'571'));
		array_push($agents['Craig Williams']['agents'], array('name' => 'Umar Mahmood', 'pass' =>'27a63f8090673fed2a55d2ad63037890', 'ext' =>'580'));
		array_push($agents['Craig Williams']['agents'], array('name' => 'Bethany Rogers', 'pass' =>'xxx', 'ext' =>'586'));
		array_push($agents['Craig Williams']['agents'], array('name' => 'Chereen Collins', 'pass' =>'xxx', 'ext' =>'587'));
		array_push($agents['David Kemp']['agents'], array('name' => 'Sinead Oliver', 'pass' =>'71c61e2e5ebd3e96ce36aedebc59f70e', 'ext' =>'265'));
		array_push($agents['David Kemp']['agents'], array('name' => 'Chantelle Taylor', 'pass' =>'6784c4e53289ef17e50c01ddcfde6e7f', 'ext' =>'415'));
		array_push($agents['David Kemp']['agents'], array('name' => 'Krystian Fouracre', 'pass' =>'ee137a9d7311a1f9052e531715c52816', 'ext' =>'483'));
		array_push($agents['David Kemp']['agents'], array('name' => 'Jaswant Singh', 'pass' =>'20a65f42cf715cc78b9d1968d740db88', 'ext' =>'484'));
		array_push($agents['David Kemp']['agents'], array('name' => 'Paul Singh', 'pass' =>'5090bb216ffb35348712b7b3b0d08927', 'ext' =>'490'));
		array_push($agents['David Kemp']['agents'], array('name' => 'Hugh Blackwood', 'pass' =>'680399f00de8b033cb308c77a01a8729', 'ext' =>'524'));
		array_push($agents['David Kemp']['agents'], array('name' => 'Adam Cross', 'pass' =>'f561aaf6ef0bf14d4208bb46a4ccb3ad', 'ext' =>'544'));
		array_push($agents['David Kemp']['agents'], array('name' => 'Paul Spencer', 'pass' =>'3b3fedb08cca1b338381280e2ce5b5c4', 'ext' =>'565'));
		array_push($agents['David Kemp']['agents'], array('name' => 'Romone Morgan', 'pass' =>'860c84f47cdd9aa067183ebf8cdb8fa0', 'ext' =>'576'));
		array_push($agents['David Kemp']['agents'], array('name' => 'Matthew Maddock', 'pass' =>'9b7a94bc2eaf908538a2d486d2d86b18', 'ext' =>'585'));
		array_push($agents['Dean Hibbert']['agents'], array('name' => 'Chantel Sweeney', 'pass' =>'64449a99383ce737f807b44683184072', 'ext' =>'258'));
		array_push($agents['Dean Hibbert']['agents'], array('name' => 'Paul Stoddard', 'pass' =>'4427d49496d14c4649765ef78727786e', 'ext' =>'413'));
		array_push($agents['Dean Hibbert']['agents'], array('name' => 'James Kemp', 'pass' =>'12de9e5db31fd6cbd04020acf463bc2a', 'ext' =>'429'));
		array_push($agents['Dean Hibbert']['agents'], array('name' => 'Halle White', 'pass' =>'8bdb97c4b7682d62992c6381bd433a02', 'ext' =>'445'));
		array_push($agents['Dean Hibbert']['agents'], array('name' => 'Carl Sweeney', 'pass' =>'37b4e2d82900d5e94b8da524fbeb33c0', 'ext' =>'448'));
		array_push($agents['Dean Hibbert']['agents'], array('name' => 'Sohail Arif', 'pass' =>'81dc9bdb52d04dc20036dbd8313ed055', 'ext' =>'450'));
		array_push($agents['Dean Hibbert']['agents'], array('name' => 'Gurmohan Singh', 'pass' =>'f2a995bf4805c2c9d426960e75b2887b', 'ext' =>'485'));
		array_push($agents['Dean Hibbert']['agents'], array('name' => 'Kevin Farrell', 'pass' =>'2419c459e9ad2d94f4a5c887b3ca18cb', 'ext' =>'570'));
		array_push($agents['Dean Hibbert']['agents'], array('name' => 'Aimee Armsden', 'pass' =>'b246a03527858e8608a27f886ad14ef9', 'ext' =>'579'));
		array_push($agents['Jon Surrey']['agents'], array('name' => 'Yasir Iqbal', 'pass' =>'5f4dcc3b5aa765d61d8327deb882cf99', 'ext' =>'235'));
		array_push($agents['Jon Surrey']['agents'], array('name' => 'Danny Gale', 'pass' =>'b8c5068a1d849579fea0a0b35c4c9da0', 'ext' =>'292'));
		array_push($agents['Jon Surrey']['agents'], array('name' => 'Antonia Winward', 'pass' =>'82e4010701956651c3f653309879aec4', 'ext' =>'400'));
		array_push($agents['Jon Surrey']['agents'], array('name' => 'Aaron Jackson', 'pass' =>'19aef1fabfb1187d5d9755813de4746e', 'ext' =>'470'));
		array_push($agents['Jon Surrey']['agents'], array('name' => 'Waqar Hassan', 'pass' =>'a40cf1cb0c65e56016954db8104a841e', 'ext' =>'494'));
		array_push($agents['Jon Surrey']['agents'], array('name' => 'Andre Bygrave', 'pass' =>'203ba4eaa9c46f0825f3f6d8bb72eb32', 'ext' =>'498'));
		array_push($agents['Jon Surrey']['agents'], array('name' => 'Mohammed Rashid', 'pass' =>'214beeecb2eee659f521c273a192afec', 'ext' =>'522'));
		array_push($agents['Jon Surrey']['agents'], array('name' => 'Rahman Aftab', 'pass' =>'831c02f1c2862bdc3d0caf79a55d3263', 'ext' =>'529'));
		array_push($agents['Jon Surrey']['agents'], array('name' => 'Shahnawaz Kaleem', 'pass' =>'681570416eb952eac59c3a9d6476d28d', 'ext' =>'582'));
		array_push($agents['Stacy Armitt']['agents'], array('name' => 'Marcel McKenzie', 'pass' =>'73829030e312a5804492202b9a61c527', 'ext' =>'277'));
		array_push($agents['Stacy Armitt']['agents'], array('name' => 'Amitava Ghosh', 'pass' =>'44608134cbc1138fc0d0f562f0f84cf9', 'ext' =>'410'));
		array_push($agents['Stacy Armitt']['agents'], array('name' => 'Akin Raheem', 'pass' =>'5d554bc5f3d2cd182cdd0952b1fb87ca', 'ext' =>'432'));
		array_push($agents['Stacy Armitt']['agents'], array('name' => 'James Johnson', 'pass' =>'3db1a73a245aa55c61204c56c8d99f6d', 'ext' =>'472'));
		array_push($agents['Stacy Armitt']['agents'], array('name' => 'Mohammed Ali', 'pass' =>'2ec199f1e2de31576869a57488e919ad', 'ext' =>'481'));
		array_push($agents['Stacy Armitt']['agents'], array('name' => 'Ben McDougall', 'pass' =>'705a09da923ab9f47fbf539013f3065a', 'ext' =>'547'));
		array_push($agents['Stacy Armitt']['agents'], array('name' => 'Alexander Jones', 'pass' =>'8129b2301c4921e9bc63120feab1e8ac', 'ext' =>'554'));
		array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Sarah Shannon', 'pass' =>'d579ba0fe35141ae95b0e380673be804', 'ext' =>'237'));
		array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Aron James', 'pass' =>'2c49ad8a57c62dcd1db2ad20be8fe02c', 'ext' =>'489'));
		array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Osma Akhtar', 'pass' =>'72d957b76f3b236c523d1d5e39397532', 'ext' =>'492'));
		array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Liam McDowell', 'pass' =>'4c8dac669c3d3f0e71b19883d76ac022', 'ext' =>'535'));
		array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Gemma McGawley', 'pass' =>'404fdd3c9ad06cb1f6ee3a6d8bd20120', 'ext' =>'555'));
		array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Jack Bowcock', 'pass' =>'2ea8e833c127b995e38265827d86ee8a', 'ext' =>'561'));
		array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Amir Garizi', 'pass' =>'7cb7b2d842355e5e2b7ecdc7cc9c5211', 'ext' =>'562'));
		array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Jennifer Juwah', 'pass' =>'5fe8d2bb4abd76ebcf6aaeedc0476595', 'ext' =>'575'));
		array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Samuel Hardy', 'pass' =>'b13eb0f3726605825dd104feddcfbd1e', 'ext' =>'583'));
		array_push($agents['Wayne Brosnan']['agents'], array('name' => 'Warren Rimmer ', 'pass' =>'80e5346badf46bb9be9f396765638c76', 'ext' =>'584'));
	
	
	
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
				(".$agentRole[0]->role_id.", ".$group[0]->group_id.", ".$team_id.", '".$username."', '".$pass."', '".$name."', 1, NULL, NULL, NULL, '2014-08-12 12:02:55', ".$ext.", NULL, NULL, 0, '2014-08-12 12:02:48')");
	
					
				if ($this->db->_error_message()) {
					return "users";
				}
			}
		}
		
		
		//Add Administrators
		$adminRole = $this->db->get_where('user_roles', array('role_name' => 'Administrator'))->result();
		
		$this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
		(".$adminRole[0]->role_id.", ".$group[0]->group_id.", NULL, 'brad.foster', '32250170a0dca92d53ec9624f336ca24', 'Brad Foster', 1, NULL, NULL, '', '2014-09-19 10:16:23', NULL, NULL, NULL, 0, '2014-09-09 10:25:27'),
		(".$adminRole[0]->role_id.", ".$group[0]->group_id.", NULL, 'esteban.correa', '32250170a0dca92d53ec9624f336ca24', 'Esteban Correa', 1, NULL, NULL, '', '2014-09-19 10:16:23', NULL, NULL, NULL, 0, '2014-09-09 10:25:27'),
		(".$adminRole[0]->role_id.", ".$group[0]->group_id.", NULL, 'doug.frost', '32250170a0dca92d53ec9624f336ca24', 'Doug Frost', 1, NULL, NULL, NULL, '2014-08-12 12:02:55', NULL, NULL, NULL, 0, '2014-08-12 12:02:48'),
		(".$adminRole[0]->role_id.", ".$group[0]->group_id.", NULL, 'chris.norman', '32250170a0dca92d53ec9624f336ca24', 'Chris Norman', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', NULL, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$adminRole[0]->role_id.", ".$group[0]->group_id.", NULL, 'rob.wilkinson', '32250170a0dca92d53ec9624f336ca24', 'Rob Wilkinson', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', NULL, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$adminRole[0]->role_id.", ".$group[0]->group_id.", NULL, 'nicci.biernat', '32250170a0dca92d53ec9624f336ca24', 'Nicci Biernat', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', NULL, NULL, NULL, 0, '2014-08-05 08:18:06')");
		
		if ($this->db->_error_message()) {
			return "users";
		}
		
		//Add Team Leaders
		$teamLeaderRole = $this->db->get_where('user_roles', array('role_name' => 'Team Leader'))->result();
		
		$this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
		(".$teamLeaderRole[0]->role_id.", ".$group[0]->group_id.", ".$agents['David Kemp']['team_id'].", 'david.kemp', '32250170a0dca92d53ec9624f336ca24', 'David Kemp', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 622, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$teamLeaderRole[0]->role_id.", ".$group[0]->group_id.", ".$agents['Jon Surrey']['team_id'].", 'jon.surrey', '32250170a0dca92d53ec9624f336ca24', 'Jon Surrey', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 620, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$teamLeaderRole[0]->role_id.", ".$group[0]->group_id.", ".$agents['Wayne Brosnan']['team_id'].", 'wayne.brosnan', '32250170a0dca92d53ec9624f336ca24', 'Wayne Brosnan', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 626, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$teamLeaderRole[0]->role_id.", ".$group[0]->group_id.", ".$agents['Dean Hibbert']['team_id'].", 'dean.hibbert', '32250170a0dca92d53ec9624f336ca24', 'Dean Hibbert', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 206, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$teamLeaderRole[0]->role_id.", ".$group[0]->group_id.", ".$agents['Stacy Armitt']['team_id'].", 'stacy.armitt', '32250170a0dca92d53ec9624f336ca24', 'Stacy Armitt', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 281, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$teamLeaderRole[0]->role_id.", ".$group[0]->group_id.", ".$agents['Craig Williams']['team_id'].", 'craig.williams', '32250170a0dca92d53ec9624f336ca24', 'Craig Williams', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 480, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$teamLeaderRole[0]->role_id.", ".$group[0]->group_id.", NULL, 'tim.martin', '32250170a0dca92d53ec9624f336ca24', 'Tim Martin', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 610, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$teamLeaderRole[0]->role_id.", ".$group[0]->group_id.", ".$agents['Dave Whittaker']['team_id'].", 'dave.whittaker', '32250170a0dca92d53ec9624f336ca24', 'Dave Whittaker', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 201, NULL, NULL, 0, '2014-08-05 08:18:06')");
		
		if ($this->db->_error_message()) {
			return "users";
		}
		
		//Add Client Services
		$clientServicesRole = $this->db->get_where('user_roles', array('role_name' => 'Client Services'))->result();
		
		$this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
		(".$clientServicesRole[0]->role_id.", 1, NULL, 'emma.greenfield', '32250170a0dca92d53ec9624f336ca24', 'Emma Greenfield', 1, NULL, NULL, NULL, '2014-08-12 10:05:48', 616, NULL, NULL, 0, NULL),
		(".$clientServicesRole[0]->role_id.", 1, NULL, 'chris.peddie', '32250170a0dca92d53ec9624f336ca24', 'Chris Peddie', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 612, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$clientServicesRole[0]->role_id.", 1, NULL, 'shahzad.hussain', '32250170a0dca92d53ec9624f336ca24', 'Shahzad Hussain', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 621, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$clientServicesRole[0]->role_id.", 1, NULL, 'sami.elbalbisi', '32250170a0dca92d53ec9624f336ca24', 'Sami El-Balbisi', 1, NULL, NULL, NULL, '2014-08-12 08:29:31', 613, NULL, NULL, 0, '2014-08-05 08:18:06'),
		(".$clientServicesRole[0]->role_id.", 1, NULL, 'kirsty.prince', '32250170a0dca92d53ec9624f336ca24', 'Kirsty Princes', 1, NULL, '', '', '2014-08-12 09:08:57', 557, NULL, NULL, 0, NULL)");
		
		if ($this->db->_error_message()) {
			return "users";
		}
		
		//Add Team Senior
		$teamSeniorRole = $this->db->get_where('user_roles', array('role_name' => 'Team Senior'))->result();
		
		$this->db->query("INSERT INTO `users` (`role_id`, `group_id`, `team_id`, `username`, `password`, `name`, `user_status`, `login_mode`, `user_telephone`, `user_email`, `last_login`, `ext`, `token`, `pass_changed`, `failed_logins`, `last_failed_login`) VALUES
		(".$teamSeniorRole[0]->role_id.", 1, NULL, 'roger.thornton', '32250170a0dca92d53ec9624f336ca24', 'Roger Thornton', 1, NULL, '', '', '2014-08-12 09:08:57', 207, NULL, NULL, 0, NULL)");
		
		if ($this->db->_error_message()) {
			return "users";
		}
	
		return "success";
	}
}