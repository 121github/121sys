<?php 
function loadFile($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
	
	//debugging curl
	//curl_setopt($ch, CURLOPT_VERBOSE, true);
	//$verbose = fopen('php://temp', 'rw+');	
	//curl_setopt($ch, CURLOPT_STDERR, $verbose);
	
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}
?>