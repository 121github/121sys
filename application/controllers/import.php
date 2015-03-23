<?php
require('upload.php');

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
if($this->Import_model->undo_changes()){
echo json_encode(array("success"=>true));	
}
}
	
	public function add_source(){
	$source = $this->input->post('source');
	$this->db->insert('data_sources',array("source_name"=>$source));
	if($this->db->_error_message()){	
	 echo json_encode(array(
            "success"=>false
        ));
		exit;
	}
	$id = $this->db->insert_id();
	 echo json_encode(array(
            "success"=>true,"data" => $id
        ));
	}
	  //this loads the data management view
    public function index()
    {
        $campaigns = $this->Form_model->get_campaigns();
        $sources   = $this->Form_model->get_sources();
        $data      = array(
            'campaign_access' => $this->_campaigns,
            'title' => 'Import Data',
            'page' => 'import_data',
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
	
	
    public function import_file()
    {
        $options               = array();
        $options['upload_dir'] = dirname($_SERVER['SCRIPT_FILENAME']) . '/datafiles/';
        $options['upload_url'] = base_url() . '/datafiles/';
        $upload_handler        = new Upload($options, true);
    }
	
	public function checkfile($file=NULL){
	$row = 1;
	if(!file_exists(FCPATH."datafiles/".$file)){
	echo "File not uploaded (".FCPATH."datafiles/".$file>")";
	exit;
	}
if (($handle = fopen(FCPATH."datafiles/".$file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		if($row==1){
		$length = count($data);
		$headers=array();
		//check the headers	
		foreach($data as $k=>$column_name){
		//duplicate columns not allowed
		if(in_array($column_name,$headers)){
		return "Duplicate header names are not allowed";
		exit;
		}
		//empty columns not allowed
		if(empty($column_name)){
		return "Empty column headers are not allowed, check for trailing columns";
		exit;
		}
		//dodgy characters not allowed
		if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',$column_name)){
		return "Please check column headers for invalid characters";
		exit;
		}
		$headers[] = $column_name;
		}
		}
		//detect mismatched header/value counts
		if(count($data)!==$length){
		return "Row $row has a different number of columns!";
		exit;	
		}
        $row++;
    }
    fclose($handle);
	return false;
}
	
		
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
		
		//run the check function to make sure all headers are valid
		$file_error = $this->checkfile($csv_file);
		$this->firephp->log($file_error);
		if(@!empty($file_error)){
		@unlink(FCPATH."datafiles\\".$csv_file);
		echo json_encode(array(
			"success"=>false,
            "error" => $file_error
        ));
		exit;
		}
		
		//run the bash script
		$command ='bash importcsv.sh "datafiles/' . $csv_file . '" ' . $table . ' ' . $database;
        $output = shell_exec($command);
		$this->firephp->log($output);
		if($this->Import_model->check_import()){
        //if csv imports successfully
        echo json_encode(array(
            "success" => true
        ));
		exit;
		}
		else {
		@unlink(FCPATH."datafiles\\".$csv_file);
		 echo json_encode(array(
		 	"success"=>false,
            "output" => $output	
        ));	
		exit;
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
		$db_name = $this->db->database;
        //if the csv has no urn column we get it from the records table auto increment
            $urn = $this->db->query("SELECT `AUTO_INCREMENT` urn
FROM  INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = '$db_name'
AND   TABLE_NAME   = 'records'")->row()->urn;
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
    public function create_client_refs()
    {
		//first check if the client_ref field was add
		$result = $this->db->query("SHOW COLUMNS FROM `importcsv` LIKE 'client_refs_client_ref'");
		$exists = $result->num_rows()?TRUE:FALSE;	
		//if there is a client ref column then import them	
		if($exists) {
  		 $qry_fields  = $this->Import_model->get_fields("client_refs");
		 if(!empty($qry_fields)){
		 $insert_query = "insert into client_refs (client_ref,urn) select " .ltrim($qry_fields['import_fields'],",") . " from importcsv";
		 $this->db->query($insert_query);
		 }
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
			$fix_details = "delete from record_details where c1 is null and c2 is null and c3 is null and c4 is null and c5 is null and d1 is null and d2 is null and d3 is null and dt1 is null and dt2 is null and n1 is null and n2 is null and n3 is null";
            //$this->firephp->log($insert_query);
            $this->db->query($insert_query);
			//clean up any empty rows in record details
			$this->db->query($fix_details);
			//this query sets all lapsed renewal dates to the subsequent year
			$renewals = "update record_details rd left join records r using(urn) left join record_details_fields rdf on r.campaign_id = rdf.campaign_id set d1 = concat(
year(curdate())+1,'-',month(d1),'-',day(d1)
) where is_renewal = 1 and d1 <= curdate()";
$this->db->query($renewals);
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
			$fix_contact_names = "update contacts set fullname = trim(concat(if(title is null,'',title),' ',if(firstname is null,'',firstname),' ',if(lastname is null,'',lastname)))  where fullname is null";
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
				$fix_telephone_numbers = "delete from contact_telephone where trim(telephone_number) = '' or telephone_number is null or char_length(telephone_number) < '4'";
            //$this->firephp->log($insert_query);
            $this->db->query($insert_query);
			$this->db->query($fix_telephone_numbers);
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
			$fix_addresses = "delete from contact_addresses where add1 is null and add2 is null and add3 is null and county is null and postcode is null";
            //$this->firephp->log($insert_query);
            $this->db->query($insert_query);
			 $this->db->query($fix_addresses);
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
            $fix_companies = "update `companies` set website =  null WHERE trim(website) < '' or website = ''";
						
            //$this->firephp->log($insert_query);
            $this->db->query($insert_query);
			$this->db->query($update_import_table);
			$this->db->query($insert_company_ids);
			$this->db->query($fix_companies);
        }
        echo json_encode(array(
            "success" => true
        ));
        
    }
    
	//create and update company sectors/subsectors
		 public function create_sectors()
    {
		$qry = "select distinct subsector_name,sector_name from importcsv";
		$result =  $this->db->query($qry)->result_array();
		foreach($result as $row){
			$insert = "insert ignore into sectors set sector_name = '{$row['sector_name']}'";
            $this->db->query($insert);
			if($this->db->insert_id()>0){
			$id = $this->db->insert_id();
			} else {
			$select = 	"select sector_id from sectors where sector_name = '{$row['sector_name']}'";
			$id = $this->db->query($select)->row()->sector_id;
			}		
          	$insert = "insert ignore into subsectors set subsector_name = '{$row['subsector_name']}',sector_id = $id";
            $this->db->query($insert_query);
			//match back the subsector_ids with the import table data
			$update_import_table = "ALTER TABLE `importcsv` ADD `subsector_id` INT NULL ,ADD INDEX ( `subsector_id` )";
            $insert_sector_ids  = "update importcsv i left join subsectors s on i.subsector_name = s.subsector_name set i.subsector_id = s.subsector_id";
			
			$add_company_sectors = "insert ignore into company_subsectors select company_id,subsector_id from importcsv";
			$this->db->query($add_company_sectors);
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
            $insert_query = "insert into company_telephone (telephone_id,company_id,description,telephone_number) select '',company_id,'$description',company_tel_" . $description . " from importcsv";
			$fix_telephone_numbers = "delete from company_telephone where trim(telephone_number) = '' or telephone_number is null  or char_length(telephone_number) < '4'";
            //$this->firephp->log($insert_query);
            $this->db->query($insert_query);
			 $this->db->query($fix_telephone_numbers);
			
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
			$fix_addresses = "delete from company_addresses where add1 is null and add2 is null and add3 is null and county is null and postcode is null";
			
            //$this->firephp->log($insert_query);
            $this->db->query($insert_query);
			$this->db->query($fix_addresses);
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
            "contact_tel_Fax" => "Contact Fax",
			"contact_tel_Transfer" => "Contact Transfer"
        );
        if ($this->input->post('type') == "B2B") {
            $fields['companies']         = array(
                "company_name" => "Company Name",
                "company_description" => "Description",
				"company_website" => "Website",
                "company_conumber" => "Company House Number",
                "company_turnover" => "Turnover",
                "company_employees" => "Employees",
				"sector_name" => "Sector",
				"subsector_name" => "Subsector"
            );
            $fields['company_addresses'] = array(
                "company_add1" => "Address 1",
                "company_add2" => "Address 2",
                "company_add3" => "Address 3",
                "company_county" => "County",
                "company_postcode" => "Postcode"
            );
            $fields['company_telephone'] = array(
            "company_tel_Telephone" => "Company Telephone",
            "company_tel_Headquarters" => "Company Headquarters",
			"company_tel_Reception" => "Company Reception",
            "company_tel_Other" => "Company Other",
            "company_tel_Mobile" => "Company Mobile",
            "company_tel_Fax" => "Company Fax",
			"company_tel_Transfer" => "Company Transfer"
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
	//if we need to do anything AFTER the import has ran (eg: formatting/checking tables) we can add it to this function
	public function tidy_up(){
		$this->load->model('Cron_model');
		echo json_encode(array("success"=>true));
	}
	
	public function update_locations(){
		$this->Cron_model->update_locations_table();
		$this->Cron_model->update_location_ids();
		$this->Cron_model->update_locations_with_google();
	}
	
	//this function is used to merge contacts for when a record has multiple contacts
	public function merge_dupe_companies(){
		$campaign_id = $this->input->post('campaign');
	$qry = "select name,urn,concat(substring(companies.name, 1, 4 ),add1,postcode) as dupe from companies left join records using(urn) left join contacts using(urn) left join company_addresses using(company_id) where campaign_id = $campaign_id and add1 is not null and postcode is not null group by concat(substring(companies.name, 1, 4 ),add1,postcode) having count(concat(substring(companies.name, 1, 4 ),add1,postcode)) > 1";
	$array = $this->db->query($qry)->result_array();
	$dupes = array();
	foreach($array as $row){
		$dupes[$row['dupe']]=array("name"=>$row['name'],"urn"=>$row['urn']);
	}
	foreach($dupes as $ref => $array){
	$update = "update contacts left join companies using(urn) left join company_addresses using(company_id) set contacts.urn = {$array['urn']} where concat(substring(companies.name, 1, 4 ),add1,postcode) = '$ref'";
	//echo $update.";<br>";
	echo $array['name'] .": ". $ref.";<br>";
	//$this->db->query($update);
	$find_removed ="select urn from client_refs where concat(substring(companies.name, 1, 4 ),add1,postcode) = '$ref' and urn <> '{$array['urn']}'";
	foreach($this->db->query($find_removed)->result_array() as $row){
		//$this->db->query("delete from records where urn = '{$row['urn']}'");
		//$this->db->query("delete from companies where urn = '{$row['urn']}'");
		//$this->db->query("delete from records where urn = '{$row['urn']}'");
			}
	
	}
	
	}
	
	
	public function merge_by_client_ref(){
		$campaign_id = $this->input->post('campaign');
	//find all client_refs that appear more than once
	$dupe_query = "SELECT client_ref, urn
FROM client_refs left join records using(urn) where campaign_id = $campaign_id
GROUP BY client_ref
HAVING count( client_ref ) >1";	
	$result = $this->db->query($dupe_query)->result_array();
	$delete_array = array();
	$keep_array = array();
	$delete_list = "";
	$keep_list = "";
	foreach($result as $row){
	$list = "";
	$comlist = "";
	$dupe = $row['client_ref'];
	$urn = $row['urn'];
	$keep_array[$urn] = $urn;
	//now find all contacts with the client ref
	$qry = "select contact_id,urn from client_refs left join contacts using(urn) where client_ref = '$dupe' and urn in(select urn from records where campaign_id=$campaign_id)";
	$con_result = $this->db->query($qry)->result_array();
	foreach($con_result as $list_item){
	$delete_array[$list_item['urn']] = $list_item['urn'];
	$list .= $list_item['contact_id'].",";
	}
	$list = rtrim($list,",");
	echo "#".$dupe.";<br>";
	$update = "update contacts set contacts.urn = $urn where contact_id in('0',$list)";
	echo $update.";<br>";
	//now update all the companies
	$qry = "select company_id,urn from client_refs left join companies using(urn) where client_ref = '$dupe'";
	$com_result = $this->db->query($qry)->result_array();
	foreach($com_result as $list_item){
	$delete_array[$list_item['urn']] = $list_item['urn'];
	$comlist .= (!empty($list_item['company_id'])?$list_item['company_id'].",":"");
	}
	$comlist = rtrim($comlist,",");
	$update = "delete from companies where companies.urn <> $urn and company_id in('0',$comlist)";
	echo $update.";<br>";
	}
	
	foreach($keep_array as $ignore){
	unset($delete_array[$ignore]);	
	}
	
	$keep_list = implode(', ', $keep_array);
	$delete_list = implode(', ', $delete_array);
	//tidy up the records table removing all the redunant records that have been merged
	$tidy2 = "delete from client_refs where urn in($delete_list)";
	$tidy = "delete from records where urn in($delete_list)";
	echo $tidy.";<br>";
	echo $tidy2.";<br>";	
	}
	
	public function match_outcomes(){
$nbf['hostname'] = '121system.com';
$nbf['username'] = 'bradf';
$nbf['password'] = 'brad123';
$nbf['database'] = 'newbusiness';
$nbf['dbdriver'] = 'mysqli';
$nbf['dbprefix'] = '';
$nbf['pconnect'] = FALSE;
$nbf['db_debug'] = TRUE;
$nbf['cache_on'] = FALSE;
$nbf['cachedir'] = '';
//$db['121backup']['char_set'] = 'UTF-8';
$nbf['dbcollat'] = 'latin1_swedish_ci';
$nbf['swap_pre'] = '';
$nbf['autoinit'] = TRUE;
$nbf['stricton'] = FALSE;
		
		if(!$this->input->post()){
		echo "<form method='post'>";
	$outcome_list = "select * from outcomes";
	$outcomes = $this->db->query($outcome_list)->result_array();
	echo "121sys Outcomes<br>";
	foreach($outcomes as $outcomes){
	echo $outcomes['outcome_id']."=>".$outcomes['outcome']."<br>";
	}
	echo "<hr>";
	$db2 = $this->load->database($nbf,true);
	$outcome_list = "select * from tbl_outcomes";
	$outcomes = $db2->query($outcome_list)->result_array();
	echo "NBF Outcomes<br>";
	foreach($outcomes as $outcomes){
	echo $outcomes['outcome']." - <input name='outcome[{$outcomes['outcome_id']}]' value='' /><br>";
	}
	echo "<input type='submit' value='go?' />";
	echo "</form>";
		} else {
		foreach($this->input->post('outcome') as $nbf_id=>$newid){
			echo "update records set outcome_id = $newid where outcome_id = $nbf_id and campaign_id = 3";
			echo ";<br>";	
		}
		}
	}
	
		public function update_history_urns(){
	$tidy = "select client_ref as nbf_urn, c.urn as newurn from importcsv i left join client_refs c on i.urn = c.client_ref";
	echo $tidy.";<br>";
	$result = $this->db->query($tidy)->result_assoc();	
	}
	
	public function update_history(){
	$tidy = "update history left join client_refs on history.urn = client_refs.client_ref set history.urn = client_ref.urn";
	echo $tidy.";<br>";
	//$this->db->query($tidy);		
	}
	
	public function clean_orphan_records(){	
	//$this->db->query("delete from records where urn = '{$row['urn']}'");		
	}

}
