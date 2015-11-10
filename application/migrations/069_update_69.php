
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_69 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 69");

$this->db->query("update`campaigns` set campaign_group_id = null");

	}
	
}