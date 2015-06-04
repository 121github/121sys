<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_11 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
		
    }

    public function up(){
		 $this->firephp->log("starting migration 11");
	$this->db->query("INSERT  ignore INTO `campaign_features` (`feature_id`, `feature_name`, `panel_path`, `permission_id`) VALUES (NULL, 'Related', 'related.php', NULL)");

	//remove any duplicates before we add the unique key to prevent any more being entered
	$this->Database_model->remove_dupes("campaigns_to_features","campaign_id","feature_id");
	$this->db->query("alter table `campaigns_to_features` add unique(`campaign_id`, `feature_id`)");
	}
}