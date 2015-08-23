<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_47 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
			
		
      $this->firephp->log("starting migration 47");

      $this->db->query("ALTER TABLE appointments_ics ADD send_from VARCHAR(255) NOT NULL");

    }

}