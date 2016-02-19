<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recordings extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
		$this->load->model('Records_model');
		$this->load->model('Recordings_model');
    }

public function find_calls(){
	session_write_close();
	$this->load->helper('date');
//the urn we will be searching for - posted via ajax
$urn = $this->input->post('urn');
$numbers =  $this->Recordings_model->get_numbers($urn);
//$this->firephp->log($numbers);
$calls =  $this->Records_model->get_calls($urn);
//$this->firephp->log($calls);
$number_list = "''";
$qry = "";
$transfer_number ="";
$array = array();
$recordings = array();
$recording = array();
if(count($numbers)>0){
$number_list = "";
$number_query = "";
foreach($numbers as $k =>$number){
	if(strpos($number['description'],"Transfer")!==false){
	$transfer_number = 	trim($number['number']);
	} else {
	$number_query .= " replace(servicename,' ','')  like '%".trim($number['number'])."' or replace(servicename,' ','')  like '%".trim($number['number'])."+".$_SESSION['current_campaign']."' or";	
	//$number_list .= '"'.trim($number['number']).'",';	
	}
}
}
$number_list = rtrim($number_list,",");
$number_query = rtrim($number_query,"or");
$db2 = $this->load->database('121backup',true);

if(count($calls)>0){
foreach($calls as $row){
$calltime = $row['contact'];
$qry .= "select id,servicename,filepath,starttime,endtime,date_format(starttime,'%d/%m/%y %H:%i') calldate,owner from calls where ($number_query) and (endtime between '$calltime' - INTERVAL 30 minute and '$calltime' + INTERVAL 5 minute) and calldate = date('$calltime') group by id union ";
}
$qry = rtrim($qry,"union ");
//$this->firephp->log($qry);
$result = $db2->query($qry);
$recordings = $result->result_array();

foreach($recordings as $k=>$row){
//once we have the dials to the customer we look for any transfers relating to those calls	
if(!empty($transfer_number)){
$owner = $row['owner']; 	
$endtime = $row['endtime']; //the endtime of the call is the starttime of the transfer
$db2->select("id,servicename,filepath,starttime,endtime,date_format(starttime,'%d/%m/%y %H:%i') calldate,server",false);
$db2->where("replace(servicename,' ','') = '$transfer_number' and owner='$owner' and starttime between '$endtime' - interval 15 second and '$endtime' + interval 15 second and calldate = date('$calltime')",null,false);
$db2->group_by("calls.id");
$transfer_query = $db2->get('recordings.calls');

$transfers = $transfer_query->result_array();
foreach($transfers as $k=>$row){
	$row['transfer'] = true;
	$recordings[] = $row;	
}
}

}

foreach($recordings as $k=>$row){
	//append some other stats to the array
	$recordings[$k]['duration']=timespan(strtotime($row['starttime']),strtotime($row['endtime']),true);
	$recordings[$k]['filepath']=base64_encode($row['filepath']);

}
}
//$this->firephp->log($recordings);
echo json_encode(array("success"=>true,"data"=>$recordings,"msg"=>"No recordings could be found for this record"));
}

public function listen(){
session_write_close();
$id = intval($this->uri->segment('3'));
$filename = base64_decode($this->uri->segment('4'));
if(strpos($filename,"34recordings")!==false){
$port = "8034";	
$path = "https://recordings.121system.com:$port/";
$remotepath = "https://www.121system.com:$port/";
} else {
$port = "8016";	
$path = "http://recordings16.121system.com/";
$remotepath = "https://www.121system.com:$port/";
}
$file34 = str_replace("xml","wav",str_replace("/mnt/16recordings/","",str_replace("/mnt/34recordings/","",$filename)));
$file = str_replace("/","\\",$file34);

//unit34 path
$conversion_path = $path."file_convert.aspx?id=$id&filename=$file";
$this->firephp->log($conversion_path);
//the old way was a bit slow
//$context = stream_context_create(array('http' => array('header'=>'Connection: close')));
//file_get_contents($conversion_path,false,$context);
$this->load->helper('remotefile');
$response = loadFile($conversion_path);

$u_agent = $_SERVER['HTTP_USER_AGENT'];
$filetype ="mp3";
if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
 $filetype="mp3";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
$filetype="ogg";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
 $filetype="mp3";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
 $filetype="mp3";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
 $filetype="ogg";
    }

echo json_encode(array("success"=>true,"filename"=>$remotepath."temp/".$id.".". $filetype,"response"=>$response,"filetype"=>$filetype));


}


}
?>
