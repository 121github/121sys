<?php 
function linkedin_id_from_url($url) {
	//if an ID is given then we just return the ID;
if(preg_match('/^[0-9]{8}/',trim($url))){
return $url;	
}
//if no ID is found we assume its a linkedin url so we have to get the id from it

	//first get the id string
 preg_match('/id=[0-9]{8}/',$url,$matches);
   if(count($matches)>0){
   $id = str_replace("id=","",$matches[0]);
    return trim($id);
   } else {
	 return false; }
	 
}


function numbers_only($number){
	return preg_replace('/\D/', '', $number);
	
}
?>