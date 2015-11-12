<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Orders_admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->project_version = $this->config->item('project_version');
    }

    public function index()
    {
		echo "boo";
	}
	
	
	
	
}