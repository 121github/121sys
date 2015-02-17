<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Calendar_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
	
	public function genColorCodeFromText($text,$min_brightness=100,$spec=10)
{
// Check inputs
if(!is_int($min_brightness)) throw new Exception("$min_brightness is not an integer");
if(!is_int($spec)) throw new Exception("$spec is not an integer");
if($spec < 2 or $spec > 10) throw new Exception("$spec is out of range");
if($min_brightness < 0 or $min_brightness > 255) throw new Exception("$min_brightness is out of range");
$hash = md5($text); //Gen hash of text
$colors = array();
for($i=0;$i<3;$i++)
$colors[$i] = max(array(round(((hexdec(substr($hash,$spec*$i,$spec)))/hexdec(str_pad('',$spec,'F')))*255),$min_brightness)); //convert hash into 3 decimal values between 0 and 255
if($min_brightness > 0) //only check brightness requirements if min_brightness is about 100
while( array_sum($colors)/3 < $min_brightness ) //loop until brightness is above or equal to min_brightness
for($i=0;$i<3;$i++)
$colors[$i] += 10;	//increase each color by 10
$output = '';
for($i=0;$i<3;$i++)
$output .= str_pad(dechex($colors[$i]),2,0,STR_PAD_LEFT); //convert each color to hex and append to output
return '#'.$output;
}
	
    public function get_events($options){
		$join = " left join records using(urn) ";
		$where = "";
		$having = "";
		$select_distance = "";
		$start = $options['start'];
		$end = $options['end'];
		$order_by = "";
		if(isset($options['modal'])&&!empty($options['distance'])){
			$order_by = " order by distance";
		}
		
		$join .= " left join companies using(urn) left join campaigns using(campaign_id) left join campaign_types using(campaign_type_id) left join appointment_attendees using(appointment_id) left join users using(user_id) ";
		if(!empty($options['postcode'])&&!empty($options['distance'])){
			$distance = intval($options['distance'])*1.1515;
			$this->db->where("postcode",$options['postcode']);
			$geodata = $this->db->get("uk_postcodes");
			if($geodata->num_rows()>0){
			$coords = $geodata->row_array();	
			} else {
			$coords = postcode_to_coords($options['postcode']);
			}
			$join .= " left join locations using(location_id) ";
			$having .= " having distance <= $distance";
			$select_distance .= ",(((ACOS(SIN((" .
              $coords['lat'] . "*PI()/180)) * SIN((lat*PI()/180))+COS((" .
              $coords['lat'] . "*PI()/180)) * COS((lat*PI()/180)) * COS(((" .
              $coords['lng'] . "- lng)*PI()/180))))*180/PI())*60) AS distance";
			
			 if (isset($coords['lat']) && isset($coords['lng'])) {
                        
                        $where .= " and ( ";
                        //Distance from the company or the contacts addresses
                        $where .= $coords['lat'] . " BETWEEN (lat-" . $distance . ") AND (lat+" . $distance . ")";
                        $where .= " and " . $coords['lng'] . " BETWEEN (lng-" . $distance . ") AND (lng+" . $distance . ")";
                        /*$where .= " and ((((
							ACOS(
								SIN(" . $coords['lat'] . "*PI()/180) * SIN(lat*PI()/180) +
								COS(" . $coords['lat'] . "*PI()/180) * COS(lat*PI()/180) * COS(((" . $coords['lng'] . " - lng)*PI()/180)
							)
						)*180/PI())*160*0.621371192)) <= " . $distance . ")";*/
                        
                        $where .= " )";
                    }
		}
		
		if(!empty($options['campaigns'])){
		$where .= " and campaign_id in(". implode(",",$options['campaigns']).")";
		}
		if(!empty($options['users'])){
		$where .= " and user_id in(". implode(",",$options['users']).")";
		}
		if(!empty($start)){
			$where .= " and date(`start`) >= '$start' ";
		}
		if(!empty($end)){
			$where .= " and date(`end`) <= '$end' ";
		}
		if(isset($_SESSION['current_campaign'])){
		$where .= "	and campaign_id = ".$_SESSION['current_campaign'];
		}
		if(isset($options['modal'])){
					$query = "select if(companies.name,'',companies.name) as title,appointment_id,`start`,`end`,users.name as user $select_distance from appointments $join where 1 $where $having $order_by";
					$this->firephp->log($query);
		} else {
		$query = "select appointments.urn,appointment_id,campaign_name,title,text,`start`,`end`,postcode,if(`status`='1','','Cancelled') as `status`,if(companies.name,'',companies.name) as company,users.name as user $select_distance from appointments $join where 1 $where $having $order_by";
		}
		$array = array();
		$users = array();
		foreach($this->db->query($query)->result_array() as $row){
			$array[$row['appointment_id']] = $row;
			$users[$row['appointment_id']][] = $row['user'];
		}
		
		foreach($array as $k => $row){
		$attendees="";
		if(isset($users[$k])){
		$attendees = implode(",",$users[$k]);
		}
		$array[$k]['attendeelist'] = (!empty($attendees)?$attendees:"No Attendees!");
		$array[$k]['color'] = $this->genColorCodeFromText($attendees);
		}
		//$this->firephp->log($array);
		return $array;
	}

public function get_postcode_from_urn($urn){
$query = "select if(campaign_type_id=1,cona.postcode,coma.postcode) postcode from records left join campaigns using(campaign_id) left join contacts using(urn) left join contact_addresses cona using(contact_id) left join companies using(urn) left join company_addresses coma using(company_id) where urn = '".intval($urn)."' and (cona.`primary`=1 or coma.`primary`=1) ";	
	$result = $this->db->query($query);
	if($result->num_rows()>0){
		$pc = $result->row(0)->postcode;;
		return $pc;
	}
}

}