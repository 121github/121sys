<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Exports extends CI_Controller {

  public function __construct() {
    parent::__construct();
    user_auth_check();
    $this->load->model('Export_model');
  }

  //view bonus report
  public function index() {
    $data = array(
        'pageId' => 'export',
        'title' => 'Exporter',
		'javascript'=> array('lib/moment.js',
		'lib/daterangepicker.js'),
				'page' => array(
                'admin' => 'exports'
            ),
		'css'=>array(
                'dashboard.css',
				'daterangepicker-bs3.css',
            )
    );
    $this->template->load('default', 'exports/view_exports.php', $data);	
	
	
  }
  
  public function sample_export(){
	 if ($this->input->post()) {
	  $options= array();
	$options['from'] = ($this->input->post('date_from')?to_mysql_datetime($this->input->post('date_from')):"2014-01-01");
	 $options['to'] = ($this->input->post('date_to')?to_mysql_datetime($this->input->post('date_to')):"2050-01-01");
	  $options['campaign'] = ($this->input->post('date_to')?$this->input->post('campaign'):"");
	  //exit;
	$result = $this->Export_model->sample_export($options);

	    $filename = "Sample Export";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
		     
		   		$headers = array("Date","Campaign","Dials");
				fputcsv($outputBuffer, $headers);
		   
        foreach($result as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
		}  
	  
  }
  
  public function reviewer_export(){
	 if ($this->input->post()) {
	  $options= array();
	$options['from'] = ($this->input->post('date_from')?to_mysql_datetime($this->input->post('date_from')):"2014-01-01");
	 $options['to'] = ($this->input->post('date_to')?to_mysql_datetime($this->input->post('date_to')):"2050-01-01");
	  //print_r($options);
	  //exit;
	$result = $this->Export_model->reviewer_scores($options);

	    $filename = "PFM-Scores";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
		     
		   		$headers = array("Reviewer","Average Score","Number of Surveys");
				fputcsv($outputBuffer, $headers);
		   
        foreach($result as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
		}  
	  
  }
	  
    public function closure_reasons(){
	 if ($this->input->post()) {
	  $options= array();
	$options['from'] = ($this->input->post('date_from')?to_mysql_datetime($this->input->post('date_from')):"2014-01-01");
	 $options['to'] = ($this->input->post('date_to')?to_mysql_datetime($this->input->post('date_to')):"2050-01-01");
	  //print_r($options);
	  //exit;
	$result = $this->Export_model->closure_reasons($options);

	    $filename = "Closure-reasons";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
		     
		   		$headers = array("Reason for closing","Count");
				fputcsv($outputBuffer, $headers);
		   
        foreach($result as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
		}  
	  
  }
  
  
  	  
    public function mismatched_surveys(){
	 if ($this->input->post()) {
	  $options= array();
	$options['from'] = ($this->input->post('date_from')?to_mysql_datetime($this->input->post('date_from')):"2014-01-01");
	 $options['to'] = ($this->input->post('date_to')?to_mysql_datetime($this->input->post('date_to')):"2050-01-01");
	  //print_r($options);
	  //exit;
	$result = $this->Export_model->mismatched_surveys($options);

	    $filename = "Mismatched-surveys";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
		     
		   		$headers = array("URN","Campaign","Survey Type");
				fputcsv($outputBuffer, $headers);
		   
        foreach($result as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
		}  
	  
  }
  
      public function gp_refs6(){
	 if ($this->input->post()) {
	  $options= array();
	$options['from'] = ($this->input->post('date_from')?to_mysql_datetime($this->input->post('date_from')):"2014-01-01");
	 $options['to'] = ($this->input->post('date_to')?to_mysql_datetime($this->input->post('date_to')):"2050-01-01");
	  //print_r($options);
	  //exit;
	$result = $this->Export_model->gp_refs6($options);

	    $filename = "Mismatched-surveys";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
		     
		   		$headers = array("URN","GP Ref","NPS Score","Survey Type","Survey Date");
				fputcsv($outputBuffer, $headers);
		   
        foreach($result as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
		}  
	  
  }
  
  
}