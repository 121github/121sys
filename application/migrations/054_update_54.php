<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_54 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {


        $this->firephp->log("starting migration 54");

        $this->db->query("ALTER TABLE appointment_rules ADD appointment_slot_id INT NULL");
        $this->db->query("ALTER TABLE `appointment_rules` ADD CONSTRAINT `appointment_rules_ibfk_4` FOREIGN KEY (`appointment_slot_id`) REFERENCES `appointment_slots` (`appointment_slot_id`)");

        $this->db->query("ALTER TABLE appointment_rules ADD UNIQUE(block_day, user_id, reason_id, appointment_slot_id)");
    }

}