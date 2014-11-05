<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
		$this->_campaigns = campaign_access_dropdown();
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Admin_model');
        $this->load->model('User_model');
    }
    //this controller loads the view for the user page
    public function users()
    {
		$options['teams']  = $this->Form_model->get_teams();
        $options['roles']  = $this->Form_model->get_roles();
        $options['groups'] = $this->Form_model->get_groups();
        $data              = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'users'
            ),
            'options' => $options,
            'javascript' => array(
                'admin/users.js'
            ),
            'css' => array(
                'dashboard.css'
            )
        );
        $this->template->load('default', 'admin/users.php', $data);
    }
    //this controller displays the users data in JSON format.
    public function user_data()
    {
        if ($this->input->is_ajax_request()) {
            $results = $this->Admin_model->get_users();
            echo json_encode(array(
                "success" => true,
                "data" => $results,
                "msg" => "Nothing found"
            ));
            exit;
        }
    }
    //this loads the user management view  
    public function campaigns()
    {
        $options['types']     = $this->Form_model->get_campaign_types(false);
        $options['features']  = $this->Form_model->get_campaign_features();
        $options['clients']   = $this->Form_model->get_clients();
        $options['groups']    = $this->Form_model->get_groups();
        $options['campaigns'] = $this->Form_model->get_campaigns();
        $data                 = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'campaign'
            ),
            'javascript' => array(
                'dashboard.js',
                'admin/campaigns.js'
            ),
            'options' => $options,
            'css' => array(
                'dashboard.css'
            )
        );
        $this->template->load('default', 'admin/campaign.php', $data);
    }
    public function users_in_group()
    {
        if ($this->input->is_ajax_request()) {
            $users = $this->Form_model->users_in_group($this->input->post("id"), $this->input->post("campaign"));
            echo json_encode(array(
                "success" => true,
                "data" => $users
            ));
        }
    }
    public function populate_outcomes()
    {
        if ($this->input->is_ajax_request()) {
            $outcomes = $this->Form_model->populate_outcomes($this->input->post("id"));
            echo json_encode(array(
                "success" => true,
                "data" => $outcomes
            ));
        }
    }
    public function populate_clients()
    {
        if ($this->input->is_ajax_request()) {
            $clients = $this->Form_model->get_clients();
            echo json_encode(array(
                "success" => true,
                "data" => $clients
            ));
        }
    }
    public function campaign_outcomes()
    {
        if ($this->input->is_ajax_request()) {
            $outcomes = $this->Form_model->campaign_outcomes($this->input->post("id"));
            echo json_encode(array(
                "success" => true,
                "data" => $outcomes
            ));
        }
    }
    public function add_campaign_outcomes()
    {
        if ($this->input->is_ajax_request()) {
            foreach ($this->input->post("outcomes") as $outcome) {
                $this->Admin_model->add_campaign_outcome($this->input->post("campaign"), $outcome);
            }
            echo json_encode(array(
                "success" => true
            ));
        }
    }
    public function remove_campaign_outcomes()
    {
        if ($this->input->is_ajax_request()) {
            foreach ($this->input->post("outcomes") as $outcome) {
                $this->Admin_model->remove_campaign_outcome($this->input->post("campaign"), $outcome);
            }
            echo json_encode(array(
                "success" => true
            ));
        }
    }
    public function revoke_access()
    {
        if ($this->input->is_ajax_request()) {
            foreach ($this->input->post("users") as $user) {
                $this->Admin_model->revoke_campaign_access($this->input->post("campaign"), $user);
            }
            $this->User_model->flag_users_for_reload($this->input->post("users"));
            echo json_encode(array(
                "success" => true
            ));
        }
    }
    public function add_access()
    {
        if ($this->input->is_ajax_request()) {
            foreach ($this->input->post("users") as $user) {
                $this->Admin_model->add_campaign_access($this->input->post("campaign"), $user);
            }
            $this->User_model->flag_users_for_reload($this->input->post("users"));
            echo json_encode(array(
                "success" => true
            ));
        }
    }
    public function get_campaign_access()
    {
        if ($this->input->is_ajax_request()) {
            $users = $this->Form_model->get_campaign_access($this->input->post("id"));
            echo json_encode(array(
                "success" => true,
                "data" => $users
            ));
        }
    }
    public function get_campaigns()
    {
        $campaigns = $this->Admin_model->get_campaign_details();
        echo json_encode(array(
            "data" => $campaigns
        ));
    }
    public function save_campaign()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();
            if (isset($form['features'])) {
                $features['features'] = $form['features'];
            } else {
                $features = array();
            }
            unset($form['features']);
            if (empty($form['start_date'])) {
                $form['start_date'] = NULL;
            } else {
                $form['start_date'] = to_mysql_datetime($form['start_date']);
            }
            if (empty($form['end_date'])) {
                $form['end_date'] = NULL;
            } else {
                $form['end_date'] = to_mysql_datetime($form['end_date']);
            }
            if (!empty($form['new_client']) && $form['client_id'] == "other" || !empty($form['new_client']) && empty($form['client_id'])) {
                $client_id = $this->Admin_model->find_client($form['new_client']);
                if (!$client_id) {
                    $client_id = $this->Admin_model->add_client($form['new_client']);
                }
                $form['client_id'] = $client_id;
            }
            unset($form['new_client']);
            if (empty($form['campaign_id'])) {
                $response                = $this->Admin_model->add_new_campaign($form);
                $features['campaign_id'] = $response;
            } else {
                $features['campaign_id'] = $form['campaign_id'];
                $response                = $this->Admin_model->update_campaign($form);
                $access                  = $this->Form_model->get_campaign_access($form['campaign_id']);
                //this part is to revoke/grant access to users that are already logged in
                $user_array              = array();
                foreach ($access as $user) {
                    $user_array[] = $user['id'];
                }
                if ($this->User_model->flag_users_for_reload($user_array));
            }
            //if it's set as B2B then we add the company feature to the campaign
            if ($form['campaign_type_id'] == "2") {
                $features['features'][] = 2;
            }
            //all campaigns need the contact and update panel at a minimum
            if (!in_array(1, $features['features'])) {
                $features['features'][] = 1;
            }
            if (!in_array(3, $features['features'])) {
                $features['features'][] = 3;
            }
            $response = $this->Admin_model->save_campaign_features($features);
            echo json_encode(array(
                "data" => $response
            ));
        }
    }
    public function get_campaign_features()
    {
        if ($this->input->is_ajax_request()) {
            $response = $this->Form_model->get_campaign_features($this->input->post('campaign'));
            $data     = array();
            foreach ($response as $row) {
                $data[] = $row['id'];
            }
            echo json_encode(array(
                "data" => $data
            ));
        }
    }
    //this loads the logs view  
    public function logs()
    {
        $logs = $this->Admin_model->get_logs();
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'logs'
            ),
            'logs' => $logs,
            'css' => array(
                'dashboard.css'
            )
        );
        $this->template->load('default', 'admin/logs.php', $data);
    }
    //roles page functions
    public function roles()
    {
        $roles            = $this->Admin_model->get_roles();
        $permissions_data = $this->Admin_model->get_permissions();
        foreach ($permissions_data as $row) {
            $permissions[$row['permission_group']][$row['permission_id']] = $row['permission_name'];
        }
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'roles'
            ),
            'javascript' => array(
                'admin/roles.js'
            ),
            'roles' => $roles,
            'permissions' => $permissions,
            'css' => array(
                'dashboard.css'
            )
        );
        $this->template->load('default', 'admin/roles.php', $data);
    }
    public function get_roles()
    {
        $roles = $this->Form_model->get_roles();
        echo json_encode(array(
            "data" => $roles
        ));
    }
    public function get_role_permissions()
    {
        $id     = $this->input->post('id');
        $result = $this->Admin_model->role_permissions($id);
        echo json_encode(array(
            "data" => $result
        ));
    }
    public function save_role()
    {
        $form = $this->input->post();
        if (empty($form['role_id'])) {
            $response = $this->Admin_model->add_new_role($form);
        } else {
            $response      = $this->Admin_model->update_role($form);
            $users_in_role = $this->Form_model->get_users_in_role($form['role_id']);
            $users         = array();
            foreach ($users_in_role as $row) {
                $users[] = $row['id'];
            }
            $this->User_model->flag_users_for_reload($users);
        }
        echo json_encode(array(
            "data" => $response
        ));
    }
    public function delete_role()
    {
        $response = $this->Admin_model->delete_role(intval($this->input->post('id')));
        if ($response) {
            echo json_encode(array(
                "success" => true,
                "data" => $response
            ));
        } else {
            echo json_encode(array(
                "success" => false
            ));
        }
    }
    //this loads the groups view  
    public function groups()
    {
        $data = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'groups'
            ),
            'javascript' => array(
                'admin/groups.js'
            ),
            'css' => array(
                'dashboard.css'
            )
        );
        $this->template->load('default', 'admin/groups.php', $data);
    }
    public function get_groups()
    {
        $groups = $this->Form_model->get_groups();
        echo json_encode(array(
            "data" => $groups
        ));
    }
    public function save_group()
    {
        $form = $this->input->post();
        if (empty($form['group_id'])) {
            $response = $this->Admin_model->add_new_group($form);
        } else {
            $response = $this->Admin_model->update_group($form);
        }
        echo json_encode(array(
            "data" => $response
        ));
    }
    /* team page functions */
    public function teams()
    {
        $groups   = $this->Form_model->get_groups();
        $managers = $this->Form_model->get_managers();
        $data     = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'teams'
            ),
            'javascript' => array(
                'admin/teams.js'
            ),
            'css' => array(
                'dashboard.css'
            ),
            'options' => array(
                'groups' => $groups,
                'managers' => $managers
            )
        );
        $this->template->load('default', 'admin/teams.php', $data);
    }
    public function get_team_managers()
    {
        $team     = intval($this->uri->segment(3));
        $result   = $this->Form_model->get_team_managers($team);
        $managers = array();
        foreach ($result as $row) {
            $managers[] = $row['id'];
        }
        echo json_encode(array(
            "data" => $managers
        ));
    }
    public function get_teams()
    {
        $teams = $this->Form_model->get_teams();
        echo json_encode(array(
            "data" => $teams
        ));
    }
    public function save_team()
    {
        $form = $this->input->post();
        if (empty($form['team_id'])) {
            $response = $this->Admin_model->add_new_team($form);
        } else {
            $response = $this->Admin_model->update_team($form);
        }
        echo json_encode(array(
            "data" => $response
        ));
    }
    /* end team page functions */
    public function save_user()
    {
        $form = $this->input->post();
        if (empty($form['user_id'])) {
            $response = $this->Admin_model->add_new_user($form);
        } else {
            $response = $this->Admin_model->update_user($form);
			$this->User_model->flag_users_for_reload(array($form['user_id']));
        }
		
        echo json_encode(array(
            "data" => $response
        ));
    }
    public function delete_group()
    {
        $response = $this->Admin_model->delete_group(intval($this->input->post('id')));
        if ($response) {
            echo json_encode(array(
                "success" => true,
                "data" => $response
            ));
        } else {
            echo json_encode(array(
                "success" => false
            ));
        }
    }
    public function delete_user()
    {
        $response = $this->Admin_model->delete_user(intval($this->input->post('id')));
        if ($response) {
            echo json_encode(array(
                "success" => true,
                "data" => $response
            ));
        } else {
            echo json_encode(array(
                "success" => false
            ));
        }
    }
    public function delete_campaign()
    {
        $response = $this->Admin_model->delete_campaign(intval($this->input->post('id')));
        if ($response) {
            echo json_encode(array(
                "success" => true,
                "data" => $response
            ));
        } else {
            echo json_encode(array(
                "success" => false
            ));
        }
    }
    
    /* hours page functions */
    public function hours()
    {
		$this->load->model('Cron_model');
		$teams = $this->Form_model->get_teams();
    	$campaigns = $this->Form_model->get_campaigns();
    	$agents = $this->Form_model->get_agents();
    	$exception_types = $this->Form_model->get_hours_exception_type();
    	$this->Cron_model->update_hours($agents);
    	$data     = array(
    			'campaign_access' => $this->_campaigns,
    			'pageId' => 'Admin',
    			'title' => 'Admin | Hours',
    			'page' => array(
    					'admin' => 'hours',
    					'inner' => 'hours',
    			),
    			'javascript' => array(
    					'admin/hours.js',
    					'lib/moment.js',
    					'lib/jquery.numeric.min.js',
    					'lib/daterangepicker.js',
    					'lib/bootstrap-datetimepicker.js'
    			),
    			'css' => array(
    					'dashboard.css',
                		'daterangepicker-bs3.css'
    			),
    			'campaigns' => $campaigns,
          		'agents' => $agents,
				'team_managers'=>$teams,
    			'exception_types'=>$exception_types
    	);
    	$this->template->load('default', 'admin/hours.php', $data);
    }
    
    /**
     * Get the hours in a date range
     * 
     * POST
     * 
     */
    public function get_hours_data()
    {
    	if ($this->input->is_ajax_request()) {
            $post = $this->input->post();
            $date_from = $post['date_from'];
            $date_to = $post['date_to'];

            $result = array();
            if ($date_from && $date_to) {
            	$totalDays = floor(((strtotime($date_to) - strtotime($date_from)) / (60 * 60 * 24)));
                for ($i = 0; $i <= $totalDays; $i++) {
                    $date = date('Y-m-d',strtotime($date_to." - ".$i." DAYS"));
                    $post['date_from'] = $date;
                    $post['date_to'] = $date;
                    $user_logged = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;
                    if (!($post['team'])) {
                        $team_managers = $this->User_model->get_team_managers($user_logged);
                        if (!empty($team_managers)) {
                            $teams = '';
                            foreach($team_managers as $team_manager) {
                                $teams .= $team_manager['team_id'].",";
                            }
                            $teams = substr($teams,0,strlen($teams)-1);
                            $post['team'] = $teams;
                        }

                    }
                    $hours = $this->Admin_model->get_hours($post);
                    $date_ = date('d/m/Y',strtotime($date));
                    $result[$date_] = $hours;
                }
            }
    		
    		echo json_encode(array(
    				"success" => true,
    				"data" => $result,
    				"msg" => "Nothing found"
    		));
    	}
    }
    
    /**
     * Add a new Hour Exception
     */
    public function add_hour_exception() {
    	$form = $this->input->post();
    	
   		$exception_id = $this->Admin_model->add_hour_exception($form);
        if ($exception_id) {
        	$hours_form = array();
        	$hours_form['hours_id'] = $form['hours_id'];
        	$hours_form['updated_date'] = date('Y-m-d H:i:s');
        	$hours_form['updated_id'] = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;
        	 
        	$response = $this->Admin_model->update_hour($hours_form);
        	if ($response) {
        		echo json_encode(array(
        				"success" => true,
        				"exception_id" => $exception_id
        		));
        	} else {
        		echo json_encode(array(
        				"success" => false
        		));
        	}
        } else {
            echo json_encode(array(
                "success" => false
            ));
        }
    }
    
    /**
     * Remove an Hour Exception
     */
    public function remove_hour_exception() {
    	$form = $this->input->post();
    	 
    	$response = $this->Admin_model->delete_hour_exception($form['exception_id']);
    	if ($response) {
    		$hours_form = array();
    		$hours_form['hours_id'] = $form['hours_id'];
    		$hours_form['updated_date'] = date('Y-m-d H:i:s');
    		$hours_form['updated_id'] = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;
    		
    		$response_hours = $this->Admin_model->update_hour($hours_form);
    		if ($response_hours) {
	    		echo json_encode(array(
	    				"success" => true,
	    				"data" => $response
	    		));
    		} else {
	    		echo json_encode(array(
	    				"success" => false
	    		));
	    	}
    	} else {
    		echo json_encode(array(
    				"success" => false
    		));
    	}
    }
    
    /**
     * Get the Hour Exceptions for a particular Hour
     */
    public function get_hour_exception() {
    	$form = $this->input->post();
    
    	$response = $this->Admin_model->get_hour_exception($form['hours_id']);
    	if ($response) {
    		echo json_encode(array(
    				"success" => true,
    				"data" => $response
    		));
    	} else {
    		echo json_encode(array(
    				"success" => false,
    				"message" => "No results"
    		));
    	}
    }
    
    /**
     * Save an Hour for an agent and campaing in a particular date
     */
    public function save_hour()
    {
    	$form = $this->input->post();
    	
    	if (isset($form['date'])) {
    		$date = explode('-', $form['date']);
    		$day = $date[0];
    		$month = $date[1];
    		$year = $date[2];
    		
    		$form['date'] = $year.'-'.$month.'-'.$day;
    	}

        //Check if the agent exceeded the 7 hours today
        $isExceeded = $this->isDurationExceeded($form['date'], $form['user_id'], $form['duration'],$form['campaign_id']);

        if (!$isExceeded) {
            if (isset($form['exception_id'])) {
                unset($form['exception_id']);
            }
            if (isset($form['exception-duration'])) {
                unset($form['exception-duration']);
            }

            $form['duration'] = $form['duration']*60;
            $form['updated_date'] = date('Y-m-d H:i:s');
            $form['updated_id'] = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;

            if (empty($form['hours_id'])) {
                $response = $this->Admin_model->add_new_hour($form);
            } else {
                $response = $this->Admin_model->update_hour($form);
            }

            if ($response) {
                echo json_encode(array(
                    "success" => true,
                    "data" => $response,
                    "message" => "Hour saved"
                ));
            }
            else {
                echo json_encode(array(
                    "success" => false,
                    "message" => "ERROR: Hour NOT saved"
                ));
            }
        }
        else {
            echo json_encode(array(
                "success" => false,
                "message" => "ERROR: This agent exceeded the total hours in a day"
            ));
        }

    }

    private function isDurationExceeded($date, $user_id, $new_duration, $campaign_id){
        $options = array();
        $options['date_from'] = $date;
        $options['date_to'] = $date;
        $options['agent'] = $user_id;
        $options['campaign'] = NULL;
        $options['team'] = NULL;

        $current_duration = 0;

        $hours = $this->Admin_model->get_hours($options);

        foreach ($hours as $hour) {
            if ($hour['campaign_id'] != $campaign_id) {
                $duration = ($hour['duration'])?$hour['duration']:0;
                $current_duration += $duration;
            }
        }

        if ($current_duration/3600 + $new_duration/60 > 7) {
            return true;
        }
        else {
            return false;
        }
    }
	
	 /* hours page functions */
    public function campaign_fields()
    {
	
    	$campaigns = $this->Form_model->get_campaigns();
    	$data     = array(
    			'campaign_access' => $this->_campaigns,
    			'pageId' => 'Admin',
    			'title' => 'Campaign custom fields',
    			'page' => array(
    					'admin' => 'custom_fields'
    			),
    			'javascript' => array(
    					'admin/customfields.js'
    			),
    			'css' => array(
    					'dashboard.css'
    			),
    			'campaigns' => $campaigns
    	);
    	$this->template->load('default', 'admin/custom_fields.php', $data);
    }
	
	public function get_custom_fields(){
		$fields = $this->Admin_model-> get_custom_fields($this->input->post('campaign'));
		echo json_encode($fields);
	}
	
		public function save_custom_fields(){
		$fields = $this->Admin_model-> save_custom_fields($this->input->post());
		echo json_encode(array("success"=>true));
	}
	
}
