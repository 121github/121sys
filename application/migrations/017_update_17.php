<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_17 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 17");

$this->db->query("ALTER TABLE  `outcomes` ADD  `set_parked_code` INT NULL AFTER  `set_progress` ,
ADD INDEX (`set_parked_code`)");

	}
	
}