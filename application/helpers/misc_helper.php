<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function minify($path,$files,$project_version){
	$src = base_url()."min/";
	$src .= "b=$path&f=".implode(",",array_unique($files));
	return $src."&v=".$project_version;
}


 function custom_formats(){
return array("table","table","list");	
}

function custom_fields($type=false){
if($type=="strings"){
return array("c1","c2","c3","c4","c5","c6","c7","c8","c9","c10");
}
if($type=="dates"){
return array("d1","d2","d3","d4","d5","d6","d7","d8","d9","d10");	
}
if($type=="datetimes"){
return array("dt1","dt2","dt3","dt4","dt5","dt6","dt7","dt8","dt9","dt10");	
}
if($type=="numbers"){
return array("n1","n2","n3","n4","n5","n6","n7","n8","n9","n10");	
}	
return array("c1","c2","c3","c4","c5","c6","c7","c8","c9","c10","d1","d2","d3","d4","d5","d6","d7","d8","d9","d10","dt1","dt2","dt3","dt4","dt5","dt6","dt7","dt8","dt9","dt10","n1","n2","n3","n4","n5","n6","n7","n8","n9","n10");	
}

function get_color($name) {
	$hash = md5($name);

	$color1 = hexdec(substr($hash, 8, 2));
	$color2 = hexdec(substr($hash, 4, 2));
	$color3 = hexdec(substr($hash, 0, 2));
	if($color1 < 128) $color1 += 128;
	if($color2 < 128) $color2 += 128;
	if($color3 < 128) $color3 += 128;
	
	return dechex($color1) . dechex($color2) . dechex($color3);
}


