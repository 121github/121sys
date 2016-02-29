<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Api extends REST_Controller
{

    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  USERS  ***********************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    function user_get()
    {
        $this->load->model('User_model');
        $user = $this->User_model->get_user_by_id( $this->get('id') );

        if($user)
        {
            $this->response($user, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }

    function users_get()
    {
        $this->load->model('User_model');
        $users = $this->User_model->get_users($this->get('limit') );

        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }

    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  RECORDS  *********************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    function record_get()
    {
        $this->load->model('Records_model');
        $this->load->model('Contacts_model');
        $this->load->model('Company_model');

        $record = $this->Records_model->get_records_by_urn( $this->get('urn'), array("2"));
        $record_details = $this->Records_model->get_record_details_by_urn( $this->get('urn'));
        $record_contacts = $this->Contacts_model->get_contacts( $this->get('urn'));
        $record_companies = $this->Company_model->get_companies( $this->get('urn'));

        $record['contacts'] = $record_contacts;
        $record['companies'] = $record_companies;
        $record['record_details'] = $record_details;

        if($record)
        {
            $this->response($record, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }

    function record_post()
    {
        $this->load->model('Records_model');

        $record = $this->post();

        //Insert record
        if (!isset($record['urn']) || $record['urn'] == '') {
            $record_urn = $this->Records_model->save_record($record);

            $message = array(
                'success' => ($record_urn?true:false),
                'urn' => ($record_urn?$record_urn:''),
                'message' => ($record_urn?'INSERTED!':'ERROR: The records was not inserted successfully!')
            );
        }
        //Update record
        else {
            //$result = $this->Records_model->update_record($record);

            $message = array(
                'success' => false,
                'urn' => $this->get('urn'),
                'message' => 'The update is not available yet!'
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

//    function record_delete()
//    {
//        //$this->some_model->deletesomething( $this->get('id') );
//        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');
//
//        $this->response($message, 200); // 200 being the HTTP response code
//    }

    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  RECORD DETAILS  **************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    function record_details_get()
    {
        $this->load->model('Records_model');

        $record_details = $this->Records_model->get_record_details_by_urn( $this->get('urn'));


        if($record_details)
        {
            $this->response($record_details, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }

    function record_details_post()
    {
        $this->load->model('Records_model');

        $record_details = $this->post();

        if (!isset($record_details['urn']) || $record_details['urn'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'message' => "ERROR: The urn is undefined or it doesn't exists!"
            );

        }
        else {

            $record_details_id = $this->Records_model->save_additional_info($record_details);

            $message = array(
                'success' => ($record_details_id?true:false),
                'urn' => $record_details['urn'],
                'detail_id' => ($record_details_id?$record_details_id:''),
                'message' => ($record_details_id?'SAVED!':'ERROR: The record details were saved successfully!')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  RECORD CONTACT ***************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    function record_contact_get()
    {
        $this->load->model('Contacts_model');

        $record_contact = $this->Contacts_model->get_contact( $this->get('id'));


        if($record_contact)
        {
            $this->response($record_contact, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }


    function record_contact_post()
    {
        $this->load->model('Contacts_model');

        $record_contact = $this->post();

        if (!isset($record_contact['urn']) || $record_contact['urn'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'message' => "ERROR: The urn is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert record company
            if (!isset($record_contact['company_id']) || $record_contact['company_id'] == '') {
                $record_contact_id = $this->Contacts_model->save_contact($record_contact);

                $message = array(
                    'success' => ($record_contact_id?true:false),
                    'urn' => $record_contact['urn'],
                    'contact_id' => ($record_contact_id?$record_contact_id:''),
                    'message' => ($record_contact_id?'INSERTED!':'ERROR: The record contact was inserted successfully!')
                );
            }
            //Update record contact
            else {
                $result = $this->Contacts_model->update_company($record_contact);

                $message = array(
                    'success' => ($result?true:false),
                    'urn' => $record_contact['urn'],
                    'contact_id' => $record_contact['contact_id'],
                    'message' => ($result?'UPDATED!':'ERROR: The record contact was updated successfully!')
                );
            }
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    //Insert/Update contact address
    function contact_address_post()
    {
        $this->load->model('Contacts_model');

        $contact_address = $this->post();

        if (!isset($contact_address['contact_id']) || $contact_address['contact_id'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'message' => "ERROR: The contact_id is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert/Update contact address
            $contact_address_id = $this->Contacts_model->save_contact_address($contact_address);

            $message = array(
                'success' => ($contact_address_id?true:false),
                'contact_id' => $contact_address['contact_id'],
                'address_id' => ($contact_address_id?$contact_address_id:''),
                'message' => ($contact_address_id?'INSERTED!':'ERROR: The contact address was inserted successfully!')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    //Insert/Update contact telephone
    function contact_telephone_post()
    {
        $this->load->model('Contacts_model');

        $contact_telephone = $this->post();

        if (!isset($contact_telephone['contact_id']) || $contact_telephone['contact_id'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'message' => "ERROR: The contact_id is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert/Update contact address
            $contact_telephone_id = $this->Contacts_model->save_contact_telephone($contact_telephone);

            $message = array(
                'success' => ($contact_telephone_id?true:false),
                'contact_id' => $contact_telephone['contact_id'],
                'address_id' => ($contact_telephone_id?$contact_telephone_id:''),
                'message' => ($contact_telephone_id?'INSERTED!':'ERROR: The contact telephone was inserted successfully!')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    /*******************************************************************************************************/
    /*******************************************************************************************************/
    /***********************  RECORD COMPANY  **************************************************************/
    /*******************************************************************************************************/
    /*******************************************************************************************************/

    /**
     * Get company details by id
     */
    function record_company_get()
    {
        $this->load->model('Company_model');

        $record_company = $this->Company_model->get_company( $this->get('id'));


        if($record_company)
        {
            $this->response($record_company, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(NULL, 404);
        }
    }

    //Insert/Update company record
    function record_company_post()
    {
        $this->load->model('Company_model');

        $record_company = $this->post();

        if (!isset($record_company['urn']) || $record_company['urn'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'message' => "ERROR: The urn is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert record company
            if (!isset($record_company['company_id']) || $record_company['company_id'] == '') {
                $record_company_id = $this->Company_model->save_company($record_company);

                $message = array(
                    'success' => ($record_company_id?true:false),
                    'urn' => $record_company['urn'],
                    'company_id' => ($record_company_id?$record_company_id:''),
                    'message' => ($record_company_id?'INSERTED!':'ERROR: The record comapny was inserted successfully!')
                );
            }
            //Update record contact
            else {
                $result = $this->Company_model->update_company($record_company);

                $message = array(
                    'success' => ($result?true:false),
                    'urn' => $record_company['urn'],
                    'company_id' => $record_company['company_id'],
                    'message' => ($result?'UPDATED!':'ERROR: The record comapny was updated successfully!')
                );
            }
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    //Insert/Update company address
    function company_address_post()
    {
        $this->load->model('Company_model');

        $company_address = $this->post();

        if (!isset($company_address['company_id']) || $company_address['company_id'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'message' => "ERROR: The company_id is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert/Update company address
            $company_address_id = $this->Company_model->save_company_address($company_address);

            $message = array(
                'success' => ($company_address_id?true:false),
                'company_id' => $company_address['company_id'],
                'address_id' => ($company_address_id?$company_address_id:''),
                'message' => ($company_address_id?'SAVED!':'ERROR: The comapny address was saved successfully!')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }

    //Insert/Update company telephone
    function company_telephone_post()
    {
        $this->load->model('Company_model');

        $company_telephone = $this->post();

        if (!isset($company_telephone['company_id']) || $company_telephone['company_id'] == '') {
            $message = array(
                'success' => false,
                'urn' => '',
                'message' => "ERROR: The company_id is undefined or it doesn't exists!"
            );

        }
        else {

            //Insert/Update company telephone
            $company_telephone_id = $this->Company_model->save_company_telephone($company_telephone);

            $message = array(
                'success' => ($company_telephone_id?true:false),
                'company_id' => $company_telephone['company_id'],
                'address_id' => ($company_telephone_id?$company_telephone_id:''),
                'message' => ($company_telephone_id?'SAVED!':'ERROR: The comapny telephone was saved successfully!')
            );
        }

        $this->response($message, 200); // 200 being the HTTP response code
    }
}