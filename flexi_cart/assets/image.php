<?php 
if(isset($_GET['src'])){
	header("Content-type: image/jpeg");
$src = $_GET['src'];
echo file_get_contents($src);
	
}
if(isset($_GET['id'])){
//save to database	
	
	
}
?>