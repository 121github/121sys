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

    public function access()
    {
        $title = "Access - Permission Denied";

        $data = array(
            'page' => 'error-page',
            'campaign_access' => $this->_campaigns,
            'title' => $title,
            'submenu' => array(
                "file" => 'default_submenu.php',
                "title" => $title,
                "hide_filter" => true
            ),
            'msg' => 'Please contact your system administrator if you believe you should have access to this record');
        $this->template->load('default', 'errors/display.php', $data);

    }

    public function files()
    {
        $title = "Files - Permission Denied";

        $data = array(
            'page' => 'error-page',
            'campaign_access' => $this->_campaigns,
            'title' => $title,
            'submenu' => array(
                "file" => 'default_submenu.php',
                "title" => $title,
                "hide_filter" => true
            ),
            'msg' => 'You do not have access to these files. Please contact the administrator if you believe you should have access');
        $this->template->load('default', 'errors/display.php', $data);
    }

    public function folder()
    {
        $title = "Folder - Permission Denied";

        $data = array(
            'page' => 'error-page',
            'campaign_access' => $this->_campaigns,
            'title' => $title,
            'submenu' => array(
                "file" => 'default_submenu.php',
                "title" => $title,
                "hide_filter" => true
            ),
            'msg' => 'You have not been granted access to any folders. Please contact the administrator if you require this feature');
        $this->template->load('default', 'errors/display.php', $data);
    }

    public function ownership()
    {
        $title = "Ownership - Permission Denied";

        $data = array(
            'page' => 'error-page',
            'campaign_access' => $this->_campaigns,
            'title' => $title,
            'submenu' => array(
                "file" => 'default_submenu.php',
                "title" => $title,
                "hide_filter" => true
            ),
            'msg' => 'This record is assigned to another user. If you believe you should have access to it, please contact the system administrator');
        $this->template->load('default', 'errors/display.php', $data);

    }

    public function data()
    {
        $title = "Data error";

        $data = array(
            'page' => 'error-page',
            'campaign_access' => $this->_campaigns,
            'title' => $title,
            'submenu' => array(
                "file" => 'default_submenu.php',
                "title" => $title,
                "hide_filter" => true
            ),
            'msg' => 'There are no records left for calling. Please try another campaign or change your filter options');
        $this->template->load('default', 'errors/display.php', $data);

    }

    public function campaign()
    {
        $title = "No campaign selected";

        $data = array(
            'page' => 'error-page',
            'campaign_access' => $this->_campaigns,
            'title' => $title,
            'submenu' => array(
                "file" => 'default_submenu.php',
                "title" => $title,
                "hide_filter" => true
            ),
            'msg' => 'Please select a campaign from the drop down menu above before you start calling');
        $this->template->load('default', 'errors/display.php', $data);
    }

    public function calendar()
    {
        $title = "No campaign selected";

        $data = array(
            'pageId' => 'error-page',
            'campaign_access' => $this->_campaigns,
            'title' => $title,
            'submenu' => array(
                "file" => 'default_submenu.php',
                "title" => $title,
                "hide_filter" => true
            ),
            'msg' => 'Please select a campaign from the drop down menu above to view the associated calendar');
        $this->template->load('default', 'errors/display.php', $data);

    }

}