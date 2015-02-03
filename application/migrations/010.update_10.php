<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_10 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {
	  $this->firephp->log("starting migration 10");
    //changing default date types for contacts and companies
      $this->db->query("ALTER TABLE `contacts` CHANGE `date_updated` `date_updated` DATETIME NULL DEFAULT NULL");
	  $this->db->query("ALTER TABLE `contacts` CHANGE `date_created` `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ");
	  
	   $this->db->query("ALTER TABLE `companies` CHANGE `date_updated` `date_updated` DATETIME NULL DEFAULT NULL");
	  $this->db->query("ALTER TABLE `companies` CHANGE `date_created` `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ");

  }
  public function down()
  {
	  
  }
}

