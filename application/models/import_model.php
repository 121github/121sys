<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Import_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
	
	public function drop_importcsv(){
	$this->db->query("drop table importcsv");	
	}
	
 public function get_fields($table_name,$prefix='')
    {
		if(empty($prefix)){
			$prefix = $table_name;
		}
		$import_fields_result = $this->db->query("SHOW COLUMNS FROM `importcsv`")->result_array();		
		$table_fields = array();
		$qry_fields = "";
		$import_fields = "";
		//get the record fields
		$table_fields_result = $this->db->query("SHOW COLUMNS FROM `$table_name`")->result_array();
		foreach($table_fields_result as $row){
		$table_fields[] = 	$prefix."_".$row['Field'];
		}
;
		//check which of the record fields are in the import table
		foreach($import_fields_result as $row){
		$import_field = $prefix."_".$row['Field'];
		if(in_array($import_field,$table_fields)){
			$this->firephp->log($import_field);
		$this->firephp->log($table_fields);
				$qry_fields .= ",".str_replace($prefix.'_','',$import_field);
				$import_fields .= ",".str_replace($prefix.'_','',$import_field);
		}
		}
		return array("table_fields"=>$qry_fields,"import_fields"=>$import_fields);
		
	}
	
	
	 public function get_telephone_numbers($prefix)
    {
		$telephone_numbers = array();
		$import_fields_result = $this->db->query("SHOW COLUMNS FROM `importcsv` where `Field` like '".$prefix."_tel_%'")->result_array();
		if(count($import_fields_result)>0){
		foreach($import_fields_result as $row){
			$explode = explode("_",$row['Field']);
			$description = $explode[2];
		$telephone_numbers[]=trim(ucfirst(strtolower($description)));
		}
		}
		return $telephone_numbers;
		
	}
	
	
	public function get_addresses($prefix)
    {
		$import_fields = "";
		$qry_fields = "";
		$import_fields_result = $this->db->query("SHOW COLUMNS FROM `importcsv` where `Field` like '".$prefix."_add%' or `Field` = '".$prefix."_county' or `Field` = '".$prefix."_country' or `Field` = '".$prefix."_postcode'")->result_array();
		if(count($import_fields_result)>0){
		foreach($import_fields_result as $row){
			$qry_fields .= ",".str_replace($prefix.'_','',$row['Field']);
			$import_fields .= ",".$row['Field'];
		}
		}
		return array("table_fields"=>$qry_fields,"import_fields"=>$import_fields);
		
	}
	
	
}