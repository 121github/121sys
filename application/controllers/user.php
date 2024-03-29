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
        $this->load->model('Booking_model');
    }
	public function home(){
		redirect($_SESSION['home']);	
	}

	public function layout(){
		//update user layout
		$campaign = $this->input->post("campaign");
		$layout = $this->input->post("layout");
		//delete the old layout if it exists	
		$this->db->where(array("user_id"=>$_SESSION['user_id'],"campaign_id"=>$campaign));
		$this->db->delete("user_layouts");
		if($layout!=="default"){
		$this->db->insert("user_layouts",array("user_id"=>$_SESSION['user_id'],"campaign_id"=>$campaign,"layout"=>$layout));	
		}
		echo json_encode(array("success"=>true));
		
	}

    public function login()
    {

		if(isset($_SESSION['user_id'])){
			if(isset($_SESSION['home'])){
			redirect($_SESSION['home']);
			} else {
		    redirect('user/logout');
			}
		}

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|strtolower');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|md5');
 			$username = $this->input->post('username');
            if ($this->form_validation->run()) {
                if ($this->User_model->validate_login($this->input->post('username'), $this->input->post('password'))) {
                    if (isset($_SESSION['current_campaign'])) {
                        $this->set_campaign_features();
                        $this->current_campaign($_SESSION['current_campaign']);
                    }
                    $this->apply_default_filter();
                    if ($this->input->post('password') == md5("pass123") && ($_SESSION['environment'] != 'demo')) {
                        $_SESSION['flashdata']['warning']= 'Your password is insecure, please change it';
                        $redirect = "user/account";
                    } else if ($this->input->post('redirect')) {
						  $redirect = base64_decode($this->input->post('redirect'));
                    } else if(isset($_SESSION['home'])){
                        $redirect = $_SESSION['home'];
					} else {
                        $redirect = "dashboard";
                    }
                    //Write on log
                    log_message('info', '[LOGIN][ACCESS] The user '.$_SESSION['name'].' (user_id: '.$_SESSION['user_id'].', client_ip: '.$this->input->ip_address().') connected');

                    redirect($redirect);
                }

                $_SESSION['flashdata']['danger'] = 'Invalid username or password.';

                //Write on log
                log_message('info', '[LOGIN][FAIL] The user '.$username.' (client_ip: '.$this->input->ip_address().') failed. Invalid username or password.');
            }
        }


        $redirect = ($this->uri->segment(3) ? $this->uri->segment(3) : false);
        $data = array(
			'username'=>isset($username)?htmlentities($username):'',
            'pageId' => 'login',
            'pageClass' => 'login',
            'title' => '121 Calling System',
            'javascript' => array(
                'login.js'
            ),
			'css' => array("login.css"),
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

        //Write on log
        log_message('info', '[LOGIN][OUT] The user '.$_SESSION['name'].' (user_id: '.$_SESSION['user_id'].', client_ip: '.$this->input->ip_address().') has been disconected');

        session_destroy();
        redirect('user/login');
    }

    public function apply_default_filter()
    {
		//not using this at the moment
        $filter = array();
    }


    public function account()
    {
        user_auth_check(false);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->load->library('form_validation');
            //$this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');

            $this->form_validation->set_rules('current_pass', 'current password', 'trim|required|strtolower|md5');
            $this->form_validation->set_rules('new_pass', 'new password', 'trim|required|min_length[5]|matches[conf_pass]');
            $this->form_validation->set_rules('conf_pass', 'confirm password', 'trim|required|min_length[5]');


            if ($this->form_validation->run()) {

                if ($this->User_model->validate_login($_SESSION['user_id'], $this->input->post('current_pass'), true)) {
                    $response = $this->User_model->set_password($this->input->post('new_pass'));
                   $_SESSION['flashdata']['success'] = 'Password was updated';
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

        if (isset($_POST['user_id']) && in_array("admin users", $_SESSION['permissions'])) {
            $user_id = $_POST['user_id'];
        }
        else if ($this->uri->segment(3) && in_array("admin users", $_SESSION['permissions'])) {
            $user_id = intval($this->uri->segment(3));
        } else {
            $user_id = $_SESSION['user_id'];
        }

        $users = $this->Form_model->get_users();
        $roles = $this->Form_model->get_roles();
        $groups = $this->Form_model->get_groups();
        $teams = $this->Form_model->get_teams();
		$campaign_access = campaign_access_dropdown();

        $title = "My Account";

        $data = array(
            'campaign_access' => $campaign_access,
            'pageId' => 'my-account',
            'page' => 'account',
            'pageClass' => 'my-account',
            'title' => $title,
            'submenu' => array(
                "file"=>'account.php',
                "title"=>$title
            ),
            'roles' => $roles,
            'groups' => $groups,
            'teams' => $teams,
            'users' => $users,
            'user_id' => $user_id,
            'css' => array(
                'plugins/bootstrap-toggle/bootstrap-toggle.min.css',
            ),
            'javascript' => array(
                'account.js',
                'lib/jquery.numeric.min.js',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
            )
        );
        $this->template->load('default', 'user/account', $data);
    }

    public function get_users() {
        $users = $this->Form_model->get_users();

        echo json_encode(array(
            "success" => (!empty($users)),
            "data" => $users,
            "msg" => (empty($users)?"Not job winners loaded":""),
        ));
    }

    public function get_user_by_id()
    {
        if ($this->input->post()) {
            $user = $this->User_model->get_user_by_id($this->input->post("user_id"));

            //get the user access_token
            $google_token = $this->Booking_model->getGoogleToken($user[0]['user_id'],'google');
            $user[0]['google'] = (isset($google_token[0]['access_token']));

            $aux = array();
            foreach ($user as $value) {
                unset($value['password']);
                array_push($aux, $value);
            }
            $user = $aux;

            echo json_encode(array(
                "success" => (!empty($user)),
                "data" => $user,
                "session_user_id" => $_SESSION['user_id']
            ));
        }
    }

    public function get_user_addresses_by_id()
    {
        if ($this->input->post()) {
            $user = $this->User_model->get_user_addresses_by_id($this->input->post("user_id"));

            $aux = array();
            foreach ($user as $value) {
                unset($value['password']);
                array_push($aux, $value);
            }
            $user = $aux;

            echo json_encode(array(
                "success" => (!empty($user)),
                "data" => $user
            ));
        }
    }

    public function get_user_address()
    {
        if ($this->input->post()) {
            $result = $this->User_model->get_user_address($this->input->post("address_id"));

            echo json_encode(array(
                "success" => (!empty($result)),
                "data" => $result
            ));
        }
    }

    /**
     * Save contact details
     */
    public function save_contact_details()
    {
        if ($this->input->post()) {
			$this->load->helper('email');
            $form = array();
			if(!valid_email($this->input->post("email_form"))){
				echo json_encode(array("success"=>false,"msg"=>"email is not valid"));
				exit;
			}
			if(!validate_postcode($this->input->post("home_postcode"))){
				echo json_encode(array("success"=>false,"msg"=>"Postcode is not valid"));
				exit;
			}
			$postcode = postcodeFormat($this->input->post("home_postcode"));
            $form['user_email'] = ($this->input->post("email_form") ? $this->input->post("email_form") : NULL);
            $form['user_telephone'] = ($this->input->post("telephone_form") ? $this->input->post("telephone_form") : NULL);
            $form['ext'] = ($this->input->post("ext_form") ? $this->input->post("ext_form") : NULL);
			$form['home_postcode'] = ($this->input->post("home_postcode") ? $postcode : NULL);
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
		//if not logged in they should be redirected to the login page
		if(!isset($_SESSION['user_id'])){
		$_SESSION['flashalert']['warning'] = "You have been logged out";
		redirect('user/logout');
		exit;
		}
		
        if ($this->User_model->check_session()) {
            $this->User_model->load_user_session();
            $this->apply_default_filter();
            //if the user has been removed from their current campaign we should kick them out by unsetting the current campaign
			if(isset($_SESSION['current_campaign'])){
            if (@!in_array($_SESSION['current_campaign'], $_SESSION['campaign_access']['array'])) {
                unset($_SESSION['current_campaign']);
                unset($_SESSION['current_campaign_name']);
				unset($_SESSION['current_client']);
            }
			}
            $session[] = 'session reloaded';
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
			
            	$session["footer"] = $footer_stats;
        }
		$session["success"] = true;
		echo json_encode($session);
    }

    public function set_campaign_features()
    {
        $campaign_features = $this->Form_model->get_campaign_features($_SESSION['current_campaign']);
		//$this->firephp->log($campaign_features);
        $features = array();
        foreach ($campaign_features as $row) {
            $features[] = $row['name'];
        }
        $_SESSION['campaign_features'] = $features;

    }
  public function set_data($pot_id = false)
    {
	if($this->input->post('data_pot')){
	$_SESSION['global_filter']['pot'] = $this->input->post('data_pot');
	} else {
	unset($_SESSION['global_filter']['pot']);
	}
	if($this->input->post('data_source')){
	$_SESSION['global_filter']['source'] = $this->input->post('data_pot');
	} else {
	unset($_SESSION['global_filter']['source']);
	}
	if($this->input->post('outcome')){
	$_SESSION['global_filter']['outcome'] = $this->input->post('outcome');
	} else {
	unset($_SESSION['global_filter']['outcome']);
	}
	if($this->input->post('owners')){
	$_SESSION['global_filter']['owners'] = $this->input->post('owners');
	} else {
	unset($_SESSION['global_filter']['owners']);
	}
	if($this->input->post('distance')){
	$_SESSION['global_filter']['distance'] = $this->input->post('distance');
	} else {
	unset($_SESSION['global_filter']['distance']);
	}
	if($this->input->post('postcode')){
	$_SESSION['global_filter']['postcode'] = $this->input->post('postcode');
	} else {
	unset($_SESSION['global_filter']['postcode']);
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
				if(in_array("campaign override",$_SESSION['permissions'])){
                $campaign_permissions = $this->User_model->campaign_permissions($campaign);
                foreach ($campaign_permissions as $row) {
                    //a 1 indicates the permission should be added otherwize it is revoked!
                    if ($row['permission_state'] == "1") {
                        $_SESSION['permissions'][$row['permission_id']] = $row['permission_name'];
                    } else {
                        unset($_SESSION['permissions'][$row['permission_id']]);
                    }
                }
				}
                $campaign_row = $this->User_model->campaign_row($campaign);
                $_SESSION['current_client'] = addslashes($campaign_row['client_name']);
                $_SESSION['current_campaign_name'] = addslashes($campaign_row['campaign_name']);
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
			$this->firephp->log("No campaign");
            unset($_SESSION['current_client']);
            unset($_SESSION['current_campaign_name']);
            unset($_SESSION['current_campaign']);
			//unset($_SESSION['current_source']);
			//unset($_SESSION['current_pot']);
            unset($_SESSION['campaign_features']);
            unset($_SESSION['filter']['values']['campaign_id']);
            /* no longer logging in realtime
            $this->User_model->close_hours();
            */

            $filter = $_SESSION['filter']['values'];
            $this->Filter_model->apply_filter($filter);

            if (!$_SESSION['data_access']['mix_campaigns']) {
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
		$custom_fields = custom_fields();
		if(in_array($field,$custom_fields)){
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
                    "msg" => "Username does not exist"
                ));
            } else {
                $user = $user[0];
                if (!$user['user_email']) {
                    echo json_encode(array(
                        "success" => false,
                        "reason" => "email_address",
                        "msg" => "There is no email address linked to your account. Please contact support"
                    ));
                } else {
                    //Set the TOKEN for this user in order to generate the link to reset the password
                    $reset_pass_token = $this->set_reset_pass_token($user);
                    $user['reset_pass_token'] = $reset_pass_token;

                    //Send email
                    $send_email = $this->send_reset_password_email($user);

                    if ($send_email) {
                        $_SESSION['flashdata']['success'] = 'An email containing password reset instructions was sent to ' . htmlentities($user['user_email']);
                    }

                    echo json_encode(array(
                        "success" => ($send_email),
                        "msg" => ($send_email ? "Password reset email was sent" : "Password reset email could not be sent"),
						"username"=>$username
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
        $subject = "Password reset";
        $body = "Hi " . $user['name'] . ",
            Please click on the link below to reset your password: " . $reset_pass_url . "";

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
           $_SESSION['flashdata']['error'] = 'This password reset request has already been used. Please submit another password reset request';
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
                     $_SESSION['flashdata']['success']  ='Password restored successfully!';
                }

                echo json_encode(array(
                    "success" => ($restored),
                    "msg" => ($restored ? "The password was restored successfully" : "ERROR: The password was not restored successfully! Please contact with the Administrator"),
					"username"=>$username
                ));
            } else {
                echo json_encode(array(
                    "success" => false,
                    "msg" => validation_errors(),
					"username"=>$username
                ));
            }
        }
    }

    /**
     * Open address user modal
     */
    public function add_user_address()
    {
        if ($this->input->is_ajax_request()) {
            $user_id = intval($this->input->post('user_id'));
            $address_id = intval($this->input->post('address_id'));
            $this->load->view('forms/edit_user_address_form.php', array("user_id" => $user_id, "address_id" => $address_id));
        }
    }

    /**
     * Save address user
     */
    public function save_address_user()
    {
        if ($this->input->post()) {
            $form = $this->input->post();
            $this->load->helper('email');

            unset($form['house-number']);

            if(!validate_postcode($form["postcode"])){
                echo json_encode(array("success"=>false,"msg"=>"Postcode is not valid"));
                exit;
            }

            if(!($form["description"])){
                echo json_encode(array("success"=>false,"msg"=>"You need to add the description"));
                exit;
            }

            if ($form['primary'] == 1) {
                //Remove primary for the others
                $this->User_model->set_no_primary_user_address($form['user_id']);

                //Add as default user postcode
                $this->User_model->update_user($form['user_id'], array('home_postcode' => $form['postcode']));
            }

            if ($form['address_id']) {
                //Save user address
                $results = $this->User_model->update_user_address($form);
            }
            else {
                //Save user address
                $results = $this->User_model->save_user_address($form);
            }

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results ? "User address saved successfully" : "ERROR: The User address was not saved successfully!")
            ));
        }
    }

    /**
     * Delete address user
     */
    public function delete_address_user()
    {
        if ($this->input->post()) {
            $user_id = $this->input->post("user_id");
            $address_id = $this->input->post("address_id");
            $primary = $this->input->post("primary");

            //Delete user address
            $results = $this->User_model->delete_user_address($address_id);

            if ($primary == 1) {
                //Remove the default user postcode
                $this->User_model->update_user($user_id, array('home_postcode' => NULL ));
            }

            echo json_encode(array(
                "success" => ($results),
                "msg" => ($results ? "User address saved successfully" : "ERROR: The User address was not saved successfully!")
            ));
        }
    }
}