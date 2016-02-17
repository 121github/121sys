<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_96 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
        $this->firephp->log("starting migration 96");

        $check = $this->db->query("SHOW COLUMNS FROM `webform_questions` LIKE 'q31'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_questions ADD q31 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_questions` LIKE 'q32'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_questions ADD q32 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_questions` LIKE 'q33'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_questions ADD q33 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_questions` LIKE 'q34'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_questions ADD q34 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_questions` LIKE 'q35'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_questions ADD q35 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_questions` LIKE 'q36'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_questions ADD q36 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_questions` LIKE 'q37'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_questions ADD q37 VARCHAR(255) NULL");
        }


        $check = $this->db->query("SHOW COLUMNS FROM `webform_answers` LIKE 'a31'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_answers ADD a31 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_answers` LIKE 'a32'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_answers ADD a32 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_answers` LIKE 'a33'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_answers ADD a33 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_answers` LIKE 'a34'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_answers ADD a34 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_answers` LIKE 'a35'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_answers ADD a35 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_answers` LIKE 'a36'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_answers ADD a36 VARCHAR(255) NULL");
        }
        $check = $this->db->query("SHOW COLUMNS FROM `webform_answers` LIKE 'a37'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_answers ADD a37 VARCHAR(255) NULL");
        }
    }
    public function down(){

    }

}