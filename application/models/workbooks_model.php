	<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Workbooks_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }

    /**
     * Get the data by urn to insert in the CRM
     *
     * @param $urn
     * @return mixed
     */
    public function get_data($urn) {
        $qry = "SELECT
                  contacts.fullname,
                  contacts.position,
                  group_concat(telephone_number SEPARATOR ',') as telephone_numbers,
                  contacts.email,
                  companies.name as company_name,
                  companies.website,
                  company_addresses.add1,
                  company_addresses.add2,
                  company_addresses.add3,
                  company_addresses.county,
                  company_addresses.postcode,
                  company_addresses.country,
                  webform_answers.a1 as temporary_contracts,
                  webform_answers.a2 as industry_sector,
                  webform_answers.a3 as num_of_employees,
                  webform_answers.a4 as num_of_temp_contractors,
                  webform_answers.a5 as ave_contract_rates,
                  webform_answers.a6 as how_do_contractors_work,
                  webform_answers.a7 as competitors,
                  webform_answers.a8 as psl_exists
				from records
				inner JOIN contacts using (urn)
				left JOIN contact_telephone using (contact_id)
				inner JOIN companies using (urn)
				left JOIN company_addresses using (company_id)
				left JOIN webform_answers using (urn)
				WHERE urn = ".$urn;

        return $this->db->query($qry)->result_array();
    }

    //Update the record with the CRM lead data inserted
    public function update_record_details($urn, $lead_data) {
        $lead_id = $lead_data['id'];
        $lead_lock_version = $lead_data['lock_version'];

        $this->db->where("urn", $urn);
        return $this->db->update("record_details", array(
            'c3' => "<a href=''><span class='view-workbooks-data' item-id='".$lead_id."'>".$lead_id."</span></a>",
            'c4' => $lead_lock_version
        ));
    }
}