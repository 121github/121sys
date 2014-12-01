<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Error extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
		$this->_campaigns = campaign_access_dropdown();
    }
	
	public function access(){
		        $data = array(
			'pageId' => 'error-page',
 			'campaign_access' => $this->_campaigns,
            'title' => 'Permission Denied',
			'msg'=>'Please contact your system administrator if you believe you should have access to this record');
			 $this->template->load('default', 'errors/display.php', $data);
		
	}
	
		public function data(){
		        $data = array(
			'pageId' => 'error-page',
			'campaign_access' => $this->_campaigns,
            'title' => 'Data error',
			'msg'=>'There are no records left for calling. Please try another campaign or contact your administrator');
			 $this->template->load('default', 'errors/display.php', $data);
		
	}
	
			public function campaign(){
		        $data = array(
			'pageId' => 'error-page',
			'campaign_access' => $this->_campaigns,
            'title' => 'No campaign selected',
			'msg'=>'Please select a campaign from the drop down menu above before you start calling');
			 $this->template->load('default', 'errors/display.php', $data);
		
	}
	
}