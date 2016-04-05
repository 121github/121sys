<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_115 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()

    {

            $this->db->query("insert ignore into permissions values('','google calendar','Calendar','Enable the google calendar API features')");
	}
	 public function down()

    {
	}
}