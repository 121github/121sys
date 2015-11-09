
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_58 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		  $this->firephp->log("starting migration 58");
		$this->db->query("ALTER TABLE `hours` CHANGE `date` `date` DATE NOT NULL");
	}
}
