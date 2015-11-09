
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

$this->db->query("ALTER TABLE `campaigns` DROP FOREIGN KEY `campaigns_ibfk_1`; ALTER TABLE `campaigns` ADD CONSTRAINT `campaigns_ibfk_1` FOREIGN KEY (`campaign_group_id`) REFERENCES `campaign_groups`(`campaign_group_id`) ON DELETE SET NULL ON UPDATE CASCADE");

	}
	
}