<?php 

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function array_escape($array)
{
	foreach($array as $k=>$v){
		   $key = addslashes($k);
	$array[$key] = addslashes($v);
	}
	return $array;
}

?>