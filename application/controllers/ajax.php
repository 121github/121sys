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

	public function fix_eldon(){
	$qry ="select urn,name from record_details left join users on users.name like concat(c3,'%')";
	$res = $this->db->query($qry)->result_array();
		foreach($res as $k=>$row){
		echo "update record_details set c3 = '{$row['name']}' where urn = {$row['urn']};";
		echo "<br>";	
		}
	}
	
	public function validate_postcode(){
		   if ($this->input->is_ajax_request()) {
            $postcode = $this->input->post('postcode');
            if(validate_postcode($postcode)){
            echo json_encode(array(
                "success" => true,
                "postcode" =>  postcodeFormat($postcode)
            ));
			} else {
			echo json_encode(array(
                "success" => false,
                "msg" =>  "Postcode is not valid"
            ));	
			}
        }
	}
	
    public function get_table_columns()
    {
        $array                  = array();
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
            $id      = intval($this->input->post('id'));
            $contact = $this->Contacts_model->get_contact($id);
            echo json_encode(array(
                "success" => true,
                "data" => $contact
            ));
        }
    }
    //this function returns a json array of company data for a given company id
    public function get_company()
    {
        if ($this->input->is_ajax_request()) {
            $id      = intval($this->input->post('id'));
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
            $urn      = intval($this->input->post('urn'));
            $contacts = $this->Contacts_model->get_contacts($urn);
            echo json_encode(array(
                "success" => true,
                "data" => $contacts
            ));
        }
    }
    
    //this function returns a json array of all contacts for a given contact urn
    public function get_companies()
    {
        if ($this->input->is_ajax_request()) {
            $urn       = intval($this->input->post('urn'));
            $companies = $this->Company_model->get_companies($urn);
            echo json_encode(array(
                "success" => true,
                "data" => $companies
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
            $array                 = $this->input->post();
            if (@!empty($array["dob"])) {
                $array["dob"] = to_mysql_datetime($array["dob"]);
            }
			if(@!empty($array['linkedin'])){
				$this->load->helper('misc');
				$array['linkedin'] = linkedin_id_from_url($array['linkedin']);
			}
			$audit_id = $this->Audit_model->log_contact_update(array_filter($array),$array['urn']);
			  $array["date_updated"] = date('Y-m-d H:i:s');
			   $this->db->where("contact_id", intval($this->input->post('contact_id')));
            if ($this->db->update('contacts', array_filter($array))):
                echo json_encode(array(
                    "success" => true,
                    "id" => intval($this->input->post('contact_id'))
                ));
            endif;
        }
    }
    
    //this function saves company data to the database. The post names should match the database field names. 
    public function save_company()
    {
        if ($this->input->is_ajax_request()) {
            $array                 = $this->input->post();
			$audit_id = $this->Audit_model->log_company_update(array_filter($array),$array['urn']);
			$array["date_updated"] = date('Y-m-d H:i:s');
            $array['turnover'] = ($array['turnover']==''?NULL:$array['turnover']);
            if (@!empty($array["date_of_creation"])) {
                $array["date_of_creation"] = to_mysql_datetime($array["date_of_creation"]);
            }
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
            if (!empty($array["dob"])) {
                $array["dob"] = to_mysql_datetime($array["dob"]);
            }
            if ($this->db->insert('contacts', array_filter($array))):
			$id = $this->db->insert_id();
			$array['contact_id']=$id;
			$this->Audit_model->log_contact_insert(array_filter($array),$array['urn']);
                echo json_encode(array(
                    "success" => true,
                    "id" => $id
                ));
            endif;
        }
    }
    
    //this function saves company data to the database. The post names should match the database field names. 
    public function add_company()
    {
        if ($this->input->is_ajax_request()) {
            $array = $this->input->post();
            if ($this->db->insert('companies', array_filter($array))):
			$id = $this->db->insert_id();
			$array['company_id']=$id;
			$this->Audit_model->log_contact_insert(array_filter($array),$array['urn']);
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
            $result         = $this->db->get('contact_telephone')->row_array();
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
            $result         = $this->db->get('company_telephone')->row_array();
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
            $result         = $this->db->get('contact_addresses')->row_array();
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
            $result         = $this->db->get('company_addresses')->row_array();
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
			$msg=false;
			$d = preg_replace('/[0-9]/','',$data['description']);
			if(empty($d)){
			$msg = "The telephone description should be text. Eg Home, Office, Mobile etc";
			}
			if(empty($data['description'])){
			$msg = "Description cannot be empty";
			}
			$data['telephone_number'] = numbers_only($data['telephone_number']);
			if(empty($data['telephone_number'])){
			$msg = "Phone number is invalid";
			}
			if($msg){
			echo json_encode(array("success"=>false,"msg"=>$msg));
			exit;
			}
				$urn = $this->db->get_where("contacts",array("contact_id"=>$data['contact_id']))->row()->urn;
				$this->Audit_model->log_phone_update($data,$urn);
				
            $this->db->where('telephone_id', $data['telephone_id']);
            if ($this->db->update('contact_telephone', elements(array(
                "contact_id",
                "telephone_number",
                "description",
                "tps"
            ), array_filter($data, 'strlen'), null))):
							
                echo json_encode(array(
					"success"=>true,
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
						$msg=false;
$d = preg_replace('/[0-9]/','',$data['description']);
			if(empty($d)){
			$msg = "The telephone description should be text. Eg Home, Office, Mobile etc";
			}
			if(empty($data['description'])){
			$msg = "Description cannot be empty";
			}
			$data['telephone_number'] = numbers_only($data['telephone_number']);
			if(empty($data['telephone_number'])){
			$msg = "Phone number is invalid";
			}
			if($msg){
			echo json_encode(array("success"=>false,"msg"=>$msg));
			exit;
			}
				$urn = $this->db->get_where("companies",array("company_id"=>$data['company_id']))->row()->urn;
				$this->Audit_model->log_cophone_update($data,$urn);
				
            $this->db->where('telephone_id', $data['telephone_id']);
            if ($this->db->update('company_telephone', elements(array(
                "company_id",
                "telephone_number",
                "description",
                "ctps"
            ), array_filter($data, 'strlen'), null))):
			

				
                echo json_encode(array(
				"success"=>true,
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
				$msg=false;
			$d = preg_replace('/[0-9]/','',$data['description']);
			if(empty($d)){
			$msg = "The telephone description should be text. Eg Home, Office, Mobile etc";
			}
			if(empty($data['description'])){
			$msg = "Description cannot be empty";
			}
			$data['telephone_number'] = numbers_only($data['telephone_number']);
			if(empty($data['telephone_number'])){
			$msg = "Phone number is invalid";
			}
			if($msg){
			echo json_encode(array("success"=>false,"msg"=>$msg));
			exit;
			}
			
            $data['tps'] = ($data['tps'] == NULL?NULL:$data['tps']);
            if ($this->db->insert('contact_telephone', $data)):
				$data['telephone_id'] = $this->db->insert_id();
				$urn = $this->db->get_where("contacts",array("contact_id"=>$data['contact_id']))->row()->urn;
				$this->Audit_model->log_phone_insert($data,$urn);
                echo json_encode(array(
				"success"=>true,
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
				$msg=false;
			$d = preg_replace('/[0-9]/','',$data['description']);
			if(empty($d)){
			$msg = "The telephone description should be text. Eg Home, Office, Mobile etc";
			}
			if(empty($data['description'])){
			$msg = "Description cannot be empty";
			}
			$data['telephone_number'] = numbers_only($data['telephone_number']);
			if(empty($data['telephone_number'])){
			$msg = "Phone number is invalid";
			}
			if($msg){
			echo json_encode(array("success"=>false,"msg"=>$msg));
			exit;
			}
            $data['ctps'] = ($data['ctps'] == NULL?NULL:$data['ctps']);
            if ($this->db->insert('company_telephone', $data)):
			$data['telephone_id'] = $this->db->insert_id();
			$urn = $this->db->get_where("companies",array("company_id"=>$data['company_id']))->row()->urn;
			$this->Audit_model->log_cophone_insert($data,$urn);
                echo json_encode(array(
				"success"=>true,
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
			$this->Audit_model->log_phone_delete($id,$urn);
			
            $this->db->where(array('telephone_id'=> $id));
            if ($this->db->delete('contact_telephone')):
                echo json_encode(array(
				"success"=>true,
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
			$this->Audit_model->log_cophone_delete($id,$urn);
			
            $this->db->where(array('telephone_id'=>$id));
            if ($this->db->delete('company_telephone')):
                echo json_encode(array(
				"success"=>true,
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
			if(!$data['postcode']){
			echo json_encode(array("success"=>false,"msg"=>"Please enter a valid postcode"));
			exit;
			}
            if ($this->input->post("primary") == "1") {
                $this->db->where("contact_id", intval($this->input->post('contact_id')));
                $this->db->update("contact_addresses", array(
                    "primary" => NULL
                ));
            }
			
				$urn = $this->db->get_where("contacts",array("contact_id"=>$data['contact_id']))->row()->urn;
				$this->Audit_model->log_address_update($data,$urn);
			
            //delete the location id incase the postcode has changed
            $this->db->where('address_id', intval($this->input->post('address_id')));
            $this->db->update('contact_addresses', array(
                "location_id" => NULL
            )); 
				
            $this->db->where('address_id', intval($this->input->post('address_id')));
            if ($this->db->update('contact_addresses', elements(array(
                "add1",
                "add2",
                "add3",
                "county",
                "country",
                "postcode",
                "contact_id",
                "primary"
            ), $data))):

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
    
    
    //this function edits company address
    public function edit_coaddress()
    {

        if ($this->input->is_ajax_request()) {
           $data = $this->input->post();
		   $this->load->helper('location');
		   $data['postcode'] = postcodeCheckFormat($data['postcode']);
			if(!$data['postcode']){
			echo json_encode(array("success"=>false,"msg"=>"Please enter a valid postcode"));
			exit;
			}
            if ($this->input->post("primary") == "1") {
                $this->db->where("company_id", $data['company_id']);
                $this->db->update("company_addresses", array(
                    "primary" => NULL
                ));
            }
			
			$urn = $this->db->get_where("companies",array("company_id"=>$data['company_id']))->row()->urn;
			$this->Audit_model->log_coaddress_update($data,$urn);
			
            //delete the location id incase the postcode has changed
            $this->db->where('address_id', $data['address_id']);
            $this->db->update('company_addresses', array(
                "location_id" => NULL
            ));
            
            $this->db->where('address_id', $data['address_id']);
            if ($this->db->update('company_addresses', elements(array(
                "add1",
                "add2",
                "add3",
                "county",
                "country",
                "postcode",
                "company_id",
                "primary"
            ), $data))):
				
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
			if(!$data['postcode']){
			echo json_encode(array("success"=>false,"msg"=>"Please enter a valid postcode"));
			exit;
			}
            if ($this->input->post("primary") == "1"||empty($this->input->post("primary"))) {
				$data['primary'] = "1";
                $this->db->where("contact_id", $data['contact_id']);
                $this->db->update("contact_addresses", array(
                    "primary" => NULL
                ));
            }
            
            if ($this->db->insert('contact_addresses', elements(array(
                "add1",
                "add2",
                "add3",
                "county",
                "country",
                "postcode",
                "contact_id",
                "primary"
            ), $data))):
		
			$data['address_id'] = $this->db->insert_id();
			$urn = $this->db->get_where("contacts",array("contact_id"=>$data['contact_id']))->row()->urn;
			$this->Audit_model->log_address_insert($data,$urn);
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
    
    //this function updates company address
    public function add_coaddress()
    {
        if ($this->input->is_ajax_request()) {
           $this->load->helper('location');
		   $data = $this->input->post();
		   $data['postcode'] = postcodeCheckFormat($data['postcode']);
			if(!$data['postcode']){
			echo json_encode(array("success"=>false,"msg"=>"Please enter a valid postcode"));
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
                "add1",
                "add2",
                "add3",
                "county",
                "country",
                "postcode",
                "company_id",
                "primary"
            ), $data))):
			$data['address_id'] = $this->db->insert_id();
			$urn = $this->db->get_where("companies",array("company_id"=>$data['company_id']))->row()->urn;
			$this->Audit_model->log_coaddress_insert($data,$urn);
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
			$this->Audit_model->log_address_delete($id,$urn);
			
            $this->db->where(array('address_id'=>$id));
            if ($this->db->delete('contact_addresses')):
                echo json_encode(array(
				"success"=>true,
                    "id" => intval($this->input->post('contact')),
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
			$this->Audit_model->log_phone_delete($id,$urn);
			
            $this->db->where(array('address_id'=>$id));
            if ($this->db->delete('company_addresses')):
                echo json_encode(array(
				"success"=>true,
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
            $urn     = intval($this->input->post('urn'));
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
    
    //get user details for a given urn
    public function get_users()
    {
        if ($this->input->is_ajax_request()) {
            $users = $this->Records_model->get_users(intval($this->input->post("urn")));
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
            $limit      = (intval($this->input->post('limit'))) ? intval($this->input->post('limit')) : NULL;
            
            $history = $this->Records_model->get_history($record_urn, $limit, 0);
            $keep    = false;
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
    
    public function update_history()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
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
    
    public function get_additional_info()
    {
        if ($this->input->is_ajax_request()) {
            $urn             = intval($this->input->post('urn'));
            $campaign        = $this->Records_model->get_campaign($urn);
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

			/*
			$where = array("urn"=>$this->input->post('urn'),"field_name"=>"Color"); 
			$this->db->where($where);
			$this->db->join("record_details_fields","records.campaign_id=record_details_fields.campaign_id");
			$result = $this->db->get_where("records",$where)->row_array();
			if(count($result)>0){
				$this->Records_model->save_record_color($this->input->post('urn'),$this->input->post($result['field']));	
			}
			*/
			$info = $this->input->post();
			$custom_fields = array("c1","c2","c3","c4","c5","c6");
			foreach($info as $k => $v){
				//check if its a special field ie-ownership,client_ref or color
				if(in_array($k,$custom_fields)){
				$qry = "select is_color,is_owner,is_client_ref from record_details_fields join campaigns 		using(campaign_id) join records using(campaign_id) where urn = '$urn' and field = '$k'";
				$special_fields = $this->db->query($qry)->result_array();
				foreach($special_fields as $row){
					if($row['is_color']==1){
						$this->Records_model->save_record_color($urn,$k);
					}
					if($row['is_owner']==1){
						 $this->Records_model->save_ownership($urn, array($v));
						 if(!empty($v)){
						 $info[$k] = $this->Records_model->get_name_from_user_id($v);
						 }
					}
					if($row['is_client_ref']==1){
						$this->Records_model->insert_client_ref($urn,$k);
					}
					
				}
				}
				
			if($v=="-"){
			$info[$k] = NULL;	
			}
			}
            if ($this->Records_model->save_additional_info($info)) {
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
                $appts[$k]['end']   = date('d/m/Y H:i', strtotime($row['end']));
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
    
}
