<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_56 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		  $this->firephp->log("starting migration 56");
		$check = $this->db->query("select * from `datatables_columns` where column_title = 'Appointment Type'");
		if(!$check->num_rows()){

$this->db->query("INSERT INTO `datatables_columns` (`column_id`, `column_title`, `column_alias`, `column_select`, `column_order`, `column_group`, `column_table`) VALUES
(53, 'Appointment Type', 'appointment_type', 'appointment_type', 'appointment_type', 'Appointment', 'appointment_types')");

		}
		
	}
	
}
