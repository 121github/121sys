

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_18 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {
	  $this->firephp->log("starting migration 18");
	    $this->db->query("ALTER TABLE `webform_answers` ADD `updated_on` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `urn`");
		
		$this->db->query("ALTER TABLE `webform_answers` ADD `updated_by` INT NULL DEFAULT NULL AFTER `updated_on`");
		
  }
  
}
  