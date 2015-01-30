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
	//connect to 121backup which has the database of call recordings in a db named "recordings"
//the urn we will be searching for - posted via ajax
$urn = $this->input->post('urn');
$numbers =  $this->Recordings_model->get_numbers($urn);
$calls =  $this->Records_model->get_calls($urn);
$number_list = "''";
$qry = "";
$transfer_number ="";
$array = array();
$recordings = array();
$recording = array();
if(count($numbers)>0){
$number_list = "";
foreach($numbers as $k =>$number){
	if($number['description']<>"Transfer"){
	$number_list .= '"'.$number['number'].'",';	
	} else {
	$transfer_number = 	$number['number'];
	}
}
}
$this->firephp->log($transfer_number);
$number_list = rtrim($number_list,",");

$db2 = $this->load->database('121backup',true);
$db3 = $this->load->database('121backup',true);
if(count($calls)>0){
foreach($calls as $row){
$calltime = $row['contact'];
$qry .= "select id,servicename,filepath,starttime,endtime,date_format(starttime,'%d/%m/%y %H:%i') calldate,owner from calls where  replace(servicename,' ','') in($number_list) and (endtime between '$calltime' - INTERVAL 10 minute and '$calltime' + INTERVAL 5 minute) and calldate = date('$calltime') group by id union ";
}
$qry = rtrim($qry,"union ");
$this->firephp->log($qry);
$result = $db2->query($qry);
$recordings = $result->result_array();

foreach($recordings as $k=>$row){
//once we have the dials to the customer we look for any transfers relating to those calls	
if(!empty($transfer_number)){
$owner = $row['owner']; 	
$endtime = $row['endtime']; //the endtime of the call is the starttime of the transfer

$db3->select("id,servicename,filepath,starttime,endtime,date_format(starttime,'%d/%m/%y %H:%i') calldate",false);
$db3->where("replace(servicename,' ','') = '$transfer_number' and owner='$owner' and starttime between '$endtime' - interval 3 second and '$endtime' + interval 3 second and calldate = date('$calltime')",null,false);
$db3->group_by("calls.id");
$transfer_query = $db3->get('recordings.calls');
$this->firephp->log($db3);
$this->firephp->log($transfer_query);
$transfers = $transfer_query->result_array();
foreach($transfers as $k=>$row){
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
$file = urlencode(str_replace("xml","wav",str_replace("/","\\",str_replace("/mnt/34recordings/","",$filename))));

$path = "http://recordings.121leads.co.uk:8034/";

//unit34 path
$conversion_path = $path."file_convert.aspx?id=$id&filename=$file";
//$this->firephp->log($conversion_path);
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

$path = "http://recordings.121leads.co.uk:8034/";

echo json_encode(array("success"=>true,"filename"=>$path."temp/".$id.".". $filetype,"response"=>$response,"filetype"=>$filetype));


}

}
?>
