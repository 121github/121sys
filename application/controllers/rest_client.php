<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 *
 * @package		CodeIgniter
 * @subpackage	Rest Client Controller
 * @category	Controller
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
//require APPPATH.'/libraries/REST_Controller.php';

class Rest_client extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);

        $this->load->library('rest', array(
            'server' => base_url().'api/',
            'http_user' => 'admin',
            'http_pass' => '1234',
            'http_auth' => 'digest' // or 'basic'
        ));

    }

    function get_record($urn = null)
    {
        if ($this->input->is_ajax_request()) {
            $urn = $this->input->post('urn');
        }

        if (!isset($urn) || $urn == '') {
            $record = array(
                "success" => false,
                "msg" => "No record found"
            );
        }
        else {
            $record = $this->rest->get('record', array('urn' => $urn), 'json');
        }

        echo (json_encode($record));
    }

    /**
     * Insert/Update a record through the api
     */
    function save_record()
    {
        if (!$this->input->post()) {
            $record = array(
                "success" => false,
                "msg" => "It is not a post request"
            );
        }
        else {

            $record_data = $this->input->post();
            $record = $this->rest->post('record', $record_data);
        }

        echo (json_encode($record));
    }


    /**
     * Insert/Update record details
     */
    function save_record_details() {
        if (!$this->input->post()) {
            $record_details = array(
                "success" => false,
                "msg" => "It is not a post request"
            );
        }
        else {
            $record_details_data = $this->input->post();
            $record_details = $this->rest->post('record_details', $record_details_data);
        }

        echo (json_encode($record_details));
    }

    /**
     * Insert/Update record contact
     */
    function save_record_contact() {
        if (!$this->input->post()) {
            $record_contact = array(
                "success" => false,
                "msg" => "It is not a post request"
            );
        }
        else {
            $record_contact_data = $this->input->post();

//            $telephone = array();
//            $address = array();
//
//            if (isset($record_contact_data['title'])) {
//
//            }

            $record_contact = $this->rest->post('record_contact', $record_contact_data);
        }

        echo (json_encode($record_contact));
    }

    /**
     * Insert/Update record company
     */
    function save_record_company() {
        if (!$this->input->post()) {
            $record_company = array(
                "success" => false,
                "msg" => "It is not a post request"
            );
        }
        else {
            $record_company_data = $this->input->post();
            $record_company = $this->rest->post('record_company', $record_company_data);
        }

        echo (json_encode($record_company));
    }
}