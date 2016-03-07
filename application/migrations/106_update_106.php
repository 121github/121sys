<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_106 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()

    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `dashboard_filters` (
                              `dashboard_filters_id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                              `filter_name` varchar(50) NOT NULL,
                              `filter_value` varchar(255) NOT NULL,
                              `editable` TINYINT(1) DEFAULT 1 NOT NULL,
                              `dashboard_id` INT NOT NULL
                            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
        );
	}

    public function down()
    {
		
	}
}