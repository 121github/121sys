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
        $table_columns = array(
            "start",
            "com.name",
            "u.name",
            "date_format(a.`date_added`,'%d/%m/%y')",
            "postcode"
        );
        $order_columns = array(
            "start",
            "com.name",
            "u.name",
            "a.date_added",
            "postcode"
        );

        $qry = "select
                    date_format(`start`,'%d/%m/%y %H:%i') start,
                    com.name,
                    title,
                    u.name attendee,
                    u.user_id as attendee_id,
                    date_format(a.`date_added`,'%d/%m/%y') date_added,
                    postcode,
                    lat,
                    lng,
                    appointment_id,
                    IFNULL(com.website,'') as website,
                    records.urn
                from appointments a
                  left join appointment_attendees aa using(appointment_id)
                  left join users u on u.user_id = aa.user_id
                  left join records using(urn)
                  left join companies com using(urn)
                  left join locations using(location_id)";

        $where = $this->get_where($options, $table_columns);
        $qry .= $where;
        $qry .= " group by appointment_id";

        $start = $options['start'];
        $length = $options['length'];
        if (isset($_SESSION['appointment_table']['order']) && $options['draw'] == "1") {
            $order = $_SESSION['appointment_table']['order'];
        } else {
            $order = " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'];
            unset($_SESSION['appointment_table']['order']);
            unset($_SESSION['appointment_table']['values']['order']);
        }

        $qry .= $order;
        $qry .= "  limit $start,$length";

        $result = $this->db->query($qry)->result_array();
        return $result;
    }

    private function get_where($options, $table_columns)
    {
        //the default condition in ever search query to stop people viewing campaigns they arent supposed to!
        $where = " where campaign_id in({$_SESSION['campaign_access']['list']}) ";

        //Check the bounds of the map
        if ($options['bounds']) {
            $where .= " and lat < ".$options['bounds']['neLat']." and lat > ".$options['bounds']['swLat']." and lng < ".$options['bounds']['neLng']." and lng > ".$options['bounds']['swLng']." ";
        }

        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $where .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        return $where;
    }
	
	public function add_record($urn,$date,$postcode){
	$this->db->where("postcode",$postcode);
	$check_location = $this->db->get("uk_postcodes");
	if($check_location->num_rows()){
	$location_id = $check_location->row()->postcode_id;
	} else {
	$coords = postcode_to_coords($postcode);
	$this->db->insert("uk_postcodes",array("postcode"=>$postcode,"lat"=>$coords['lat'],"lng"=>$coords['lng']));
	$location_id = $this->db->insert_id();
	$this->db->insert("locations",array("location_id"=>$location_id,"lat"=>$coords['lat'],"lng"=>$coords['lng']));
	}
	$qry = "replace into record_planner set urn = '$urn', user_id = '".$_SESSION['user_id']."',start_date = '$date', postcode ='$postcode', location_id = '$location_id'";	
	$this->db->query($qry);
	}
	
		public function remove_record($urn){
	$this->db->where(array("urn"=>$urn,"user_id"=>$_SESSION['user_id']));
		$this->db->delete("record_planner");
	}
	
}
