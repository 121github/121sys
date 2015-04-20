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
        $qry     = "select *,c.description as codescription,ct.description as ctdescription, date_format(c.date_of_creation,'%d/%m/%Y') date_of_creation from companies c left join company_addresses ca using(company_id) left join company_telephone ct using(company_id) where company_id = '$id'";
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
				"conumber" => $result['conumber'],
                "status" => $result['status'],
                "date_of_creation" => $result['date_of_creation']
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
                    "add1" => !empty($result['add1'])?$result['add1']:'',
                    "add2" => $result['add2'],
                    "add3" => $result['add3'],
                    "county" => $result['county'],
                    "country" => $result['country'],
                    "postcode" => !empty($result['postcode'])?$result['postcode']:'',
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
        
        $qry     = "select com.urn,com.company_id,com.name coname,com.description ,com.conumber,com.description codescription,sector_name,employees,subsector_name,a.primary cois_primary,com.website cowebsite,ct.telephone_id cotelephone_id, ct.description cotel_name,ct.telephone_number cotelephone_number,ctps,address_id coaddress_id, add1 coadd1,add2 coadd2,add3 coadd3,county cocounty,country cocountry,postcode copostcode,lat latitude,lng longitude from companies com left join company_telephone ct using(company_id) left join company_addresses a using(company_id) left join locations using(location_id) left join company_subsectors using(company_id) left join subsectors using(subsector_id) left join sectors using(sector_id) where urn = '$urn' order by com.company_id";
        $results = $this->db->query($qry)->result_array();
        //put the contact details into array
        // $this->firephp->log($qry);
        foreach ($results as $result):
           			 $companies[$result['company_id']]['visible'] = array(
                "Company" => $result['coname'],
                "Sector" => $result['sector_name'],
                "Subsector" => $result['subsector_name'],
                "Description" => $result['codescription'],
                "Website" => $result['cowebsite'],
				"Employees" => $result['employees'],
				"Company #" => $result['conumber'],
            );

			$companies[$result['company_id']]['telephone'][$result['cotelephone_id']] = array(
                "tel_name" => $result['cotel_name'],
                "tel_num" => $result['cotelephone_number'],
                "tel_tps" => $result['ctps']
            );
			
			 //we only want to display the primary address for the company
            if ($result['cois_primary'] == "1") {
                 $companies[$result['company_id']]['visible']['Address']['add1']     = $result['coadd1'];
                 $companies[$result['company_id']]['visible']['Address']['add2']     = $result['coadd2'];
                 $companies[$result['company_id']]['visible']['Address']['add3']     = $result['coadd3'];
                 $companies[$result['company_id']]['visible']['Address']['county']   = $result['cocounty'];
                 $companies[$result['company_id']]['visible']['Address']['country']  = $result['cocountry'];
                 $companies[$result['company_id']]['visible']['Address']['postcode'] = $result['copostcode'];
               array_filter($companies[$result['company_id']]['visible']['Address']);
            }
        endforeach;
        return $companies;
    }
	
    public function get_companies_by_urn_list($urn_list) {
    	$qry = "select *
				from companies cm
				left join company_addresses cma ON (cma.company_id = cm.company_id)
				left join company_subsectors cms ON (cms.company_id = cm.company_id)
				left join company_telephone cmt ON (cmt.company_id = cm.company_id)
				where urn IN ".$urn_list;
    	 
    	return $this->db->query($qry)->result_array();
    }
    
	public function get_numbers($urn){
		$qry = "select telephone_number from company_telephone left join companies using(company_id) where urn = '$urn'";	
		return $this->db->query($qry)->result_array();	
	}
	
	public function get_numbers_from_urn_list($urn_list){
		$qry = "select telephone_number from company_telephone left join companies using(company_id) where urn IN $urn_list";
		return $this->db->query($qry)->result_array();
	}
		
	public function save_company ($form) {
		$this->db->insert("companies", $form);
	
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
	
		return $insert_id;
	}

    public function save_company_address ($form) {
        $this->db->insert("company_addresses", $form);

        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $insert_id;
    }

    public function update_company ($form) {

        $this->db->where("company_id", $form['company_id']);
        $result = $this->db->update("companies", $form);

        $this->db->trans_complete();

        return $result;
    }
	
}