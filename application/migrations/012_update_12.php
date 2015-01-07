
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_12 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->db->query("ALTER TABLE `record_details_fields` ADD `is_visible` INT NULL DEFAULT NULL");
		$this->db->query("ALTER TABLE `record_details_fields` ADD `is_renewal` INT NULL DEFAULT NULL");
		$this->db->query("ALTER TABLE `data_sources` ADD UNIQUE(`source_name`)");
		//just added 7 jan
		$this->db->query("ALTER TABLE `outcomes` ADD `keep_record` INT NULL DEFAULT NULL ");
       }

    public function down()
    {
   
    }

}