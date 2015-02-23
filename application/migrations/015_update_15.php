<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_15 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {
	  $this->firephp->log("starting migration 15");
	    $this->db->query("ALTER TABLE `users` ADD `phone_un` VARCHAR(50) NULL DEFAULT NULL AFTER `last_login`, ADD `phone_pw` VARCHAR(50) NULL DEFAULT NULL AFTER `phone_un`, ADD UNIQUE (`phone_un`)");
		
  }
  
}