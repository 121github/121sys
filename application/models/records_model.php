<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Records_model extends CI_Model
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
	
	public function get_record(){
		$campaign = $_SESSION['current_campaign'];
		$user_id = $_SESSION['user_id'];
		if(intval($campaign)){
		$qry = "select urn from records left join ownership using(urn) where campaign_id = '$campaign' and record_status = 1 and (outcome_id is null or nextcall < now()) and (user_id is null or user_id = '$user_id') limit 1";
		$urn = $this->db->query($qry)->row(0)->urn;
		//$this->db->replace("ownership",array("user_id"=>$user_id,"urn"=>$urn));
		return $urn;
		}
	}
	
    //sets the manager progress status as 1=pending so they know that something needs looking at on this record
    public function set_pending($urn)
    {
        $this->db->where("urn", $urn);
        $this->db->update("records", array(
            "progress_id" => "1"
        ));
    }
    //reset the record
    public function reset_record($urn)
    {
        $this->db->where("urn", $urn);
        return $this->db->update("records", array(
            "progress_id" => NULL,
            "outcome_id" => NULL,
            "record_status" => "1",
            "nextcall" => NULL
        ));
    }
    //sets the record as "dead"
    public function set_dead($urn)
    {
        $this->db->where("urn", $urn);
        $this->db->update("records", array(
            "record_status" => "3"
        ));
    }
    //sets the record status as defined in the function
    public function set_status($urn, $status)
    {
        $this->db->where("urn", $urn);
        $this->db->update("records", array(
            "record_status" => $status
        ));
    }
    
    //function to list all the records
    public function get_records($options)
    {
        $table_columns = array(
            "campaign_name",
            "fullname",
            "outcome",
            "date_format(r.date_updated,'%d/%m/%Y %H:%i')",
            "date_format(r.nextcall,'%d/%m/%Y %H:%i')",
            "rand()"
        );
        
        $join = array();
        $qry  = "select r.urn, outcome, if(com.name is null,fullname,com.name) fullname, {$this->name_field} client_name, campaign_name, date_format(r.date_updated,'%d/%m/%y') date_updated,date_format(nextcall,'%d/%m/%y') nextcall from records r ";
        //if any join is required we should apply it here
        if (isset($_SESSION['filter']['join'])) {
            $join = $_SESSION['filter']['join'];
        }
        //these joins are mandatory for sorting by name, outcome or campaign
		$join['companies']  = " left join companies com on com.urn = r.urn ";
        $join['contacts']  = " left join contacts con on con.urn = r.urn ";
        $join['outcomes']  = " left join outcomes o on o.outcome_id = r.outcome_id ";
        $join['campaigns'] = " left join campaigns camp on camp.campaign_id = r.campaign_id ";
        
        
        foreach ($join as $join_query) {
            $qry .= $join_query;
        }
        //set the default criteria
        $qry .= " where r.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        
        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $qry .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        
        //if any filter has been set then we should apply it here
        if (isset($_SESSION['filter']['where']) && !empty($_SESSION['filter']['where'])) {
            $qry .= $_SESSION['filter']['where'];
        }
        
        /*agents can only see live records unless they specifically search for dead ones
        if (!isset($_SESSION['filter']['values']['record_status']) && $_SESSION['role'] == 3) {
        $qry .= " and record_status = 1 ";
        } */
        
		/* users can only see records that have not been parked */
		 if (!isset($_SESSION['filter']['values']['parked_code'])) {
        $qry .= " and parked_code is null ";
        }
		
		
        $qry .= " group by r.urn";
        //if any order has been set then we should apply it here
        //$this->firephp->log($qry);
        $start  = $options['start'];
        $length = $options['length'];
        if (isset($_SESSION['filter']['order']) && $options['draw'] == "1") {
            $order = $_SESSION['filter']['order'];
        } else {
            $order = " order by CASE WHEN " . $table_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $table_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'] . ",urn";
            unset($_SESSION['filter']['order']);
            unset($_SESSION['filter']['values']['order']);
        }
        
        $qry .= $order;
        $qry .= "  limit $start,$length";
        //$this->firephp->log($qry);
        $records = $this->db->query($qry)->result_array();
        
        return $records;
    }
    
    public function get_nav($options = "")
    {
        $table_columns = array(
            "campaign_name",
            "fullname",
            "outcome",
            "date_format(r.date_updated,'%d/%m/%Y %H:%i')",
            "date_format(r.nextcall,'%d/%m/%Y %H:%i')",
            "rand()"
        );
        $navqry        = "select r.urn from records r ";
        $join          = array();
        //if any join is required we should apply it here
        if (isset($_SESSION['filter']['join'])) {
            $join = $_SESSION['filter']['join'];
        }
        
        //these joins are mandatory for sorting by name, outcome or campaign
        $join['contacts']  = " left join contacts con on con.urn = r.urn ";
        $join['outcomes']  = " left join outcomes o on o.outcome_id = r.outcome_id ";
        $join['campaigns'] = " left join campaigns camp on camp.campaign_id = r.campaign_id ";
        
        foreach ($join as $join_query) {
            $navqry .= $join_query;
        }
        //set the default criteria
        $navqry .= " where r.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        
        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $navqry .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        
        //if any filter has been set then we should apply it here
        if (isset($_SESSION['filter']['where'])) {
            $navqry .= $_SESSION['filter']['where'];
        }
        
        
        //group by urn to prevent dupes
        $navqry .= " group by r.urn";
        
        
        
        
        $_SESSION['navigation'] = array();
        
        if ($this->db->query($navqry)->num_rows()) {
            //if any order has been set then we should apply it here
            $order = (isset($_SESSION['filter']['order']) && $options['draw'] == "1" ? $_SESSION['filter']['order'] : " order by CASE WHEN " . $table_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $table_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'] . ",urn");
            $navqry .= $order;
            //$this->firephp->log($navqry);
            $navigation = $this->db->query($navqry)->result_array();
            
            foreach ($navigation as $navurn):
                $_SESSION['navigation'][] = $navurn['urn'];
            endforeach;
        }
        return $_SESSION['navigation'];
    }
    
    
    public function count_records($options)
    {
        
        $qry = "select count(r.urn) as `count` from records r  ";
        //if any join is required we should apply it here
        if (isset($_SESSION['filter']['join'])) {
            $join = $_SESSION['filter']['join'];
            foreach ($join as $join_query) {
                $qry .= $join_query;
            }
        }
        //set the default criteria
        $qry .= " where campaign_id in({$_SESSION['campaign_access']['list']}) ";
        
        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $qry .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        
        //if any filter has been set then we should apply it here
        if (isset($_SESSION['filter']['where'])) {
            $qry .= $_SESSION['filter']['where'];
        }
        
        
        //$this->firephp->log($qry);
        return $this->db->query($qry)->row('count');
    }
    
    public function get_details($urn, $features)
    {
        $select = "select r.urn,c.contact_id,`{$this->name_field}` fullname,title,firstname,lastname,c.email,c.linkedin,date_format(dob,'%d/%m/%Y') dob, c.notes,email_optout,c.website,c.position,ct.telephone_id, ct.description as tel_name,ct.telephone_number,ct.tps,a.address_id,custom_panel_name, a.add1,a.add2,a.add3,a.county,a.country,a.postcode,a.latitude,a.longitude,a.`primary` is_primary,date_format(r.nextcall,'%d/%m/%Y %H:%i') nextcall,o.outcome,r.outcome_id,r.record_status,r.progress_id,pd.description as progress,urgent,date_format(r.date_updated,'%d/%m/%Y %H:%i') date_updated,r.last_survey_id,r.campaign_id,camp.campaign_name,r.reset_date,park_reason ";
        $from   = " from records r ";
        $from .= "  left join outcomes o using(outcome_id) left join progress_description pd using(progress_id) ";
		$from .= "  left join park_codes pc using(parked_code) ";
        $from .= "left join contacts c using(urn) left join contact_telephone ct using(contact_id) left join contact_addresses a using(contact_id) left join campaigns camp using(campaign_id) ";
        
        if (in_array(4, $features)) {
            $select .= " ,sticky.note as sticky_note ";
            $from .= " left join sticky_notes sticky using(urn) ";
        }
        if (in_array(2, $features)) {
            $select .= ",com.company_id,com.name coname, sector_name, subsector_name,com.description codescription, com.website cowebsite,com.employees,comt.telephone_id cotelephone_id, comt.description cotel_name,comt.telephone_number cotelephone_number,coma.`primary` cois_primary,ctps,coma.address_id coaddress_id,coma.add1 coadd1,coma.add2 coadd2,coma.add3 coadd3,coma.county cocounty,coma.country cocountry,coma.postcode copostcode,coma.latitude colatitude,coma.longitude colongitude";
            $from .= " left join companies com using(urn) left join company_addresses coma using(company_id) left join company_telephone comt using(company_id) left join company_subsectors using(company_id)  left join subsectors using(subsector_id) left join sectors using(sector_id)";
        }
        if (in_array(6, $features)) {
            $select .= " ,sc.script_name,sc.script_id,sc.expandable  ";
            $from .= "  left join scripts_to_campaigns using(campaign_id) left join scripts sc using(script_id) ";
        }
        if (in_array(5, $features)) {
            $select .= " ,u.user_id,u.user_email,u.user_telephone,u.name";
            $from .= " left join ownership own using(urn) left join users u using(user_id)";
        }
        $where   = "  where r.campaign_id in({$_SESSION['campaign_access']['list']}) and urn = '$urn' ";
        $order   = " order by c.sort,c.contact_id ";
        $qry     = $select . $from . $where . $order;
        $results = $this->db->query($qry)->result_array();
        
        $fav      = "select urn from favorites where urn = '$urn' and user_id = '{$_SESSION['user_id']}'";
        $favorite = $this->db->query($fav)->num_rows();
        //put the contact details into array
        $data     = array();
        if (count($results)) {
            foreach ($results as $result):
                $use_fullname = ($this->name_field == "fullname" ? true : false);
                if ($result['contact_id']) {
                    $data['contacts'][$result['contact_id']]['name']    = array(
                        "title" => $result['title'],
                        "firstname" => $result['firstname'],
                        "lastname" => $result['lastname'],
                        "fullname" => $result['fullname'],
                        "use_full" => $use_fullname
                    );
                    $data['contacts'][$result['contact_id']]['visible'] = array(
                        "Job" => $result['position'],
                        "DOB" => $result['dob'],
                        "Email address" => $result['email'],
                        "Linkedin" => $result['linkedin'],
                        "Email Optout" => $result['email_optout'],
                        "Website" => $result['website']
                    );
                    
                    $data['contacts'][$result['contact_id']]['telephone'][$result['telephone_id']] = array(
                        "tel_name" => $result['tel_name'],
                        "tel_num" => $result['telephone_number'],
                        "tel_tps" => $result['tps']
                    );
                    
                    //we only want to display the primary address for each contact
                    if ($result['is_primary'] == "1") {
                        $data['contacts'][$result['contact_id']]['visible']['Address']['add1']     = $result['add1'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['add2']     = $result['add2'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['add3']     = $result['add3'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['county']   = $result['county'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['country']  = $result['country'];
                        $data['contacts'][$result['contact_id']]['visible']['Address']['postcode'] = $result['postcode'];
                        array_filter($data['contacts'][$result['contact_id']]['visible']['Address']);
                    }
                }
                
                if (in_array(2, $features)) {
					 if ($result['company_id']) {
                    $data['company'][$result['company_id']]["Company Name"] = $result['coname'];
                    $data['company'][$result['company_id']]['visible']      = array(
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
                    
                }
				}
                if (in_array(5, $features)) {
                    
                    //put the ownership dteails into the array
                    if ($result['user_id']) {
                        $data['ownership'][$result['user_id']] = array(
                            "name" => $result['name'],
                            "email" => $result['user_email'],
                            "telephone" => $result['user_telephone']
                        );
                    }
                }
                if (in_array(6, $features)) {
                    //put any scripts into the array
					if($result['script_id']){
                    $data['scripts'][$result['script_id']] = array(
						"script_id"=>$result['script_id'],
                        "name" => $result['script_name'],
                        "expandable" => $result['expandable']
                    );
					}
                }
            //put the record details into the array
                $data['record'] = array(
                    "urn" => $result['urn'],
					"park_reason" => $result['park_reason'],
                    "nextcall" => $result['nextcall'],
                    "outcome" => $result['outcome'],
                    "outcome_id" => $result['outcome_id'],
                    "record_status" => $result['record_status'],
                    "progress" => $result['progress'],
                    "progress_id" => $result['progress_id'],
                    "urgent" => $result['urgent'],
                    "last_update" => $result['date_updated'],
                    "last_survey_id" => $result['last_survey_id'],
                    "campaign_id" => $result['campaign_id'],
                    "campaign" => $result['campaign_name'],
                    "favorite" => $favorite,
                    "reset_date" => $result['reset_date'],
                    "custom_name" => $result['custom_panel_name']
                );
            endforeach;
        }
        if (in_array(4, $features)) {
            $data['record']["sticky_note"] = $result['sticky_note'];
        }
        //return the completed array
        return $data;
    }
    
    public function get_history($urn)
    {
        $qry = "select date_format(contact,'%d/%m/%y %H:%i') contact, u.name client_name,if(outcome_id is null,if(pd.description is null,'No Action Required',pd.description),outcome) as outcome, history_id, comments from history left join outcomes using(outcome_id) left join progress_description pd using(progress_id) left join users u using(user_id) where urn = '$urn' order by history_id desc";
        return $this->db->query($qry)->result_array();
    }
    
    public function get_outcomes($campaign)
    {
        $user_role = $_SESSION['role'];
        $qry       = "select outcome_id,outcome,delay_hours from outcomes left join outcomes_to_campaigns using(outcome_id) where campaign_id = '$campaign' and enable_select = 1 order by outcome";
        return $this->db->query($qry)->result_array();
    }
    
    public function get_users($urn = "")
    {
        if (empty($urn)):
            $qry = "select user_id,name,user_email,user_telephone from users where user_status = 1 and user_id in(select user_id from users_to_campaigns where campaign_id in({$_SESSION['campaign_access']['list']})) ";
        else:
            $qry = "select user_id,name,user_email,user_telephone from ownership left join users using(user_id) where user_status = 1 and urn = '$urn' and user_id in(select user_id from users_to_campaigns where campaign_id in({$_SESSION['campaign_access']['list']}))";
        endif;
        return $this->db->query($qry)->result_array();
    }
    
    
    public function save_ownership($urn, $owners)
    {
        //first remove the old owners for the urn
        $this->db->where("urn", $urn);
        $this->db->delete("ownership");
        //then add the new owners
        if ($owners) {
            foreach ($owners as $user) {
                $data = array(
                    "urn" => $urn,
                    "user_id" => $user
                );
                $this->db->insert("ownership", $data);
            }
        }
    }
    //returns an array of users that own a record (urn)
    public function get_ownership($urn)
    {
        $qry = "select user_id from ownership left join users using(user_id) where user_status = 1 and urn = '$urn'";
        return $this->db->query($qry)->result_array();
    }
    
    //updates a record
    public function update_record($post)
    {
        //if no nextcall is set then we just use the current timestamp else we convert the uk date to mysql
        if (empty($post['nextcall']) || !isset($post['nextcall'])) {
            $post['nextcall'] = date('Y-m-d H:i:s');
        } else {
            //if the time set is less than now then we set it as now because nextcall dates should not be in the past
            if (strtotime($post["nextcall"]) < strtotime('now')) {
                $post["nextcall"] = date('Y-m-d H:i:s');
            }
        }
        //$this->firephp->log($post['nextcall']);
        $post['date_updated'] = date('Y-m-d H:i:s');
        $update_array         = array(
            "nextcall",
            "date_updated"
        );
        //if the update is from an agent it will have an outcome_id
        if (isset($post['outcome_id'])) {
            if ($post["pending_manager"] == "1") {
                $post["progress_id"] = "1";
                $update_array[]      = "progress_id";
            } else if ($post["pending_manager"] == "2") {
                $post["progress_id"] = "1";
                $post["urgent"]      = "1";
                $update_array[]      = "urgent";
                $update_array[]      = "progress_id";
            }
            //only change the outcome and increase dial count if they are not just adding notes (outcome_id = 67)
            if ($post['outcome_id'] <> "67") {
                $update_array[] = "outcome_id";
                $qry            = "update records set dials = dials+1 where urn = '" . intval($post['urn']) . "'";
                $this->db->query($qry);
            }
            
        } else {
            //if the update is from a manager we update the progress_id instead of the outcome_id. Making sure an empty value is NULL
            $this->db->where("urn", $post['urn']);
            $this->db->update("records", array(
                "progress_id" => NULL
            ));
            if (intval($post["progress_id"]) > 0) {
                $update_array[] = "progress_id";
            }
        }
        $this->db->where("urn", $post['urn']);
		$this->firephp->log($post);
        $this->db->update("records", elements($update_array, $post));
    }
    
    //add entry to the history table
    public function add_history($hist)
    {
        //if no nextcall is set then we just use the current timestamp else we convert the uk date to mysql
        if (empty($hist['nextcall']) || !isset($hist['nextcall'])) {
            $hist["nextcall"] = date('Y-m-d H:i:s');
        } else {
            $hist["nextcall"] = to_mysql_datetime($hist['nextcall']);
            //if the time set is less than now then we set it as now because nextcall dates should not be in the past
            if (strtotime($hist["nextcall"]) < strtotime('now')) {
                $hist["nextcall"] = date('Y-m-d H:i:s');
            }
        }
        
        $hist["contact"]  = date('Y-m-d H:i');
        $hist["user_id"]  = $_SESSION['user_id'];
        $hist["role_id"]  = $_SESSION['role'];
        $hist["group_id"] = $_SESSION['group'];
         $this->db->insert("history", elements(array(
            "urn",
            "campaign_id",
            "nextcall",
            "contact",
            "description",
            "outcome_id",
            "comments",
            "nextcall",
            "user_id",
            "role_id",
            "group_id",
            "contact_id",
            "progress_id",
            "last_survey"
        ), $hist, NULL));
		return $this->db->insert_id();
    }
    
    
    //get the campaign of a given urn
    public function get_campaign($urn = "")
    {
        if (intval($urn)) {
            $this->db->select("records.campaign_id,campaign_name");
            $this->db->from('records');
            $this->db->join('campaigns', 'records.campaign_id = campaigns.campaign_id', 'left');
            $this->db->where("urn", $urn);
            $rows = $this->db->get()->result_array();
            return $rows[0];
        }
    }
    
    //get the last comment for a given urn
    public function get_last_comment($urn)
    {
        $urn      = intval($urn);
        $comment  = "";
        $qry      = "select comments from history where urn = '$urn' and comments <> '' and comments is not null order by history_id desc limit 1";
        $comments = $this->db->query($qry)->result_array();
        foreach ($comments as $row) {
            $comment = $row['comments'];
        }
        return $comment;
    }
    
    //find all dates that a record has been contacted on
    public function get_calls($urn)
    {
        $qry = "select contact from history where urn = '$urn' and `group_id` = 1";
        return $this->db->query($qry)->result_array();
    }
    
    //find all dates that a record has been contacted on
    public function get_xfers($camp)
    {
        $qry = "select xfer_campaign id,campaign_name name from campaign_xfers left join campaigns on campaigns.campaign_id = xfer_campaign where campaign_xfers.campaign_id = '$camp'";
        return $this->db->query($qry)->result_array();
    }
    
    public function get_additional_info($urn = false, $campaign, $id = false)
    {
        $fields_qry    = "select `field`,`field_name`,`is_select` from record_details_fields where campaign_id = '$campaign' order by sort";
        $fields_result = $this->db->query($fields_qry)->result_array();
        $fields        = "";
        foreach ($fields_result as $row) {
            $stuff1[$row['field_name']] = $row['field'];
            if ($row['is_select'] == 1) {
                $this->db->select("id,option");
                $this->db->where(array(
                    "field" => $row['field'],
                    "campaign_id" => $campaign
                ));
                $option_result = $this->db->get("record_details_options")->result_array();
                foreach ($option_result as $opt) {
                    $options[$opt['id']] = $opt['option'];
                }
                $stuff2[$row['field_name']] = $options;
            }
            
            if (substr($row['field'], 0, 1) == "d") {
                $sqlfield = "date_format(" . $row['field'] . ",'%d/%m/%Y')";
            } else {
                $sqlfield = $row['field'];
            }
            
            $fields .= $sqlfield . " as `" . $row['field_name'] . "`,";
        }
        
        $select = $fields . "detail_id ";
        $qry    = "select $select from record_details where urn='$urn'";
        if ($id) {
            $qry = "select $select from record_details where detail_id='$id'";
        }
        $result = $this->db->query($qry)->result_array();
        $info   = array();
        foreach ($result as $id => $detail) {
            foreach ($detail as $k => $v) {
                if ($k <> "detail_id") {
                    $info[$id][$k]["id"]   = $detail['detail_id'];
                    $info[$id][$k]["code"] = $stuff1[$k];
                    $info[$id][$k]["name"] = $k;
                    if (isset($stuff2[$k])) {
                        $info[$id][$k]["options"] = $stuff2[$k];
                    }
                    $info[$id][$k]["value"] = $v;
                    if (strpos($stuff1[$k], "c") !== false) {
                        $info[$id][$k]["type"] = "varchar";
                    } else if (substr($stuff1[$k], 1, 1) == "t") {
                        $info[$id][$k]["type"] = "datetime";
                    } else if (strpos($stuff1[$k], "n") !== false) {
                        $info[$id][$k]["type"] = "number";
                    } else {
                        $info[$id][$k]["type"] = "date";
                    }
                }
                
            }
            
        }
        //$this->firephp->log($info);
        return $info;
    }
    
    public function save_additional_info($post)
    {
        foreach ($post as $k => $v) {
            if (substr($k, 0, 1) == "d" && $k <> "detail_id") {
                $post[$k] = to_mysql_datetime($post[$k]);
            }
        }
        if (!empty($post['detail_id'])) {
            $this->db->where("detail_id", $post['detail_id']);
            return $this->db->update("record_details", $post);
        } else {
           return  $this->db->insert("record_details", $post);
        }
    }
    
    public function remove_custom_item($id)
    {
        $this->db->where("detail_id", $id);
        $this->db->delete("record_details");
    }
    
    public function delete_appointment($id)
    {
        $this->db->where("appointment_id", $id);
        $this->db->set("status", '0');
        $this->db->update("appointments");
    }
    
    //get appointmnet data for a given urn
    public function get_appointments($urn, $id = false)
    {
        $this->db->select("appointments.appointment_id,title,text,start,end,urn,postcode,appointment_attendees.user_id");
        $this->db->join("appointment_attendees", "appointment_attendees.appointment_id=appointments.appointment_id", "LEFT");
        $this->db->where(array(
            "urn" => $urn,
            "status" => "1"
        ));
        if ($id) {
            $this->db->where("appointments.appointment_id", $id);
        }
        $this->db->group_by("appointment_id");
        $result = $this->db->get("appointments")->result_array();
        return $result;
    }
    
    public function save_appointment($post)
    {
        $attendees = $post['attendees'];
        unset($post['attendees']);
        $post['start'] = to_mysql_datetime($post['start']);
        $post['end']   = to_mysql_datetime($post['end']);
        
        if (!empty($post['appointment_id'])) {
            $this->db->where("appointment_id", $post['appointment_id']);
            $this->db->delete("appointment_attendees");
            foreach ($attendees as $attendee) {
                $this->db->insert("appointment_attendees", array(
                    "appointment_id" => $post['appointment_id'],
                    "user_id" => $attendee
                ));
            }
            
            
            $this->db->where(array(
                "appointment_id" => $post['appointment_id'],
                "urn" => $post['appointment_id']
            ));
            $post['date_updated'] = date('Y-m-d H:i:s');
            $this->db->update("appointments", $post);
        } else {
            $post['created_by'] = $_SESSION['user_id'];
            $this->db->insert("appointments", $post);
            $insert = $this->db->insert_id();
            foreach ($attendees as $attendee) {
                $this->db->insert("appointment_attendees", array(
                    "appointment_id" => $insert,
                    "user_id" => $attendee
                ));
            }
        }
        
        
        
        
    }
    
	/*get all the new owners for a specific outcome*/
	public function get_owners_for_outcome($campaign_id,$outcome_id){
	 $owners = array();
        $this->db->where(array(
            "outcome_id" => $outcome_id,
            "campaign_id" => $campaign_id
        ));
        $this->db->join("ownership_trigger_users", "ownership_triggers.trigger_id = ownership_trigger_users.trigger_id", "LEFT");
        $result = $this->db->get("ownership_triggers")->result_array();
        foreach ($result as $row) {
            $owners[] = $row['user_id'];
        }
        return $owners;
}
	
    
    public function get_campaign_managers($campaign = "")
    {
        $managers = array();
        $this->db->where("campaign_id", $campaign);
        $result = $this->db->get("campaign_managers")->result_array();
        foreach ($result as $row) {
            $managers[] = $row['user_id'];
        }
        return $managers;
    }
    
	public function add_xfer($id,$campaign){
	$this->db->insert('cross_transfers',array('history_id'=>$id,'campaign_id'=>$campaign));	
	}
	
	public function get_positive_for_footer($campaign){
	$qry = "select outcome_id from outcomes_to_campaigns left join outcomes using(outcome_id) where campaign_id = '$campaign' group by outcome_id";
	$result = $this->db->query($qry)->result_array();
	$outcome = "";
		foreach($result as $row){
			if($row['outcome_id']==70){
			$outcome = "Transfers";	
			}
			if($row['outcome_id']==60&&$outcome<>"Transfers"){
			$outcome = "Surveys";
			}
			if($row['outcome_id']==72&&$outcome<>"Transfers"&&$outcome<>"Surveys"){
			$outcome = "Appointments";
			}
	}
	return $outcome;
	}
}
?>