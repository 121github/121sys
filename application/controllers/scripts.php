<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Scripts extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		user_auth_check(false);
$this->_campaigns = campaign_access_dropdown();

		$this->load->model('Script_model');
		$this->load->model('Form_model');
		
	}
	
	/**
	 * This is the controller loads the initial view for the scripts
	 */
	public function index()
	{
		$campaigns = $this->Form_model->get_user_campaigns();
		
		$data = array(
				'campaign_access' => $this->_campaigns,
'campaign_pots' => $this->_pots,
'pageId' => 'Admin',
				'title' => 'Admin',
				'page'=> 'scripts',
				'css' => array(
						'dashboard.css',
				),
	
				'javascript' => array(
						'script.js',
				),
				'campaigns' => $campaigns,
		);
	
		$this->template->load('default', 'script/script.php', $data);
	}
	
	/**
	 * Get the scripts
	 */
	public function script_data()
	{
		if ($this->input->is_ajax_request()) {
			$scriptList = $this->Script_model->get_scripts();
			echo json_encode(array(
					"success" => true,
					"data" => $scriptList,
					"msg" => "Nothing found"
			));
			exit;
		}
	}
	
	/**
	 * Get the campaigns for a script
	 */
	public function get_campaings_by_script_id() {
		$script_id = $this->input->post('id');
		
		$campaignList = $this->Script_model->get_campaigns_by_script_id($script_id);
		$auxList = array();
		foreach ($campaignList as $campaign) {
			array_push($auxList, $campaign["campaign_id"]);
		}
		$campaignList = $auxList;
		
		echo json_encode(array(
				"success" => true,
				"data" => $campaignList
		));
		exit;
	}
	
	/**
	 * Insert or update a script
	 */
	public function save_script()
	{
		$form = $this->input->post();

		$form['expandable'] = (isset($form['expandable']))?1:0;
		
		//Check if the user selected any campaign for this script
		$campaignsForm = array();
		if (isset($form['campaign_id'])) {
			$campaignsForm = $form['campaign_id'];
			unset($form['campaign_id']);
		}
	
		//Insert the new script
		if (empty($form['script_id'])) {
			$insert_id = $this->Script_model->add_new_script($form);
			$response = ($insert_id)?true:false;
		} else {
			//Update the script
			$response = $this->Script_model->update_script($form);
			if ($response) {
				//Save the campaigns
				$insert_id =  $form['script_id'];
			}
		}
	
		if ($response && isset($campaignsForm)) {
			//Save the campaigns
			$response = $this->save_campaign_by_script($campaignsForm, $insert_id);
		}
	
	
		echo json_encode(array("success"=>true,"data"=>$response));
	
	}
	
	/**
	 * Save campaigns for a particular template
	 *
	 * @param unknown $campaign_list
	 * @param unknown $template_id
	 * @return boolean
	 */
	public function save_campaign_by_script($campaign_list, $script_id) {
	
		$response_delete = true;
		$response_insert = true;
	
		//Get the old campaigns
		$old_campaigns = $this->Script_model->get_campaigns_by_script_id($script_id);
		$aux = array();
		foreach ($old_campaigns as $old_campaign) {
			array_push($aux, $old_campaign['campaign_id']);
		}
		$old_campaigns = $aux;
	
		//Campaings to remove from scripts_to_campaigns
		$delete_campaigns = array_intersect($old_campaigns, array_diff($old_campaigns, $campaign_list));
		if (!empty($delete_campaigns)) {
			$response_delete = $this->Script_model->delete_campaigns_by_script_id($script_id, $delete_campaigns);
		}
	
	
		//Campaings to insert in scripts_to_campaigns
		$insert_campaigns = array_intersect($campaign_list, array_diff($campaign_list, $old_campaigns));
		if (!empty($insert_campaigns)) {
			$response_insert = $this->Script_model->insert_campaigns_by_script_id($script_id, $insert_campaigns);
		}
	
	
		return ($response_delete && $response_insert);
	}
	
	/**
	 * Delete a script
	 */
	public function delete_script(){
		$script_id = intval($this->input->post('id'));
		
		//Delete the campaigns
		$campaignList = $this->Script_model->get_campaigns_by_script_id($script_id);
		$aux = array();
		foreach ($campaignList as $old_campaign) {
			array_push($aux, $old_campaign['campaign_id']);
		}
		$campaignList = $aux;
		$response_delete = $this->Script_model->delete_campaigns_by_script_id($script_id, $campaignList);
		if ($response_delete) {
				$response = $this->Script_model->delete_script($script_id);
			if($response){
				echo json_encode(array("success"=>true,"data"=>$response));
			} else {
				echo json_encode(array("success"=>false));
			}
		}
		else {
			echo json_encode(array("success"=>false));
		}
	}
	
}