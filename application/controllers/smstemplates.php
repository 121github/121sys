<?php
require('upload.php');

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Smstemplates extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
        $this->_campaigns = campaign_access_dropdown();
        $this->load->model('Sms_model');
        $this->load->model('Form_model');

    }

    /**
     * This is the controller loads the initial view for the templates
     */
    public function index()
    {
        $campaigns = $this->Form_model->get_user_campaigns();
        $sms_senders = $this->Form_model->get_sms_senders();

        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'Dashboard',
            'title' => 'Admin | SMS Templates',
            'page' => 'smstemplates',
            'css' => array(
                'dashboard.css',
                'plugins/fontAwesome/css/font-awesome.css'
            ),

            'javascript' => array(
                'sms-templates.js'
            ),
            'campaigns' => $campaigns,
            'sms_senders' => $sms_senders,
        );

        $this->template->load('default', 'sms/template.php', $data);
    }

    /**
     * Get the templates
     */
    public function all_template_data()
    {
        if ($this->input->is_ajax_request()) {
            $templateList = $this->Sms_model->get_templates();
            echo json_encode(array(
                "success" => true,
                "data" => $templateList,
                "msg" => "Nothing found"
            ));
            exit;
        }
    }

    public function template_data()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            $template = $this->Sms_model->get_template($id);
            echo json_encode(array(
                "success" => true,
                "data" => $template,
                "msg" => "Nothing found"
            ));
            exit;
        }
    }

    /**
     * Get the campaigns for a template
     */
    public function get_campaings_by_template_id()
    {
        $template_id = $this->input->post('id');

        $campaignList = $this->Sms_model->get_campaigns_by_template_id($template_id);

        $auxList = array();
        foreach ($campaignList as $campaign) {
            array_push($auxList, $campaign["campaign_id"]);
        }
        $campaignList = $auxList;

        echo json_encode(array(
            "success" => true,
            "data" => $campaignList,
            "msg" => "Nothing found"
        ));
        exit;
    }

    /**
     * Insert or update a template
     */
    public function save_template()
    {
        $form = $this->input->post();
        $form['template_text'] = $this->input->post('template_text');


        //Check if the user selected any campaign for this template
        $campaignsForm = array();
        if (isset($form['campaign_id'])) {
            $campaignsForm = $form['campaign_id'];
            unset($form['campaign_id']);
        }

        //Insert the new template
        if (empty($form['template_id'])) {
            $insert_id = $this->Sms_model->add_new_template($form);
            $response = ($insert_id) ? true : false;
        } else {
            //Update the template
            $response = $this->Sms_model->update_template($form);
            if ($response) {
                //Save the campaigns
                $insert_id = $form['template_id'];
            }
        }

        if ($response && isset($campaignsForm)) {
            //Save the campaigns
            $response = $this->save_campaign_by_template($campaignsForm, $insert_id);
        }

        echo json_encode(array("success" => true, "data" => $response));

    }

    /**
     * Save campaigns for a particular template
     *
     * @param unknown $campaign_list
     * @param unknown $template_id
     * @return boolean
     */
    public function save_campaign_by_template($campaign_list, $template_id)
    {

        $response_delete = true;
        $response_insert = true;

        //Get the old campaigns
        $old_campaigns = $this->Sms_model->get_campaigns_by_template_id($template_id);
        $aux = array();
        foreach ($old_campaigns as $old_campaign) {
            array_push($aux, $old_campaign['campaign_id']);
        }
        $old_campaigns = $aux;

        //Campaings to remove from mail_template_to_campaigns
        $delete_campaigns = array_intersect($old_campaigns, array_diff($old_campaigns, $campaign_list));
        if (!empty($delete_campaigns)) {
            $response_delete = $this->Sms_model->delete_campaigns_by_template_id($template_id, $delete_campaigns);
        }


        //Campaings to insert in mail_template_to_campaigns
        $insert_campaigns = array_intersect($campaign_list, array_diff($campaign_list, $old_campaigns));
        if (!empty($insert_campaigns)) {
            $response_insert = $this->Sms_model->insert_campaigns_by_template_id($template_id, $insert_campaigns);
        }

        return ($response_delete && $response_insert);
    }

    /**
     * Delete a template
     */
    public function delete_template()
    {
        $template_id = intval($this->input->post('id'));
        //Get the path images from the editor in order to delete the files after the template is removed
        $template = $this->Sms_model->get_template($template_id);
        $image_list = $template['template_text'];


        $response = $this->Sms_model->delete_template($template_id);
        if ($response) {
            echo json_encode(array("success" => true, "data" => $response));
        } else {
            echo json_encode(array("success" => false));
        }

    }

}