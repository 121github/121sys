<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Audit_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
  
####################################################################################

############## log company functions ###########################################

####################################################################################
  
  //company inserted
      public function log_company_insert($data = array(),$urn=NULL) {
			$id = $data['id'];
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "insert",
                'table_name' => 'companies',
                'reference' => $id,
				'urn' => $urn
            );
        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();
		$log_fields = array("name","conumber","turnover","employees","email");
        foreach ($data as $column => $value) {
             if (in_array($column,$log_fields)&&!empty($value)) {
            $fields = array(
                'audit_id' => $audit_id,
                'column_name' => $column,
                'oldval' => "",
                'newval' => $value
            );
                $this->db->insert('audit_values', $fields);
            }
        }
        return $audit_id;
    }
  
  //company updated
    public function log_company_update($data = array(),$urn=NULL) {
		
        $id = $data['company_id'];
        $qry = "SELECT * from companies WHERE company_id = '$id'";
        $original = $this->db->query($qry)->result_array();
        foreach ($original[0] as $key => $value) {
			
            if (!array_key_exists($key, $data)) {
                unset($original[0][$key]);
            }
        }

        $diff = array_diff($data, $original[0]);
        $audit_id = NULL;

        if (count($diff) > 0) {
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "update",
                'table_name' => 'companies',
                'reference' => $id,
				'urn' => $urn
            );
            $this->db->insert('audit', $details);
			//$this->firephp->log($this->db->last_query());
            $audit_id = $this->db->insert_id();
        }

        foreach ($diff as $column => $value) {
            $oldval = (empty($original[0][$column]) ? "" : $original[0][$column]);
			
            $fields = array(
                'audit_id' => $audit_id,
                'column_name' => $column,
                'oldval' => $oldval,
                'newval' => $value
            );

            $this->db->insert('audit_values', $fields);
        }

        return $audit_id;
    }
	
//company deleted	
//this function should be ran BEFORE the deletion actually occurs so it can make a log of the old data */
    public function log_company_delete($company_id) {
		
		$qry = "SELECT * from companies WHERE company_id = '$company_id'";
        $original = $this->db->query($qry)->row_array();
		
        $details = array(
            'user_id' => $_SESSION['user_id'],
			'reference'=>$company_id,
            'change_type' => "delete",
            'table_name' => "companies",
            'urn' => $original['urn']
        );

        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();
		
        foreach ($original as $k => $v) {
		$log_fields = array("name","conumber","turnover","employees","email");
		if(in_array($k,$log_fields)&&!empty($v)){
            $fields = array(
                'audit_id' => $audit_id,
                'oldval' => $v,
                'newval' => NULL,
                'column_name' => $k
            );
		 $this->db->insert('audit_values', $fields);
		}
        }

        return $audit_id;
    }
	
####################################################################################

############## log contact functions ###########################################

####################################################################################
	
	
//contact inserted
    public function log_contact_insert($data = array(),$urn=NULL) {
			$id = $data['contact_id'];
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "insert",
                'table_name' => 'contacts',
                'reference' => $id,
				'urn' => $urn
            );
        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();
		
		$log_fields = array("fullname","gender","position","dob","email","website","linkedin","facebook","notes");
		
        foreach ($data as $column => $value) {
            if (in_array($column,$log_fields)&&!empty($value)) {
            $fields = array(
                'audit_id' => $audit_id,
                'column_name' => $column,
                'oldval' => "",
                'newval' => $value
            );
                $this->db->insert('audit_values', $fields);
            }
        }
        return $audit_id;
    }


//contact updated
    public function log_contact_update($data = array(),$urn=NULL) {
        $id = $data['contact_id'];
        $qry = "SELECT * from contacts WHERE contact_id = '$id'";
        $original = $this->db->query($qry)->row_array();
        $urn = $original['urn'];

        foreach ($original as $key => $value) {
            if (!array_key_exists($key, $data)) {
                unset($original[$key]);
            }
        }
        $diff = array_diff($data, $original);

        if (count($diff) > 0) {
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "update",
                'table_name' => 'contacts',
                'reference' => $id,
				'urn' => $urn
            );
            $this->db->insert('audit', $details);
            $audit_id = $this->db->insert_id();
            foreach ($diff as $column => $value) {
				
				$oldval = (empty($original[$column]) ? "" : $original[$column]);
                $fields = array(
                    'audit_id' => $audit_id,
                    'column_name' => $column,
                    'oldval' => $oldval,
                    'newval' => $value
                );
                $this->db->insert('audit_values', $fields);
            }
 			
            return $audit_id;
        }
    }
	
	
	//contact deleted
	// this function should be ran BEFORE the deletion actually occurs so it can make a log of the old data
    public function log_contact_delete($contact_id) {
		
		$qry = "SELECT * from contacts WHERE contact_id = '$contact_id'";
        $original = $this->db->query($qry)->row_array();
		
        $details = array(
            'user_id' => $_SESSION['user_id'],
			'reference'=>$contact_id,
            'change_type' => "delete",
            'table_name' => "contacts",
            'urn' => $original['urn']
        );

        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();
        foreach ($original as $k => $v) {
		$log_fields = array("fullname","gender","position","dob","email","website","linkedin","facebook","notes");
		if(in_array($k,$log_fields)&&!empty($v)){
            $fields = array(
                'audit_id' => $audit_id,
                'oldval' => $v,
                'newval' => NULL,
                'column_name' => $k
            );
		 $this->db->insert('audit_values', $fields);
		}
        }
        return $audit_id;
    }

