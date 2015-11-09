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

public function sic_to_subsectors(){
	      if ($this->input->is_ajax_request()) {
			  $sectors = array();
	$sic_codes = $this->input->post('sic_codes');
	$sic_list = ",".implode(",",$sic_codes);
	$query = "select subsector_id,sector_name,subsector_name from subsectors inner join sectors using(sector_id) where subsector_id in('' $sic_list)";
	$result = $this->db->query($query)->result_array();
	foreach($result as $row){
	$sectors[$row['sector_name']][] = array("subsector_id"=>$row['subsector_id'],"subsector_name"=>$row['subsector_name']); 	
	}
	echo json_encode($sectors);
		  }
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

            echo $response;
			
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

            echo $response;
        }
    }

    public function update_company() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
			$subsectors = $form['subsector_id'];
			unset($form['subsector_id']);
			
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
				
				$this->Company_model->update_subsectors($subsectors,$company['company_id']);
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
