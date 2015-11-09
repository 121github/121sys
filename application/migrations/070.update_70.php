<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_70 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 70");

$this->db->query("INSERT ignore  INTO `campaign_features` (`feature_id`, `feature_name`, `panel_path`, `permission_id`) VALUES (NULL, 'Orders', 'orders.php', NULL)");

	}
	
}