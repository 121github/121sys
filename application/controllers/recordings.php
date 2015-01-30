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
$array = array();
$recordings = array();
$recording = array();
if(count($numbers)>0){
$number_list = "";
foreach($numbers as $k =>$num){
	$number_list .= '"'.$num.'",';	
}
}

$number_list = rtrim($number_list,",");

$db2 = $this->load->database('121backup',true);
if(count($calls)>0){
foreach($calls as $row){
$calltime = $row['contact'];
$qry .= "select call_id,servicename,filepath,starttime,endtime,date_format(starttime,'%d/%m/%y %H:%i') calldate from calls left join parties on calls.id=parties.call_id where name <> '' and replace(servicename,' ','') in($number_list) and (endtime > '$calltime' - INTERVAL 5 minute or endtime < '$calltime' + INTERVAL 5 minute) and calldate = date('$calltime') group by call_id union ";
}
$qry = rtrim($qry,"union ");

$result = $db2->query($qry);
$this->firephp->log($result->num_rows());
$array = $result->result_array();
$this->firephp->log($array);


foreach($array as $k=> $row){
$recordings[] = $row;
}
$this->firephp->log($recordings);
foreach($recordings as $k=>$row){
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
