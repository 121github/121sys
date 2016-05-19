<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ajax extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->_campaigns = campaign_access_dropdown();
        $this->load->model('Company_model');
        $this->load->model('Contacts_model');
        $this->load->model('Records_model');
        $this->load->model('Survey_model');
        $this->load->model('User_model');
        $this->load->model('Form_model');
        $this->load->model('Audit_model');
        $this->load->helper('array');
        $this->load->helper('misc');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
    }
	public function delete_data_panel(){
		$data_id = $this->input->post('data_id');
		$this->db->where(array("data_id"=>$data_id));	
		$this->db->delete("custom_panel_data");
		$this->db->where(array("data_id"=>$data_id));	
		$this->db->delete("custom_panel_values");
		echo json_encode(array("success"=>true));	
	}

	public function delete_view(){
	$id = $this->input->post('id');
	$this->db->where(array("view_id"=>$id,"user_id"=>$_SESSION['user_id']));
	$this->db->delete("datatables_views");
	echo json_encode(array("success"=>true));	
	}

    public function change_theme()
    {
        if ($this->input->post('theme')) {
            $_SESSION['theme_color'] = $this->input->post('theme');
            $this->db->where("user_id", $_SESSION['user_id']);
            $this->db->update("users", array("theme_color" => $this->input->post('theme')));
        }
    }

    public function suppress_by_urn()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $urn = $this->input->post('urn');
            $numbers = $this->Contacts_model->get_numbers($urn);
            $campaign = $this->Records_model->get_campaign_from_urn($urn);
            $reason = "Suppressed by " . $_SESSION['name'];
            $campaigns = array($campaign);
            foreach ($numbers as $number) {
                $suppression_id = $this->Data_model->insert_suppression($number, $reason);
                $this->Data_model->save_suppression_by_campaign($suppression_id, $campaigns);
            }
        }
        echo json_encode(array("success" => true, "post" => $form));
    }

    public function get_campaigns()
    {
        $campaigns = $this->Form_model->get_campaigns();
        echo json_encode(array(
            "data" => $campaigns
        ));
    }

    public function save_record_options()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            //checks
            if (empty($form['campaign_id'])) {
                unset($form['campaign_id']);
            }
            if (empty($form['source_id'])) {
                unset($form['source_id']);
            }
            if (empty($form['pot_id']) && isset($form['pot_id'])) {
                $form['pot_id'] = NULL;
            }
            if (empty($form['parked_code']) && isset($form['parked_code'])) {
                $form['parked_code'] = NULL;
            }
            if (empty($form['record_color']) && isset($form['record_color'])) {
                $form['record_color'] = NULL;
            }
            if (empty($form['map_icon']) && isset($form['map_icon'])) {
                $form['map_icon'] = NULL;
            }
            $this->Records_model->save_record_options($form);
            echo json_encode(array("success" => true, "post" => $form));
        }

    }

    public function pots_in_campaign()
    {
        $campaign = $this->input->post("campaign");
        $pots = $this->Form_model->pots_in_campaign($campaign);
        echo json_encode($pots);
    }

