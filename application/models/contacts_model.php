<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contacts_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
        if ($_SESSION['config']['use_fullname'] == 1) {
            $this->name_field = "fullname";
        } else {
            $this->name_field = "concat(title,' ',firstname,' ',lastname)";
        }
    }
    
    public function get_contact($id)
    {
        $qry     = "select * from contacts left join contact_addresses using(contact_id) left join contact_telephone using(contact_id) where contact_id = '$id'";
        //$this->firephp->log($qry);
        $results = $this->db->query($qry)->result_array();
        foreach ($results as $result):
            $contact['general'] = array(
                "contact_id" => $result['contact_id'],
                "urn" => $result['urn'],
                "fullname" => $result['fullname'],
                "position" => $result['position'],
                "dob" => $result['dob'],
                "website" => $result['website'],
                "linkedin" => $result['linkedin'],
                "facebook" => $result['facebook'],
                "notes" => $result['notes'],
                "email" => $result['email'],
                "optout" => $result['email_optout']
            );
            if ($result['telephone_id']) {
                $contact['telephone'][$result['telephone_id']] = array(
                    "tel_name" => $result['description'],
                    "tel_num" => $result['telephone_number'],
                    "tel_tps" => $result['tps'],
                    "tel_id" => $result['telephone_id']
                );
            }
            if ($result['address_id']) {
                $contact['address'][$result['address_id']] = array(
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
        
        return $contact;
    }
    
    public function get_contact_list($urn)
    {
        $this->db->select("contact_id,fullname");
        $this->db->where("urn", $urn);
        return $results = $this->db->get("contacts")->result_array();
    }
    
    
    public function get_contacts($urn)
    {
        
        $qry     = "select c.urn,c.contact_id,fullname,a.primary is_primary,c.email,c.linkedin,c.position,date_format(dob,'%d/%m/%Y') dob, c.notes,email_optout,website,ct.telephone_id, ct.description as tel_name,ct.telephone_number,ct.tps,address_id, add1,add2,add3,county,country,postcode,lat latitude,lng longitude from contacts c left join contact_telephone ct using(contact_id) left join contact_addresses a using(contact_id) left join locations using(location_id) where urn = '$urn' order by c.sort,c.contact_id";
        $results = $this->db->query($qry)->result_array();
        //put the contact details into array
        // $this->firephp->log($qry);
        foreach ($results as $result):
           // $use_fullname                            = ($this->name_field == "fullname" ? true : false);
			 $use_fullname  = true;
            $contacts[$result['contact_id']]['name'] = array(
                "fullname" => $result['fullname'],
                "use_full" => $use_fullname
            );
            $contacts[$result['contact_id']]['visible'] = array(
                "Job" => $result['position'],
                "DOB" => $result['dob'],
                "Email address" => $result['email'],
                "Linkedin" => $result['linkedin'],
                "Email Optout" => $result['email_optout'],
                "Website" => $result['website']
            );
            $contacts[$result['contact_id']]['telephone'][$result['telephone_id']] = array(
                "tel_name" => $result['tel_name'],
                "tel_num" => $result['telephone_number'],
                "tel_tps" => $result['tps']
            );
        //we only want to display the primary address for each contact
            if ($result['is_primary'] == "1") {
                $contacts[$result['contact_id']]['visible']['Address']['add1']     = $result['add1'];
                $contacts[$result['contact_id']]['visible']['Address']['add2']     = $result['add2'];
                $contacts[$result['contact_id']]['visible']['Address']['add3']     = $result['add3'];
                $contacts[$result['contact_id']]['visible']['Address']['county']   = $result['county'];
                $contacts[$result['contact_id']]['visible']['Address']['country']  = $result['country'];
                $contacts[$result['contact_id']]['visible']['Address']['postcode'] = $result['postcode'];
                array_filter($contacts[$result['contact_id']]['visible']['Address']);
            }
        endforeach;
        return $contacts;
    }
	
	public function get_numbers($urn){
		$qry = "select replace(telephone_number,' ','') as telephone_number from contact_telephone left join contacts using(contact_id) where urn = '$urn'";	
		$result =  $this->db->query($qry)->result_array();	
		foreach($result as $row){
			$numbers[] = $row['telephone_number'];	
		}
		$qry = "select replace(telephone_number,' ','') as telephone_number from company_telephone left join companies using(company_id) where urn = '$urn'";	
		$result =  $this->db->query($qry)->result_array();	
		foreach($result as $row){
			$numbers[] = $row['telephone_number'];	
		}
		return $numbers;
	}
	
	public function get_numbers_from_urn_list($urn_list){
		$qry = "select replace(telephone_number,' ','') as telephone_number from contact_telephone left join contacts using(contact_id) where urn IN $urn_list";
		$result =  $this->db->query($qry)->result_array();
		foreach($result as $row){
		$numbers[] = $row['telephone_number'];
		}
		$qry = "select replace(telephone_number,' ','') as telephone_number from company_telephone left join companies using(company_id) where urn IN $urn_list";
		$result =  $this->db->query($qry)->result_array();
		foreach($result as $row){
		$numbers[] = $row['telephone_number'];
		}
		return $numbers;
	}
	

	public function save_contact ($form) {
		$this->db->insert("contacts", $form);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
	
		return $insert_id;
	}
	
}