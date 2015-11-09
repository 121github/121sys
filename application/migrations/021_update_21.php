
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_21 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 21");	
		$this->db->query("ALTER TABLE `record_details_fields` CHANGE `field_name` `field_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
    }

}