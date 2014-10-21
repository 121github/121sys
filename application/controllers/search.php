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
        $subsectors     = $this->Form_model->get_subsectors();
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
    		$postcode = $this->input->post('postcode');
    			
    		$coords = postcode_to_coords($postcode);
    		
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
            $count   = $this->Filter_model->count_records($filter);
            echo json_encode(array(
                "success" => true,
                "data" => $count
            ));
        }
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
            "outcome" => "outcome_id",
            "survey" => "surveys.survey_info_id",
            "campaign" => "records.campaign_id",
            "user" => "users.user_id",
            "status" => "record_status",
            "nextcall" => "nextcall",
            "score" => "answer",
            "contact" => "contact",
            "question" => "question_id",
            "dials" => "dials",
            "progress" => "progress_id",
			"team" => "teams.team_id",
			"source" => "records.source_id",
			"parked" => "parked_code"
        );
        
        $search_fields_2 = array(
            "outcome" => "outcome",
            "survey" => "survey_name",
            "campaign" => "campaign_name",
            "user" => "users.name",
            "status" => "status_name",
            "nextcall" => "nextcall",
            "progress" => "description",
			"team" => "team_name",
			"source" => "source_name",
			"parked" => "parked_code"
        );
        $fields          = array();
        $array           = array();
        //loop through the array and change the field names for the database column names. Unset any values that we havent included in the search fields array above. 
        foreach ($uri as $k => $v) {
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
}