<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_2 extends CI_Migration
{
    
    public function __construct()
    {
        $this->load->model('Database_model');
    }
    
    public function up()
    {
		$this->firephp->log("test");
		$this->db->query("ALTER TABLE `email_triggers` ADD `template_id` INT NULL DEFAULT NULL ,
ADD INDEX ( `template_id` )");
$this->db->query("ALTER TABLE `users` ADD `attendee` TINYINT( 1 ) NOT NULL DEFAULT '0'");
	}
	    public function down()
    {
		$this->db->query("ALTER TABLE `email_triggers` DROP `template_id`");
			$this->db->query("ALTER TABLE `users` DROP `attendee`");
	}
	
}
	