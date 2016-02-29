
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_99 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
		
		$this->db->query("ALTER TABLE `user_groups` CHANGE `theme_images` `theme_images` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default'");
		$this->db->query("update `user_groups` set theme_images = 'default',theme_color = 'default' where theme_images = ''");
		
	}
	
	 public function down()
    {
		
	}
}