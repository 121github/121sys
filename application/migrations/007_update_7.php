<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_7 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {
    //Alter users table (add reset password token)
    $this->db->query("CREATE TABLE export_to_users
                      (
                          export_forms_id INT NOT NULL,
                          user_id INT NOT NULL,
                      PRIMARY KEY (export_forms_id, user_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
  }
  public function down()
  {
    $this->db->query("DROP TABLE export_to_users");
  }
}
