<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_107 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()

    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `user_layouts` (
  `user_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `layout` varchar(50) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1"
        );
	}

    public function down()
    {
		
	}
}