<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_23 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 23");

        $this->db->query("ALTER TABLE `contact_telephone` ADD INDEX `telephone_number` (`telephone_number`);");
        $this->db->query("ALTER TABLE `company_telephone` ADD INDEX `telephone_number` (`telephone_number`);");

        //Create tps table if it does not exist
        $this->db->query("CREATE TABLE IF NOT EXISTS `tps` (
              `telephone` varchar(20) NOT NULL,
              PRIMARY KEY (`telephone`),
              UNIQUE KEY `telephone` (`telephone`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ");

        //Add the data field to the tps table
        $this->db->query("ALTER TABLE `tps` ADD `date_updated` TIMESTAMP NOT NULL");
        //Set the current date to the numbers that already exists in the tps table
        $this->db->query("UPDATE `tps` SET `date_updated`=NOW()");
    }
    public function down()
    {
        $this->db->query("DROP INDEX `telephone_number` ON TABLE `contact_telephone`");
        $this->db->query("DROP INDEX `telephone_number` ON TABLE `company_telephone`");
        $this->db->query("DROP TABLE `tps`");
    }
}