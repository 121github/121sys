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
		$this->load->model('File_model');
    }


    public function user()
    {
        //this function returns all files upload by a specific user
    }


    public function campaign()
    {
        //this function returns all files uploaded to a specific campaign
    }

    public function manager()
    {
		user_auth_check(false);
        //this function returns all files uploaded to the open directory (unrestricted/no login required)
        $folder = ($this->uri->segment(3) ? $this->uri->segment(3) : false);
		$user_folders = $this->File_model->get_folders();
        if (@!is_dir(FCPATH . "upload/".$user_folders[$folder]['folder_name'])||@intval($folder)&&!array_key_exists($folder,$user_folders)||@!count($user_folders)) {
          redirect('error/files');
           exit;
        }
		//admin has read and write access if a folder is selected. Otherwize use the database results
		$read = $_SESSION['role']==1&&$folder||$folder&&$user_folders[$folder]['read']==1?true:false;
		$write = $_SESSION['role']==1&&$folder||$folder&&$user_folders[$folder]['write']==1?true:false;
		$folder_name = $folder?$user_folders[$folder]['folder_name']:false;
		$filetypes = $folder?$user_folders[$folder]['accepted_filetypes']:false;
		$ft = explode(",",$filetypes);
		$check_string = "";
		foreach($ft as $k=>$v){
			$check_string .= "file.name.split('.').pop() != '$v'&&";
		}
		$check_string = $folder?rtrim($check_string,"&&"):false;
		$files = $this->File_model->get_files($folder);

        $data = array(
			'check_string'=>$check_string,
			'filetypes'=>$filetypes,
			'write' =>$write,
			'read' => $read,
			'folder_name'=>$folder_name,
            'folder' => $folder,
            'pageId' => 'Admin',
            'campaign_access' => $this->_campaigns,
            'title' => 'File Manager',
			'user_folders'=>$user_folders,
			'files'=>$files,
            'javascript' => array(
                'lib/dropzone.js',
				'files.js'
            ),
            'css' => array(
                'plugins/dropzone/basic.min.css', 'plugins/dropzone/dropzone.min.css'
            )
        );
        $this->template->load('default', 'files/manager.php', $data);
    }
	
	public function get_files($folder=NULL){
		   if ($this->input->is_ajax_request()) {
				$folder = $this->input->post('folder');
				$showall = false;
				if($this->input->post('showall')){
					$showall = true;
				}
				$files = $this->File_model->get_files($folder,$showall);	
				echo json_encode(array("success"=>true,"files"=>$files));
			}

	}

	public function download(){
	$this->load->library('zip');
	$file_id = $this->uri->segment(3);
	$result = $this->get_file_from_id($file_id,true);
	$file = FCPATH ."/upload/" . $result['folder_name'] . "/" . $result['filename'];
 	$this->zip->read_file($file);
 //uncomment the below if you wish to archive downloads in the download folder
 //$zippath = FCPATH . "downloads/".date('ymdhis').$file['filename'].".zip";
 //$this->zip->archive($zippath);
	$this->zip->download($result['filename'].'.zip');
	}
	
		public function delete_file(){
	$file_id = $this->input->post('id');
	
	$filepath = $this->get_file_from_id($file_id);
	if(@unlink($filepath)){
	$result = $this->File_model->delete_file($file_id);
	echo json_encode(array("success"=>true));
	} else {
	echo json_encode(array("success"=>false,"msg"=>"File could not be deleted"));
	}
	}
	
	public function get_file_from_id($file_id=false,$parts=false){
	if(!$file_id){
	$file_id = $this->input->post('id');
	}
	$result = $this->File_model->file_from_id($file_id);
	if(count($result)==0){
	 redirect('error/files');	
	}
	if($parts){
	return $result;	
	}
	$filepath = FCPATH ."/upload/" . $result['folder_name'] . "/" . $result['filename'];
	return $filepath;
	}

    public function start_upload()
    {
		$user_folders = $this->File_model->get_folders();
        $folder = $this->input->post('folder');   //2
		$folder_name = $this->input->post('folder_name'); 
        if (!is_dir(FCPATH . "upload/$folder_name")) {
            echo json_encode(array("success" => false));
        }

        if (!empty($_FILES) && is_dir(FCPATH . "upload/$folder_name")) {

           $tempFile = $_FILES['file']['tmp_name'];          //3
			$originalname = preg_replace("/[^A-Za-z0-9. ]/", '', $_FILES['file']['name']);
            $targetPath = FCPATH . "upload/$folder_name/";  //4
            $targetFile = $targetPath . $originalname;  //5
			$filesize = filesize($tempFile);
if(file_exists($targetFile)){
header('HTTP/1.0 406 Not Found');
echo "File already exists in the selected folder";
exit;	
}
            if (move_uploaded_file($tempFile, $targetFile)) {
                //Send email to cvproject@121customerinsight.co.uk
				$this->File_model->add_file($originalname,$folder,$filesize);
                if ($this->send_email($targetFile)) {
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
		$this->email->to('bradf@121customerinsight.co.uk');
        //$this->email->to('cvproject@121customerinsight.co.uk');
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
			$folders = $this->File_model->get_folders();
	        $data                 = array(
            'campaign_access' => $this->_campaigns,
			'folders'=>$folders,
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
	
public function fix_db(){

	$folder = FCPATH . "upload/cv";
	if(!is_dir($folder)){
	echo "folder not found";	
	}
foreach (scandir($folder) as $file) {
        if ('.' === $file) continue;
        if ('..' === $file) continue;
$date = filemtime($folder."/".$file);
 
    clearstatcache();
	$this->db->query("insert into files set filename = '$file',filesize='".filesize($folder."/".$file)."',folder_id = 1,date_added='".date('Y-m-d H:i:s',$date)."'");
     // echo $file . date('Y-m-d H:i:s',$date); echo formatSizeUnits(filesize($file));
}
	
}

}