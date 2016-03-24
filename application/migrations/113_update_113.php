<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_113 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()

    {

            $this->db->query("alter table `custom_panel_values` add unique(data_id,field_id)");

	}
	 public function down()

    {
	}
	}