<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_48 extends CI_Migration
{

    public function __construct()
    {
        $this->load->model('Database_model');
    }

    public function up()
    {
			
		
      $this->firephp->log("starting migration 48");

	    $this->db->query("CREATE TABLE webform_questions
        (
          webform_question_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
          webform_id INT NOT NULL,
          q1 VARCHAR(255),
          q2 VARCHAR(255),
          q3 VARCHAR(255),
          q4 VARCHAR(255),
          q5 VARCHAR(255),
          q6 VARCHAR(255),
          q7 VARCHAR(255),
          q8 VARCHAR(255),
          q9 VARCHAR(255),
          q10 VARCHAR(255),
          q11 VARCHAR(255),
          q12 VARCHAR(255),
          q13 VARCHAR(255),
          q14 VARCHAR(255),
          q15 VARCHAR(255),
          q16 VARCHAR(255),
          q17 VARCHAR(255),
          q18 VARCHAR(255),
          q19 VARCHAR(255),
          q20 VARCHAR(255),
          q21 VARCHAR(255),
          q22 VARCHAR(255),
          q23 VARCHAR(255),
          q24 VARCHAR(255),
          q25 VARCHAR(255),
          q26 VARCHAR(255),
          q27 VARCHAR(255),
          q28 VARCHAR(255),
          q29 VARCHAR(255),
          q30 VARCHAR(255),
          CONSTRAINT `FK_webform_questions_id` FOREIGN KEY (`webform_id`) REFERENCES `webforms` (`webform_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    }

}