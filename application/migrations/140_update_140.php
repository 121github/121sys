<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_140 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->db->query("INSERT ignore INTO `appointment_types` (`appointment_type_id`, `appointment_type`, `is_default`, `icon`) VALUES
('', 'Imported', 0, 'fa fa-google')");
        $this->db->query("INSERT ignore INTO `datafields` (`datafield_id`, `datafield_title`, `datafield_alias`, `datafield_select`, `datafield_order`, `datafield_group`, `datafield_table`, `campaign`, `modal_keys`) VALUES (NULL, 'Start', 'start', 'date_format(a.start,%d/%m/%Y %H:%i)', 'date_format(a.start,%a %D %b %Y %l%:%i%p)', 'Appointments', 'appointments', NULL, '1')");
        $this->db->query("update modal_datafields set datafield_id = (select datafield_id from datafields where datafield_title='Start') where datafield_id = (select datafield_id from datafields where datafield_title='Start Date')");

    }

}

