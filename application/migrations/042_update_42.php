<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_42 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 42");

        //add new permissions
		$this->db->query("ALTER TABLE `history` ADD `call_direction` BOOLEAN NULL DEFAULT NULL");
        $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'set call direction', 'Records')");
    }

}