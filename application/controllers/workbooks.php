<?php
require_once(APPPATH.'libraries/workbooks_api.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Workbooks extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
        $this->load->model('Form_model');
        $this->load->model('Workbooks_model');

        $this->_campaigns = campaign_access_dropdown();
        $this->workbooks = new WorkbooksApi(array(
            'application_name' => 'PHP test client',
            'user_agent' => 'php_test_client/0.1',
            //'api_key' => '88979-95551-0f48a-1fbbd-9a35b-464f5-48da2-ba6a3', //Prod DB
            'api_key' => '875df-632ea-baee0-12876-632a8-4e89e-c2038-2f61a   ', //Testing DB
        ));
    }


    /**
     * Create a new lead for a record (urn)
     */
    public function create_lead() {

        $urn = $this->uri->segment(3, 0);

        $current_date = new DateTime('now');

        //Get the data for this urn
        $data = $this->Workbooks_model->get_data($urn);
        $data = (isset($data[0])?$data[0]:array());

        $telephone_numbers = explode(',',$data['telephone_numbers']);
        foreach($telephone_numbers as $telephone_number) {
            if (strcmp('07',substr($telephone_number,0,2)) == 0) {
                if (!isset($data['mobile'])) {
                    $data['mobile'] = $telephone_number;
                }
            }
            else {
                if (!isset($data['telephone'])) {
                    $data['telephone'] = $telephone_number;
                }
            }
        }

        $fullname = explode(' ', $data['fullname']);
        foreach ($fullname as $val) {
            if (in_array($val,array("Mr", "Ms", "Miss", "Mrs", "Dr"))) {
                $data['title'] = $val;
            }
            else {
                if (!isset($data['firstname'])) {
                    $data['firstname'] = $val;
                }
                else if (!isset($data['lastname'])) {
                    $data['lastname'] = $val;
                }
            }
        }

        $data = array(
            'person_lead_party[person_personal_title]' => (isset($data['title'])?$data['title']:NULL),
            'name' => $data['fullname'],
            'person_lead_party[person_job_title]' => $data['position'],
            'person_lead_party[person_first_name]' => (isset($data['firstname'])?$data['firstname']:NULL),
            'person_lead_party[person_last_name]' => (isset($data['lastname'])?$data['lastname']:NULL),
            'person_lead_party[person_salutation]' => 'Dear '.$data['fullname'],
            'org_lead_party[main_location[telephone]]' => $data['telephone'],
            'org_lead_party[main_location[mobile]]' => (isset($data['mobile'])?$data['mobile']:NULL),
            'org_lead_party[main_location[email]]' => $data['email'],
            'org_lead_party[name]' => $data['company_name'],
            'org_lead_party[website]' => $data['website'],
            'org_lead_party[main_location[street_address]]' => $data['add1']." ".$data['add2'],
            'org_lead_party[main_location[town]]' => $data['add3'],
            'org_lead_party[main_location[county_province_state]]' => $data['county'],
            'org_lead_party[main_location[postcode]]' => $data['postcode'],
            'org_lead_party[main_location[country]]' => ($data['country']?$data['country']:"United Kingdom"),
            //'org_lead_party[no_phone_soliciting]' => false,
            //'org_lead_party[no_email_soliciting]' => false,
            //'org_lead_party[no_post_soliciting]' => false,
            'lead_source_type' => 'one2one Project',
            'sales_lead_rating_type' => 'Hot',
            'sales_lead_status_type' => 'Open',
            'last_contacted' => $current_date->format('d M Y'),
            'cf_sales_lead_permanent_only' => ($data['temporary_contracts']=='perminant'?true:false),
            'org_lead_party[organisation_num_employees]' => $data['num_of_employees'],
            'cf_sales_lead_no_of_contractors' => $data['num_of_temp_contractors'],
            'cf_sales_lead_ave_contract_rate' => $data['ave_contract_rates']." GBP 0",
            'cf_sales_lead_how_contractors_work' => str_replace(',',', ',$data['how_do_contractors_work']),
            'cf_sales_lead_using_a_competitor' => (isset($data['competitors'])?true:false),
            'cf_sales_lead_main_competitor' => $data['competitors'],
            'cf_sales_lead_uses_a_psl' => (isset($data['psl_exists'])?true:false),
            'cf_sales_lead_psl_review_date' => (isset($data['psl_exists'])?$current_date->format('d M Y'):null),
            'cf_sales_lead_psl_review_person' => $data['psl_exists'],
            //'cf_sales_lead_year_established' => 2002,
            //'org_lead_party[organisation_annual_revenue]' => 0.2,
            'cf_sales_lead_industry_description' => $data['industry_sector'],
        );

        $response = $this->create_data('crm/sales_leads', $data);
        if ($response['success']) {
            $new_lead = $this->get_last_created('crm/sales_leads');
            $lead_data = $new_lead['data'][0];

            //Update the record details in order to associate the lead to the record
            $this->Workbooks_model->update_record_details($urn, $lead_data);
        }
        echo json_encode(array(
            "msg" => $response['flash'],
            "success" => $response['success'],
            "new_lead" => $new_lead
        ));
    }

