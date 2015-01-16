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
    $this->db->query("ALTER TABLE `records` ADD `urn_copied` INT NULL DEFAULT NULL");
    $this->db->query("ALTER TABLE `companies` ADD `company_copied` INT NULL DEFAULT NULL");
    $this->db->query("ALTER TABLE `contacts` ADD `contact_copied` INT NULL DEFAULT NULL");
  }
  public function down()
  {
    $this->db->query("ALTER TABLE `records` DROP `urn_copied`");
    $this->db->query("ALTER TABLE `companies` DROP `company_copied`");
    $this->db->query("ALTER TABLE `contacts` DROP `contact_copied`");
  }

}
