<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Charts extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
$this->_campaigns = campaign_access_dropdown();

        $this->load->model('Charts_model');
    }
    
	public function custom_chart(){
		  if ($this->input->is_ajax_request()) {
                $surveys = $this->Charts_model->custom_chart($this->input->post());
				$monthly_targets= array(0,163,
                162,
                100,
                75);
				
	 $start = ($this->input->post('date_from')?strtotime($this->input->post('date_from')):strtotime("2014-07-02"));
     $end = ($this->input->post('date_to')?strtotime($this->input->post('date_to')):strtotime("now"));
     $datediff = ($end==$start?"1":$end - $start);
     $days_in_range = ceil($datediff/(60*60*24));
	 
	 foreach($monthly_targets as $monthly_target){
		$targets[] = number_format(($monthly_target/30) * $days_in_range,1);
	 }

	 
				
				 foreach ($surveys as $k => $row) {
                $surveys[$k]['target'] = $targets[$row['survey_info_id']];
				$surveys[$k]['pc'] = number_format(($surveys[$k]['survey_count']/$surveys[$k]['target'])*100,1);
				 }
				echo json_encode($surveys);
		  }
	}
	
		public function question_chart(){
		  if ($this->input->is_ajax_request()) {
                $results = $this->Charts_model->question_chart($this->input->post());
				echo json_encode($results);
		  }
	}
	
    public function latest_surveys()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('filter')) {
                $surveys = $this->Charts_model->all_surveys($this->input->post('filter'));
            } else {
                $surveys = $this->Charts_model->all_surveys();
            }
            $array = array();
            $x     = 0;
            foreach ($surveys as $row) {
                $x++;
                $array[] = array(
                    "survey_id" => $row['survey_id'],
                    "ID" => $x,
                    "Score" => $row['answer'],
                    "Survey Name" => $row['survey_name'],
                    "uk_date" => date("jS F Y", strtotime($row['completed_date']))
                );
            }
            echo json_encode($array);
        }
    }
    
    
    public function survey_counts()
    {
        if ($this->input->is_ajax_request()) {
            $surveys = $this->Charts_model->survey_counts();
            $array   = array();
            foreach ($surveys as $row) {
                $array[] = array(
                    "label" => $row['survey_name'],
                    "value" => $row['count']
                );
            }
            echo json_encode($array);
        }
    }
    
    
    
}