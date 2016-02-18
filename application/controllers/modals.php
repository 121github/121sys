<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Modals extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check();
		$this->custom_fields = custom_fields();
        $this->_campaigns = campaign_access_dropdown();

        $this->load->model('User_model');
        $this->load->model('Modal_model');
        $this->load->model('Records_model');
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
    }
    public function user_account_details()
    {
        if ($this->input->is_ajax_request()) {
			if($this->input->post('id')&&in_array("edit users",$_SESSION['permissions'])){
			$id = intval($this->input->post('id'));
			} else {
			$id = $_SESSION['user_id'];	
			}
			  $this->load->view('forms/edit_user_details.php',array("id"=>$id));
        }
		 
    }

    public function new_email_form()
    {
        if ($this->input->is_ajax_request()) {
            $campaign_id = $this->Records_model->get_campaign_from_urn($this->input->post('urn'));
            $templates = $this->Form_model->get_templates_by_campaign_id($campaign_id);
            $email_options = array("templates" => $templates);
            $this->load->view('forms/new_email_form.php', $email_options);
        }
    }

    public function view_email()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->view('email/view_email.php');
        }
    }

	    public function record_options()
    {
        if ($this->input->is_ajax_request()) {
			$current_options = $this->Modal_model->get_record_options($this->input->post('urn'));
			$sources = $this->Form_model->get_sources();
			$pots = $this->Form_model->get_pots();
			$campaigns = $this->Form_model->get_user_campaigns();
			$parked_codes = $this->Form_model->get_parked_codes();
			$options = array();
			$options["urn"] = $this->input->post('urn');
			$options["sources"] = $sources;
			$options["pots"] = $pots;
			$options["campaigns"] = $campaigns;
			$options["parked_codes"] = $parked_codes;
			$options["current"] = $current_options;
            $this->load->view('forms/record_options.php',$options);
        }
    }

    public function show_all_email()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->view('email/show_all_email.php');
        }
    }

    public function new_sms_form()
    {
        if ($this->input->is_ajax_request()) {
            $urn = $this->input->post('urn');
            $campaign_id = $this->Records_model->get_campaign_from_urn($urn);
            $templates = $this->Form_model->get_sms_templates_by_campaign_id($campaign_id);
            $email_options = array(
                "templates" => $templates
            );
            $this->load->view('forms/new_sms_form.php', $email_options);
        }
    }

    public function view_sms()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->view('sms/view_sms.php');
        }
    }

    public function show_all_sms()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->view('sms/show_all_sms.php');
        }
    }

    public function start_survey()
    {
        if ($this->input->is_ajax_request()) {
            $urn = $this->input->post('urn');
            $contacts = $this->Form_model->get_contacts($urn);
            foreach ($contacts as $row) {
                $survey_options["contacts"][$row['id']] = $row['name'];
            }
            $campaign_id = $this->Records_model->get_campaign_from_urn($urn);
            $available_surveys = $this->Form_model->get_surveys($campaign_id);
            $survey_options["surveys"] = $available_surveys;

            $this->load->view('forms/new_survey_form.php', $survey_options);
        }
    }

    public function merge_record()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->view('forms/merge_record.php');
        }
    }

    public function load_company_search()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->view('forms/search_company_form.php');
            $this->load->view('forms/get_company_form.php');
        }
    }

    public function load_contact_form()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->view('forms/edit_contact_form.php');
        }
    }

    public function load_company_form()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->view('forms/edit_company_form.php');
        }
    }

    public function load_export_form()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->view('forms/edit_export_form.php');
        }
    }

    public function view_record()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Records_model');
            $ownership = array();
            $urn = intval($this->input->post('urn'));
			$data = array("urn"=>$urn);
			$modal_type = 1;
			$options = $this->Modal_model->get_modal_fields($urn,$modal_type);		
            $record = $this->Modal_model->get_record($options,$urn);	
			$fields=array();
			$modal = array();	
			foreach($options['modal'] as $row){
				$fields[$row['column_title']][$row['datafield_title']] = $record[$row['datafield_title']];
				$modal[$row['column_title']] = array("display"=>$row['field_display'],"title"=>$row['column_title'],"list_icon"=>$row['list_icon'],"table_class"=>$row['table_class'],"fields"=>$fields[$row['column_title']]);
			}
			$data['record'] = $modal;
            $history = $this->Modal_model->view_history($urn);
            $data['history'] = $history;
            $appointments = $this->Modal_model->view_appointments($urn);
            foreach ($appointments as $k => $appointment) {
                if ($appointment['status'] == "1" && strtotime($appointment['sqlstart']) > time()) {
                    $appointments[$k]['status'] = "<span class='glyphicon glyphicon-time orange'></span>";
                } else if ($appointment['status'] == "1" && strtotime($appointment['sqlstart']) < time()) {
                    $appointments[$k]['status'] = "<span class='glyphicon glyphicon-ok green'></span>";
                } else if ($appointment['status'] == "0") {
                    $appointments[$k]['status'] = "<span class='glyphicon glyphicon-remove red'></span>";
                }
            }
            $data['appointments'] = $appointments;
            //add in the custom fields
            $additional_info = $this->Records_model->get_additional_info($urn, $record['campaign_id']);
            $data['custom_info'] = $additional_info;
            $all_addresses = $this->Records_model->get_addresses($urn);
            $addresses = array();
            foreach ($all_addresses as $k => $address):
                if (!empty($address['postcode'])) {
                    $add = ($address['type'] == "company" ? $address['name'] . ", " : "");
                    $add .= (!empty($address['add1']) ? $address['add1'] . ", " : "");
                    $add .= (!empty($address['postcode']) ? $address['postcode'] : "");
                    $addresses[] = array("postcode" => $address['postcode'], "address" => $add);
                }
            endforeach;
            $data['addresses'] = $addresses;

            echo json_encode(array("success" => true, "data" => $data));
        }
    }
	
	 public function view_appointment()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Records_model');
            $ownership = array();
            $id = intval($this->input->post('id'));
			$required = $this->get_appointment_meta($id);
			$data = $required;
			$modal_type = 2;
			$options = $this->Modal_model->get_modal_fields($id,$modal_type);		
            $record = $this->Modal_model->get_appointment($options,$id);	
			$fields=array();
			$modal = array();	
			foreach($options['modal'] as $row){
				$fields[$row['column_title']][$row['datafield_title']] = $record[$row['datafield_title']];
				$modal[$row['column_title']] = array("display"=>$row['field_display'],"title"=>$row['column_title'],"list_icon"=>$row['list_icon'],"table_class"=>$row['table_class'],"fields"=>$fields[$row['column_title']]);
			}
			$data['appointment'] = $modal;
            echo json_encode(array("success" => true, "data" => $data));
        }
    }
	

    public function get_appointment_meta($id)
    {
            $data = array();
            $postcode = false;
            if (isset($_COOKIE['current_postcode'])) {
                $postcode = postcodeFormat($_COOKIE['current_postcode']);
            }
            $result = $this->Modal_model->view_appointment_meta($id, $postcode);
			foreach ($result as $row) {
                    $attendee_names[] = $row['attendee'];
                    $attendees[] = $row['user_id'];
                    $data = $row;
                    $data['attendee_names'] = $attendee_names;
                    $data['attendees'] = $attendees;
                }
			$data['id'] = $id;
			$data['current_postcode'] = $postcode;
            return $data;
    }

    public function edit_appointment()
    {
        if ($this->input->is_ajax_request()) {
			if(isset($_SESSION['cover_letter_address'])){ unset($_SESSION['cover_letter_address']); }
            $urn = intval($this->input->post('urn'));
            $campaign_id = $this->Records_model->get_campaign_from_urn($urn);
            $addresses = $this->Records_model->get_addresses($urn);
            $attendees = $this->Records_model->get_attendees(false, $campaign_id);
            $types = $this->Records_model->get_appointment_types(false, $campaign_id);
            $branches = $this->Form_model->get_campaign_branches($campaign_id);
            $this->load->view('forms/edit_appointment_form.php', array("urn" => $urn, "attendees" => $attendees, "addresses" => $addresses, "types" => $types, "branches" => $branches));
        }
    }

    public function search_records()
    {
        if ($this->input->is_ajax_request()) {

            if ($_SESSION['role'] == 1) {
                $campaigns = $this->Form_model->get_campaigns();
            } else {
                $campaigns = $this->Form_model->get_campaigns_by_user($_SESSION['user_id']);
            }

            $this->load->view('forms/search_record_form.php', array(
                'campaigns' => $campaigns
            ));
        }
    }

    public function view_filter_options()
    {
        if ($this->input->is_ajax_request()) {
            $data = array();
            $filter_options = array();
            if (isset($_SESSION['filter'])) {
                $filter_options = $_SESSION['filter']['values'];
            }

            $result = $this->filter_display($filter_options);

            if ($result) {
                echo json_encode(array("success" => true, "data" => $result));
            } else {
                echo json_encode(array("success" => false, "msg" => "No filters have been applied!", "data" => $result));
            }
        }
    }

    private function filter_display($filter_options)
    {
        $mappings = array(
            "campaign_id" => array("name" => "Campaign name", "remove" => in_array("mix campaigns", $_SESSION['permissions']) ? true : false),
            "source_id" => array("name" => "Data source"),
            "client_id" => array("name" => "Client name"),
            "campaign_type_id" => array("name" => "Campaign type"),
            "urn" => array("name" => "URN"),
            "client_ref" => array("name" => "Client reference"),
            "outcome_id" => array("name" => "Outcome"),
            "progress_id" => array("name" => "Progress"),
            "record_status" => array("name" => "Record status", in_array("search dead", $_SESSION['permissions']) ? true : false),
            "parked_code" => array("name" => "Parked code"),
            "group_id" => array("name" => "Group ownership", in_array("search groups", $_SESSION['permissions']) ? true : false),
            "user_id" => array("name" => "User ownership", "remove" => in_array("search any owner", $_SESSION['permissions']) ? true : false),
            "nextcall" => array("name" => "Nextcall date"),
            "date_updated" => array("name" => "Lastcall date"),
            "date_added" => array("name" => "Created date"),
            "contact_id" => array("name" => "Contact ID"),
            "fullname" => array("name" => "Contact name"),
            "phone" => array("name" => "Contact phone"),
            "position" => array("name" => "Contact position"),
            "dob" => array("name" => "Contact DOB"),
            "contact_email" => array("name" => "Contact email"),
            "address" => array("name" => "Contact address"),
            "company_id" => array("name" => "Company ID"),
            "coname" => array("name" => "Company Name"),
            "company_phone" => array("name" => "Company phone"),
            "sector_id" => array("name" => "Sector"),
            "subsector_id" => array("name" => "Subsector"),
            "turnover" => array("name" => "Turnover"),
            "employees" => array("name" => "Employees"),
            "postcode" => array("name" => "Postcode"),
            "distance" => array("name" => "Distance"),
            "new_only" => array("name" => "New records only"),
            "dials" => array("name" => "Number of dials"),
            "survey" => array("name" => "With survey only"),
            "favorites" => array("name" => "Favorites only"),
            "urgent" => array("name" => "Urgent only"),
            "email" => array("name" => "Email filter"),
            "no_company_tel" => array("name" => "Companies without numbers"),
            "no_phone_tel" => array("name" => "Contacts without numbers"),
            "order" => array("name" => "Order by"),
            "order_direction" => array("name" => "Order direction")
        );

        $aux = array();
        foreach ($filter_options as $option => $values) {
            if (isset($mappings[$option])) {
                $aux[$mappings[$option]['name']]['name'] = $option;
                $aux[$mappings[$option]['name']]['values'] = $this->Modal_model->get_filter_values($option, $values);
                $aux[$mappings[$option]['name']]['removable'] = (isset($mappings[$option]['remove']) ? $mappings[$option]['remove'] : TRUE);
            } else {
                $aux[$option]['name'] = $option;
                $aux[$option]['values'] = $values;
                $aux[$option]['removable'] = TRUE;
            }
        }
        $filter_options = $aux;

        return $filter_options;
    }

    public function remove_filter_option()
    {
        if ($this->input->is_ajax_request()) {

            $field = $this->input->post('field');

            unset($_SESSION['filter']['values'][$field]);

            $filter = $_SESSION['filter']['values'];

            $this->Filter_model->apply_filter($filter);

            echo json_encode(array("success" => true));
        }
    }

    public function clear_filter_option()
    {
        if ($this->input->is_ajax_request()) {

            $filter = $_SESSION['filter']['values'];
            foreach ($filter as $field => $val_filter) {
                unset($_SESSION['filter']['values'][$field]);
            }
            $filter = $_SESSION['filter']['values'];

            $this->Filter_model->apply_filter($filter);

            echo json_encode(array("success" => true));
        }
    }
}