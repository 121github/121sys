<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recordings extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
		$this->_campaigns = campaign_access_dropdown();
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
foreach($calls as $row){
$calltime = $row['contact'];
$qry .= "select call_id,servicename,starttime,endtime,date_format(starttime,'%d/%m/%y %H:%i') calldate from calls left join parties on calls.id=parties.call_id where name <> '' and replace(servicename,' ','') in($number_list) and (endtime > '$calltime' - INTERVAL 5 minute or endtime < '$calltime' + INTERVAL 5 minute) and calldate = date('$calltime') group by call_id union ";
}
$qry = rtrim($qry,"union ");
$array = $db2->query($qry)->result_array();
foreach($array as $k=> $row){
$recordings[] = $row;
}
foreach($recordings as $k=>$row){
	$recordings[$k]['duration']=timespan(strtotime($row['starttime']),strtotime($row['endtime']),true);
}
echo json_encode(array("success"=>true,"data"=>$recordings,"msg"=>"No recordings could be found for this record"));
}

public function listen(){
$id = intval($this->uri->segment('3'));
$qry = "select * from calls where id='$id'";
$db2 = $this->load->database('121backup',true);
$filename = $db2->query($qry)->row()->filepath;
$file = urlencode(str_replace("xml","wav",str_replace("/","\\",str_replace("/mnt/34recordings/","",$filename))));


if(strpos($_SERVER['REMOTE_ADDR'],"192.168.1"!==false)||strpos($_SERVER['REMOTE_ADDR'],"::1")!==false){
$path = "http://192.168.1.16/";	
} 
else
{
$path = "http://www.121leads.co.uk:8034/";		
}
//unit34 path
$conversion_path = $path."file_convert.aspx?id=$id&filename=$file";
$response = file_get_contents($conversion_path);
$this->firephp->log($response);
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
if($_SERVER['REMOTE_ADDR']=="84.19.44.186"){
$path = $path = "http://192.168.1.16/";	
}
echo json_encode(array("success"=>true,"filename"=>$path."temp/".$id.".". $filetype,"response"=>$response,"filetype"=>$filetype));


}

}
?>