<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_4 extends CI_Migration
{
    public function up()
    {
		$this->db->query("ALTER TABLE `user_groups` ADD `theme_folder` VARCHAR( 50 ) NOT NULL");
    }
    public function down()
    {
        $this->db->query("ALTER TABLE `user_groups` drop theme_folder");
    }
}