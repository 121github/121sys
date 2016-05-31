<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_148 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $check = $this->db->query("SHOW INDEX FROM `dashboard_reports` where Key_name = 'dashboard_reports_order_uindex'");
        if ($check->num_rows()) {
            $this->db->query("ALTER TABLE `dashboard_reports`
                              DROP INDEX `dashboard_reports_order_uindex`");
        }

        $this->db->query("ALTER TABLE `dashboard_reports`
                              ADD UNIQUE INDEX `dashboard_reports_order_uindex` (`position`, `dashboard_id`)");
    }
}

