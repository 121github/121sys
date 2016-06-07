<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contacts_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
        if (isset($_SESSION['config']['use_fullname']) && @$_SESSION['config']['use_fullname'] == 1) {
            $this->name_field = "fullname";
        } else {
            $this->name_field = "concat(title,' ',firstname,' ',lastname)";
        }
    }
    
    public function get_contact($id)
    {
        $qry     = "select *, IF (dob,date_format(dob,'%d/%m/%Y'),null) dob, contact_addresses.description as add_description from contacts left join contact_addresses using(contact_id) left join contact_telephone using(contact_id) where contact_id = '$id'";
        $results = $this->db->query($qry)->result_array();
        foreach ($results as $result):
            $contact['general'] = array(
                "contact_id" => $result['contact_id'],
                "urn" => $result['urn'],
                "title" => $result['title'],
                "firstname" => $result['firstname'],
                "lastname" => $result['lastname'],
                "fullname" => $result['fullname'],
                "position" => $result['position'],
                "website" => $result['website'],
                "linkedin" => $result['linkedin'],
                "facebook" => $result['facebook'],
                "notes" => $result['notes'],
                "email" => $result['email'],
                "optout" => $result['email_optout'],
                "dob" => $result['dob']
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
        
        return $contact;
    }
    
    public function get_contact_list($urn)
    {
        $this->db->select("contact_id,fullname");
        $this->db->where("urn", $urn);
        return $results = $this->db->get("contacts")->result_array();
    }
    
	public function add_address($data){
	$this->db->insert("contact_addresses",$data);	
	}

    public function save_contact_address ($form) {
        $this->db->insert_update("contact_addresses", $form);

        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $insert_id;
    }

    public function add_telephone($data){
	$this->db->insert("contact_telephone",$data);	
	}

    public function save_contact_telephone ($form) {
        $this->db->insert_update("contact_telephone", $form);

        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $insert_id;
    }

    public function get_contacts($urn)
    {

        $qry = "select
                      camp.telephone_prefix,
                      camp.telephone_protocol,
                      c.urn,
                      c.contact_id,
                      fullname,
                      c.email,
                      c.linkedin,
                      c.facebook,
                      c.position,
                      IF (dob,date_format(dob,'%d/%m/%Y'),'') dob,
                      c.notes,email_optout,website,
                      ct.telephone_id, ct.description as tel_name,
                      ct.telephone_number,
                      ct.tps,
                      address_id,a.description codescription,add1,add2,add3,city,county,country,a.primary is_primary,a.visible is_visible,
                      if(postcode is null,'',postcode) postcode,lat latitude,lng longitude
                  from contacts c left join contact_telephone ct using(contact_id) left join contact_addresses a using(contact_id) left join locations using(location_id) join records using(urn) join campaigns camp using(campaign_id) where urn = '$urn' order by c.sort,c.contact_id,ct.description";
		$query = $this->db->query($qry);
        $results = $query->result_array();
        //put the contact details into array
		if(count($results)==0){
			return false;
		} 
        foreach ($results as $result):
           // $use_fullname                            = ($this->name_field == "fullname" ? true : false);
			 $use_fullname  = true;
            $contacts[$result['contact_id']]['name'] = array(
                "fullname" => !empty($result['fullname'])?$result['fullname']:"No Name",
                "use_full" => $use_fullname
            );

            if (!isset($contacts[$result['contact_id']]['visible'])) {
                $contacts[$result['contact_id']]['visible'] = array(
                    "Job" => $result['position'],
                    "DOB" => $result['dob'],
                    "Email address" => $result['email'],
                    "Email Optout" => $result['email_optout'],
                    "Notes" => $result['notes']
                );
            }
            if (!isset($contacts[$result['contact_id']]['links'])) {
                $contacts[$result['contact_id']]['links'] = array(
                    "Linkedin" => $result['linkedin'],
                    "Facebook" => $result['facebook'],
                    "Website" => $result['website']
                );
            }
			if(strpos($result['tel_name'],"Transfer")!==false){
			 $contacts[$result['contact_id']]['transfer'][$result['telephone_id']] = array(
                "tel_name" => $result['tel_name'],
                "tel_num" => $result['telephone_number'],
                "tel_tps" => $result['tps'],
				"tel_prefix" => $result['telephone_prefix'],
				"tel_protocol" => $result['telephone_protocol']
            );	
			} else {
            $contacts[$result['contact_id']]['telephone'][$result['telephone_id']] = array(
                "tel_name" => $result['tel_name'],
                "tel_num" => $result['telephone_number'],
                "tel_tps" => $result['tps'],
				"tel_prefix" => $result['telephone_prefix'],
				"tel_protocol" => $result['telephone_protocol']
            );
			}
        //we only want to display the primary address for each contact
            if (($result['is_primary'] == "1") || ($result['is_visible'] == "1")) {
                $contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]['description']     = $result['codescription'];
                $contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]['add1']     = $result['add1'];
                $contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]['add2']     = $result['add2'];
                $contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]['add3']     = $result['add3'];
                $contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]['city'] = $result['city'];
                $contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]['county']   = $result['county'];
                $contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]['country']  = $result['country'];
                $contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]['postcode'] = $result['postcode'];
                $contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]['primary'] = $result['is_primary'];
                $contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]['visible'] = $result['is_visible'];
                array_filter($contacts[$result['contact_id']]['visible']['Address'][$result['address_id']]);
            }
        endforeach;
        return $contacts;
    }
    
    public function get_contacts_by_urn_list($urn_list) {
    	$qry = "select *, cn.contact_id
				from contacts cn
				left join contact_addresses cna ON (cna.contact_id = cn.contact_id)
				left join contact_telephone cnt ON (cnt.contact_id = cn.contact_id)
				where urn IN ".$urn_list;
    	
    	return $this->db->query($qry)->result_array();
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
		$numbers = array();
		
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

    public function get_mobile_numbers($urn){
        $numbers = array();

        $qry = "select replace(telephone_number,' ','') as telephone_number from contact_telephone left join contacts using(contact_id) where urn = '$urn' and telephone_number REGEXP '^(447|[[.+.]]447|00447|0447|07)'";
        $result =  $this->db->query($qry)->result_array();
        foreach($result as $row){
            $numbers[] = $row['telephone_number'];
        }
        $qry = "select replace(telephone_number,' ','') as telephone_number from company_telephone left join companies using(company_id) where urn = '$urn' and telephone_number REGEXP '^(447|[[.+.]]447|00447|0447|07)'";
        $result =  $this->db->query($qry)->result_array();
        foreach($result as $row){
            $numbers[] = $row['telephone_number'];
        }
        return $numbers;
    }
	

	public function save_contact ($form) {
		$insert_query = $this->db->insert_string("contacts", $form);
		$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
		$this->db->query($insert_query);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
	
		return $insert_id;
	}


    public function update_contact ($form) {

        $this->db->where("contact_id", $form['contact_id']);
        $result = $this->db->update("contacts", $form);

        $this->db->trans_complete();

        return $result;
    }

    public function get_referrals($urn)
    {

        $qry = "select
                      camp.telephone_prefix,
                      camp.telephone_protocol,
                      r.*,
                      address_id,a.description r_description,add1,add2,add3,city,county,country,a.primary is_primary,a.visible is_visible,
                      postcode,lat latitude,lng longitude
                  from referral r
                    left join referral_address a using(referral_id)
                    left join locations using(location_id)
                    join records using(urn)
                    join campaigns camp using(campaign_id)
                  where urn = '$urn'
                  order by r.referral_id";

        $query = $this->db->query($qry);
        $results = $query->result_array();

        //put the contact details into array
        if (count($results) == 0) {
            return false;
        }
        foreach ($results as $result) {

            if (!isset($referrals[$result['referral_id']]['visible'])) {
                $referrals[$result['referral_id']]['visible'] = array(
                    "Name" => $result['title'] . " " . $result['firstname'] . " " . $result['lastname'],
                    "Telephone Number" => $result['telephone_number'],
                    "Mobile Number" => $result['mobile_number'],
                    "Other Number" => $result['other_number'],
                    "Email address" => $result['email']
                );
            }
            //we only want to display the primary address for each referral
            if (($result['is_primary'] == "1") || ($result['is_visible'] == "1")) {
                $referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]['description'] = $result['r_description'];
                $referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]['add1'] = $result['add1'];
                $referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]['add2'] = $result['add2'];
                $referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]['add3'] = $result['add3'];
                $referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]['city'] = $result['city'];
                $referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]['county'] = $result['county'];
                $referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]['country'] = $result['country'];
                $referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]['postcode'] = $result['postcode'];
                $referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]['primary'] = $result['is_primary'];
                $referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]['visible'] = $result['is_visible'];
                array_filter($referrals[$result['referral_id']]['visible']['Address'][$result['address_id']]);
            }
        }

        return $referrals;
    }

    public function get_referral($id)
    {
        $qry     = "select *, referral_address.description as add_description from referral left join referral_address using(referral_id) where referral_id = '$id'";
        $results = $this->db->query($qry)->result_array();
        foreach ($results as $result):
            $referral['general'] = array(
                "referral_id" => $result['referral_id'],
                "urn" => $result['urn'],
                "title" => $result['title'],
                "firstname" => $result['firstname'],
                "lastname" => $result['lastname'],
                "telephone_number" => $result['telephone_number'],
                "mobile_number" => $result['mobile_number'],
                "other_number" => $result['other_number'],
                "email" => $result['email'],
                "user_id" => $result['user_id'],
            );
            if ($result['address_id']) {
                $referral['address'][$result['address_id']] = array(
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

        return $referral;
    }
	
	
}