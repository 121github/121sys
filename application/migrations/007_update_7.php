<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_7 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->db->query("INSERT INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'calendar nav', 'Calendar')");
		$this->db->query("ALTER TABLE `appointments` ADD INDEX ( `postcode` )");
$this->db->query("ALTER TABLE `uk_postcodes` CHANGE `lat` `lat` FLOAT( 10, 8 ) NOT NULL");
$this->db->query("ALTER TABLE `uk_postcodes` CHANGE `lng` `lng` FLOAT( 10, 8 ) NOT NULL");

    }

    public function down()
    {
        $this->db->query("delete from permissions where permission_name = 'calendar nav'");
    }

}