<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Appointments_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

	public function get_appointment_slot($user_id,$datetime){
	//find the appointment slot number(s) that this falls into
	}

    public function slot_availability($campaign_id=false, $user_id = false, $postcode = false, $distance = false, $source = false, $app_type = false)
    {
        $days = array(1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Thursday", 5 => "Friday", 6 => "Saturday", 7 => "Sunday");
        $timeslots = array();
        $where = "";
        if (!empty($user_id)) {
            $where .= " and user_id = '$user_id' ";
            $holidays_where = $where;
        }
        if ($campaign_id) {
            $where .= " and campaign_id = '$campaign_id' ";
        }
        if ($source) {
            $where .= " and (source_id = '$source' or source_id is null )";
        }
        //first configure the default array for all days
        $qry = "select appointment_slot_id,slot_name,slot_description,slot_start,slot_end,user_id, max_slots max_apps,`day` from appointment_slots join appointment_slot_assignment using(appointment_slot_id) where `day` is null  $where ";
        $max = array();
        $default = $this->db->query($qry)->result_array();

        $thresholds = array();
		 foreach ($days as $day_num => $day) {
            foreach ($default as $row) {
				if(isset($max[$day_num][$row['appointment_slot_id']])){
				 $max[$day_num][$row['appointment_slot_id']] += $row['max_apps'];
				} else {
				 $max[$day_num][$row['appointment_slot_id']] = $row['max_apps'];
				}
			}
		 }
		
        foreach ($days as $day_num => $day) {
            foreach ($default as $row) {
                $daycheck = "select slot_assignment_id from appointment_slot_assignment where appointment_slot_id = " . $row['appointment_slot_id'] . " and user_id = " . $row['user_id'] . " and day = " . $day_num;
                if (!$this->db->query($daycheck)->num_rows()) {
                    $timeslots[$row['appointment_slot_id']] = array("slot_name" => $row['slot_name'], "slot_description" => $row['slot_description'], "slot_start" => $row['slot_start'], "slot_end" => $row['slot_end'], "reason" => "");
                   
                    unset($row['max_apps']);
                    $thresholds[$day][$row['appointment_slot_id']] = $row;
                    $thresholds[$day][$row['appointment_slot_id']]['max_apps'] = $max[$day_num][$row['appointment_slot_id']];
                    $thresholds[$day][$row['appointment_slot_id']]['apps'] = 0;
                } else {
                    $thresholds[$day][$row['appointment_slot_id']] = $row;
                    $thresholds[$day][$row['appointment_slot_id']]['max_apps'] = 0;
                    $thresholds[$day][$row['appointment_slot_id']]['apps'] = 0;
					$thresholds[$day][$row['appointment_slot_id']]['reason'] = "";
                }
            }
        }
        if (count($thresholds) == "0") {
            return array("error" => "The selected user does not have appointment slots configured");
        }

        //get any user specified days
        $defined_slots = array();
        $get_slots = "select appointment_slot_id,`date`,max_slots,notes reason from appointment_slot_override where `date` >= curdate() $where ";
        $get_slots_result = $this->db->query($get_slots)->result_array();
        foreach ($get_slots_result as $row) {
            $defined_slots[$row['date']][$row['appointment_slot_id']]["max_apps"] = $row['max_slots'];
			$defined_slots[$row['date']][$row['appointment_slot_id']]["reason"] = $row['reason'];
        }
        //now find the specified daily slots and overwrite the default array
        $max = array();
        foreach ($days as $daynum => $day) {
            $qry = "select appointment_slot_id,slot_name,slot_description,slot_start,slot_end,user_id, max_slots max_apps,`day` from appointment_slots join appointment_slot_assignment using(appointment_slot_id) where `day` = $daynum $where ";
            $daily_slots = $this->db->query($qry)->result_array();
            foreach ($daily_slots as $k => $row) {
                $thresholds[$day][$row['appointment_slot_id']]['apps'] = 0;
                $timeslots[$row['appointment_slot_id']] = array("appointment_slot_id" => $row['appointment_slot_id'], "slot_name" => $row['slot_name'], "slot_description" => $row['slot_description'], "slot_start" => $row['slot_start'], "slot_end" => $row['slot_end']);
                if (isset($thresholds[$day][$row['appointment_slot_id']]['max_apps'])) {
                    $thresholds[$day][$row['appointment_slot_id']]['max_apps'] += $row['max_apps'];
                } else {
                    $thresholds[$day][$row['appointment_slot_id']] = $row;
                }
            }

        }


        /* get user holidays to remove from slots */
        $holidays = array();
		/*
        if ($user_id) {
            $get_holidays = "select reason,reason_id,block_day,appointment_slot_id,other_reason from appointment_rules join appointment_rule_reasons using(reason_id) where 1 ";
            $get_holidays .= $holidays_where;

            foreach ($this->db->query($get_holidays)->result_array() as $k => $row) {
				$slot = intval($row['appointment_slot_id']);
                $holidays[$row['block_day']] = array("slot"=>$slot,"reason" => $row['reason']);
				if($row['reason_id']=="3"){
					 $holidays[$row['block_day']]['reason'] = $row['other_reason'];
				}
            }
        }
		*/
        /* end holidays */

        /* now push all the data into each day for the next 30 days and if there is a holiday for the day we remove the slots and add the reason */

        for ($i = 0; $i < 45; $i++) {
            $date = date("Y-m-d", strtotime('+' . $i . ' days'));
			//insert the date into each slot -  this is used in the js to add data-date to the radio inputs in the slots so we can prepopulate the appointment form
			foreach($thresholds as $d=>$s){
				foreach($s as $k=>$v){
			$thresholds[$d][$k]["sqldate"] =  $date;
				}
			}
		
			//take each day and insert all the slot data
            $this_day = $thresholds[date("l", strtotime('+' . $i . ' days'))];
            if (array_key_exists($date, $defined_slots)) {
                foreach ($this_day as $slot => $details) {
                    if (isset($defined_slots[$date][$slot])) {
                        $this_day[$slot]['max_apps'] = $defined_slots[$date][$slot]["max_apps"];
						 $this_day[$slot]['reason'] = $defined_slots[$date][$slot]["reason"];
                    }
                }
            }
			//now set the slots as 0 where a holiday exists on that day
            if (array_key_exists($date, $holidays)) {
					if($holidays[$date]["slot"]=="0"){
                foreach ($this_day as $slot => $details) {
                    $this_day[$slot]['max_apps'] = 0;
                    @$this_day[$slot]['reason'] = $holidays[$date]['reason'];
                }
					} else {
					 $this_day[$holidays[$date]['slot']]['max_apps'] = 0;
                    @$this_day[$holidays[$date]['slot']]['reason'] = $holidays[$date]['reason'];	
					}
            }

            $slots[date("D jS M y", strtotime('+' . $i . ' days'))] = $this_day;
        }		
		
        /* now get the appointments in each slot for each day and push them into the array */

        $join_locations = "";
        $distance_select = "";
        $distance_order = "";

        if (!empty($postcode)) {
            $coords = postcode_to_coords($postcode);
            if (isset($coords['lat']) && isset($coords['lng'])) {
                $distance_order = " order by distance";
                $distance_select = ",min((((ACOS(SIN((" .
                    $coords['lat'] . "*PI()/180)) * SIN((lat*PI()/180))+COS((" .
                    $coords['lat'] . "*PI()/180)) * COS((lat*PI()/180)) * COS(((" .
                    $coords['lng'] . "- lng)*PI()/180))))*180/PI())*60*1.1515)) AS distance";

                $join_locations = " left join locations on locations.location_id = appointments.location_id ";
                if ($distance > 0) {
                    $where .= " and ( ";
                    //Distance from the company or the contacts addresses
                    $where .= " (";
                    $where .= $coords['lat'] . " BETWEEN (locations.lat-" . $distance . ") AND (locations.lat+" . $distance . ")";
                    $where .= " and " . $coords['lng'] . " BETWEEN (locations.lng-" . $distance . ") AND (locations.lng+" . $distance . ")";
                    $where .= " and ((((
							ACOS(
								SIN(" . $coords['lat'] . "*PI()/180) * SIN(locations.lat*PI()/180) +
								COS(" . $coords['lat'] . "*PI()/180) * COS(locations.lat*PI()/180) * COS(((" . $coords['lng'] . " - locations.lng)*PI()/180)
							)
						)*180/PI())*160*0.621371192)) <= " . $distance . ")";

                    $where .= " ) )";
                }
            } else {
                return array("error" => "There was an error with the postcode");
            }
        }


        foreach ($timeslots as $id => $timeslot) {
            $app_type_where = "";
            if ($app_type) {
                $app_type_where = " and appointment_type_id = '$app_type' ";
            }
            $qry = "select date(`start`) start $distance_select, count(*) count from appointments $join_locations left join records using(urn) join appointment_attendees using(appointment_id) where `status` = 1  and time(`start`) between '" . $timeslot['slot_start'] . "' and '" . $timeslot['slot_end'] . "' and date(`start`) between curdate() and adddate(curdate(),interval 45 day) $where $app_type_where group by date(`start`) $distance_order";

            $results = $this->db->query($qry)->result_array();

            $i = 0;
            foreach ($results as $row) {
                $date = date("D jS M y", strtotime($row['start']));
                @$slots[$date][$id]['sqldate'] = $row['start'];
                @$slots[$date][$id]['apps'] = $row['count'];
                //the smallest distance for this timeslot
                @$slots[$date][$id]['min_distance'] = number_format($row['distance'], 2);
                if ($i == 0) {
                    //first record is best distance
                    @$slots[$date][$id]['best_distance'] = true;
                }
                $i++;
            }
        }
		$name = $this->db->where("user_id",$user_id)->get("users")->row()->name;
        return array("timeslots" => $timeslots, "apps" => $slots,"name"=>$name);


    }

    public function appointment_data($count = false, $options = false)
    {
		 $tables = $options['visible_columns']['tables'];
      $columns =  $options['visible_columns']['columns'];
        $table_columns = $options['visible_columns']['select'];
        $filter_columns = $options['visible_columns']['filter'];
        $order_columns = $options['visible_columns']['order'];	
$datafield_ids = array();
		foreach($table_columns as $k=>$col){
				$datafield_ids[$k] = 0;	
		if(strpos($col,"custom_")!==false){
			$split = explode("_",$col);
			$datafield_ids[$k] = intval($split[1]);
			$filter_columns[$k] = "t_".intval($split[1]).".value";
			$order_columns[$k] = "t_".intval($split[1]).".value";
			$table_columns[$k] = "t_".intval($split[1]).".value " .$columns[$k]['data'];
		}
		}
  //these tables must be joined to the query regardless of the selected columns to allow the map to function
        $required_tables = array("campaigns", "companies", "company_addresses", "contacts","contact_addresses","contact_locations", "company_locations","appointment_locations","ownership","record_planner","record_planner_user","ownership","outcomes","appointment_attendees","appointment_users","appointment_types");
        foreach ($required_tables as $rt) {
            if (!in_array($rt, $tables)) {
                $tables[] = $rt;
            }
        }
		   $join = array();
        //add mandatory column selections here
           $required_select_columns = array(
            "a.postcode",
            "appointment_locations.lat",
            "appointment_locations.lng",
            "appointment_locations.location_id",
            "a.appointment_id",
            "r.urn",
            "a.appointment_id marker_id",
            "GROUP_CONCAT(DISTINCT CONCAT(coma.postcode, '(',company_locations.lat,'/',company_locations.lng,')','|',company_locations.location_id) separator ',') as company_location",
               "GROUP_CONCAT(DISTINCT CONCAT(cona.postcode, '(',contact_locations.lat,'/',contact_locations.lng,')','|',contact_locations.location_id) separator ',') as contact_location",
             "date_format(rp.start_date,'%d/%m/%Y') planner_date",
             "rp.user_id planner_user_id",
             "rp.record_planner_id",
             "rp.postcode as planner_postcode",
             "rp.location_id as planner_location_id",
             "rpu.name planner_user",
			 "r.record_color",
             "r.map_icon",
             "camp.map_icon as campaign_map_icon"
        );

				          //if any of the mandatory columns are missing from the columns array we push them in
        foreach ($required_select_columns as $required) {
            if (!in_array($required, $table_columns)) {
                $table_columns[] = $required;
            }
        }
        //turn the selection array into a list
        $selections = implode(",", $table_columns);	  
        $qry = "select $selections from appointments a join records r using(urn) ";
				  
        $table_joins = table_joins();
		unset($table_joins['appointments']);
        $join_array = join_array();
		unset($join_array['appointments']);

      $tablenum=0;
	  $tableappnum=0;
        foreach ($tables as $k=>$table) {
			if($table=="custom_panels"){ $tablenum++;
			$field_id = $datafield_ids[$k];
				$join[] = " left join (select max(id) id,urn from custom_panel_values join custom_panel_data using(data_id) where field_id = '$field_id' group by urn) mc_$field_id on mc_$field_id.urn =  r.urn left join  custom_panel_values t_$field_id on t_$field_id.id = mc_$field_id.id ";
			}
			if($table=="custom_panels_appointments"){ $tableappnum++;
			$field_id = $datafield_ids[$k];
				$join[] = " left join (select id,appointment_id from custom_panel_values join custom_panel_data using(data_id) where field_id = '$field_id') mc_$field_id on mc_$field_id.appointment_id =  a.appointment_id left join custom_panel_values t_$field_id on t_$field_id.id = mc_$field_id.id ";
			}
			
			if($table<>"custom_panels"){
            if (array_key_exists($table, $join_array)) {
                foreach ($join_array[$table] as $t) {
                    $join[$t] = @$table_joins[$t];
                }
            } else {
                $join[$table] = @$table_joins[$table];
            }
        }
		}

        foreach ($join as $join_query) {
            $qry .= $join_query;
        }

        $qry .= $this->get_where($options, $filter_columns);
        $qry .= " group by appointment_id";
        if ($count) {

            return $this->db->query($qry)->num_rows();
        }


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
		if($length>0){
        $qry .= "  limit $start,$length";
		}
        $result = $this->db->query($qry)->result_array();
        return $result;
    }

    private function get_where($options, $table_columns)
    {
        //the default condition in ever search query to stop people viewing campaigns they arent supposed to!

        $where = " where a.`status` = 1 and r.campaign_id in({$_SESSION['campaign_access']['list']}) ";
		if(isset($_SESSION['current_campaign'])){
			 $where .= " and r.campaign_id = '".$_SESSION['current_campaign']."' ";
		}
        //Check the bounds of the map
        if ($options['bounds'] && $options['map']=='true') {
            $where .= " and (appointment_locations.lat < ".$options['bounds']['neLat']." and appointment_locations.lat > ".$options['bounds']['swLat']." and appointment_locations.lng < ".$options['bounds']['neLng']." and appointment_locations.lng > ".$options['bounds']['swLng'].") ";
        }

        //Check the start_date and end_date
        if (isset($options['date_from']) && $options['date_from']) {
            $where .= " and date(a.start) >= '" . $options['date_from'] . "' ";
        }
        if (isset($options['date_to']) && $options['date_to']) {
            $where .= " and date(a.end) <= '" . $options['date_to'] . "' ";
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

    public function appointment_modal($id)
    {
        $qry = "select * from appointments a left join users u on u.user_id = a.created_by where appointment_id = " . intval($id) . " group by appointment_id";
        return $this->db->query($qry)->row_array();
    }

    public function appointment_attendees($id)
    {
        $qry = "select name from appointment_attendees left join users using(user_id) where appointment_id = " . intval($id);
        return $this->db->query($qry)->result_array();
    }

    public function checkDayBlocked($attendee_id, $startDate, $endDate)
    {

        $qry = "SELECT *
                    FROM appointment_slot_override apr
                      LEFT JOIN appointment_slots aps USING (appointment_slot_id)
                    WHERE max_slots = 0 and user_id = " . $attendee_id . "
                          AND (`date` >= DATE('" . to_mysql_datetime($startDate) . "') AND `date` <= DATE('" . to_mysql_datetime($endDate) . "'))
                          AND (
                            (
                                (TIME('" . to_mysql_datetime($startDate) . "') >= aps.slot_start AND TIME('" . to_mysql_datetime($startDate) . "') <= aps.slot_end)
                                 OR
                                (TIME('" . to_mysql_datetime($endDate) . "') >= aps.slot_start AND TIME('" . to_mysql_datetime($endDate) . "') <= aps.slot_end)
                            )
                            OR
                            appointment_slot_id IS NULL
                          )
               ";

        $results = $this->db->query($qry)->result_array();

        //Return true if $results is not empty, because there is at least one block day between both dates
        return (!empty($results));
    }


    /**
     *
     * Check if the attendee already has an appointment where the block day is between the start and the end date schedulled
     *
     */
    public function checkNoAppointmentForTheDayBlocked($attendee_id, $blockDay, $appointment_slot_id = NULL)
    {

        $appointment_slot_start = '00:00:00';
        $appointment_slot_end = '23:59:59';
        if ($appointment_slot_id) {
            $qry = "select * from appointment_slots where appointment_slot_id = " . $appointment_slot_id;
            $results = $this->db->query($qry)->result_array();

            $appointment_slot = (!empty($results) ? $results[0] : array());
            if (!empty($appointment_slot)) {
                $appointment_slot_start = $appointment_slot['slot_start'];
                $appointment_slot_end = $appointment_slot['slot_end'];
            }
        }

        $qry = "select apt.user_id, a.appointment_id, a.start, a.end
                from appointments a
                  left join appointment_attendees apt using(appointment_id)
                where
                  apt.user_id = '$attendee_id' and `status` = 1
                  and '" . to_mysql_datetime($blockDay) . " " . $appointment_slot_start . "' <= a.start
                  and '" . to_mysql_datetime($blockDay) . " " . $appointment_slot_end . "' >= a.end";

        $results = $this->db->query($qry)->result_array();

        //Return true if $results is not empty, because there is at least one appointment where the block day is between the start and the end date schedulled
        return (!empty($results));
    }

    public function checkSlotAvailable($attendee_id, $startDate, $endDate)
    {

        //TODO check if there is a slot available for this date
    }


    /**
     * Get appointment by id
     *
     */
    public function getAppointmentById($appointment_id)
    {

        $this->db->select('*');
        $this->db->from('appointments');
        $this->db->where('appointment_id', $appointment_id);
        $this->db->join('branch', 'branch.branch_id=appointments.branch_id', 'LEFT');
        $this->db->join('appointment_types', 'appointment_types.appointment_type_id=appointments.appointment_type_id', 'LEFT');
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return NULL;
        }
    }

    /**
     * Get getLastAppointmentIcsByUid
     *
     */
    public function getLastAppointmentIcsByUid($uid)
    {

        $this->db->select('*');
        $this->db->from('appointments_ics');
        $this->db->where('uid', $uid);
        $this->db->order_by("appointments_ics_id", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return NULL;
        }
    }


    /**
     * Get the Last Appointment Updated by urn
     *
     */
    public function getLastAppointmentUpdated($urn)
    {

        $this->db->select('*');
        $this->db->from('appointments');
        $this->db->where('urn', $urn);
        $this->db->order_by("date_added", "desc");
        $this->db->order_by("date_updated", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return NULL;
        }
    }

    /**
     * Add appointment ics
     *
     */
    public function saveAppointmentIcs($appointment_ics)
    {

        $this->db->insert('appointments_ics', $appointment_ics);

        return $this->db->insert_id();

    }

    /**
     * Update appointment ics by email_id
     *
     */
    public function updateAppointmentIcs($appointment_ics)
    {
        $this->db->where('email_id', $appointment_ics['email_id']);
        return  $this->db->update('appointments_ics', $appointment_ics);
    }

    //Get the contact appointment by appointment_id
    public function get_contact_appointment($appointment_id) {
        $this->db->select('contacts.*');
        $this->db->from('appointments');
        $this->db->join('contacts', 'contacts.contact_id=appointments.contact_id', 'INNER');
        $this->db->where('appointment_id', $appointment_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return NULL;
        }
    }

    public function check_overlap_appointments($urn, $appointment_id, $attendee_id, $start, $end) {
        $qry = "SELECT *
                FROM appointments a
                  LEFT JOIN appointment_attendees at USING (appointment_id)
                WHERE user_id = " . $attendee_id . "
                      ".($appointment_id != '' ?(" AND appointment_id <> ".$appointment_id) : "")."
                      AND status = 1
                      AND (
                            (`start` < '" . to_mysql_datetime($start) . "' AND `end` > '" . to_mysql_datetime($start) . "')
                            OR
                            (`start` < '" . to_mysql_datetime($end) . "' AND `end` > '" . to_mysql_datetime($end) . "')
                            OR
                            (`start` = '" . to_mysql_datetime($start) . "')
                            OR
                            (`end` = '" . to_mysql_datetime($end) . "')
                        )
               ";

        $results = $this->db->query($qry)->result_array();

        return (!empty($results));
    }



}
