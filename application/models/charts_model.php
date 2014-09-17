<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Charts_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }
	//question chart
	public function question_chart($options){
		$filter = (isset($options['filter'])?$options['filter']:"");
		$date_from = (isset($options['date_from'])?$options['date_from']:"");
		$date_to = (isset($options['date_to'])?$options['date_to']:"");
		$limit = (isset($options['limit'])?$options['limit']:"");
		
		$qry = "SELECT round(avg(answer),1) score,question_name,question_script script FROM `questions` left join survey_answers using(question_id) left join surveys using(survey_id) where answer is not null";
        if (!empty($filter)) {
        $qry .= " and surveys.survey_info_id = '$filter' ";
        }
		if (!empty($date_from)) {
        $qry .= " and date(completed_date) >= '$date_from' ";
        }
		if (!empty($date_to)) {
        $qry .= " and date(completed_date) <= '$date_from' ";
        }
        $qry .= " group by question_name order by avg(answer) ";
		if(!empty($limit)){
		$qry .= " limit $limit";
		}
		//$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();	
	}
	
    //this chart was requested by think money - it shows the number of surveys/target
	public function custom_chart($options)
    {
		$filter = (isset($options['filter'])?$options['filter']:"");
		$date_from = (isset($options['date_from'])?$options['date_from']:"");
		$date_to = (isset($options['date_to'])?$options['date_to']:"");
        $where = "";
        if (!empty($filter)) {
            $where .= " and surveys.survey_info_id = '$filter' ";
        }
		if (!empty($date_from)) {
            $where .= " and date(completed_date) >= '$date_from' ";
        }
		if (!empty($date_to)) {
            $where .= " and date(completed_date) <= '$date_to' ";
        }
		$qry = "select count(*) survey_count, surveys.survey_info_id, survey_name,round(nps,1) nps from surveys left join survey_info using(survey_info_id) left join (select avg(answer) nps,surveys.survey_info_id from survey_answers left join surveys using(survey_id) left join questions using(question_id) where nps_question = '1' $where group by questions.survey_info_id) n on n.survey_info_id = surveys.survey_info_id";
		 $qry .= " where 1 $where ";
        $qry .= " group by survey_info_id order by survey_info_id";
		//$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }
	
	
    public function all_surveys($survey_info_id = "")
    {
        $qry = "select answer,completed_date,survey_name,survey_id from surveys left join survey_info using(survey_info_id) left join survey_answers using(survey_id) where answer is not null and completed_date > subdate(curdate(),interval 30 day) ";
        if (!empty($survey_info_id)) {
            $qry .= " and survey_info_id = '$survey_info_id' ";
        }
        $qry .= " group by survey_id order by completed_date desc limit 50";
        $this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
        
    }
    
    public function survey_counts()
    {
        $qry = "select survey_name, count(*) count from surveys left join survey_info using(survey_info_id)";
        $qry .= " group by survey_info_id";
        //$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
        
    }
    
}