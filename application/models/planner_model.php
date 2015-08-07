<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Planner_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function planner_data($count = false, $options = false)
    {

        $qry = "select
                    date_format(rp.`start_date`,'%d/%m/%Y') start,
					if(com.name is null,fullname,com.name) title,
                    if(planner_type=3,'Destination',if(planner_type=1,'Start',if(com.name is null,'na',com.name))) name,
					planner_type,
                    fullname,
                    u.name user,
                    u.user_id as attendee_id,
                    postcode,
                    lat,
                    lng,
                    rp.record_planner_id,
                    rp.location_id,
                    outcome,
                    records.urn,
                    date_format(records.date_updated,'%d/%m/%y') date_updated,
                    date_format(records.nextcall,'%d/%m/%y') nextcall,
                    rpr.start_add,
                    rpr.start_lat,
                    rpr.start_lng,
                    rpr.end_add,
                    rpr.end_lat,
                    rpr.end_lng,
                    rpr.distance,
                    rpr.duration,
                    rpr.travel_mode,
					rp.planner_type
                from record_planner rp
                  left join users u on u.user_id = rp.user_id
                  left join records using(urn)
				  left join client_refs using(urn)
				  left join campaigns using(campaign_id)
                  left join companies com using(urn)
                  left join contacts con using(urn)
                  left join locations using(location_id)
                  left join record_planner_route rpr using(record_planner_id)
                  left join outcomes using(outcome_id)";

        $where = $this->get_where($options);
        $qry .= $where;
        $qry .= "  group by record_planner_id order by case when rp.order_num is not null then rp.order_num else record_planner_id end asc ";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }

    private function get_where($options, $table_columns=array())
    {
        //the default condition in ever search query to stop people viewing campaigns they arent supposed to!
        $where = " where (campaign_id in({$_SESSION['campaign_access']['list']}) or campaign_id is null)";

        //User
        $where .= " and rp.user_id = {$_SESSION['user_id']} ";

        //Date
        if ($options['date']) {
            $where .= " and date(rp.start_date) = '" . $options['date'] . "' ";
        }

        //Check the bounds of the map
        //not needed for the planner
		/*if ($options['map'] == 'true' && $options['bounds']) {
            $where .= " and lat < " . $options['bounds']['neLat'] . " and lat > " . $options['bounds']['swLat'] . " and lng < " . $options['bounds']['neLng'] . " and lng > " . $options['bounds']['swLng'] . " ";
        }
		*/

        return $where;
    }

    public function add_record($urn=NULL, $date, $postcode, $user_id=false, $type=2, $order=20)
    {
		if(!$user_id){
		$user_id = 	$_SESSION['user_id'];
		}
		$location_id = $this->get_location_id($postcode);
		//add the location to the planner table
		$data = array("urn"=>$urn,"user_id"=>$user_id,"start_date"=>$date,"postcode"=>$postcode,"location_id"=>$location_id,"planner_status"=>1,"planner_type"=>$type,"order_num"=>$order);
		$this->db->insert("record_planner",$data);
		return $this->db->insert_id();
    }
		
	public function check_planner($urn,$user_id){
	$qry = "select urn from record_planner where planner_status = 1 and urn = '$urn' and user_id = '$user_id'";
	if($this->db->query($qry)->num_rows()){
		return true;
	}	
	}
	
    public function remove_record($urn,$user_id)
    {	
	$data = array("urn" => $urn, "user_id" => $user_id, "planner_status"=>1);
	$date = $this->db->get_where("record_planner",$data)->row()->start_date;
	
	$delete = "delete from record_planner_route where record_planner_id in(select record_planner_id from record_planner where start_date = '$date' and user_id = '$user_id')";
	$this->db->query($delete);
	$this->firephp->log($this->db->last_query());
    $this->db->delete("record_planner",$data);
    }

    public function save_record_order($record_list, $user_id, $date)
    {
        //Reset the order of all waypoints as null for this user and on this date
        $data = array(
            'order_num' => NULL
        );
        $this->db->where('user_id', $user_id);
        $this->db->where('date(start_date)', $date);
		$this->db->where('planner_type', '2');
        $this->db->update('record_planner', $data);
		

        //Set the order for the record_planners for this user and on this date
        $data = array();
        foreach ($record_list as $order_num => $record) {
            if (isset($record['record_planner'])) {
                array_push($data, array(
                    'record_planner_id' => $record['record_planner']['record_planner_id'],
                    'order_num' => $order_num+1
                ));
            }
        }
		//set the origin and destination items to the front and back of the route
		$qry = "update record_planner set order_num = 0 where date(start_date) = '$date' and user_id = '$user_id' and planner_type=1";
		$this->db->query($qry);
		$qry = "update record_planner set order_num = 100 where date(start_date) = '$date' and user_id = '$user_id' and planner_type=3";
		$this->db->query($qry);
       
	    if (!empty($data)) {
            $this->db->where('user_id', $user_id);
            $this->db->where('date(start_date)', $date);
            $this->db->update_batch('record_planner', $data, 'record_planner_id');
        }
    }
