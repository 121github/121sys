<?php
require('upload.php');

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Templates extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		user_auth_check(false);
$this->_campaigns = campaign_access_dropdown();
		$this->load->model('Email_model');
		$this->load->model('Form_model');
		
	}
	
	/**
	 * This is the controller loads the initial view for the templates
	 */
	public function index()
	{
		$campaigns = $this->Form_model->get_user_campaigns();
		
		$data = array(
				'campaign_access' => $this->_campaigns,
'pageId' => 'Dashboard',
				'title' => 'Dashboard',
				'page'=> array('admin'=>'templates'),
				'css' => array(
						'dashboard.css',
						'plugins/summernote/summernote.css',
						'plugins/fontAwesome/css/font-awesome.css',
						'plugins/jqfileupload/jquery.fileupload.css',
				),
	
				'javascript' => array(
						'template-table.js',
						'plugins/summernote/summernote.min.js',
						'plugins/jqfileupload/vendor/jquery.ui.widget.js',
						'plugins/jqfileupload/jquery.iframe-transport.js',
						'plugins/jqfileupload/jquery.fileupload.js',
                        'plugins/jqfileupload/jquery.fileupload-process.js',
                        'plugins/jqfileupload/jquery.fileupload-validate.js'
				),
				'campaigns' => $campaigns,
		);
	
		$this->template->load('default', 'email/template.php', $data);
	}
	
	/**
	 * Get the templates
	 */
	public function template_data()
	{
		if ($this->input->is_ajax_request()) {
			$templateList = $this->Email_model->get_templates();
			echo json_encode(array(
					"success" => true,
					"data" => $templateList,
					"msg" => "Nothing found"
			));
			exit;
		}
	}
	
	/**
	 * Get the campaigns for a template
	 */
	public function get_campaings_by_template_id() {
		$template_id = $this->input->post('id');
		
		$campaignList = $this->Email_model->get_campaigns_by_template_id($template_id);
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
	 * Get the attachments for a template
	 */
	public function get_attachments_by_template_id() {
		$template_id = $this->input->post('id');
	
		$attachmentList = $this->Email_model->get_attachments_by_template_id($template_id);
		$auxList = array();
		foreach ($attachmentList as $attachment) {
			$auxList[$attachment['id']]['name'] = $attachment["name"];
			$auxList[$attachment['id']]['path'] = $attachment["path"];
		}
		$attachmentList = $auxList;
	
		echo json_encode(array(
				"success" => true,
				"data" => $attachmentList,
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
		$form['template_body'] = base64_decode($this->input->post('template_body'));
		
		//Before save the template, check if the image/s used in the template are still in the new body, otherwise, we remove the image/s from the upload/template folder
		if ($form['template_id']) {
			$old_template = $this->Email_model->get_template($form['template_id']);
			$this->delete_old_images($old_template['template_body'], $form['template_body']);
		}
		
		//Check if the user selected any campaign for this template
		$campaignsForm = array();
		if (isset($form['campaign_id'])) {
			$campaignsForm = $form['campaign_id'];
			unset($form['campaign_id']);
		}
		
		//Check if the user selected any attachment for this template 
		$attachmentsForm = array();
		if (!empty($form['template_attachments'])) {
			$attachmentsForm = $form['template_attachments'];
			$attachmentsForm = explode(",", $attachmentsForm);
			$aux = array();
			foreach ($attachmentsForm as $attachment) {
				$name = substr($attachment, strripos($attachment, "?") + 1);
                $path = substr($attachment, 0, strripos($attachment, "?"));
				$element = array("name" => $name, "path" => $path);
				array_push($aux, $element);
			}
			$attachmentsForm = $aux;
		}
		unset($form['template_attachments']);
		
		//Insert the new template
		if (empty($form['template_id'])) {
			$insert_id = $this->Email_model->add_new_template($form);
			$response = ($insert_id)?true:false;
		} else {
			//Update the template
			$response = $this->Email_model->update_template($form);
			if ($response) {
				//Save the campaigns
				$insert_id =  $form['template_id'];
			}
		}
		
		if ($response && isset($campaignsForm)) {
			//Save the campaigns
			$response = $this->save_campaign_by_template($campaignsForm, $insert_id);
		}
		if ($response && !empty($attachmentsForm)) {
			//Save the attachments
			$response = $this->save_attachment_by_template($attachmentsForm, $insert_id);
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
	public function save_campaign_by_template($campaign_list, $template_id) {
		
		$response_delete = true;
		$response_insert = true;
		
		//Get the old campaigns
		$old_campaigns = $this->Email_model->get_campaigns_by_template_id($template_id);
		$aux = array();
		foreach ($old_campaigns as $old_campaign) {
			array_push($aux, $old_campaign['campaign_id']);
		}
		$old_campaigns = $aux;
		
		//Campaings to remove from mail_template_to_campaigns
		$delete_campaigns = array_intersect($old_campaigns, array_diff($old_campaigns, $campaign_list));
		if (!empty($delete_campaigns)) {
			$response_delete = $this->Email_model->delete_campaigns_by_template_id($template_id, $delete_campaigns);
		}
		
		
		//Campaings to insert in mail_template_to_campaigns
		$insert_campaigns = array_intersect($campaign_list, array_diff($campaign_list, $old_campaigns));
		if (!empty($insert_campaigns)) {
			$response_insert = $this->Email_model->insert_campaigns_by_template_id($template_id, $insert_campaigns);
		}
		
		
		return ($response_delete && $response_insert);	
	}
	
	
	/**
	 * Upload new attachments
	 */
	public function upload_template_attach() {
		$options = array();
		$options['upload_dir'] = dirname ( $_SERVER['SCRIPT_FILENAME'] )  . '/upload/templates/attachments/';
		$options['upload_url'] = base_url().'upload/templates/attachments/';
		$options['image_versions'] = array();
		$upload_handler = new Upload($options, true);
	}
	
	/**
	 * Get the upload attachment folder path
	 */
	public function get_attachment_file_path () {
		$file = $this->input->post('file');
		$path = base_url().'upload/templates/attachments/';
		$fullpath = $path.$file;
	
		$result = array(
				"path" => $fullpath,
		);
	
		$json = json_encode($result);
		echo $json;
	}
	
	/**
	 * Insert new attachments for a template
	 * 
	 * @param unknown $attachment_list
	 * @param unknown $template_id
	 * @return boolean
	 */
	public function save_attachment_by_template ($attachment_list, $template_id) {
		$response = true;
		
		foreach ($attachment_list as $attachment) {
			if (!($this->Email_model->insert_attachment_by_template_id($template_id, $attachment))) {
				$response = false;
			}
		}
		
		return $response;
	}
	
	
	/**
	 * return attachments to set in the edit template form
	 */
	public function set_attached_files() {
		$data = $this->input->post();

		$files_ar = (strlen($data['newFiles']) > 0)?explode(",", $data['newFiles']):array();
		$aux = array();
		foreach ($files_ar as $file) {
			$aux[substr($file, strripos($file, "/") + 1)] = $file;
		}
		$files_ar = $aux;
		
		$files_ar[substr($data['path']."?".$data["filename"], strripos($data['path']."?".$data["filename"], "?") + 1)] = $data['path']."?".$data["filename"];
		
		$response = $files_ar;
		echo json_encode(array("success"=>true,"data"=>implode(",", $response), "data_array" => $response));
	}
	
	/**
	 * return unset an attachment from the list to set in the edit template form
	 */
	public function unset_attached_files() {
		$data = $this->input->post();
	
		$files_ar = (strlen($data['newFiles']) > 0)?explode(",", $data['newFiles']):array();
		$aux = array();
		foreach ($files_ar as $file) {
			$aux[substr($file, strripos($file, "/") + 1)] = $file;
		}
		$files_ar = $aux;
	
		//Check if the path exist in the new Attachments array
		if ($key = array_search($data['path'], $files_ar)) {
				unset($files_ar[$key]);
		}
	
		$response = $files_ar;
		echo json_encode(array("success"=>true,"data"=>implode(",", $response), "data_array" => $response));
	}
	
	/**
	 * Detele attachment
	 */
	public function delete_attachment_by_id () {
		$response = true;
		$data = $this->input->post();
		
		if (!empty($data['id'])) {
			//Delete from database
			$response = $this->Email_model->delete_attachment_by_id($data['id']);
		}
		
		if ($response) {
			//Get the attachment list (only one file in this case)
			$attachment_list = array($data['path']);
			//Delete the files from the server folder
			foreach ($attachment_list as $path) {
                if (strripos($path, "?")) {
                    $path = substr($path,0,  strripos($path, "?"));
                }
				if (!unlink(strstr('./'.$path, 'upload'))) {
					$response = false;
				}
			}
		}
		
		echo json_encode(array("success"=>$response));
	}
	
	public function delete_old_attachments($attachment_list) {
		$response = true;
		foreach ($attachment_list as $attachment) {
            if (strripos($attachment['path'], "?")) {
                $attachment['path'] = substr($attachment['path'],0,  strripos($attachment['path'], "?"));
            }
			if (!unlink(strstr('./'.$attachment['path'], 'upload'))) {
				$response = false;
			}
		}
		return $response;
	}
	
	/**
	 * Delete attachment list from the server folder
	 */
	public function delete_attachments_list () {
		$response = true;
		
		$data = $this->input->post();
		
		if (!empty($data['filesUploaded'])) {
			$attachment_list = explode(",",$data['filesUploaded']);
			
			//Delete the files from the server folder
			foreach ($attachment_list as $path) {
                if (strripos($path, "?")) {
                    $path = substr($path,0,  strripos($path, "?"));
                }
				if (!unlink(strstr('./'.$path, 'upload'))) {
					$response = false;
				}	
			}
		}
		
		echo json_encode(array("success"=>true, "data" => $response));
		exit;
	}
	
	/**
	 * Upload an image from the editor view
	 */
	public function saveimage() {
		$allowed = array('png', 'jpg', 'gif');
		$maxsize    = 2097152; //2MB
		
		
		if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
		
			$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
		
			if(!in_array(strtolower($extension), $allowed)){
				echo json_encode(array("success"=>false,"status" => "File extension not allowed (png, jpg, gif)"));
				exit;
			}
			
			if(($_FILES['file']['size'] >= $maxsize) || ($_FILES["file"]["size"] == 0)) {
				echo json_encode(array("success"=>false,"status" => "File too large. File must be less than 2 megabytes."));
				exit;
			}
		
			$name = md5(rand(100, 200));
			$ext = explode('.',$_FILES['file']['name']);
			$filename = $name.'.'.$ext[1];
			$location =  $_FILES["file"]["tmp_name"];
			$destination = './upload/templates/'.$filename;			
			
			if(move_uploaded_file($location, $destination)){
				$tmp = base_url().'/upload/templates/'.$filename;
				echo $tmp;
				exit;
			}
		}
		
		echo json_encode(array("success"=>false,"status" => "File too large. File must be less than 2 megabytes."));
		exit;
	}
	
	/**
	 * Delete the old images from the editor
	 * 
	 * @param unknown $old_body
	 * @param unknown $new_body
	 */
	private function delete_old_images($old_body, $new_body) {
		$old_body_matches = array();
		$new_body_matches = array();
		
		//Our domain
		$myDomain = str_replace(".","\.",$_SERVER['HTTP_HOST'] );
		//RegEx to extract the images from the html code
		$re_extractImages = '/<img.*src=["\'][^ ^"^\']*'.$myDomain.'([^ ^"^\']*)["\']/';
		$re_extractImages = '/<img.*src=["\']([^ ^"^\']*)["\']/ims';
		$re_extractImages = '/< *img[^>]*src *= *["\']?([^"\']*).(jpg|png|gif)/i';
		
		//Use preg_match in order to extract the images in the code:
		preg_match_all($re_extractImages, $old_body, $old_body_matches);
		preg_match_all($re_extractImages, $new_body, $new_body_matches);
		
		$old_images = array();
		foreach ($old_body_matches[1] as $key => $old_img) {
			array_push($old_images, $old_img.'.'.$old_body_matches[2][$key]);
		}
		
		$new_images = array();
		foreach ($new_body_matches[1] as $key => $new_img) {
			array_push($new_images, $new_img.'.'.$new_body_matches[2][$key]);
		}
		
		//Get the images that are in the old_body but not in the new_body
		$img_delete_ar = array_intersect($old_images, array_diff($old_images, $new_images));
		
		
		//Delete the old images from the uploads folder
		foreach ($img_delete_ar as $filename) {
			unlink(strstr('./'.$filename, 'upload'));
		}
	}
	
	/**
	 * Delete a template
	 */
	public function delete_template(){
		$template_id = intval($this->input->post('id'));
		//Get the path images from the editor in order to delete the files after the template is removed
		$template = $this->Email_model->get_template($template_id);
		$image_list =  $template['template_body'];
		
		//Get the attachments by id template
		$attachment_list = $this->Email_model->get_attachments_by_template_id($template_id);
		
		$response = $this->Email_model->delete_template($template_id);
		if($response){
			//Delete the images (old_images). The second parameter represents the new images (empty array in this case)
			$this->delete_old_images($image_list, '');
			//Delete the old attachments
			$this->delete_old_attachments($attachment_list);
			echo json_encode(array("success"=>true,"data"=>$response));
		} else {
			echo json_encode(array("success"=>false));
		}
	
	}
	
}