//    /**
//     * Delete a lead by id
//     */
//    public function delete_lead()
//    {
//
//        $lead_id = $this->uri->segment(3, 0);
//        $lead_lock_version = $this->uri->segment(4, 0);
//
//        $response = $this->delete_data('crm/sales_leads', $lead_id, $lead_lock_version);
//
//        echo json_encode(array(
//            "msg" => $response['flash'],
//            "success" => $response['success']
//        ));
//
//    }
//
//    /**
//     * Get a lead by id
//     */
//    public function get_lead()
//    {
//
//        $lead_id = $this->uri->segment(3, 0);
//
//        $response = $this->get_data_by_id('crm/sales_leads', $lead_id);
//
//        echo json_encode(array(
//            "success" => $response['success'],
//            "data" => $response['data']
//        ));
//
//    }


    /**
     * Delete a lead by id
     */
    public function delete_lead()
    {

        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            $lead_id = $form['lead_id'];
            $lead_lock_version = $form['lead_lock_version'];

            $response = $this->delete_data('crm/sales_leads', $lead_id, $lead_lock_version);

            echo json_encode(array(
                "msg" => $response['flash'],
                "success" => $response['success']
            ));
        }

        echo json_encode(array(
            "msg" => 'ERROR: Lead not found',
            "success" => false
        ));
    }

    /**
     * Get a lead by id
     */
    public function get_lead()
    {

        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            $lead_id = $form['lead_id'];

            $response = $this->get_data_by_id('crm/sales_leads', $lead_id);

            echo json_encode(array(
                "success" => $response['success'],
                "data" => $response['data']
            ));
        }

        echo json_encode(array(
            "msg" => 'ERROR: Lead not found',
            "success" => false
        ));

    }


    /***************************************************************************************************/
    /******************************** PRIVATE FUNCTIONS ************************************************/
    /***************************************************************************************************/


    /**
     * Get the last entry created from a table
     *
     * @param $table
     * @return Array
     */
    private function get_last_created($table)
    {

        $filter_limit_select = array(
            '_start'               => '0',      // Starting from the 'zeroth' record
            '_limit'               => '1',      //   fetch up to 100 records
            '_sort'                => 'id',     // Sort by 'id'
            '_dir'                 => 'DESC'
        );
        $response = $this->workbooks->assertGet($table, $filter_limit_select);

        return $response;
    }


    /**
     * Get an element from a table by id
     *
     * @param $table
     * @return Array
     */
    private function get_data_by_id($table, $lead_id)
    {

        $filter_limit_select = array(
            '_start'               => '0',          // Starting from the 'zeroth' record
            '_limit'               => '1',          //   fetch up to 100 records
            '_sort'                => 'id',         // Sort by 'id'
            '_dir'                 => 'DESC',
            '_ff[]'                => 'id',         // Filter by this column
            '_ft[]'                => 'eq',         //   containing
            '_fc[]'                => $lead_id,      //   'lead id'

        );
        $response = $this->workbooks->assertGet($table, $filter_limit_select);

        return $response;
    }

    /**
     * Insert new data in a table
     *
     * @param $table
     * @param $data
     * @return Array
     */
    private function create_data($table,$data) {
        $response = $this->workbooks->create($table, $data);

        return $response;

    }

    /**
     * Delete an element from a table by id and lock_version
     *
     * @param $table
     * @param $id
     * @param $lock_version
     * @return Array
     */
    private function delete_data($table, $id, $lock_version) {

        $data = array(
            array (
                'id' => $id,
                'lock_version' => 0
            )
        );
        //$response = $this->workbooks->assertDelete('crm/organisations', $object_id_lock_versions);
        $response = $this->workbooks->delete($table, $data);
        var_dump($response);

        return $response;

    }
}
