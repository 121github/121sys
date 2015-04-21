<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_29 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 29");
		$this->db->query("ALTER TABLE `email_templates` ADD `template_unsubscribe` TINYINT(1) NOT NULL DEFAULT '0'");
		$this->db->query("ALTER TABLE `email_history` ADD `template_unsubscribe` TINYINT(1) NOT NULL DEFAULT '0'");
		$this->db->query("CREATE TABLE IF NOT EXISTS `email_unsubscribe` (
  `unsubscribe_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_address` varchar(100) NOT NULL,
  `client_id` int(11) NOT NULL,
  `urn` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(20) NOT NULL,
  PRIMARY KEY (`unsubscribe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
$this->db->query("alter table email_unsubscribe add unique(email_address,client_id)");

	}
}
?>