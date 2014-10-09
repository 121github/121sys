<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_3 extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `users` ADD `reload_session` BOOLEAN NOT NULL DEFAULT FALSE");
    }
    public function down()
    {
        $this->db->query("ALTER TABLE `users` drop reload_session");
		$this->db->query("TRUNCATE TABLE `role_permissions`");
    }
}