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
        $users = $this->Form_model->get_users_with_email();

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
			'campaigns' => $campaigns,
            'users' => $users
        );
        $this->template->load('default', 'exports/view_exports.php', $data);
    }

    public function get_export_forms() {
        $results = $this->Export_model->get_export_forms();

        echo json_encode(array(
            "success" => (!empty($results)),
            "data" => (!empty($results)?$results:"No export forms were created yet!"),
            "edit_permission" => (in_array("edit export",$_SESSION['permissions']))
        ));
    }

    public function get_export_users() {
        if ($this->input->post()) {
            $export_forms_id = $this->input->post('export_forms_id');

            $results = $this->Export_model->get_export_users_by_export_id($export_forms_id);

            $auxList = array();
            foreach ($results as $user) {
                array_push($auxList, $user["user_id"]);
            }
            $results = $auxList;

            echo json_encode(array(
                "success" => ($results),
                "data" => $results
            ));
        }
    }

    /*
    Data export
    */
    public function data_export()
    {
        if ($this->input->post()) {
            $options             = array();
            $options['from']     = ($this->input->post('date_from') ? $this->input->post('date_from') : "2014-01-01");
            $options['to']       = ($this->input->post('date_to') ? $this->input->post('date_to') : "2015-01-01");
            $options['campaign'] = ($this->input->post('campaign') ? $this->input->post('campaign') : "");
            $options['campaign_name'] = ($this->input->post('campaign_name') ? str_replace(" ", "", $this->input->post('campaign_name')) : "");
            $options['export_forms_id'] = ($this->input->post('export_forms_id') ? $this->input->post('export_forms_id') : "");

            $export_form = $this->Export_model->get_export_forms_by_id($options['export_forms_id']);

            if (!empty($export_form)) {
                $filename = $this->get_filename(str_replace(" ", "", $export_form['name']), $options);
                $headers  = explode(";",$export_form['header']);

                $result = $this->Export_model->get_data($export_form, $options);

                //Export the data to a csv file
                $this->export2csv($result, $filename, $headers);
            }
        }
    }


    //Load the data for the report
    public function load_export_report_data() {

        if ($this->input->post()) {
            $options             = array();
            $options['from']     = ($this->input->post('date_from') ? $this->input->post('date_from') : "2014-01-01");
            $options['to']       = ($this->input->post('date_to') ? $this->input->post('date_to') : "2015-01-01");
            $options['campaign'] = ($this->input->post('campaign') ? $this->input->post('campaign') : "");
            $options['campaign_name'] = ($this->input->post('campaign_name') ? str_replace(" ", "", $this->input->post('campaign_name')) : "");
            $options['export_forms_id'] = ($this->input->post('export_forms_id') ? $this->input->post('export_forms_id') : "");

            $export_form = $this->Export_model->get_export_forms_by_id($options['export_forms_id']);

            if (!empty($export_form)) {
                $results = $this->Export_model->get_data($export_form, $options);

                echo json_encode(array(
                    "success" => ($results),
                    "data" => ($results?$results:"No export forms were created yet!"),
                    "header" => explode(";",$export_form['header'])
                ));
            }
            else {
                echo json_encode(array(
                    "success" => false,
                    "data" => ("ERROR: Please contact with your administrator!")
                ));
            }
        }
    }


    //Save or update an export form
    public function save_export_form(){
        if ($this->input->post()) {
            $form = $this->input->post();

            $users = (isset($form['user_id'])?$form['user_id']:array());
            unset($form['user_id']);


            if (!empty($form['export_forms_id'])) {
                $results = $this->Export_model->update_export_form($form);
                $export_forms_id = $form['export_forms_id'];
            }
            else {
                $export_forms_id = $this->Export_model->insert_export_form($form);
            }

            if ($export_forms_id) {
                $results = $this->Export_model->update_export_user($users, $export_forms_id);
            }

            echo json_encode(array(
                "success" => ($export_forms_id),
                "msg" => ($export_forms_id?"Export Form saved successfully":"ERROR: The export form was not saved successfully!")
            ));

        }
    }

    //Delete an export form
    public function delete_export_form(){
        if ($this->input->post()) {
            $export_forms_id = $this->input->post("export_forms_id");

            $results = $this->Export_model->delete_export_form($export_forms_id);

            //Delete the users for this export
            if ($results) {
                $results = $this->Export_model->update_export_user(array(), $export_forms_id);
            }

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Export Form deleted successfully":"ERROR: The export form was not deleted successfully!")
            ));
        }
    }

    //Export data to csv
    private function export2csv($data, $filename, $headers) {
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$filename}.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $outputBuffer = fopen("php://output", 'w');

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