<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_12 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {
	  $this->firephp->log("starting migration 12");
      //Add suppressed parked_code
      $this->db->query("INSERT INTO `park_codes` (parked_code, park_reason) VALUES (5,'Duplicated')");
  }
  public function down()
  {
    $this->db->query("DELETE FROM `park_codes` WHERE parked_code = 5");
  }
}

