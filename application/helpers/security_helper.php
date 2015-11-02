<?php 

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function array_escape($array)
{
	foreach($array as $k=>$v){
		if(!is_array($k)&&!is_array($v)){
		   $key = addslashes($k);
	$array[$key] = addslashes($v);
		}
	}
	return $array;
}

?>