<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_138 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

            $this->db->query("insert ignore into outcomes set outcome = 'Appointment Imported',enable_select = null");
      
	}
	
}