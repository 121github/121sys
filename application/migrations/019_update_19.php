<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_19 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 19");
        //Adding search actions permissions and edit export permissions
        $this->db->query("CREATE TABLE `function_triggers`
                        (
                           `trigger_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                           `campaign_id` INT NOT NULL,
                           `outcome_id` INT NOT NULL,
                           `path` VARCHAR(255) NOT NULL,
                           CONSTRAINT `fk_FT_Campaign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`),
                           CONSTRAINT `fk_FT_Outcome` FOREIGN KEY (`outcome_id`) REFERENCES `outcomes` (`outcome_id`)
                        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

        //Adding the trigger for the integration between Onions campaign CRM (workbooks) and the 121 calling system
        $this->db->query("INSERT ignore INTO `function_triggers` (`campaign_id`, `outcome_id`, `path`) VALUES
                      (12, 72, 'workbooks/create_leads/')");

    }

    public function down()
    {
        $this->db->query("DROP TABLE `function_triggers`");
    }

}