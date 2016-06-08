<?php
require('upload.php');

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Planner extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->_campaigns = campaign_access_dropdown();
        $this->project_version = $this->config->item('project_version');

        $this->load->model('Records_model');
        $this->load->model('User_model');
        $this->load->model('Form_model');
        $this->load->model('Planner_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
        if (intval($this->input->post('user_id')) > 0) {
            $this->user_id = intval($this->input->post('user_id'));
        } else {
            $this->user_id = $_SESSION['user_id'];
        }
    }

    public function get_driver_availability($driver, $d = false)
    {
        //$driver = $this->uri->segment('3');
        for ($i = 0; $i < 45; $i++) {
            $slots[date("Y-m-d", strtotime('+' . $i . ' days'))] = array();
            $total_apps[date("Y-m-d", strtotime('+' . $i . ' days'))] = 0;
        }
        //get all the available slots for all days (wildcard day = null)
        $qry = "select * from appointment_slot_assignment join appointment_slots using(appointment_slot_id) where user_id = '$driver' and day is null";
        $result = $this->db->query($qry)->result_array();
        if (count($result) > 0) {
            foreach ($slots as $date => $slot) {
                foreach ($result as $row) {
                    $slots[$date][$row['appointment_slot_id']] = array("start" => $row['slot_start'], "end" => $row['slot_end'], "max_apps" => $row['max_slots'], "apps" => 0, "total" => 0);
                }
            }
        }
        //overwrite the slots with available slots for this DAY
        $qry = "select * from appointment_slot_assignment join appointment_slots using(appointment_slot_id) where user_id = '$driver' and day is not null";
        $result = $this->db->query($qry)->result_array();
        if (count($result) > 0) {
            foreach ($slots as $date => $slot) {
                foreach ($result as $row) {
                    if (date('N', strtotime($date)) == $row['day']) {
                        $slots[$date][$row['appointment_slot_id']] = array("start" => $row['slot_start'], "end" => $row['slot_end'], "max_apps" => $row['max_slots'], "apps" => 0, "total" => 0);
                    }
                }
            }
        }
        //overwrite the slots with available slots for this DATE
        $qry = "select * from appointment_slot_override join appointment_slots using(appointment_slot_id) where user_id = '$driver'";
        $result = $this->db->query($qry)->result_array();
        if (count($result) > 0) {
            foreach ($result as $row) {
                $slots[$row['date']][$row['appointment_slot_id']] = array("start" => $row['slot_start'], "end" => $row['slot_end'], "max_apps" => $row['max_slots'], "apps" => 0, "total" => 0);
            }
        }
        //now check how may apps have been booked on each day
        $qry = "select *,date(`start`) `date` from appointments join appointment_attendees using(appointment_id) where date(`start`)>=curdate() and user_id = '$driver'";
        $appointments = $this->db->query($qry)->result_array();
        foreach ($appointments as $row) {
			if(isset($slots[$row['date']])){
            foreach ($slots[$row['date']] as $id => $slot) {

                if (strtotime($row['start']) >= strtotime($row['date'] . " " . $slot['start']) && strtotime($row['start']) <= strtotime($row['date'] . " " . $slot['end']) || strtotime($row['end']) >= strtotime($row['date'] . " " . $slot['start']) && strtotime($row['end']) <= strtotime($row['date'] . " " . $slot['end'])) {
                    $total_apps[$row['date']]++;
                    if (isset($slots[$row['date']][$id]['apps'])) {
                        $slots[$row['date']][$id]['apps'] += 1;
                    } else {
                        $slots[$row['date']][$id]['apps'] = 1;
                    }
                }
                //add the total number of appointments for the day
                foreach ($slot as $k => $v) {
                    $slots[$row['date']][$id]['total_apps'] = $total_apps[$row['date']];
                }
            }
			}
        }
        if ($d) {
            return $slots[$d];
        } else {
            return $slots;
        }
    }

    public function get_journey_details($start, $end)
    {

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . urlencode($start) . ",uk&destinations=" . urlencode($end) . ",uk&mode=Driving&units=imperial&key=AIzaSyBARAzbeCEh9WjrZfxyxyH3DFeWGzvei3U";
        $response = json_decode(file_get_contents($url), true);
        if (!isset($response['rows'][0]['elements'][0])) {
            $this->firephp->log($response);
            return array("distance" => array("text" => "", "value" => 0), "duration" => array("text" => "", "value" => 0), "added_distance" => array("text" => "", "value" => 0), "added_duration" => array("text" => "", "value" => 0));
        }
        return $response['rows'][0]['elements'][0];
    }

    public function simulate_121_planner()
    {
		session_write_close();
        $customer_postcode = $this->input->post('postcode');
        $driver_id = $this->input->post('driver_id');
        $branch_id = $this->input->post('branch_id');
        $driver_postcode = $this->Planner_model->get_user_postcode($driver_id);
        $position = $this->input->post('position');
        $availability = $this->get_driver_availability($driver_id);
        $plannerdate = $this->input->post('date');
        if ($plannerdate) {
            $uk_date = DateTime::createFromFormat('Y-m-d', $plannerdate)->format('D jS M Y');
        } else {
            $uk_date = false;
        }
        if ($branch_id == "" || $branch_id == "false") {
            $branch_id = false;
        }

        if ($branch_id) {
            $branch_postcode = $this->Planner_model->get_branch_postcode($branch_id);
            $default_journey = $this->get_journey_details($driver_postcode, $branch_postcode);
            $default_journey2 = $this->get_journey_details($branch_postcode, $customer_postcode);
        } else {
            $default_journey = $this->get_journey_details($driver_postcode, $customer_postcode);
        }
        //first create the days to populate
        for ($i = 0; $i < 45; $i++) {
            if (date("D", strtotime('+' . $i . ' days')) <> "Sun") {
                $thisdate = date("Y-m-d", strtotime('+' . $i . ' days'));
                if ($thisdate == $plannerdate || $plannerdate == false) {
                    $days[] = $thisdate;
                }
            }
        }
        $data = array();
        $travel_info = array();
        foreach ($days as $k => $day) {
            $locations = array();
            $app_duration = array(0 => 0, 1 => 0);
            $total_distance = 0;
            $total_duration = 0;
            $added_duration = 0;
            $added_distance = 0;
            $stats = array("distance" => array("text" => "", "value" => 0), "duration" => array("text" => "", "value" => 0), "added_distance" => array("text" => "", "value" => 0), "added_duration" => array("text" => "", "value" => 0));


            //default position of simulated appointment
            $position = 1;
            //if position is not manually set we just find the first available slot and use that to position the simulated appointment in the array
            if (!$this->input->post('position')) {
                $p = 0;
                foreach ($availability[$day] as $id => $slot) {
                    $p++;
                    if ($slot['max_apps'] > $slot['apps']) {
                        $position = $p;
                        break;
                    }
                }
            } else {
                $position = $this->input->post('position');
            }

            $qry = "select date(start) `app_date`,postcode, access_postcode, TIME_FORMAT(start,'%h:%i %p') as time, TIME_TO_SEC(TIMEDIFF(`end`,`start`)) app_duration from appointments join appointment_attendees using(appointment_id) join users using(user_id) where user_id = '$driver_id' and date(`start`) = '$day' and `status` = 1 and postcode is not null group by appointment_id order by `end` asc";
            $result = $this->db->query($qry)->result_array();

            if (isset($result[0])) {

                $customer_data = array("postcode" => $customer_postcode, "app_duration" => 3600, 'type' => "customer_postcode",);
                $aux = array();
                foreach ($result as $k => $row) {
                    if ($position == $k + 1) {
                        array_push($aux, $customer_data);
                    }
                    //Add the access postcode if exists
                    if (isset($result[$k]['access_postcode']) && $result[$k]['access_postcode']) {
                        array_push($aux, array(
                            'type' => "access_postcode",
                            'postcode' => $row['access_postcode']
                        ));
                    }
                    unset($row['access_postcode']);
                    $row['type'] = "appointment_postcode";
                    array_push($aux, $row);
                }
                if ($position == count($result) + 1) {
                    array_push($aux, $customer_data);
                }
                $result = $aux;

                //add driver postcode to waypoints
                $driver_data = array("postcode" => $driver_postcode, "type" => "start_postcode");
                array_splice($result, 0, 0, array($driver_data));
                $result[] = array("postcode" => $driver_postcode, "type" => "start_postcode");

                if ($branch_id) {
                    $branch_postcode = $this->Planner_model->get_branch_postcode($branch_id);
                    $branch_data = array("postcode" => $branch_postcode);
                    array_splice($result, 1, 0, array($branch_data));
                    array_splice($result, count($result) - 1, 0, array($branch_data));
                }
                $travel_info[$day] = array();
                $app_num = 1;
                foreach ($result as $k => $row) {
                    if (isset($result[$k + 1])) {
                        $travel_info[$day][$k] = $this->get_journey_details($result[$k]['postcode'], $result[$k + 1]['postcode']);
                        if ($k == 0) {
                            $title = "Start";
                            $app_duration = false;
                        } else if (isset($result[$k]['type']) && $result[$k]['type'] == 'customer_postcode') {
                            $title = "This appointment";
                        } else if (isset($result[$k]['type']) && $result[$k]['type'] == 'access_postcode') {
                            $title = "Access address for appointment $app_num";
                        } else {
                            $title = "Appointment $app_num [" . @$result[$k]['time'] . "]";
                            $app_num++;
                        }
                    } else {
                        $title = "End";
                        $app_duration = false;
                    }
                    if ($branch_id && $k == 1 || $branch_id && (count($result) - 2) == $k) {
                        $title = "Branch";
                        $app_duration = false;
                    }
                    $data[$day][$k] = array("title" => $title, "postcode" => $row['postcode'], "app_duration_val" => 3600);
                    if (isset($result[$k]['app_duration'])) {
                        $data[$day][$k]['app_duration_val'] = $result[$k]['app_duration'];
                        $data[$day][$k]['app_duration'] = convertToHoursMins($result[$k]['app_duration'] / 60, '%2dh %2dm');
                    }
                    if (isset($result[$k]['type'])) {
                        $data[$day][$k]['type'] = $result[$k]['type'];
                    }
                }
            } else if ($branch_id) {
                $travel_info[$day][0] = $default_journey;
                $travel_info[$day][1] = $default_journey2;
                $travel_info[$day][2] = $default_journey2;
                $travel_info[$day][3] = $default_journey;
                $data[$day][0] = array("title" => "Start", "postcode" => $driver_postcode, "type" => "start_postcode", "app_duration_val" => 0, "app_duration" => "");
                $data[$day][1] = array("title" => "Branch", "postcode" => $branch_postcode, "app_duration_val" => 0, "app_duration" => "");
                $data[$day][2] = array("title" => "This appointment", "postcode" => $customer_postcode, "type" => "customer_postcode", "app_duration_val" => 3600, "app_duration" => convertToHoursMins(3600 / 60, '%2dh %2dm'));
                $data[$day][3] = array("title" => "Branch", "postcode" => $branch_postcode, "app_duration_val" => 0, "app_duration" => "");
                $data[$day][4] = array("title" => "End", "postcode" => $driver_postcode, "type" => "end_postcode", "app_duration_val" => 0, "app_duration" => "");
            } else {
                $travel_info[$day][0] = $default_journey;
                $travel_info[$day][1] = $default_journey;
                $data[$day][0] = array("title" => "Start", "postcode" => $driver_postcode, "type" => "start_postcode", "app_duration_val" => 0, "app_duration" => "");
                $data[$day][1] = array("title" => "This appointment", "postcode" => $customer_postcode, "type" => "customer_postcode", "app_duration_val" => 3600, "app_duration" => convertToHoursMins(3600 / 60, '%2dh %2dm'));
                $data[$day][2] = array("title" => "End", "postcode" => $driver_postcode, "type" => "end_postcode", "app_duration_val" => 0, "app_duration" => "");
            }
        }

        foreach ($travel_info as $day => $info) {
            $total_distance = 0;
            $total_duration = 0;
            foreach ($info as $k => $v) {
                $total_distance += $v['distance']['value'];
                $total_duration += $v['duration']['value'];
                if (isset($data[$day][$k]['app_duration'])) {
                    $total_duration += $data[$day][$k]['app_duration_val'];
                }

                $data[$day][0]['time'] = "09:00 am";
                $data[$day][0]['uk_date'] = date('D jS M Y', strtotime($day));
                $data[$day][0]['datetime'] = date('H:i', strtotime("9am"));
                $data[$day][0]['sqldate'] = $day;
                if ($k > 0) {
                    $data[$day][$k]['time'] = date('H:i a', strtotime("9am + " . $v['duration']['value'] . " seconds"));
                    $data[$day][$k]['datetime'] = date('H:i', strtotime("9am + " . $v['duration']['value'] . " seconds"));
                }
                $travel_info[$day][$k]['added_distance']['value'] = $total_distance;
                $travel_info[$day][$k]['added_duration']['value'] = $total_duration;

                $travel_info[$day][$k]['added_distance']['text'] = number_format(($total_distance / 1000) * 0.621371192, 1) . " Miles";
                $travel_info[$day][$k]['added_duration']['text'] = convertToHoursMins($total_duration / 60, '%2dh %2dm');
            }

            $new_key = count($travel_info[$day]);
            $travel_info[$day][$new_key]['distance']['value'] = $total_distance;
            $travel_info[$day][$new_key]['duration']['value'] = $total_duration;
            $travel_info[$day][$new_key]['distance']['text'] = number_format(($total_distance / 1000) * 0.621371192, 1) . " Miles";
            $travel_info[$day][$new_key]['duration']['text'] = convertToHoursMins($total_duration / 60, '%2dh %2dm');
            $travel_info[$day][$new_key]['added_distance'] = $travel_info[$day][$new_key]['distance'];
            $travel_info[$day][$new_key]['added_duration'] = $travel_info[$day][$new_key]['duration'];
        }


//also add the slots to the data to show in the panel
        $this->load->model('Appointments_model');
        $slots = array();

        $appointments = $this->Appointments_model->slot_availability(false, $driver_id);

        if (!isset($appointments['apps'])) {
            $error = "Availability has not been configured for this attendee";
            if (in_array("slot config", $_SESSION['permissions'])) {
                $error .= "<br><a href='" . base_url() . 'admin/availability' . "'>Click here to configure the availability</a>";
            }
            echo json_encode(array("success" => false, "msg" => $error));
            exit;
        };

        foreach ($appointments['apps'] as $date => $day) {
            $sql_date = DateTime::createFromFormat('D jS M y', $date)->format('Y-m-d');
            if ($plannerdate && $plannerdate <> $sql_date) {
                continue;
            }
            $max_apps = 0;
            $apps = 0;
            $reason = false;
            foreach ($day as $k => $row) {
                if (isset($row['max_apps'])) {
                    $max_apps += $row['max_apps'];
                    $apps += $row['apps'];
                    if (!empty($row['reason'])) {
                        $reason = $row['reason'];
                    }

                    $slots[$sql_date] = array("apps" => $apps, "max_apps" => $max_apps, "reason" => $reason);
                }
            }
        }
        echo json_encode(array("success" => true, "uk_date" => $uk_date, "date" => $plannerdate, "waypoints" => $data, "stats" => $travel_info, "slots" => $slots, "user_id" => $driver_id));
        exit;
    }


    public function simulate_hsl_planner()
    {
        $campaign_id = $_SESSION['current_campaign'];
        $customer_postcode = $this->input->post('postcode');
        $branch_id = $this->input->post('branch_id');
        $driver_id = $this->input->post('driver_id');
        $slot = "1";
        if ($this->input->post('slot')) {
            $slot = $this->input->post('slot');
        }
        //get the user for the branch

        //step 1 get the drivers postcode
        $driver_postcode = $this->Planner_model->get_user_postcode($driver_id);
        $branch_postcode = $this->Planner_model->get_branch_postcode($branch_id);

        for ($i = 0; $i < 45; $i++) {
            if (date("D", strtotime('+' . $i . ' days')) <> "Sun") {
                $days[] = date("Y-m-d", strtotime('+' . $i . ' days'));
            }
        }

        $travel_info = array();
        $data = array();

        $driver_to_branch_details = $this->get_journey_details($driver_postcode, $branch_postcode);
        $branch_to_customer_details = $this->get_journey_details($branch_postcode, $customer_postcode);


        $stats = array("distance" => array("text" => "", "value" => 0), "duration" => array("text" => "", "value" => 0), "added_distance" => array("text" => "", "value" => 0), "added_duration" => array("text" => "", "value" => 0));

        foreach ($days as $day) {
            $app_duration = array(0 => 0, 1 => 0);
            $uk_date = date('D jS M', strtotime($day));
            $total_distance = 0;
            $total_duration = 0;
            $added_duration = 0;
            $added_distance = 0;
            $travel_info[$day][0] = $driver_to_branch_details;
            $added_distance = $branch_to_customer_details['distance'];
            $added_duration = $branch_to_customer_details['duration'];
            //get appointments for user in next 14 days
            $qry = "select date(start) `app_date`,postcode,TIME_TO_SEC(TIMEDIFF(`end`,`start`)) app_duration, if(time(`start`)<'13:00:00','am','pm') ampm from appointments join appointment_attendees using(appointment_id) join users using(user_id) where user_id = '$driver_id' and date(`start`) = '$day' and `status` = 1 order by `end` asc";
            $result = $this->db->query($qry)->result_array();

            $full = false;
            $apps = count($result);
            $appointment_1 = isset($result[0]) ? $result[0] : false;
            $appointment_2 = isset($result[1]) ? $result[1] : false;
            if ($appointment_1 && !$appointment_2 && $appointment_1['ampm'] == "am") {
                $slot = 2;
            } else {
                $slot = 1;
            }

            //adding uk date format to the start so we can show this instead of mysql
            $data[$day]['start'] = array("title" => "Driver Home", "postcode" => $driver_postcode, "uk_date" => $uk_date);
            $data[$day]['branch_start'] = array("title" => "Branch", "postcode" => $branch_postcode);

            /* if a PM appointment has already been booked this day the new appointment will be put first */
            if ($slot == "1" && $apps == "1") {
                $app_duration[0] = 3600;
                $app_duration[1] = $appointment_1['app_duration'];
                $travel_info[$day][1] = $branch_to_customer_details;
                $data[$day]['slot1'] = array("title" => "This Appointment", "postcode" => $customer_postcode, "app_duration_val" => $app_duration[0], "app_duration" => convertToHoursMins(3600 / 60, '%2dh %2dm'));
                $data[$day]['slot2'] = array("title" => "Second Appointment", "postcode" => $appointment_1['postcode'], "app_duration_val" => $app_duration[1], "app_duration" => convertToHoursMins($appointment_1['app_duration'] / 60, '%2dh %2dm'));

                //get distance between slots
                $travel_info[$day][2] = $this->get_journey_details($customer_postcode, $appointment_1['postcode']);
                $travel_info[$day][3] = $this->get_journey_details($appointment_1['postcode'], $branch_postcode);
            }
            /* if an AM appointment has already been booked this day the new appointment will be put last */
            if ($slot == "2" && $apps == "1") {
                $app_duration[0] = $appointment_1['app_duration'];
                $app_duration[1] = 3600;
                $travel_info[$day][1] = $this->get_journey_details($branch_postcode, $appointment_1['postcode']);

                $data[$day]['slot1'] = array("title" => "First Appointment", "postcode" => $appointment_1['postcode'], "app_duration_val" => $app_duration[0], "app_duration" => convertToHoursMins($appointment_1['app_duration'] / 60, '%2dh %2dm'));
                $data[$day]['slot2'] = array("title" => "This Appointment", "postcode" => $customer_postcode, "app_duration_val" => $app_duration[1], "app_duration" => convertToHoursMins(3600 / 60, '%2dh %2dm'));

                //get distance between slots
                $travel_info[$day][2] = $this->get_journey_details($appointment_1['postcode'], $customer_postcode);
                $travel_info[$day][3] = $branch_to_customer_details;

            }

            /* if the slots are both taken */
            if ($apps > 1) {
                $app_duration[0] = $appointment_1['app_duration'];
                $app_duration[1] = $appointment_2['app_duration'];
                $data[$day]['slot1'] = array("title" => "First Appointment", "postcode" => $appointment_1['postcode'], "app_duration_val" => $app_duration[0], "app_duration" => convertToHoursMins($appointment_1['app_duration'] / 60, '%2dh %2dm'));
                $data[$day]['slot2'] = array("title" => "Second Appointment", "postcode" => $appointment_2['postcode'], "app_duration_val" => $app_duration[1], "app_duration" => convertToHoursMins($appointment_2['app_duration'] / 60, '%2dh %2dm'));
                $travel_info[$day][1] = $this->get_journey_details($branch_postcode, $appointment_1['postcode']);
                $travel_info[$day][2] = $this->get_journey_details($appointment_1['postcode'], $appointment_2['postcode']);
                $travel_info[$day][3] = $this->get_journey_details($appointment_2['postcode'], $branch_postcode);
                //get distance between slots

            }

            /* if no slots are taken */
            if ($slot == "1" && $apps == "0") {
                $app_duration[0] = 3600;
                $app_duration[1] = 0;
                $travel_info[$day][1] = $branch_to_customer_details;
                $travel_info[$day][2] = $branch_to_customer_details;
                $travel_info[$day][3] = $stats;
                $data[$day]['slot1'] = array("title" => "This Appointment", "postcode" => $customer_postcode, "app_duration" => convertToHoursMins(3600 / 60, '%2dh %2dm'), "app_duration_val" => $app_duration[0]);
                $data[$day]['slot2'] = array("title" => "Second Appointment", "postcode" => "", "app_duration_val" => $app_duration[1]);

                $total_app_duration = $app_duration[0] + $app_duration[1];
            }

            $data[$day]['branch_end'] = array("title" => "Branch", "postcode" => $branch_postcode);
            $data[$day]['destination'] = array("title" => "Driver Home", "postcode" => $driver_postcode);
            $travel_info[$day][4] = $driver_to_branch_details;
        }

        foreach ($travel_info as $day => $a) {
            $total_distance = 0;
            $total_duration = 0;
            foreach ($a as $k => $v) {
                $total_distance += $v['distance']['value'];
                $total_duration += $v['duration']['value'];

                $data[$day]['start']['time'] = "09:00 am";
                if ($k == 0) {
                    $data[$day]['branch_start']['time'] = date('H:i a', strtotime("9am + " . $total_duration . " seconds"));
                }
                if ($k == 1) {
                    $data[$day]['slot1']['time'] = date('H:i a', strtotime("9am + " . $total_duration . " seconds"));
                    if ($slot == "1") {
                        $data[$day]['slot1']['datetime'] = date('H:i', strtotime("9am + " . $total_duration . " seconds"));
                    }
                }
                if ($k == 2) {
                    $dur = $total_duration + $data[$day]['slot2']['app_duration_val'];
                    $data[$day]['slot2']['time'] = date('H:i a', strtotime("9am + " . intval($dur) . " seconds"));
                    if ($slot == "2") {
                        $data[$day]['slot2']['datetime'] = date('H:i', strtotime("9am + " . intval($dur) . " seconds"));
                    }
                }
                if ($k == 3) {
                    $dur = $total_duration + $data[$day]['slot1']['app_duration_val'] + $data[$day]['slot2']['app_duration_val'];
                    $data[$day]['branch_end']['time'] = date('H:i a', strtotime("9am + " . intval($dur) . " seconds"));
                }
                if ($k == 4) {
                    $dur = $total_duration + $data[$day]['slot1']['app_duration_val'] + $data[$day]['slot2']['app_duration_val'];
                    $data[$day]['destination']['time'] = date('H:i a', strtotime("9am + " . intval($dur) . " seconds"));
                }


                $travel_info[$day][$k]['added_distance']['value'] = $total_distance;
                $travel_info[$day][$k]['added_duration']['value'] = $total_duration;

                $travel_info[$day][$k]['added_distance']['text'] = number_format(($total_distance / 1000) * 0.621371192, 1) . " Miles";
                $travel_info[$day][$k]['added_duration']['text'] = convertToHoursMins($total_duration / 60, '%2dh %2dm');
            }
            $total_duration += $total_app_duration;
            $travel_info[$day][5]['distance']['value'] = $total_distance;
            $travel_info[$day][5]['duration']['value'] = $total_duration;
            $travel_info[$day][5]['distance']['text'] = number_format(($total_distance / 1000) * 0.621371192, 1) . " Miles";
            $travel_info[$day][5]['duration']['text'] = convertToHoursMins($total_duration / 60, '%2dh %2dm');
            $travel_info[$day][5]['added_distance'] = $travel_info[$day][5]['distance'];
            $travel_info[$day][5]['added_duration'] = $travel_info[$day][5]['duration'];
        }

//also add the slots to the data to show in the panel
        $this->load->model('Appointments_model');
        $slots = array();
        $appointments = $this->Appointments_model->slot_availability(false, $driver_id);

        foreach ($appointments['apps'] as $date => $day) {
            $max_apps = 0;
            $apps = 0;
            foreach ($day as $k => $row) {
                if (isset($row['max_apps'])) {
                    $max_apps += $row['max_apps'];
                    $apps += $row['apps'];
                    $reason = isset($row['reason']) ? $row['reason'] : false;
                    $sql_date = DateTime::createFromFormat('D jS M y', $date)->format('Y-m-d');
                    $slots[$sql_date] = array("apps" => $apps, "max_apps" => $max_apps, "reason" => $reason);
                }
            }
        }
        echo json_encode(array("success" => true, "waypoints" => $data, "stats" => $travel_info, "slots" => $slots, "user_id" => $driver_id));

    }

    public function index()
    {

        $user_id = ($_SESSION['user_id'] ? $_SESSION['user_id'] : NULL);

        $campaign_branch_users = $this->getCampaignBranchUsers();
        $drivers = $this->Form_model->get_drivers();
        $campaign = false;
        if (isset($_SESSION['current_campaign'])) {
            $campaign = $_SESSION['current_campaign'];
        }
        $attendees = $this->Records_model->get_attendees(false, $campaign);

        $title = "Planner";

        $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'System planner',
            'title' => $title,
            'submenu' => array(
                "file"=>'planner.php',
                "title"=>$title
            ),
            'page' => 'planner',
            'campaign_branch_users' => $campaign_branch_users,
            'user_id' => $user_id,
            'drivers' => $drivers,
            'attendees' => $attendees,
            'css' => array(
                'daterangepicker-bs3.css',
                //'../js/plugins/DataTables/extensions/Scroller/css/dataTables.scroller.min.css',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.css',
                'map.css',
                'daterangepicker-bs3.css'
            ),
            'javascript' => array(
                'lib/moment.js',
                'lib/daterangepicker.js',
                'location.js?v' . $this->project_version,
                'map.js?v' . $this->project_version,
                'planner/planner.js?v' . $this->project_version,
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
                'plugins/touch-punch/jquery-ui-touch-punch.js',
                'plugins/fontawesome-markers/fontawesome-markers.min.js'
            )
        );
        $this->template->load('default', 'dashboard/planner_with_map.php', $data);
    }


    public function getCampaignBranchUsers()
    {
        $campaign_branch_users = $this->Planner_model->getCampaignBranchUsers();

        $aux = array();
        $aux['Campaigns'] = array();
        $aux['Others'] = array();
        foreach ($campaign_branch_users as $campaign_branch_user) {
            if (isset($campaign_branch_user['campaign_id'])) {
                if (!isset($aux['Campaigns'][$campaign_branch_user['campaign_name']][$campaign_branch_user['branch_name']])) {
                    $aux['Campaigns'][$campaign_branch_user['campaign_name']][$campaign_branch_user['branch_name']] = array();
                }
                $aux['Campaigns'][$campaign_branch_user['campaign_name']][$campaign_branch_user['branch_name']][$campaign_branch_user['user_id']] = $campaign_branch_user['name'];
            } else {
                $aux['Others'][$campaign_branch_user['user_id']] = $campaign_branch_user['name'];
            }
        }

        if (!in_array($_SESSION['user_id'], $aux['Others'])) {
            $aux['Others'][$_SESSION['user_id']] = $_SESSION['name'];
        }

        $campaign_branch_users = $aux;

        return $campaign_branch_users;
    }

    public function showBranches()
    {
        if ($this->input->is_ajax_request()) {
            $user_id = $this->input->post('user_id');

            $campaign_branch_users = $this->Planner_model->getCampaignBranchUsers();

            $aux = array();
            foreach ($campaign_branch_users as $campaign_branch_user) {
                if (isset($campaign_branch_user['campaign_id'])) {
                    if (!isset($aux[$campaign_branch_user['branch_id']]['current_branch']) || !$aux[$campaign_branch_user['branch_id']]['current_branch']) {
                        $campaign_branch_user['current_branch'] = ($campaign_branch_user['user_id'] === $user_id ? true : false);
                    } else {
                        $campaign_branch_user['current_branch'] = $aux[$campaign_branch_user['branch_id']]['current_branch'];
                    }
                    if (strpos($campaign_branch_user["map_icon"], ".png") !== false) {
                        $campaign_branch_user["map_icon_type"] = "image";
                    } else {
                        $campaign_branch_user["map_icon_type"] = "icon";
                        $campaign_branch_user["map_icon"] = ($campaign_branch_user["map_icon"] ? str_replace("FA_", "", str_replace("-", "_", strtoupper($campaign_branch_user["map_icon"]))) : NULL);
                    }

                    if (!isset($aux[$campaign_branch_user['branch_id']])) {
                        $campaign_branch_user['user_id'] = array($campaign_branch_user['user_id']);
                    } else {
                        $branch_user_id = $campaign_branch_user['user_id'];
                        $campaign_branch_user['user_id'] = $aux[$campaign_branch_user['branch_id']]['user_id'];
                        array_push($campaign_branch_user['user_id'], $branch_user_id);
                    }
                    $aux[$campaign_branch_user['branch_id']] = $campaign_branch_user;
                }
            }

            $campaign_branch_users = $aux;

            echo json_encode(array(
                "data" => $campaign_branch_users
            ));
        }
    }

    public function planner_data()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('Records_model');
            $records = $this->Planner_model->planner_data(false, $this->input->post());
            $letters = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M");
            $i = 0;
            $use_home = true;
            foreach ($records as $k => $v) {
                if ($v['planner_type'] == 1) {
                    $records[$k]["letter"] = "dd-start";
                    $use_home = false;
                } else if ($v['planner_type'] == 3) {
                    $records[$k]["letter"] = "dd-end";
                    $use_home = false;
                } else {
                    $records[$k]["letter"] = "marker" . $letters[$i];
                    $i++;
                }
                if (!empty($v['urn'])) {
                    $records[$k]["comments"] = $this->Records_model->get_last_comment($v['urn']);
                }

            }

            $data = array(
                "data" => $records
            );
            $data['use_home'] = false;
            if ($use_home) {
                $user_postcode = $this->Planner_model->get_user_postcode($this->input->post('user_id'));
                $data['user_postcode'] = $user_postcode;
                $data['use_home'] = true;
            }
            echo json_encode($data);
        }
    }

    public function add_record()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $postcode = $this->input->post('postcode');
            if (validate_postcode($postcode)) {
                $urn = $this->input->post('urn');
                $date = to_mysql_datetime($this->input->post('date'));
                if (strtotime($date) < strtotime('today')) {
                    echo json_encode(array("success" => false, "msg" => "You can only plan for the future!"));
                    exit;
                }
                if ($this->Planner_model->check_planner($urn, $this->user_id)) {
                    echo json_encode(array("success" => false, "msg" => "Record is already in planner"));
                    exit;
                }
                $this->Planner_model->add_record($urn, $date, $postcode, $this->user_id);
                echo json_encode(array("success" => true, "msg" => "Planner was updated"));
                exit;
            } else {
                echo json_encode(array("success" => false, "msg" => "Postcode is invalid"));
                exit;
            }
        } else {
            echo "denied";
            exit;
        }
    }

    public function remove_record()
    {
        if ($this->input->is_ajax_request() && $this->_access) {
            $urn = $this->input->post('urn');
            $this->Planner_model->remove_record($urn, $this->user_id);
            echo json_encode(array("success" => true, "msg" => "Planner was updated"));
        } else {
            echo "denied";
            exit;
        }
    }

    /**
     * Save the record route and the order selected/optimized
     */
    public function save_record_route()
    {
        if ($this->input->is_ajax_request()) {

            $record_list = $this->input->post('record_list');
            $date = $this->input->post('date');
            $origin = postcodeFormat($this->input->post('origin'));
            $destination = postcodeFormat($this->input->post('destination'));
            if (postcodeCheckFormat($origin) && !$this->Planner_model->get_location_id($origin)) {
                echo json_encode(array("success" => false, "error" => "The origin postcode is not valid"));
                exit;
            }
            if (postcodeCheckFormat($destination) && !$this->Planner_model->get_location_id($destination)) {
                echo json_encode(array("success" => false, "error" => "The destination postcode is not valid"));
                exit;
            }
            $this->Planner_model->save_record_route($record_list, $this->user_id, $date, $origin, $destination);
            $this->Planner_model->save_record_order($record_list, $this->user_id, $date, $origin, $destination);

            echo json_encode(array(
                "success" => true,
                "msg" => "Planner was updated"
            ));

        } else {

            echo "denied";
            exit;
        }
    }

    /**
     * Add appointment to the planner, assigned to the attendee and to all the users that belong to the branch region if that exists on the planner
     */
    public function add_appointment_to_the_planner()
    {

        $appointment_id = $this->input->post('appointment_id');

        if ($appointment_id) {
            //get the info for the planner
            $appointment_data = $this->Planner_model->getPlannerInfoByAppointment($appointment_id);
			if(!isset($appointment_data['region_users'])){
				$appointment_data['region_users']="";
			}
			if(!isset($appointment_data['branch_users'])){
				$appointment_data['branch_users']="";
			}
			
            $users = array_unique(array_merge(
                explode(',', $appointment_data['attendees']),
                explode(',', @$appointment_data['region_users']),
                explode(',', @$appointment_data['branch_users'])
            ));

            //Create the planner data for every user
            $planner_data = array();
            foreach ($users as $user) {
                $planner = array(
                    "urn" => $appointment_data['urn'],
                    "user_id" => $user,
                    "start_date" => $appointment_data['start'],
                    "postcode" => $appointment_data['postcode'],
                    "location_id" => $appointment_data['location_id'],
                    "order_num" => 20,
                    "planner_status" => PLANNER_STATUS_LIVE,
                    "planner_type" => PLANNER_TYPE_WAYPOINT,
                );

                array_push($planner_data, $planner);
            }

            //Add or update the record planner for each user
            foreach ($planner_data as $planner) {
                //Check if the planner already exist for this urn
                $planner_id = $this->Planner_model->check_planner($planner['urn'], $planner['user_id']);
                //If the status of the appointment is not cancellation, we add or update the appointment
                if ($appointment_data['status'] != APPOINTMENT_STATUS_CANCEL) {
                    if ($planner_id) {
                        //Update the planner
                        $this->Planner_model->update_record_planner($planner_id, $planner);
                    } else {
                        //Add the planner for that user
                        $this->Planner_model->add_record($planner['urn'], $planner['start_date'], $planner['postcode'], $planner['user_id']);
                    }
                } else if ($planner_id) {
                    //Cancel the planner if already exist
                    $this->Planner_model->remove_record($planner['urn'], $planner['user_id']);
                }
            }

            if ($appointment_data['status'] != APPOINTMENT_STATUS_CANCEL) {
                echo json_encode(array(
                    "success" => true,
                    "msg" => "Appointment added or updated on the planner"
                ));
            } else {
                echo json_encode(array(
                    "success" => true,
                    "msg" => "Appointment cancelled on the planner"
                ));
            }
        } else {
            echo json_encode(array(
                "success" => false,
                "msg" => "The appointment_id doesn't exist"
            ));
        }
    }

}