<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_2 extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `campaign_features` ADD `permission_id` INT NULL DEFAULT NULL , ADD INDEX (`permission_id`)");
    }
    public function down()
    {
        $this->db->query("ALTER TABLE `campaign_features` drop permission_id");
    }
}