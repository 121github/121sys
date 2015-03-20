<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Appointments_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
  
public function appointment_data($count=false,$options=false){
	      $table_columns = array(
            "start",
            "com.name",
            "title",
            "u.name",
             "date_format(a.`date_added`,'%d/%m/%y')",
			 "postcode"
        );
        $order_columns = array(
            "start",
            "com.name",
            "title",
            "u.name",
            "a.date_added",
			"postcode"
        );
	
	$qry = "select date_format(`start`,'%d/%m/%y %H:%i') start,com.name,title,u.name attendee,date_format(a.`date_added`,'%d/%m/%y') date_added,postcode,appointment_id from appointments a left join appointment_attendees aa using(appointment_id) left join users u on u.user_id = aa.user_id left join records using(urn) left join companies com using(urn) ";
	$where = $this->get_where($options, $table_columns);
		$qry .= $where;
		$qry .= " group by appointment_id";
	if($count){
		
	return $this->db->query($qry)->num_rows();
	}
	

	 $start  = $options['start'];
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
        
        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $where .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        return $where;
    }
public function appointment_modal($id){
	$qry = "select * from appointments a left join users u on u.user_id = a.created_by where appointment_id = ".intval($id)." group by appointment_id";	
	return $this->db->query($qry)->row_array();
}

public function appointment_attendees($id){
	$qry = "select name from appointment_attendees left join users using(user_id) where appointment_id = ".intval($id);	
	return $this->db->query($qry)->result_array();
}

}
