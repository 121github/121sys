<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Files extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
        //check_page_permissions("view files");
        $this->_campaigns = campaign_access_dropdown();
		$this->load->model('Docscanner_model');
    }


    public function user()
    {
        //this function returns all files upload by a specific user
    }


    public function campaign()
    {
        //this function returns all files uploaded to a specific campaign
    }

    public function upload()
    {
		user_auth_check(false);
        //this function returns all files uploaded to the open directory (unrestricted/no login required)
        $folder = ($this->uri->segment(3) ? $this->uri->segment(3) : "");
        if (!is_dir(FCPATH . "upload/$folder")) {
            echo "Path does not exist";
            exit;
        }
        $data = array(
            'folder' => $folder,
            'pageId' => 'Admin',
            'campaign_access' => $this->_campaigns,
            'title' => 'File Manager',
            'javascript' => array(
                'lib/dropzone.js'
            ),
            'css' => array(
                'plugins/dropzone/basic.min.css', 'plugins/dropzone/dropzone.min.css'
            )
        );
        $this->template->load('default', 'files/upload.php', $data);
    }

    public function start_upload()
    {


        $folder = $this->input->post('folder');   //2

        if (!is_dir(FCPATH . "upload/$folder")) {
            echo json_encode(array("success" => false));
        }

        if (!empty($_FILES) && is_dir(FCPATH . "upload/$folder")) {

            $tempFile = $_FILES['file']['tmp_name'];          //3

            $targetPath = FCPATH . "upload/$folder/";  //4

            $targetFile = $targetPath . date('ymdhis-') . $_FILES['file']['name'];  //5

            if (move_uploaded_file($tempFile, $targetFile)) {
                //Send email to cvproject@121customerinsight.co.uk
                if ($this->send_email($targetFile)) {
                    //Return success
                    echo json_encode(array("success" => true));
                }
            }

        }

    }

    private function send_email($filePath) {

        $this->load->library('email');

        $config = array("smtp_host"=>"mail.121system.com",
            "smtp_user"=>"mail@121system.com",
            "smtp_pass"=>"L3O9QDirgUKXNE7rbNkP",
            "smtp_port"=>25);

        $config['mailtype'] = 'html';

        $this->email->initialize($config);

        $this->email->from('noreply@121customerinsight.co.uk');
        $this->email->to('cvproject@121customerinsight.co.uk');
        $this->email->cc('');
        $this->email->bcc('');
        $this->email->subject('CV Submission');
        $this->email->message('A new CV File was uploaded');


        //Attach the file
        $this->email->attach($filePath);

        $result = $this->email->send();
        //$this->email->print_debugger();
        $this->email->clear();

        return $result;
    }
	
	
		public function index(){
	        $data                 = array(
            'campaign_access' => $this->_campaigns,
			'pageId' => 'Admin',
            'title' => 'Admin',
            'javascript' => array(
                'dashboard.js',
            ),
            'css' => array(
                'dashboard.css'
            )
        );
        $this->template->load('default', 'files/scanner.php', $data);	
		
	}
	
	
	 public function scanall(){
		 $directory = FCPATH . "upload\cv";
        $files = array_diff(scandir($directory), array(
            '..',
            '.'
        ));
		print_r($files);
		
		foreach($files as $file){
		$this->scanfile($directory."/".$file,$file);	
		}
	 }
	
    public function scanfile($filename,$file)
    {
		$doctxt = $this->Docscanner_model->convertToText($filename);
		//echo $doctxt;
		$joblist = array("Quality Manager","Quality Assurance Manager","Technical Assistant","Technical Manager","Technical Coordinator","NPD Technologist","NPD Manager","Product Development Manager","Development Manager","Product Development Technologist","Development Technologist","Food Technologist","Development Chef","Concept Technologist","Specification Technologist","Development Chef","Concept Chef","Process Technologist","Process Development Technologist","Process Manager","Process Improvement Manager","Engineering","Mechanical Engineer","Electrical engineer – very important","Project engineer","Continuous improvement engineer","Value stream engineer","Lean manufacturing engineer","Refrigeration engineer","Multi-skilled engineer","Automation engineer","Technician","Electrical Technician","Maintenance Engineer","Shift Engineer","Engineering supervisor","Engineering team leader","Engineering manager","Chief engineer","Engineering director","Multi-Skilled Engineer","Regional HR Manager","HR Manager","HR Assistant","HR Officer","HR Administrator","HR Director","Production and Operations","Operations Director","Factory Manager","Site Manager","Manufacturing Manager","Production Supervisor","Production manager","Operations supervisor","Operations manager","Shift Manager","Supply chain manager","Planning manager","Production scheduler","Production planner","Category Manager","National Account Manager","Business Development Manager","Commercial Manager","Commercial Director");

$this->load->helper('scan');
$result = contains($doctxt,$joblist);

if($result){
	$industry = array("management");
$final = contains($doctxt,$industry);
echo $file . " => ". $final;	
}

}
	

}