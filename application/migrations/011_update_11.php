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
	  $this->firephp->log("starting migration 11");
    //changing default date types for contacts and companies
      $this->db->query("ALTER TABLE `records` CHANGE `date_added` `date_added` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
  }
  public function down()
  {
	  
  }
}

