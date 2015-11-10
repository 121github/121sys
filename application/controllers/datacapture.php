<?php
//class just for testing things
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Datacapture extends CI_Controller
{
    
public function index(){
		if(!isset($_GET['json'])){
	echo "<p>The list below shows the data that we will capture in this url. If you would like the response in json format please include <b>&amp;json=true</b> in your URL parameters</p>";
foreach($_GET as $k=>$v){

	$key = htmlentities($k);
	$val = htmlentities($v);
	echo $key.":<b>".$val."</b><br>";
	}
	} else {
	echo json_encode($_GET);	
	}

}
}