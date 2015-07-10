
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_25 extends CI_Migration
{
    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 25");

        $this->db->query("
            CREATE TABLE `campaign_triggers`
            (
                `trigger_id` INT NOT NULL,
                `campaign_id` INT NOT NULL,
                `path` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`trigger_id`),
                CONSTRAINT `camp_triggers_Campaign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
    }
}