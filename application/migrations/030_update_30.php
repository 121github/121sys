<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_30 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 30");

		$this->db->query("ALTER TABLE `email_history` ADD `pending` TINYINT DEFAULT 0 NOT NULL");
	}

    public function down()
    {
        $this->db->query("ALTER TABLE `email_history` DROP `pending`");
    }
}
?>