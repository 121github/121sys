<?php
//require('upload.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Import extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
        $this->load->model('Import_model');
    }
    
    public function upload_file()
    {
        //if file upload successfully
        echo json_encode(array(
            "success" => true
        ));
    }
    
    
    public function import_csv()
    {
        $table    = "importcsv";
        $csv_file = "sample_big.csv";
        $output   = array();
		//$this->Import_model->drop_importcsv();
        exec('bash importcsv.sh "datafiles/' . $csv_file . '" ' . $table, $output);
		$this->firephp->log($output);
		$this->add_urns();
        $this->format_data();
        $this->create_records();
		$this->create_record_details();
        $this->create_contacts();
        $this->create_contact_telephones();
		$this->create_contact_addresses();
        $this->create_companies();
        $this->create_company_telephones();
		$this->create_company_addresses();
		
        //if csv imports successfully
        echo json_encode(array(
            "success" => true,
            "output" => $output
        ));
    }
    
    public function add_urns()
    {
        $query = $this->db->query("SHOW COLUMNS FROM `importcsv` where `Field` = 'urn'");
		$urn = 1;
        //if the csv has no urn column we make one starting from the max urn in the records table
        if ($query->num_rows() == 0) {
            $urn = $this->db->query("select max(urn)+1 urn from records")->row()->urn;
            if (empty($urn)) {
                $urn = 1;
            }
            $this->db->query("ALTER TABLE `importcsv` ADD `urn` INT NULL AUTO_INCREMENT PRIMARY KEY,AUTO_INCREMENT=$urn");
        }
        $this->firephp->log($urn);
        echo json_encode(array(
            "success" => true
        ));
    }
    
    
    public function format_data()
    {
        //format telephone numbers
        $fields = $this->db->query("SHOW COLUMNS FROM `importcsv` LIKE '%_tel_%'")->result_array();
        if ($fields > 0) {
            foreach ($fields as $row) {
                $field = $row['Field'];
                $this->db->query("update importcsv set `$field` = trim(replace(`$field`,' ',''))");
            }
        }
        
        //set empty values as NULL
        $fields = $this->db->query("SHOW COLUMNS FROM `importcsv`")->result_array();
        if ($fields > 0) {
            foreach ($fields as $row) {
                $field = $row['Field'];
                $this->db->query("update importcsv set `$field` = NULL where `$field` = ''");
            }
        }
        
        //format UK dates as SQL
        $fields = $this->db->query("SHOW COLUMNS FROM `importcsv` where `Field` in('custom_d1','custom_d2','custom_d3','custom_dt1','custom_dt2','custom_dt3','dob','lastcall','nextcall') ")->result_array();
        if ($fields > 0) {
            foreach ($fields as $row) {
                $field = $row['Field'];
                $this->db->query("update importcsv set `$field` = str_to_date(`$field`,'%d/%m/%Y %H:%i:%s') where `$field` like '%/%'");
            }
        }
        
        //if data formats successfully
        echo json_encode(array(
            "success" => true
        ));
    }
    
    //create records 
    public function create_records()
    {
        $campaign_id = 1;
        $source_id   = 1;
        $qry_fields  = $this->Import_model->get_fields("records");
        if (!empty($qry_fields)) {
            $insert_query = "insert into records (campaign_id,source_id " . $qry_fields['table_fields'] . ") select '" . $campaign_id . "','" . $source_id . "' " . $qry_fields['import_fields'] . " from importcsv";
            $this->firephp->log($insert_query);
            $this->db->query($insert_query);
        }
        echo json_encode(array(
            "success" => true
        ));
    }
    
	    //create record details 
    public function create_record_details()
    {
        $qry_fields  = $this->Import_model->get_fields("record_details");
        if (!empty($qry_fields)) {
            $insert_query = "insert into record_details (detail_id" . $qry_fields['table_fields'] . ") select ''" . $qry_fields['import_fields'] . " from importcsv";
            $this->firephp->log($insert_query);
            $this->db->query($insert_query);
        }
        echo json_encode(array(
            "success" => true
        ));
    }
	
    //create contacts
    public function create_contacts()
    {
        $qry_fields = $this->Import_model->get_fields("contacts","contact");
        if (!empty($qry_fields)) {
            $insert_query        = "insert into contacts (contact_id " .  $qry_fields['table_fields'] . ") select '' " . $qry_fields['import_fields'] . " from importcsv";
            $update_import_table = "ALTER TABLE `importcsv` ADD `contact_id` INT NULL ,ADD INDEX ( `contact_id` )";
            $insert_contact_ids  = "update importcsv i left join contacts c using(urn) set i.contact_id = c.contact_id";
            
            $this->firephp->log($insert_query);
            $this->db->query($insert_query);
			$this->db->query($update_import_table);
			$this->db->query($insert_contact_ids);
        }
        echo json_encode(array(
            "success" => true
        ));
    }
    
    //create contact telephones
    public function create_contact_telephones()
    {
        $number_descriptions = $this->Import_model->get_telephone_numbers("contact");
        foreach ($number_descriptions as $description) {
            $insert_query = "insert into contact_telephone (telephone_id,contact_id,description,telephone_number) select '',contact_id,contact_tel_" . $description . ",'$description' from importcsv ";
            $this->firephp->log($insert_query);
            $this->db->query($insert_query);
        }
        echo json_encode(array(
            "success" => true
        ));
    }
    
     //create contact addresses
    public function create_contact_addresses()
    {
        $qry_fields = $this->Import_model->get_addresses("contact");
        if (!empty($qry_fields)) {
			$insert_query = "insert into contact_addresses (address_id,contact_id " .$qry_fields['table_fields'].",`primary`) select '',contact_id " . $qry_fields['import_fields'] . ",'1' from importcsv";
            $this->firephp->log($insert_query);
            $this->db->query($insert_query);
        }
        echo json_encode(array(
            "success" => true
        ));
        
    }
    
    //create companies
    public function create_companies()
    {
        $qry_fields = $this->Import_model->get_fields("companies","company");
        if (!empty($qry_fields)) {
            $insert_query        = "insert into companies (company_id " . $qry_fields['table_fields'] . ") select '' " . $qry_fields['import_fields']. " from importcsv";
            $update_import_table = "ALTER TABLE `importcsv` ADD `company_id` INT NULL ,ADD INDEX ( `company_id` )";
            $insert_company_ids  = "update importcsv i left join companies c using(urn) set i.company_id = c.company_id";
            			
            $this->firephp->log($insert_query);
            $this->db->query($insert_query);
			$this->db->query($update_import_table);
			$this->db->query($insert_company_ids);
        }
        echo json_encode(array(
            "success" => true
        ));
        
    }
    
    //create company telephone numbers
    public function create_company_telephones()
    {
        $number_descriptions = $this->Import_model->get_telephone_numbers("company");
        foreach ($number_descriptions as $description) {
            $insert_query = "insert into company_telephone (telephone_id,company_id,description,telephone_number) select '',company_id,company_tel_" . $description . ",'$description' from importcsv";
            $this->firephp->log($insert_query);
            $this->db->query($insert_query);
        }
        echo json_encode(array(
            "success" => true
        ));
    }
    
    
    //create company addresses
    public function create_company_addresses()
    {
        $qry_fields = $this->Import_model->get_addresses("company");
        if (!empty($qry_fields)) {
			$insert_query = "insert into company_addresses (address_id,company_id " .$qry_fields['table_fields'] .",`primary`) select '',company_id " . $qry_fields['import_fields'] . ",'1' from importcsv";
            $this->firephp->log($insert_query);
            $this->db->query($insert_query);
        }
        echo json_encode(array(
            "success" => true
        ));
        
    }
    
    
}