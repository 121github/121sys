<?php 
function loadFile($url,$apiKey=false) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
	if($apiKey){
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Authorization: ' . $apiKey
));
	}
	
	//debugging curl
	curl_setopt($ch, CURLOPT_VERBOSE, true);	
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}
?>