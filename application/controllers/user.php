<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }
    
    public function login()
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|strtolower');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|strtolower|md5');
            
            if ($this->form_validation->run()) {
                
                if ($this->User_model->validate_login($this->input->post('username'), $this->input->post('password'))) {
                    
                    $redirect = $this->input->post('redirect');
                    if ($redirect) {
                        redirect(base_url().base64_decode($redirect));
                    } else {
                        redirect('records/view/1');
                    }
                }
                $this->session->set_flashdata('error', 'Invalid username or password.');
                redirect('user/login'); //Need to redirect to show the flash error.
            }
        }
        
        session_destroy();
        
        $redirect = ($this->uri->segment(3) ? $this->uri->segment(3) : false);
        $data = array(
            'pageId' => 'login',
            'pageClass' => 'login',
            'title' => 'NPS Login',
            'redirect' => $redirect
        );
        $this->template->load('default', 'user/login', $data);
    }
    
    public function logout()
    {
        redirect('user/login');
    }
    
    public function account()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $this->load->library('form_validation');
            //$this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');
            
            $this->form_validation->set_rules('current_pass', 'current password', 'trim|required|strtolower|md5');
            $this->form_validation->set_rules('new_pass', 'new password', 'trim|required|strtolower|min_length[5]|matches[conf_pass]');
            $this->form_validation->set_rules('conf_pass', 'confirm password', 'trim|required|strtolower|min_length[5]');
            
            
            
            if ($this->form_validation->run()) {
                
                if ($this->User_model->validate_login($_SESSION['user_id'], $this->input->post('current_pass'), true)) {
                    $response = $this->User_model->set_password($this->input->post('new_pass'));
                    echo json_encode(array(
                        "success" => true,
                        "msg" => 'Password was updated'
                    ));
                    exit;
                } else {
                    echo json_encode(array(
                        "msg" => 'Current password was incorrect'
                    ));
                    exit;
                }
                
            }
            echo json_encode(array(
                "msg" => validation_errors()
            ));
            exit;
        }
        
        $data = array(
            'pageId' => 'my-account',
            'pageClass' => 'my-account',
            'title' => 'My Account'
        );
        $this->template->load('default', 'user/account', $data);
    }
    
    
    public function index()
    {
        //redirect('user/account');
    }
    
    
}