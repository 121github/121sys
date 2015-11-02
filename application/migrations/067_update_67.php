
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_67 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 67");
$check = $this->db->query("SHOW COLUMNS FROM `sms_templates` LIKE 'custom_sender'");
		if(!$check->num_rows()){
        $this->db->query("ALTER TABLE `sms_templates` ADD `custom_sender` VARCHAR( 25 ) NOT NULL DEFAULT ''");
		$this->db->query("INSERT ignore INTO `sms_sender` (`sender_id`, `name`) VALUES ('0', 'Automatic')");
		$this->db->query("update sms_sender set sender_id = '0' where name='Automatic'");
		
		}

    }
}