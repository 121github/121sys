<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_89 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 89");
		$this->db->query("CREATE TABLE IF NOT EXISTS `datatables_views` (
  `view_id` int(11) NOT NULL AUTO_INCREMENT,
  `view_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `view_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `selected` BOOLEAN NOT NULL,
  PRIMARY KEY (`view_id`),
  KEY `user_id` (`user_id`),
  KEY `table_id` (`table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

$this->db->query("CREATE TABLE IF NOT EXISTS `datatables_view_columns` (
	`id` int(11) not null AUTO_INCREMENT,
  `view_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `view_id` (`view_id`,`column_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

$this->db->query("ALTER TABLE `datatables_view_columns` ADD FOREIGN KEY ( `view_id` ) REFERENCES `datatables_views` (`view_id`) ON DELETE CASCADE ON UPDATE CASCADE");

	}
	
	 public function down()
    {
		$this->db->query("drop table if exists datatables_view_columns");
		$this->db->query("drop table if exists datatables_views");
	}
	
}