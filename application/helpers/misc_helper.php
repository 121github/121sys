<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function lines_to_list($lines=false,$type=false){
if(strpos($lines,",")!==false){
	$seperator = ",";
} else {
	$seperator = "\n";
}
	
if($type=="backquote"){
echo $quote = "`";	
} else if($type=="singlequote"){
echo $quote = "'";	
} else if($type=="double"){
echo $quote = '"';	
} else {
$quote = "";	
}

	
return explode($seperator,trim($lines));	
}


function color_name_to_hex($color_name)
{
    // standard 147 HTML color names
    $colors  =  array(
        'aliceblue'=>'F0F8FF',
        'antiquewhite'=>'FAEBD7',
        'aqua'=>'00FFFF',
        'aquamarine'=>'7FFFD4',
        'azure'=>'F0FFFF',
        'beige'=>'F5F5DC',
        'bisque'=>'FFE4C4',
        'black'=>'000000',
        'blanchedalmond '=>'FFEBCD',
        'blue'=>'0000FF',
        'blueviolet'=>'8A2BE2',
        'brown'=>'A52A2A',
		'bronze'=>'A52A2A',
        'burlywood'=>'DEB887',
        'cadetblue'=>'5F9EA0',
        'chartreuse'=>'7FFF00',
        'chocolate'=>'D2691E',
        'coral'=>'FF7F50',
        'cornflowerblue'=>'6495ED',
        'cornsilk'=>'FFF8DC',
        'crimson'=>'DC143C',
        'cyan'=>'00FFFF',
        'darkblue'=>'00008B',
        'darkcyan'=>'008B8B',
        'darkgoldenrod'=>'B8860B',
        'darkgray'=>'A9A9A9',
        'darkgreen'=>'006400',
        'darkgrey'=>'A9A9A9',
        'darkkhaki'=>'BDB76B',
        'darkmagenta'=>'8B008B',
        'darkolivegreen'=>'556B2F',
        'darkorange'=>'FF8C00',
        'darkorchid'=>'9932CC',
        'darkred'=>'8B0000',
        'darksalmon'=>'E9967A',
        'darkseagreen'=>'8FBC8F',
        'darkslateblue'=>'483D8B',
        'darkslategray'=>'2F4F4F',
        'darkslategrey'=>'2F4F4F',
        'darkturquoise'=>'00CED1',
        'darkviolet'=>'9400D3',
        'deeppink'=>'FF1493',
        'deepskyblue'=>'00BFFF',
        'dimgray'=>'696969',
        'dimgrey'=>'696969',
        'dodgerblue'=>'1E90FF',
        'firebrick'=>'B22222',
        'floralwhite'=>'FFFAF0',
        'forestgreen'=>'228B22',
        'fuchsia'=>'FF00FF',
        'gainsboro'=>'DCDCDC',
        'ghostwhite'=>'F8F8FF',
        'gold'=>'FFD700',
        'goldenrod'=>'DAA520',
        'gray'=>'808080',
        'green'=>'008000',
        'greenyellow'=>'ADFF2F',
        'grey'=>'808080',
        'honeydew'=>'F0FFF0',
        'hotpink'=>'FF69B4',
        'indianred'=>'CD5C5C',
        'indigo'=>'4B0082',
        'ivory'=>'FFFFF0',
        'khaki'=>'F0E68C',
        'lavender'=>'E6E6FA',
        'lavenderblush'=>'FFF0F5',
        'lawngreen'=>'7CFC00',
        'lemonchiffon'=>'FFFACD',
        'lightblue'=>'ADD8E6',
        'lightcoral'=>'F08080',
        'lightcyan'=>'E0FFFF',
        'lightgoldenrodyellow'=>'FAFAD2',
        'lightgray'=>'D3D3D3',
        'lightgreen'=>'90EE90',
        'lightgrey'=>'D3D3D3',
        'lightpink'=>'FFB6C1',
        'lightsalmon'=>'FFA07A',
        'lightseagreen'=>'20B2AA',
        'lightskyblue'=>'87CEFA',
        'lightslategray'=>'778899',
        'lightslategrey'=>'778899',
        'lightsteelblue'=>'B0C4DE',
        'lightyellow'=>'FFFFE0',
        'lime'=>'00FF00',
        'limegreen'=>'32CD32',
        'linen'=>'FAF0E6',
        'magenta'=>'FF00FF',
        'maroon'=>'800000',
        'mediumaquamarine'=>'66CDAA',
        'mediumblue'=>'0000CD',
        'mediumorchid'=>'BA55D3',
        'mediumpurple'=>'9370D0',
        'mediumseagreen'=>'3CB371',
        'mediumslateblue'=>'7B68EE',
        'mediumspringgreen'=>'00FA9A',
        'mediumturquoise'=>'48D1CC',
        'mediumvioletred'=>'C71585',
        'midnightblue'=>'191970',
        'mintcream'=>'F5FFFA',
        'mistyrose'=>'FFE4E1',
        'moccasin'=>'FFE4B5',
        'navajowhite'=>'FFDEAD',
        'navy'=>'000080',
        'oldlace'=>'FDF5E6',
        'olive'=>'808000',
        'olivedrab'=>'6B8E23',
        'orange'=>'FFA500',
        'orangered'=>'FF4500',
        'orchid'=>'DA70D6',
        'palegoldenrod'=>'EEE8AA',
        'palegreen'=>'98FB98',
        'paleturquoise'=>'AFEEEE',
        'palevioletred'=>'DB7093',
        'papayawhip'=>'FFEFD5',
        'peachpuff'=>'FFDAB9',
        'peru'=>'CD853F',
        'pink'=>'FFC0CB',
		'platinum'=>'F8F8FF',
        'plum'=>'DDA0DD',
        'powderblue'=>'B0E0E6',
        'purple'=>'800080',
        'red'=>'FF0000',
        'rosybrown'=>'BC8F8F',
        'royalblue'=>'4169E1',
        'saddlebrown'=>'8B4513',
        'salmon'=>'FA8072',
        'sandybrown'=>'F4A460',
        'seagreen'=>'2E8B57',
        'seashell'=>'FFF5EE',
        'sienna'=>'A0522D',
        'silver'=>'C0C0C0',
        'skyblue'=>'87CEEB',
        'slateblue'=>'6A5ACD',
        'slategray'=>'708090',
        'slategrey'=>'708090',
        'snow'=>'FFFAFA',
        'springgreen'=>'00FF7F',
        'steelblue'=>'4682B4',
        'tan'=>'D2B48C',
        'teal'=>'008080',
        'thistle'=>'D8BFD8',
        'tomato'=>'FF6347',
        'turquoise'=>'40E0D0',
        'violet'=>'EE82EE',
        'wheat'=>'F5DEB3',
        'white'=>'FFFFFF',
        'whitesmoke'=>'F5F5F5',
        'yellow'=>'FFFF00',
        'yellowgreen'=>'9ACD32');

    $color_name = strtolower($color_name);
    if (isset($colors[$color_name]))
    {
        return ($colors[$color_name]);
    }
    else
    {
        return ($color_name);
    }
}


function array_non_empty_items($input) {
    // If it is an element, then just return it
    if (!is_array($input)) {
      return $input;
    }

    $non_empty_items = array();

    foreach ($input as $key => $value) {
      // Ignore empty cells
      if($value) {
        // Use recursion to evaluate cells
        $non_empty_items[$key] = array_non_empty_items($value);
      }
    }

    // Finally return the array without empty items
    return $non_empty_items;
  }


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