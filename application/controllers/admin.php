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
        $options['groups'] = $this->Form_model->get_all_groups();
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
        $options['groups']    = $this->Form_model->get_all_groups();
        $options['campaigns'] = $this->Form_model->get_campaigns();
		$options['views'] = $this->Form_model->get_custom_views();
        $data                 = array(
            'campaign_access' => $this->_campaigns,
'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'campaign'
            ),
            'javascript' => array(
                'dashboard.js',
                'admin/campaigns.js',
                'lib/jquery.numeric.min.js'
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
            if (empty($form['min_quote_days'])) {
                $form['min_quote_days'] = NULL;
            }
            if (empty($form['max_quote_days'])) {
                $form['max_quote_days'] = NULL;
            }
			if (empty($form['record_layout'])) {
                $form['record_layout'] = "2col.php";
            }
            //Form to save the backup by campaign settings
            $backup_form = array();
            $backup_form['months_ago'] = $form['months_ago'];
            $backup_form['months_num'] = $form['months_num'];
            unset($form['months_ago']);
            unset($form['months_num']);

			
			//Check if the minimum quote days is less than the maximum quote days
            if (($form['min_quote_days'] && $form['max_quote_days'])&&(intval($form['min_quote_days']) > intval($form['max_quote_days']))) {
                echo json_encode(array(
                    "data" => false,
                    "message" => "The minimum quote days must be less than the maximum quote days",
                    "success" => false
                ));
                exit;
            }

            //Check if the number of months is greater than the months ago
            if (($backup_form['months_ago'] && $backup_form['months_num'])&&(intval($backup_form['months_ago']) < intval($backup_form['months_num']))) {
                echo json_encode(array(
                    "data" => false,
                    "message" => "The number of months must be greater than the months ago",
                    "success" => false
                ));
                exit;
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

                $backup_form['campaign_id'] = $response;
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

                $backup_form['campaign_id'] = $form['campaign_id'];
            }

            //Save the backup by campaign settings
            if ($backup_form['months_ago'] && $backup_form['months_num']) {
                $backup_by_campaign = $this->Admin_model->get_backup_by_campaign($backup_form['campaign_id']);
                if (!empty($backup_by_campaign)) {
                    $this->Admin_model->update_backup_by_campaign($backup_form);
                }
                else {
                    $this->Admin_model->insert_backup_by_campaign($backup_form);
                }
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
                "data" => false,
                "message" => "Campaign Saved",
                "success" => true
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
			$form['role_id'] = $response;
			$this->Admin_model->update_role($form);
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
        $groups = $this->Form_model->get_all_groups();
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
    
    /* campaign fields page functions */
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
