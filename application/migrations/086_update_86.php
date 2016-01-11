<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_86 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 86");

            $this->db->query("CREATE TABLE IF NOT EXISTS `record_comments` (
  `urn` int(11) NOT NULL,
  `last_comment` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`urn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		
		$this->db->query("insert ignore into record_comments select lch.urn,lch.comments from (select max(history_id) mhid,urn from history where comments <> '' group by urn) last_history join history lch on last_history.mhid = lch.history_id");
		
		$this->db->query("replace into `datatables_columns` (`column_id`, `column_title`, `column_alias`, `column_select`, `column_order`, `column_group`, `column_table`) VALUES
(58, 'Last Comment', 'last_comment', 'record_comments.last_comment', 'record_comments.last_comment', 'Record', 'record_comments')");

		
    }
    public function down()
    {
    }
}