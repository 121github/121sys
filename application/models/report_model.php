<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Report_model extends CI_Model
{
    
    
    
    function __construct()
    {
        parent::__construct();
    }
    
     public function all_answers_data()
    {
        $qry = "SELECT survey_name,l.question_id,surveys.survey_info_id,count(distinct survey_id) count,avg(answer) average_nps,tens,low_score from surveys left join survey_info using(survey_info_id) left join survey_answers using(survey_id) left join questions using(question_id)
	 left join (select surveys.survey_info_id,count(*) tens from surveys left join survey_answers using(survey_id) left join questions using(question_id) where answer = 10 and answer is not null and nps_question = 1 group by surveys.survey_info_id) t on t.survey_info_id = surveys.survey_info_id
	 left join (select surveys.survey_info_id,question_id,count(*) low_score from surveys left join survey_answers using(survey_id) left join questions using(question_id) where answer < 4 and answer is not null and nps_question = 1 group by surveys.survey_info_id) l on l.survey_info_id = surveys.survey_info_id where nps_question = 1
	 group by surveys.survey_info_id";
        return $this->db->query($qry)->result_array();
    }

 public function answers_data($survey)
    {
        if (!empty($survey)) {
            $qry_filter = " and questions.survey_info_id = " . intval($survey);
        } else {
            $qry_filter = "";
        }
        $qry = "SELECT survey_name,sa.question_id,surveys.survey_info_id,question_name,question_script,count(sa.question_id) count,avg(answer) average,t.tens,IF(low_score is null,'0',low_score) low_score from surveys left join survey_info using(survey_info_id) left join survey_answers sa using(survey_id) left join questions using(question_id) 
	 left join (select question_id,count(*) tens from survey_answers left join questions using(question_id) where answer = 10 and answer is not null $qry_filter group by question_id) t on t.question_id = sa.question_id
	  left join (select question_id,count(*) low_score from survey_answers left join questions using(question_id) where answer < 4 and answer is not null $qry_filter group by question_id) l on l.question_id = sa.question_id
	 where answer is not null  $qry_filter group by sa.question_id ";
        return $this->db->query($qry)->result_array();
    }

  public function get_activity($options)
    {
		$date_from = $options['date_from'];
		$date_to = $options['date_to'];
		$campaign = $options['campaign'];
		$user = isset($options['agent'])?$options['agent']:"";
		$team = isset($options['team'])?$options['team']:"";
		$source = $options['source'];
		
		$where = "";
		if (!empty($date_from)) {
        $where .= " and date(contact) >= '$date_from' ";
        }
		if (!empty($date_to)) {
        $where .= " and date(contact) <= '$date_to' ";
        }
		if (!empty($campaign)) {
        $where .= " and history.campaign_id = '$campaign' ";
        }
				if (!empty($user)) {
        $where .= " and history.user_id = '$user' ";
        }
						if (!empty($team)) {
        $where .= " and teams.team_id = '$team' ";
        }
						if (!empty($source)) {
        $where .= " and source_id = '$source' ";
        }
		//if the user does not have the agent reporting permission they can only see their own stats 
		if(@!in_array("by agent",$_SESSION['permissions'])){
		$where .= " and history.user_id = '{$_SESSION['user_id']}' ";	
		}
		
				//if the user does not have the group reporting permission they can only see their own stats 
		if(@!in_array("by group",$_SESSION['permissions'])){
		//$where .= " and history.group_id = '{$_SESSION['group']}' ";	
		}
		
		//if the user does not have the group reporting permission they can only see their own stats
		if(@!in_array("by team",$_SESSION['permissions'])){
		//this doesnt work because some people dont have a team such as clients
		//$where .= " and history.team_id = '{$_SESSION['team']}' ";	
		}
		
        $qry = "select outcome,count(*) count,total from history left join outcomes using(outcome_id) left join records using(urn) left join users using(user_id) left join teams on users.team_id = teams.team_id left join (select count(*) total,history.outcome_id from history left join outcomes using(outcome_id) left join users using(user_id) left join teams on users.team_id = teams.team_id left join records using(urn) where 1 and outcome is not null ";
		$qry .= $where;
        
        $qry .= " ) t on history.outcome_id = outcomes.outcome_id where 1 and outcome is not null ";
        
		$qry .= $where;
        $qry .= " group by history.outcome_id order by count desc ";
		$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }
    
    
    public function get_campaign_by_id($campaign_id) {
    	$qry = "Select *
    	from campaigns
    	where campaign_id =  '$campaign_id'";
    	 
    	return $this->db->query($qry)->result_array();
    }
    
    public function get_transfers_data($options)
    {
    	$date_from = $options['date_from'];
		$agent = $options['agent'];
    	$date_to = $options['date_to'];
    	$campaign = $options['campaign'];
    	$team_manager = $options['team'];
    	$source = $options['source'];

        $hours_where = "";
    	$where = "";
    	$crosswhere = "";
    	if (!empty($date_from)) {
    		$where .= " and date(contact) >= '$date_from' ";
            $hours_where .= " and hr.date >= '$date_from' ";
    	}
    	if (!empty($date_to)) {
    		$where .= " and date(contact) <= '$date_to' ";
            $hours_where .= " and hr.date <= '$date_to' ";
    	}
    	if (!empty($campaign)) {
    		$where .= " and h.campaign_id = '$campaign' ";
			$crosswhere .= " and ct.campaign_id = '$campaign' ";
            $hours_where .= " and hr.campaign_id = '$campaign' ";
    	}
    	if (!empty($team_manager)) {
    		$where .= " and u.team_id = '$team_manager' ";
    	}
		if (!empty($agent)) {
    		$where .= " and h.user_id = '$agent' ";
    	}
    	if (!empty($source)) {
    		$where .= " and r.source_id = '$source' ";
    	}
    $joins = " left join users u using(user_id) left join records r using(urn) ";
    	$qry = "select c.campaign_id,campaign_name,if(transfer_count is null,0,transfer_count) transfer_count,if(cross_count is null,0,cross_count) cross_count,d.dials+if(cross_count is null,'0',cross_count) as total_dials,(select sum(hr.duration) from hours hr where h.user_id=hr.user_id $hours_where) as duration from history h left join campaigns c using(campaign_id)  left join
		(select count(*) transfer_count,h.campaign_id from history h $joins where h.outcome_id = 70 $where group by campaign_id) transfers on transfers.campaign_id = h.campaign_id left join 
		(select count(*) cross_count,ct.campaign_id from history h $joins left join cross_transfers ct using(history_id) where h.outcome_id = 71 $crosswhere group by ct.campaign_id) crosstrans on crosstrans.campaign_id = h.campaign_id left join 
		(select count(*) dials,h.campaign_id from history h $joins where h.outcome_id <> 71 $where group by campaign_id) d on d.campaign_id = h.campaign_id group by h.campaign_id "
    	;

        return $this->db->query($qry)->result_array();
    }
    
	public function get_outcome_data($options)
    {
		if($options['group']=="agent"){
			$group_by = "h.user_id";
			$id = "h.user_id";
			$name = "u.name";
			$joins = " left join users u using(user_id) left join records r using(urn) ";
			$hours = "hr.`user_id` = h.user_id ";
		}else if($options['group']=="date"){
			$group_by = "date(contact)";
            $id = "date(h.contact) `sql`,date_format(h.contact,'%d/%m/%y')";
			$name = "'All'";
			$hours = "hr.`date` = date(h.contact)";
        }else if($options['group']=="time"){
            $group_by = "hour(h.contact)";
            $id = "hour(h.contact) `sql`,date_format(h.contact,'%H:00:00')";
            $name = "'All'";
            $hours = 1;
            //$hours = "1";
        } else {
			$group_by = "h.campaign_id";
			$id = "c.campaign_id";
			$name = "campaign_name";
			$hours = "hr.`campaign_id` = h.campaign_id";
		}
		
		$outcome_id = $options['outcome'];
    	$date_from = $options['date_from'];
		$agent = $options['agent'];
    	$date_to = $options['date_to'];
    	$campaign = $options['campaign'];
    	$team_manager = $options['team'];
    	$source = $options['source'];
		$hours_where = "";
    	$where = "";
    	$crosswhere = "";
    	if (!empty($date_from)) {
    		$where .= " and date(contact) >= '$date_from' ";
			$hours_where .= " and hr.date >= '$date_from' ";
    	}
    	if (!empty($date_to)) {
    		$where .= " and date(contact) <= '$date_to' ";
			$hours_where .= " and hr.date <= '$date_to' ";
    	}
    	if (!empty($campaign)) {
    		$where .= " and h.outcome_id <> 71 and h.campaign_id = '$campaign' ";
			$crosswhere .= " and ct.campaign_id = '$campaign' ";
			$hours_where .= " and hr.campaign_id = '$campaign' ";
    	}
    	if (!empty($team_manager)) {
    		$where .= " and h.team_id = '$team_manager' ";
    	}
		if (!empty($agent)) {
    		$where .= " and h.user_id = '$agent' ";
            $name = "u.name";
    	}
    	if (!empty($source)) {
    		$where .= " and r.source_id = '$source' ";
    	}
    $joins = " left join users u using(user_id) left join records r using(urn) ";
    	$qry = "select $id id,$name name,if(outcome_count is null,0,outcome_count) outcome_count,if(d.dials is null,'0',d.dials) as total_dials,(select sum(hr.duration)
		 from hours hr where $hours $hours_where) as duration from history h left join campaigns c using(campaign_id)  $joins left join 
		(select count(*) outcome_count,$group_by gb from history h $joins where h.outcome_id = $outcome_id $where group by $group_by) oc on oc.gb = $group_by left join
		(select count(*) dials,$group_by dd from history h $joins where h.outcome_id is not null $where group by $group_by) d on d.dd = $group_by
        where 1 $where
		group by $group_by "
    	;
		$this->firephp->log($qry);
    	return $this->db->query($qry)->result_array();
    }
	
	
   
    public function get_agent_transfer_data($options)
    {
    	$date_from = $options['date_from'];
		$agent = $options['agent'];
    	$date_to = $options['date_to'];
    	$campaign = $options['campaign'];
    	$team_manager = $options['team'];
    	$source = $options['source'];
    	
    	$where = "";
		$hours_where = "";
    	$crosswhere = "";
    	if (!empty($date_from)) {
    		$where .= " and date(contact) >= '$date_from' ";
			$hours_where = " and `date` >= '$date_from' ";
    	}
    	if (!empty($date_to)) {
    		$where .= " and date(contact) <= '$date_to' ";
			$hours_where = " and `date` >= '$date_to' ";
    	}
    	if (!empty($campaign)) {
    		$where .= " and h.campaign_id = '$campaign' ";
			$crosswhere .= " and ct.campaign_id = '$campaign' ";
    	}
    	if (!empty($team_manager)) {
    		$where .= " and u.team_id = '$team_manager' ";
    	}
		if (!empty($agent)) {
    		$where .= " and h.user_id = '$agent' ";
    	}
    	if (!empty($source)) {
    		$where .= " and r.source_id = '$source' ";
    	}
    $joins = " left join users u using(user_id) left join records r using(urn) ";
    	$qry = "select h.user_id,u.name,if(transfer_count is null,0,transfer_count) transfer_count,if(cross_count is null,0,cross_count) cross_count,d.dials+if(cross_count is null,'0',cross_count) as total_dials,(select sum(hr.duration) from hours hr where h.user_id=hr.user_id $hours_where) as duration from history h $joins left join 
		(select count(*) transfer_count,h.user_id from history h $joins where h.outcome_id = 70 $where group by h.user_id) transfers on transfers.user_id = h.user_id left join 
		(select count(*) cross_count,h.user_id from history h $joins left join cross_transfers ct using(history_id) where h.outcome_id = 71 $crosswhere group by h.user_id) crosstrans on crosstrans.user_id = h.user_id left join 
		(select count(*) dials,h.user_id from history h $joins where h.outcome_id <> 71 $where group by user_id) d on d.user_id = h.user_id group by h.user_id "
    	;
    			 
    			return $this->db->query($qry)->result_array();
    }
    
  
    
    public function get_daily_transfer_data($options)
    {
    	$date_from = $options['date_from'];
		$agent = $options['agent'];
    	$date_to = $options['date_to'];
    	$campaign = $options['campaign'];
    	$team_manager = $options['team'];
    	$source = $options['source'];
    	$group_by_field = $options['view'];
		
    	$where = "";
		$hours_where = "";
    	$crosswhere = "";
    	if (!empty($date_from)) {
    		$where .= " and date(contact) >= '$date_from' ";
			$hours_where .= " and `date` >= '$date_from' ";
    	}
    	if (!empty($date_to)) {
    		$where .= " and date(contact) <= '$date_to' ";
			$hours_where .= " and `date` >= '$date_to' ";
    	}
    	if (!empty($campaign)) {
    		$where .= " and h.campaign_id = '$campaign' ";
			$crosswhere .= " and ct.campaign_id = '$campaign' ";
    	}
		$select="";
		$group_by = "";
		$transfer_join = "";
		$cross_transfers_join = "";
		$all_dials_join = "";
		$group_by_details["user_id"] = array("name"=>"u.name","field"=>"h.user_id");
		$group_by_details["team_id"] = array("name"=>"teams.team_name","field"=>"h.team_id");
		$group_by_details["source_id"] = array("name"=>"data_sources.source_name","field"=>"r.source_id");
		$group_by_details["campaign_id"] = array("name"=>"c.campaign_name","field"=>"h.campaign_id");
		
		if (!empty($group_by_field)){
			$group_by = ",".$group_by_details[$group_by_field]['field'];
			$select = ",".$group_by_details[$group_by_field]['name']." as name,".$group_by_details[$group_by_field]['field'] ." as id";
			$transfer_join = " and transfers.id = ".$group_by_details[$group_by_field]['field'];
			$cross_transfers_join = "and crosstrans.id = ".$group_by_details[$group_by_field]['field'];
			$all_dials_join = " and d.id = ".$group_by_details[$group_by_field]['field'];
    	}
    	if (!empty($team_manager)) {
    		$where .= " and u.team_id = '$team_manager' ";
    	}
		if (!empty($agent)) {
    		$where .= " and h.user_id = '$agent' ";
    	}
    	if (!empty($source)) {
    		$where .= " and r.source_id = '$source' ";
    	}

        $joins = " left join users u using(user_id) left join teams on h.team_id = teams.team_id left join records r using(urn) left join data_sources using(source_id) left join campaigns c on c.campaign_id = h.campaign_id ";
	
    	$qry = "select date(contact) `date` $select,if(transfer_count is null,0,transfer_count) transfer_count,if(cross_count is null,0,cross_count) cross_count,d.dials+if(cross_count is null,'0',cross_count) as total_dials,(select sum(hr.duration) from hours hr where h.user_id=hr.user_id $hours_where) as duration from history h $joins left join 
		(select count(*) transfer_count,date(contact) `date` $select from history h $joins where h.outcome_id = 70 $where group by date(contact) $group_by) transfers on transfers.`date` = date(h.contact) $transfer_join left join 
		(select count(*) cross_count,date(contact) `date` $select from history h $joins left join cross_transfers ct using(history_id) where h.outcome_id = 71 $crosswhere group by date(contact) $group_by) crosstrans on crosstrans.`date` = date(h.contact) $cross_transfers_join left join 
		(select count(*) dials,date(contact) `date` $select from history h $joins where h.outcome_id <> 71 $where group by date(contact) $group_by) d on d.`date` = date(h.contact) $all_dials_join group by date(h.contact) $group_by having total_dials is not null"
    	;

        return $this->db->query($qry)->result_array();
    }

    public function get_time_transfer_data($options)
    {
        $date_from = $options['date_from'];
        $agent = $options['agent'];
        $date_to = $options['date_to'];
        $campaign = $options['campaign'];
        $team_manager = $options['team'];
        $source = $options['source'];
        $group_by_field = $options['view'];

    	$where = "";
		$hours_where = "";
    	$crosswhere = "";
    	if (!empty($date_from)) {
    		$where .= " and date(contact) >= '$date_from' ";
			$hours_where .= " and `date` >= '$date_from' ";
    	}
    	if (!empty($date_to)) {
    		$where .= " and date(contact) <= '$date_to' ";
			$hours_where .= " and `date` >= '$date_to' ";
    	}
        if (!empty($campaign)) {
            $where .= " and h.campaign_id = '$campaign' ";
            $crosswhere .= " and ct.campaign_id = '$campaign' ";
        }
        $select="";
        $group_by = "";
        $transfer_join = "";
        $cross_transfers_join = "";
        $all_dials_join = "";
        $group_by_details["user_id"] = array("name"=>"u.name","field"=>"h.user_id");
        $group_by_details["team_id"] = array("name"=>"teams.team_name","field"=>"h.team_id");
        $group_by_details["source_id"] = array("name"=>"data_sources.source_name","field"=>"r.source_id");
        $group_by_details["campaign_id"] = array("name"=>"c.campaign_name","field"=>"h.campaign_id");

        if (!empty($group_by_field)){
            $group_by = ",".$group_by_details[$group_by_field]['field'];
            $select = ",".$group_by_details[$group_by_field]['name']." as name,".$group_by_details[$group_by_field]['field'] ." as id";
            $transfer_join = " and transfers.id = ".$group_by_details[$group_by_field]['field'];
            $cross_transfers_join = "and crosstrans.id = ".$group_by_details[$group_by_field]['field'];
            $all_dials_join = " and d.id = ".$group_by_details[$group_by_field]['field'];
        }
        if (!empty($team_manager)) {
            $where .= " and u.team_id = '$team_manager' ";
        }
        if (!empty($agent)) {
            $where .= " and h.user_id = '$agent' ";
        }
        if (!empty($source)) {
            $where .= " and r.source_id = '$source' ";
        }
        $joins = " left join users u using(user_id) left join teams on h.team_id = teams.team_id left join records r using(urn) left join data_sources using(source_id) left join campaigns c on c.campaign_id = h.campaign_id ";

        $qry = "select hour(h.contact) `time` $select,if(transfer_count is null,0,transfer_count) transfer_count,if(cross_count is null,0,cross_count) cross_count,d.dials+if(cross_count is null,'0',cross_count) as total_dials from history h $joins left join
		(select count(*) transfer_count,hour(contact) `time` $select from history h $joins where h.outcome_id = 70 $where group by hour(contact) $group_by) transfers on transfers.`time` = hour(h.contact) $transfer_join left join
		(select count(*) cross_count,hour(contact) `time` $select from history h $joins left join cross_transfers ct using(history_id) where h.outcome_id = 71 $crosswhere group by hour(contact) $group_by) crosstrans on crosstrans.`time` = hour(h.contact) $cross_transfers_join left join
		(select count(*) dials,hour(contact) `time` $select from history h $joins where h.outcome_id <> 71 $where group by hour(contact) $group_by) d on d.`time` = hour(h.contact) $all_dials_join group by hour(h.contact) $group_by having total_dials is not null"
        ;

        return $this->db->query($qry)->result_array();
    }

    public function get_email_data($options)
    {
        if($options['group']=="agent"){
            $group_by = "eh.user_id";
            $id = "eh.user_id `sql`, eh.user_id";
            $name = "u.name";
        }else if($options['group']=="date"){
            $group_by = "date(eh.sent_date)";
            $id = "date(eh.sent_date) `sql`,date_format(eh.sent_date,'%d/%m/%y')";
            $name = "'All'";
        }else if($options['group']=="time"){
            $group_by = "hour(eh.sent_date)";
            $id = "hour(eh.sent_date) `sql`,date_format(eh.sent_date,'%H:00:00')";
            $name = "'All'";
        } else {
            $group_by = "c.campaign_id";
            $id = "c.campaign_id `sql`, c.campaign_id";
            $name = "campaign_name";
        }

        $date_from = $options['date_from'];
        $agent = $options['agent'];
        $date_to = $options['date_to'];
        $template = $options['template'];
        $campaign = $options['campaign'];
        $team_manager = $options['team'];
        $source = $options['source'];
        $hours_where = "";
        $where = "";
        if (!empty($date_from)) {
            $where .= " and date(eh.sent_date) >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(eh.sent_date) <= '$date_to' ";
        }
        if (!empty($template)) {
            $where .= " and eh.template_id = '$template' ";
        }
        if (!empty($campaign)) {
            $where .= " and c.campaign_id = '$campaign' ";
        }
        if (!empty($team_manager)) {
            $where .= " and u.team_id = '$team_manager' ";
        }
        if (!empty($agent)) {
            $where .= " and eh.user_id = '$agent' ";
            $name = "u.name";
        }
        if (!empty($source)) {
            $where .= " and r.source_id = '$source' ";
        }
		
		//if the user does not have the agent reporting permission they can only see their own stats 
		if(@!in_array("by agent",$_SESSION['permissions'])){
		$where .= " and eh.user_id = '{$_SESSION['user_id']}' ";	
		}
		
				//if the user does not have the group reporting permission they can only see their own stats 
		if(@!in_array("by group",$_SESSION['permissions'])){
		//$where .= " and eh.group_id = '{$_SESSION['group']}' ";	
		}
		
						//if the user does not have the group reporting permission they can only see their own stats 
		if(@!in_array("by team",$_SESSION['permissions'])){
		//$where .= " and users.team_id = '{$_SESSION['team']}' ";	
		}
		
        $joins = "
          inner join records r ON (r.urn = eh.urn)
          inner join campaigns c ON (c.campaign_id = r.campaign_id)
          inner join users u ON (u.user_id = eh.user_id)";

        $qry = "select $id id,
                $name name,
                count(*) as email_sent_count,
                if(email_read_count is null,0,email_read_count) email_read_count,
                if(email_unsent_count is null,0,email_unsent_count) email_unsent_count
        from email_history eh left join users using(user_id) 
          $joins
          left join (select count(*) email_read_count,$group_by gb from email_history eh $joins where eh.read_confirmed = 1 $where group by $group_by) erc on erc.gb = $group_by
          left join (select count(*) email_unsent_count,$group_by gb_2 from email_history eh $joins where eh.status = 0 $where group by $group_by) euc on euc.gb_2 = $group_by
        where eh.status=1 $where
		group by $group_by "
        ;

        return $this->db->query($qry)->result_array();
    }
}