//this is only for hsl we should move it somewhere nice when we can
    public function add_cover_letter_address()
    {
        $this->firephp->log($this->input->post('coverletter_address'));
        if ($this->input->post('coverletter_address') || $this->input->post('coverletter_address') != '') {
            $_SESSION['cover_letter_address'] = $this->input->post('coverletter_address');
        } else {
            unset($_SESSION['cover_letter_address']);
        }
    }

    public function update_branch_locations()
    {
        $this->load->model('Planner_model');
        foreach ($this->db->query("select branch_id,postcode from branch_addresses")->result_array() as $row) {
            $postcode = $row['postcode'];
            $location_id = $this->Planner_model->get_location_id($postcode);
            $this->db->where("branch_id", $row['branch_id']);
            $this->db->update("branch_addresses", array("location_id" => $location_id));
        }
    }

    public function get_branch_info()
    {
        session_write_close();
        $result = null;
        $this->load->model('Branches_model');
        if ($this->input->post('postcode') && !$this->input->post('region_id')) {
            $result = $this->Branches_model->get_branch_info(false, $this->input->post('postcode'));
        } else if ($this->input->post('region_id')) {
            $result = $this->Branches_model->get_branch_info($this->input->post('region_id'), $this->input->post('postcode'));
        }
        echo json_encode($result);
    }

    public function fix_eldon()
    {
        $qry = "select urn,name from record_details left join users on users.name like concat(c3,'%')";
        $res = $this->db->query($qry)->result_array();
        foreach ($res as $k => $row) {
            echo "update record_details set c3 = '{$row['name']}' where urn = {$row['urn']};";
            echo "<br>";
        }
    }

    public function validate_postcode()
    {
        if ($this->input->is_ajax_request()) {
            $error = false;
			$postcode = $this->input->post('postcode');
				  if (validate_postcode($postcode)) {
						$postcode = postcodeFormat($postcode);
                    } else {
                        $error = "Postcode " . $postcode . " is not valid";
                    }
            if ($error) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => $error
                ));
            } else {
                echo json_encode(array(
                    "success" => true,
                    "postcode" => postcodeFormat($postcode)
                ));
            }
        }
    }

    public function get_table_columns()
    {
        $array = array();
        $array['Record Fields'] = array(
            "r.dials" => "Dials",
            "records.last_updated" => "Last Updated",
            "outcomes.outcome" => "Last Outcome",
            "users.name" => "Updated By"
        );

        $array['Contact Fields'] = array(
            "contacts.fullname" => "Contact Name",
            "contacts.dob" => "Contact DOB",
            "contacts.email" => "Contact Email",
            "contact_telephone.telephone_number" => "Contact Telepohone",
            "contact_addresses.postcode" => "Contact Postcode"
        );

        $array['Company Fields'] = array(
            "companies.name" => "Company Name",
            "companies.conumber" => "Company Number",
            "companies.email" => "Company Email",
            "companies.website" => "Company Website",
            "companies.employees" => "Employees"
        );

        echo json_encode($array);
    }

    //this function returns a json array of contact data for a given contact id
    public function get_contact()
    {
        if ($this->input->is_ajax_request()) {
            $id = intval($this->input->post('id'));
            $contact = $this->Contacts_model->get_contact($id);
            echo json_encode(array(
                "success" => true,
                "data" => $contact
            ));
        }
    }

    //this function returns a json array of referral data for a given referral id
    public function get_referral()
    {
        if ($this->input->is_ajax_request()) {
            $id = intval($this->input->post('id'));
            $referral = $this->Contacts_model->get_referral($id);
            echo json_encode(array(
                "success" => true,
                "data" => $referral
            ));
        }
    }

    //this function returns a json array of company data for a given company id
    public function get_company()
    {
        if ($this->input->is_ajax_request()) {
            $id = intval($this->input->post('id'));
            $company = $this->Company_model->get_company($id);
            echo json_encode(array(
                "success" => true,
                "data" => $company
            ));
        }
    }

    //this function returns a json array of all contacts for a given contact urn
    public function get_contacts()
    {
        if ($this->input->is_ajax_request()) {
            $urn = intval($this->input->post('urn'));
            $contacts = $this->Contacts_model->get_contacts($urn);
            echo json_encode(array(
                "success" => true,
                "data" => $contacts
            ));
        }
    }

    //this function returns a json array of all referrals for a given urn
    public function get_referrals()
    {
        if ($this->input->is_ajax_request()) {
            $urn = intval($this->input->post('urn'));
            $referrals = $this->Contacts_model->get_referrals($urn);
            echo json_encode(array(
                "success" => true,
                "data" => $referrals
            ));
        }
    }

    //this function returns a json array of all contacts for a given contact urn
    public function get_companies()
    {
        if ($this->input->is_ajax_request()) {
            $urn = intval($this->input->post('urn'));
            $companies = $this->Company_model->get_companies($urn);
            echo json_encode(array(
                "success" => true,
                "data" => $companies,
                "count" => count($companies)
            ));
        }
    }

    //this function returns the script data for a given script_id
    public function get_script()
    {
        if ($this->input->is_ajax_request()) {
            $id = intval($this->input->post('id'));
            $this->db->where("script_id", intval($this->input->post('id')));
            $result = $this->db->get("scripts")->row_array();
            $result['script'] = nl2br($result['script']);
            echo json_encode(array(
                "success" => true,
                "data" => $result
            ));
        }
    }

    //this function saves contact data to the database. The post names should match the database field names. 
    public function save_contact()
    {
        if ($this->input->is_ajax_request()) {
            $array = $this->input->post();

            $array["position"] = (@!empty($array["position"]) ? $array["position"] : NULL);
            $array["dob"] = (@!empty($array["dob"]) ? to_mysql_datetime($array["dob"]) : NULL);
            $array["email"] = (@!empty($array["email"]) ? $array["email"] : NULL);
            $array["website"] = (@!empty($array["website"]) ? $array["website"] : NULL);
            $array["facebook"] = (@!empty($array["facebook"]) ? $array["facebook"] : NULL);
            $array["linkedin"] = (@!empty($array["linkedin"]) ? $array["linkedin"] : NULL);
            $array["notes"] = (@!empty($array["notes"]) ? $array["notes"] : NULL);

            $array = array_map('trim', $array);
            $audit_id = $this->Audit_model->log_contact_update(array_filter($array), $array['urn']);
            $array["date_updated"] = date('Y-m-d H:i:s');
            $this->db->where("contact_id", intval($this->input->post('contact_id')));
            if ($this->db->update('contacts', $array)) {
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('contact_id'))
                ));
            }
        }
    }

    //this function saves referral data to the database. The post names should match the database field names.
    public function save_referral()
    {
        if ($this->input->is_ajax_request()) {
            $array = $this->input->post();

            $this->db->where("referral_id", intval($this->input->post('referral_id')));
            if ($this->db->update('referral', $array)) {
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('referral_id'))
                ));
            }
        }
    }

    //this function saves company data to the database. The post names should match the database field names. 
    public function save_company()
    {
        if ($this->input->is_ajax_request()) {
            $array = $this->input->post();

            $audit_id = $this->Audit_model->log_company_update(array_filter($array), $array['urn']);

            $array["date_updated"] = date('Y-m-d H:i:s');

            $array["name"] = (@!empty($array["name"]) ? $array["name"] : NULL);
            $array["description"] = (@!empty($array["description"]) ? $array["description"] : NULL);
            $array["conumber"] = (@!empty($array["conumber"]) ? $array["conumber"] : NULL);
            $array["email"] = (@!empty($array["email"]) ? $array["email"] : NULL);
            $array["website"] = (@!empty($array["website"]) ? $array["website"] : NULL);
            $array["employees"] = (@($array["employees"]) != "null" && @($array["employees"]) != "" ? $array["employees"] : NULL);
            $array["turnover"] = (@($array["turnover"]) != "null" && @($array["turnover"]) != "" ? $array["turnover"] : NULL);
            $array["date_of_creation"] = (@!empty($array["date_of_creation"]) ? to_mysql_datetime($array["date_of_creation"]) : NULL);

            $this->db->where("company_id", intval($this->input->post('company_id')));
            if ($this->db->update('companies', $array)):
                $this->firephp->log($this->db->last_query());
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('company_id'))
                ));
            endif;
        }
    }

    //this function saves contact data to the database. The post names should match the database field names. 
    public function add_contact()
    {
        if ($this->input->is_ajax_request()) {
            $array = $this->input->post();

            $array["position"] = (@!empty($array["position"]) ? $array["position"] : NULL);
            $array["dob"] = (@!empty($array["dob"]) ? to_mysql_datetime($array["dob"]) : NULL);
            $array["email"] = (@!empty($array["email"]) ? $array["email"] : NULL);
            $array["website"] = (@!empty($array["website"]) ? $array["website"] : NULL);
            $array["facebook"] = (@!empty($array["facebook"]) ? $array["facebook"] : NULL);
            $array["linkedin"] = (@!empty($array["linkedin"]) ? $array["linkedin"] : NULL);
            $array["notes"] = (@!empty($array["notes"]) ? $array["notes"] : NULL);


            if ($this->db->insert('contacts', array_filter($array))) {
                $id = $this->db->insert_id();
                $array['contact_id'] = $id;
                $this->Audit_model->log_contact_insert(array_filter($array), $array['urn']);
                echo json_encode(array(
                    "success" => true,
                    "id" => $id
                ));
            }
        }
    }

    //this function saves referral data to the database. The post names should match the database field names.
    public function add_referral()
    {
        if ($this->input->is_ajax_request()) {
            $array = $this->input->post();

            if ($this->db->insert('referral', array_filter($array))) {
                $id = $this->db->insert_id();
                $array['referral_id'] = $id;
                echo json_encode(array(
                    "success" => true,
                    "id" => $id
                ));
            }
        }
    }

    //this function saves company data to the database. The post names should match the database field names. 
    public function add_company()
    {
        if ($this->input->is_ajax_request()) {
            $array = $this->input->post();

            $array["name"] = (@!empty($array["name"]) ? $array["name"] : NULL);
            $array["description"] = (@!empty($array["description"]) ? $array["description"] : NULL);
            $array["conumber"] = (@!empty($array["conumber"]) ? $array["conumber"] : NULL);
            $array["email"] = (@!empty($array["email"]) ? $array["email"] : NULL);
            $array["website"] = (@!empty($array["website"]) ? $array["website"] : NULL);
            $array["employees"] = (@!empty($array["employees"]) ? $array["employees"] : NULL);
            $array["date_of_creation"] = (@!empty($array["date_of_creation"]) ? to_mysql_datetime($array["date_of_creation"]) : NULL);

            if ($this->db->insert('companies', array_filter($array))):
                $id = $this->db->insert_id();
                $array['company_id'] = $id;
                $this->Audit_model->log_contact_insert(array_filter($array), $array['urn']);
                echo json_encode(array(
                    "success" => true,
                    "id" => $id,
                ));
            endif;
        }
    }

    //return a contact phone number from an id 
    public function get_contact_number()
    {
        if ($this->input->is_ajax_request()) {
            $this->db->where('telephone_id', $this->input->post('id'));
            $result = $this->db->get('contact_telephone')->row_array();
            $result['type'] = "phone";
            $result['success'] = true;
            if ($result):
                echo json_encode($result);
            endif;
        }
    }

    //return a company phone number from an id 
    public function get_company_number()
    {
        if ($this->input->is_ajax_request()) {
            $this->db->where('telephone_id', $this->input->post('id'));
            $result = $this->db->get('company_telephone')->row_array();
            $result['type'] = "phone";
            $result['success'] = true;
            if ($result):
                echo json_encode($result);
            endif;
        }
    }

    //return a contact address from an id 
    public function get_contact_address()
    {
        if ($this->input->is_ajax_request()) {
            $this->db->where('address_id', $this->input->post('id'));
            $result = $this->db->get('contact_addresses')->row_array();
            $result['type'] = "address";
            $result['success'] = true;
            if ($result):
                echo json_encode($result);
            endif;
        }
    }

    //return a referral address from an id
    public function get_referral_address()
    {
        if ($this->input->is_ajax_request()) {
            $this->db->where('address_id', $this->input->post('id'));
            $result = $this->db->get('referral_address')->row_array();
            $result['type'] = "address";
            $result['success'] = true;
            if ($result):
                echo json_encode($result);
            endif;
        }
    }

    //return a company address from an id 
    public function get_company_address()
    {
        if ($this->input->is_ajax_request()) {
            $this->db->where('address_id', $this->input->post('id'));
            $result = $this->db->get('company_addresses')->row_array();
            $result['type'] = "address";
            $result['success'] = true;
            if ($result):
                echo json_encode($result);
            endif;
        }
    }

    //this function delete contact and associated data for a given id
    public function delete_contact()
    {
        if ($this->input->is_ajax_request()) {
            $this->Audit_model->log_contact_delete($this->input->post('contact'));
            $this->db->where('contact_id', $this->input->post('contact'));
            if ($this->db->delete('contacts')):
                //if the contact is deleted then remove the phone numbers
                $this->db->where('contact_id', $this->input->post('contact'));
                $this->db->delete('contact_telephone');
                //if the contact is deleted then remove the addresses
                $this->db->where('contact_id', $this->input->post('contact'));
                $this->db->delete('contact_addresses');
                echo json_encode(array(
                    "success" => true
                ));
            endif;
        }
    }

    //this function delete referral and associated data for a given id
    public function delete_referral()
    {
        if ($this->input->is_ajax_request()) {
            $this->db->where('referral_id', $this->input->post('referral'));
            if ($this->db->delete('referral')):
                //if the contact is deleted then remove the addresses
                $this->db->where('referral_id', $this->input->post('referral'));
                $this->db->delete('referral_address');
                echo json_encode(array(
                    "success" => true
                ));
            endif;
        }
    }

    //this function delete contact and associated data for a given id
    public function delete_company()
    {
        if ($this->input->is_ajax_request()) {
            $this->Audit_model->log_company_delete($this->input->post('company'));
            $this->db->where('company_id', $this->input->post('company'));
            if ($this->db->delete('companies')):
                //if the contact is deleted then remove the phone numbers
                $this->db->where('company_id', $this->input->post('company'));
                $this->db->delete('company_telephone');
                //if the contact is deleted then remove the addresses
                $this->db->where('company_id', $this->input->post('company'));
                $this->db->delete('company_addresses');
                echo json_encode(array(
                    "success" => true
                ));
            endif;
        }
    }

    //this function delete surveys and answers
    public function delete_survey()
    {
        if ($this->input->is_ajax_request()) {
            $this->db->where('survey_id', intval($this->input->post('survey')));
            $this->db->delete('survey_answers');
            $this->db->where('survey_id', $this->input->post('survey'));
            if ($this->db->delete('surveys')):
                echo json_encode(array(
                    "success" => true
                ));
            endif;
        }
    }

    //this function edits contact phone numbers
    public function edit_phone()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $msg = false;
            $d = preg_replace('/[0-9]/', '', $data['description']);
            if (empty($d)) {
                $msg = "The telephone description should be text. Eg Home, Office, Mobile etc";
            }
            if (empty($data['description'])) {
                $msg = "Description cannot be empty";
            }
            $data['telephone_number'] = numbers_only($data['telephone_number']);
            if (empty($data['telephone_number'])) {
                $msg = "Phone number is invalid";
            }
            if ($msg) {
                echo json_encode(array("success" => false, "msg" => $msg));
                exit;
            }
            $urn = $this->db->get_where("contacts", array("contact_id" => $data['contact_id']))->row()->urn;
            $this->Audit_model->log_phone_update($data, $urn);

            $this->db->where('telephone_id', $data['telephone_id']);
            if ($this->db->update('contact_telephone', elements(array(
                "contact_id",
                "telephone_number",
                "description",
                "tps"
            ), array_filter($data, 'strlen'), null))
            ):

                echo json_encode(array(
                    "success" => true,
                    "id" => intval($data['contact_id']),
                    "type" => "phone"
                ));
            endif;
        }
    }

    //this function edits company phone numbers
    public function edit_cophone()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $msg = false;
            $d = preg_replace('/[0-9]/', '', $data['description']);
            if (empty($d)) {
                $msg = "The telephone description should be text. Eg Home, Office, Mobile etc";
            }
            if (empty($data['description'])) {
                $msg = "Description cannot be empty";
            }
            $data['telephone_number'] = numbers_only($data['telephone_number']);
            if (empty($data['telephone_number'])) {
                $msg = "Phone number is invalid";
            }
            if ($msg) {
                echo json_encode(array("success" => false, "msg" => $msg));
                exit;
            }
            $urn = $this->db->get_where("companies", array("company_id" => $data['company_id']))->row()->urn;
            $this->Audit_model->log_cophone_update($data, $urn);

            $this->db->where('telephone_id', $data['telephone_id']);
            if ($this->db->update('company_telephone', elements(array(
                "company_id",
                "telephone_number",
                "description",
                "ctps"
            ), array_filter($data, 'strlen'), null))
            ):


                echo json_encode(array(
                    "success" => true,
                    "id" => intval($data['company_id']),
                    "type" => "phone"
                ));
            endif;
        }
    }

    //this function adds contact phonne numbers
    public function add_phone()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $msg = false;
            $d = preg_replace('/[0-9]/', '', $data['description']);
            if (empty($d)) {
                $msg = "The telephone description should be text. Eg Home, Office, Mobile etc";
            }
            if (empty($data['description'])) {
                $msg = "Description cannot be empty";
            }
            $data['telephone_number'] = numbers_only($data['telephone_number']);
            if (empty($data['telephone_number'])) {
                $msg = "Phone number is invalid";
            }
            if ($msg) {
                echo json_encode(array("success" => false, "msg" => $msg));
                exit;
            }

            $data['tps'] = ($data['tps'] == NULL ? NULL : $data['tps']);
            if ($this->db->insert('contact_telephone', $data)):
                $data['telephone_id'] = $this->db->insert_id();
                $urn = $this->db->get_where("contacts", array("contact_id" => $data['contact_id']))->row()->urn;
                $this->Audit_model->log_phone_insert($data, $urn);
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($data['contact_id']),
                    "type" => "phone"
                ));
            endif;
        }
    }

    //this function adds company phonne numbers
    public function add_cophone()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $msg = false;
            $d = preg_replace('/[0-9]/', '', $data['description']);
            if (empty($d)) {
                $msg = "The telephone description should be text. Eg Home, Office, Mobile etc";
            }
            if (empty($data['description'])) {
                $msg = "Description cannot be empty";
            }
            $data['telephone_number'] = numbers_only($data['telephone_number']);
            if (empty($data['telephone_number'])) {
                $msg = "Phone number is invalid";
            }
            if ($msg) {
                echo json_encode(array("success" => false, "msg" => $msg));
                exit;
            }
            $data['ctps'] = ($data['ctps'] == NULL ? NULL : $data['ctps']);
            if ($this->db->insert('company_telephone', $data)):
                $data['telephone_id'] = $this->db->insert_id();
                $urn = $this->db->get_where("companies", array("company_id" => $data['company_id']))->row()->urn;
                $this->Audit_model->log_cophone_insert($data, $urn);
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($data['company_id']),
                    "type" => "phone"
                ));
            endif;
        }
    }

    //this function deletes contact phone numbers
    public function delete_phone()
    {
        if ($this->input->is_ajax_request()) {
            $id = intval($this->input->post('id'));
            $urn = $this->db->query("select urn from contacts left join contact_telephone using(contact_id) where telephone_id = '$id'")->row()->urn;
            $this->Audit_model->log_phone_delete($id, $urn);

            $this->db->where(array('telephone_id' => $id));
            if ($this->db->delete('contact_telephone')):
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('id')),
                    "type" => "phone"
                ));
            endif;
        }
    }

    //this function deletes company phone numbers
    public function delete_cophone()
    {
        if ($this->input->is_ajax_request()) {
            $id = intval($this->input->post('id'));
            $urn = $this->db->query("select urn from companies left join company_telephone using(company_id) where telephone_id = '$id'")->row()->urn;
            $this->Audit_model->log_cophone_delete($id, $urn);

            $this->db->where(array('telephone_id' => $id));
            if ($this->db->delete('company_telephone')):
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('id')),
                    "type" => "phone"
                ));
            endif;
        }
    }

    //this function edits contact address
    public function edit_address()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->helper('location');
            $data = $this->input->post();
            $data['postcode'] = postcodeCheckFormat($data['postcode']);
            if (!$data['postcode']) {
                echo json_encode(array("success" => false, "msg" => "Please enter a valid postcode"));
                exit;
            }
            if ($this->input->post("primary") == "1") {
                $this->db->where("contact_id", intval($this->input->post('contact_id')));
                $this->db->update("contact_addresses", array(
                    "primary" => NULL
                ));
            }

            $urn = $this->db->get_where("contacts", array("contact_id" => $data['contact_id']))->row()->urn;
            $this->Audit_model->log_address_update($data, $urn);

            //delete the location id incase the postcode has changed
            $this->db->where('address_id', intval($this->input->post('address_id')));
            $this->db->update('contact_addresses', array(
                "location_id" => NULL
            ));

            $this->db->where('address_id', intval($this->input->post('address_id')));
            if ($this->db->update('contact_addresses', elements(array(
                "description",
                "add1",
                "add2",
                "add3",
                "add4",
                "locality",
                "city",
                "county",
                "country",
                "postcode",
                "contact_id",
                "primary",
                "visible"
            ), $data))
            ):

                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('contact_id')),
                    "type" => "address"
                ));
            endif;
        }
        $this->load->model('Locations_model');
        //set the location id on the appointment
        $this->Locations_model->set_location_id($this->input->post('postcode'));
    }

    //this function edits referral address
    public function edit_referral_address()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->helper('location');
            $data = $this->input->post();
            $data['postcode'] = postcodeCheckFormat($data['postcode']);
            if (!$data['postcode']) {
                echo json_encode(array("success" => false, "msg" => "Please enter a valid postcode"));
                exit;
            }
            if ($this->input->post("primary") == "1") {
                $this->db->where("referral_id", intval($this->input->post('referral_id')));
                $this->db->update("referral_address", array(
                    "primary" => NULL
                ));
            }

            $urn = $this->db->get_where("referral", array("referral_id" => $data['referral_id']))->row()->urn;

            //delete the location id incase the postcode has changed
            $this->db->where('address_id', intval($this->input->post('address_id')));
            $this->db->update('referral_address', array(
                "location_id" => NULL
            ));

            $this->db->where('address_id', intval($this->input->post('address_id')));
            if ($this->db->update('referral_address', elements(array(
                "description",
                "add1",
                "add2",
                "add3",
                "add4",
                "locality",
                "city",
                "county",
                "country",
                "postcode",
                "referral_id",
                "primary",
                "visible"
            ), $data))
            ):

                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('referral_id')),
                    "type" => "address"
                ));
            endif;
        }
        $this->load->model('Locations_model');
        //set the location id on the appointment
        $this->Locations_model->set_location_id($this->input->post('postcode'));
    }


    //this function edits company address
    public function edit_coaddress()
    {

        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $this->load->helper('location');
            $data['postcode'] = postcodeCheckFormat($data['postcode']);
            if (!$data['postcode']) {
                echo json_encode(array("success" => false, "msg" => "Please enter a valid postcode"));
                exit;
            }
            if ($this->input->post("primary") == "1") {
                $this->db->where("company_id", $data['company_id']);
                $this->db->update("company_addresses", array(
                    "primary" => NULL
                ));
            }

            $urn = $this->db->get_where("companies", array("company_id" => $data['company_id']))->row()->urn;
            $this->Audit_model->log_coaddress_update($data, $urn);

            //delete the location id incase the postcode has changed
            $this->db->where('address_id', $data['address_id']);
            $this->db->update('company_addresses', array(
                "location_id" => NULL
            ));

            $this->db->where('address_id', $data['address_id']);
            if ($this->db->update('company_addresses', elements(array(
                "description",
                "add1",
                "add2",
                "add3",
                "add4",
                "locality",
                "city",
                "county",
                "country",
                "postcode",
                "company_id",
                "primary",
                "visible"
            ), $data))
            ):

                echo json_encode(array(
                    "success" => true,
                    "id" => intval($data['company_id']),
                    "type" => "address"
                ));
            endif;
        }
        $this->load->model('Locations_model');
        //set the location id on the appointment
        $this->Locations_model->set_location_id($this->input->post('postcode'));;
    }

    //this function updates contact address
    public function add_address()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->helper('location');
            $data = $this->input->post();
            $data['postcode'] = postcodeCheckFormat($data['postcode']);
            if (!$data['postcode']) {
                echo json_encode(array("success" => false, "msg" => "Please enter a valid postcode"));
                exit;
            }
            if ($this->input->post("primary") == "1" || ($this->input->post("primary") == '')) {
                $data['primary'] = "1";
                $this->db->where("contact_id", $data['contact_id']);
                $this->db->update("contact_addresses", array(
                    "primary" => NULL
                ));
            }

            if ($this->db->insert('contact_addresses', elements(array(
                "description",
                "add1",
                "add2",
                "add3",
                "add4",
                "locality",
                "city",
                "county",
                "country",
                "postcode",
                "contact_id",
                "primary",
                "visible"
            ), $data))
            ):

                $data['address_id'] = $this->db->insert_id();
                $urn = $this->db->get_where("contacts", array("contact_id" => $data['contact_id']))->row()->urn;
                $this->Audit_model->log_address_insert($data, $urn);
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($data['contact_id']),
                    "type" => "address"
                ));
            endif;
        }
        $this->load->model('Locations_model');
        //set the location id on the appointment
        $this->Locations_model->set_location_id($this->input->post('postcode'));
    }

    //this function updates referral address
    public function add_referral_address()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->helper('location');
            $data = $this->input->post();
            $data['postcode'] = postcodeCheckFormat($data['postcode']);
            if (!$data['postcode']) {
                echo json_encode(array("success" => false, "msg" => "Please enter a valid postcode"));
                exit;
            }
            if ($this->input->post("primary") == "1" || ($this->input->post("primary") == '')) {
                $data['primary'] = "1";
                $this->db->where("referral_id", $data['referral_id']);
                $this->db->update("referral_address", array(
                    "primary" => NULL
                ));
            }

            if ($this->db->insert('referral_address', elements(array(
                "description",
                "add1",
                "add2",
                "add3",
                "add4",
                "locality",
                "city",
                "county",
                "country",
                "postcode",
                "referral_id",
                "primary",
                "visible"
            ), $data))
            ):

                $data['address_id'] = $this->db->insert_id();
                $urn = $this->db->get_where("referral", array("referral_id" => $data['referral_id']))->row()->urn;
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($data['referral_id']),
                    "type" => "address"
                ));
            endif;
        }
        $this->load->model('Locations_model');
        //set the location id on the appointment
        $this->Locations_model->set_location_id($this->input->post('postcode'));
    }

    //this function updates company address
    public function add_coaddress()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->helper('location');
            $data = $this->input->post();
            $data['postcode'] = postcodeCheckFormat($data['postcode']);
            if (!$data['postcode']) {
                echo json_encode(array("success" => false, "msg" => "Please enter a valid postcode"));
                exit;
            }
            if ($this->input->post("primary") == "1") {
                $this->db->where("company_id", $data['company_id']);
                $this->db->update("company_addresses", array(
                    "primary" => NULL
                ));
                $this->firephp->log("done");
            }

            if ($this->db->insert('company_addresses', elements(array(
                "description",
                "add1",
                "add2",
                "add3",
                "add4",
                "locality",
                "city",
                "county",
                "country",
                "postcode",
                "company_id",
                "primary",
                "visible"
            ), $data))
            ):
                $data['address_id'] = $this->db->insert_id();
                $urn = $this->db->get_where("companies", array("company_id" => $data['company_id']))->row()->urn;
                $this->Audit_model->log_coaddress_insert($data, $urn);
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($data['company_id']),
                    "type" => "address"
                ));
            endif;
        }
        $this->load->model('Locations_model');
        //set the location id on the appointment
        $this->Locations_model->set_location_id($this->input->post('postcode'));
    }

    //this function deletes contact address
    public function delete_address()
    {
        if ($this->input->is_ajax_request()) {
            $id = intval($this->input->post('id'));
            $urn = $this->db->query("select urn from contacts left join contact_addresses using(contact_id) where address_id = '$id'")->row()->urn;
            $this->Audit_model->log_address_delete($id, $urn);

            $this->db->where(array('address_id' => $id));
            if ($this->db->delete('contact_addresses')):
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('contact')),
                    "type" => "address"
                ));
            endif;
        }
    }

    //this function deletes referral address
    public function delete_referral_address()
    {
        if ($this->input->is_ajax_request()) {
            $id = intval($this->input->post('id'));
            $urn = $this->db->query("select urn from referral left join referral_address using(referral_id) where address_id = '$id'")->row()->urn;

            $this->db->where(array('address_id' => $id));
            if ($this->db->delete('referral_address')):
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('referral')), 
                    "type" => "address"
                ));
            endif;
        }
    }

    //this function deletes company address
    public function delete_coaddress()
    {
        if ($this->input->is_ajax_request()) {
            $id = intval($this->input->post('id'));
            $urn = $this->db->query("select urn from companies left join company_addresses using(company_id) where address_id = '$id'")->row()->urn;
            $this->Audit_model->log_phone_delete($id, $urn);

            $this->db->where(array('address_id' => $id));
            if ($this->db->delete('company_addresses')):
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('company')),
                    "type" => "address"
                ));
            endif;
        }
    }

    //get all sruvey data for a given urn
    public function get_surveys()
    {
        if ($this->input->is_ajax_request()) {
            $urn = intval($this->input->post('urn'));
            $surveys = $this->Survey_model->get_record_surveys(intval($this->input->post("urn")));
            foreach ($surveys as $key => $row) {
                //administrators can edit any survey. USers can only edit their own surveys
                if ($row['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] == "1") {
                    $surveys[$key]["locked"] = false;
                } else {
                    $surveys[$key]["locked"] = true;
                }
            }
            if (count($surveys)) {
                echo json_encode(array(
                    "success" => true,
                    "data" => $surveys
                ));
            } else {
                echo json_encode(array(
                    "success" => true
                ));
            }
        }

    }


    //save ownership changes to a urn
    public function save_ownership()
    {
        if ($this->input->is_ajax_request()) {
            $this->Records_model->save_ownership(intval($this->input->post("urn")), $this->input->post("owners"));
            echo json_encode(array(
                "success" => true
            ));
        }
    }

    //return owner id's for a given urn
    public function get_ownership()
    {
        if ($this->input->is_ajax_request()) {
            $result = $this->Records_model->get_ownership(intval($this->input->post("urn")));
            $owners = array();
            foreach ($result as $row) {
                $owners[] = $row['user_id'];
            }
            echo json_encode(array(
                "success" => true,
                "data" => $owners
            ));
        }
    }

    //Add ownership to a record if it doesn't exists
    public function add_ownership() {
        if ($this->input->is_ajax_request()) {
            $this->Records_model->add_ownership(intval($this->input->post("urn")), $this->input->post("user_id"));
            echo json_encode(array(
                "success" => true
            ));
        }
    }

    //get user details for a given urn
    public function get_users()
    {
        if ($this->input->is_ajax_request()) {
            $users = $this->Records_model->get_users(intval($this->input->post("urn")));

            $aux = array();
            foreach ($users as $value) {
                unset($value['password']);
                array_push($aux, $value);
            }
            $users = $aux;

            echo json_encode(array(
                "success" => true,
                "data" => $users
            ));
        }
    }


    //fetch all history entries for a given urn
    public function get_history()
    {
        if ($this->input->is_ajax_request()) {
            $record_urn = intval($this->input->post('urn'));
            $limit = (intval($this->input->post('limit'))) ? intval($this->input->post('limit')) : NULL;

            $history = $this->Records_model->get_history($record_urn, $limit, 0);
            $keep = false;
            foreach ($history as $row) {
                //if any outcome in the history was a keeper and set by the current user we flag ensure that they remain the owner by setting this keep flag. This tells the JS to append a new hidden form element which gets passed to the php if the user submits the form
                if ($row['keep_record'] == 1 && $row['user_id'] == $_SESSION['user_id']) {
                    $keep = true;
                }
            }
            echo json_encode(array(
                "success" => true,
                "keep" => $keep,
                "data" => $history
            ));
        }
    }

    //fetch the history entry for a given history_id
    public function get_history_by_id()
    {
        if ($this->input->is_ajax_request()) {
            $history_id = intval($this->input->post('id'));

            $history = $this->Records_model->get_history_by_id($history_id);
            echo json_encode(array(
                "success" => true,
                "data" => $history,
                "outcomes" => $this->Form_model->get_outcomes(),
                "progress_list" => $this->Form_model->get_progress_descriptions()
            ));
        }
    }

    public function get_outcome_reasons()
    {
        if ($this->input->is_ajax_request()) {
            $outcome_id = intval($this->input->post('outcome_id'));
            $urn = intval($this->input->post('urn'));
            $campaign = $this->Records_model->get_campaign_from_urn($urn);
            $outcome_reasons = $this->Form_model->get_outcome_reasons($campaign, $outcome_id);
            echo json_encode(array(
                "success" => true,
                "data" => $outcome_reasons,
            ));
        }

    }

    public function update_history()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            if (isset($form['call_direction']) && $form['call_direction'] == '') {
                $form['call_direction'] = NULL;
            }
            if (isset($form['contact'])) {
                $form['contact'] = to_mysql_datetime($form['contact']);
            }
            if ($this->Records_model->save_history($form)) {
                echo json_encode(array(
                    "success" => true,
                    "msg" => "The history has been updated"
                ));
            } else {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "The history could not be updated"
                ));
            }
        }
    }

    public function delete_history()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->Records_model->remove_history($this->input->post('history_id'))) {
                echo json_encode(array(
                    "success" => true,
                    "msg" => "The history has been deleted"
                ));
            } else {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "The history could not be deleted"
                ));
            }
        }
    }

    public function get_record_details()
    {
        if ($this->input->is_ajax_request()) {
            $urn = intval($this->input->post('urn'));
            $record_details = $this->Records_model->get_record_details($urn);
            echo json_encode(array(
                "success" => true,
                "record_details" => $record_details
            ));
        }
    }

    public function get_additional_info()
    {
        if ($this->input->is_ajax_request()) {
			session_write_close();
            $urn = intval($this->input->post('urn'));
            $campaign = $this->Records_model->get_campaign($urn);
            $additional_info = $this->Records_model->get_additional_info($urn, $campaign['campaign_id']);
            echo json_encode(array(
                "success" => true,
                "data" => $additional_info,
                "urn" => $urn
            ));
        }
    }

    public function save_additional_info()
    {
        if ($this->input->is_ajax_request()) {
            $urn = $this->input->post('urn');

            $info = $this->input->post();
            $custom_fields = array("c1", "c2", "c3", "c4", "c5", "c6", "c7", "c8", "c9", "10");
            $date_fields = array("d1", "d2", "d3", "d4", "d5", "d6", "d7", "d8", "d9", "d10", "dt1", "dt2", "dt3", "dt4", "dt5", "dt6", "dt7", "dt8", "dt9", "dt10");
            foreach ($info as $k => $v) {
                if (in_array($k, $date_fields)) {
                    if ($v == "") {
                        $info[$k] = NULL;
                    }
                }
                //check if its a special field ie-ownership,client_ref or color
                if (in_array($k, $custom_fields)) {
                    $qry = "select field_name,is_color,is_owner,is_client_ref,is_pot,is_source from record_details_fields join campaigns using(campaign_id) join records using(campaign_id) where urn = '$urn' and field = '$k'";
                    $special_fields = $this->db->query($qry)->result_array();
                    foreach ($special_fields as $row) {
                        if ($row['is_color'] == 1) {
                            $this->Records_model->save_record_color($urn, $v);
                        }
                        if ($row['is_owner'] == 1) {
                            $this->Records_model->save_ownership($urn, array($v));
                            if (!empty($v)) {
                                $info[$k] = $this->Records_model->get_name_from_user_id($v);
                            }
                        }
						 if ($row['is_pot'] == 1) {
                            $this->Records_model->save_pot($urn, $v);
                            if (!empty($v)) {
                                $info[$k] = $this->Records_model->get_pot_from_id($v);
                            }
                        }
						 if ($row['is_source'] == 1) {
                            $this->Records_model->save_source($urn, $v);
                            if (!empty($v)) {
                                $info[$k] = $this->Records_model->get_source_from_id($v);
                            }
                        }
                        if ($row['is_client_ref'] == 1) {
                            $this->Records_model->insert_client_ref($urn, $v);
                        }

                    }
                }

                if ($v == "-" || empty($v)) {
                    $info[$k] = NULL;
                }
            }
            if (!empty($info['detail_id'])) {
                $this->Audit_model->log_custom_fields_update($info, $info['urn']);
                $id = $this->Records_model->save_additional_info($info);
            } else {
                $id = $this->Records_model->save_additional_info($info);
                $info['detail_id'] = $id;
                $this->Audit_model->log_custom_fields_insert($info, $info['urn']);
            }

            $record_details = $this->Records_model->get_record_details_by_id($info['detail_id']);

            if ($id) {
                echo json_encode(array(
                    "success" => true,
                    "data" => $record_details,
                    "msg" => "The information has been updated"
                ));
            } else {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "The information could not be updated"
                ));
            }
        }

    }

    public function remove_custom_item()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->Records_model->remove_custom_item($this->input->post('id'))) {
                echo json_encode(array(
                    "success" => true,
                    "msg" => "The information has been updated"
                ));
            } else {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "The information could not be updated"
                ));
            }
        }

    }

    public function get_details_from_id()
    {
        if ($this->input->is_ajax_request()) {
            $result = $this->Records_model->get_additional_info($this->input->post('urn'), $this->input->post('campaign'), $this->input->post('id'));
            if ($result) {
                echo json_encode(array(
                    "success" => true,
                    "data" => $result
                ));
            }
        }

    }

    public function get_appointment()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $appts = $this->Records_model->get_appointments($this->input->post("urn"), $this->input->post("id"));
            foreach ($appts as $k => $row) {
                $appts[$k]['start'] = date('d/m/Y H:i', strtotime($row['start']));
                $appts[$k]['end'] = date('d/m/Y H:i', strtotime($row['end']));
            }
            //return success to page
            echo json_encode(array(
                "success" => true,
                "data" => $appts
            ));
        } else {
            echo "Denied";
            exit;
        }
    }

    public function get_addresses_by_postcode()
    {
        if ($this->input->is_ajax_request()) {

            $postcode = str_replace(" ", "", $this->input->post("postcode"));
            $house_number = $this->input->post("house_number");

            $curl = curl_init();

            //Get the address
            curl_setopt($curl, CURLOPT_URL, 'http://www.121leads.co.uk/121it/web/api/addresses/' . $postcode . '/open.json');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $addresses = curl_exec($curl);
            $curl_info = curl_getinfo($curl);
            curl_close($curl);

            $addresses = json_decode($addresses, true);

            if ($curl_info['http_code'] != 200) {
                echo json_encode(array(
                    "success" => false,
                    "data" => array(),
                    "postcode" => postcodeFormat($postcode)
                ));
            } else {
                $address_selected = null;
                //Check if the house number match any of the results
                if ($house_number) {
                    foreach ($addresses as $key => $address) {
                        $ar = $address;
                        unset($ar['postcode_io']);
                        unset($ar['id']);
                        unset($ar['latitude']);
                        unset($ar['longitude']);
                        unset($ar['created_date']);
                        unset($ar['postcode']);
                        if (preg_grep("/{$house_number}/i", $ar)) {
                            $address_selected = $key;
                            break;
                        }
                    }
                }
                //return success to page
                echo json_encode(array(
                    "success" => (!empty($addresses)),
                    "data" => $addresses,
                    "postcode" => postcodeFormat($postcode),
                    "address_selected" => $address_selected
                ));
            }
        }
    }

    public function get_custom_panels()
    {
        $campaign = intval($this->input->post("campaign"));
        $result = $this->Records_model->get_custom_panels($campaign);
        echo json_encode(array("success" => true, "data" => $result));

    }

    public function load_custom_panel()
    {
		session_write_close();
        $id = intval($this->input->post("id"));
        $urn = intval($this->input->post("urn"));
        $panel_details = $this->Records_model->get_custom_panel($id);
        $panel_fields = $this->Records_model->get_custom_panel_fields($id);
        $fields = array();
        $options = array();
        foreach ($panel_fields as $k => $row) {
            $options[$row['name']][] = array("option_id" => $row['option_id'], "option_name" => $row['option_name'], "option_subtext" => $row['option_subtext']);
            $fields[$row['name']] = $row;
			$fields[$row['name']]['value'] = "-";
            $fields[$row['name']]['options'] = $options[$row['name']];
        }
        $panel_data = $this->Records_model->get_custom_panel_data($urn, $id);
		
        $data = array();
        foreach ($panel_data as $k => $row) {
			if(!isset($data[$row['data_id']])){
				$data[$row['data_id']] = $fields;	
			}
            if ($fields[$row['name']]['type'] == "date") {
                $row['value'] == date($fields[$row['name']]['format'], strtotime($row['value']));
            }
            if ($fields[$row['name']]['type'] == "decimal") {
                $row['value'] == number_format(intval($row['value']), 2);
            }
            if ($fields[$row['name']]['type'] == "number") {
                $row['value'] == number_format(intval($row['value']));
            }
            if ($fields[$row['name']]['type'] == "string") {
                $row['value'] == htmlentities($row['value']);
            }
            if ($fields[$row['name']]['type'] == "select") {
               $row['value'] == htmlentities($row['value']);
            }
            if ($fields[$row['name']]['type'] == "multiple") {
               $row['value'] == htmlentities($row['value']);
            }
            $data[$row['data_id']][$row['name']] = $row;
            $data[$row['data_id']][$row['name']]['name'] = $fields[$row['name']]['name'];
			
			$email_query = $this->db->query("select email from contacts join appointments using(contact_id) join custom_panel_values on appointment_id = `value` join custom_panel_fields where is_appointment_id = 1 and data_id = '{$row['data_id']}'");
		if($email_query->num_rows()){
				$data[$row['data_id']]['email'] = $email_query->row()->email;
        }
		}
        echo json_encode(array("success" => true, "panel" => $panel_details, "fields" => $fields, "data" => $data));

    }

