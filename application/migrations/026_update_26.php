<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_26 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 26");

        //Adding search actions permissions and edit export permissions
        $this->db->query("ALTER TABLE `email_trigger_recipients` ADD `type` VARCHAR( 5 ) NULL DEFAULT NULL");


    }
    public function down()
    {
	}
    
}