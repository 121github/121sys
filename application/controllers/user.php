<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
    }

    public function login()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|strtolower');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|md5');

            if ($this->form_validation->run()) {
                $username = $this->input->post('username');
                if ($this->User_model->validate_login($this->input->post('username'), $this->input->post('password'))) {
                    if (isset($_SESSION['current_campaign'])) {
                        $this->set_campaign_features();
                        $this->current_campaign($_SESSION['current_campaign']);
                    }
                    $this->apply_default_filter();
                    if ($this->input->post('password') == md5("pass123")) {
                        $this->session->set_flashdata('change_pass', '1');
                        $redirect = base64_encode("user/account");
                    } else if (in_array("files only", $_SESSION['permissions'])) {
                        redirect('files/manager');
                    } else {
                        $redirect = $this->input->post('redirect');
                    }
                    if (!empty($redirect)) {
                        redirect(base64_decode($redirect));
                    } else {
                        if ($_SESSION['role'] == 5 || $_SESSION['role'] == 3) {
                            redirect('dashboard/agent');
                        }
                        if ($_SESSION['role'] == 2) {
                            redirect('dashboard/management');
                        }
                        if ($_SESSION['role'] == 4) {
                            redirect('dashboard');
                        } else if ($_SESSION['role'] == 9) {
                            redirect('records/detail/0');
                        } else {
                            redirect('dashboard');
                        }
                    }
                }
                $this->session->set_flashdata('error', 'Invalid username or password.');
                $this->session->set_flashdata('username', $username);
                redirect('user/login'); //Need to redirect to show the flash error.
            }
        }
		if(isset($_SESSION['user_id'])){
		$this->User_model->log_logout($_SESSION['user_id']);
		}
        session_destroy();

        $redirect = ($this->uri->segment(3) ? $this->uri->segment(3) : false);
        $data = array(
            'pageId' => 'login',
            'pageClass' => 'login',
            'title' => '121 Calling System',
            'javascript' => array(
                'login.js'
            ),
            'redirect' => $redirect
        );
        $this->template->load('default', 'user/login', $data);
    }

    public function logout()
    {
        if (in_array("log hours", $_SESSION['permissions'])) {
            $this->User_model->close_hours();
        }
		$this->User_model->log_logout($_SESSION['user_id']);
        session_destroy();
        redirect('user/login');
    }

    public function apply_default_filter()
    {
        $filter = array();
        /*set the default filter options up accounding to the "Default view" permissions */
        if (@in_array("view live", $_SESSION['permissions'])) {
            $filter['record_status'][] = 1;
        }
        if (@in_array("view pending", $_SESSION['permissions'])) {
            $filter['record_status'][] = 2;
        }
        if (@in_array("view dead", $_SESSION['permissions'])) {
            $filter['record_status'][] = 3;
        }
        if (@in_array("view completed", $_SESSION['permissions'])) {
            $filter['record_status'][] = 4;
        }

        /*if the default is to view all parked record we just add all the parked codes to the filter (as long as there are less than 10 this will do the trick) */
        if (@in_array("view parked", $_SESSION['permissions'])) {
            $filter['view_parked'] = true;
        }

        if (in_array("view unassigned", $_SESSION['permissions'])) {
            $filter['view_unassigned'] = true; //this allows to search for null value in ownership table
        }
        if (in_array("view own records", $_SESSION['permissions'])) {
            $filter['user_id'][] = $_SESSION['user_id'];
        }

        if (!isset($_SESSION['filter']['values']['group_id'])) {
            if (in_array("view own group", $_SESSION['permissions']) && !empty($_SESSION['group'])) {
                $filter['group_id'][] = $_SESSION['group'];
            }
        }
        if (!isset($_SESSION['filter']['values']['team_id'])) {
            if (in_array("view own team", $_SESSION['permissions']) && !empty($_SESSION['team'])) {
                $filter['team_id'][] = $_SESSION['team'];
            }

            if (isset($_SESSION['current_campaign'])) {
                $filter['campaign_id'][] = $_SESSION['current_campaign'];
            }
            //we can add any additional filter requirements here if we ever need to
            $where = "";
            $_SESSION['filter']['where'] = $where;
        }

        $this->Filter_model->apply_filter($filter);

    }


    public function account()
    {
        user_auth_check(false);
        $campaign_access = campaign_access_dropdown();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->load->library('form_validation');
            //$this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');

            $this->form_validation->set_rules('current_pass', 'current password', 'trim|required|strtolower|md5');
            $this->form_validation->set_rules('new_pass', 'new password', 'trim|required|min_length[5]|matches[conf_pass]');
            $this->form_validation->set_rules('conf_pass', 'confirm password', 'trim|required|min_length[5]');


            if ($this->form_validation->run()) {

                if ($this->User_model->validate_login($_SESSION['user_id'], $this->input->post('current_pass'), true)) {
                    $response = $this->User_model->set_password($this->input->post('new_pass'));
                    $this->session->set_flashdata('success', 'Password was updated');
                    echo 'Logout';
                    exit;
                } else {
                    echo json_encode(array(
                        "msg" => 'Current password was incorrect'
                    ));
                    exit;
                }

            } else {
                echo json_encode(array(
                    "msg" => validation_errors()
                ));
                exit;
            }

        }

        $user_id = $_SESSION['user_id'];

        $users = $this->Form_model->get_users();
        $roles = $this->Form_model->get_roles();
        $groups = $this->Form_model->get_groups();
        $teams = $this->Form_model->get_teams();

        $data = array(
            'campaign_access' => $campaign_access,
            'pageId' => 'my-account',
            'page' => array('records' => 'account'),
            'pageClass' => 'my-account',
            'title' => 'My Account',
            'roles' => $roles,
            'groups' => $groups,
            'teams' => $teams,
            'users' => $users,
            'user_id' => $user_id,
            'javascript' => array(
                'account.js',
                'lib/jquery.numeric.min.js',
            ),
        );
        $this->template->load('default', 'user/account', $data);
    }

    public function get_user_by_id()
    {
        if ($this->input->post()) {
            $user = $this->User_model->get_user_by_id($this->input->post("user_id"));

            echo json_encode(array(
                "success" => (!empty($user)),
                "data" => $user
            ));
        }
    }

    /**
     * Save contact details
     */
    public function save_contact_details()
    {
        if ($this->input->post()) {
            $form = array();
            $form['user_email'] = ($this->input->post("email_form") ? $this->input->post("email_form") : NULL);
            $form['user_telephone'] = ($this->input->post("telephone_form") ? $this->input->post("telephone_form") : NULL);
            $form['ext'] = ($this->input->post("ext_form") ? $this->input->post("ext_form") : NULL);
            $user_id = $this->input->post("user_id");


            //Update the contact details
            $results = $this->User_model->update_user($user_id, $form);

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results ? "Contact details saved successfully" : "ERROR: The Contact details was not saved successfully!")
            ));
        }
    }

    /*
     * Rsest failed login to 0
     */
    public function reset_failed_logins()
    {
        if ($this->input->post()) {

            $user_id = $this->input->post('user_id');

            $form['failed_logins'] = 0;

            //Update the user
            $results = $this->User_model->update_user($user_id, $form);

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results ? "Failed logins reset to 0 successfully" : "ERROR: The Failed logins was not updated successfully!")
            ));
        }
    }

    /* at the bottom of default.php template: this function is ran every time a page is loaded and it checks whether user permissions/access have been changed or not so they can be reapplied without needing to log out */
    public function check_session()
    {
        if ($this->User_model->check_session()) {
            $this->User_model->load_user_session();
            $this->apply_default_filter();
            //if the user has been removed from their current campaign we should kick them out by unsetting the current campaign
            if (@!in_array($_SESSION['current_campaign'], $_SESSION['campaign_access']['array'])) {
                unset($_SESSION['current_campaign']);
                unset($_SESSION['current_campaign_name']);
            }
            echo "Session reloaded";
        }
        //no longer showing footer 
        if (in_array("show footer", $_SESSION['permissions']) && isset($_SESSION['current_campaign'])) {
            $user_id = $_SESSION['user_id'];
            $campaign = $_SESSION['current_campaign'];

            $this->load->model('Records_model');
            // $duration = $this->User_model->get_duration($campaign,$user_id);
            // $rate = number_format(($transfers + $cross_transfers)/($duration/60/60),2);
            $positive_outcomes = $this->Records_model->get_positive_for_footer($campaign);


            $worked = $this->User_model->get_worked($campaign, $user_id);
            $positives = $this->User_model->get_positives($campaign, $user_id);


            $cross_transfers = $this->User_model->get_cross_transfers_by_campaign_destination($campaign, $user_id);
            $footer_stats["Records Worked"] = $worked;
            foreach ($positives as $row) {
                if ($row['outcome'] == "Transfer") {
                    $row['count'] = $row['count'] + $cross_transfers;
                }
                $footer_stats[$row['outcome']] = $row['count'];
            }
            echo json_encode($footer_stats);

        }
    }

    public function set_campaign_features()
    {
        $campaign_features = $this->Form_model->get_campaign_features($_SESSION['current_campaign']);
        $features = array();
        foreach ($campaign_features as $row) {
            $features[] = $row['name'];
        }
        $_SESSION['campaign_features'] = $features;

    }
  public function set_data($pot_id = false)
    {
	if(intval($this->input->post('data_pot'))>0){
	$_SESSION['current_pot'] = 	$this->input->post('data_pot');
	} else {
	unset($_SESSION['current_pot']);
	}
	if(intval($this->input->post('data_source'))>0){
	$_SESSION['current_source'] = 	$this->input->post('data_source');
	} else {
	unset($_SESSION['current_source']);
	}
	}
	
    /* at the bottom of default.php template: when the campaign drop down is changed we set the new campaign in the session so we can filter all the records easily */
    public function current_campaign($camp_id = false)
    {
			
        $campaign = ($camp_id ?  $camp_id:intval($this->uri->segment(3)));
        $user_id = $_SESSION['user_id'];
        unset($_SESSION['next']);
        if ($campaign > "0") {
            unset($_SESSION['filter']);
            if (in_array($campaign, $_SESSION['campaign_access']['array'])) {
				
                // no longer logging in realtime  
                if(in_array("log hours",$_SESSION['permissions'])){
                //start logging the duration on the selected campaign
                $this->User_model->update_hours_log($campaign,$user_id);
                }
                //reset the permissions
                $this->User_model->set_permissions();
                //this function lets you add and remove permissions based on the selected campaign rather than user role! :)

                $campaign_permissions = $this->User_model->campaign_permissions($campaign);
                foreach ($campaign_permissions as $row) {
                    //a 1 indicates the permission should be added otherwize it is revoked!
                    if ($row['permission_state'] == "1") {
                        $_SESSION['permissions'][$row['permission_id']] = $row['permission_name'];
                    } else {
                        unset($_SESSION['permissions'][$row['permission_id']]);
                    }
                }

                $campaign_row = $this->User_model->campaign_row($campaign);
                $_SESSION['current_client'] = $campaign_row['client_name'];
                $_SESSION['current_campaign_name'] = $campaign_row['campaign_name'];
                $_SESSION['current_campaign'] = $campaign_row['campaign_id'];
				$_SESSION['timeout'] = $campaign_row['timeout'];
				//this is to set the order of virgin records in the start calling pot
				if(!empty($campaign_row['virgin_order_string'])){
				$_SESSION['custom_joins'] = $campaign_row['virgin_order_join'];
				$_SESSION['custom_order'] = $campaign_row['virgin_order_string'];
				} else {
				if(!empty($campaign_row['virgin_order_1'])){
				$_SESSION['custom_joins'] = $this->get_join($campaign_row['virgin_order_1']);
				$_SESSION['custom_order']= $campaign_row['virgin_order_1'];
				}
				if(!empty($campaign_row['virgin_order_2'])&&!empty($campaign_row['virgin_order_1'])){
				$_SESSION['custom_joins'] .= $this->get_join($campaign_row['virgin_order_2']);
				$_SESSION['custom_order'] .= ",".$campaign_row['virgin_order_1'];
				}
				}
				
                $this->set_campaign_features();
                $this->apply_default_filter();
            }
        } else {
            unset($_SESSION['current_client']);
            unset($_SESSION['current_campaign_name']);
            unset($_SESSION['current_campaign']);
            unset($_SESSION['campaign_features']);
            unset($_SESSION['filter']['values']['campaign_id']);
            /* no longer logging in realtime
            $this->User_model->close_hours();
            */

            $filter = $_SESSION['filter']['values'];
            $this->Filter_model->apply_filter($filter);

            if (@!in_array('mix campaigns', $_SESSION['permissions'])) {
                echo json_encode(array(
                    "location" => 'dashboard'
                ));
                return;
            }
        }
        echo json_encode(array());
    }

	public function get_join($field){
		$joins="";
		if($field == "employees" || $field == "turnover"){
		$joins .= " left join companies using(urn) ";	
		}
		if($field == "client_ref"){
		$joins .= " left join client_refs using(urn) ";	
		}
		if($field == "c1" || $field == "c2" || $field == "c3" || $field == "c4" || $field == "c5" || $field == "c6" || $field == "d1" || $field == "d2" || $field == "dt1" || $field == "dt2" || $field == "n1" || $field == "n2" || $field == "n3"){
		$joins .= " left join record_details using(urn) ";	
		}
		if($field == "distance"){
		$joins .= " ";	
		}
		return $joins;
	}

    public function index()
    {
        //redirect('user/account');
    }

    //Send an email with the instructions to reset a password
    public function send_email_reset_password()
    {
        if ($this->input->is_ajax_request()) {

            $form = $this->input->post();
            $username = $form['username'];

            //Get the user by the username
            $user = $this->User_model->get_user_by_username($username);

            if (!$user) {
                echo json_encode(array(
                    "success" => false,
                    "reason" => "user",
                    "msg" => "The username does not exist!"
                ));
            } else {
                $user = $user[0];
                if (!$user['user_email']) {
                    echo json_encode(array(
                        "success" => false,
                        "reason" => "email_address",
                        "msg" => "You don't have an email address saved! Please, contact with the Administrator"
                    ));
                } else {
                    //Set the TOKEN for this user in order to generate the link to reset the password
                    $reset_pass_token = $this->set_reset_pass_token($user);
                    $user['reset_pass_token'] = $reset_pass_token;

                    //Send email
                    $send_email = $this->send_reset_password_email($user);

                    if ($send_email) {
                        $this->session->set_flashdata('success', 'An email was sent successfully to ' . $user['user_email'] . '. You will receive it in your mailbox soon with the instructions to reset the password');
                        $this->session->set_flashdata('username', $username);
                    }

                    echo json_encode(array(
                        "success" => ($send_email),
                        "msg" => ($send_email ? "The email was sent successfully. You will receive it in your mailbox soon" : "ERROR: The email was not sent successfully! Please contact with the Administrator")
                    ));
                }
            }
        }
    }

    //Set the TOKEN for this user in order to generate the link to reset the password
    private function set_reset_pass_token($user)
    {
        $user_id = $user['user_id'];
        unset($user['user_id']);
        $user['reset_pass_token'] = md5(uniqid(mt_rand(), true));;
        $results = $this->User_model->update_user($user_id, $user);

        return $user['reset_pass_token'];
    }

    private function send_reset_password_email($user)
    {

        //Send the email

        $reset_pass_url = base_url() . "user/restore_password/" . $user['reset_pass_token'];
        $email_address_from = "no-reply@121system.com";
        $email_address_to = $user['user_email'];
        $subject = "121System - Restore your Password";
        $body = "Hi " . $user['name'] . ",
            Please, click on the next link if you want to restore your password: " . $reset_pass_url . "";

        $this->load->library('email');

        $config = $this->config->item('email');

        $this->email->initialize($config);

        $this->email->from($email_address_from);
        $this->email->to($email_address_to);
        $this->email->subject($subject);
        $this->email->message($body);

        $result = $this->email->send();
        $this->email->clear();

        return $result;
    }

    //Restore the password
    public function restore_password()
    {
        $reset_pass_token = ($this->uri->segment(3) ? $this->uri->segment(3) : false);

        //Get the user with this reset_pass_token
        $user = $this->User_model->get_user_by_reset_pass_token($reset_pass_token);

        //If the user replaced the password with this token before, redirect to login page
        if (empty($user)) {
            $this->session->set_flashdata('error', 'The password was replaced before from this link. If you need to restore the password again, please, click on forgot password and we will send you an email again');
            redirect('user/login');
        } else {
            $user = $user[0];

            $data = array(
                'pageId' => 'login',
                'pageClass' => 'login',
                'title' => '121 Calling System',
                'javascript' => array(
                    'login.js'
                ),
                'user_id' => $user['user_id'],
                'username' => $user['username']
            );
            $this->template->load('default', 'user/restore_password', $data);
        }
    }

    //Save the restored the password
    public function save_restored_password()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $form = $this->input->post();
            $user_id = $form['user_id'];
            $username = $form['username'];
            unset($form['user_id']);
            unset($form['username']);

            $this->load->library('form_validation');

            //$this->form_validation->set_rules('current_pass', 'current password', 'trim|required|strtolower|md5');
            $this->form_validation->set_rules('new_pass', 'new password', 'trim|required|strtolower|min_length[5]|matches[conf_pass]');
            $this->form_validation->set_rules('conf_pass', 'confirm password', 'trim|required|strtolower|min_length[5]');

            if ($this->form_validation->run()) {

                $restored = $this->User_model->restore_password($user_id, $form['new_pass']);

                if ($restored) {
                    $this->session->set_flashdata('success', 'Password restored successfully!');
                    $this->session->set_flashdata('username', $username);
                }

                echo json_encode(array(
                    "success" => ($restored),
                    "msg" => ($restored ? "The password was restored successfully" : "ERROR: The password was not restored successfully! Please contact with the Administrator")
                ));
            } else {
                echo json_encode(array(
                    "success" => false,
                    "msg" => validation_errors()
                ));
            }
        }
    }
}