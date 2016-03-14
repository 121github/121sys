
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_108 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()

    {
				 $check = $this->db->query("SHOW COLUMNS FROM `custom_panel_fields` LIKE 'read_only'");
        if(!$check->num_rows()){
        $this->db->query("ALTER TABLE `custom_panel_fields` ADD `read_only` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `type` ,
ADD `hidden` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `read_only`"
        );
		}
	}

    public function down()
    {
		
	}
}