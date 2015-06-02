<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Companyhouse extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
        $this->load->model('Company_model');
        $this->load->model('Contacts_model');

        $this->api_key = 'PVPgCUILJkqJu9iQJA3CWCK7OphEIByn7phaapUH';
        $this->url = 'https://api.companieshouse.gov.uk';
    }

    /**
     *
     */
    public function search_companies() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $search = urlencode($form['search']);
            $num_per_page = (isset($form['num_per_page'])?$form['num_per_page']:"");
            $start_index = (isset($form['start_index'])?$form['start_index']:"");

            $response = $this->search($search, $num_per_page, $start_index);

            //echo $response;
			
			echo '{"items":[{"url":"\/company\/02104126","description":"02104126 - Incorporated on 26 February 1987","markdown_description"
:"**02104126** - Incorporated on 26 February 1987","title":"CASTLEMEAD INSURANCE BROKERS LIMITED","description_identifier"
:"ltd-active","description_values":{"company_number":"02104126","company_status":"active","company_type"
:"ltd"},"date_of_creation":"1987-02-26","snippet":"Castlemead House, St. Johns Road, Bristol BS3 1AL"
,"kind":"searchresults#company","markdown_title":"**CASTLEMEAD** **INSURANCE** **BROKERS** LIMITED"}
],"total_results":1,"items_per_page":20,"page_number":1,"kind":"search#","start_index":0}';
        }
    }

    /**
     *
     */
    public function get_company() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $company_no = $form['company_no'];

            $response = $this->get($company_no);

            //echo $response;
			 $json = json_encode('{"undeliverable_registered_office_address":false,"date_of_creation":"1987-02-26","accounts":{"next_due":"2015-09-30","last_accounts":{"made_up_to":"2013-12-31","type":"total-exemption-small"},"next_made_up_to":"2014-12-31","accounting_reference_date":{"day":"31","month":"12"},"overdue":false},"company_number":"02104126","sic_codes":["65120"],"registered_office_address":{"address_line_1":"Castlemead House","address_line_2":"St. Johns Road","locality":"Bristol","postal_code":"BS3 1AL"},"annual_return":{"next_due":"2016-03-15","next_made_up_to":"2016-02-16","overdue":false,"last_made_up_to":"2015-02-16"},"jurisdiction":"england-wales","last_full_members_list_date":"2015-02-16","has_been_liquidated":false,"company_name":"CASTLEMEAD INSURANCE BROKERS LIMITED","type":"ltd","officer_summary":{"resigned_count":11,"active_count":5,"officers":[{"date_of_birth":"1974-03-27","name":"BEECH, Rachael","appointed_on":"2006-12-13","officer_role":"secretary"},{"name":"BEECH, Rachael","date_of_birth":"1974-03-27","officer_role":"director","appointed_on":"2007-05-01"},{"officer_role":"director","appointed_on":"2003-10-01","name":"FROST, Nicholas","date_of_birth":"1974-05-29"},{"appointed_on":"1998-01-05","officer_role":"director","date_of_birth":"1967-06-22","name":"GAMLIN, Clive George"},{"appointed_on":"2003-10-01","officer_role":"director","date_of_birth":"1976-03-01","name":"INGLEBY, Richard James"}]},"etag":"fd90cf1a26939548188f60840b815615d1bc4a0c","company_status":"active","has_insolvency_history":false,"has_charges":true,"can_file":true}');
echo json_decode($json);

        }
    }

    public function update_company() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            if (@!empty($form["date_of_creation"])) {
                $form["date_of_creation"] = to_mysql_datetime($form["date_of_creation"]);
            }

            //Prepare the company
            $company = array(
                'company_id' => $form['company_id'],
                'urn' => $form['urn'],
                'name' => $form['company_name'],
                'conumber' => $form['company_number'],
                'status' => $form['company_status'],
                'date_of_creation' => $form['date_of_creation']
            );

            //Prepare the address
            $company_address = array(
                'company_id' => $form['company_id'],
                'add1' => $form['address_line_1'],
                'add2' => $form['address_line_2'],
                'add3' => $form['locality'],
                'postcode' => $form['postal_code']
            );

            //Prepare the contacts
            $contacts = array();
            if (isset($form['officer'])) {
                $contacts = $form['officer'];
                unset($form['officer']);

            }
            $this->firephp->log($company);
            $this->firephp->log($company_address);

            //Update the company
            if ($response = $this->Company_model->update_company($company)) {

                //Insert a new address for the company
                $company_address_id = $this->Company_model->save_company_address($company_address);

                //Insert new contacts
                foreach($contacts as $contact) {
                    $contact = explode('_',$contact);
                    $contact_name = explode(', ',$contact[0]);
                    $aux = array();

                    $aux['urn'] = $form['urn'];
                    $aux['firstname'] = (isset($contact_name[1])?$contact_name[1]:'');
                    $aux['lastname'] = (isset($contact_name[0])?$contact_name[0]:'');
                    $aux['fullname'] = $aux['firstname'].' '.$aux['lastname'];
                    $aux['position'] = (isset($contact[1])?$contact[1]:'');
                    $aux['dob'] = (isset($contact[2])?$contact[2]:'');
                    $contact = $aux;

                    $contact_id = $this->Contacts_model->save_contact($contact);
                }
            }

            echo json_encode(array(
                "msg" => ($response?'Company updated successfully':'ERROR: The company was NOT updated successfully!'),
                "success" => $response
            ));

        }
    }


    private function search($search, $num_per_page, $start_index) {

        $num_per_page = ($num_per_page?'\&num='.$num_per_page:'');
        $start_index = ($start_index?'\&startIndex='.$start_index:'');

        $response = exec('curl -XGET -u '.$this->api_key.': '.$this->url.'/search/companies?q='.$search.$num_per_page.$start_index);

        return $response;
    }

    private function get($company_no) {

        $response = exec('curl -XGET -u '.$this->api_key.': '.$this->url.'/company/'.$company_no);

        return $response;
    }
}
