<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Orders extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
		$this->_campaigns = campaign_access_dropdown();
        $this->project_version = $this->config->item('project_version');

        $this->load->model('Form_model');
        $this->load->model('User_model');
    }

    /* hours page functions */
    public function create()
    {
		$categories = $this->
        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Admin',
            'title' => 'Admin',
            'page' => 'copy_campaign',
            'css' => array(
                'dashboard.css'
            ),
            'javascript' => array(
                'admin/copy_campaign.js?v' . $this->project_version
            ),
            'options' => array("campaigns" => $campaigns)

        );
        $this->template->load('default', 'admin/copy_campaign.php', $data);
	}
	
}