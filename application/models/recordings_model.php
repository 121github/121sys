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
	$qry = "select replace(telephone_number,' ','') as telephone_number,description from contact_telephone left join contacts using(contact_id) where urn = '$urn'";	
	$result =  $this->db->query($qry)->result_array();	
	foreach($result as $row){
	$numbers[] = array("description"=>$row['description'],"number"=>$row['telephone_number']);	
	}
	$qry = "select replace(telephone_number,' ','') as telephone_number,description from company_telephone left join companies using(company_id) where urn = '$urn'";	
	$result =  $this->db->query($qry)->result_array();	
		foreach($result as $row){
	$numbers[] = array("description"=>$row['description'],"number"=>$row['telephone_number']);
	}
	return $numbers;
	}
	
}
