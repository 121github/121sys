<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Exports extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
        $this->_campaigns = campaign_access_dropdown();
        $this->load->model('Export_model');
		$this->load->model('Form_model');
    }
    //view bonus report
    public function index()
    {
		$campaigns = $this->Form_model->get_user_campaigns();

        $data = array(
            'campaign_access' => $this->_campaigns,
			'pageId' => 'export',
            'title' => 'Admin | Exporter',
            'javascript' => array(
                'lib/moment.js',
                'lib/daterangepicker.js',
                'export.js'
            ),
            'page' => array(
                'admin' => 'data',
            	'inner' => 'export'
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
    Dials export
    */
    public function dials_export()
    {
        if ($this->input->post()) {
            $options             = array();
            $options['from']     = ($this->input->post('date_from') ? $this->input->post('date_from') : "2014-01-01");
            $options['to']       = ($this->input->post('date_to') ? $this->input->post('date_to') : "2015-01-01");
            $options['campaign'] = ($this->input->post('campaign') ? $this->input->post('campaign') : "");
            $options['campaign_name'] = ($this->input->post('campaign_name') ? str_replace(" ", "", $this->input->post('campaign_name')) : "");

            $result = $this->Export_model->dials_export($options);
            $filename = $this->get_filename("DialsExport", $options);

            //Export the data to a csv file
            $this->export2csv($result, $filename);
        }
    }

    /*
    Contacts added export
    */
    public function contacts_added_export()
    {
        if ($this->input->post()) {
            $options             = array();
            $options['from']     = ($this->input->post('date_from') ? $this->input->post('date_from') : "2014-01-01");
            $options['to']       = ($this->input->post('date_to') ? $this->input->post('date_to') : "2015-01-01");
            $options['campaign'] = ($this->input->post('campaign') ? $this->input->post('campaign') : "");
            $options['campaign_name'] = ($this->input->post('campaign_name') ? str_replace(" ", "", $this->input->post('campaign_name')) : "");

            $result = $this->Export_model->contacts_added_export($options);
            $filename = $this->get_filename("ContactsAddedExport", $options);

            //Export the data to a csv file
            $this->export2csv($result, $filename);
        }
    }

    //Export data to csv
    private function export2csv($data, $filename) {
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$filename}.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $outputBuffer = fopen("php://output", 'w');
        $headers      = array(
            "Date",
            "Fullname",
            "URN",
            "Add1",
            "Add2",
            "Add3",
            "Postcode",
            "County",
            "Country",
            "Telephone number",
            "Email",
            "Email optout",
            "Website",
            "Linkedin",
            "Facebook"

        );
        fputcsv($outputBuffer, $headers);
        foreach ($data as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
    }

    private function get_filename($name, $options) {
        $filename = date("YmdHsi")."_".$name;

        if (!empty($options['from'])) {
            $filename .= "_".$options['from'];
        }
        if (!empty($options['to'])) {
            $filename .= "_".$options['to'];
        }
        if (!empty($options['campaign'])) {
            $filename .= "_".$options['campaign_name'];
        }

        return $filename;
    }
}