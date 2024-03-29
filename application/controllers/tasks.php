<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tasks extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        

        $this->load->model('User_model');
        $this->load->model('Form_model');
    }
    
    public function Index()
    {
              $data     = array(
            'page' => 'tasks',
            'title' => 'Tasks',
            'javascript' => array(
                'tasks.js'
            ),
        );
        $this->template->load('default', 'tasks/list.php', $data);  
		
	}
	
}