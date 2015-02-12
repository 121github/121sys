<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_13 extends CI_Migration
{

  public function __construct()
  {
    $this->load->model('Database_model');
  }

  public function up()
  {
	  $this->firephp->log("starting migration 13");
      
	        $this->db->query("CREATE TABLE IF NOT EXISTS `files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(100) NOT NULL,
  `filesize` int(11) DEFAULT NULL,
  `folder_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_on` timestamp NULL DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
	
	      $this->db->query("CREATE TABLE IF NOT EXISTS `folders` (
  `folder_id` int(11) NOT NULL AUTO_INCREMENT,
  `folder_name` varchar(100) NOT NULL,
  `accepted_filetypes` varchar(200) NOT NULL,
  PRIMARY KEY (`folder_id`),
  UNIQUE KEY `folder_name` (`folder_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
	  
      $this->db->query("CREATE TABLE IF NOT EXISTS `folder_permissions` (
  `user_id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `read` tinyint(4) DEFAULT '1',
  `write` tinyint(4) DEFAULT NULL,
  UNIQUE KEY `user_id` (`user_id`,`folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");


    //Adding search actions permissions and edit export permissions
    $this->db->query("INSERT ignore INTO `permissions` (`permission_id`, `permission_name`, `permission_group`) VALUES
                      (116, 'parkcodes', 'Data')");

    $this->db->query("INSERT ignore INTO `role_permissions` (`role_id`, `permission_id`) VALUES
                    (1, 116)");
  }
  public function down()
  {
    $this->db->query("DELETE FROM permissions WHERE permission_id IN (116)");
    $this->db->query("DELETE FROM role_permissions WHERE permission_id IN (116)");
  }
}

