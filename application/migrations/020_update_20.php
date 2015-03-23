<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_20 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		
        $this->firephp->log("starting migration 19");
        //Adding search actions permissions and edit export permissions
        $this->db->query("ALTER TABLE `companies` CHANGE `conumber` `conumber` VARCHAR( 11 ) NULL DEFAULT NULL");


    }

    public function down()
    {
      
    }

}