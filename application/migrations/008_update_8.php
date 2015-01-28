<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_8 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {
    //Adding search actions permissions and edit export permissions
      $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES
                      (110, 'search actions', 'Search'),
                      (111, 'edit export', 'Data')");

      $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES
                    (1, 110),
                    (1, 111)");
  }
  public function down()
  {
      $this->db->query("DELETE FROM permissions WHERE permission_id IN (110, 111)");
      $this->db->query("DELETE FROM role_permissions WHERE permission_id IN (110, 111)");
  }
}
