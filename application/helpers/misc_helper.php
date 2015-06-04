<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function linkedin_id_from_url($url)
{
    //if an ID is given then we just return the ID;
    if (preg_match('/^[0-9]{8}/', trim($url))) {
        return $url;
    }
//if no ID is found we assume its a linkedin url so we have to get the id from it

    //first get the id string
    preg_match('/id=[0-9]{8}/', $url, $matches);
    if (count($matches) > 0) {
        $id = str_replace("id=", "", $matches[0]);
        return trim($id);
    } else {
        return false;
    }

}


function numbers_only($number)
{
    return preg_replace('/\D/', '', $number);

}


if (!function_exists('genColorCodeFromText')) {
    //get color from text
    function genColorCodeFromText($text, $min_brightness = 100, $spec = 10)
    {
        // Check inputs
        if (!is_int($min_brightness)) throw new Exception("$min_brightness is not an integer");
        if (!is_int($spec)) throw new Exception("$spec is not an integer");
        if ($spec < 2 or $spec > 10) throw new Exception("$spec is out of range");
        if ($min_brightness < 0 or $min_brightness > 255) throw new Exception("$min_brightness is out of range");
        $hash = md5($text); //Gen hash of text
        $colors = array();
        for ($i = 0; $i < 3; $i++)
            $colors[$i] = max(array(round(((hexdec(substr($hash, $spec * $i, $spec))) / hexdec(str_pad('', $spec, 'F'))) * 255), $min_brightness)); //convert hash into 3 decimal values between 0 and 255
        if ($min_brightness > 0) //only check brightness requirements if min_brightness is about 100
            while (array_sum($colors) / 3 < $min_brightness) //loop until brightness is above or equal to min_brightness
                for ($i = 0; $i < 3; $i++)
                    $colors[$i] += 10;    //increase each color by 10
        $output = '';
        for ($i = 0; $i < 3; $i++)
            $output .= str_pad(dechex($colors[$i]), 2, 0, STR_PAD_LEFT); //convert each color to hex and append to output
        return '#' . $output;
    }

}

?>