
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_5 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 5");

        //add new permissions
        $this->db->query("alter table record_planner add unique(user_id,urn)");     
	}
	
}