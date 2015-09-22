<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_55 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		  $this->firephp->log("starting migration 55");
		$check = $this->db->query("select * from `datatables_columns` where column_title = 'Start Date'");
		if(!$check->num_rows()){
		$this->db->query("INSERT INTO `datatables_columns` (`column_id`, `column_title`, `column_alias`, `column_select`, `column_order`, `column_group`, `column_table`) VALUES
(44, 'Start Date', 'start', 'date_format(a.start,''%d/%m/%Y'')', 'a.start', 'Appointment', 'appointments'),
(45, 'End Time', 'end_time', 'date_format(a.end,''%l:%i %p'')', 'time(a.end)', 'Appointment', 'appointments'),
(46, 'App. Postcode', 'appointment_postcode', 'a.postcode', 'a.postcode', 'Appointment', 'appointments'),
(47, 'App. Created', 'appointment_created', 'date_format(a.date_added,''%d/%m/%Y %H:%i'')', 'a.date_added', 'Appointment', 'appointments'),
(48, 'App. Title', 'appointment_title', 'a.title', 'a.title', 'Appointment', 'appointments'),
(49, 'App. Notes', 'appointment_notes', 'if(char_length(a.text)>100,concat(substring(a.text,1,100),''...''),substring(a.text,1,100))', 'a.text', 'Appointment', 'appointments'),
(50, 'Start Time', 'start_time', 'date_format(a.start,''%l:%i %p'')', 'time(a.start)', 'Appointment', 'appointments'),
(51, 'Created By', 'created_by', 'appointment_users.name', 'appointment_users.name', 'Appointment', 'appointments'),
(52, 'Attendees', 'attendees', 'group_concat(au.name)', 'au.name', 'Appointment', 'appointments')");

$this->db->query("INSERT INTO `datatables_table_columns` (`table_id`, `column_id`) VALUES
(3, 53),
(3, 52),
(3, 51),
(3, 50),
(3, 49),
(3, 48),
(3, 47),
(3, 46),
(3, 45),
(3, 44),
(3, 41),
(3, 29),
(3, 27),
(3, 25),
(3, 23),
(3, 21),
(3, 20),
(3, 19),
(3, 18),
(3, 17),
(3, 16),
(3, 13),
(3, 12),
(3, 9),
(3, 8),
(3, 7),
(3, 6),
(3, 5),
(3, 4),
(3, 3),
(3, 2),
(3, 1)");
		}

	}
	
}