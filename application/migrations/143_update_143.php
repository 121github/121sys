<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_143 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {

        $check = $this->db->query("SHOW COLUMNS FROM `webform_questions` LIKE 'q38'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_questions ADD q38 VARCHAR(255) NULL");
            $this->db->query("UPDATE webform_questions set `q38`='What product is the customer interested in?'");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `webform_questions` LIKE 'q39'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_questions ADD q39 VARCHAR(255) NULL");
            $this->db->query("UPDATE webform_questions set `q39`='Is there any specific fabric customer has in mind?'");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `webform_answers` LIKE 'a38'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_answers ADD a38 VARCHAR(255) NULL");
        }

        $check = $this->db->query("SHOW COLUMNS FROM `webform_answers` LIKE 'a39'");
        if(!$check->num_rows()){
            $this->db->query("ALTER TABLE webform_answers ADD a39 VARCHAR(255) NULL");
        }
	}

}

