<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Files extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
   
    public function user()
    {
		 //this function returns all files upload by a specific user
	}
	
	   
    public function campaign()
    {
		 //this function returns all files uploaded to a specific campaign
	}
	
	    public function anon()
    {
		 //this function returns all files uploaded to the open directory (unrestricted/no login required)
		 if($this->uri->segment(3)){
			 $folder = $this->uri->segment(3);	
			if(!is_dir(FCPATH."upload/$folder")){
				echo "Path does not exist";
				exit;
			}
		 }
		    $data              = array(
			'folder'=>$folder,
			'pageId' => 'Admin',
            'title' => 'File Manager',
            'javascript' => array(
                'lib/dropzone.js'
            ),
            'css' => array(
                'plugins/dropzone/basic.min.css','plugins/dropzone/dropzone.min.css'
            )
        );
        $this->template->load('default', 'files/anonymous.php', $data);
	}
	
	public function upload(){


$folder = $this->input->post('folder');   //2
 
if (!empty($_FILES)&&is_dir(FCPATH."upload/$folder")) {
     
    $tempFile = $_FILES['file']['tmp_name'];          //3             
      
    $targetPath = FCPATH."upload/$folder/";  //4
     
    $targetFile =  $targetPath. $_FILES['file']['name'];  //5
 
    if(move_uploaded_file($tempFile,$targetFile)){
	echo json_encode(array("success"=>true));
	}
     
}

	}
	
}