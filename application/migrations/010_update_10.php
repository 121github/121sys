
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_10 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 10");

        //add travelMode
        $this->db->query("ALTER TABLE `records` ADD `record_color` VARCHAR(6) NULL DEFAULT NULL");
	}

    public function down()
    {
        $this->db->query("ALTER TABLE `records` DROP `record_color`");
    }
	
}