function convertToHoursMins($time, $format = '%d:%d') {
    settype($time, 'integer');
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

function arraySort_distance($a, $b) {
    return $a['distance'] - $b['distance'];
}

function closest_strings($str,$array,$tolerance=3){
$minStr = "";
$result = array();
foreach($array as $curStr) {
  $dis = levenshtein($str, $curStr);
  if ($dis <= $tolerance) {
    $result[] = $curStr;
  }
}
	return $result;
}

function join_array(){
	$array = array();
	$array['companies'] = array("companies");
	$array['company_addresses'] = array("companies","company_addresses");
	$array['contact_addresses'] = array("contacts","contact_addresses");
	$array['contact_locations'] = array("contacts","contact_addresses","contact_locations");
	$array['company_locations'] = array("companies","company_addresses","company_locations");	
	$array['contact_telephone'] = array("contacts","contact_telephone");
	$array['company_telephone'] = array("companies","company_telephone");
	$array['clients'] = array("campaigns","clients");
	$array['campaign_types'] = array("campaigns","campaign_types");
	$array['ownership'] = array("ownership","ownership_users");
	$array['subsectors'] = array("companies","company_subsectors","subsectors");
	$array['sectors'] = array("companies","company_subsectors","subsectors","sectors");
	$array['ownership_users'] = array("ownership","ownership_users");
	$array['appointment_users'] = array("appointments","appointment_users");
	$array['appointment_attendees'] = array("appointments","appointment_attendees");
	$array['appointment_contacts'] = array("appointments","appointment_contacts","appointment_addresses","appointment_telephones");
	$array['appointment_types'] = array("appointments","appointment_types");
	$array['record_comments'] = array("record_comments");
	$array['progress_description'] = array("progress_description");
	$array['sticky_notes'] = array("sticky_notes");
	$array['all_notes'] = array("sticky_notes","record_comments");
	$array['all_addresses'] = array("all_contacts","all_companies","all_addresses");
	$array['all_telephones'] = array("all_contacts","all_companies","all_telephones");
	$array['surveys'] = array("survey_answers","survey_info","answers_to_options","answer_notes","questions","questions_to_categories","question_options","surveys_to_questions");
	$array['survey_user'] = array("survey_user");
	$array['survey_locations'] = array("survey_contacts","survey_addresses","survey_locations");
	$array['survey_nps'] = array("survey_nps");
	return $array;
}

function table_joins(){
		$join = array();
		$join['records'] = "";
		$join['all_contacts'] = "left join contacts all_contacts on all_contacts.urn = r.urn ";
		$join['all_companies'] = "left join companies all_companies on all_companies.urn = r.urn ";
		$join['all_addresses'] = "left join company_addresses acoma on acoma.company_id = all_companies.company_id left join contact_addresses acona on acona.contact_id = all_contacts.contact_id ";
		$join['all_telephones'] = "left join company_telephone acomt on acomt.company_id = all_companies.company_id left join contact_telephone acont on acont.contact_id = all_contacts.contact_id ";
		
		$join['appointment_contacts'] = "left join contacts appointment_contacts on appointment_contacts.contact_id = appointments.contact_id left join contact_telephone appointment_telephone on appointment_telephone.contact_id = appointments.contact_id ";
		$join['history'] = "left join history h on h.urn = r.urn ";
		$join['history_campaign'] = "left join campaigns hist_camp on h.campaign_id = hist_camp.campaign_id ";
		$join['history_users'] = " left join users hu on h.user_id = hu.user_id ";
		$join['history_outcomes'] = " left join outcomes ho on h.outcome_id = ho.outcome_id ";
		$join['history_reasons'] = " left join outcome_reasons hor on hor.outcome_reason_id = h.outcome_reason_id ";
		$join['history_contacts'] = " left join contacts hc on hc.contact_id = h.contact_id ";
		$join['history_groups'] = " left join user_groups hg on hg.group_id = h.group_id ";
		$join['history_teams'] = " left join teams ht on ht.team_id = h.team_id ";
		
		$join['client_refs'] = " left join client_refs cref on cref.urn = r.urn ";
		$join['progress_description'] = " left join progress_description on r.progress_id = progress_description.progress_id ";
		$join['data_pots'] = " left join data_pots dp on r.pot_id = dp.pot_id ";
	    $join['record_planner'] = " left join (select urn,location_id,start_date,record_planner_id,postcode,user_id from record_planner where date(start_date)>=curdate() and planner_status = 1)rp on rp.urn = r.urn ";
        $join['record_planner_user'] = " left join users rpu on rpu.user_id = rp.user_id ";
        $join['appointments'] = " left join appointments a on a.urn = r.urn  ";
        $join['companies'] = " left join companies com on com.urn = r.urn ";
        $join['company_addresses'] = " left join company_addresses coma on coma.company_id = com.company_id and coma.`primary` = 1 ";
       
        $join['contacts'] = " left join contacts con on con.urn = r.urn and con.`primary` = 1 ";
        $join['contact_addresses'] = " left join contact_addresses cona on cona.contact_id = con.contact_id and cona.`primary` = 1 ";
		$join['branch'] = " left join branch branch on branch.branch_id = r.branch_id ";
		$join['branch_regions'] = " left join branch_regions branch_regions on branch_regions.region_id = r.region_id ";
        $join['outcomes'] = " left join outcomes o on o.outcome_id = r.outcome_id ";
        $join['campaigns'] = " left join campaigns camp on camp.campaign_id = r.campaign_id ";
        $join['ownership'] = " left join ownership ow on ow.urn = r.urn ";
        $join['ownership_users'] = " left join users owu on ow.user_id = owu.user_id ";

		$join['status_list'] = " left join status_list sl on sl.record_status_id = r.record_status ";		
		$join['park_codes'] = " left join park_codes pc on pc.parked_code = r.parked_code ";
		$join['surveys'] = " left join surveys surv on surv.urn = r.urn ";
		$join['record_details'] = " left join record_details rd on rd.urn = r.urn ";
		$join['company_subsectors'] = " left join company_subsectors comsubsec on comsubsec.company_id = com.company_id ";
		$join['subsectors'] = " left join subsectors subsec on subsec.subsector_id = comsubsec.subsector_id ";		
		$join['sectors'] = " left join sectors sec on sec.sector_id = subsec.subsector_id ";	
		$join['contact_telephone']  = " left join contact_telephone cont on cont.contact_id = con.contact_id ";
		$join['company_telephone'] = " left join company_telephone comt on comt.company_id = com.company_id ";
		$join['contact_addresses']  = " left join contact_addresses cona on cona.contact_id = con.contact_id ";
		$join['company_addresses'] = " left join company_addresses coma on coma.company_id = com.company_id ";
		$join['data_sources'] = " left join data_sources ds on ds.source_id = r.source_id ";
		$join['clients'] = " left join clients cli on cli.client_id = camp.client_id ";
		$join['campaign_types'] = " left join campaign_types campt on campt.campaign_type_id = camp.campaign_type_id ";
		$join['company_locations'] = " left JOIN locations company_locations ON (coma.location_id = company_locations.location_id) ";
        $join['contact_locations'] = " left JOIN locations contact_locations ON (cona.location_id = contact_locations.location_id) ";
		$join['appointments'] = " left JOIN appointments a on a.urn = r.urn ";
		$join['appointment_users'] = " left JOIN users appointment_users ON appointment_users.user_id = a.created_by ";
		$join['appointment_attendees'] = " 	left join appointment_attendees aa on aa.appointment_id = a.appointment_id left join users au on au.user_id = aa.user_id ";
		$join['appointment_locations'] = " left JOIN locations appointment_locations on a.location_id =  appointment_locations.location_id ";
		$join['appointment_types'] = " left JOIN appointment_types using(appointment_type_id) ";
		$join['record_comments'] = " left join record_comments on record_comments.urn = r.urn ";
		$join['sticky_notes'] = " left join sticky_notes on sticky_notes.urn = r.urn ";
		$join['surveys'] = " left join surveys on surveys.urn = r.urn ";
		$join['survey_info'] = " left join survey_info si on si.survey_info_id = surveys.survey_info_id ";
		$join['survey_answers'] = " left join survey_answers on survey_answers.survey_id = surveys.survey_id ";
		$join['questions'] = " left join questions q on survey_answers.question_id = q.question_id ";
		$join['answer_notes'] = " left join answer_notes on answer_notes.answer_id = survey_answers.answer_id ";
		$join['answers_to_options'] = " left join answers_to_options on answers_to_options.answer_id = survey_answers.answer_id ";
		$join['question_options'] = " left join question_options qo on answers_to_options.option_id = qo.option_id ";
		$join['survey_contacts'] = " left join contacts survey_contacts on survey_contacts.contact_id = surveys.contact_id ";
		$join['survey_addresses'] = " left join contact_addresses survey_addresses on survey_addresses.contact_id = survey_contacts.contact_id ";
		$join['survey_locations'] = " left join locations survey_locations on survey_locations.location_id = survey_addresses.address_id ";
		$join['survey_nps'] = " left join (select answer nps_score,survey_id  from survey_answers join questions using(question_id) where nps_question = 1) survey_nps on surveys.survey_id = survey_nps.survey_id ";
		$join['survey_user'] = " left join users survey_user on survey_user.user_id = surveys.user_id ";

		
		
		
		
		
		return $join;
}

function lines_to_list($lines = false, $type = false)
{
    if (strpos($lines, ",") !== false) {
        $seperator = ",";
    } else {
        $seperator = "\n";
    }

    if ($type == "backquote") {
        echo $quote = "`";
    } else if ($type == "singlequote") {
        echo $quote = "'";
    } else if ($type == "double") {
        echo $quote = '"';
    } else {
        $quote = "";
    }


    return explode($seperator, trim($lines));
}


function color_name_to_hex($color_name)
{
    // standard 147 HTML color names
    $colors = array(
        'aliceblue' => 'F0F8FF',
        'antiquewhite' => 'FAEBD7',
        'aqua' => '00FFFF',
        'aquamarine' => '7FFFD4',
        'azure' => 'F0FFFF',
        'beige' => 'F5F5DC',
        'bisque' => 'FFE4C4',
        'black' => '000000',
        'blanchedalmond ' => 'FFEBCD',
        'blue' => '0000FF',
        'blueviolet' => '8A2BE2',
        'brown' => 'A52A2A',
        'bronze' => 'FF6600',
        'burlywood' => 'DEB887',
        'cadetblue' => '5F9EA0',
        'chartreuse' => '7FFF00',
        'chocolate' => 'D2691E',
        'coral' => 'FF7F50',
        'cornflowerblue' => '6495ED',
        'cornsilk' => 'FFF8DC',
        'crimson' => 'DC143C',
        'cyan' => '00FFFF',
        'darkblue' => '00008B',
        'darkcyan' => '008B8B',
        'darkgoldenrod' => 'B8860B',
        'darkgray' => 'A9A9A9',
        'darkgreen' => '006400',
        'darkgrey' => 'A9A9A9',
        'darkkhaki' => 'BDB76B',
        'darkmagenta' => '8B008B',
        'darkolivegreen' => '556B2F',
        'darkorange' => 'FF8C00',
        'darkorchid' => '9932CC',
        'darkred' => '8B0000',
        'darksalmon' => 'E9967A',
        'darkseagreen' => '8FBC8F',
        'darkslateblue' => '483D8B',
        'darkslategray' => '2F4F4F',
        'darkslategrey' => '2F4F4F',
        'darkturquoise' => '00CED1',
        'darkviolet' => '9400D3',
        'deeppink' => 'FF1493',
        'deepskyblue' => '00BFFF',
        'dimgray' => '696969',
        'dimgrey' => '696969',
        'dodgerblue' => '1E90FF',
        'firebrick' => 'B22222',
        'floralwhite' => 'FFFAF0',
        'forestgreen' => '228B22',
        'fuchsia' => 'FF00FF',
        'gainsboro' => 'DCDCDC',
        'ghostwhite' => 'F8F8FF',
        'gold' => 'FFCC00',
        'goldenrod' => 'DAA520',
        'gray' => '808080',
        'green' => '008000',
        'greenyellow' => 'ADFF2F',
        'grey' => '808080',
        'honeydew' => 'F0FFF0',
        'hotpink' => 'FF69B4',
        'indianred' => 'CD5C5C',
        'indigo' => '4B0082',
        'ivory' => 'FFFFF0',
        'khaki' => 'F0E68C',
        'lavender' => 'E6E6FA',
        'lavenderblush' => 'FFF0F5',
        'lawngreen' => '7CFC00',
        'lemonchiffon' => 'FFFACD',
        'lightblue' => 'ADD8E6',
        'lightcoral' => 'F08080',
        'lightcyan' => 'E0FFFF',
        'lightgoldenrodyellow' => 'FAFAD2',
        'lightgray' => 'D3D3D3',
        'lightgreen' => '90EE90',
        'lightgrey' => 'D3D3D3',
        'lightpink' => 'FFB6C1',
        'lightsalmon' => 'FFA07A',
        'lightseagreen' => '20B2AA',
        'lightskyblue' => '87CEFA',
        'lightslategray' => '778899',
        'lightslategrey' => '778899',
        'lightsteelblue' => 'B0C4DE',
        'lightyellow' => 'FFFFE0',
        'lime' => '00FF00',
        'limegreen' => '32CD32',
        'linen' => 'FAF0E6',
        'magenta' => 'FF00FF',
        'maroon' => '800000',
        'mediumaquamarine' => '66CDAA',
        'mediumblue' => '0000CD',
        'mediumorchid' => 'BA55D3',
        'mediumpurple' => '9370D0',
        'mediumseagreen' => '3CB371',
        'mediumslateblue' => '7B68EE',
        'mediumspringgreen' => '00FA9A',
        'mediumturquoise' => '48D1CC',
        'mediumvioletred' => 'C71585',
        'midnightblue' => '191970',
        'mintcream' => 'F5FFFA',
        'mistyrose' => 'FFE4E1',
        'moccasin' => 'FFE4B5',
        'navajowhite' => 'FFDEAD',
        'navy' => '000080',
        'oldlace' => 'FDF5E6',
        'olive' => '808000',
        'olivedrab' => '6B8E23',
        'orange' => 'FFA500',
        'orangered' => 'FF4500',
        'orchid' => 'DA70D6',
        'palegoldenrod' => 'EEE8AA',
        'palegreen' => '98FB98',
        'paleturquoise' => 'AFEEEE',
        'palevioletred' => 'DB7093',
        'papayawhip' => 'FFEFD5',
        'peachpuff' => 'FFDAB9',
        'peru' => 'CD853F',
        'pink' => 'FFC0CB',
        'platinum' => '336699',
        'plum' => 'DDA0DD',
        'powderblue' => 'B0E0E6',
        'purple' => '800080',
        'red' => 'FF0000',
        'rosybrown' => 'BC8F8F',
        'royalblue' => '4169E1',
        'saddlebrown' => '8B4513',
        'salmon' => 'FA8072',
        'sandybrown' => 'F4A460',
        'seagreen' => '2E8B57',
        'seashell' => 'FFF5EE',
        'sienna' => 'A0522D',
        'silver' => '666666',
        'skyblue' => '87CEEB',
        'slateblue' => '6A5ACD',
        'slategray' => '708090',
        'slategrey' => '708090',
        'snow' => 'FFFAFA',
        'springgreen' => '00FF7F',
        'steelblue' => '4682B4',
        'tan' => 'D2B48C',
        'teal' => '008080',
        'thistle' => 'D8BFD8',
        'tomato' => 'FF6347',
        'turquoise' => '40E0D0',
        'violet' => 'EE82EE',
        'wheat' => 'F5DEB3',
        'white' => 'FFFFFF',
        'whitesmoke' => 'F5F5F5',
        'yellow' => 'FFFF00',
        'yellowgreen' => '9ACD32');

    $color_name = strtolower($color_name);
    if (isset($colors[$color_name])) {
        return ($colors[$color_name]);
    } else {
        return ($color_name);
    }
}


function array_non_empty_items($input)
{
    // If it is an element, then just return it
    if (!is_array($input)) {
        return $input;
    }

    $non_empty_items = array();

    foreach ($input as $key => $value) {
        // Ignore empty cells
        if ($value) {
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

function format_mobile($mobile) {

    $pattern = '/^(447|\+447|00447|0447|07)/';
    $replacement = '447';

    $mobile = preg_replace($pattern, $replacement, $mobile);

    return $mobile;
}


?>