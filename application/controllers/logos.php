<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logos extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
		  user_auth_check(false);
		$this->_campaigns = campaign_access_dropdown();

		 $this->load->model('Form_model');
    }
    
	public function index(){
		$campaigns = $this->Form_model->get_campaigns();
		 $logos = array();
		 $path = FCPATH."/assets/logos";
		if ($handle = opendir($path)) {
    		while (false !== ($entry = readdir($handle))) {
       		 	if ($entry != "." && $entry != "..") {
            $logos[$entry] = base_url()."assets/logos/".$entry;
        	}
    		}
   		 closedir($handle);
			}
		
    	$data     = array(
    			'campaign_access' => $this->_campaigns,

    			'pageId' => 'Admin',
    			'title' => 'Campaign logos',
    			'page' => 'logos',
    			'javascript' => array(	
				'lib/dropzone.js'
    			),
    			'css' => array(
    					'dashboard.css',
						 'plugins/dropzone/dropzone.min.css'
    			),
    			'campaigns' => $campaigns,
				"logos"=>$logos
    	);
    	$this->template->load('default', 'admin/logos.php', $data);
	}
	
	
	public function get_logo_html(){
	$id = $this->input->post('id');
	$query = $this->db->query("select logo from campaigns where campaign_id = '$id'");
	if($query->num_rows()>0){
	$campaign_logo = $query->row()->logo;
	} else {
	$campaign_logo = "";
	}
		 $logos = array();
		 $path = FCPATH."/assets/logos";
		if ($handle = opendir($path)) {
    		while (false !== ($entry = readdir($handle))) {
       		 	if ($entry != "." && $entry != "..") {
            $logos[$entry] = base_url()."assets/logos/".$entry;
        	}
    		}
   		 closedir($handle);
			}
			?><table class="table"><tr><?php
			$i=0; foreach($logos as $filename => $logo){ $i++; if($i%4==1){ echo "</tr><tr>"; } ?><td><input <?php if($campaign_logo==$filename){ echo "checked"; } ?> class="logo-btn" type="radio" id="<?php echo "logo".$i ?>" value="<?php echo $filename ?>" name="logo"/> <label for="<?php echo "logo".$i ?>"> <img src="<?php echo $logo ?>" /></label></td><?php
        } ?></tr></table><?php exit;		
		
	}
	
	
	public function save(){
	$data = $this->input->post();
	$this->db->where(array("campaign_id"=>$data['campaign_id']));
	if($this->db->update("campaigns",array("logo"=>$data['logo']))){
	echo json_encode(array("success"=>true));		
	}
		
	}
}