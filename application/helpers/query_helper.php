<?php 

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


   function get_where($options, $table_columns)
    {
        //the default condition in ever search query to stop people viewing campaigns they arent supposed to!
        $where = " where 1 ";

        if (isset($_SESSION['current_campaign'])) {
            //this is already added to the session filter when the campaign is selected
			$where .= " and r.campaign_id = '".$_SESSION['current_campaign'] ."'";
        }		
        $where .= " and r.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        //Check the bounds of the map
        if ($options['bounds'] && $options['map'] == 'true') {
            $where .= " and (
                    (company_locations.lat < " . $options['bounds']['neLat'] . " and company_locations.lat > " . $options['bounds']['swLat'] . " and company_locations.lng < " . $options['bounds']['neLng'] . " and company_locations.lng > " . $options['bounds']['swLng'] . ")
                      or
                    (contact_locations.lat < " . $options['bounds']['neLat'] . " and contact_locations.lat > " . $options['bounds']['swLat'] . " and contact_locations.lng < " . $options['bounds']['neLng'] . " and contact_locations.lng > " . $options['bounds']['swLng'] . ")
                  )";
        }

        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                if ($table_columns[$k] == "map_icon" && $v['search']['value'] == "Icon") {
                    //ignore this
                } else {
                    $where .= " and " . $table_columns[$k] . " like '%" . addslashes($v['search']['value']) . "%' ";
                }
            }
        }

        //if any filter has been set then we should apply it here
        if (isset($_SESSION['filter']['where']) && !empty($_SESSION['filter']['where'])) {
            $where .= $_SESSION['filter']['where'];
        }
 		//$where .= $_SESSION['data_access_query'];

        return $where;

    }