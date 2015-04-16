<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_27 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 27");

        //Add cancellation_reason field for the appointments
        $this->db->query("ALTER TABLE `appointments` ADD `cancellation_reason` VARCHAR(255) NULL");
        //Add the person who updated the appointment
        $this->db->query("ALTER TABLE `appointments` ADD `updated_by` INT");

    }
    public function down()
    {
        $this->db->query("ALTER TABLE `appointments` DROP `cancellation_reason`");
        $this->db->query("ALTER TABLE `appointments` DROP `updated_by`");
	}
    
}