function get_location_id($postcode){
		$postcode = postcodeFormat($postcode);
        $this->db->where("postcode", $postcode);
		$this->db->join("locations","location_id=postcode_id","LEFT");
        $check_location = $this->db->get("uk_postcodes");
        if ($check_location->num_rows()) {
			$loc = $check_location->row();
			if(empty($loc->location_id)){
			$this->db->replace("locations", array("location_id" => $loc->postcode_id, 
			"lat" => $loc->lat, 
			"lng" => $loc->lng));
			$location_id = $this->db->insert_id();	
			} else {
			$location_id = $loc->location_id;	
			}
        } else {
            $coords = postcode_to_coords($postcode);
			if(isset($coords['lat'])){
            $this->db->insert("uk_postcodes", array("postcode" => $postcode, "lat" => $coords['lat'], "lng" => $coords['lng']));
            $location_id = $this->db->insert_id();
            $this->db->replace("locations", array("location_id" => $location_id, "lat" => $coords['lat'], "lng" => $coords['lng']));
			} else {
			$location_id = false;	
			}
        }
		return $location_id;
		
}

    public function save_record_route($record_list, $user_id, $date, $origin, $dest)
    {

        //Prepare the data
        $data = array();
		//clear the old origin / destination from the planner
		$delete = "delete from record_planner where date(start_date) = '$date' and user_id = $user_id and planner_type in(1,3)";
		$this->db->query($delete);
		$this->firephp->log($this->db->last_query());
		//insert the origin with type 1 (origin)
		$first = $this->add_record(NULL, $date, $origin, $user_id, $type=1, $order=0);
        foreach ($record_list as $order_num => $record) {
            if (isset($record['record_planner']['record_planner_id'])) {
			$planner_id = $record['record_planner']['record_planner_id'];
			} else {
			//if its the destination address then add it in the planner
			$planner_id = $this->add_record(NULL, $date, $dest,$user_id, $type=3, $order=100);
			}
                array_push($data, array(
                    'record_planner_id' => $planner_id,
                    'start_add' => $record['start_add'],
                    'start_lat' => $record['start_lat'],
                    'start_lng' => $record['start_lng'],
                    'end_add' => $record['end_add'],
                    'end_lat' => $record['end_lat'],
                    'end_lng' => $record['end_lng'],
                    'distance' => $record['distance'],
                    'duration' => $record['duration'],
                    'travel_mode' => $record['travel_mode']
                ));
        }

        //Delete the routes for the user and date
        $qry = "delete rpr from record_planner_route rpr inner join record_planner rp using(record_planner_id) where rp.user_id=".$user_id." and date(rp.start_date) ='".$date."'";
        $this->db->query($qry);
        //Save the routes for the record planners in the list
        if (!empty($data)) {
            $this->db->insert_batch('record_planner_route', $data);
        }
    }
}
