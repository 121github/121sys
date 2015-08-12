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

        $this->db->query("ALTER TABLE branch ADD map_icon VARCHAR(50) NULL");
        $this->db->query("ALTER TABLE branch ADD color_map VARCHAR(50) NULL");
		  
	}
	
}
