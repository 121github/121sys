<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Branches_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('uk_postcodes',true); 
    }



    public function get_branch_info($id=false,$postcode=false){
        $qry = "select postcode,latitude lat, longitude lng from uk_postcodes.PostcodeIo where postcode = '$postcode'";
        $check_location = $this->db2->query($qry);
        if ($check_location->num_rows()) {
            $coords = $check_location->row_array();
        } else {
            $coords = postcode_to_coords($postcode);
        }
//        $qry = "select
//            br.region_id,
//            b.branch_id,
//            b.branch_name,
//            region_name,
//            consultant,
//            consultant_id,
//            consultant_email,
//            (((ACOS(SIN((" .
//                $coords['lat'] . "*PI()/180)) * SIN((lat*PI()/180))+COS((" .
//                $coords['lat'] . "*PI()/180)) * COS((lat*PI()/180)) * COS(((" .
//                $coords['lng'] . "- lng)*PI()/180))))*180/PI())*60*1.1515) AS distance
//            from branch b
//                join branch_addresses ba using(branch_id)
//                join locations l using(location_id)
//                join branch_regions br using(region_id)
//                join branch_region_users bru using(region_id)
//                left join
//                    (select user_id consultant_id,name consultant,user_email consultant_email
//                        from users where attendee=0
//                    ) consultants on (bru.user_id = consultant_id AND bru.is_manager = 0)
//                join branch_user bu on bu.branch_id = b.branch_id
//                join branch_campaigns bc on bc.branch_id = b.branch_id
//            where branch_status= 1";
        $qry = "SELECT
                br.region_id,
                br.region_name,
                br.default_branch_id,
                b.branch_id,
                b.branch_name,
                GROUP_CONCAT(DISTINCT bus_attendee.user_id) bus_attendee,
                GROUP_CONCAT(DISTINCT bus.user_id) bus,
                GROUP_CONCAT(DISTINCT bus_manager.user_id) bus_manager,
                GROUP_CONCAT(DISTINCT brus_attendee.user_id) brus_attendee,
                GROUP_CONCAT(DISTINCT brus.user_id) brus,
                GROUP_CONCAT(DISTINCT brus_manager.user_id) brus_manager,
                (((ACOS(SIN((" .
            $coords['lat'] . "*PI()/180)) * SIN((lat*PI()/180))+COS((" .
            $coords['lat'] . "*PI()/180)) * COS((lat*PI()/180)) * COS(((" .
            $coords['lng'] . "- lng)*PI()/180))))*180/PI())*60*1.1515) AS distance
            FROM branch b
              INNER JOIN branch_addresses ba USING (branch_id)
              INNER JOIN branch_user bu ON bu.branch_id = b.branch_id
              INNER JOIN locations l USING (location_id)
              INNER JOIN branch_regions br USING (region_id)
              INNER JOIN branch_region_users bru USING (region_id)
              INNER JOIN branch_campaigns bc ON bc.branch_id = b.branch_id
              LEFT JOIN users bus_attendee ON (bu.user_id = bus_attendee.user_id and bus_attendee.attendee = 1)
              LEFT JOIN users bus ON (bu.user_id = bus.user_id and bu.is_manager = 0)
              LEFT JOIN users bus_manager ON (bu.user_id = bus_manager.user_id and bu.is_manager = 1)
              LEFT JOIN users brus_attendee ON (bru.user_id = brus_attendee.user_id and brus_attendee.attendee = 1)
              LEFT JOIN users brus ON (bru.user_id = brus.user_id and bru.is_manager = 0)
              LEFT JOIN users brus_manager ON (bru.user_id = brus_manager.user_id and bru.is_manager = 1)
            WHERE branch_status = 1";
        $qry .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
        if ($id) {
            $qry .= " and br.region_id = '$id' ";
        }
        $qry .= "  group by b.branch_id order by distance";

        $result = $this->db->query($qry)->result_array();

        $branches = array();
        $order = 0;
        foreach ($result as $k => $row) {
            if (!isset($branches[$row['region_id']])) {
                $branches[$row['region_id']] = array(
                    'id' => $row['region_id'],
                    'name' => $row['region_name'],
                    'brus_attendees' => $row['brus_attendee'],
                    'brus' => $row['brus'],
                    'brus_managers' => $row['brus_manager'],
                    'default_branch_id' => $row['default_branch_id'],
                    'order' => $order,
                    'branches' => array()
                );
                $order++;
            }
            array_push($branches[$row['region_id']]['branches'], array(
                "id" => $row['branch_id'],
                "name" => $row['branch_name'],
                "distance" => number_format($row['distance'], 2) . " miles",
                "bus_attendees" => $row['bus_attendee'],
                "bus" => $row['bus'],
                "bus_managers" => $row['bus_manager']
            ));
        }

        $aux = array();
        foreach($branches as $val) {
            $aux[$val['order']] = $val;
        }

        $result = $aux;

        return array("success" => true, "branches" => $result);
    }
}