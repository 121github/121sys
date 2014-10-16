<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Form_model');
		$this->load->model('Cron_model');
    }
    
    public function update_hours()
    {
        $agents = $this->Form_model->get_agents();
        $this->Cron_model->update_hours($agents);
        
    }
	
	    public function clear_hours()
    {
        $this->Cron_model->clear_hours();
    }
    
}