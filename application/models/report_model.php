<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Report_model extends CI_Model
{
    
    
    
    function __construct()
    {
        parent::__construct();
    }
    
     public function all_answers_data()
    {
        $qry = "SELECT survey_name,l.question_id,surveys.survey_info_id,count(distinct survey_id) count,avg(answer) average_nps,tens,low_score from surveys left join survey_info using(survey_info_id) left join survey_answers using(survey_id) left join questions using(question_id)
	 left join (select surveys.survey_info_id,count(*) tens from surveys left join survey_answers using(survey_id) left join questions using(question_id) where answer = 10 and answer is not null and nps_question = 1 group by surveys.survey_info_id) t on t.survey_info_id = surveys.survey_info_id
	 left join (select surveys.survey_info_id,question_id,count(*) low_score from surveys left join survey_answers using(survey_id) left join questions using(question_id) where answer < 4 and answer is not null and nps_question = 1 group by surveys.survey_info_id) l on l.survey_info_id = surveys.survey_info_id where nps_question = 1
	 group by surveys.survey_info_id";
        return $this->db->query($qry)->result_array();
    }

 public function answers_data($survey)
    {
        if (!empty($survey)) {
            $qry_filter = " and questions.survey_info_id = " . intval($survey);
        } else {
            $qry_filter = "";
        }
        $qry = "SELECT survey_name,sa.question_id,surveys.survey_info_id,question_name,question_script,count(sa.question_id) count,avg(answer) average,t.tens,IF(low_score is null,'0',low_score) low_score from surveys left join survey_info using(survey_info_id) left join survey_answers sa using(survey_id) left join questions using(question_id) 
	 left join (select question_id,count(*) tens from survey_answers left join questions using(question_id) where answer = 10 and answer is not null $qry_filter group by question_id) t on t.question_id = sa.question_id
	  left join (select question_id,count(*) low_score from survey_answers left join questions using(question_id) where answer < 4 and answer is not null $qry_filter group by question_id) l on l.question_id = sa.question_id
	 where answer is not null  $qry_filter group by sa.question_id ";
        return $this->db->query($qry)->result_array();
    }

  public function get_activity($options)
    {
		$date_from = $options['date_from'];
		$date_to = $options['date_to'];
		$campaign = $options['campaign'];
		$user = $options['agent'];
		$where = "";
		if (!empty($date_from)) {
        $where .= " and date(contact) >= '$date_from' ";
        }
		if (!empty($date_to)) {
        $where .= " and date(contact) <= '$date_to' ";
        }
		if (!empty($campaign)) {
        $where .= " and history.campaign_id = '$campaign' ";
        }
				if (!empty($user)) {
        $where .= " and user_id = '$user' ";
        }
		
        $qry = "select outcome,count(*) count,total from history left join outcomes using(outcome_id) left join records using(urn) left join (select count(*) total,history.outcome_id from history left join records using(urn) where 1 and history.outcome_id is not null ";
		$qry .= $where;
        
        $qry .= " ) t on history.outcome_id = outcomes.outcome_id where 1 and history.outcome_id is not null ";
        
		$qry .= $where;
        $qry .= " group by history.outcome_id order by count desc ";
        return $this->db->query($qry)->result_array();
    }
}