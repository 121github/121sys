<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('address_format')) {
	function addressFormat($array=array(),$seperator=", "){
		$allowed = array("add1","add2","add3","add4","city","county","country","postcode");
		$address = "";
	foreach($array as $k=>$v){
	if(!in_array($k,$allowed)||empty($v)){
		unset($array[$k]);
	}
	}
	$address =implode($seperator,$array);
	return $address;
}
}

if (!function_exists('postcodeFormat')) {
//format postcode
function postcodeFormat($postcode)
{
    //trim and remove spaces
    $cleanPostcode = preg_replace("/[^A-Za-z0-9]/", '', $postcode);
 
    //make uppercase
    $cleanPostcode = strtoupper($cleanPostcode);
 
    //if 5 charcters, insert space after the 2nd character
    if(strlen($cleanPostcode) == 5)
    {
        $postcode = substr($cleanPostcode,0,2) . " " . substr($cleanPostcode,2,3);
    }
 
    //if 6 charcters, insert space after the 3rd character
    elseif(strlen($cleanPostcode) == 6)
    {
        $postcode = substr($cleanPostcode,0,3) . " " . substr($cleanPostcode,3,3);
    }
 
 
    //if 7 charcters, insert space after the 4th character
    elseif(strlen($cleanPostcode) == 7)
    {
        $postcode = substr($cleanPostcode,0,4) . " " . substr($cleanPostcode,4,3);
    }
 
    return $postcode;
}
}


if (!function_exists('postcodeCheckFormat')) {
 function postcodeCheckFormat($postcode) {

//--------------------------------------------------
// Clean up the user input

$postcode = strtoupper($postcode);
$postcode = preg_replace('/[^A-Z0-9]/', '', $postcode);
$postcode = preg_replace('/([A-Z0-9]{3})$/', ' \1', $postcode);
$postcode = trim($postcode);

//--------------------------------------------------
// Check that the submitted value is a valid
// British postcode: AN NAA | ANN NAA | AAN NAA |
// AANN NAA | ANA NAA | AANA NAA

if (preg_match('/^[a-z](\d[a-z\d]?|[a-z]\d[a-z\d]?) \d[a-z]{2}$/i', $postcode)) {
return $postcode;
} else {
return NULL;
}

} 
}


if (!function_exists('distance')) {

    function distance($lat1, $lon1, $lat2, $lon2, $unit = null) {

        $theta = $lon1 - $lon2;
        $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist  = acos($dist);
        $dist  = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit  = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

}

if (!function_exists('postcode_to_coords')) {

    function postcode_to_coords($postcode) {
        $_SESSION['current_postcode'] = $postcode;
        //Contact the google maps api to get the lat & lng from the postcode
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($postcode) . ',uk&sensor=false';
        $json = json_decode(file_get_contents($url));
		if(isset($json->error_message)){
			return array("error"=>$json->error_message);
		}
		
		else if (isset($json->results[0])) {
			return array(
					'lat' => $json->results[0]->geometry->location->lat,
					'lng' => $json->results[0]->geometry->location->lng
			);
		}
		else {
			return array(
					'error' => $json->status,
			);
		}
    }

}

if (!function_exists('get_postcode_data')) {

    function get_postcode_data($postcode) {
		$postcode = str_replace(" ","",$postcode);
        //Contact the google maps api to get the lat & lng from the postcode
        $url = 'http://it.121system.com/api/postcodeios/' . $postcode . '.json';
        $json = json_decode(file_get_contents($url),true);
		
		if (isset($json['latitude'])) {
			return $json;
		}
		else {
			return array(
					'error' => "Could not get postcode data from the API",
					'url' => $url
			);
		}
    }

}

if (!function_exists('validate_postcode')) {

    function validate_postcode(&$toCheck) {

        // Permitted letters depend upon their position in the postcode.
        $alpha1 = "[abcdefghijklmnoprstuwyz]";                          // Character 1
        $alpha2 = "[abcdefghklmnopqrstuvwxy]";                          // Character 2
        $alpha3 = "[abcdefghjkpmnrstuvwxy]";                            // Character 3
        $alpha4 = "[abehmnprvwxy]";                                     // Character 4
        $alpha5 = "[abdefghjlnpqrstuwxyz]";                             // Character 5
        $BFPOa5 = "[abdefghjlnpqrst]{1}";                               // BFPO character 5
        $BFPOa6 = "[abdefghjlnpqrstuwzyz]{1}";                          // BFPO character 6
        // Expression for BF1 type postcodes 
        $pcexp[0] = '/^(bf1)([[:space:]]{0,})([0-9]{1}' . $BFPOa5 . $BFPOa6 . ')$/';

        // Expression for postcodes: AN NAA, ANN NAA, AAN NAA, and AANN NAA with a space
        $pcexp[1] = '/^(' . $alpha1 . '{1}' . $alpha2 . '{0,1}[0-9]{1,2})([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})$/';

        // Expression for postcodes: ANA NAA
        $pcexp[2] = '/^(' . $alpha1 . '{1}[0-9]{1}' . $alpha3 . '{1})([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})$/';

        // Expression for postcodes: AANA NAA
        $pcexp[3] = '/^(' . $alpha1 . '{1}' . $alpha2 . '{1}[0-9]{1}' . $alpha4 . ')([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})$/';

        // Exception for the special postcode GIR 0AA
        $pcexp[4] = '/^(gir)([[:space:]]{0,})(0aa)$/';

        // Standard BFPO numbers
        $pcexp[5] = '/^(bfpo)([[:space:]]{0,})([0-9]{1,4})$/';

        // c/o BFPO numbers
        $pcexp[6] = '/^(bfpo)([[:space:]]{0,})(c\/o([[:space:]]{0,})[0-9]{1,3})$/';

        // Overseas Territories
        $pcexp[7] = '/^([a-z]{4})([[:space:]]{0,})(1zz)$/';

        // Anquilla
        $pcexp[8] = '/^ai-2640$/';

        // Load up the string to check, converting into lowercase
        $postcode = strtolower($toCheck);

        // Assume we are not going to find a valid postcode
        $valid = false;

        // Check the string against the six types of postcodes
        foreach ($pcexp as $regexp) {

            if (preg_match($regexp, $postcode, $matches)) {

                // Load new postcode back into the form element  
                $postcode = strtoupper($matches[1] . ' ' . $matches [3]);

                // Take account of the special BFPO c/o format
                $postcode = preg_replace('/C\/O([[:space:]]{0,})/', 'c/o ', $postcode);

                // Take acount of special Anquilla postcode format (a pain, but that's the way it is)
                if (preg_match($pcexp[7], strtolower($toCheck), $matches))
                    $postcode = 'AI-2640';

                // Remember that we have found that the code is valid and break from loop
                $valid = true;
                break;
            }
        }

        // Return with the reformatted valid postcode in uppercase if the postcode was 
        // valid
        if ($valid) {
            $toCheck = $postcode;
            return true;
        } else
            return false;
    }
function postcode_from_string($string){
$pattern = "/((GIR 0AA)|((([A-PR-UWYZ][0-9][0-9]?)|(([A-PR-UWYZ][A-HK-Y][0-9][0-9]?)|(([A-PR-UWYZ][0-9][A-HJKSTUW])|([A-PR-UWYZ][A-HK-Y][0-9][ABEHMNPRVWXY])))) [0-9][ABD-HJLNP-UW-Z]{2}))/i";
preg_match($pattern, $string, $matches);

if(isset($matches[0])){
return postcodeFormat($matches[0]);
}  else { 
return ""; 
}	

}

}


/* End of file location_helper.php */
/* Location: ./application/helpers/location_helper.php */

