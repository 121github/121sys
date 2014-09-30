<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Exports extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->load->model('Export_model');
		$this->load->model('Form_model');
    }
    //view bonus report
    public function index()
    {
		$campaigns = $this->Form_model->get_user_campaigns();

        $data = array(
            'pageId' => 'export',
            'title' => 'Exporter',
            'javascript' => array(
                'lib/moment.js',
                'lib/daterangepicker.js'
            ),
            'page' => array(
                'admin' => 'exports'
            ),
            'css' => array(
                'dashboard.css',
                'daterangepicker-bs3.css'
            ),
			'campaigns' => $campaigns
        );
        $this->template->load('default', 'exports/view_exports.php', $data);
    }
    /*
    Sample export
    */
    public function sample_export()
    {
        if ($this->input->post()) {
            $options             = array();
            $options['from']     = ($this->input->post('date_from') ? to_mysql_datetime($this->input->post('date_from')) : "2014-01-01");
            $options['to']       = ($this->input->post('date_to') ? to_mysql_datetime($this->input->post('date_to')) : "2050-01-01");
            $options['campaign'] = ($this->input->post('date_to') ? $this->input->post('campaign') : "");
            //exit;
            $result              = $this->Export_model->sample_export($options);
            $filename            = "Sample Export";
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename={$filename}.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            $outputBuffer = fopen("php://output", 'w');
            $headers      = array(
                "Date",
                "Campaign",
                "Dials"
            );
            fputcsv($outputBuffer, $headers);
            foreach ($result as $val) {
                fputcsv($outputBuffer, $val);
            }
            fclose($outputBuffer);
        }
    }
}