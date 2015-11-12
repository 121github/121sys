<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_73 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 73");
$this->db->query("replace INTO `datatables_columns` (`column_id`, `column_title`, `column_alias`, `column_select`, `column_order`, `column_group`, `column_table`) VALUES
(59, 'Contact Email', 'contact_email', 'con.email', 'con.email', 'Contact', 'contacts')
");
$this->db->query("replace INTO `datatables_table_columns` (`table_id`, `column_id`) VALUES ('1', '59'), ('3', '59')");
	
	}
	
}
