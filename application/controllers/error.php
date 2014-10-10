<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Error extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();

    }
	
	public function access(){
		        $data = array(
'pageId' => 'list-records',
            'title' => 'Access Error');
			 $this->template->load('default', 'errors/access.php', $data);
		
	}
	
}