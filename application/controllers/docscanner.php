<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Docscanner extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
		user_auth_check(false);
		$this->_campaigns = campaign_access_dropdown();
$this->_pots = campaign_pots();
		 $this->load->model('Docscanner_model');
    }
    
	public function index(){
	        $data                 = array(
            'campaign_access' => $this->_campaigns,
'campaign_pots' => $this->_pots,
			'pageId' => 'Admin',
            'title' => 'Admin',
            'javascript' => array(
                'dashboard.js',
            ),
            'options' => $options,
            'css' => array(
                'dashboard.css'
            )
        );
        $this->template->load('default', 'scanner.php', $data);	
		
	}
	
	
	 public function scanall(){
		 echo "starting scan...";
		 echo "<br>";
		 $directory = FCPATH . "upload\cv";
        $files = array_diff(scandir($directory), array(
            '..',
            '.'
        ));
		print_r($files);
		
		foreach($files as $file){
		echo $file;
		$this->scanfile($directory."/".$file);	
		}
	 }
	
    public function scanfile($filename)
    {
		$doctxt = $this->Docscanner_model->convertToText($filename);
		//echo $doctxt;
		$joblist = array("Quality Manager","Quality Assurance Manager","Technical Assistant","Technical Manager","Technical Coordinator","NPD Technologist","NPD Manager","Product Development Manager","Development Manager","Product Development Technologist","Development Technologist","Food Technologist","Development Chef","Concept Technologist","Specification Technologist","Development Chef","Concept Chef","Process Technologist","Process Development Technologist","Process Manager","Process Improvement Manager","Engineering","Mechanical Engineer","Electrical engineer – very important","Project engineer","Continuous improvement engineer","Value stream engineer","Lean manufacturing engineer","Refrigeration engineer","Multi-skilled engineer","Automation engineer","Technician","Electrical Technician","Maintenance Engineer","Shift Engineer","Engineering supervisor","Engineering team leader","Engineering manager","Chief engineer","Engineering director","Multi-Skilled Engineer","Regional HR Manager","HR Manager","HR Assistant","HR Officer","HR Administrator","HR Director","Production and Operations","Operations Director","Factory Manager","Site Manager","Manufacturing Manager","Production Supervisor","Production manager","Operations supervisor","Operations manager","Shift Manager","Supply chain manager","Planning manager","Production scheduler","Production planner","Category Manager","National Account Manager","Business Development Manager","Commercial Manager","Commercial Director");

$this->load->helper('scan');
$result = contains($doctxt,$joblist);

if($result){
	$industry = array("management");
$final = contains($doctxt,$industry);
echo $final;	
}

}
}

?>