<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_78 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 78");
		$check = $this->db->query("SHOW COLUMNS FROM `user_groups` LIKE 'theme_images'");
		if(!$check->num_rows()){
		$this->db->query("ALTER TABLE `user_groups` CHANGE `theme_folder` `theme_images` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		}
			$check3 = $this->db->query("SHOW COLUMNS FROM `user_groups` LIKE 'theme_color'");
			if(!$check3->num_rows()){
		$this->db->query("ALTER TABLE `user_groups` ADD `theme_color` VARCHAR( 50 ) NOT NULL");
		$this->db->query("update user_groups set theme_color = theme_images");
			}
		$check2 = $this->db->query("SHOW COLUMNS FROM `users` LIKE 'theme_color'");
			if(!$check2->num_rows()){
			$this->db->query("ALTER TABLE `users` ADD `theme_color` VARCHAR( 50 ) NOT NULL");
			
			}

	}
	
}