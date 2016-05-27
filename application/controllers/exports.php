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
        $this->project_version = $this->config->item('project_version');

        $this->load->model('Export_model');
		$this->load->model('Form_model');
    }
    //view bonus report
    public function index()
    {
		$campaigns_by_group = $this->Form_model->get_user_campaigns_ordered_by_group();
        $aux = array();
        foreach ($campaigns_by_group as $campaign) {
            if (!isset($aux[$campaign['group_name']])) {
                $aux[$campaign['group_name']] = array();
            }
            array_push($aux[$campaign['group_name']], $campaign);
        }
        $campaigns_by_group = $aux;

        $current_campaign = (isset($_SESSION['current_campaign']) ? array($_SESSION['current_campaign']) : array());
        $campaign_outcomes = $this->Form_model->get_outcomes_by_campaign_list($current_campaign);

        $aux = array(
            "positive" => array(),
            "No_positive" => array(),
        );
        foreach ($campaign_outcomes as $outcome) {
            if ($outcome['positive']) {
                array_push($aux['positive'], $outcome);
            } else {
                array_push($aux['No_positive'], $outcome);
            }
        }
        $campaign_outcomes = $aux;

        $sources = $this->Form_model->get_sources_by_campaign_list($current_campaign);
        $pots = $this->Form_model->get_pots_by_campaign_list($current_campaign);

        $teams = $this->Form_model->get_teams();
        $branches = $this->Form_model->get_branches();

        $users = $this->Form_model->get_users();
        $aux = array();
        if (isset($_SESSION['user_id'])) {
            $aux["-"] = array(array(
                "name" => "User Logged in",
                "id" => "user_id"
            ));
        }
        foreach ($users as $user) {
            if (!isset($aux[$user['role_name']])) {
                $aux[$user['role_name']] = array();
            }
            array_push($aux[$user['role_name']], $user);
        }
        $users = $aux;

        $data = array(
            'campaign_access' => $this->_campaigns,

			'pageId' => 'export',
            'title' => 'Admin | Report Settings',
            'javascript' => array(
                'lib/moment.js',
                'lib/daterangepicker.js',
                'export.js?v' . $this->project_version,
                'charts.js?v' . $this->project_version,
                'plugins/DataTables/datatables.min.js'
            ),
            'page' => 'export_data',
            'css' => array(
                'dashboard.css',
                'daterangepicker-bs3.css'
            ),
			'campaigns_by_group' => $campaigns_by_group,
            'campaign_outcomes' => $campaign_outcomes,
            'sources' => $sources,
			'pots' => $pots,
            'teams' => $teams,
            'branches' => $branches,
            'users' => $users
        );
        $this->template->load('default', 'exports/view_exports.php', $data);
    }

    public function get_export_forms() {
		session_write_close();
        $results = $this->Export_model->get_export_forms();

        echo json_encode(array(
            "success" => (!empty($results)),
            "data" => (!empty($results)?$results:"No export forms were created yet!"),
            "edit_permission" => (in_array("edit export",$_SESSION['permissions']))
        ));
    }

    public function get_export_users() {
		session_write_close();
        if ($this->input->post()) {
            $export_forms_id = $this->input->post('export_forms_id');
            $results = array();

            if ($export_forms_id) {
                $results = $this->Export_model->get_export_users_by_export_id($export_forms_id);

                $auxList = array();
                foreach ($results as $user) {
                    array_push($auxList, $user["user_id"]);
                }
                $results = $auxList;
            }

            $users = $this->Form_model->get_users_with_email();
            $aux = array();
            foreach ($users as $user) {
                if (!isset($aux[$user['role_name']])) {
                    $aux[$user['role_name']] = array();
                }
                array_push($aux[$user['role_name']],array(
                    "id" => $user['id'],
                    "name" => $user['name']
                ));
            }
            $users = $aux;

            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "users" => $users
            ));
        }
    }

    public function get_export_viewers() {
		session_write_close();
        if ($this->input->post()) {
            $export_forms_id = $this->input->post('export_forms_id');
            $results = array();

            if ($export_forms_id) {
                $results = $this->Export_model->get_export_viewers_by_export_id($export_forms_id);

                $auxList = array();
                foreach ($results as $user) {
                    array_push($auxList, $user["user_id"]);
                }
                $results = $auxList;
            }

            $users = $this->Form_model->get_users();
            $aux = array();
            foreach ($users as $user) {
                if (!isset($aux[$user['role_name']])) {
                    $aux[$user['role_name']] = array();
                }
                array_push($aux[$user['role_name']],array(
                    "id" => $user['id'],
                    "name" => $user['name']
                ));
            }
            $users = $aux;

            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "users" => $users
            ));
        }
    }

    public function get_export_graphs() {
        if ($this->input->post()) {
            $export_forms_id = $this->input->post('export_forms_id');
            $results = array();

            if ($export_forms_id) {
                $results = $this->Export_model->get_export_graphs_by_export_id($export_forms_id);
            }

            echo json_encode(array(
                "success" => !empty($results),
                "graphs" => $results
            ));
        }
    }

    /*
    Data for custom export
    */
    public function data_export()
    {
        if ($this->input->post()) {
            $options             = array();
            $nowDate = new \DateTime('now');
            $options['from']     = ($this->input->post('date_from') ? $this->input->post('date_from') : "2014-07-02");
            $options['to']       = ($this->input->post('date_to') ? $this->input->post('date_to') : $nowDate->format('Y-m-d'));
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
	/*
	public function test_assoc_helper(){
	$results = $this->db->query("select * from custom_panel_data join custom_panel_values using(data_id) join custom_panel_fields using(field_id)")->result_array();	
		$results = custom_assoc($results);
		echo "<pre>";
		print_r($results);	
		echo "<pre>";
	}
	*/

    //Load the data for the report
    public function load_export_report_data() {
		session_write_close();
        if ($this->input->post()) {
            $options             = array();
            $nowDate = new \DateTime('now');
            $options['from']     = ($this->input->post('date_from') ? $this->input->post('date_from') : "2014-01-01");
            $options['to']       = ($this->input->post('date_to') ? $this->input->post('date_to') : $nowDate->format('Y-m-d'));
            $options['campaigns'] = ($this->input->post('campaign') ? $this->input->post('campaign') : "");
            $options['campaign_name'] = ($this->input->post('campaign_name') ? str_replace(" ", "", $this->input->post('campaign_name')) : "");
            $options['outcomes'] = ($this->input->post('outcomes') ? $this->input->post('outcomes') : "");
            $options['outcome_name'] = ($this->input->post('outcome_name') ? str_replace(" ", "", $this->input->post('campaign_name')) : "");
            $options['sources'] = ($this->input->post('sources') ? $this->input->post('sources') : "");
            $options['source_name'] = ($this->input->post('source_name') ? str_replace(" ", "", $this->input->post('source_name')) : "");
			$options['pots'] = ($this->input->post('pot') ? $this->input->post('pot') : "");
            $options['pot_name'] = ($this->input->post('pot_name') ? str_replace(" ", "", $this->input->post('pot_name')) : "");
            $options['teams'] = ($this->input->post('team') ? $this->input->post('team') : "");
            $options['team_name'] = ($this->input->post('team_name') ? str_replace(" ", "", $this->input->post('team_name')) : "");
            $options['branches'] = ($this->input->post('branches') ? $this->input->post('branches') : "");
            $options['branch_name'] = ($this->input->post('branch_name') ? str_replace(" ", "", $this->input->post('branch_name')) : "");
            $options['user'] = ($this->input->post('user') ? $this->input->post('user') : "");
            $options['user_name'] = ($this->input->post('user_name') ? str_replace(" ", "", $this->input->post('user_name')) : "");

            $options['export_forms_id'] = ($this->input->post('export_forms_id') ? $this->input->post('export_forms_id') : "");


            $export_form = $this->Export_model->get_export_forms_by_id($options['export_forms_id']);

            if (!empty($export_form)) {
                $results = $this->Export_model->get_data($export_form, $options);
				//$this->firephp->log($results);
				//$results = custom_assoc($results);
				//$this->firephp->log($results);

                //Get graphs
                $graphs = $this->Export_model->get_export_graphs_by_export_id($options['export_forms_id']);

                $aux = array();
                foreach ($graphs as $graph) {
                    $graph['data'] = array();

                    $z_values = array();
                    if (isset($graph['z_value']) && $graph['z_value'] != "" && !empty($results)) {
                        foreach ($results as $result) {
                            if (!isset($graph['data'][$result[$graph['x_value']]])) {
                                $graph['data'][$result[$graph['x_value']]] = array();
                            }
                            array_push($z_values, $result[$graph['z_value']]);
                        }

                        $z_values = array_unique($z_values);
                        sort($z_values);
                        foreach($graph['data'] as $key => $value) {
                            foreach ($z_values as $v) {
                                if (!isset($graph['data'][$key][$v])) {
                                    $graph['data'][$key][$v] = 0;
                                }
                            }
                        }

                        foreach ($results as $result) {
                            if (isset($graph['y_value']) && $graph['y_value'] != "") {
                                $graph['data'][$result[$graph['x_value']]][$result[$graph['z_value']]] += $result[$graph['y_value']];
                            }
                            else {
                                $graph['data'][$result[$graph['x_value']]][$result[$graph['z_value']]]++;
                            }
                        }

                        array_push($aux, $graph);
                    }
                    else {
                        foreach ($results as $result) {

                            if (!isset($graph['data'][$result[$graph['x_value']]])) {
                                $graph['data'][$result[$graph['x_value']]] = 0;
                            }
                            if (isset($graph['y_value']) && $graph['y_value'] != "") {
                                $graph['data'][$result[$graph['x_value']]] += $result[$graph['y_value']];
                            }
                            else {
                                $graph['data'][$result[$graph['x_value']]]++;
                            }
                        }
                        array_push($aux, $graph);
                    }
                }
                $graphs = $aux;
            }

			if(count($results)){
                echo json_encode(array(
                    "success" => true,
                    "data" => ($results?$results:"No export forms were created yet!"),
                    "header" => explode(";",$export_form['header']),
                    'graphs' => $graphs
                ));
            }
            else {
                echo json_encode(array(
                    "success" => false,
                    "data" => "No results found",
                    'graphs' => array()
                ));
            }
        }
    }

    /*
    Data for available export
    */
    public function data_available_export()
    {
        if ($this->input->post()) {
            $options             = array();
            $options['export_form_name'] = ($this->input->post('export_form_name') ? $this->input->post('export_form_name') : "");
            $options['date_from'] = $this->input->post("date_from");
            $options['campaigns'] = $this->input->post("campaigns");
            $options['date_to'] = $this->input->post("date_to");
            $options['users'] = $this->input->post("agents");
            $options['teams'] = $this->input->post("teams");
            $options['sources'] = $this->input->post("sources");
            $options['pots'] = $this->input->post("pots");

            $result = $this->get_data_available_export($options);

            //Export the data to a csv file
            $this->export2csv($result['data'], $result['filename'], $result['headers']);
        }
    }

    /**
     * Get the available export data
     */
    private function get_data_available_export($options) {
        $result = array();

        $result['filename'] = $this->get_filename(str_replace(" ", "","no_results"), $options);
        $result['headers'] = ("no_results");

        switch ($options['export_form_name']) {
            case "contacts-data":
                $result['filename'] = $this->get_filename(str_replace(" ", "","contacts_data"), $options);
                $result['data'] = $this->Export_model->get_contacts_data($options);
                $aux = array();
                $num_company_telephone_numbers = 1;
                $num_contact_telephone_numbers = 1;
                foreach ($result['data'] as $val) {

                    $company_telephone_array = explode(',',$val['company_telephone_number']);
                    $i = 1;
                    if ($company_telephone_array) {
                        foreach ($company_telephone_array as $company_telephone_number) {
                            $val['company_telephone'.($i>1?"_".$i:"")] = ($company_telephone_number?$company_telephone_number:'-');
                            $num_company_telephone_numbers = ($i>$num_company_telephone_numbers?$i:$num_company_telephone_numbers);
                            $i++;
                        }
                        unset($val['company_telephone_number']);
                    }

                    $contact_telephone_array = explode(',',$val['contact_telephone_number']);
                    $i = 1;
                    if ($contact_telephone_array) {
                        foreach ($contact_telephone_array as $contact_telephone_number) {
                            $val['contact_telephone'.($i>1?"_".$i:"")] = ($contact_telephone_number?$contact_telephone_number:'-');
                            $num_contact_telephone_numbers = ($i>$num_contact_telephone_numbers?$i:$num_contact_telephone_numbers);
                            $i++;
                        }
                        unset($val['contact_telephone_number']);
                    }

                    array_push($aux,$val);
                }

                $result['data'] = $aux;

                $aux = array();
                foreach($result['data'] as $val) {
                    $aux_val = array();

                    $aux_val['campaign_name'] = $val['campaign_name'];
                    $aux_val['company_name'] = $val['company_name'];
                    $aux_val['add1'] = $val['add1'];
                    $aux_val['add2'] = $val['add2'];
                    $aux_val['add3'] = $val['add3'];
                    $aux_val['postcode'] = $val['postcode'];
                    $aux_val['county'] = $val['county'];
                    $aux_val['country'] = $val['country'];

                    for ($i=1;$i<=$num_company_telephone_numbers;$i++) {
                        if (!isset($val['company_telephone'.($i>1?"_".$i:"")])) {
                            $aux_val['company_telephone'.($i>1?"_".$i:"")] = '-';
                        }
                        else {
                            $aux_val['company_telephone'.($i>1?"_".$i:"")] = $val['company_telephone'.($i>1?"_".$i:"")];
                        }
                    }

                    $aux_val['title'] = $val['title'];
                    $aux_val['fullname'] = $val['fullname'];
                    $aux_val['position'] = $val['position'];
                    $aux_val['email'] = $val['email'];

                    for ($i=1;$i<=$num_contact_telephone_numbers;$i++) {
                        if (!isset($val['contact_telephone'.($i>1?"_".$i:"")])) {
                            $aux_val['contact_telephone'.($i>1?"_".$i:"")] = '-';
                        }
                        else {
                            $aux_val['contact_telephone'.($i>1?"_".$i:"")] = $val['contact_telephone'.($i>1?"_".$i:"")];
                        }
                    }

                    $aux_val['outcome'] = $val['outcome'];
                    $aux_val['dials'] = $val['dials'];

                    array_push($aux, $aux_val);
                }

                $result['data'] = $aux;

                $company_telephone_header = array();
                for ($i=1;$i<=$num_company_telephone_numbers;$i++) {
                    array_push($company_telephone_header,'company_telephone'.($i>1?"_".$i:""));
                }
                $contact_telephone_header = array();
                for ($i=1;$i<=$num_contact_telephone_numbers;$i++) {
                    array_push($contact_telephone_header,'contact_telephone'.($i>1?"_".$i:""));
                }
                $result['headers'] = ("campaign_name;company_name;add1;add2;add3;postcode;county;country;".implode(';',$company_telephone_header).";title;fullname;position;email;".implode(';',$contact_telephone_header).";outcome;dials");

                $result['headers'] = explode(";",$result['headers']);

                break;
            case "combo-data":
                $result['filename'] = $this->get_filename(str_replace(" ", "","combo_data"), $options);

                $campaigns = $this->Export_model->get_campaigns_by_id_list($options['campaigns']);

                $result['data'] = $this->Export_model->get_combo_export_data($options, $campaigns);
                $aux = array();
                foreach ($result['data'] as $val) {
                    $val['date'] = mysql_to_uk_date($val['date']);
                    array_push($aux, $val);
                }
                $result['data'] = $aux;

                $aux = array();
                foreach($campaigns as $campaign) {
                    array_push($aux, $campaign." [hours]");
                    array_push($aux, $campaign." [positive]");
                }
                $campaigns = $aux;
                $result['headers'] = ("login;name;date;".implode(';',$campaigns));
                $result['headers'] = explode(";",$result['headers']);

                break;
            case "dials-data":
                $result['filename'] = $this->get_filename(str_replace(" ", "","dials_data"), $options);

                $campaigns = $this->Export_model->get_campaigns_by_id_list($options['campaigns']);

                $result['data'] = $this->Export_model->get_dials_export_data($options, $campaigns);
                $aux = array();
                foreach ($result['data'] as $val) {
                    $val['date'] = mysql_to_uk_date($val['date']);
                    array_push($aux, $val);
                }
                $result['data'] = $aux;

                $result['headers'] = ("date;".implode(';',$campaigns));
                $result['headers'] = explode(";",$result['headers']);

                break;
        }

        return $result;
    }

    //Load the data for the report
    public function load_available_export_report_data() {

        if ($this->input->post()) {
            $options = array();
            $options['date_from'] = $this->input->post("date_from");
            $options['campaigns'] = $this->input->post("campaigns");
            $options['date_to'] = $this->input->post("date_to");
            $options['users'] = $this->input->post("agents");
            $options['teams'] = $this->input->post("teams");
            $options['sources'] = $this->input->post("sources");
            $options['pots'] = $this->input->post("pots");

            $options['export_form_name'] = ($this->input->post('export_form_name') ? $this->input->post('export_form_name') : "");

            if (empty($options['campaigns'])) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "You should select at least one campaign on the filter"
                ));
            }
            else {
                $result = $this->get_data_available_export($options);

                echo json_encode(array(
                    "success" => !empty($result['data']),
                    "data" => $result['data'],
                    "header" => $result['headers'],
                    "msg" => !empty($result['data'])?"":"No data"
                ));
            }
        }
    }




    //Save or update an export form
    public function save_export_form(){
        if ($this->input->post()) {
            $form = $this->input->post();

            if ($form['name'] == '' || $form['header'] == '' || $form['query'] == '') {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "Please fill at least the name, header and query on the form"
                ));
            }
            else {
                $users = (isset($form['user_id'])?$form['user_id']:array());
                unset($form['user_id']);

                $viewers = (isset($form['viewer_id'])?$form['viewer_id']:array());
                unset($form['viewer_id']);

                $graph = array();
                if ($form['graph_name'] != "" && $form['graph_type'] != "" && $form['x_value'] != "") {
                    $graph = array(
                        "export_forms_id" => $form['export_forms_id'],
                        "name" => $form['graph_name'],
                        "type" => $form['graph_type'],
                        "x_value" => $form['x_value'],
                        "y_value" => (isset($form['y_value'])?$form['y_value']:NULL),
                        "z_value" => (isset($form['z_value'])?$form['z_value']:NULL)
                    );
                }
                unset($form['graph_name']);
                unset($form['graph_type']);
                unset($form['x_value']);
                unset($form['y_value']);
                unset($form['z_value']);


                if (!empty($form['export_forms_id'])) {
                    $this->Export_model->update_export_form($form);
                    $export_forms_id = $form['export_forms_id'];
                }
                else {
                    $export_forms_id = $this->Export_model->insert_export_form($form);
                }

                if ($export_forms_id) {

                    $this->Export_model->update_export_user($users, $export_forms_id);
                    $this->Export_model->update_export_viewers($viewers, $export_forms_id);

                    if (!empty($graph)) {
                        $this->Export_model->insert_export_graph($graph);
                    }
                }

                echo json_encode(array(
                    "success" => ($export_forms_id),
                    "msg" => ($export_forms_id?"Export Form saved successfully":"ERROR: The export form was not saved successfully!")
                ));
            }
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

    //Save an export graph
    public function save_export_graph(){
        if ($this->input->post()) {
            $form = $this->input->post();

            $graph = array();
            if ($form['graph_name'] != "" && $form['graph_type'] != "" && $form['x_value'] != "") {
                $graph = array(
                    "export_forms_id" => $form['export_forms_id'],
                    "name" => $form['graph_name'],
                    "type" => $form['graph_type'],
                    "x_value" => $form['x_value'],
                    "y_value" => (isset($form['y_value'])?$form['y_value']:NULL),
                    "z_value" => (isset($form['z_value'])?$form['z_value']:NULL)
                );
            }


            if (!empty($graph)) {
                $graph_id = $this->Export_model->insert_export_graph($graph);

                echo json_encode(array(
                    "success" => ($graph_id),
                    "msg" => ($graph_id?"Graph saved successfully":"ERROR: The graph was not saved successfully!")
                ));
            }
            else {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "ERROR: The graph was not saved successfully! Please, fill all the fields."
                ));
            }



        }
    }

    //Delete an export graph
    public function delete_export_graph(){
        if ($this->input->post()) {
            $graph_id = $this->input->post("graph_id");

            $results = $this->Export_model->delete_export_graph($graph_id);

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Graph deleted successfully":"ERROR: The graph was not deleted successfully!")
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
            fputcsv($outputBuffer, preg_replace('/\r?\n|\r/','', $val));
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