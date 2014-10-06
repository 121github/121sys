<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update extends CI_Migration
{
    public function up()
    {
		$this->db->query("ALTER TABLE `configuration` ADD `theme_folder` VARCHAR( 50 ) NOT NULL");
    }
    public function down()
    {
        $this->db->query("ALTER TABLE `configuration` drop theme_folder");
    }
}