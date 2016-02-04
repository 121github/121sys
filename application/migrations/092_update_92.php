<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_92 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		$this->firephp->log("starting migration 92");
		 $check = $this->db->query("SHOW COLUMNS FROM `datatables_columns` LIKE 'c10'");
        if(!$check->num_rows()){
			$this->db->query("INSERT IGNORE INTO `datatables_columns` (
`column_id` ,
`column_title` ,
`column_alias` ,
`column_select` ,
`column_order` ,
`column_group` ,
`column_table`
)
VALUES 
(NULL , 'c7', '', 'c7', 'c7', 'Extra Fields', 'record_details'),
(NULL , 'c8', '', 'c8', 'c8', 'Extra Fields', 'record_details'),
(NULL , 'c9', '', 'c9', 'c9', 'Extra Fields', 'record_details'),
(NULL , 'c10', '', 'c10', 'c10', 'Extra Fields', 'record_details'),
(NULL , 'd4', '', 'd4', 'd4', 'Extra Fields', 'record_details'),
(NULL , 'd5', '', 'd5', 'd5', 'Extra Fields', 'record_details'),
(NULL , 'd6', '', 'd6', 'd6', 'Extra Fields', 'record_details'),
(NULL , 'd7', '', 'd7', 'd7', 'Extra Fields', 'record_details'),
(NULL , 'd8', '', 'd8', 'd8', 'Extra Fields', 'record_details'),
(NULL , 'd9', '', 'd9', 'd9', 'Extra Fields', 'record_details'),
(NULL , 'd10', '', 'd10', 'd10', 'Extra Fields', 'record_details'),
(NULL , 'dt4', '', 'dt4', 'dt4', 'Extra Fields', 'record_details'),
(NULL , 'dt5', '', 'dt5', 'dt5', 'Extra Fields', 'record_details'),
(NULL , 'dt6', '', 'dt6', 'dt6', 'Extra Fields', 'record_details'),
(NULL , 'dt7', '', 'dt7', 'dt7', 'Extra Fields', 'record_details'),
(NULL , 'dt8', '', 'dt8', 'dt8', 'Extra Fields', 'record_details'),
(NULL , 'dt9', '', 'dt9', 'dt9', 'Extra Fields', 'record_details'),
(NULL , 'dt10', '', 'dt10', 'dt10', 'Extra Fields', 'record_details'),
(NULL , 'n4', '', 'n4', 'n4', 'Extra Fields', 'record_details'),
(NULL , 'n5', '', 'n5', 'n5', 'Extra Fields', 'record_details'),
(NULL , 'n6', '', 'n6', 'n6', 'Extra Fields', 'record_details'),
(NULL , 'n7', '', 'n7', 'n7', 'Extra Fields', 'record_details'),
(NULL , 'n8', '', 'n8', 'n8', 'Extra Fields', 'record_details'),
(NULL , 'n9', '', 'n9', 'n9', 'Extra Fields', 'record_details'),
(NULL , 'n10', '', 'n10', 'n10', 'Extra Fields', 'record_details')");
		}
	}
	 public function down(){
		 
	 }
	 
}