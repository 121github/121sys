<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recordings_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
        
    }

	public function get_numbers($urn){
	$qry = "select replace(telephone_number,' ','') as telephone_number,contact_telephone.description from contact_telephone left join contacts using(contact_id) where urn = '$urn'";	
	$result =  $this->db->query($qry)->result_array();	
	foreach($result as $row){
	$numbers[] = array("description"=>$row['description'],"number"=>$row['telephone_number']);	
	}
	$qry = "select replace(telephone_number,' ','') as telephone_number,company_telephone.description from company_telephone left join companies using(company_id) where urn = '$urn'";	
	$result =  $this->db->query($qry)->result_array();	
		foreach($result as $row){
	$numbers[] = array("description"=>$row['description'],"number"=>$row['telephone_number']);
	}
	return $numbers;
	}
	
	public function get_where($options, $table_columns){
		 $where = "";
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
		return $where;
	}
	
	public function get_all_recordings($options){
		$tables = array();
        $columns =  $options['visible_columns']['columns'];
        $table_columns = $options['visible_columns']['select'];
        $filter_columns = $options['visible_columns']['filter'];
        $order_columns = $options['visible_columns']['order'];
		
		
	  	$table_columns[] = "filepath";
		
		$db2 = $this->load->database('121backup',true);
		$selections = implode(",", $table_columns);
		
		
		
		$qry = "";
        $select = "select $selections
                from recordings.calls where 1 ";		
		$numrows = "select count(*) numrows
                from recordings.calls where 1 ";	 
		$qry .= " and `calldate` > date_sub(curdate(),interval 1 month) and TIMEDIFF(endtime,starttime) > '00:00:05' ";
		$qry .= $this->get_where($options,$filter_columns);
		
		
		$count = $db2->query($numrows)->row()->numrows;
		if(isset($options['order'][0]['column'])){
		$order = " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'] . ",id";
		}
		
		$start = $options['start'];
        $length = $options['length'];
		if($options['draw']=="1"){
		$length = 10;	
		}
        $qry .= $order;
		if($length>0){
        $qry .= "  limit $start,$length";
		}
		$this->firephp->log($select.$qry);
        $recordings = $this->db->query($select.$qry)->result_array();
        $recordings['count'] = $count;
        return $recordings;
	}
	
}
