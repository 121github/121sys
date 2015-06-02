
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_9 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 9");

        //add travelMode
        $this->db->query("CREATE TABLE IF NOT EXISTS `company_sectors` (
  `company_id` tinyint(4) NOT NULL,
  `sector_id` tinyint(4) NOT NULL,
  UNIQUE KEY `company_id` (`company_id`,`sector_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
");

 $this->db->query("insert ignore into company_sectors (select company_id,sector_id from company_subsectors left join subsectors using(subsector_id))");
	}

    public function down()
    {
        $this->db->query("drop table company_sectors");
    }
	
}