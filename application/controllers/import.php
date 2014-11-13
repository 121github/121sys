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
		$this->_campaigns = campaign_access_dropdown();
        $this->load->model('Import_model');
		$this->load->model('Form_model');
        $this->load->model('Company_model');
        $this->load->model('Contacts_model');
    }

//clean up database if the import fails
public function undo_changes(){
if($this->db->Import_model->undo_changes()){
echo json_encode(array("success"=>true));	
}
}
	
	  //this loads the data management view
    public function index()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $sources   = $this->Form_model->get_sources();
        $data      = array(
            'campaign_access' => $this->_campaigns,
			'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => array(
                'admin' => 'data'
            ),
            'javascript' => array(
                'plugins/jqfileupload/vendor/jquery.ui.widget.js',
                'plugins/jqfileupload/jquery.iframe-transport.js',
                'plugins/jqfileupload/jquery.fileupload.js',
                'plugins/jqfileupload/jquery.fileupload-process.js',
                'plugins/jqfileupload/jquery.fileupload-validate.js',
                'import.js'
            ),
            'campaigns' => $campaigns,
            'sources' => $sources,
            'css' => array(
                'dashboard.css',
                'plugins/jqfileupload/jquery.fileupload.css'
            )
        );
        $this->template->load('default', 'data/import.php', $data);
    }
	
	
    public function import_csv()
    {
	
		$database = $this->db->database;
        $table    = "importcsv";
        $csv_file = $this->input->post('filename');
		if(empty($csv_file)){
		$csv_file = "import_sample.csv";	
		}
        $output   = array();
		//run the bash script
		$command ='bash importcsv.sh "datafiles/' . $csv_file . '" ' . $table . ' ' . $database;
		$this->firephp->log($command);
        exec($command,$output);
		if($this->Import_model->check_import()){
        //if csv imports successfully
        echo json_encode(array(
            "success" => true
        ));
		}
		else {
		 echo json_encode(array(
            "output" => $output	
        ));	
		}
    }
    
	public function start_import(){
		$this->import_csv();
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
	}
	
    public function add_urns()
    {
		$columns = $this->Import_model->get_import_fields();
		if(in_array("urn",$columns)){
        $this->db->query("ALTER TABLE `importcsv` DROP `urn`");
		}
		if(in_array("contact_id",$columns)){
        $this->db->query("ALTER TABLE `importcsv` DROP `contact_id`");
		}
		if(in_array("company_id",$columns)){
        $this->db->query("ALTER TABLE `importcsv` DROP `company_id`");
		}
		$urn = 1;
        //if the csv has no urn column we make one starting from the max urn in the records table
            $urn = $this->db->query("select max(urn)+1 urn from records")->row()->urn;
            if (empty($urn)) {
                $urn = 1;
            }
			$this->firephp->log("Starting URN: ".$urn);
            $this->db->query("ALTER TABLE `importcsv` ADD `urn` INT NULL AUTO_INCREMENT PRIMARY KEY,AUTO_INCREMENT=$urn");
        echo json_encode(array(
            "success" => true,"action"=>"configure unique keys"
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
        $fields = $this->db->query("SHOW COLUMNS FROM `importcsv` where `Field` in('d1','d2','d3','dt1','dt2','dt3','contact_dob','records_lastcall','records_nextcall') ")->result_array();
        if ($fields > 0) {
            foreach ($fields as $row) {
                $field = $row['Field'];
                $this->db->query("update importcsv set `$field` = str_to_date(`$field`,'%d/%m/%Y %H:%i:%s') where `$field` like '%/%'");
            }
        }
        
		        //format phone numebrs - append leading zero
        $fields = $this->db->query("SHOW COLUMNS FROM `importcsv` where `Field` like '%_tel_%' ")->result_array();
        if ($fields > 0) {
            foreach ($fields as $row) {
                $field = $row['Field'];
                $this->db->query("update importcsv set `$field` = concat('0',`$field`) where `$field` not like '0%' and `$field` not like '+%'");
            }
        }
		
        //if data formats successfully
        echo json_encode(array(
            "success" => true,"action"=>"format data"
        ));
    }
    
    //create records 
    public function create_records()
    {
        $campaign_id = $this->input->post('campaign');
        $source_id   = $this->input->post('source');
		
		if(empty($campaign_id)||empty($source_id)){
		echo json_encode(array(
            "success" => false,"msg"=>"You must set the campaign and data source"
        ));	
		exit;
		}
		
        $qry_fields  = $this->Import_model->get_fields("records");
        if (!empty($qry_fields)) {
            $insert_query = "insert into records (campaign_id,source_id " . $qry_fields['table_fields'] . ") select '" . $campaign_id . "','" . $source_id . "' " . $qry_fields['import_fields'] . " from importcsv";
            //$this->firephp->log($insert_query);
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
            //$this->firephp->log($insert_query);
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
            $fix_contact_names = "update contacts set fullname = concat(title,' ',firstname,' ',lastname)";
            //$this->firephp->log($insert_query);
            $this->db->query($insert_query);
			$this->db->query($update_import_table);
			$this->db->query($insert_contact_ids);
			$this->db->query($fix_contact_names);
        }
        echo json_encode(array(
            "success" => true
        ));
    }
    
    //create contact telephones
    public function create_contact_telephones()
    {
        $number_descriptions = $this->Import_model->get_telephone_numbers("contact");
		if(!empty($number_descriptions)){
        foreach ($number_descriptions as $description) {
            $insert_query = "insert into contact_telephone (telephone_id,contact_id,description,telephone_number) select '',contact_id,'$description',contact_tel_" . $description . " from importcsv ";
            //$this->firephp->log($insert_query);
            $this->db->query($insert_query);
        }
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
            //$this->firephp->log($insert_query);
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
            			
            //$this->firephp->log($insert_query);
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
            //$this->firephp->log($insert_query);
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
            //$this->firephp->log($insert_query);
            $this->db->query($insert_query);
        }
        echo json_encode(array(
            "success" => true
        ));
        
    }
    
 
  public function import_fields($echo = true)
    {
        $fields['records']           = array(
            "urn" => "urn",
            "records_nextcall" => "Next Call",
			"records_lastcall" => "Last call",
            "records_urgent" => "Urgent",
			"client_refs_client_ref"=> "Client Reference"
        );
        $fields['contacts']          = array(
            "contact_fullname" => "Full name",
            "contact_title" => "Title",
            "contact_firstname" => "Firstname",
            "contact_lastname" => "Lastname",
			"contact_gender" => "Gender",
			"contact_dob" => "Date of birth",
            "contact_position" => "Position/Job",
			"contact_email" => "Email",
			"contact_website" => "Website",
			"contact_facebook" => "Facebook",
			"contact_linkedin" => "Linkedin"
        );
        $fields['contact_telephone'] = array(
            "contact_tel_Telephone" => "Contact Telephone",
            "contact_tel_Landline" => "Contact Landline",
			"contact_tel_Home" => "Contact Home",
            "contact_tel_Mobile" => "Contact Mobile",
            "contact_tel_Work" => "Contact Work",
            "contact_tel_Fax" => "Contact Fax"
        );
        if ($this->input->post('type') == "B2B") {
            $fields['companies']         = array(
                "company_name" => "Company Name",
                "company_description" => "Description",
				"company_website" => "Website",
                "company_company_number" => "Company House Number",
                "company_turnover" => "Turnover",
                "company_employees" => "employees"
            );
            $fields['company_addresses'] = array(
                "company_add1" => "Address 1",
                "company_add2" => "Address 2",
                "company_add3" => "Address 3",
                "company_county" => "County",
                "company_postcode" => "Postcode"
            );
            $fields['company_telephone'] = array(
                "company_tel_Telephone" => "Company Telephone"
            );
        } else {
            $fields['contact_addresses'] = array(
                "contact_add1" => "Address 1",
                "contact_add2" => "Address 2",
                "contact_add3" => "Address 3",
                "contact_county" => "County",
                "contact_postcode" => "Postcode"
            );
        }
        $custom = $this->Import_model->get_custom_fields($this->input->post('campaign'));
        foreach ($custom as $k => $v) {
            $fields['record_details'][$k] = $v;
        }
		$selected = $this->Import_model->get_selected_fields();
        if ($echo) {
            echo json_encode(array("fields"=>$fields,"selected"=>$selected));
        } else {
            return $fields;
        }
    }
      public function get_sample()
    {
   		$sample = $this->Import_model->get_sample();
		if(count($sample)>0){
        echo json_encode(array("success"=>true,"sample"=>$sample));
		}
    }
    
	public function update_headers(){
		$type = $this->input->post('type');
		$import_fields = $this->Import_model->get_import_fields();
		$form_fields =  $this->input->post('field');
		$dupe_check = array();
		
		foreach($form_fields as $field){
			if(!empty($field)){
			if(!in_array($field,$dupe_check)){
		$dupe_check[] = 	$field;
			} else {
			echo json_encode(array("success"=>false,"msg"=>"Cannot assign ".$field." to more than 1 column"));
			exit;
			}
			}
		}
		foreach($import_fields as $key => $import_field){
			if(!empty($form_fields[$key])){
			if($form_fields[$key]<>$import_field&&!empty($form_fields[$key])){
				$this->db->query("ALTER TABLE `importcsv` CHANGE `$import_field` `".$form_fields[$key]."` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL");
			}
			}
		}
		
		echo json_encode(array("success"=>true));
	}
	
	public function check_import(){
		$type = $this->input->post('type');
		$fields= $this->input->post('field');
		$error="";
		if($type=="B2B"){
			if(!in_array("company_name",$fields)){
			$error ="B2B Campaigns require a company name";	
			}
			$tel=false;
			foreach($fields as $field){
			if(strpos($field,"contact_tel")!==false||strpos($field,"company_tel")!==false){
			$tel = true;
			}
			}
			if(!$tel){
			$error ="B2B campaigns require a telephone number";		
			}
		}
		if($type=="B2C"){
			$tel=false;
			foreach($fields as $field){
			if(strpos($field,"contact_tel")!==false){
			$tel = true;
			}
			}
			if(!$tel){
			$error ="B2C campaigns require a contact telephone number";		
			}
		}
		if(!empty($error)){
		echo json_encode(array("success"=>false,"msg"=>$error));	
		} else {
		echo json_encode(array("success"=>true));
		}
		
	}
	
	
}