//same as "load_custom_panel()" but we split the fields into 2 columns
    public function load_custom_form()
    {
        $id = intval($this->input->post("id"));
        $urn = intval($this->input->post("urn"));
        $panel_details = $this->Records_model->get_custom_panel($id);
        $panel_fields = $this->Records_model->get_custom_panel_fields($id);
        $fields = array();
        $options = array();
        foreach ($panel_fields as $k => $row) {
            $options[$row['name']][] = array("option_id" => $row['option_id'], "option_name" => $row['option_name'], "option_subtext" => $row['option_subtext']);
            $fields[$row['modal_column']][$row['name']] = $row;
            $fields[$row['modal_column']][$row['name']]['options'] = $options[$row['name']];
        }
        $panel_data = $this->Records_model->get_custom_panel_data($urn, $id);
        $data = array();
        foreach ($panel_data as $k => $row) {
            if ($fields[$row['modal_column']][$row['name']]['type'] == "date" || $fields[$row['modal_column']][$row['name']]['type'] == "datetime") {
                $row['value'] == date($fields[$row['modal_column']][$row['name']]['format'], strtotime($row['value']));
            }
            if ($fields[$row['modal_column']][$row['name']]['type'] == "decimal") {
                $row['value'] == $row['value'];
            }
            if ($fields[$row['modal_column']][$row['name']]['type'] == "number") {
                $row['value'] == $row['value'];
            }
            if ($fields[$row['modal_column']][$row['name']]['type'] == "string") {
                $row['value'] == htmlentities($row['value']);
            }
            $data[$row['data_id']][$row['modal_column']][$row['field_id']] = $row;
            $data[$row['data_id']][$row['modal_column']][$row['field_id']]['name'] = $fields[$row['modal_column']][$row['name']]['name'];
        }

        echo json_encode(array("success" => true, "panel" => $panel_details, "fields" => $fields, "data" => $data));

    }

    public function save_custom_panel()
    {
        $now = date('Y-m-d H:i:s');
        $id = $this->input->post('data_id');
        $urn = $this->input->post('urn');
        if (empty($id)) {
            //create new data set
            $data = array("urn" => $urn, "created_on" => $now, "created_by" => $_SESSION['user_id'], "updated_on" => $now);
            $this->db->insert("custom_panel_data", $data);
            $id = $this->db->insert_id();
        }
        if (!empty($id)) {
            //update existing data set
            $data = array("updated_on" => $now);
            $this->db->where(array("data_id" => $id));
            $this->db->update("custom_panel_data", $data);
        }

        //add in the values
        foreach ($this->input->post() as $field => $val) {
            if ($field <> "urn" && $field <> "data_id") {
                if (is_array($val)) {
                    $val = implode(",", $val);
                }
                $values[] = array("data_id" => $id, "field_id" => $field, "value" => $val);
            }
        }
		$data = json_encode($this->input->post());
        $this->db->where(array("data_id" => $id));
        $this->db->delete("custom_panel_values");
        $this->db->insert_update_batch("custom_panel_values", $values);
        echo json_encode(array("success" => true, "data_id" => $id, "data"=>$data));

    }

    //get user details for a given user_role
    public function get_users_by_role()
    {
        if ($this->input->is_ajax_request()) {
            $users = $this->User_model->get_users_by_role(intval($this->input->post("role_id")));
            echo json_encode(array(
                "success" => true,
                "data" => $users
            ));
        }
    }

}
