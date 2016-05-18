<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Import_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
	
	/* use the lines below to manually undo an import
	delete from record_details where urn in (select urn from importcsv);
delete from contacts where urn in (select urn from importcsv);
delete from companies where urn in (select urn from importcsv);
delete from client_refs where urn in (select urn from importcsv);
delete from company_telephone where company_id not in (select company_id from companies);
delete from contact_telephone where contact_id not in (select contact_id from contacts);
delete from company_addresses where company_id not in (select company_id from companies);
delete from contact_addresses where contact_id not in (select contact_id from contacts);
delete from records where urn in (select urn from importcsv)
*/

	public function undo_changes(){
		$this->db->query("delete from record_details where urn in (select urn from importcsv)");
		$this->db->query("delete from contacts where urn in (select urn from importcsv)");
		$this->db->query("delete from companies where urn in (select urn from importcsv)");
		$this->db->query("delete from client_refs where urn in (select urn from importcsv)");
		$this->db->query("delete from company_telephone where company_id not in (select company_id from companies)");
		$this->db->query("delete from contact_telephone where contact_id not in (select contact_id from contacts)");
		$this->db->query("delete from company_addresses where company_id not in (select company_id from companies)");
		$this->db->query("delete from contact_addresses where contact_id not in (select contact_id from contacts)");
		$this->db->query("delete from records where urn in (select urn from importcsv)");
		return true;
	}
	
	public function seperate_fullname(){
		$query ="select contact_id,fullname from contacts join records using(urn) where (fullname is not null and fullname <> '') and (lastname is null or lastname = '') and (firstname is null or firstname = '')";
		if(intval($this->uri->segment(3))){
		$query .= " and campaign_id = ".$this->uri->segment(3);
		}
		
		$names = $this->db->query($query)->result_array();
		foreach($names as $row){
		$name = explode(" ",$row['fullname']);
		foreach($name as $k=>$part){
		$name[$k] = ucfirst($part);	
		}
		$titles = array("Mr","Mrs","Miss","Ms","Sir","Dr");
		$title = NULL;
		$firstname = NULL;	
		$lastname = NULL;
		$data = array();
		if(in_array($name[0],$titles)){
			$data['title'] = $name[0];
			unset($name[0]);
			reset($name);
		}
		if(isset($name[0])&&!empty($name[0])){
		$data['firstname'] = 	$name[0];
		} 
		if(isset($name[1])&&!empty($name[1])){
		$data['lastname'] = 	$name[1];
		if(isset($name[2])&&!empty($name[2])){
		$data['lastname'] .=  " ".$name[2];
		}
		} 
		if(!empty($data['firstname'])||!empty($data['lastname'])){
			$this->db->where("contact_id",$row['contact_id']);
			$this->db->update("contacts",$data);
			echo $this->db->last_query(); echo "<br>";
		}
		}
		
	}
	
	
		public function get_selected_fields(){
	$fields = array();
	$result = $this->db->query("SHOW COLUMNS FROM `importcsv`")->result_array();	
	foreach($result as $row){
	$fields[] = $row['Field'];	
	}
	return $fields;
	}
	
			public function get_sample(){
	$result = array();
	$result = $this->db->query("select * FROM `importcsv` limit 6")->result_array();
	return $result;
	}
	
	public function drop_importcsv(){
	$this->db->query("drop table importcsv");	
	}
	
	public function check_import(){
	$query = $this->db->query("select * from importcsv");
	if($query->num_rows()>0){
	return true;	
	}
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
;			$prefix .= "_";
		//check which of the record fields are in the import table
		foreach($import_fields_result as $row){
		$import_field = $prefix.$row['Field'];
		if(in_array($import_field,$table_fields)){
				$qry_fields .= ",".preg_replace("/$prefix/",'',$import_field);
				$import_fields .= ",".preg_replace("/$prefix/",'',$import_field,1);
		}
		
				if(in_array($row['Field'],$table_fields)){
				$qry_fields .= ",".preg_replace("/$prefix/",'',$import_field);
				$import_fields .= ",".preg_replace("/$prefix/",'',$import_field,1);
		}
		}
		if(!empty($qry_fields)){
		return array("table_fields"=>$qry_fields,"import_fields"=>$import_fields);
		}
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
		$import_fields_result = $this->db->query("SHOW COLUMNS FROM `importcsv` where `Field` like '".$prefix."_add1' or `Field` like '".$prefix."_add2' or `Field` like '".$prefix."_add3' or `Field` = '".$prefix."_county' or `Field` = '".$prefix."_country' or `Field` = '".$prefix."_postcode'")->result_array();
		if(count($import_fields_result)>0){
		foreach($import_fields_result as $row){
			$qry_fields .= ",".str_replace($prefix.'_','',$row['Field']);
			$import_fields .= ",".$row['Field'];
		}
		}
		return array("table_fields"=>$qry_fields,"import_fields"=>$import_fields);
		
	}
	
	    public function get_custom_fields($campaign)
    {
        $this->db->where("campaign_id", $campaign);
        $result = $this->db->get("record_details_fields")->result_array();
        $array  = array();
        foreach ($result as $row) {
            $array[$row['field']] = $row['field_name'];
        }
        return $array;
    }
	
	public function get_import_fields(){
		$fields=array();
	$results =  $this->db->query("SHOW COLUMNS FROM `importcsv`")->result_array();	
	foreach($results as $row){
	$fields[] = $row['Field'];	
	}
	return $fields;
	}
}