<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_40 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
		$this->load->dbforge();
    }

    public function up(){
        $this->firephp->log("starting migration 40");
 
	}
	
}
