<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_139 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

            $this->db->query("INSERT ignore INTO `datafields` (`datafield_id`, `datafield_title`, `datafield_alias`, `datafield_select`, `datafield_order`, `datafield_group`, `datafield_table`, `campaign`, `modal_keys`) VALUES
(88, 'Distance', 'distance', 'distance', 'distance', 'Record', 'contact_locations', NULL, 1)");
$this->db->query("insert ignore into datatables_table_fields set table_id =1, datafield_id = (select datafield_id from datafields where datafield_title = 'Distance')");
	}
	
}

