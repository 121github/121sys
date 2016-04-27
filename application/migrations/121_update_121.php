<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_121 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

        $this->db->query("update campaign_triggers set path = concat('better_connected/',path) where path like 'better_connected%'");
		 $this->db->query("update campaign_triggers set path = concat('lhs/',path) where path like 'lhs.php'");
		  $this->db->query("update campaign_triggers set path = concat('lhs/',path) where path like 'lhs_accept%'");
		   $this->db->query("update campaign_triggers set path = concat('hsl/',path) where path like 'hsl.php'");
		   $this->db->query("update campaign_triggers set path = concat('hsl/',path) where path like 'hsl_accept%'");
		   $this->db->query("update campaign_triggers set path = concat('lps/',path) where path like '%lps%'");
			$this->db->query("update campaign_triggers set path = concat('ghs/',path) where path like '%ghs%'");
			$this->db->query("update campaign_triggers set path = concat('eldon/',path) where path like '%eldon%'");

	}
	
}