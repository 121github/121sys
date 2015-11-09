
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_13 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 13");


        //add campaign map_icon
        $this->db->query("ALTER TABLE `campaigns` ADD `map_icon` VARCHAR(50) NULL DEFAULT NULL");

        //add record map_icon
        $this->db->query("ALTER TABLE `records` ADD `map_icon` VARCHAR(50) NULL DEFAULT NULL");
	}

    public function down()
    {
        $this->db->query("ALTER TABLE `campaigns` DROP `map_icon`");
        $this->db->query("ALTER TABLE `records` DROP `map_icon`");
    }
	
}