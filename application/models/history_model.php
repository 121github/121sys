<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class History_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->name_field = "concat(title,' ',firstname,' ',lastname)";
		$this->load->helper('query');
    }
	
	
	   //function to list all the records
    public function get_history($options,$urn=false)
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
        $required_tables = array("history", "record_planner", "record_planner_user", "ownership", "campaigns", "contact_locations", "company_locations");
        foreach ($required_tables as $rt) {
            if (!in_array($rt, $tables)) {
                $tables[] = $rt;
            }
        }

        $join = array();
        //add mandatory column selections here
        $required_select_columns = array("h.urn",
            "date_format(rp.start_date,'%d/%m/%Y') planner_date",
            "rp.user_id planner_user_id",
            "rp.record_planner_id",
            "rp.postcode as planner_postcode",
            "rpu.name planner_user",
            "h.urn marker_id",
            "GROUP_CONCAT(DISTINCT CONCAT(coma.postcode, '(',company_locations.lat,'/',company_locations.lng,')','|',company_locations.location_id) separator ',') as company_location",
            "GROUP_CONCAT(DISTINCT CONCAT(cona.postcode, '(',contact_locations.lat,'/',contact_locations.lng,')','|',contact_locations.location_id) separator ',') as contact_location",
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
		$qry = "";
        $select = "select $selections
                from records r ";
		$numrows = "select count(distinct h.urn) numrows
                from records r ";		
        //if any join is required we should apply it here
        if (isset($_SESSION['filter']['join'])) {
            $join = $_SESSION['filter']['join'];
        }

        //the joins for all the tables are stored in a helper
        $table_joins = table_joins();
        $join_array = join_array();

		$tablenum=0;
        foreach ($tables as $k=>$table) {
			if($table=="custom_panels"){ $tablenum++;
		
			$field_id = $datafield_ids[$k];
				$join[] = " left join (select max(id) id,urn from custom_panel_values join custom_panel_data using(data_id) where field_id = '$field_id' group by urn) mc_$field_id on mc_$field_id.urn =  r.urn left join custom_panel_values t_$field_id on t_$field_id.id = mc_$field_id.id ";
			}
			
			if($table<>"custom_panels"){
            if (array_key_exists($table, $join_array)) {
                foreach ($join_array[$table] as $t) {
                    $join[$t] = $table_joins[$t];
                }
            } else {
                $join[$table] = $table_joins[$table];
            }
        }
		}

        foreach ($join as $join_query) {
            $qry .= $join_query;
        }
		
	
        $qry .= get_where($options, $filter_columns);

		//get the total number of records before any limits or pages are applied
        $count = $this->db->query($numrows.$qry)->row()->numrows;
		
		$qry .= " group by h.urn";
		
        //if any order has been set then we should apply it here
        $start = $options['start'];
        $length = $options['length'];
        if (isset($_SESSION['filter']['order']) && $options['draw'] == "1") {
            $order = $_SESSION['filter']['order'];
        } else {
           $order = " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'] . ",h.urn";
            unset($_SESSION['filter']['order']);
            unset($_SESSION['filter']['values']['order']);
        }

        $qry .= $order;
		if($length>0){
        $qry .= "  limit $start,$length";
		}
		$this->firephp->log($select.$qry);
        $records = $this->db->query($select.$qry)->result_array();
        $records['count'] = $count;
        
        return $records;
    }
	
}