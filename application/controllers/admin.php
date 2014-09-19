<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Admin_model');
    }
    //this controller loads the view for the user page
    public function users()
    {
        $options['roles']  = $this->Form_model->get_roles();
        $options['groups'] = $this->Form_model->get_groups();
        $data              = array(
            'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'users'
            ),
            'options' => $options,
            'javascript' => array(
                'admin.js'
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
            'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'campaign'
            ),
            'javascript' => array(
                'dashboard.js',
                'admin.js'
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
            if (!empty($form['new_client']) && $form['client_id'] == "other") {
                $client_id         = $this->Admin_model->add_client($form['new_client']);
                $form['client_id'] = $client_id;
            }
            unset($form['new_client']);
            //if it's set as B2B then we add the company feature to the campaign
            if ($form['campaign_type_id'] == "2") {
                $form['features'][] = 2;
            }
            //all campaigns need the contact and update panel at a minimum
            $form['features'][] = 1;
            $form['features'][] = 3;
            $response           = $this->Admin_model->save_campaign_features($form);
            unset($form['features']);
            if (empty($form['campaign_id'])) {
                $response = $this->Admin_model->add_new_campaign($form);
            } else {
                $response = $this->Admin_model->update_campaign($form);
            }
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
            'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'roles'
            ),
            'javascript' => array(
                'admin.js'
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
            $response = $this->Admin_model->update_role($form);
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
        $groups = $this->Admin_model->get_groups();
        $data   = array(
            'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => array(
                'admin' => 'groups'
            ),
            'javascript' => array(
                'admin.js'
            ),
            'groups' => $groups,
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
    public function save_user()
    {
        $form = $this->input->post();
        if (empty($form['user_id'])) {
            $response = $this->Admin_model->add_new_user($form);
        } else {
            $response = $this->Admin_model->update_user($form);
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
}
