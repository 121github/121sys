
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_111 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()

    {
        $check = $this->db->query("SHOW COLUMNS FROM `email_history` LIKE 'inbound'");
        if (!$check->num_rows()) {
            $this->db->query("ALTER TABLE `email_history` ADD `inbound` TINYINT( 1 ) NOT NULL DEFAULT '0'");
        }
	}
	 public function down()

    {
	}
	}