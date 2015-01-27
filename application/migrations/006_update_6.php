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
    //Alter users table (add reset password token)
    $this->db->query("CREATE TABLE export_forms
                      (
                          export_forms_id INT NOT NULL AUTO_INCREMENT,
                          name VARCHAR(50) NOT NULL,
                          description VARCHAR(255) NOT NULL,
                          header TEXT NOT NULL,
                          query TEXT NOT NULL,
                          order by VARCHAR(25) NULL,
                          group by VARCHAR(25) NULL,
                          date_filter VARCHAR(25),
                          campaign_filter VARCHAR(25),
                      PRIMARY KEY (export_forms_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
  }
  public function down()
  {
    $this->db->query("DROP TABLE export_forms");
  }
}
