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
        $where .= " and user_id = '$user' ";
        }
						if (!empty($team)) {
        $where .= " and teams.team_id = '$team' ";
        }
						if (!empty($source)) {
        $where .= " and source_id = '$source' ";
        }
		
        $qry = "select outcome,count(*) count,total from history left join outcomes using(outcome_id) left join records using(urn) left join users using(user_id) left join teams on users.team_id = teams.team_id left join (select count(*) total,history.outcome_id from history left join users using(user_id) left join teams on users.team_id = teams.team_id left join records using(urn) where 1 and history.outcome_id is not null ";
		$qry .= $where;
        
        $qry .= " ) t on history.outcome_id = outcomes.outcome_id where 1 and history.outcome_id is not null ";
        
		$qry .= $where;
        $qry .= " group by history.outcome_id order by count desc ";
		//$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }
    
    
    public function get_campaign_by_id($campaign_id) {
    	$qry = "Select *
    	from campaigns
    	where campaign_id =  '$campaign_id'";
    	 
    	return $this->db->query($qry)->result_array();
    }
    
    public function get_campaign_report_by_outcome($options,$report)
    {
    	$date_from = $options['date_from'];
		$agent = $options['agent'];
    	$date_to = $options['date_to'];
    	$campaign = $options['campaign'];
    	$team_manager = $options['team'];
    	$source = $options['source'];
    	if($report=="transfers"){
		$where = " and h.outcome_id in(70,71) ";	
		} else if ($report=="surveys"){
		$where = " and h.outcome_id = 60 ";	
		} else if($report=="appointments"){
		$where = " and h.outcome_id = 72 ";		
		} else {
    	$where = "";
		}
    	if (!empty($date_from)) {
    		$where .= " and date(contact) >= '$date_from' ";
    	}
    	if (!empty($date_to)) {
    		$where .= " and date(contact) <= '$date_to' ";
    	}
    	if (!empty($campaign)) {
    		$where .= " and ((h.outcome_id <> '71' and h.campaign_id = '$campaign') OR (h.outcome_id = '71' and ct.campaign_id = '$campaign')) ";
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
    
    	$qry = "select c.campaign_id as campaign, c.campaign_name as name, count(*) as count, o.outcome as outcome, ct.campaign_id, (select sum(hr.duration) from hours hr where c.campaign_id=hr.campaign_id) as duration
    			from history h
    			inner join records r ON (r.urn = h.urn)
    			inner join campaigns c ON (c.campaign_id = h.campaign_id)
				inner join outcomes o ON (o.outcome_id = h.outcome_id)
				inner join users u ON (u.user_id = h.user_id)
    			left  join cross_transfers ct ON (ct.history_id = h.history_id)
				where 1"
    	;
    	$qry .= $where;
    
    	$qry .= " group by h.outcome_id, campaign order by name asc";
    	return $this->db->query($qry)->result_array();
    }
    
    public function get_campaigndials_report($options)
    {
    	$date_from = $options['date_from'];
    	$date_to = $options['date_to'];
    	$campaign = $options['campaign'];
    	$agent = $options['agent'];
    	$team_manager = $options['team'];
    	$source = $options['source'];
    
    	$where = "";
    
    	if (!empty($date_from)) {
    		$where .= " and date(contact) >= '$date_from' ";
    	}
    	if (!empty($date_to)) {
    		$where .= " and date(contact) <= '$date_to' ";
    	}
    	if (!empty($campaign)) {
    		$where .= " and h.campaign_id = '$campaign' ";
    	}
    	if (!empty($agent)) {
    		$where .= " and u.user_id = '$agent' ";
    	}
    	if (!empty($team_manager)) {
    		$where .= " and u.team_id = '$team_manager' ";
    	}
    	if (!empty($source)) {
    		$where .= " and r.source_id = '$source' ";
    	}
    
    	$qry = "select c.campaign_name as name, count(*) as count
    			from history h
    			inner join records r ON (r.urn = h.urn)
				inner join campaigns c ON (c.campaign_id = h.campaign_id)
				inner join users u ON (u.user_id = h.user_id)"
    			;
    			$qry .= $where;
    
    			$qry .= "group by c.campaign_id order by c.campaign_id asc";
    
    			return $this->db->query($qry)->result_array();
    }
    
    public function get_agent_report_by_outcome($options)
    {
    	$date_from = $options['date_from'];
    	$date_to = $options['date_to'];
    	$campaign = $options['campaign'];
    	$agent = $options['agent'];
    	$team_manager = $options['team'];
    	$source = $options['source'];
    	 
    	
    	$where = "";
    	 
    	if (!empty($date_from)) {
    		$where .= " and date(contact) >= '$date_from' ";
    	}
    	if (!empty($date_to)) {
    		$where .= " and date(contact) <= '$date_to' ";
    	}
    	if (!empty($campaign)) {
    		$where .= " and ((h.outcome_id <> '71' and h.campaign_id = '$campaign') OR (h.outcome_id = '71' and ct.campaign_id = '$campaign')) ";
    	}
    	if (!empty($agent)) {
    		$where .= " and u.user_id = '$agent' ";
    	}
    	if (!empty($team_manager)) {
    		$where .= " and u.team_id = '$team_manager' ";
    	}
    	if (!empty($source)) {
    		$where .= " and r.source_id = '$source' ";
    	}
    
    	$qry = "select u.user_id as agent, u.name as name, count(*) as count, o.outcome as outcome, (select sum(hr.duration) from hours hr where u.user_id=hr.user_id) as duration
    			from history h
    			inner join records r ON (r.urn = h.urn)
				inner join outcomes o ON (o.outcome_id = h.outcome_id)
				inner join users u ON (u.user_id = h.user_id)
    			left  join cross_transfers ct ON (ct.history_id = h.history_id)
				where 1"
    			;
    			$qry .= $where;
    
    			$qry .= " group by h.outcome_id, agent order by name asc";
    			 
    			return $this->db->query($qry)->result_array();
    }
    
    public function get_agentdials_report($options)
    {
    	$date_from = $options['date_from'];
    	$date_to = $options['date_to'];
    	$campaign = $options['campaign'];
    	$agent = $options['agent'];
    	$team_manager = $options['team'];
    	$source = $options['source'];
    	 
    	$where = "";
    	 
    	if (!empty($date_from)) {
    		$where .= " and date(contact) >= '$date_from' ";
    	}
    	if (!empty($date_to)) {
    		$where .= " and date(contact) <= '$date_to' ";
    	}
    	if (!empty($campaign)) {
    		$where .= " and h.campaign_id = '$campaign' ";
    	}
    	if (!empty($agent)) {
    		$where .= " and u.user_id = '$agent' ";
    	}
    	if (!empty($team_manager)) {
    		$where .= " and u.team_id = '$team_manager' ";
    	}
    	if (!empty($source)) {
    		$where .= " and r.source_id = '$source' ";
    	}
    
    	$qry = "select u.ext as advisor, u.name as name, count(*) as count
    			from history h
    			inner join records r ON (r.urn = h.urn)
				inner join campaigns c ON (c.campaign_id = h.campaign_id)
				inner join users u ON (u.user_id = h.user_id)"
    			;
    			$qry .= $where;
    
    			$qry .= "group by advisor order by advisor asc";
    			 
    			return $this->db->query($qry)->result_array();
    }
    
    public function get_daily_report_by_outcome($options)
    {
    	$date_from = $options['date_from'];
    	$date_to = $options['date_to'];
    	$campaign = $options['campaign'];
    	$agent = $options['agent'];
    	$team_manager = $options['team'];
    	$source = $options['source'];
    	 
    	$where = "";
    	 
    	if (!empty($date_from)) {
    		$where .= " and date(contact) >= '$date_from' ";
    	}
    	if (!empty($date_to)) {
    		$where .= " and date(contact) <= '$date_to' ";
    	}
    	if (!empty($campaign)) {
    		$where .= " and ((h.outcome_id <> '71' and h.campaign_id = '$campaign') OR (h.outcome_id = '71' and ct.campaign_id = '$campaign')) ";
    	}
    	if (!empty($agent)) {
    		$where .= " and u.user_id = '$agent' ";
    	}
    	if (!empty($team_manager)) {
    		$where .= " and u.team_id = '$team_manager' ";
    	}
    	if (!empty($source)) {
    		$where .= " and r.source_id = '$source' ";
    	}
    
    	$qry = "select SUBSTRING(h.contact, 1, 10) as date, u.name as name, count(*) as count, o.outcome as outcome, (select sum(hr.duration) from hours hr where SUBSTRING(h.contact, 1, 10)=SUBSTRING(hr.date, 1, 10)) as duration
    			from history h
    			inner join records r ON (r.urn = h.urn)
				inner join outcomes o ON (o.outcome_id = h.outcome_id)
				inner join users u ON (u.user_id = h.user_id)
    			left  join cross_transfers ct ON (ct.history_id = h.history_id)
				where 1"
    			;
    			$qry .= $where;
    
    			$qry .= " group by h.outcome_id, date order by date desc";
    			 
    			return $this->db->query($qry)->result_array();
    }
	

	
}