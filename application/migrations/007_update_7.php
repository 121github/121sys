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
        $this->db->query("INSERT INTO `121sys`.`permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES (NULL, 'calendar nav', 'Calendar')");


    }

    public function down()
    {
        $this->db->query("delete from permissions where permission_name = 'calendar nav'");
    }

}