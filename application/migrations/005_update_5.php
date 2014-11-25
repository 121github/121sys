<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_update_5 extends CI_Migration
{
    
    public function __construct()
    {
        $this->load->model('Database_model');
    }
    
    public function up()
    {
		$this->db->query("CREATE TABLE IF NOT EXISTS `suppression` (
  `suppression_id` int(11) NOT NULL AUTO_INCREMENT,
  `telephone_number` varchar(20) NOT NULL,
  `outcome_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`suppression_id`),
  KEY `telephone_number` (`telephone_number`),
  KEY `outcome_id` (`outcome_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");


}
	    public function down()
    {
		$this->db->query("drop table `suppression`");
	}
	
}