<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Audit_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }


    public function get_custom_panel_name($urn)
    {
        $this->db->select("custom_panel_name");
        $this->db->join("records", "records.campaign_id=campaigns.campaign_id");
        $this->db->where("urn", $urn);
        return $this->db->get("campaigns")->row()->custom_panel_name;
    }

############## log custom fields functions ###########################################

#################################################################################### 

    //custom field inserted
    public function log_custom_fields_insert($data = array(), $urn = NULL)
    {
        $id = $data['detail_id'];
        $details = array(
            'user_id' => $_SESSION['user_id'],
            'change_type' => "insert",
            'table_name' => 'record_details',
            'reference' => $id,
            'urn' => $urn
        );
        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();
        $log_fields = custom_fields();

        //get the custom field names
        $field_names = array();
        $custom_fields_query = "select field,field_name from record_details_fields join records using(campaign_id) join campaigns using(campaign_id) where urn = '" . $urn . "'";
        $custom_field_result = $this->db->query($custom_fields_query)->result_array();
        foreach ($custom_field_result as $row) {
            $field_names[$row['field']] = $row['field_name'];
        }


        foreach ($data as $column => $value) {
            if (in_array($column, $log_fields) && !empty($value)) {
                $fields = array(
                    'audit_id' => $audit_id,
                    'column_name' => $field_names[$column],
                    'oldval' => "",
                    'newval' => $value
                );
                $this->db->insert('audit_values', $fields);
            }
        }
        return $audit_id;
    }

    //custom field updated
    public function log_custom_fields_update($data = array(), $urn = NULL)
    {

        $id = $data['detail_id'];
        $qry = "SELECT * from record_details WHERE detail_id = '$id'";

        $original = $this->db->query($qry)->result_array();
        foreach ($original[0] as $key => $value) {

            if (!array_key_exists($key, $data)) {
                unset($original[0][$key]);
            }
        }
        foreach ($data as $k => $v) {
            if ($v && in_array($k, array("d1", "d2"))) {
                $date = DateTime::createFromFormat('d/m/Y', $v)->format('Y-m-d');
                $data[$k] = $date;
            }
            if ($v && in_array($k, array("dt1", "dt2"))) {
                $date = DateTime::createFromFormat('d/m/Y H:i:s', $v)->format('Y-m-d H:i:s');
                $data[$k] = $date;
            }
        }
        $diff = array_diff($data, $original[0]);
        $audit_id = NULL;

        if (count($diff) > 0) {
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "update",
                'table_name' => 'record_details',
                'reference' => $id,
                'urn' => $urn
            );
            $this->db->insert('audit', $details);
            //$this->firephp->log($this->db->last_query());
            $audit_id = $this->db->insert_id();
        }

        //get the custom field names
        $field_names = array();
        $custom_fields_query = "select field,field_name from record_details_fields join records using(campaign_id) join campaigns using(campaign_id) where urn = '" . $data['urn'] . "'";
        $custom_field_result = $this->db->query($custom_fields_query)->result_array();
        foreach ($custom_field_result as $row) {
            $field_names[$row['field']] = $row['field_name'];
        }

        foreach ($diff as $column => $value) {
            $oldval = (empty($original[0][$column]) ? "" : $original[0][$column]);
            $fields = array(
                'audit_id' => $audit_id,
                'column_name' => $field_names[$column],
                'oldval' => $oldval,
                'newval' => $value
            );

            $this->db->insert('audit_values', $fields);
        }

        return $audit_id;
    }


####################################################################################

############## log company functions ###########################################

