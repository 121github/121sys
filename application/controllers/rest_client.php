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
//        user_auth_check(true);

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

            if (isset($record_contact_data['title']) && isset($record_contact_data['firstname']) && isset($record_contact_data['lastname'])) {
                $record_contact_data['fullname'] = $record_contact_data['title']." ".$record_contact_data['firstname']." ".$record_contact_data['lastname'];
            }
            $addresses = array();
            if (isset($record_contact_data['addresses'])) {
                $addresses = $record_contact_data['addresses'];
                unset($record_contact_data['addresses']);
            }

            $telephone_number = "";
            if (isset($record_contact_data['telephone_number'])) {
                $telephone_number = $record_contact_data['telephone_number'];
                unset($record_contact_data['telephone_number']);
            }

            $record_contact = $this->rest->post('record_contact', $record_contact_data);

            if ($record_contact['contact_id']) {
                $record_contact['success'] = $record_contact;
                foreach($addresses as $address) {
                    //Save address
                    $address = explode("/",$address);
                    $contact_address_data['add1'] = (isset($address[0])?$address[0]:"");
                    $contact_address_data['postcode'] = (isset($address[1])?$address[1]:"");
                    $contact_address_data['contact_id'] = $record_contact['contact_id'];
                    $record_contact_address = $this->rest->post('contact_address', $contact_address_data);

                    if (!isset($record_contact['addresses'])) {
                        $record_contact['addresses'] = array();
                    }
                    array_push($record_contact['addresses'], $record_contact_address);
                }

                if ($telephone_number != "") {
                    //Save telephone
                    $contact_telephone_data['telephone_number'] = $telephone_number;
                    $contact_telephone_data['contact_id'] = $record_contact['contact_id'];
                    $record_contact_telephone = $this->rest->post('contact_telephone', $contact_telephone_data);

                    $record_contact['telephone_number'] = $record_contact_telephone;
                }
            }
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