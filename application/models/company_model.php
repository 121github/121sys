<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Company_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();

    }
    
		public function add_address($data){
	$this->db->insert("company_addresses",$data);	
	}
	    public function add_telephone($data){
	$this->db->insert("company_telephone",$data);	
	}
	public function update_subsectors($subsectors,$company){
		$this->db->where('company_id',$company);	
		$this->db->delete('company_subsectors');
		
		foreach($subsectors as $subsector){
			$this->db->insert("company_subsectors",array("company_id"=>$company,"subsector_id"=>$subsector));	
		}
	}
	
    public function get_company($id=false,$urn=false)
    {
        $qry     = "select *,IF(c.employees IS NOT NULL,c.employees,'') employees, IF(c.turnover IS NOT NULL,c.turnover,'') turnover, c.description as codescription,ct.description as ctdescription, IF(c.date_of_creation,date_format(c.date_of_creation,'%d/%m/%Y'),'') date_of_creation, ca.description as add_description from companies c left join company_addresses ca using(company_id) left join company_telephone ct using(company_id) left join locations using(location_id) where 1 ";
		if($id){
			 $qry     .= " and company_id = '$id' ";
		}
		if($urn){
			 $qry     .= " and urn = '$urn' ";
		}
		//only 1 company per urn at the moment
 		$qry     .= " limit 1 ";
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
                    "description" => !empty($result['add_description'])?$result['add_description']:'',
                    "add1" => !empty($result['add1'])?$result['add1']:'',
                    "add2" => $result['add2'],
                    "add3" => $result['add3'],
                    "city" => $result['city'],
                    "county" => $result['county'],
                    "country" => $result['country'],
                    "postcode" => !empty($result['postcode'])?$result['postcode']:'',
                    "primary" => $result['primary'],
                    "visible" => $result['visible'],
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
        $companies = array();

        $qry = "select telephone_prefix,com.email coemail, telephone_protocol,com.urn,com.company_id,com.name coname,com.description ,com.conumber,com.description codescription,sector_name,IF(employees IS NOT NULL,employees,'') employees,subsector_name,a.primary cois_primary,a.visible cois_visible, com.website cowebsite,ct.telephone_id cotelephone_id, ct.description cotel_name,ct.telephone_number cotelephone_number,ctps,address_id coaddress_id, add1 coadd1,add2 coadd2,add3 coadd3,city cocity,county cocounty,country cocountry,postcode copostcode, a.description as add_description, lat latitude,lng longitude from companies com left join company_telephone ct using(company_id) left join company_addresses a using(company_id) left join locations using(location_id) left join company_subsectors using(company_id) left join subsectors using(subsector_id) left join sectors using(sector_id) join records using(urn) join campaigns using(campaign_id) where urn = '$urn' order by com.company_id";
        $results = $this->db->query($qry)->result_array();

        //put the contact details into array
        foreach ($results as $result):
            if (!isset($companies[$result['company_id']]['links'])) {
                $companies[$result['company_id']]['links'] = array(
                    "Website" => $result['cowebsite']
                );
            }

            if (!isset($companies[$result['company_id']]['visible'])) {
                $companies[$result['company_id']]['visible'] = array(
                    "Company" => $result['coname'],
                    "Sector" => $result['sector_name'],
                    "Subsector" => $result['subsector_name'],
                    "Description" => $result['codescription'],
                    "Employees" => $result['employees'],
                    "Company #" => $result['conumber'],
                    "Email address" => $result['coemail']
                );
            }

			$companies[$result['company_id']]['telephone'][$result['cotelephone_id']] = array(
                "tel_name" => $result['cotel_name'],
                "tel_num" => $result['cotelephone_number'],
                "tel_tps" => $result['ctps'],
				"tel_prefix" => $result['telephone_prefix'],
				"tel_protocol" => $result['telephone_protocol']
            );
			
			 //we want to display the primary and/or visible address for the company
            if (($result['cois_primary'] == "1") || ($result['cois_visible'] == "1")) {
                 $companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]['description']     = $result['add_description'];
                 $companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]['add1']     = $result['coadd1'];
                 $companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]['add2']     = $result['coadd2'];
                 $companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]['add3']     = $result['coadd3'];
                 $companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]['city'] = $result['cocity'];
                 $companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]['county']   = $result['cocounty'];
                 $companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]['country']  = $result['cocountry'];
                 $companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]['postcode'] = $result['copostcode'];
                 $companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]['primary'] = $result['cois_primary'];
                 $companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]['visible'] = $result['cois_visible'];
               array_filter($companies[$result['company_id']]['visible']['Address'][$result['coaddress_id']]);
            }
        endforeach;
        return $companies;
    }
	
    public function get_companies_by_urn_list($urn_list) {
    	$qry = "select *
				from companies cm
				left join company_addresses cma using(company_id)
				left join company_subsectors cms using(company_id)
				left join company_telephone cmt using(company_id)
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
        $this->db->insert_update("company_addresses", $form);

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