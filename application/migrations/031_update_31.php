<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_31 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 31");

		$this->db->query("ALTER TABLE `question_options` ADD `sort` INT NULL DEFAULT NULL");
	}

    public function down()
    {
        $this->db->query("ALTER TABLE `question_options` DROP `sort`");
    }
}
?>