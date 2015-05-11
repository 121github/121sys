<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_3 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {

    //Alter table suppression
    $this->db->query("ALTER TABLE `suppression` ADD `date_updated` TIMESTAMP NULL");
    $this->db->query("ALTER TABLE `suppression` ADD `reason` TEXT NULL");
    $this->db->query("ALTER TABLE `suppression` DROP INDEX `outcome_id`");
    $this->db->query("ALTER TABLE `suppression` DROP `outcome_id`");
    $this->db->query("ALTER TABLE `suppression` DROP INDEX `campaign_id`");
    $this->db->query("ALTER TABLE `suppression` DROP `campaign_id`");

    //Add suppressed parked_code
    //$this->db->query("INSERT INTO `park_codes` (parked_code, park_reason) VALUES (4,'Suppressed')");

    //Create suppression_by_campaign table
    $this->db->query("CREATE TABLE suppression_by_campaign
                      (
                          suppression_id INT NOT NULL,
                          campaign_id INT NOT NULL,
                          PRIMARY KEY (suppression_id, campaign_id)
                      );");
  }
  public function down()
  {
    $this->db->query("ALTER TABLE `suppression` DROP `reason`");
    $this->db->query("ALTER TABLE `suppression` ADD `outcome_id` INT(11) NOT NULL");
    $this->db->query("ALTER TABLE `suppression` ADD INDEX `outcome_id`");
    $this->db->query("ALTER TABLE `suppression` ADD `campaign_id` INT(11) NOT NULL");
    $this->db->query("ALTER TABLE `suppression` ADD INDEX `campaign_id`");

    //$this->db->query("DELETE FROM `park_codes` WHERE parked_code = 4");

    $this->db->query("DROP TABLE suppression_by_campaign");
  }
}
