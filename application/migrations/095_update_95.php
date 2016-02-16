<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_95 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }


    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `modals` (
                              `modal_id` int(11) NOT NULL AUTO_INCREMENT,
                              `modal_name` varchar(50) NOT NULL,
                              PRIMARY KEY (`modal_id`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3"
        );


        $this->db->query("INSERT IGNORE INTO `modals` (`modal_id`, `modal_name`) VALUES
                            (1, 'record'),
                            (2, 'appointment')"
        );


        $this->db->query("CREATE TABLE IF NOT EXISTS `modal_columns` (
                          `column_id` int(11) NOT NULL AUTO_INCREMENT,
                          `modal_config_id` int(11) NOT NULL,
                          `column_title` varchar(50) NOT NULL,
                          `field_display` varchar(5) NOT NULL DEFAULT 'table',
                          `table_class` varchar(100) NOT NULL DEFAULT 'table',
                          `list_icon` varchar(20) NOT NULL DEFAULT 'fa-circle',
                          `column_sort` int(11) DEFAULT NULL,
                          PRIMARY KEY (`column_id`)
                        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5"
        );


        $this->db->query("INSERT IGNORE INTO `modal_columns` (`column_id`, `modal_config_id`, `column_title`, `field_display`, `table_class`, `list_icon`, `column_sort`) VALUES
                            (1, 1, 'Details', 'table', 'table small', '', 1),
                            (2, 1, 'Status', 'table', 'table small', '', 2),
                            (3, 2, 'Appointment Details', 'table', 'table small', '', 1)"
        );
		$this->db->query("ALTER TABLE `modal_columns` ADD INDEX ( `modal_config_id` )");

        $this->db->query("CREATE TABLE IF NOT EXISTS `modal_config` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `modal_id` int(11) NOT NULL,
                              `user_id` int(11) DEFAULT NULL,
                              `campaign_id` int(11) DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `modal_id` (`modal_id`,`user_id`,`campaign_id`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4"
        );


        $this->db->query("INSERT IGNORE INTO `modal_config` (`id`, `modal_id`, `user_id`, `campaign_id`) VALUES
                            (1, 1, NULL, NULL),
                            (2, 2, NULL, NULL)"
        );

        $this->db->query("CREATE TABLE IF NOT EXISTS `modal_datafields` (
  `column_id` int(11) NOT NULL,
  `datafield_id` int(11) NOT NULL,
  `sort` int(11) DEFAULT NULL,
  UNIQUE KEY `column_id` (`column_id`,`datafield_id`),
  KEY `datafield_id` (`datafield_id`),
  KEY `column_id_2` (`column_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1"
        );

        $this->db->query("INSERT IGNORE INTO `modal_datafields` (`column_id`, `datafield_id`, `sort`) VALUES
                            (1, 1, 1),
                            (1, 3, 10),
                            (1, 4, 15),
                            (1, 15, 5),
                            (1, 17, 1),
                            (1, 42, 20),
                            (1, 58, 25),
                            (2, 2, 20),
                            (2, 5, 5),
                            (2, 6, 10),
                            (2, 8, 30),
                            (3, 1, 5),
                            (3, 3, 5),
                            (3, 44, 10),
                            (3, 48, 15),
                            (3, 49, 20),
                            (3, 52, 25),
                            (3, 53, 30),
                            (3, 86, 35)");

$this->db->query("DROP TABLE IF EXISTS `datatables_user_columns`");
     $db = $this->db->database;
	$check = $this->db->query("show tables from `$db` where Tables_in_$db = 'datatables_columns'");
if($check->num_rows()){
        $this->db->query("RENAME TABLE `datatables_columns` TO `datafields`");
        $this->db->query("RENAME TABLE `datatables_view_columns` TO `datatables_view_fields`");
        $this->db->query("RENAME TABLE `datatables_table_columns` TO `datatables_table_fields`");

        $this->db->query("ALTER TABLE `datatables_table_fields` CHANGE `column_id` `datafield_id` INT( 11 ) NOT NULL");

        $this->db->query("ALTER TABLE `datafields` CHANGE `column_id` `datafield_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
                            CHANGE `column_title` `datafield_title` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
                            CHANGE `column_alias` `datafield_alias` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
                            CHANGE `column_select` `datafield_select` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
                            CHANGE `column_order` `datafield_order` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
                            CHANGE `column_group` `datafield_group` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
                            CHANGE `column_table` `datafield_table` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");

        $this->db->query("ALTER TABLE `datatables_view_fields` CHANGE `column_id` `datafield_id` INT( 11 ) NOT NULL");
}
        $this->db->query("
                CREATE TABLE IF NOT EXISTS export_graphs
                (
                    graph_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    export_forms_id INT NOT NULL,
                    name VARCHAR(50) NOT NULL,
                    type VARCHAR(25) NOT NULL,
                    x_value VARCHAR(50) NOT NULL,
                    y_value VARCHAR(50),
                    z_value VARCHAR(50),
                    CONSTRAINT export_graphs_fk_export_id FOREIGN KEY (export_forms_id) REFERENCES export_forms (export_forms_id) ON DELETE CASCADE ON UPDATE CASCADE
                )
			");

        $check = $this->db->query("SHOW COLUMNS FROM `dashboard_reports` LIKE 'show_default'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE dashboard_reports ADD show_default VARCHAR(25) NULL DEFAULT 'data'");
        }
		

		  /* $this->db->query("ALTER TABLE `modal_datafields`
                          ADD CONSTRAINT `modal_datafields_ibfk_2` FOREIGN KEY (`datafield_id`) REFERENCES `datafields` (`datafield_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                          ADD CONSTRAINT `modal_datafields_ibfk_1` FOREIGN KEY (`column_id`) REFERENCES `modal_columns` (`column_id`) ON DELETE CASCADE ON UPDATE CASCADE");
						  */
    }

    public function down()
    {

    }


}