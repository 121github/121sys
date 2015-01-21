<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Search extends CI_Controller
{
    
    public function __construct()

    {
        parent::__construct();
        user_auth_check();
		check_page_permissions("search records");
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
			
            echo json_encode(array(
                "success" => true,
                "query" => $urn_array['query'],
                "data" => count($urn_array['data'])
            ));
        }
    }

    public function get_urn_list()
    {
        $form = $this->input->post();

        $urn_array = $this->Filter_model->get_urn_list($form['query']);

        $urn_list = "0";
        foreach($urn_array as $urn) {
            $urn_list .= ", ".$urn['urn'];
        }

        $urn_list = "(".$urn_list.")";

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
                    $suppression_campaigns = $form['suppression_campaigns'];

                    //Get the phone numbers that are not already suppressed for one campaign or for all of them
                    $phone_number_list = $this->Contacts_model->get_numbers_from_urn_list($urn_list);
                    $aux = array();
                    foreach($phone_number_list as $phone_number) {
                        $aux[trim($phone_number)] = array(
                            "telephone_number" => trim($phone_number),
                            "reason" => $reason
                        );
                    }
                    $phone_number_list = $aux;
                    
                    //Get numbers already suppressed
                    $suppressed_number_list =  $this->Filter_model->get_suppressed_numbers();
                    $aux = array();
                    foreach($suppressed_number_list as $suppressed) {
                        if (!isset($aux[$suppressed['telephone_number']])) {
                            $aux[$suppressed['telephone_number']] = array();
                        }
                        array_push($aux[$suppressed['telephone_number']], $suppressed);
                        array_push($suppressed_number_list, $suppressed['telephone_number']);
                    }
                    $suppressed_number_list = $aux;

                    //Suppress the phone numbers
                    $this->suppress_phone_numbers($phone_number_list, $suppressed_number_list, $all_campaigns, $suppression_campaigns);
                }
            }

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Parked code was set successfully":"ERROR: Parked code was not set successfully!")
            ));
        }
    }

    private function suppress_phone_numbers($phone_number_list, $suppressed_number_list, $all_campaigns, $suppression_campaigns) {
        foreach($phone_number_list as $phone_number)
        {
            if (!isset($suppressed_number_list[$phone_number['telephone_number']])) {
                //Insert new suppressed number
                $suppression_id = $this->Filter_model->insert_suppression_number($phone_number);
                if (!$all_campaigns && $suppression_id) {
                    //Insert suppression_by_campaign
                    foreach($suppression_campaigns as $campaign_id) {
                        $this->Filter_model->insert_suppression_by_campaign($suppression_id, $campaign_id);
                    }
                }
            }
            else {
                //Update suppressed number (reason)
                $this->Filter_model->update_suppression_number($phone_number);
                //Update suppression_by_campaign if is needed
                $update_suppression_by_campaign_ar = array();
                foreach($suppressed_number_list[$phone_number['telephone_number']] as $suppressed_number) {
                    array_push($update_suppression_by_campaign_ar, $suppressed_number['campaign_id']);
                }
                if ($all_campaigns) {
                    foreach($update_suppression_by_campaign_ar as $campaign_id) {
                        $this->Filter_model->remove_suppression_by_campaign($suppressed_number['suppression_id']);
                    }
                }
                else {
                    foreach($suppression_campaigns as $campaign_id) {
                        if (!in_array($campaign_id, $update_suppression_by_campaign_ar) || empty($update_suppression_by_campaign_ar)) {
                            $this->Filter_model->insert_suppression_by_campaign($suppressed_number['suppression_id'], $campaign_id);
                        }
                    }
                }
            }
        }
    }

    public function add_ownership() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $urn_list = str_replace(' ','', $form['urn_list']);
            $urn_list_ar = explode(',', substr($urn_list, 1, strlen($urn_list)-2));
            $ownership_list = $form['ownership_ar'];

            $ownership_by_urn_list = $this->Records_model->get_ownership_by_urn_list($urn_list);
            $aux = array();
            foreach($ownership_by_urn_list as $ownership) {
                if (!isset($aux[$ownership['urn']])) {
                    $aux[$ownership['urn']] = array();
                }
                array_push($aux[$ownership['urn']], $ownership['user_id']);
            }
            $ownership_by_urn_list = $aux;

            $aux = array();
            foreach($urn_list_ar as $urn) {
                foreach($ownership_list as $ownership) {
                    if ($urn > 0 && isset($ownership_by_urn_list[$urn]) && !in_array($ownership, $ownership_by_urn_list[$urn])) {
                        array_push($aux, array(
                            'urn' => $urn,
                            'user_id' => $ownership
                        ));
                    }
                }
            }
            $form = $aux;

            if (!empty($form)) {
                $results = $this->Filter_model->add_ownership($form);
                echo json_encode(array(
                    "success" => ($results),
                    "msg" => ($results?"Ownership(s) added successfully":"ERROR: Ownership(s) not added successfully!")
                ));
            }
            else {
                echo json_encode(array(
                    "success" => true,
                    "msg" => ("Ownership(s) already exist for the urn(s) listed!")
                ));
            }
        }
    }

    public function replace_ownership() {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            $urn_list = str_replace(' ','', $form['urn_list']);
            $urn_list_ar = explode(',', substr($urn_list, 1, strlen($urn_list)-2));
            $ownership_list = $form['ownership_ar'];

            $this->Filter_model->remove_ownership_by_urn_list($urn_list);

            $aux = array();
            foreach($urn_list_ar as $urn) {
                foreach($ownership_list as $ownership) {
                    if ($urn > 0) {
                        array_push($aux, array(
                            'urn' => $urn,
                            'user_id' => $ownership
                        ));
                    }
                }
            }
            $form = $aux;

            $results = $this->Filter_model->add_ownership($form);

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Ownership(s) replaced successfully":"ERROR: Ownership(s) not replaced successfully!")
            ));
        }
    }

    public function copy_records() {
        if ($this->input->is_ajax_request()) {
        	$form = $this->input->post();
        	$urn_list = $form['urn_list'];
            $campaign_id = $form['campaign_id'];
        	
        	//Get the records
            $records = $this->Records_model->get_records_by_urn_list($urn_list);
            $new_urn_list = $this->copy_records_to_campaign($records, $urn_list, $campaign_id);

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
        							"conumber" => $data['conumber'],
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
            if (!empty($record_details)) {
                $this->Filter_model->copy_record_details($record_details);
            }
        	
        	//Copy the companies
            if (!empty($companies_to_copy)) {
                $this->copy_companies($companies_to_copy);
            }
        	
        	//Copy the contacts
            if (!empty($contacts_to_copy)) {
                $this->copy_contacts($contacts_to_copy);
            }

            $results = true;

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results?"Records were copied successfully":"ERROR: Records were not copied  successfully!")
            ));
        }
    }

    private function copy_records_to_campaign($records, $urn_list, $campaign_id) {

        //Get the next urn to be inserted (with the autoincrement)
        $next_urn = $this->Filter_model->get_next_autoincrement_id('records');

        //Copy the records
        $records_to_copy = array();
        foreach ($records as $record) {

            $record['campaign_id'] = $campaign_id;
            $record['urn_copied'] = $record['urn'];
            unset($record['urn']);
            $record['date_added'] = date('Y-m-d H:i:s');
            unset($record['date_updated']);
            array_push($records_to_copy, $record);
        }
        $results = $this->Filter_model->copy_records($records_to_copy);


        //Get the new urns inserted
        $new_urn_list = $this->Filter_model->get_urns_inserted($urn_list, $next_urn);
        $aux = array();
        foreach ($new_urn_list as $new_urn) {
            $aux[$new_urn['urn_copied']] = $new_urn['urn'];
        }
        $new_urn_list = $aux;

        $this->firephp->log($new_urn_list);

        return $new_urn_list;
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
    	
    	//Copy the companies
    	$results = $this->Filter_model->copy_companies($companies_to_copy);
    	
    	//Get the new company_ids
    	$new_company_list = $this->Filter_model->get_companies_inserted($company_list, $next_company_id);
    	$aux = array();
    	foreach ($new_company_list as $new_company) {
    		$aux[$new_company['company_copied']] = $new_company['company_id'];
    	}
    	$new_company_list = $aux;

    	$company_addresses = array();
        $company_subsectors = array();
        $company_telephones = array();
        foreach($companies as $company) {
            foreach($company['company_addresses'] as $company_address) {
                $company_address['company_id'] = $new_company_list[$company_address['company_id']];
                unset($company_address['address_id']);
                array_push($company_addresses, $company_address);
            }
            foreach($company['company_subsectors'] as $company_subsector) {
                $company_subsector['company_id'] = $new_company_list[$company_subsector['company_id']];
                unset($company_subsector['subsector_id']);
                array_push($company_subsectors, $company_subsector);
            }
            foreach($company['company_telephone'] as $company_telephone) {
                $company_telephone['company_id'] = $new_company_list[$company_telephone['company_id']];
                unset($company_telephone['telephone_id']);
                array_push($company_telephones, $company_telephone);
            }
        }

        //Copy the company_addresses
        if (!empty($company_addresses)) {
            $results = $this->Filter_model->copy_company_addresses($company_addresses);
        }

        //Copy the company_subsectors
        if (!empty($company_subsectors)) {
            $results = $this->Filter_model->copy_company_subsectors($company_subsectors);
        }

        //Copy the company_telephones
        if (!empty($company_telephones)) {
            $results = $this->Filter_model->copy_company_telephone($company_telephones);
        }
    }

    private function copy_contacts($contacts) {

        $contacts_to_copy = array();
        $contact_list = "0";
        foreach ($contacts as $contact_id => $contact) {
            array_push($contacts_to_copy, $contact['contacts']);
            $contact_list .= ", ".$contact_id;
        }
        $contact_list = "(".$contact_list.")";

        //Get the next contact_id to be inserted (with the autoincrement)
        $next_contact_id = $this->Filter_model->get_next_autoincrement_id('contacts');

        //Copy the contacts
        $results = $this->Filter_model->copy_contacts($contacts_to_copy);

        //Get the new contact_ids
        $new_contact_list = $this->Filter_model->get_contacts_inserted($contact_list, $next_contact_id);
        $aux = array();
        foreach ($new_contact_list as $new_contact) {
            $aux[$new_contact['contact_copied']] = $new_contact['contact_id'];
        }
        $new_contact_list = $aux;

        $contact_addresses = array();
        $contact_telephones = array();
        foreach($contacts as $contact) {
            foreach($contact['contact_addresses'] as $contact_address) {
                $contact_address['contact_id'] = $new_contact_list[$contact_address['contact_id']];
                unset($contact_address['address_id']);
                array_push($contact_addresses, $contact_address);
            }
            foreach($contact['contact_telephone'] as $contact_telephone) {
                $contact_telephone['contact_id'] = $new_contact_list[$contact_telephone['contact_id']];
                unset($contact_telephone['telephone_id']);
                array_push($contact_telephones, $contact_telephone);
            }
        }

        //Copy the contact_addresses
        if (!empty($contact_addresses)) {
            $results = $this->Filter_model->copy_contact_addresses($contact_addresses);
        }

        //Copy the contact_telephones
        if (!empty($contact_telephones)) {
            $results = $this->Filter_model->copy_contact_telephone($contact_telephones);
        }
    }
}