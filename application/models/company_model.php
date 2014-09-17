<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Company_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();

    }
    
    public function get_company($id)
    {
        $qry     = "select *,c.description as codescription,ct.description as ctdescription from companies c left join company_addresses ca using(company_id) left join company_telephone ct using(company_id) where company_id = '$id'";
        //$this->firephp->log($qry);
        $results = $this->db->query($qry)->result_array();
        foreach ($results as $result):
            $company['general'] = array(
                "company_id" => $result['company_id'],
                "urn" => $result['urn'],
                "name" => $result['name'],
                "website" => $result['website'],
                "email" => $result['email'],
                "description" => $result['codescription'],
				"employees" => $result['employees'],
				"turnover" => $result['turnover'],
				"company_number" => $result['company_number']
            );
            if ($result['telephone_id']) {
                $company['telephone'][$result['telephone_id']] = array(
                    "tel_name" => $result['ctdescription'],
                    "tel_num" => $result['telephone_number'],
                    "ctps" => $result['ctps'],
                    "tel_id" => $result['telephone_id']
                );
            }
            if ($result['address_id']) {
                $company['address'][$result['address_id']] = array(
                    "add1" => $result['add1'],
                    "add2" => $result['add2'],
                    "add3" => $result['add3'],
                    "county" => $result['county'],
                    "country" => $result['country'],
                    "postcode" => $result['postcode'],
                    "primary" => $result['primary'],
                    "address_id" => $result['address_id']
                );
            }
        endforeach;
        
        return $company;
    }
    
    public function get_company_list($urn)
    {
        $this->db->select("company_id,name");
        $this->db->where("urn", $urn);
        return $results = $this->db->get("companies")->result_array();
    }
    
    
    public function get_companies($urn)
    {
        
        $qry     = "select com.urn,com.company_id,com.name coname,a.primary cois_primary,com.website cowebsite,ct.telephone_id cotelephone_id, ct.description cotel_name,ct.telephone_number cotelephone_number,ctps,address_id coaddress_id, add1 coadd1,add2 coadd2,add3 coadd3,county cocounty,country cocountry,postcode copostcode,latitude,longitude from companies com left join company_telephone ct using(company_id) left join company_addresses a using(company_id)  where urn = '$urn' order by com.company_id";
        $results = $this->db->query($qry)->result_array();
        //put the contact details into array
        // $this->firephp->log($qry);
        foreach ($results as $result):
           			 $data['company'][$result['company_id']]['visible'] = array(
                "Company Name" => $result['coname'],
                "Sector" => $result['sector_name'],
                "Subsector" => $result['subsector_name'],
                "Description" => $result['codescription'],
                "Website" => $result['cowebsite'],
                "Employees" => $result['employees']
            );

			$data['company'][$result['company_id']]['telephone'][$result['cotelephone_id']] = array(
                "tel_name" => $result['cotel_name'],
                "tel_num" => $result['cotelephone_number'],
                "tel_tps" => $result['ctps']
            );
			
			 //we only want to display the primary address for the company
            if ($result['cois_primary'] == "1") {
                $data['company'][$result['company_id']]['visible']['Address']['add1']     = $result['coadd1'];
                $data['company'][$result['company_id']]['visible']['Address']['add2']     = $result['coadd2'];
                $data['company'][$result['company_id']]['visible']['Address']['add3']     = $result['coadd3'];
                $data['company'][$result['company_id']]['visible']['Address']['county']   = $result['cocounty'];
                $data['company'][$result['company_id']]['visible']['Address']['country']  = $result['cocountry'];
                $data['company'][$result['company_id']]['visible']['Address']['postcode'] = $result['copostcode'];
                array_filter($data['company'][$result['company_id']]['visible']['Address']);
            }
        endforeach;
        return $contacts;
    }
	
	public function get_numbers($urn){
	$qry = "select telephone_number from company_telephone left join companies using(company_id) where urn = '$urn'";	
	return $this->db->query($qry)->result_array();	
	}
	
	
}