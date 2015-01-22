<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_6 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {
    $this->db->query("INSERT INTO `121sys`.`permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'keep records', 'System');");
  }
  public function down()
  {

  }

}
