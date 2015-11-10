<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_71 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 71");
		

$this->db->query("CREATE TABLE IF NOT EXISTS  `ci_sessions` (
	session_id varchar(40) DEFAULT '0' NOT NULL,
	ip_address varchar(45) DEFAULT '0' NOT NULL,
	user_agent varchar(120) NOT NULL,
	last_activity int(10) unsigned DEFAULT 0 NOT NULL,
	user_data text NOT NULL,
	PRIMARY KEY (session_id),
	KEY `last_activity_idx` (`last_activity`)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_cart_config` (
  `config_id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `config_order_number_prefix` varchar(50) NOT NULL DEFAULT '',
  `config_order_number_suffix` varchar(50) NOT NULL DEFAULT '',
  `config_increment_order_number` tinyint(1) NOT NULL DEFAULT '0',
  `config_min_order` smallint(5) NOT NULL DEFAULT '0',
  `config_quantity_decimals` tinyint(1) NOT NULL DEFAULT '0',
  `config_quantity_limited_by_stock` tinyint(1) NOT NULL DEFAULT '0',
  `config_increment_duplicate_items` tinyint(1) NOT NULL DEFAULT '0',
  `config_remove_no_stock_items` tinyint(1) NOT NULL DEFAULT '0',
  `config_auto_allocate_stock` tinyint(1) NOT NULL DEFAULT '0',
  `config_save_ban_shipping_items` tinyint(1) NOT NULL DEFAULT '0',
  `config_weight_type` varchar(25) NOT NULL DEFAULT '',
  `config_weight_decimals` tinyint(1) NOT NULL DEFAULT '0',
  `config_display_tax_prices` tinyint(1) NOT NULL DEFAULT '0',
  `config_price_inc_tax` tinyint(1) NOT NULL DEFAULT '0',
  `config_multi_row_duplicate_items` tinyint(1) NOT NULL DEFAULT '0',
  `config_dynamic_reward_points` tinyint(1) NOT NULL DEFAULT '0',
  `config_reward_point_multiplier` double(8,4) NOT NULL DEFAULT '0.0000',
  `config_reward_voucher_multiplier` double(8,4) NOT NULL DEFAULT '0.0000',
  `config_reward_voucher_ratio` smallint(5) NOT NULL DEFAULT '0',
  `config_reward_point_days_pending` smallint(5) NOT NULL DEFAULT '0',
  `config_reward_point_days_valid` smallint(5) NOT NULL DEFAULT '0',
  `config_reward_voucher_days_valid` smallint(5) NOT NULL DEFAULT '0',
  `config_custom_status_1` varchar(50) NOT NULL DEFAULT '',
  `config_custom_status_2` varchar(50) NOT NULL DEFAULT '',
  `config_custom_status_3` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_id`),
  KEY `config_id` (`config_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2");

$this->db->query("INSERT IGNORE INTO `flexicart_cart_config` (`config_id`, `config_order_number_prefix`, `config_order_number_suffix`, `config_increment_order_number`, `config_min_order`, `config_quantity_decimals`, `config_quantity_limited_by_stock`, `config_increment_duplicate_items`, `config_remove_no_stock_items`, `config_auto_allocate_stock`, `config_save_ban_shipping_items`, `config_weight_type`, `config_weight_decimals`, `config_display_tax_prices`, `config_price_inc_tax`, `config_multi_row_duplicate_items`, `config_dynamic_reward_points`, `config_reward_point_multiplier`, `config_reward_voucher_multiplier`, `config_reward_voucher_ratio`, `config_reward_point_days_pending`, `config_reward_point_days_valid`, `config_reward_voucher_days_valid`, `config_custom_status_1`, `config_custom_status_2`, `config_custom_status_3`) VALUES
(1, '', '', 1, 0, 0, 1, 1, 0, 1, 0, 'gram', 0, 1, 1, 0, 1, 10.0000, 0.0100, 250, 14, 365, 365, '', '', '')");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_cart_data` (
  `cart_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'user_id',
  `cart_data_array` text NOT NULL,
  `cart_data_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cart_data_readonly_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cart_data_id`),
  UNIQUE KEY `cart_data_id` (`cart_data_id`) USING BTREE,
  KEY `cart_data_user_fk` (`user_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ");


$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_currency` (
  `curr_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `curr_name` varchar(50) NOT NULL DEFAULT '',
  `curr_exchange_rate` double(8,4) NOT NULL DEFAULT '0.0000',
  `curr_symbol` varchar(25) NOT NULL DEFAULT '',
  `curr_symbol_suffix` tinyint(1) NOT NULL DEFAULT '0',
  `curr_thousand_separator` varchar(10) NOT NULL DEFAULT '',
  `curr_decimal_separator` varchar(10) NOT NULL DEFAULT '',
  `curr_status` tinyint(1) NOT NULL DEFAULT '0',
  `curr_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`curr_id`),
  KEY `curr_id` (`curr_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ");


$this->db->query("INSERT IGNORE INTO `flexicart_currency` (`curr_id`, `curr_name`, `curr_exchange_rate`, `curr_symbol`, `curr_symbol_suffix`, `curr_thousand_separator`, `curr_decimal_separator`, `curr_status`, `curr_default`) VALUES
(1, 'AUD', 2.0000, 'AU $', 0, ',', '.', 1, 0),
(2, 'EUR', 1.1500, '&euro;', 1, '.', ',', 1, 0),
(3, 'GBP', 1.0000, '&pound;', 0, ',', '.', 1, 1),
(4, 'USD', 1.6000, 'US $', 0, ',', '.', 1, 0)");
$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_customers` (
  `user_id` int(5) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL DEFAULT '',
  `user_group_fk` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`) USING BTREE,
  KEY `user_group_fk` (`user_group_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: This is a custom demo table for users.' AUTO_INCREMENT=6 ");
$this->db->query("INSERT IGNORE INTO `flexicart_customers` (`user_id`, `user_name`, `user_group_fk`) VALUES
(1, 'Customer #1', 1),
(2, 'Customer #2', 1),
(3, 'Customer #3', 2),
(4, 'Customer #4', 1),
(5, 'Customer #5', 2)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_discounts` (
  `disc_id` int(11) NOT NULL AUTO_INCREMENT,
  `disc_type_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_method_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_tax_method_fk` tinyint(1) NOT NULL DEFAULT '0',
  `disc_user_acc_fk` int(11) NOT NULL DEFAULT '0',
  `disc_item_fk` int(11) NOT NULL DEFAULT '0' COMMENT 'Item / Product Id',
  `disc_group_fk` int(11) NOT NULL DEFAULT '0',
  `disc_location_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_code` varchar(50) NOT NULL DEFAULT '' COMMENT 'Discount Code',
  `disc_description` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name shown in cart when active',
  `disc_quantity_required` smallint(5) NOT NULL DEFAULT '0' COMMENT 'Quantity required for offer',
  `disc_quantity_discounted` smallint(5) NOT NULL DEFAULT '0' COMMENT 'Quantity affected by offer',
  `disc_value_required` double(8,2) NOT NULL DEFAULT '0.00',
  `disc_value_discounted` double(8,2) NOT NULL DEFAULT '0.00' COMMENT '% discount, flat fee discount, new set price - specified via calculation_fk',
  `disc_recursive` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Discount is repeatable multiple times on one item',
  `disc_non_combinable_discount` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Cannot be applied if any other discount is applied',
  `disc_void_reward_points` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Voids any current reward points',
  `disc_force_ship_discount` tinyint(1) NOT NULL DEFAULT '0',
  `disc_custom_status_1` varchar(50) NOT NULL DEFAULT '',
  `disc_custom_status_2` varchar(50) NOT NULL DEFAULT '',
  `disc_custom_status_3` varchar(50) NOT NULL DEFAULT '',
  `disc_usage_limit` smallint(5) NOT NULL DEFAULT '0' COMMENT 'Number of offers available',
  `disc_valid_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `disc_expire_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `disc_status` tinyint(1) NOT NULL DEFAULT '0',
  `disc_order_by` smallint(1) NOT NULL DEFAULT '100' COMMENT 'Default value of 100 to ensure non set ''order by'' values of zero are not before 1,2,3 etc.',
  PRIMARY KEY (`disc_id`),
  UNIQUE KEY `disc_id` (`disc_id`) USING BTREE,
  KEY `disc_item_fk` (`disc_item_fk`),
  KEY `disc_location_fk` (`disc_location_fk`),
  KEY `disc_zone_fk` (`disc_zone_fk`),
  KEY `disc_method_fk` (`disc_method_fk`) USING BTREE,
  KEY `disc_type_fk` (`disc_type_fk`),
  KEY `disc_group_fk` (`disc_group_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ");


$this->db->query("INSERT IGNORE INTO `flexicart_discounts` (`disc_id`, `disc_type_fk`, `disc_method_fk`, `disc_tax_method_fk`, `disc_user_acc_fk`, `disc_item_fk`, `disc_group_fk`, `disc_location_fk`, `disc_zone_fk`, `disc_code`, `disc_description`, `disc_quantity_required`, `disc_quantity_discounted`, `disc_value_required`, `disc_value_discounted`, `disc_recursive`, `disc_non_combinable_discount`, `disc_void_reward_points`, `disc_force_ship_discount`, `disc_custom_status_1`, `disc_custom_status_2`, `disc_custom_status_3`, `disc_usage_limit`, `disc_valid_date`, `disc_expire_date`, `disc_status`, `disc_order_by`) VALUES
(1, 1, 11, 1, 0, 0, 0, 1, 0, 'FREE-UK-SHIPPING', 'Discount Code "FREE-UK-SHIPPING" - Free UK shipping.', 0, 0, 0.00, 0.00, 0, 0, 1, 1, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(2, 2, 12, 1, 0, 0, 0, 0, 0, '10-PERCENT', 'Discount Code "10-PERCENT" - 10% off grand total.', 0, 0, 0.00, 10.00, 0, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(3, 2, 13, 1, 0, 0, 0, 0, 0, '10-FIXED-RATE', 'Discount Code "10-FIXED-RATE" - &pound;10 off grand total.', 0, 0, 0.00, 10.00, 0, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(4, 2, 13, 1, 0, 0, 0, 0, 0, '', 'Discount Summary, Spend over &pound;1,000, get &pound;100 off.', 1, 1, 1000.00, 100.00, 0, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(5, 2, 11, 1, 0, 0, 0, 0, 0, '', 'Discount Summary, Spend over &pound;500, get free worldwide shipping.', 0, 0, 500.00, 0.00, 0, 0, 0, 1, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(6, 2, 12, 1, 0, 0, 0, 0, 0, '', 'Discount Summary, Logged in users get 5% off total.', 0, 0, 0.00, 5.00, 0, 0, 0, 0, '1', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(7, 1, 1, 1, 0, 301, 0, 0, 0, '', 'Discount Item #301, 10% off original price (&pound;24.99).', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9997, '2015-11-06 11:56:09', '2016-01-06 11:56:09', 1, 1),
(8, 1, 2, 1, 0, 302, 0, 0, 0, '', 'Discount Item #302, Fixed price of &pound;5.00 off original price of &pound;12.50.', 1, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(9, 1, 3, 1, 0, 303, 0, 0, 0, '', 'Discount Item #303, New price of &pound;15.00, item was &pound;25.00.', 1, 1, 0.00, 15.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(10, 1, 3, 1, 0, 304, 0, 0, 0, '', 'Discount Item #304, Buy 2, get 1 free.', 3, 1, 0.00, 0.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(11, 1, 1, 1, 0, 305, 0, 0, 0, '', 'Discount Item #305, Buy 1, get 1 @ 50% off.', 2, 1, 0.00, 50.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(12, 1, 2, 1, 0, 306, 0, 0, 0, '', 'Discount Item #306, Buy 2 @ &pound;15.00, get 1 with &pound;5.00 off.', 3, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(13, 1, 3, 1, 0, 307, 0, 0, 0, '', 'Discount Item #307, Buy 5 @ &pound;10.00, get 2 for &pound;7.00.', 7, 2, 0.00, 7.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(14, 1, 6, 1, 0, 308, 0, 1, 0, '', 'Discount Item #308, Buy 3, get free UK shipping on these items (Other items still charged).', 3, 3, 0.00, 0.00, 1, 0, 0, 1, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(15, 1, 1, 1, 0, 309, 0, 0, 0, '', 'Discount Item #309, Spend over &pound;75.00 on this item, get 10% off this items total.', 1, 1, 75.00, 10.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(16, 1, 2, 1, 0, 310, 0, 0, 0, '', 'Discount Item #310, Spend over &pound;100.00 on this item, get &pound;10.00 off this items total.', 1, 1, 100.00, 10.00, 0, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(17, 1, 3, 1, 0, 0, 1, 0, 0, '', 'Discount Group: Discount Items #311, #312 and #313 - buy 3, get cheapest item free.', 3, 1, 0.00, 0.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(18, 1, 1, 1, 0, 314, 0, 0, 0, '', 'Discount Item #314, 10% off original price - but for logged in users only.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '1', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(19, 1, 1, 1, 0, 315, 0, 0, 0, '', 'Discount Item #315, 10% off original price - but removes the items reward points (Normally 200 points).', 1, 1, 0.00, 10.00, 1, 0, 1, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(20, 1, 1, 1, 0, 316, 0, 0, 0, '', 'Discount Item #316, 10% off original price - but applies to first item only (Non recursive quantity discount).', 1, 1, 0.00, 10.00, 0, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(21, 1, 1, 1, 0, 317, 0, 1, 0, '', 'Discount Item #317, 10% off original price - but applies to orders being shipped to the UK only.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(22, 1, 1, 1, 0, 318, 0, 0, 0, '', 'Discount Item #318, 10% off original price - but cannot be applied if other discounts exist.', 1, 1, 0.00, 10.00, 1, 1, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(23, 1, 1, 1, 0, 401, 0, 0, 0, '', 'Discount Tax #401, get 10% off original price (&pound;10.00) - Method #1.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(24, 1, 1, 2, 0, 402, 0, 0, 0, '', 'Discount Tax #402, get 10% off original price (&pound;10.00) - Method #2.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(25, 1, 1, 3, 0, 403, 0, 0, 0, '', 'Discount Tax #403, get 10% off original price (&pound;10.00) - Method #3.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(26, 1, 2, 1, 0, 404, 0, 0, 0, '', 'Discount Tax #404, get set price of (&pound;5.00) off original price (&pound;10.00) - Method #1.', 1, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(27, 1, 2, 2, 0, 405, 0, 0, 0, '', 'Discount Tax #405, get set price of (&pound;5.00) off original price (&pound;10.00) - Method #2.', 1, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(28, 1, 2, 3, 0, 406, 0, 0, 0, '', 'Discount Tax #406, get set price of (&pound;5.00) off original price (&pound;10.00) - Method #3.', 1, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:09', '2016-01-06 11:56:09', 1, 1),
(29, 1, 3, 1, 0, 407, 0, 0, 0, '', 'Discount Tax #407, get for new price of &pound;7.50 (Original price &pound;10.00) - Method #1.', 1, 1, 0.00, 7.50, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:10', '2016-01-06 11:56:10', 1, 1),
(30, 1, 3, 2, 0, 408, 0, 0, 0, '', 'Discount Tax #408, get for new price of &pound;7.50 (Original price &pound;10.00) - Method #2.', 1, 1, 0.00, 7.50, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:10', '2016-01-06 11:56:10', 1, 1),
(31, 1, 3, 3, 0, 409, 0, 0, 0, '', 'Discount Tax #409, get for new price of &pound;7.50 (Original price &pound;10.00) - Method #3.', 1, 1, 0.00, 7.50, 1, 0, 0, 0, '', '', '', 9999, '2015-11-04 11:56:10', '2016-01-06 11:56:10', 1, 1),
(32, 1, 3, 0, 0, 1, 0, 0, 0, '', 'Database Item #1: Buy 2, Get 1 Free.', 2, 1, 0.00, 0.00, 1, 0, 0, 0, '', '', '', 9, '2015-11-04 11:56:10', '2015-12-04 11:56:10', 1, 1),
(33, 1, 1, 0, 0, 3, 0, 0, 0, '', 'Database Item #3: 10% off original price.', 1, 1, 0.00, 10.00, 1, 0, 0, 0, '', '', '', 9, '2015-11-04 11:56:10', '2015-11-30 11:56:10', 1, 1),
(34, 1, 2, 0, 0, 5, 0, 0, 0, '', 'Database Item #5: Get &pound;5.00 off original price.', 1, 1, 0.00, 5.00, 1, 0, 0, 0, '', '', '', 9, '2015-11-04 11:56:10', '2015-11-27 11:56:10', 1, 1),
(35, 3, 14, 0, 1, 0, 0, 0, 0, '2AC2AE9AEF923F4', 'Reward Voucher: 2AC2AE9AEF923F4', 0, 0, 0.00, 5.00, 0, 0, 1, 0, '', '', '', 1, '2015-11-04 11:56:10', '2016-01-06 11:56:10', 1, 100),
(36, 3, 14, 0, 4, 0, 0, 0, 0, '088F148041B66A9', 'Reward Voucher: 088F148041B66A9', 0, 0, 0.00, 10.00, 0, 0, 1, 0, '', '', '', 0, '2015-11-04 11:56:10', '2016-01-06 11:56:10', 1, 100)");
$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_discount_calculation` (
  `disc_calculation_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_calculation` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`disc_calculation_id`),
  UNIQUE KEY `disc_calculation_id` (`disc_calculation_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: Do not alter the order or id''s of records in table.' AUTO_INCREMENT=4 ");

$this->db->query("INSERT IGNORE INTO `flexicart_discount_calculation` (`disc_calculation_id`, `disc_calculation`) VALUES
(1, 'Percentage Based'),
(2, 'Flat Fee'),
(3, 'New Value')");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_discount_columns` (
  `disc_column_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_column` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`disc_column_id`),
  UNIQUE KEY `disc_column_id` (`disc_column_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: Do not alter the order or id''s of records in table.' AUTO_INCREMENT=7 ");

$this->db->query("INSERT IGNORE INTO `flexicart_discount_columns` (`disc_column_id`, `disc_column`) VALUES
(1, 'Item Price'),
(2, 'Item Shipping'),
(3, 'Summary Item Total'),
(4, 'Summary Shipping Total'),
(5, 'Summary Total'),
(6, 'Summary Total (Voucher)')");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_discount_groups` (
  `disc_group_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_group` varchar(255) NOT NULL DEFAULT '',
  `disc_group_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`disc_group_id`),
  UNIQUE KEY `disc_group_id` (`disc_group_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ");

$this->db->query("INSERT IGNORE INTO `flexicart_discount_groups` (`disc_group_id`, `disc_group`, `disc_group_status`) VALUES
(1, 'Demo Group : Items #311, #312 and #313', 1)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_discount_group_items` (
  `disc_group_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `disc_group_item_group_fk` int(11) NOT NULL DEFAULT '0',
  `disc_group_item_item_fk` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`disc_group_item_id`),
  UNIQUE KEY `disc_group_item_id` (`disc_group_item_id`) USING BTREE,
  KEY `disc_group_item_group_fk` (`disc_group_item_group_fk`) USING BTREE,
  KEY `disc_group_item_item_fk` (`disc_group_item_item_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ");


$this->db->query("INSERT IGNORE INTO `flexicart_discount_group_items` (`disc_group_item_id`, `disc_group_item_group_fk`, `disc_group_item_item_fk`) VALUES
(1, 1, 311),
(2, 1, 312),
(3, 1, 313)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_discount_methods` (
  `disc_method_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_method_type_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_method_column_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_method_calculation_fk` smallint(5) NOT NULL DEFAULT '0',
  `disc_method` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`disc_method_id`),
  UNIQUE KEY `disc_method_id` (`disc_method_id`) USING BTREE,
  KEY `disc_method_column_fk` (`disc_method_column_fk`) USING BTREE,
  KEY `disc_method_calculation_fk` (`disc_method_calculation_fk`) USING BTREE,
  KEY `disc_method_type_fk` (`disc_method_type_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: Do not alter the order or id''s of records in table.' AUTO_INCREMENT=15 ");

$this->db->query("INSERT IGNORE INTO `flexicart_discount_methods` (`disc_method_id`, `disc_method_type_fk`, `disc_method_column_fk`, `disc_method_calculation_fk`, `disc_method`) VALUES
(1, 1, 1, 1, 'Item Price - Percentage Based'),
(2, 1, 1, 2, 'Item Price - Flat Fee'),
(3, 1, 1, 3, 'Item Price - New Value'),
(4, 1, 2, 1, 'Item Shipping - Percentage Based'),
(5, 1, 2, 2, 'Item Shipping - Flat Fee'),
(6, 1, 2, 3, 'Item Shipping - New Value'),
(7, 2, 3, 1, 'Summary Item Total - Percentage Based'),
(8, 2, 3, 2, 'Summary Item Total - Flat Fee'),
(9, 2, 4, 1, 'Summary Shipping Total - Percentage Based'),
(10, 2, 4, 2, 'Summary Shipping Total - Flat Fee'),
(11, 2, 4, 3, 'Summary Shipping Total - New Value'),
(12, 2, 5, 1, 'Summary Total - Percentage Based'),
(13, 2, 5, 2, 'Summary Total - Flat Fee'),
(14, 3, 6, 2, 'Summary Total - Flat Fee (Voucher)')");
$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_discount_tax_methods` (
  `disc_tax_method_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_tax_method` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`disc_tax_method_id`),
  UNIQUE KEY `disc_tax_method_id` (`disc_tax_method_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: Do not alter the order or id''s of records in table.' AUTO_INCREMENT=4 ");


$this->db->query("INSERT IGNORE INTO `flexicart_discount_tax_methods` (`disc_tax_method_id`, `disc_tax_method`) VALUES
(1, 'Apply Tax Before Discount '),
(2, 'Apply Discount Before Tax'),
(3, 'Apply Discount Before Tax, Add Original Tax')");



$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_discount_types` (
  `disc_type_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `disc_type` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`disc_type_id`),
  UNIQUE KEY `disc_type_id` (`disc_type_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: Do not alter the order or id''s of records in table.' AUTO_INCREMENT=4 ");

$this->db->query("INSERT IGNORE INTO `flexicart_discount_types` (`disc_type_id`, `disc_type`) VALUES
(1, 'Item Discount'),
(2, 'Summary Discount'),
(3, 'Reward Voucher')");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_category_id` smallint(6) DEFAULT NULL,
  `item_subcategory_id` smallint(6) DEFAULT NULL,
  `item_name` varchar(50) NOT NULL DEFAULT '',
  `item_short_description` varchar(255) NOT NULL,
  `item_full_description` text NOT NULL,
  `item_price` double(6,2) NOT NULL DEFAULT '0.00',
  `item_weight` double(6,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_id` (`item_id`),
  KEY `item_cat_fk` (`item_category_id`) USING BTREE,
  KEY `item_subcategory_id` (`item_subcategory_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Note: This is a custom demo table for items.' AUTO_INCREMENT=13 ");


$this->db->query("INSERT IGNORE INTO `flexicart_items` (`item_id`, `item_category_id`, `item_subcategory_id`, `item_name`, `item_short_description`, `item_full_description`, `item_price`, `item_weight`) VALUES
(1, 1, 1, 'Cream of tomato', '', '', 1.00, 0.32),
(2, 1, 1, 'Cream of chicken', '', '', 1.00, 0.32),
(3, 1, 1, 'Cream of muchroom', '', '', 1.00, 0.32),
(4, 1, 1, 'Oxtail', '', '', 1.00, 0.32),
(5, 1, 2, 'Tomato with chilli', '', '', 1.00, 0.32),
(6, 1, 2, 'Chicken with thai spices', '', '', 1.00, 0.32),
(7, 1, 2, 'Beef and paprika', '', '', 1.00, 0.32),
(8, 1, 4, 'Beef and veg', '', '', 1.00, 0.32),
(9, 1, 4, 'Chicken and veg', '', '', 1.00, 0.32),
(10, 1, 3, 'Tomato', '', '', 1.00, 0.32),
(11, 1, 3, 'Chicken', '', '', 1.00, 0.32),
(12, 1, 3, 'Chicken noodle', '', '', 1.00, 0.32)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_item_categories` (
  `item_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_category_name` varchar(255) NOT NULL,
  `item_catgory_description` varchar(255) NOT NULL,
  `item_category_image` varchar(255) NOT NULL,
  PRIMARY KEY (`item_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ");


$this->db->query("INSERT IGNORE INTO `flexicart_item_categories` (`item_category_id`, `item_category_name`, `item_catgory_description`, `item_category_image`) VALUES
(1, 'Soup', '', '')");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_item_stock` (
  `stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_item_fk` int(11) NOT NULL DEFAULT '0',
  `stock_quantity` smallint(5) NOT NULL DEFAULT '0',
  `stock_auto_allocate_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stock_id`),
  UNIQUE KEY `stock_id` (`stock_id`) USING BTREE,
  KEY `stock_item_fk` (`stock_item_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ");


$this->db->query("INSERT IGNORE INTO `flexicart_item_stock` (`stock_id`, `stock_item_fk`, `stock_quantity`, `stock_auto_allocate_status`) VALUES
(1, 112, 20, 1),
(2, 113, 0, 1),
(3, 1, 95, 1),
(4, 2, 98, 1),
(5, 3, 100, 1),
(6, 4, 100, 1),
(7, 5, 99, 1)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_item_subcategories` (
  `item_subcategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_subcategory_name` varchar(255) NOT NULL,
  `item_subcategory_description` varchar(255) NOT NULL,
  `item_subcategory_image` varchar(255) NOT NULL,
  `item_category_id` int(11) NOT NULL,
  PRIMARY KEY (`item_subcategory_id`),
  KEY `item_category_id` (`item_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ");


$this->db->query("INSERT IGNORE INTO `flexicart_item_subcategories` (`item_subcategory_id`, `item_subcategory_name`, `item_subcategory_description`, `item_subcategory_image`, `item_category_id`) VALUES
(1, 'Classics', '', '', 1),
(2, 'Black Label', '', '', 1),
(3, 'Cup Soups', '', '', 1),
(4, 'Farmers Market', '', '', 1)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_locations` (
  `loc_id` int(11) NOT NULL AUTO_INCREMENT,
  `loc_type_fk` smallint(5) NOT NULL DEFAULT '0',
  `loc_parent_fk` int(11) NOT NULL DEFAULT '0',
  `loc_ship_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `loc_tax_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `loc_name` varchar(50) NOT NULL DEFAULT '',
  `loc_status` tinyint(1) NOT NULL DEFAULT '0',
  `loc_ship_default` tinyint(1) NOT NULL DEFAULT '0',
  `loc_tax_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`loc_id`),
  UNIQUE KEY `loc_id` (`loc_id`) USING BTREE,
  KEY `loc_type_fk` (`loc_type_fk`) USING BTREE,
  KEY `loc_tax_zone_fk` (`loc_tax_zone_fk`),
  KEY `loc_ship_zone_fk` (`loc_ship_zone_fk`),
  KEY `loc_parent_fk` (`loc_parent_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ");


$this->db->query("INSERT IGNORE INTO `flexicart_locations` (`loc_id`, `loc_type_fk`, `loc_parent_fk`, `loc_ship_zone_fk`, `loc_tax_zone_fk`, `loc_name`, `loc_status`, `loc_ship_default`, `loc_tax_default`) VALUES
(1, 1, 0, 0, 4, 'United Kingdom (EU)', 1, 1, 1),
(2, 1, 0, 1, 4, 'France (EU)', 1, 0, 0),
(3, 1, 0, 1, 4, 'Germany (EU)', 1, 0, 0),
(4, 1, 0, 2, 4, 'Portugal (EU)', 1, 0, 0),
(5, 1, 0, 2, 4, 'Spain (EU)', 1, 0, 0),
(6, 1, 0, 3, 5, 'Norway (Non EU)', 1, 0, 0),
(7, 1, 0, 3, 5, 'Switzerland (Non EU)', 1, 0, 0),
(8, 1, 0, 0, 0, 'Australia', 1, 0, 0),
(9, 1, 0, 0, 0, 'Canada', 1, 0, 0),
(10, 1, 0, 0, 0, 'United States', 1, 0, 0),
(11, 2, 8, 0, 0, 'NSW', 1, 0, 0),
(12, 2, 8, 0, 0, 'Queensland', 1, 0, 0),
(13, 2, 8, 0, 0, 'Victoria', 1, 0, 0),
(14, 2, 10, 0, 0, 'California', 1, 0, 0),
(15, 2, 10, 0, 0, 'Florida', 1, 0, 0),
(16, 2, 10, 0, 0, 'New York', 1, 0, 0),
(17, 2, 10, 0, 0, 'Pennsylvania', 1, 0, 0),
(18, 3, 16, 0, 0, '10101', 1, 0, 0),
(19, 3, 16, 0, 0, '10102', 1, 0, 0)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_location_type` (
  `loc_type_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `loc_type_parent_fk` smallint(5) NOT NULL DEFAULT '0',
  `loc_type_name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`loc_type_id`),
  UNIQUE KEY `loc_type_id` (`loc_type_id`),
  KEY `loc_type_parent_fk` (`loc_type_parent_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ");

$this->db->query("INSERT IGNORE INTO `flexicart_location_type` (`loc_type_id`, `loc_type_parent_fk`, `loc_type_name`) VALUES
(1, 0, 'Country'),
(2, 1, 'State'),
(3, 2, 'Post / Zip Code')");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_location_zones` (
  `lzone_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `lzone_name` varchar(50) NOT NULL DEFAULT '',
  `lzone_description` longtext NOT NULL,
  `lzone_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lzone_id`),
  UNIQUE KEY `lzone_id` (`lzone_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ");


$this->db->query("INSERT IGNORE INTO `flexicart_location_zones` (`lzone_id`, `lzone_name`, `lzone_description`, `lzone_status`) VALUES
(1, 'Shipping Europe Zone 1', 'Example Zone 1 includes France and Germany', 1),
(2, 'Shipping Europe Zone 2', 'Example Zone 2 includes Portugal and Spain', 1),
(3, 'Shipping Europe Zone 3', 'Example Zone 3 includes Norway and Switzerland', 1),
(4, 'Tax EU Zone', 'Example Tax Zone for EU countries', 1),
(5, 'Tax Non EU Zone', 'Example Tax Zone for Non EU European countries', 1)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_order_customers` (
  `user_id` int(5) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL DEFAULT '',
  `user_group_fk` smallint(5) NOT NULL DEFAULT '0',
  `password` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`) USING BTREE,
  KEY `user_group_fk` (`user_group_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='lets contacts login to view their order details and status' AUTO_INCREMENT=6 ");

$this->db->query("INSERT IGNORE INTO `flexicart_order_customers` (`user_id`, `user_name`, `user_group_fk`, `password`, `email`) VALUES
(1, 'Customer #1', 1, '', ''),
(2, 'Customer #2', 1, '', ''),
(3, 'Customer #3', 2, '', ''),
(4, 'Customer #4', 1, '', ''),
(5, 'Customer #5', 2, '', '')");
$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_order_details` (
  `ord_det_id` int(11) NOT NULL AUTO_INCREMENT,
  `ord_det_order_number_fk` varchar(25) NOT NULL DEFAULT '',
  `ord_det_cart_row_id` varchar(32) NOT NULL DEFAULT '',
  `ord_det_item_fk` int(11) NOT NULL DEFAULT '0',
  `ord_det_item_name` varchar(255) NOT NULL DEFAULT '',
  `ord_det_item_option` varchar(255) NOT NULL DEFAULT '',
  `ord_det_quantity` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_non_discount_quantity` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_discount_quantity` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_stock_quantity` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_price` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_price_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_discount_price` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_discount_price_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_discount_description` varchar(255) NOT NULL DEFAULT '',
  `ord_det_tax_rate` double(8,4) NOT NULL DEFAULT '0.0000',
  `ord_det_tax` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_tax_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_shipping_rate` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_weight` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_weight_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_reward_points` int(10) NOT NULL DEFAULT '0',
  `ord_det_reward_points_total` int(10) NOT NULL DEFAULT '0',
  `ord_det_status_message` varchar(255) NOT NULL DEFAULT '',
  `ord_det_quantity_shipped` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_quantity_cancelled` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_det_shipped_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ord_det_demo_user_note` varchar(255) NOT NULL DEFAULT '',
  `ord_det_demo_sku` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`ord_det_id`),
  UNIQUE KEY `ord_det_id` (`ord_det_id`) USING BTREE,
  KEY `ord_det_order_number_fk` (`ord_det_order_number_fk`) USING BTREE,
  KEY `ord_det_item_fk` (`ord_det_item_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ");


$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_order_status` (
  `ord_status_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `ord_status_description` varchar(50) NOT NULL DEFAULT '',
  `ord_status_cancelled` tinyint(1) NOT NULL DEFAULT '0',
  `ord_status_save_default` tinyint(1) NOT NULL DEFAULT '0',
  `ord_status_resave_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ord_status_id`),
  KEY `ord_status_id` (`ord_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ");


$this->db->query("INSERT IGNORE INTO `flexicart_order_status` (`ord_status_id`, `ord_status_description`, `ord_status_cancelled`, `ord_status_save_default`, `ord_status_resave_default`) VALUES
(1, 'Awaiting Payment', 0, 1, 0),
(2, 'New Order', 0, 0, 1),
(3, 'Processing Order', 0, 0, 0),
(4, 'Order Complete', 0, 0, 0),
(5, 'Order Cancelled', 1, 0, 0)");
$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_order_summary` (
  `ord_order_number` varchar(25) NOT NULL DEFAULT '',
  `ord_cart_data_fk` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `urn` int(11) DEFAULT NULL,
  `ord_item_summary_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_item_summary_savings_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_shipping` varchar(100) NOT NULL DEFAULT '',
  `ord_shipping_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_item_shipping_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_summary_discount_desc` varchar(255) NOT NULL DEFAULT '',
  `ord_summary_savings_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_savings_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_surcharge_desc` varchar(255) NOT NULL DEFAULT '',
  `ord_surcharge_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_reward_voucher_desc` varchar(255) NOT NULL DEFAULT '',
  `ord_reward_voucher_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_tax_rate` varchar(25) NOT NULL DEFAULT '',
  `ord_tax_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_sub_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_total` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_total_rows` int(10) NOT NULL DEFAULT '0',
  `ord_total_items` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_total_weight` double(10,2) NOT NULL DEFAULT '0.00',
  `ord_total_reward_points` int(10) NOT NULL DEFAULT '0',
  `ord_currency` varchar(25) NOT NULL DEFAULT '',
  `ord_exchange_rate` double(8,4) NOT NULL DEFAULT '0.0000',
  `ord_status` tinyint(1) NOT NULL DEFAULT '0',
  `ord_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ord_demo_bill_name` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_bill_company` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_bill_address_01` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_bill_address_02` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_bill_city` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_bill_state` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_bill_post_code` varchar(25) NOT NULL DEFAULT '',
  `ord_demo_bill_country` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_ship_name` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_ship_company` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_ship_address_01` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_ship_address_02` varchar(75) NOT NULL DEFAULT '',
  `ord_demo_ship_city` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_ship_state` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_ship_post_code` varchar(25) NOT NULL DEFAULT '',
  `ord_demo_ship_country` varchar(50) NOT NULL DEFAULT '',
  `ord_demo_email` varchar(255) NOT NULL DEFAULT '',
  `ord_demo_phone` varchar(25) NOT NULL DEFAULT '',
  `ord_demo_comments` longtext NOT NULL,
  PRIMARY KEY (`ord_order_number`),
  UNIQUE KEY `ord_order_number` (`ord_order_number`) USING BTREE,
  KEY `ord_cart_data_fk` (`ord_cart_data_fk`) USING BTREE,
  KEY `ord_user_fk` (`user_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");


$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_reward_points_converted` (
  `rew_convert_id` int(10) NOT NULL AUTO_INCREMENT,
  `rew_convert_ord_detail_fk` int(10) NOT NULL DEFAULT '10',
  `rew_convert_discount_fk` varchar(50) NOT NULL DEFAULT '',
  `rew_convert_points` int(10) NOT NULL DEFAULT '10',
  `rew_convert_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`rew_convert_id`),
  UNIQUE KEY `rew_convert_id` (`rew_convert_id`) USING BTREE,
  KEY `rew_convert_discount_fk` (`rew_convert_discount_fk`),
  KEY `rew_convert_ord_detail_fk` (`rew_convert_ord_detail_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ");

$this->db->query("INSERT IGNORE INTO `flexicart_reward_points_converted` (`rew_convert_id`, `rew_convert_ord_detail_fk`, `rew_convert_discount_fk`, `rew_convert_points`, `rew_convert_date`) VALUES
(1, 1, '35', 400, '2015-11-01 20:49:48'),
(2, 2, '35', 100, '2015-11-03 00:36:28'),
(3, 7, '36', 1000, '2015-11-04 04:23:08')");
$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_shipping_item_rules` (
  `ship_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `ship_item_item_fk` int(11) NOT NULL DEFAULT '0',
  `ship_item_location_fk` smallint(5) NOT NULL DEFAULT '0',
  `ship_item_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `ship_item_value` double(8,4) DEFAULT NULL,
  `ship_item_separate` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicate if item should have a shipping rate calculated specifically for it.',
  `ship_item_banned` tinyint(1) NOT NULL DEFAULT '0',
  `ship_item_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ship_item_id`),
  UNIQUE KEY `ship_item_id` (`ship_item_id`) USING BTREE,
  KEY `ship_item_zone_fk` (`ship_item_zone_fk`) USING BTREE,
  KEY `ship_item_location_fk` (`ship_item_location_fk`) USING BTREE,
  KEY `ship_item_item_fk` (`ship_item_item_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ");

$this->db->query("INSERT IGNORE INTO `flexicart_shipping_item_rules` (`ship_item_id`, `ship_item_item_fk`, `ship_item_location_fk`, `ship_item_zone_fk`, `ship_item_value`, `ship_item_separate`, `ship_item_banned`, `ship_item_status`) VALUES
(1, 104, 1, 0, 0.0000, 0, 0, 1),
(2, 106, 0, 0, NULL, 1, 0, 1),
(3, 107, 1, 0, NULL, 0, 1, 1),
(4, 108, 1, 0, NULL, 0, 2, 1),
(5, 108, 2, 0, NULL, 0, 2, 1)");
$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_shipping_options` (
  `ship_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `ship_name` varchar(50) NOT NULL DEFAULT '',
  `ship_description` varchar(50) NOT NULL DEFAULT '',
  `ship_location_fk` smallint(5) NOT NULL DEFAULT '0',
  `ship_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `ship_inc_sub_locations` tinyint(1) NOT NULL DEFAULT '0',
  `ship_tax_rate` double(7,4) DEFAULT NULL,
  `ship_discount_inclusion` tinyint(1) NOT NULL DEFAULT '0',
  `ship_status` tinyint(1) NOT NULL DEFAULT '0',
  `ship_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ship_id`),
  UNIQUE KEY `ship_id` (`ship_id`) USING BTREE,
  KEY `ship_zone_fk` (`ship_zone_fk`) USING BTREE,
  KEY `ship_location_fk` (`ship_location_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ");

$this->db->query("INSERT IGNORE INTO `flexicart_shipping_options` (`ship_id`, `ship_name`, `ship_description`, `ship_location_fk`, `ship_zone_fk`, `ship_inc_sub_locations`, `ship_tax_rate`, `ship_discount_inclusion`, `ship_status`, `ship_default`) VALUES
(1, 'UK Standard Shipping', '2-3 Days', 1, 0, 0, NULL, 1, 1, 1),
(2, 'UK Recorded Shipping', '2-3 Days', 1, 0, 0, NULL, 0, 1, 0),
(3, 'UK Special Shipping', 'Next Day', 1, 0, 0, NULL, 0, 1, 0),
(4, 'UK Collection', 'Available Next Day', 1, 0, 0, NULL, 0, 1, 0),
(5, 'EU Zone 1: Standard Shipping', '3-4 Days', 0, 1, 0, NULL, 0, 1, 0),
(6, 'EU Zone 1: Special Shipping', '1-2 Days', 0, 1, 0, NULL, 0, 1, 0),
(7, 'EU Zone 2: Standard Shipping', '4-6 Days', 0, 2, 0, NULL, 0, 1, 0),
(8, 'EU Zone 2: Special Shipping', '2-4 Days', 0, 2, 0, NULL, 0, 1, 0),
(9, 'EU Zone 3: Standard Shipping', '5-8 Days', 0, 3, 0, NULL, 0, 1, 0),
(10, 'EU Zone 3: Special Shipping', '3-5 Days', 0, 3, 0, NULL, 0, 1, 0),
(11, 'Australia (Non NSW) Shipping', '12 Days', 8, 0, 0, NULL, 0, 1, 0),
(12, 'Australia NSW Shipping', '10 Days', 11, 0, 0, NULL, 0, 1, 0),
(13, 'Canada Shipping', '8 Days', 9, 0, 0, NULL, 0, 1, 0),
(14, 'United States (Non CA or NY) Shipping', '8 Days', 10, 0, 0, NULL, 0, 1, 0),
(15, 'New York State Shipping', '6 Days', 16, 0, 1, NULL, 0, 1, 0),
(16, 'California State Shipping', '8 Days', 14, 0, 0, NULL, 0, 1, 0),
(17, 'New York City Shipping', '6 Days', 18, 0, 0, NULL, 0, 1, 0)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_shipping_rates` (
  `ship_rate_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `ship_rate_ship_fk` smallint(5) NOT NULL DEFAULT '0',
  `ship_rate_value` double(8,2) NOT NULL DEFAULT '0.00',
  `ship_rate_tare_wgt` double(8,2) NOT NULL DEFAULT '0.00',
  `ship_rate_min_wgt` double(8,2) NOT NULL DEFAULT '0.00',
  `ship_rate_max_wgt` double(8,2) NOT NULL DEFAULT '9999.00',
  `ship_rate_min_value` double(10,2) NOT NULL DEFAULT '0.00',
  `ship_rate_max_value` double(10,2) NOT NULL DEFAULT '9999.00',
  `ship_rate_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ship_rate_id`),
  UNIQUE KEY `ship_rate_id` (`ship_rate_id`) USING BTREE,
  KEY `ship_rate_ship_fk` (`ship_rate_ship_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ");

$this->db->query("INSERT IGNORE INTO `flexicart_shipping_rates` (`ship_rate_id`, `ship_rate_ship_fk`, `ship_rate_value`, `ship_rate_tare_wgt`, `ship_rate_min_wgt`, `ship_rate_max_wgt`, `ship_rate_min_value`, `ship_rate_max_value`, `ship_rate_status`) VALUES
(1, 1, 3.95, 2.00, 0.00, 50.00, 0.00, 500.00, 1),
(2, 1, 4.50, 2.00, 50.00, 150.00, 0.00, 500.00, 1),
(3, 1, 5.25, 2.00, 150.00, 500.00, 0.00, 500.00, 1),
(4, 2, 5.10, 2.00, 0.00, 50.00, 0.00, 500.00, 1),
(5, 2, 5.75, 2.00, 50.00, 150.00, 0.00, 500.00, 1),
(6, 2, 6.40, 2.00, 150.00, 500.00, 0.00, 500.00, 1),
(7, 3, 7.50, 10.00, 0.00, 500.00, 0.00, 1000.00, 1),
(8, 3, 10.95, 10.00, 500.00, 0.00, 0.00, 9999.00, 1),
(9, 4, 0.00, 10.00, 0.00, 0.00, 0.00, 9999.00, 1),
(10, 5, 7.25, 10.00, 0.00, 250.00, 0.00, 500.00, 1),
(11, 6, 15.75, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(12, 7, 7.75, 10.00, 0.00, 250.00, 0.00, 500.00, 1),
(13, 8, 16.25, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(14, 9, 8.50, 10.00, 0.00, 250.00, 0.00, 500.00, 1),
(15, 10, 20.10, 0.00, 0.00, 0.00, 0.00, 0.00, 1),
(16, 11, 16.50, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(17, 12, 14.90, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(18, 13, 14.50, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(19, 14, 14.50, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(20, 15, 13.25, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(21, 16, 15.30, 10.00, 0.00, 0.00, 0.00, 0.00, 1),
(22, 17, 10.55, 10.00, 0.00, 0.00, 0.00, 0.00, 1)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_tax` (
  `tax_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `tax_location_fk` smallint(5) NOT NULL DEFAULT '0',
  `tax_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `tax_name` varchar(25) NOT NULL DEFAULT '',
  `tax_rate` double(7,4) NOT NULL DEFAULT '0.0000',
  `tax_status` tinyint(1) NOT NULL DEFAULT '0',
  `tax_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tax_id`),
  UNIQUE KEY `tax_id` (`tax_id`),
  KEY `tax_zone_fk` (`tax_zone_fk`),
  KEY `tax_location_fk` (`tax_location_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ");


$this->db->query("INSERT IGNORE INTO `flexicart_tax` (`tax_id`, `tax_location_fk`, `tax_zone_fk`, `tax_name`, `tax_rate`, `tax_status`, `tax_default`) VALUES
(1, 0, 4, 'VAT', 20.0000, 1, 1),
(2, 0, 5, 'No Tax (Non EU)', 0.0000, 1, 0),
(3, 16, 0, 'Tax New York', 4.0000, 1, 0),
(4, 14, 0, 'Tax California', 8.2500, 1, 0),
(5, 10, 0, 'Tax (Other US)', 6.0000, 1, 0),
(6, 18, 0, 'Tax New York City', 8.3700, 1, 0),
(7, 8, 0, 'GST', 10.0000, 1, 0),
(8, 9, 0, 'HST', 8.0000, 1, 0)");

$this->db->query("CREATE TABLE IF NOT EXISTS `flexicart_tax_item_rates` (
  `tax_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_item_item_fk` int(11) NOT NULL DEFAULT '0',
  `tax_item_location_fk` smallint(5) NOT NULL DEFAULT '0',
  `tax_item_zone_fk` smallint(5) NOT NULL DEFAULT '0',
  `tax_item_rate` double(7,4) NOT NULL DEFAULT '0.0000',
  `tax_item_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tax_item_id`),
  UNIQUE KEY `tax_item_id` (`tax_item_id`) USING BTREE,
  KEY `tax_item_zone_fk` (`tax_item_zone_fk`),
  KEY `tax_item_location_fk` (`tax_item_location_fk`),
  KEY `tax_item_item_fk` (`tax_item_item_fk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ");

$this->db->query("INSERT IGNORE INTO `flexicart_tax_item_rates` (`tax_item_id`, `tax_item_item_fk`, `tax_item_location_fk`, `tax_item_zone_fk`, `tax_item_rate`, `tax_item_status`) VALUES
(1, 110, 0, 0, 0.0000, 1)");

$this->db->query("ALTER TABLE `flexicart_item_subcategories`
  ADD CONSTRAINT `item_subcategories_ibfk_1` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`item_category_id`) ON DELETE CASCADE ON UPDATE CASCADE");

		
	}
	
}