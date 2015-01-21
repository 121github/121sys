<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_4 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {
    //Alter users table (add reset password token)
    $this->db->query("ALTER TABLE `users` ADD `reset_pass_token` TEXT NULL");
  }
  public function down()
  {
    $this->db->query("ALTER TABLE `users` DROP `reset_pass_token`");
  }
}
