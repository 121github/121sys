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
 $user_folders = $this->File_model->get_folders();
/*
        $folder_name  = $folder ? $user_folders[$folder]['folder_name'] : false;
        $filetypes    = $folder ? $user_folders[$folder]['accepted_filetypes'] : false;
        $ft           = explode(",", $filetypes);
        $check_string = "";
        foreach ($ft as $k => $v) {
            $check_string .= "file.name.split('.').pop() != '$v'&&";
        }
        $check_string = $folder ? rtrim($check_string, "&&") : false;
        //$files = $this->File_model->get_files($folder);
        */
		
        $data = array(
          //  'check_string' => $check_string,
          ///  'filetypes' => $filetypes,
           // 'write' => $write,
           // 'read' => $read,
            //'folder_name' => $folder_name,
            //'folder' => $folder,
            'pageId' => 'Admin',
            'campaign_access' => $this->_campaigns,
            'title' => 'File Manager',
            'user_folders' => $user_folders,
            //'files'=>$files,
            'javascript' => array(
                'lib/dropzone.js',
                'files.js'
            ),
            'css' => array(
                'plugins/dropzone/basic.min.css',
                'plugins/dropzone/dropzone.min.css'
            )
        );
        $this->template->load('default', 'files/manager.php', $data);
    }
    
    public function process_files()
    {
        if ($this->input->is_ajax_request()) {
            $count = $this->File_model->get_files_for_table($this->input->post(),true); //second param just returns the count
            $files = $this->File_model->get_files_for_table($this->input->post());
            $data  = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "data" => $files
            );
            echo json_encode($data);
        }
    }
    
	    public function get_permissions()
    {
        if ($this->input->is_ajax_request()) {
		$folder = $this->input->post('id');
		if($_SESSION['role']==1){
		$permissions = array("folder_id"=>$folder,"write_access"=>1,"read_access"=>1,"accepted_filetypes"=>"*");
		} else {
		$permissions = $this->File_model->get_permissions($folder);
		}
		/* doesnt work :(
		$ft = explode(",", $permissions['accepted_filetypes']);
        $check_string = "";
        foreach ($ft as $k => $v) {
            $check_string .= "file.name.split('.').pop() != '$v'&&";
        }
        $check_string = $folder ? rtrim($check_string, "&&") : false;
		*/
echo json_encode(array("success"=>true,"permissions"=>$permissions));


		}
	}
	
    public function get_files($folder = NULL)
    {
        if ($this->input->is_ajax_request()) {
            $folder  = $this->input->post('folder');
            $showall = false;
            if ($this->input->post('showall')) {
                $showall = true;
            }
            $files = $this->File_model->get_files($folder, $showall);
            echo json_encode(array(
                "success" => true,
                "files" => $files
            ));
        }
        
    }
    
    public function download()
    {
        $this->load->library('zip');
        $file_id = $this->uri->segment(3);
        $result  = $this->get_file_from_id($file_id, true);
        $file    = FCPATH . "/upload/" . $result['folder_name'] . "/" .$result['subfolder']."/". $result['filename'];
        $this->zip->read_file($file);
        //uncomment the below if you wish to archive downloads in the download folder
        //$zippath = FCPATH . "downloads/".date('ymdhis').$file['filename'].".zip";
        //$this->zip->archive($zippath);
        $this->zip->download($result['filename'] . '.zip');
    }
    
    public function delete_file()
    {
        $file_id = $this->input->post('id');
        
        $filepath = $this->get_file_from_id($file_id);
        if (@unlink($filepath)) {
            $result = $this->File_model->delete_file($file_id);
            echo json_encode(array(
                "success" => true
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "msg" => "File could not be deleted"
            ));
        }
    }
    
    public function get_file_from_id($file_id = false, $parts = false)
    {
        if (!$file_id) {
            $file_id = $this->input->post('id');
        }
        $result = $this->File_model->file_from_id($file_id);
        if (count($result) == 0) {
            redirect('error/files');
        }
        if ($parts) {
            return $result;
        }
        $filepath = FCPATH . "/upload/" . $result['folder_name'] . "/" . $result['subfolder'] . "/" . $result['filename'];
        return $filepath;
    }
    
    public function start_upload()
    {
		$this->load->model('Docscanner_model');
        $this->load->helper('scan');
        $user_folders = $this->File_model->get_folders();
        $folder       = $this->input->post('folder');
		//$this->firephp->log($folder);
		//check the user has access to write to this folder
		if($user_folders[$folder]['write']=="1"){
		$folder_name = $user_folders[$folder]['folder_name'];
		$day = date('Y-m-d');
		if(!is_dir(FCPATH . "upload/".$folder_name."/".$day)){
		mkdir(FCPATH . "upload/$folder_name/$day");
		}
		} else {
		//display an error
		header('HTTP/1.0 406 Not Found');
       	echo "You don't have permission to write to this folder";
        exit;	
		}
        if (!is_dir(FCPATH . "upload/$folder_name")) {
        header('HTTP/1.0 406 Not Found');
       	echo "Selected folder does not exist";
        exit;
        }
        
        if (!empty($_FILES) && is_dir(FCPATH . "upload/$folder_name/$day")) {
            
            $tempFile     = $_FILES['file']['tmp_name']; //3
            $originalname = preg_replace("/[^A-Za-z0-9. ]/", '', $_FILES['file']['name']);
            $targetPath   = FCPATH . "upload/$folder_name/$day"; //4
            $targetFile   = $targetPath ."/". $originalname; //5
            $filesize     = filesize($tempFile);
			
			//this checks the filetype is allowed
			if(@!empty($user_folders[$folder]['accepted_filetypes'])){
			$allowed =  explode(",",$user_folders[$folder]['accepted_filetypes']);
			$ext = pathinfo($originalname, PATHINFO_EXTENSION);
			if(!in_array($ext,$allowed) ) {
   				header('HTTP/1.0 406 Not Found');
                echo "File type is not allowed only {$user_folders[$folder]['accepted_filetypes']} may be added to this folder";
                exit;
				}
			}
			//check the file is not a duplicate
            if (file_exists($targetFile)) {
                header('HTTP/1.0 406 Not Found');
                echo "File already exists in the selected folder";
                exit;
            }
			//if there are no errors then move the file
            if (move_uploaded_file($tempFile, $targetFile)) {
                //Send email to cvproject@121customerinsight.co.uk
               if($this->File_model->add_file($originalname, $folder, $filesize)){
					$this->send_email($targetFile);
                    echo json_encode(array(
                        "success" => true
                    ));
                }
            }
            
        }
        
    }
    
    private function send_email($filePath)
    {
        
        $this->load->library('email');
        
        $config = array(
            "smtp_host" => "mail.121system.com",
            "smtp_user" => "mail@121system.com",
            "smtp_pass" => "L3O9QDirgUKXNE7rbNkP",
            "smtp_port" => 25
        );
        
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
    
    
    public function index()
    {
        $folders = $this->File_model->get_folders();
        $data    = array(
            'campaign_access' => $this->_campaigns,
            'folders' => $folders,
            'pageId' => 'Admin',
            'title' => 'Admin',
            'javascript' => array(
                'dashboard.js'
            ),
            'css' => array(
                'dashboard.css'
            )
        );
        $this->template->load('default', 'files/scanner.php', $data);
        
    }
    
    
    public function scanall()
    {
        $directory = FCPATH . "upload\cv";
        $files     = array_diff(scandir($directory), array(
            '..',
            '.'
        ));
        print_r($files);
        
        foreach ($files as $file) {
            $this->scanfile($directory . "/" . $file, $file);
        }
    }
    
    public function scanfile($filename, $file)
    {
        $doctxt  = $this->Docscanner_model->convertToText($filename);
        //echo $doctxt;
        $joblist = array(
            "Quality Manager",
            "Quality Assurance Manager",
            "Technical Assistant",
            "Technical Manager",
            "Technical Coordinator",
            "NPD Technologist",
            "NPD Manager",
            "Product Development Manager",
            "Development Manager",
            "Product Development Technologist",
            "Development Technologist",
            "Food Technologist",
            "Development Chef",
            "Concept Technologist",
            "Specification Technologist",
            "Development Chef",
            "Concept Chef",
            "Process Technologist",
            "Process Development Technologist",
            "Process Manager",
            "Process Improvement Manager",
            "Engineering",
            "Mechanical Engineer",
            "Electrical engineer – very important",
            "Project engineer",
            "Continuous improvement engineer",
            "Value stream engineer",
            "Lean manufacturing engineer",
            "Refrigeration engineer",
            "Multi-skilled engineer",
            "Automation engineer",
            "Technician",
            "Electrical Technician",
            "Maintenance Engineer",
            "Shift Engineer",
            "Engineering supervisor",
            "Engineering team leader",
            "Engineering manager",
            "Chief engineer",
            "Engineering director",
            "Multi-Skilled Engineer",
            "Regional HR Manager",
            "HR Manager",
            "HR Assistant",
            "HR Officer",
            "HR Administrator",
            "HR Director",
            "Production and Operations",
            "Operations Director",
            "Factory Manager",
            "Site Manager",
            "Manufacturing Manager",
            "Production Supervisor",
            "Production manager",
            "Operations supervisor",
            "Operations manager",
            "Shift Manager",
            "Supply chain manager",
            "Planning manager",
            "Production scheduler",
            "Production planner",
            "Category Manager",
            "National Account Manager",
            "Business Development Manager",
            "Commercial Manager",
            "Commercial Director"
        );
        
        $this->load->helper('scan');
        $result = contains($doctxt, $joblist);
        
        if ($result) {
            $industry = array(
                "management"
            );
            $final    = contains($doctxt, $industry);
            echo $file . " => " . $final;
        }
        
    }
    
    public function fix_db()
    {
        
        $folder = FCPATH . "upload/cv";
        if (!is_dir($folder)) {
            echo "folder not found";
        }
        foreach (scandir($folder) as $original) {
            if ('.' === $original)
                continue;
            if ('..' === $original)
                continue;
            $date = filemtime($folder . "/" . $original);
            
            $parts    = explode("-", $original);
            $filedate = $parts[0];
            $cvname   = str_replace($filedate . "-", "", $original);
            
            $file = $cvname . " ";
            
            
            
            
            $file2 = str_replace("doc ", ".doc", $file);
            $file3 = str_replace("docx ", ".docx", $file2);
            $file4 = str_replace("..", ".", $file3);
            rename(FCPATH . "/upload/cv/" . $original, FCPATH . "/upload/cv/" . $file4);
            clearstatcache();
            echo $query = "insert into files set filename = '" . addslashes($file4) . "',filesize='" . filesize($folder . "/" . $file4) . "',folder_id = 1,date_added=str_to_date('$filedate','%y%m%d%H%i%s')";
            echo ";<br>";
            //$this->db->query($query);
            // echo $file . date('Y-m-d H:i:s',$date); echo formatSizeUnits(filesize($file));
        }
        
    }
       public function fix_db2(){
		$files = $this->db->query("select filename,date(date_added) subfolder from files")->result_array();
		  $folder = FCPATH . "upload/cv";
		foreach( $files as $row){
			echo $old = $folder."/".$row['filename'];
			echo $new = $folder."/".$row['subfolder']."/".$row['filename'];
			if(!is_dir($folder."/".$row['subfolder'])){
			mkdir($folder."/".$row['subfolder']);	
			}
			if(file_exists($old)&&is_dir($row['subfolder'])){
			rename($old,$new);
			}
		}
	   }
}