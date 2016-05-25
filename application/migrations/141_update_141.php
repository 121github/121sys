<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_141 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

        $this->db->query("update user_groups set theme_color='brigthblue' where theme_color='voice'");
        $this->db->query("update user_groups set theme_color='deepblue' where theme_color='hsl'");
        $this->db->query("update user_groups set theme_color='darkblue' where theme_color='coop'");
        $this->db->query("update user_groups set theme_color='green' where theme_color='smartprospector'");
        $this->db->query("update user_groups set theme_color='orange' where theme_color='orange'");
        $this->db->query("update user_groups set theme_color='red' where theme_color='pelican'");
        $this->db->query("update user_groups set theme_color='purple' where theme_color='eldon'");


        $this->db->query("update users set theme_color='brigthblue' where theme_color='voice'");
        $this->db->query("update users set theme_color='deepblue' where theme_color='hsl'");
        $this->db->query("update users set theme_color='darkblue' where theme_color='coop'");
        $this->db->query("update users set theme_color='green' where theme_color='smartprospector'");
        $this->db->query("update users set theme_color='orange' where theme_color='orange'");
        $this->db->query("update users set theme_color='red' where theme_color='pelican'");
        $this->db->query("update users set theme_color='purple' where theme_color='eldon'");
	}
	
}