####################################################################################

    //company inserted
    public function log_company_insert($data = array(), $urn = NULL)
    {
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
        $log_fields = array("name", "conumber", "turnover", "employees", "email");
        foreach ($data as $column => $value) {
            if (in_array($column, $log_fields) && !empty($value)) {
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
    public function log_company_update($data = array(), $urn = NULL)
    {

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
    public function log_company_delete($company_id)
    {

        $qry = "SELECT * from companies WHERE company_id = '$company_id'";
        $original = $this->db->query($qry)->row_array();

        $details = array(
            'user_id' => $_SESSION['user_id'],
            'reference' => $company_id,
            'change_type' => "delete",
            'table_name' => "companies",
            'urn' => $original['urn']
        );

        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        foreach ($original as $k => $v) {
            $log_fields = array("name", "conumber", "turnover", "employees", "email");
            if (in_array($k, $log_fields) && !empty($v)) {
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
    public function log_contact_insert($data = array(), $urn = NULL)
    {
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

        $log_fields = array("fullname", "gender", "position", "dob", "email", "website", "linkedin", "facebook", "notes");

        foreach ($data as $column => $value) {
            if (in_array($column, $log_fields) && !empty($value)) {
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
    public function log_contact_update($data = array(), $urn = NULL)
    {
        $audit_id = NULL;
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
    public function log_contact_delete($contact_id)
    {

        $qry = "SELECT * from contacts WHERE contact_id = '$contact_id'";
        $original = $this->db->query($qry)->row_array();

        $details = array(
            'user_id' => $_SESSION['user_id'],
            'reference' => $contact_id,
            'change_type' => "delete",
            'table_name' => "contacts",
            'urn' => $original['urn']
        );

        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();
        foreach ($original as $k => $v) {
            $log_fields = array("fullname", "gender", "position", "dob", "email", "website", "linkedin", "facebook", "notes");
            if (in_array($k, $log_fields) && !empty($v)) {
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
    public function log_appointment_insert($data = array(), $urn = NULL)
    {
        $this->firephp->log($data);
        unset($data['attendees']);
        $id = $data['appointment_id'];
        $details = array(
            'user_id' => $_SESSION['user_id'],
            'change_type' => "insert",
            'table_name' => 'appointments',
            'reference' => $id,
            'urn' => $data['urn']
        );
        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();
        $log_fields = array("address", "appointment_type_id", "start", "end", "text", "title", "contact_id", "postcode");
        foreach ($data as $column => $value) {
            if (in_array($column, $log_fields) && !empty($value)) {
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
    public function log_appointment_update($data = array())
    {
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
        $audit_id = NULL;
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

//cophone inserted
    public function log_cophone_insert($data = array(), $urn = NULL)
    {
        $id = $data['telephone_id'];
        $details = array(
            'user_id' => $_SESSION['user_id'],
            'change_type' => "insert",
            'table_name' => 'company_telephone',
            'reference' => $id,
            'urn' => $urn
        );
        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        $log_fields = array("telephone_id", "company_id", "telephone_number", "description", "ctps");

        foreach ($data as $column => $value) {
            if (in_array($column, $log_fields) && !empty($value)) {
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


//cophone inserted
    public function log_phone_insert($data = array(), $urn = NULL)
    {
        $id = $data['telephone_id'];
        $details = array(
            'user_id' => $_SESSION['user_id'],
            'change_type' => "insert",
            'table_name' => 'contact_telephone',
            'reference' => $id,
            'urn' => $urn
        );
        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        $log_fields = array("telephone_id", "contact_id", "telephone_number", "description", "ctps");

        foreach ($data as $column => $value) {
            if (in_array($column, $log_fields) && !empty($value)) {
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

//cophone inserted
    public function log_address_insert($data = array(), $urn = NULL)
    {
        $id = $data['address_id'];
        $details = array(
            'user_id' => $_SESSION['user_id'],
            'change_type' => "insert",
            'table_name' => 'contact_addresses',
            'reference' => $id,
            'urn' => $urn
        );
        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        $log_fields = array("address_id", "contact_id", "add1", "add2", "add3", "county", "country", "postcode");

        foreach ($data as $column => $value) {
            if (in_array($column, $log_fields) && !empty($value)) {
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

    public function log_address_update($data = array(), $urn = NULL)
    {
        $id = $data['address_id'];
        $qry = "SELECT * from contact_addresses WHERE address_id = '$id'";

        $original = $this->db->query($qry)->row_array();

        foreach ($original as $key => $value) {
            if (!array_key_exists($key, $data)) {
                unset($original[$key]);
            }
        }

        //compare the new data with the old data to see what has changed
        $diff = array_diff($data, $original);
        //$this->firephp->log($original[0]);
        $audit_id = NULL;
        $this->firephp->log($diff);
        //if something has changed we log the change
        if (count($diff) > 0) {
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "update",
                'table_name' => 'contact_addresses',
                'reference' => $id,
                'urn' => $urn
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


    public function log_coaddress_update($data = array(), $urn = NULL)
    {
        $id = $data['address_id'];
        $qry = "SELECT * from company_addresses WHERE address_id = '$id'";
        $original = $this->db->query($qry)->row_array();

        foreach ($original as $key => $value) {
            if (!array_key_exists($key, $data)) {
                unset($original[$key]);
            }
        }

        //compare the new data with the old data to see what has changed
        $diff = array_diff($data, $original);
        //$this->firephp->log($original[0]);
        $audit_id = NULL;

        //if something has changed we log the change
        if (count($diff) > 0) {
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "update",
                'table_name' => 'company_addresses',
                'reference' => $id,
                'urn' => $urn
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

    public function log_phone_update($data = array(), $urn = NULL)
    {
        $id = $data['telephone_id'];
        $qry = "SELECT * from contact_telephone WHERE telephone_id = '$id'";
        $original = $this->db->query($qry)->row_array();

        foreach ($original as $key => $value) {
            if (!array_key_exists($key, $data)) {
                unset($original[$key]);
            }
        }

        //compare the new data with the old data to see what has changed
        $diff = array_diff($data, $original);
        //$this->firephp->log($original[0]);
        $audit_id = NULL;

        //if something has changed we log the change
        if (count($diff) > 0) {
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "update",
                'table_name' => 'contact_telephone',
                'reference' => $id,
                'urn' => $urn
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

    public function log_cophone_update($data = array(), $urn = NULL)
    {
        $id = $data['telephone_id'];
        $qry = "SELECT * from company_telephone WHERE telephone_id = '$id'";
        $original = $this->db->query($qry)->row_array();

        foreach ($original as $key => $value) {
            if (!array_key_exists($key, $data)) {
                unset($original[$key]);
            }
        }

        //compare the new data with the old data to see what has changed
        $diff = array_diff($data, $original);
        //$this->firephp->log($original[0]);
        $audit_id = NULL;

        //if something has changed we log the change
        if (count($diff) > 0) {
            $details = array(
                'user_id' => $_SESSION['user_id'],
                'change_type' => "update",
                'table_name' => 'company_telephone',
                'reference' => $id,
                'urn' => $urn
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


    public function log_coaddress_insert($data = array(), $urn = NULL)
    {
        $id = $data['address_id'];
        $details = array(
            'user_id' => $_SESSION['user_id'],
            'change_type' => "insert",
            'table_name' => 'company_addresses',
            'reference' => $id,
            'urn' => $urn
        );
        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        $log_fields = array("address_id", "company_id", "add1", "add2", "add3", "county", "country", "postcode");

        foreach ($data as $column => $value) {
            if (in_array($column, $log_fields) && !empty($value)) {
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

    public function log_phone_delete($id, $urn)
    {

        $qry = "SELECT * from contact_telephone WHERE telephone_id = '$id'";
        $original = $this->db->query($qry)->row_array();

        $details = array(
            'user_id' => $_SESSION['user_id'],
            'reference' => $id,
            'change_type' => "delete",
            'table_name' => "contact_telephone",
            'urn' => $urn
        );

        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        foreach ($original as $k => $v) {
            $log_fields = array("telephone_id", "contact_id", "telephone_number", "description", "tps");
            if (in_array($k, $log_fields) && !empty($v)) {
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


    public function log_cophone_delete($id, $urn)
    {
        $qry = "SELECT * from company_telephone WHERE telephone_id = '$id'";
        $original = $this->db->query($qry)->row_array();

        $details = array(
            'user_id' => $_SESSION['user_id'],
            'reference' => $id,
            'change_type' => "delete",
            'table_name' => "company_telephone",
            'urn' => $urn
        );
        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        foreach ($original as $k => $v) {
            $log_fields = array("telephone_id", "company_id", "telephone_number", "description", "ctps");
            if (in_array($k, $log_fields) && !empty($v)) {
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

    public function log_address_delete($id, $urn)
    {

        $qry = "SELECT * from contact_addresses WHERE address_id = '$id'";
        $original = $this->db->query($qry)->row_array();

        $details = array(
            'user_id' => $_SESSION['user_id'],
            'reference' => $id,
            'change_type' => "delete",
            'table_name' => "contact_addresses",
            'urn' => $urn
        );

        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        foreach ($original as $k => $v) {
            $log_fields = array("address_id", "contact_id", "add1", "add2", "add3", "county", "country", "postcode", "primary");
            if (in_array($k, $log_fields) && !empty($v)) {
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


    public function log_coaddress_delete($id, $urn)
    {

        $qry = "SELECT * from company_addresses WHERE address_id = '$id'";
        $original = $this->db->query($qry)->row_array();

        $details = array(
            'user_id' => $_SESSION['user_id'],
            'reference' => $id,
            'change_type' => "delete",
            'table_name' => "company_addresses",
            'urn' => $urn
        );

        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        foreach ($original as $k => $v) {
            $log_fields = array("address_id", "company_id", "add1", "add2", "add3", "county", "country", "postcode", "primary");
            if (in_array($k, $log_fields) && !empty($v)) {
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

//this function should be ran BEFORE the deletion actually occurs so it can make a log of the old data
    public function log_appointment_delete($appointment_id)
    {

        $qry = "SELECT * from appointments WHERE appointment_id = '$appointment_id'";
        $original = $this->db->query($qry)->row_array();

        $details = array(
            'user_id' => $_SESSION['user_id'],
            'reference' => $appointment_id,
            'change_type' => "delete",
            'table_name' => "appointments",
            'urn' => $original['urn']
        );

        $this->db->insert('audit', $details);
        $audit_id = $this->db->insert_id();

        foreach ($original as $k => $v) {
            $log_fields = array("title", "text", "start", "end", "postcode");
            if (in_array($k, $log_fields) && !empty($v)) {
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

    public function audit_data($options = false)
    {
        $table_columns = array(
            "campaign_name",
            "urn",
            "table_name",
            "change_type",
            "name",
            "date_format(`timestamp`,'%d/%m/%y %H:%i')",
        );
        $order_columns = array(
            "campaign_name",
            "urn",
            "table_name",
            "change_type",
            "name",
            "timestamp"
        );

        $fields = "campaign_name,table_name,change_type,column_name,name,date_format(`timestamp`,'%d/%m/%Y %H:%i') `timestamp`,urn,audit_id ";
		$user_campaigns = "";
		if(!$_SESSION['data_access']['all_campaigns']){
		$user_campaigns = " join users_to_campaigns uc on uc.user_id = users.user_id ";
		}
			
        $qry = "select $fields from audit left join audit_values using(audit_id) left join records using(urn) left join campaigns using(campaign_id) left join users using(user_id) $user_campaigns ";
		$numrows = "select count(distinct audit_id) numrows from audit left join audit_values using(audit_id) left join records using(urn) left join campaigns using(campaign_id) left join users using(user_id) $user_campaigns";
		
		
        $where = $this->get_where($options, $table_columns);
		
		if($_SESSION['data_access']['user_records']){
		$where .= " and users.user_id = '".$_SESSION['user_id']."' ";
		}
		
        $qry .= $where;
		$this->firephp->log($qry);
		$count = $this->db->query($numrows.$where)->row()->numrows;

        $start = $options['start'];
        $length = $options['length'];
        if (isset($_SESSION['audit_table']['order']) && $options['draw'] == "1") {
            $order = " order by `timestamp` desc ";
        } else {
            $order = " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'];
            unset($_SESSION['audit_table']['order']);
            unset($_SESSION['audit_table']['values']['order']);
        }
        $qry .= " group by audit.audit_id";
        $qry .= $order;
        $qry .= "  limit $start,$length";
        $result = $this->db->query($qry)->result_array();
		$result['count'] = $count;
        return $result;
    }


    private function get_where($options, $table_columns)
    {
        //the default condition in ever search query to stop people viewing campaigns they arent supposed to!
        $where = " where 1 ";
		if(isset($_SESSION['current_campaign'])){
		 $where .= " and campaign_id = '".$_SESSION['current_campaign']."' ";
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

    public function audit_modal($id)
    {
        $qry = "select * from audit left join users using(user_id) where audit_id = " . intval($id);
        return $this->db->query($qry)->row_array();
    }

    public function audit_values($id)
    {
        $qry = "select * from audit_values where audit_id = " . intval($id);
        return $this->db->query($qry)->result_array();
    }

}
