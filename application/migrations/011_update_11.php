
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_11 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->db->query("ALTER TABLE `campaigns` ADD `record_layout` VARCHAR(20) NOT NULL DEFAULT '2col.php' AFTER `campaign_name`");
		$this->db->query("ALTER TABLE `campaigns` ADD `logo` VARCHAR(100) NULL DEFAULT NULL AFTER `record_layout`");
       }

    public function down()
    {
   
    }

}