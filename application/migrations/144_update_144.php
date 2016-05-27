<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_144 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $check = $this->db->query("SHOW INDEX FROM `record_details` where Key_name = 'urn_2'");
        if (!$check->num_rows()) {
            $this->db->query("DROP INDEX `urn_2` ON `record_details`");
        }
    }

}