####################################################################################

############## log appointment functions ###########################################

####################################################################################
//appointment inserted
 public function log_appointment_insert($data = array(),$urn=NULL) {
			$id = $data['id'];
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "insert",
                'table_name' => 'appointments',
                'reference' => $id,
				'urn' => $urn
            );
        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        foreach ($data as $column => $value) {
            if (!empty($value)) {
            $fields = array(
                'audit_id' => $audit_id,
                'column_name' => $column,
                'oldval' => "",
                'newval' => $value
            );
                $this->db->insert('audit_values', $fields);
            }
        }
        return $audit_id;
    }
//appointment updated  
    public function log_appointment_update($data = array()) {
		unset($data['attendees']);
        $id = $data['appointment_id'];
        $qry = "SELECT * from appointments WHERE appointment_id = '$id'";
        $original = $this->db->query($qry)->row_array();

        foreach ($original as $key => $value) {
            if (!array_key_exists($key, $data)) {
                unset($original[$key]);
            }
        }
		
        //compare the new data with the old data to see what has changed
        $diff = array_diff($data, $original);
        //$this->firephp->log($original[0]);
        $log_id = NULL;

        //if something has changed we log the change
        if (count($diff) > 0) {
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "update",
                'table_name' => 'appointments',
                'reference' => $id,
				'urn' => $original['urn']
            );
            $this->db->insert('audit', $details);
            $audit_id = $this->db->insert_id();
        }
        //we also log the associated values in the log data table
        foreach ($diff as $column => $value) {
            $oldval = (empty($original[$column]) ? "" : $original[$column]);

            $fields = array(
                'audit_id' => $audit_id,
                'column_name' => $column,
                'oldval' => $oldval,
                'newval' => $value
            );

            $this->db->insert('audit_values', $fields);
        }

        return $audit_id;
    }
	
//appointment deleted	
//this function should be ran BEFORE the deletion actually occurs so it can make a log of the old data
    public function log_appointment_delete($appointment_id) {
		
		$qry = "SELECT * from appointments WHERE appointment_id = '$appointment_id'";
        $original = $this->db->query($qry)->row_array();
		
        $details = array(
            'user_id' => $_SESSION['user_id'],
			'reference'=>$appointment_id,
            'change_type' => "delete",
            'table_name' => "appointments",
            'urn' =>  $original['urn']
        );

        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();
		
        foreach ($original as $k => $v) {
		$log_fields = array("title","text","start","end","postcode");
		if(in_array($k,$log_fields)&&!empty($v)){
            $fields = array(
                'audit_id' => $audit_id,
                'oldval' => $v,
                'newval' => NULL,
                'column_name' => $k
            );
		 $this->db->insert('audit_values', $fields);
		}
        }

        return $audit_id;
    }

public function audit_data($count=false,$options=false){
	      $table_columns = array(
            "campaign_name",
            "table_name",
            "change_type",
            "column_name",
            "name",
            "date_format(`timestamp`,'%d/%m/%y')",
        );
        $order_columns = array(
            "campaign_name",
            "table_name",
            "change_type",
            "column_name",
            "name",
            "timestamp"
        );
	
	$qry = "select campaign_name,table_name,change_type,column_name,name,date_format(`timestamp`,'%d/%m/%Y %H:%i') `timestamp`,urn,audit_id from audit left join audit_values using(audit_id) left join records using(urn) left join campaigns using(campaign_id) left join users using(user_id)";
	$where = $this->get_where($options, $table_columns);
		$qry .= $where;
	if($count){
	return $this->db->query($qry)->num_rows();
	}
	

	 $start  = $options['start'];
        $length = $options['length'];
        if (isset($_SESSION['audit_table']['order']) && $options['draw'] == "1") {
            $order = $_SESSION['audit_table']['order'];
        } else {
            $order = " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'];
            unset($_SESSION['audit_table']['order']);
            unset($_SESSION['audit_table']['values']['order']);
        }
        
        $qry .= $order;
        $qry .= "  limit $start,$length";
	$result = $this->db->query($qry)->result_array();
	return $result;
}

private function get_where($options, $table_columns)
    {
        //the default condition in ever search query to stop people viewing campaigns they arent supposed to!
        $where = " where 1 ";
        
        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $where .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        return $where;
    }
public function audit_modal($id){
	$qry = "select * from audit left join users using(user_id) where audit_id = ".intval($id);	
	return $this->db->query($qry)->row_array();
}

public function audit_values($id){
	$qry = "select * from audit_values where audit_id = ".intval($id);	
	return $this->db->query($qry)->result_array();
}

}
