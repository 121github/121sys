<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_72 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 72");
		

$this->db->query("INSERT ignore INTO `datatables_columns` (`column_id`, `column_title`, `column_alias`, `column_select`, `column_order`, `column_group`, `column_table`) VALUES
(58, 'Last Comment', 'last_comments', 'last_comments.comments', 'last_comments.comments', 'Record', 'last_comments');
");
$this->db->query("INSERT ignore INTO `datatables_table_columns` (`table_id`, `column_id`) VALUES ('1', '58'), ('3', '58')");
	
	}
	
}


