<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Search extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
		$this->_campaigns = campaign_access_dropdown();
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Company_model');
        $this->load->model('Contacts_model');
        $this->load->model('Records_model');
    }
    //this function returns all subsectors for the selected sectors
	public function get_subsectors(){
		$sectors = $this->input->post('sectors');
		$subsectors     = $this->Form_model->get_subsectors($sectors);
		echo json_encode($subsectors);
	}
    
    //this is the default controller for search, its specified in application/config/routes.php.  
    public function search_form()
    { 
        $campaigns      = $this->Form_model->get_user_campaigns();
        $clients        = $this->Form_model->get_clients();
        $users          = $this->Form_model->get_users();
        $outcomes       = $this->Form_model->get_outcomes();
        $outcomes[]     = array(
            "id" => 69,
            "name" => "No Number"
        );
        $progress       = $this->Form_model->get_progress_descriptions();
        $sectors        = $this->Form_model->get_sectors();
		$selected_sectors = array();
		if(isset($_SESSION['filter']['values']['sector_id'])){
		$selected_sectors = $_SESSION['filter']['values']['sector_id'];	
		}
        $subsectors     = $this->Form_model->get_subsectors($selected_sectors);
        $status         = $this->Form_model->get_status_list();
		$parked_codes   = $this->Form_model->get_parked_codes();
        $groups         = $this->Form_model->get_groups();
        $sources        = $this->Form_model->get_sources();
        $campaign_types = $this->Form_model->get_campaign_types();
        $this->firephp->log($sources);
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Search',
            'title' => 'Search',
            'campaigns' => $campaigns,
            'campaign_types' => $campaign_types,
            'clients' => $clients,
            'sources' => $sources,
            'users' => $users,
            'outcomes' => $outcomes,
            'progress' => $progress,
            'sectors' => $sectors,
            'subsectors' => $subsectors,
            'status' => $status,
			'parked_codes' => $parked_codes,
            'groups' => $groups,
            'javascript' => array(
                'filter.js',
            	'location.js'
            )
        );
        $this->template->load('default', 'records/search.php', $data);
    }
    
    public function get_coords()
    {
    	if ($this->input->is_ajax_request()) {
    		$postcode = postcodeCheckFormat($this->input->post('postcode'));
			
    		$this->db->where("postcode",$postcode);
			$geodata = $this->db->get("uk_postcodes");
			if($geodata->num_rows()>0){
			$coords = $geodata->row_array();	
			} else {
			$coords = postcode_to_coords($options['postcode']);
			if(isset($coords['lat'])){
			$this->db->query("insert ignore into uk_postcodes set postcode = '$postcode',lat='{$coords['lat']}',lng='{$coords['lng']}'");
			}
			}
    		
    		if (isset($coords['lat']) && isset($coords['lng'])) {
    			echo json_encode(array(
    					"success" => true,
    					"coords" => $coords
    			));
    		}
    		else {
    			echo json_encode(array(
    					"success" => false,
    					"error" => $coords['error']
    			));
    		}
    	}
    }
    
    public function count_records()
    {
        if ($this->input->is_ajax_request()) {
			$filter = $this->input->post();
			
			if(!in_array("search campaigns",$_SESSION['permissions'])){ 
			  $filter['campaign_id']=array($_SESSION['current_campaign']);
			}
            $urn_array   = $this->Filter_model->count_records($filter);
            $count   = count($urn_array);
            $urn_list = "0";
            foreach($urn_array as $urn) {
                $urn_list .= ", ".$urn['urn'];
            }
            $_SESSION['filter']['result']['urn_list'] = "(".$urn_list.")";
            $_SESSION['filter']['result']['count'] = $count;

            echo json_encode(array(
                "success" => true,
                "data" => $count
            ));
        }
    }

    public function get_urn_list()
    {
        $urn_list = $_SESSION['filter']['result']['urn_list'];

        echo json_encode(array(
            "success" => (strlen($urn_list)),
            "data" => $urn_list
        ));
    }
    
    
    public function apply_filter()
    {
        if ($this->input->is_ajax_request()) {
			$filter = $this->input->post();
			
			if(!in_array("search campaigns",$_SESSION['permissions'])){ 
			  $filter['campaign_id']=array($_SESSION['current_campaign']);
			}
            $count = $this->Filter_model->apply_filter($filter);
            echo json_encode(array(
                "success" => true,
                "data" => $count
            ));
        }
    }
    
    public function custom()
    {
        
        //this turns the url into an key=>val array starting from the given segment (3) because thats after the search(1)/custom(2) part
        $uri   = $this->uri->uri_to_assoc(4);
        //this gets the table we want to look at - typically the records table or the history table
        $table = $this->uri->segment(3);

        $search_fields_1 = array(
            "outcome" => "outcomes.outcome_id",
            "survey" => "surveys.survey_info_id",
            "campaign" => "records.campaign_id",
            "user" => "users.user_id",
            "status" => "record_status",
            "nextcall" => "records.nextcall",
            "score" => "answer",
            "contact-from" => "contact-from",
			"contact-to" => "contact-to",
			"contact" => "date(contact)",
            "question" => "question_id",
            "dials" => "dials",
            "progress" => "progress_id",
			"team" => "teams.team_id",
			"source" => "records.source_id",
			"parked" => "parked_code",
			"cross" => "cross_transfers.campaign_id",
			"alldials" => "alldials", //this gets all dials to a specific campaign including cross transfers
			"transfers" => "transfers", //this gets the transfers + cross transfers
            "time" => "hour(contact)",
            "emails" => "emails",
            "user-email-sent-id" => "user-email-sent-id",
            "sent-email-from" => "sent-email-from",
            "sent-email-to" => "sent-email-to",
            "sent-email-date" => "date(email_history.sent_date)",
            "sent-email-time" => "hour(email_history.sent_date)",
			"read-date" => "date(read_confirmed_date)",
			"template" => "template_id",
            "parked-code" => "records.parked_code",
            "parked" => "parked",
            "update-date-from" => "update-date-from",
            "update-date-to" => "update-date-to",
            "renewal-date-from" => "renewal-date-from",
            "renewal-date-to" => "renewal-date-to"
        );
        
        $search_fields_2 = array(
            "outcome" => "outcome",
            "survey" => "survey_name",
            "campaign" => "campaigns.campaign_name",
            "user" => "users.name",
            "status" => "status_name",
            "nextcall" => "records.nextcall",
            "progress" => "progress_description.description",
			"contact-from" => "date(contact)",
			"contact-to" => "date(contact)",
			"contact" => "date(contact)",
			"team" => "team_name",
			"source" => "source_name",
			"parked" => "parked_code",
			"alldials" => "alldials",
			"transfers" => "transfers",
            "emails" => "emails",
			"template" => "template",
            "parked" => "parked"
        );
        $fields          = array();
        $array           = array();
        //loop through the array and change the field names for the database column names. Unset any values that we havent included in the search fields array above. 
        foreach ($uri as $k => $v) {
			$keysplit = explode("_",$k);
            if(isset($keysplit[1])){
                $k = $keysplit[0];
                if (array_key_exists($k, $search_fields_1)) {
                    //if the value is a number then we look for the ID, if not we look for the text
                    if (intval($v)) {
                        $array[$search_fields_1[$k].":".$keysplit[1]] = urldecode($v);
                    } else {
                        $array[$search_fields_2[$k].":".$keysplit[1]]= urldecode($v);
                    }
                    //end if
                    $fields[] = $k;
                }
			} else {
                if (array_key_exists($k, $search_fields_1)) {
                    //if the value is a number then we look for the ID, if not we look for the text
                    if (intval($v)) {
                        $array[$search_fields_1[$k]] = urldecode($v);
                    } else {
                        $array[$search_fields_2[$k]] = urldecode($v);
                    }
                    //end if
                    $fields[] = $k;
                }
			}
        }
        $this->firephp->log($array);
        
        //Now we can just stick the new array into an active record query to find the matching records!
        $_SESSION['custom_view']['array']  = $array;
        $_SESSION['custom_view']['fields'] = $fields;
        $_SESSION['custom_view']['table']  = $table;
        //this array contains data for the visible columsn in the table on the view page
        $visible_columns                   = array();
        $visible_columns[]                 = array(
            "column" => "campaign_name",
            "header" => "Client Type"
        );
        $visible_columns[]                 = array(
            "column" => "fullname",
            "header" => "Client Name"
        );
        $visible_columns[]                 = array(
            "column" => "outcome",
            "header" => "Last Outcome"
        );
        $visible_columns[]                 = array(
            "column" => "date_updated",
            "header" => "Last Updated"
        );
        $visible_columns[]                 = array(
            "column" => "nextcall",
            "header" => "Next Call"
        );
        $visible_columns[]                 = array(
            "column" => "options",
            "header" => "Options"
        );
        
        $data = array(
            'campaign_access' => $this->_campaigns,
			'pageId' => 'Search',
            'title' => 'Search',
            'columns' => $visible_columns
        );
        $this->template->load('default', 'records/custom.php', $data);
    }
    
    public function process_custom_view()
    {
        if ($this->input->is_ajax_request()) {
            $results = $this->Filter_model->custom_search($this->input->post());
            $count   = $results['count'];
            $records = $results['data'];
            foreach ($records as $k => $v) {
                $records[$k]["options"] = "<a href='" . base_url() . "records/detail/" . $v['urn'] . "'>View</a>";
            }
            $data = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "data" => $records
            );
            echo json_encode($data);
        }
    }

    public function save_parked_code() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $results = $this->Filter_model->save_parked_code($form);

            if ($results) {
                $suppress = $form['suppress'];
                $urn_list = $form['urn_list'];
                if ($suppress) {
                    //Add the phone numbers to the suppression table
                    $reason = $form['reason'];
                    $all_campaigns = $form['all_campaigns'];

                    //Get the phone numbers that are not already suppressed for one campaign or for all of them
                    $phone_number_list = $this->Contacts_model->get_numbers_from_urn_list($urn_list);
                    
                    //Get numbers already suppressed
                    $suppressed_number_list =  $this->Filter_model->get_suppressed_numbers($all_campaigns, 1);
                    
                    //Suppress only the numbers that are not already suppressed
                    $numbers_to_suppress = array_diff($phone_number_list, $suppressed_number_list);

                    //Suppress the phone numbers
                    //$results = $this->suppress_phone_numbers($numbers_to_suppress, $reason, $all_campaigns, 1);
                }
            }

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Parked code was set successfully":"ERROR: Parked code was not set successfully!")
            ));
        }
    }

    public function save_ownership() {
        if ($this->input->is_ajax_request()) {
            $results = $this->Filter_model->save_ownership($this->input->post());

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Ownership was set successfully":"ERROR: Ownership was not set successfully!")
            ));
        }
    }

    public function copy_records() {
        if ($this->input->is_ajax_request()) {
        	$form = $this->input->post();
        	$urn_list = $form['urn_list'];
        	
        	//Get the records
        	$records = $this->Records_model->get_records_by_urn_list($urn_list);
        	
        	//Get the next urn to be inserted (with the autoincrement)
        	$next_urn = $this->Filter_model->get_next_autoincrement_id('records');
        	
        	//Copy the records
        	$records_to_copy = array();
        	foreach ($records as $record) {
        		$record['urn_copied'] = $record['urn'];
        		unset($record['urn']);
        		array_push($records_to_copy, $record);
        	}
        	//$results = $this->Filter_model->copy_records($records_to_copy);
        	
        	//Get the new urns inserted
        	$new_urn_list = $this->Filter_model->get_urns_inserted($urn_list, $next_urn);
        	$aux = array();
        	foreach ($new_urn_list as $new_urn) {
        		$aux[$new_urn['urn_copied']] = $new_urn['urn'];
        	}
        	$new_urn_list = $aux;
        	
        	//Get the record_details
        	$record_details = $this->Records_model->get_record_details_by_urn_list($urn_list);
        	$aux = array();
        	foreach ($record_details as $record_detail) {
        		$record_detail['urn'] = $new_urn_list[$record_detail['urn']];
        		$aux[$record_detail['urn']] = $record_detail;
        	}
        	$record_details = $aux;
        	
        	//Get the companies
        	$companies = $this->Company_model->get_companies_by_urn_list($urn_list);
        	$aux = array();
        	foreach ($companies as $company) {
        		$company['urn'] = $new_urn_list[$company['urn']];
        		if (!isset($aux[$company['urn']][$company['company_id']])) {
        			$aux[$company['urn']][$company['company_id']] = array();
        		}
        		array_push($aux[$company['urn']][$company['company_id']],$company);
        	}
        	$companies = $aux;
        	
        	//Get the contacts
        	$contacts = $this->Contacts_model->get_contacts_by_urn_list($urn_list);
        	$aux = array();
        	foreach ($contacts as $contact) {
        		$contact['urn'] = $new_urn_list[$contact['urn']];
        		if (!isset($aux[$contact['urn']][$contact['contact_id']])) {
        			$aux[$contact['urn']][$contact['contact_id']] = array();
        		}
        		array_push($aux[$contact['urn']][$contact['contact_id']],$contact);
        	}
        	$contacts = $aux;
        	
        	
        	//Compose the array to be copied
        	$record_details_to_copy = array();
        	$companies_to_copy = array();
        	$contacts_to_copy = array();
        	foreach ($records as $record) {
        		$record['urn_copied'] = $record['urn'];
        		$record['urn'] = $new_urn_list[$record['urn']];
        		
        		if (isset($record_details[$record['urn']])) {
        			unset($record_details[$record['urn']]['detail_id']);
        			array_push($record_details_to_copy, $record_details[$record['urn']]);
        		}
        		
        		if (isset($companies[$record['urn']])) {
        			foreach ($companies[$record['urn']] as $company_id => $company) {
        				foreach ($company as $data) {
        					$companies_to_copy[$company_id]['companies'] = array(
        							"company_copied" => $data['company_id'],
        							"urn" => $data['urn'],
        							"name" => $data['name'],
        							"description" => $data['description'],
        							"company_number" => $data['company_number'],
        							"turnover" => $data['turnover'],
        							"employees" => $data['employees'],
        							"website" => $data['website'],
        							"email" => $data['email']
        					);
        					$company_address = array(
        							"company_id" => $data['company_id'],
        							"address_id" => $data['address_id'],
       								"add1" => $data['add1'],
        							"add2" => $data['add2'],
        							"add3" => $data['add3'],
        							"county" => $data['county'],
        							"country" => $data['country'],
        							"postcode" => $data['postcode'],
        							"location_id" => $data['location_id'],
        							"primary" => $data['primary']
        					);
        					if (!isset($companies_to_copy[$company_id]['company_addresses'])) {
        						$companies_to_copy[$company_id]['company_addresses'] = array();
        					}
        					if (!isset($companies_to_copy[$company_id]['company_addresses'][$company_address['address_id']]) && $company_address['address_id']) {
        						$companies_to_copy[$company_id]['company_addresses'][$company_address['address_id']] = $company_address;
        					}
        					
        					$company_subsector = array(
        							"company_id" => $data['company_id'],
        							"subsector_id" => $data['subsector_id']
        					);
        					if (!isset($companies_to_copy[$company_id]['company_subsectors'])) {
        						$companies_to_copy[$company_id]['company_subsectors'] = array();
        					}
        					if (!isset($companies_to_copy[$company_id]['company_subsectors'][$company_subsector['subsector_id']]) && $company_subsector['subsector_id']) {
        						$companies_to_copy[$company_id]['company_subsectors'][$company_subsector['subsector_id']] = $company_subsector;
        					}
        					
        					$company_telephone = array(
        							"company_id" => $data['company_id'],
        							"telephone_id" => $data['telephone_id'],
        							"telephone_number" => $data['telephone_number'],
        							"ctps" => $data['ctps']
        					);
        					if (!isset($companies_to_copy[$company_id]['company_telephone'])) {
        						$companies_to_copy[$company_id]['company_telephone'] = array();
        					}
        					if (!isset($companies_to_copy[$company_id]['company_telephone'][$company_telephone['telephone_id']]) && $company_telephone['telephone_id']) {
        						$companies_to_copy[$company_id]['company_telephone'][$company_telephone['telephone_id']] = $company_telephone;
        					}
        					
        				}
        			}
        		}
        		
        		if (isset($contacts[$record['urn']])) {
        			foreach ($contacts[$record['urn']] as $contact_id => $contact) {
        				foreach ($contact as $data) {
        					$contacts_to_copy[$contact_id]['contacts'] = array(
        							"contact_copied" => $data['contact_id'],
        							"urn" => $data['urn'],
        							"fullname" => $data['fullname'],
        							"title" => $data['title'],
        							"firstname" => $data['firstname'],
        							"lastname" => $data['lastname'],
        							"gender" => $data['gender'],
        							"position" => $data['position'],
        							"dob" => $data['dob'],
        							"fax" => $data['fax'],
        							"email" => $data['email'],
        							"email_optout" => $data['email_optout'],
        							"website" => $data['website'],
        							"linkedin" => $data['linkedin'],
        							"facebook" => $data['facebook'],
        							"notes" => $data['notes'],
        							"sort" => $data['sort']
        					);
        					$contact_address = array(
        							"contact_id" => $data['contact_id'],
        							"address_id" => $data['address_id'],
       								"add1" => $data['add1'],
        							"add2" => $data['add2'],
        							"add3" => $data['add3'],
        							"county" => $data['county'],
        							"country" => $data['country'],
        							"postcode" => $data['postcode'],
        							"location_id" => $data['location_id'],
        							"primary" => $data['primary']
        					);
        					if (!isset($contacts_to_copy[$contact_id]['contact_addresses'])) {
        						$contacts_to_copy[$contact_id]['contact_addresses'] = array();
        					}
        					if (!isset($contacts_to_copy[$contact_id]['contact_addresses'][$contact_address['address_id']]) && $contact_address['address_id']) {
        						$contacts_to_copy[$contact_id]['contact_addresses'][$contact_address['address_id']] = $contact_address;
        					}
        					
        					$contact_telephone = array(
        							"contact_id" => $data['contact_id'],
        							"telephone_id" => $data['telephone_id'],
        							"telephone_number" => $data['telephone_number'],
        							"description" => $data['description'],
        							"tps" => $data['tps']
        					);
        					if (!isset($contacts_to_copy[$contact_id]['contact_telephone'])) {
        						$contacts_to_copy[$contact_id]['contact_telephone'] = array();
        					}
        					if (!isset($contacts_to_copy[$contact_id]['contact_telephone'][$contact_telephone['telephone_id']]) && $contact_telephone['telephone_id']) {
        						$contacts_to_copy[$contact_id]['contact_telephone'][$contact_telephone['telephone_id']] = $contact_telephone;
        					}
        					
        				}
        			}
        		}
        	}
        	
        	//Copy the record_details
        	$results = $this->Filter_model->copy_record_details($record_details);
        	
        	//Copy the companies
        	$this->copy_companies($companies_to_copy);
        	
        	//Copy the contacts
        	//$this->copy_companies($contacts_to_copy);

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Records were copied successfully":"ERROR: Records were not copied  successfully!")
            ));
        }
    }
    
    private function copy_companies($companies) {
    	$companies_to_copy = array();
    	$company_list = "0";
    	foreach ($companies as $company_id => $company) {
    		array_push($companies_to_copy, $company['companies']);
    		$company_list .= ", ".$company_id;
    	}
    	$company_list = "(".$company_list.")";
    	
    	//Get the next company_id to be inserted (with the autoincrement)
    	$next_company_id = $this->Filter_model->get_next_autoincrement_id('companies');
    	
    	//Copy the record_details
    	//$results = $this->Filter_model->copy_companies($companies_to_copy);
    	
    	//Get the new company_ids
    	$new_company_list = $this->Filter_model->get_companies_inserted($company_list, $next_company_id);
    	$aux = array();
    	foreach ($new_company_list as $new_company) {
    		$aux[$new_company['company_copied']] = $new_company['company_id'];
    	}
    	$new_company_list = $aux;
    	
    	
    }
}