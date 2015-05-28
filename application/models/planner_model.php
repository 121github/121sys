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
            "date_format(rp.`start_date`,'%d/%m/%y')",
            "com.name",
            "u.name",
            "distance",
            "time",
            "postcode"
        );

        $qry = "select
                    date_format(rp.`start_date`,'%d/%m/%Y') start,
                    if(com.name is null,'na',com.name) name,
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
                    com.website as company_website,
                    con.website as contact_website,
                    rpr.start_add,
                    rpr.start_lat,
                    rpr.start_lng,
                    rpr.end_add,
                    rpr.end_lat,
                    rpr.end_lng,
                    rpr.distance,
                    rpr.duration
                from record_planner rp
                  left join users u on u.user_id = rp.user_id
                  left join records using(urn)
                  left join companies com using(urn)
                  left join contacts con using(urn)
                  left join locations using(location_id)
                  left join record_planner_route rpr using(record_planner_id)
                  inner join outcomes using(outcome_id)";

        $where = $this->get_where($options, $table_columns);
        $qry .= $where;
        $qry .= "  group by record_planner_id order by case when rp.order_num is not null then rp.order_num else record_planner_id end asc ";

        $result = $this->db->query($qry)->result_array();
        return $result;
    }

    private function get_where($options, $table_columns)
    {
        //the default condition in ever search query to stop people viewing campaigns they arent supposed to!
        $where = " where campaign_id in({$_SESSION['campaign_access']['list']}) ";

        //User
        $where .= " and rp.user_id = {$_SESSION['user_id']} ";

        //Date
        if ($options['date']) {
            $where .= " and date(rp.start_date) = '" . $options['date'] . "' ";
        }

        //Check the bounds of the map
        if ($options['map'] == 'true' && $options['bounds']) {
            $where .= " and lat < " . $options['bounds']['neLat'] . " and lat > " . $options['bounds']['swLat'] . " and lng < " . $options['bounds']['neLng'] . " and lng > " . $options['bounds']['swLng'] . " ";
        }

        return $where;
    }

    public function add_record($urn, $date, $postcode)
    {
        $this->db->where("postcode", $postcode);
        $check_location = $this->db->get("uk_postcodes");
        if ($check_location->num_rows()) {
            $location_id = $check_location->row()->postcode_id;
        } else {
            $coords = postcode_to_coords($postcode);
            $this->db->insert("uk_postcodes", array("postcode" => $postcode, "lat" => $coords['lat'], "lng" => $coords['lng']));
            $location_id = $this->db->insert_id();
            $this->db->insert("locations", array("location_id" => $location_id, "lat" => $coords['lat'], "lng" => $coords['lng']));
        }
        $qry = "replace into record_planner set urn = '$urn', user_id = '" . $_SESSION['user_id'] . "',start_date = '$date', postcode ='$postcode', location_id = '$location_id'";
        $this->db->query($qry);
    }

    public function remove_record($urn)
    {
        $this->db->where(array("urn" => $urn, "user_id" => $_SESSION['user_id']));
        $this->db->delete("record_planner");
    }

    public function save_record_order($record_list, $user_id, $date)
    {
        //Reset the order as null for this user and on this date
        $data = array(
            'order_num' => null
        );
        $this->db->where('user_id', $user_id);
        $this->db->where('date(start_date)', $date);
        $this->db->update('record_planner', $data);

        //Set the order for the record_planners for this user and on this date
        $data = array();
        foreach ($record_list as $order_num => $record) {
            if (isset($record['record_planner'])) {
                array_push($data, array(
                    'record_planner_id' => $record['record_planner']['record_planner_id'],
                    'order_num' => $order_num
                ));
            }
        }

        if (!empty($data)) {
            $this->db->where('user_id', $user_id);
            $this->db->where('date(start_date)', $date);
            $this->db->update_batch('record_planner', $data, 'record_planner_id');
        }
    }

    public function save_record_route($record_list, $user_id, $date)
    {
        //Prepare the data
        $data = array();
        $record_planner_ids = array();
        foreach ($record_list as $order_num => $record) {
            if (isset($record['record_planner'])) {
                array_push($record_planner_ids,$record['record_planner']['record_planner_id']);
                array_push($data, array(
                    'record_planner_id' => $record['record_planner']['record_planner_id'],
                    'start_add' => $record['start_add'],
                    'start_lat' => $record['start_lat'],
                    'start_lng' => $record['start_lng'],
                    'end_add' => $record['end_add'],
                    'end_lat' => $record['end_lat'],
                    'end_lng' => $record['end_lng'],
                    'distance' => $record['distance'],
                    'duration' => $record['duration']
                ));
            }
        }

        //Delete the routes for the record planners in the list
        $this->db->where("record_planner_id IN ('".implode(",",$record_planner_ids)."')");
        $this->db->delete('record_planner_route');

        //Save the routes for the record planners in the list
        if (!empty($data)) {
            $this->db->insert_batch('record_planner_route', $data);
        }
    }
}
