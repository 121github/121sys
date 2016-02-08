<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_94 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		$this->db->query("CREATE TABLE IF NOT EXISTS `apis` (
  `api_id` int(11) NOT NULL AUTO_INCREMENT,
  `api_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `api_token` smallint(6) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`api_id`),
  UNIQUE KEY `api_name` (`api_name`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
	}
	public function down()
    {
	}
}