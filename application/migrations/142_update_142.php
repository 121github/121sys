<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_142 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
	$check = $this->db->query("SHOW INDEX FROM `datatables_table_names` where Column_name = 'table_name'");
	if($check->num_rows()==0){
$this->db->query("ALTER TABLE `datatables_table_names` ADD UNIQUE (`table_name`)");
	}

$this->db->query("INSERT ignore INTO `datatables_table_names` (`table_id`,`table_name`) VALUES (NULL , 'Surveys')");

	}
	
}

