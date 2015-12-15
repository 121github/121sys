<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Report_model extends CI_Model
{


    function __construct()
    {
        parent::__construct();
    }

    public function get_audit_data($options)
    {
        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $campaign = $options['campaign'];
        $user = isset($options['agent']) ? $options['agent'] : "";
        $team = isset($options['team']) ? $options['team'] : "";
        $source = $options['source'];

        $where = " and history.campaign_id in({$_SESSION['campaign_access']['list']}) ";
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
        $qry = "select campaign_name,table_name,change_type,count(*) from audit left join records using(urn) left join campaigns using(campaign_id) group by campaign_id,table_name,change_type";
        //not finished
    }


    public function all_answers_data()
    {
        $qry = "SELECT survey_name,l.question_id,surveys.survey_info_id,count(distinct survey_id) count,avg(answer) average_nps,tens,low_score from surveys left join survey_info using(survey_info_id) left join surveys_to_campaigns using(survey_info_id) left join survey_answers using(survey_id) left join questions using(question_id)
	 left join (select surveys.survey_info_id,count(*) tens from surveys left join survey_answers using(survey_id) left join questions using(question_id) where answer = 10 and answer is not null and nps_question = 1 group by surveys.survey_info_id) t on t.survey_info_id = surveys.survey_info_id
	 left join (select surveys.survey_info_id,question_id,count(*) low_score from surveys left join survey_answers using(survey_id) left join questions using(question_id) where answer < 7 and answer is not null and nps_question = 1 group by surveys.survey_info_id) l on l.survey_info_id = surveys.survey_info_id where nps_question = 1 and campaign_id in({$_SESSION['campaign_access']['list']})
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
        $qry = "SELECT survey_name,sa.question_id,surveys.survey_info_id,question_name,question_script,count(sa.question_id) count,avg(answer) average,t.tens,IF(low_score is null,'0',low_score) low_score from surveys left join survey_info using(survey_info_id) left join surveys_to_campaigns using(survey_info_id) left join survey_answers sa using(survey_id) left join questions using(question_id) 
	 left join (select question_id,count(*) tens from survey_answers left join questions using(question_id) where answer = 10 and answer is not null $qry_filter group by question_id) t on t.question_id = sa.question_id
	  left join (select question_id,count(*) low_score from survey_answers left join questions using(question_id) where answer < 7 and answer is not null $qry_filter group by question_id) l on l.question_id = sa.question_id 
	 where answer is not null and campaign_id in({$_SESSION['campaign_access']['list']}) $qry_filter group by sa.question_id ";
        return $this->db->query($qry)->result_array();
    }

    public function get_activity($options)
    {
        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();
        $outcomes = isset($options['outcomes']) ? $options['outcomes'] : array();
        $users = isset($options['agents']) ? $options['agents'] : array();
        $teams = isset($options['teams']) ? $options['teams'] : array();
        $sources = isset($options['sources']) ? $options['sources'] : array();

        $where = "";
        if (!empty($date_from)) {
            $where .= " and date(contact) >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(contact) <= '$date_to' ";
        }
        if (!empty($campaigns)) {
            $where .= " and history.campaign_id IN (".implode(",",$campaigns).") ";
        }
        if (!empty($outcomes)) {
            $where .= " and history.outcome_id IN (".implode(",",$outcomes).") ";
        }
        if (!empty($users)) {
            $where .= " and history.user_id IN (".implode(",",$users).") ";
        }
        if (!empty($teams)) {
            $where .= " and teams.team_id IN (".implode(",",$teams).") ";
        }
        if (!empty($sources)) {
            $where .= " and history.source_id IN (".implode(",",$sources).") ";
        }
        //if the user does not have the agent reporting permission they can only see their own stats
        if (@!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and history.user_id = '{$_SESSION['user_id']}' ";
        }

        //if the user does not have the group reporting permission they can only see their own stats
        if (@!in_array("by group", $_SESSION['permissions'])) {
            //$where .= " and history.group_id = '{$_SESSION['group']}' ";
        }

        //if the user does not have the group reporting permission they can only see their own stats
        if (@!in_array("by team", $_SESSION['permissions'])) {
            //this doesnt work because some people dont have a team such as clients
            //$where .= " and history.team_id = '{$_SESSION['team']}' ";
        }

        $qry = "select outcome,count(*) count,total from history left join outcomes using(outcome_id) left join records using(urn) left join users using(user_id) left join teams on users.team_id = teams.team_id left join (select count(*) total,history.outcome_id from history left join outcomes using(outcome_id) left join users using(user_id) left join teams on users.team_id = teams.team_id left join records using(urn) where 1 and outcome is not null and history.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= $where;

        $qry .= " ) t on history.outcome_id = outcomes.outcome_id where 1 and outcome is not null and history.campaign_id in({$_SESSION['campaign_access']['list']}) ";

        $qry .= $where;
        $qry .= " group by history.outcome_id order by count desc ";
        return $this->db->query($qry)->result_array();
    }

    public function get_campaign_by_id($campaign_id)
    {
        $qry = "Select *
    	from campaigns
    	where campaign_id =  '$campaign_id' and campaign_id in({$_SESSION['campaign_access']['list']})";

        return $this->db->query($qry)->result_array();
    }

    public function get_outcome_data($options)
    {
        $joins = '';
        if ($options['group'] == "agent") {
            $group_by = "h.user_id";
            $id = "h.user_id";
            $name = "u.name";
            //$joins .= " left join users u using(user_id) left join records r using(urn) ";
            $hours = "hr.`user_id` = h.user_id ";
        } else if ($options['group'] == "date") {
            $group_by = "date(contact)";
            $id = "date(h.contact) `sql`,date_format(h.contact,'%d/%m/%y')";
            $name = "'All'";
            $hours = "hr.`date` = date(h.contact)";
        } else if ($options['group'] == "time") {
            $group_by = "hour(h.contact)";
            $id = "hour(h.contact) `sql`,date_format(h.contact,'%H:00:00')";
            $name = "'All'";
            $hours = 1;
        } else if ($options['group'] == "reason") {
            $group_by = "h.outcome_reason_id";
            $id = "outr.outcome_reason_id";
            $name = "outr.outcome_reason";
            $joins .= " inner join outcome_reasons outr ON (outr.outcome_reason_id = h.outcome_reason_id) ";
            $hours = 1;
        } else {
            $group_by = "h.campaign_id";
            $id = "c.campaign_id";
            $name = "campaign_name";
            $hours = "hr.`campaign_id` = h.campaign_id";
        }

        $outcome_ids = isset($options['outcomes']) ? $options['outcomes'] : array();
        $date_from = $options['date_from'];
        $agents = isset($options['agents']) ? $options['agents'] : array();
        $date_to = $options['date_to'];
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();
        $team_managers = isset($options['teams']) ? $options['teams'] : array();
        $sources = isset($options['sources']) ? $options['sources'] : array();
        $hours_where = "";
        $where = "";
        $crosswhere = "";
        $outcomes_where = "";
        if (!empty($date_from)) {
            $where .= " and date(contact) >= '$date_from' ";
            $hours_where .= " and hr.date >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(contact) <= '$date_to' ";
            $hours_where .= " and hr.date <= '$date_to' ";
        }
        if (!empty($campaigns)) {
            $where .= " and h.outcome_id NOT IN (71) and h.campaign_id IN (" . implode(",", $campaigns) . ") ";
            $crosswhere .= " and ct.campaign_id IN (" . implode(",", $campaigns) . ") ";
            $hours_where .= " and hr.campaign_id IN (" . implode(",", $campaigns) . ") ";
        }
        if (!empty($outcome_ids)) {
            $outcomes_where .= " where h.outcome_id IN (" . implode(",", $outcome_ids) . ") ";
        } else {
            $outcomes_where = "where 1 ";
        }
        if (!empty($team_managers)) {
            $where .= " and h.team_id IN (" . implode(",", $team_managers) . ") ";
        }
        if (!empty($agents)) {
            $where .= " and h.user_id IN (" . implode(",", $agents) . ") ";
            $hours_where .= " and hr.user_id IN (" . implode(",", $agents) . ") ";
            //$name = "u.name";
        }
        if (!empty($sources)) {
            $where .= " and r.source_id IN (" . implode(",", $sources) . ") ";
        }
        $where .= " and h.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $joins = " left join users u using(user_id) left join records r using(urn) " . $joins;
        $qry = "select
                    $id id,
                    $name name,
                    if(outcome_count is null,0,outcome_count) outcome_count,
                    if(d.dials is null,'0',d.dials) as total_dials,
                    (select sum(hr.duration) from hours hr where $hours $hours_where) as duration
                from history h
                left join campaigns c using(campaign_id)  $joins
                left join (select count(*) outcome_count,$group_by gb from history h $joins $outcomes_where $where group by $group_by) oc on oc.gb = $group_by
                left join (select count(*) dials,$group_by dd from history h $joins where h.outcome_id is not null $where group by $group_by) d on d.dd = $group_by
        where 1 $where
		group by $group_by ";

        return $this->db->query($qry)->result_array();
    }

    public function get_email_data($options)
    {
        if ($options['group'] == "agent") {
            $group_by = "eh.user_id";
            $id = "eh.user_id `sql`, eh.user_id";
            $name = "u.name";
        } else if ($options['group'] == "date") {
            $group_by = "date(eh.sent_date)";
            $id = "date(eh.sent_date) `sql`,date_format(eh.sent_date,'%d/%m/%y')";
            $name = "'All'";
        } else if ($options['group'] == "time") {
            $group_by = "hour(eh.sent_date)";
            $id = "hour(eh.sent_date) `sql`,date_format(eh.sent_date,'%H:00:00')";
            $name = "'All'";
        } else {
            $group_by = "c.campaign_id";
            $id = "c.campaign_id `sql`, c.campaign_id";
            $name = "campaign_name";
        }

        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $agents = isset($options['agents']) ? $options['agents'] : array();
        $templates = isset($options['templates']) ? $options['templates'] : array();
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();
        $team_managers = isset($options['teams']) ? $options['teams'] : array();
        $sources = isset($options['sources']) ? $options['sources'] : array();
        $hours_where = "";
        $where = "";
        if (!empty($date_from)) {
            $where .= " and date(eh.sent_date) >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(eh.sent_date) <= '$date_to' ";
        }
        if (!empty($templates)) {
            $where .= " and eh.template_id IN (".implode(",",$templates).") ";
        }
        if (!empty($campaigns)) {
            $where .= " and c.campaign_id IN (".implode(",",$campaigns).") ";
        }
        if (!empty($team_managers)) {
            $where .= " and u.team_id IN (".implode(",",$team_managers).") ";
        }
        if (!empty($agents)) {
            $where .= " and eh.user_id IN (".implode(",",$agents).") ";
            $name = "u.name";
        }
        if (!empty($sources)) {
            $where .= " and r.source_id IN (".implode(",",$sources).") ";
        }

        //if the user does not have the agent reporting permission they can only see their own stats
        if (@!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and eh.user_id = '{$_SESSION['user_id']}' ";
        }

        //if the user does not have the group reporting permission they can only see their own stats
        if (@!in_array("by group", $_SESSION['permissions'])) {
            $where .= " and u.group_id = '{$_SESSION['group']}' ";
        }

        //if the user does not have the group reporting permission they can only see their own stats
        if (@!in_array("by team", $_SESSION['permissions'])) {
            $where .= " and u.team_id = '{$_SESSION['team']}' ";
        }
        $where .= " and r.campaign_id in({$_SESSION['campaign_access']['list']})";
        $joins = "
          inner join records r ON (r.urn = eh.urn)
          inner join campaigns c ON (c.campaign_id = r.campaign_id)
          inner join users u ON (u.user_id = eh.user_id)";

        $qry = "select $id id,
                $name name,
                count(*) as email_sent_count,
                if(email_read_count is null,0,email_read_count) email_read_count,
				if(email_pending_count is null,0,email_pending_count) email_pending_count,
                if(email_unsent_count is null,0,email_unsent_count) email_unsent_count
        from email_history eh left join users using(user_id) 
          $joins
          left join (select count(*) email_read_count,$group_by gb from email_history eh $joins where eh.read_confirmed = 1 $where group by $group_by) erc on erc.gb = $group_by
		   left join (select count(*) email_pending_count,$group_by gb from email_history eh $joins where eh.pending = 1 $where group by $group_by) epc on epc.gb = $group_by
          left join (select count(*) email_unsent_count,$group_by gb_2 from email_history eh $joins where eh.status = 0 and pending = 0 $where group by $group_by) euc on euc.gb_2 = $group_by
        where eh.status=1 $where
		group by $group_by ";

        return $this->db->query($qry)->result_array();
    }

public function get_realtime_history($options){
	$date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $user = isset($options['agent']) ? $options['agent'] : "";
        $team = isset($options['team']) ? $options['team'] : "";
        $outcome = isset($options['outcome']) ? $options['outcome'] : "";
 		$campaign = isset($options['campaign']) ? $options['campaign'] : "";
		
        $where = "";

        if (!empty($date_from)) {
            $where .= " and date(`contact`) >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(`contact`) <= '$date_to' ";
        }
        if (!empty($user)) {
            $where .= " and history.user_id = '$user' ";
        }
        if (!empty($team)) {
            $where .= " and history.team_id = '$team' ";
        }
		 if (!empty($campaign)) {
            $where .= " and campaign_id = '$campaign' ";
        }
        if (!empty($outcome)) {
            $where .= " and history.outcome_id = '$outcome' ";
        }

        //if the user does not have the agent reporting permission they can only see their own stats
        if (@!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and history.user_id = '{$_SESSION['user_id']}' ";
        }

        $where .= " and history.campaign_id in({$_SESSION['campaign_access']['list']}) ";
		$qry = "select count(*) count,user_id,users.name from history join campaigns using(campaign_id) join role_permissions using(role_id) join permissions using(permission_id) join users using(user_id) where permission_name = 'log hours' $where group by user_id";
		$this->firephp->log($qry);
		return $this->db->query($qry)->result_array();
	
}
public function get_realtime_hours($options){
		$date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $user = isset($options['agent']) ? $options['agent'] : "";
        $team = isset($options['team']) ? $options['team'] : "";
        $outcome = isset($options['outcome']) ? $options['outcome'] : "";
$campaign = isset($options['campaign']) ? $options['campaign'] : "";
        $where = "";

        if (!empty($date_from)) {
            $where .= " and date(`date`) >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(`date`) <= '$date_to' ";
        }
        if (!empty($user)) {
            $where .= " and hours.user_id = '$user' ";
        }
        if (!empty($team)) {
            $where .= " and history.team_id = '$team' ";
        }
		 if (!empty($campaign)) {
            $where .= " and hours.campaign_id = '$campaign' ";
        }

        //if the user does not have the agent reporting permission they can only see their own stats
        if (@!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and hours.user_id = '{$_SESSION['user_id']}' ";
        }

        $where .= " and hours.campaign_id in({$_SESSION['campaign_access']['list']}) ";
		$qry = "select SUM(hours.time_logged) as duration,user_id,users.name from hours join campaigns using(campaign_id) join users using(user_id) join role_permissions using(role_id) join permissions using(permission_id)  where permission_name = 'log hours' $where group by user_id";
		$this->firephp->log($qry);
		return $this->db->query($qry)->result_array();
	
}
public function get_realtime_hours_logged($options){
		$date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $user = isset($options['agent']) ? $options['agent'] : "";
        $team = isset($options['team']) ? $options['team'] : "";
        $outcome = isset($options['outcome']) ? $options['outcome'] : "";
$campaign = isset($options['campaign']) ? $options['campaign'] : "";
        $where = " and user_status = 1";

        if (!empty($date_from)) {
            $where .= " and date(`start_time`)= date('$date_from') ";
        }
        if (!empty($date_to)) {
            //$where .= " and date(`contact`) <= '$date_to' ";
        }
        if (!empty($user)) {
            $where .= " and user_id = '$user' ";
        }
        if (!empty($team)) {
            $where .= " and history.team_id = '$team' ";
        }
		 if (!empty($campaign)) {
            $where .= " and campaign_id = '$campaign' ";
        }

        //if the user does not have the agent reporting permission they can only see their own stats
        if (@!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and hours_logged.user_id = '{$_SESSION['user_id']}' ";
        }

        $where .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
		$qry = "select sum(TIME_TO_SEC(TIMEDIFF(if(end_time is null,now(),end_time),start_time))) duration,user_id,users.name from hours_logged join campaigns using(campaign_id) join users using(user_id) join role_permissions using(role_id) join permissions using(permission_id) where permission_name = 'log hours' $where group by user_id";
		$this->firephp->log($qry);
		return $this->db->query($qry)->result_array();
	
}



    /**
     * Get the data for the productivity report
     *
     * @param $options
     * @return array
     */
    public function get_productivity($options)
    {
        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $users = isset($options['agents']) ? $options['agents'] : array();
        $teams = isset($options['teams']) ? $options['teams'] : array();
        $sources = isset($options['sources']) ? $options['sources'] : array();
        $outcomes = isset($options['outcomes']) ? $options['outcomes'] : array();
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();
        $hours_where = "";
        $hours = "hr.`user_id` = users.user_id ";
        $exceptions = "t.`user_id` = users.user_id ";
        $exceptions_where = "";

        $where = "";
        $where_calls = "";
        if (!empty($date_from)) {
            $where .= " and date(contact) >= '$date_from' ";
            $where_calls .= " and date(call_log.call_date) >= '$date_from' ";
            $hours_where .= " and hr.date >= '$date_from' ";
            $exceptions_where .= " and t.date >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(contact) <= '$date_to' ";
            $where_calls .= " and date(call_log.call_date) <= '$date_to' ";
            $hours_where .= " and hr.date <= '$date_to' ";
            $exceptions_where .= " and t.date <= '$date_to' ";
        }
        if (!empty($users)) {
            $where .= " and history.user_id IN (" . implode(",", $users) . ") ";
            $hours_where .= " and hr.user_id IN (" . implode(",", $users) . ") ";
            $exceptions_where .= " and t.user_id IN (" . implode(",", $users) . ") ";
        }
        if (!empty($teams)) {
            $where .= " and teams.team_id IN (" . implode(",", $teams) . ") ";
        }

        if (!empty($sources)) {
            $where .= " and sources.source_id IN (" . implode(",", $sources) . ") ";
        }

        if (!empty($outcomes)) {
            $where .= " and history.outcome_id IN (" . implode(",", $outcomes) . ") ";
        }

        if (!empty($campaigns)) {
            $where .= " and history.campaign_id IN (" . implode(",", $campaigns) . ") ";
            $hours_where .= " and hr.campaign_id IN (" . implode(",", $campaigns) . ") ";
        }


        //if the user does not have the agent reporting permission they can only see their own stats
        if (@!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and history.user_id = '{$_SESSION['user_id']}' ";
        }

        $where .= " and history.campaign_id in({$_SESSION['campaign_access']['list']}) ";

        $qry = "select
                      count(*) count,
                      IF(duration,duration,0) as duration,
                      IF(ring_time,ring_time,0) as ring_time,
                      users.ext, users.name as agent,
                      users.user_id as agent_id,
                      (select sum(hr.duration) from hours hr where $hours $hours_where) as minutes,
                      (select IF(sum(te.duration),sum(te.duration),0) from time_exception te inner join time t using (time_id) where $exceptions $exceptions_where) as exceptions
                from users
				  left join history using(user_id)
                  join records using(urn)
                  join data_sources sources on records.source_id = sources.source_id
                  join teams on users.team_id = teams.team_id
                  left join (
                    select  SUM(TIME_TO_SEC(call_log.duration)) as duration, SUM(call_log.ring_time) as ring_time, users.ext as extension
                      FROM call_log
                        inner JOIN users on users.ext = call_log.call_from
                        where call_log.inbound = 0 " . $where_calls . "
                      GROUP BY users.user_id
                    ) calls on (users.ext = extension)";

        $qry .= " where user_status = 1 " . $where;

        $qry .= " GROUP BY users.user_id
                  ORDER BY users.user_id";

        return $this->db->query($qry)->result_array();
    }

    /**
     * Get the data for the last outcomes report
     *
     * @param $options
     * @return array
     */
    public function get_last_outcomes($options)
    {
        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $sources = isset($options['sources']) ? $options['sources'] : array();
        $outcomes = isset($options['outcomes']) ? $options['outcomes'] : array();
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();

        $where = "";
        if (!empty($date_from)) {
            $where .= " and (date(r.date_updated) >= '".$date_from."' or (r.date_updated is null and date(r.date_added) >=  '".$date_from."')) ";
        }
        if (!empty($date_to)) {
            $where .= " and (date(r.date_updated) <= '".$date_to."' or (r.date_updated is null and date(r.date_added) <=  '".$date_to."')) ";
        }

        if (!empty($sources)) {
            $where .= " and sources.source_id IN (" . implode(",", $sources) . ") ";
        }

        if (!empty($outcomes)) {
            $where .= " and r.outcome_id IN (" . implode(",", $outcomes) . ") ";
        }

        if (!empty($campaigns)) {
            $where .= " and r.campaign_id IN (" . implode(",", $campaigns) . ") ";
        }

        $where .= " and r.campaign_id in({$_SESSION['campaign_access']['list']}) ";

        $qry = "SELECT
                  if (o.outcome is not NULL, o.outcome, '- No Outcome Set -') as outcome,
                  r.outcome_id,
                  count(*) as num,
                  IF (s.status_name = 'Completed' OR s.status_name = 'Dead','completed','in_progress') as status
                FROM records r
                  LEFT JOIN outcomes o USING (outcome_id)
                  LEFT JOIN status_list s ON (s.record_status_id = r.record_status)
                  LEFT JOIN data_sources using (source_id)";

        $qry .= " where 1 " . $where;

        $qry .= " GROUP BY status, o.outcome_id
                  ORDER BY num desc";

        return $this->db->query($qry)->result_array();
    }

    /**
     * Get total records for the last outcomes report
     *
     * @param $options
     * @return array
     */
    public function get_total_records($options)
    {
        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $sources = isset($options['sources']) ? $options['sources'] : array();
        $outcomes = isset($options['outcomes']) ? $options['outcomes'] : array();
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();

        $where = "";
        if (!empty($date_from)) {
            $where .= " and (date(r.date_updated) >= '".$date_from."' or (r.date_updated is null and date(r.date_added) >=  '".$date_from."')) ";
        }
        if (!empty($date_to)) {
            $where .= " and (date(r.date_updated) <= '".$date_to."' or (r.date_updated is null and date(r.date_added) <=  '".$date_to."')) ";
        }

        if (!empty($sources)) {
            $where .= " and sources.source_id IN (" . implode(",", $sources) . ") ";
        }

        if (!empty($outcomes)) {
            $where .= " and r.outcome_id IN (" . implode(",", $outcomes) . ") ";
        }

        if (!empty($campaigns)) {
            $where .= " and r.campaign_id IN (" . implode(",", $campaigns) . ") ";
        }


        //if the user does not have the agent reporting permission they can only see their own stats
        if (@!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and r.user_id = '{$_SESSION['user_id']}' ";
        }

        $where .= " and r.campaign_id in({$_SESSION['campaign_access']['list']}) ";

        $qry = "SELECT
                  count(*) as num,
                  IF (r.dials > 0, 'Called Records', 'Virgin Records') as num_dials
                FROM records r
                  LEFT JOIN outcomes o USING (outcome_id)
                  LEFT JOIN status_list s ON (s.record_status_id = r.record_status)
                  LEFT JOIN data_sources using (source_id)";

        $qry .= " where 1 " . $where;

        $qry .= " GROUP BY num_dials";

        return $this->db->query($qry)->result_array();
    }

    /**
     * Get the data for the get_dials_by_outcome report
     *
     * @param $options
     * @return array
     */
    public function get_dials_by_outcome($options)
    {
        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $sources = isset($options['sources']) ? $options['sources'] : array();
        $outcomes = isset($options['outcomes']) ? $options['outcomes'] : array();
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();

        $where = "";
        if (!empty($date_from)) {
            $where .= " and (date(r.date_updated) >= '".$date_from."' or (r.date_updated is null and date(r.date_added) >=  '".$date_from."')) ";
        }
        if (!empty($date_to)) {
            $where .= " and (date(r.date_updated) <= '".$date_to."' or (r.date_updated is null and date(r.date_added) <=  '".$date_to."')) ";
        }

        if (!empty($sources)) {
            $where .= " and sources.source_id IN (" . implode(",", $sources) . ") ";
        }

        if (!empty($outcomes)) {
            $where .= " and r.outcome_id IN (" . implode(",", $outcomes) . ") ";
        }

        if (!empty($campaigns)) {
            $where .= " and r.campaign_id IN (" . implode(",", $campaigns) . ") ";
        }

        $where .= " and r.campaign_id in({$_SESSION['campaign_access']['list']}) ";

        $qry = "SELECT
                  IF (o.outcome is not NULL, o.outcome, '- No Outcome Set -') as outcome,
                  r.outcome_id,
                  IF (o.contact_made, 'contact', 'no_contact') as contact,
                  count(r.dials) as num
                FROM records r
                  LEFT JOIN outcomes o USING (outcome_id)
                  LEFT JOIN status_list s ON (s.record_status_id = r.record_status)
                  LEFT JOIN data_sources using (source_id)";

        $qry .= " where 1 " . $where;

        $qry .= " GROUP BY contact, o.outcome_id
                  ORDER BY num desc";

        return $this->db->query($qry)->result_array();
    }

    /**
     * Get total dials for the last outcomes report
     *
     * @param $options
     * @return array
     */
    public function get_total_dials($options)
    {
        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $sources = isset($options['sources']) ? $options['sources'] : array();
        $outcomes = isset($options['outcomes']) ? $options['outcomes'] : array();
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();

        $where = "";
        if (!empty($date_from)) {
            $where .= " and (date(r.date_updated) >= '".$date_from."' or (r.date_updated is null and date(r.date_added) >=  '".$date_from."')) ";
        }
        if (!empty($date_to)) {
            $where .= " and (date(r.date_updated) <= '".$date_to."' or (r.date_updated is null and date(r.date_added) <=  '".$date_to."')) ";
        }

        if (!empty($sources)) {
            $where .= " and sources.source_id IN (" . implode(",", $sources) . ") ";
        }

        if (!empty($outcomes)) {
            $where .= " and r.outcome_id IN (" . implode(",", $outcomes) . ") ";
        }

        if (!empty($campaigns)) {
            $where .= " and r.campaign_id IN (" . implode(",", $campaigns) . ") ";
        }

        $where .= " and r.campaign_id in({$_SESSION['campaign_access']['list']}) ";

        $qry = "SELECT
                  count(r.dials) as num,
                  IF (o.contact_made, 'Total Contact', 'Total No Contact') as contact
                FROM records r
                  LEFT JOIN outcomes o USING (outcome_id)
                  LEFT JOIN status_list s ON (s.record_status_id = r.record_status)
                  LEFT JOIN data_sources using (source_id)";

        $qry .= " where 1 " . $where;

        $qry .= " GROUP BY contact";

        return $this->db->query($qry)->result_array();
    }

    /**
     * Get sms data
     */
    public function get_sms_data($options)
    {
        if ($options['group'] == "agent") {
            $group_by = "sh.user_id";
            $id = "sh.user_id `sql`, sh.user_id";
            $name = "u.name";
        } else if ($options['group'] == "date") {
            $group_by = "date(sh.sent_date)";
            $id = "date(sh.sent_date) `sql`,date_format(sh.sent_date,'%d/%m/%y')";
            $name = "'All'";
        } else if ($options['group'] == "time") {
            $group_by = "hour(sh.sent_date)";
            $id = "hour(sh.sent_date) `sql`,date_format(sh.sent_date,'%H:00:00')";
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
            $where .= " and date(sh.sent_date) >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(sh.sent_date) <= '$date_to' ";
        }
        if (!empty($template)) {
            $where .= " and sh.template_id = '$template' ";
        }
        if (!empty($campaign)) {
            $where .= " and c.campaign_id = '$campaign' ";
        }
        if (!empty($team_manager)) {
            $where .= " and u.team_id = '$team_manager' ";
        }
        if (!empty($agent)) {
            $where .= " and sh.user_id = '$agent' ";
            $name = "u.name";
        }
        if (!empty($source)) {
            $where .= " and r.source_id = '$source' ";
        }

        //if the user does not have the agent reporting permission they can only see their own stats
        if (@!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and sh.user_id = '{$_SESSION['user_id']}' ";
        }

        //if the user does not have the group reporting permission they can only see their own stats
        if (@!in_array("by group", $_SESSION['permissions'])) {
            $where .= " and u.group_id = '{$_SESSION['group']}' ";
        }

        //if the user does not have the group reporting permission they can only see their own stats
        if (@!in_array("by team", $_SESSION['permissions'])) {
            $where .= " and u.team_id = '{$_SESSION['team']}' ";
        }
        $where .= " and r.campaign_id in({$_SESSION['campaign_access']['list']})";
        $joins = "
          inner join records r ON (r.urn = sh.urn)
          inner join campaigns c ON (c.campaign_id = r.campaign_id)
          left join users u ON (u.user_id = sh.user_id)";

        $qry = "select $id id,
                $name name,
                count(*) as sms_sent_count,
                credits,
                if(sms_delivered_count is null,0,sms_delivered_count) sms_delivered_count,
				if(sms_pending_count is null,0,sms_pending_count) sms_pending_count,
				if(sms_undelivered_count is null,0,sms_undelivered_count) sms_undelivered_count,
				if(sms_unknown_count is null,0,sms_unknown_count) sms_unknown_count,
				if(sms_error_count is null,0,sms_error_count) sms_error_count
        from sms_history sh
          left join users using(user_id)
          $joins
          left join (select count(*) sms_delivered_count,$group_by gb from sms_history sh $joins where sh.status_id = " . SMS_STATUS_SENT . " $where group by $group_by) ssc on ssc.gb = $group_by
          left join (select count(*) sms_pending_count,$group_by gb from sms_history sh $joins where sh.status_id = " . SMS_STATUS_PENDING . " $where group by $group_by) spc on spc.gb = $group_by
          left join (select count(*) sms_undelivered_count,$group_by gb from sms_history sh $joins where sh.status_id = " . SMS_STATUS_UNDELIVERED . " $where group by $group_by) suc on suc.gb = $group_by
          left join (select count(*) sms_unknown_count,$group_by gb from sms_history sh $joins where sh.status_id = " . SMS_STATUS_UNKNOWN . " $where group by $group_by) sunc on sunc.gb = $group_by
          left join (select count(*) sms_error_count,$group_by gb from sms_history sh $joins where sh.status_id = " . SMS_STATUS_ERROR . " $where group by $group_by) sec on sec.gb = $group_by
          left join (select SUM(IF(LENGTH(sh.text)<=160,1,IF(LENGTH(sh.text)<=306,2,IF(LENGTH(sh.text)<=459,3,IF(LENGTH(sh.text)<=612,4,5))))) as credits,$group_by gb from sms_history sh $joins where sh.status_id != " . SMS_STATUS_PENDING . " $where group by $group_by) cred on cred.gb = $group_by
        where 1 $where
		group by $group_by ";
        $this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }
	
	  public function get_data_counts($options)
    {
        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();
        $outcomes = isset($options['outcomes']) ? $options['outcomes'] : array();
        $users = isset($options['agents']) ? $options['agents'] : array();
        $teams = isset($options['teams']) ? $options['teams'] : array();
        $sources = isset($options['sources']) ? $options['sources'] : array();

        $where = "where 1 ";
        if (!empty($date_from)) {
            $where .= " and date(date_updated) >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(date_updated) <= '$date_to' ";
        }
        if (!empty($campaigns)) {
            $where .= " and records.campaign_id IN (".implode(",",$campaigns).") ";
        }
        if (!empty($outcomes)) {
            $where .= " and records.outcome_id IN (".implode(",",$outcomes).") ";
        }
        if (!empty($users)) {
            $where .= " and records.user_id IN (".implode(",",$users).") ";
        }
        if (!empty($teams)) {
            $where .= " and teams.team_id IN (".implode(",",$teams).") ";
        }
        if (!empty($sources)) {
            $where .= " and records.source_id IN (".implode(",",$sources).") ";
        }
        //if the user does not have the agent reporting permission they can only see their own stats
        if (@!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and ownership.user_id = '{$_SESSION['user_id']}' ";
        }

        //if the user does not have the group reporting permission they can only see their own stats
        if (@!in_array("by group", $_SESSION['permissions'])) {
            //$where .= " and history.group_id = '{$_SESSION['group']}' ";
        }

        //if the user does not have the group reporting permission they can only see their own stats
        if (@!in_array("by team", $_SESSION['permissions'])) {
            //this doesnt work because some people dont have a team such as clients
            //$where .= " and history.team_id = '{$_SESSION['team']}' ";
        }

        $qry = "select campaign_status,campaign_name name,campaign_id id,tr,ta,tp,va,vp,wa,wp,fd,fc from 
		(select campaign_id,count(distinct urn) tr from records left join ownership using(urn) left join users using(user_id) $where and campaign_id in({$_SESSION['campaign_access']['list']}) group by campaign_id) total_r join
		(select campaign_id,count(distinct urn) ta from records left join ownership using(urn) left join users using(user_id) $where and record_status = 1 and parked_code is null group by campaign_id) total_a using(campaign_id) left join
		(select campaign_id,count(distinct urn) tp from records left join ownership using(urn) left join users using(user_id) $where and record_status = 1 and parked_code is not null group by campaign_id) total_p using(campaign_id) left join
		(select campaign_id,count(distinct urn) va from records left join ownership using(urn) left join users using(user_id) $where and (dials = 0) and record_status = 1 and parked_code is null group by campaign_id) virgin_a using(campaign_id) left join
		(select campaign_id,count(distinct urn) vp from records left join ownership using(urn) left join users using(user_id) $where and (dials = 0) and record_status = 1 and parked_code is not null group by campaign_id) virgin_p using(campaign_id) left join
		(select campaign_id,count(distinct urn) wa from records left join ownership using(urn) left join users using(user_id) $where and (dials > 0) and record_status = 1 and parked_code is null group by campaign_id) working_a using(campaign_id) left join
		(select campaign_id,count(distinct urn) wp from records left join ownership using(urn) left join users using(user_id) $where and (dials > 0) and record_status = 1 and parked_code is not null group by campaign_id) working_p using(campaign_id) left join
		(select campaign_id,count(distinct urn) fd from records left join ownership using(urn) left join users using(user_id) $where and record_status = 3 group by campaign_id) finished_d using(campaign_id) left join
		(select campaign_id,count(distinct urn) fc from records left join ownership using(urn) left join users using(user_id) $where and record_status = 4 group by campaign_id) finished_c using(campaign_id) left join campaigns using(campaign_id) order by campaign_status desc,campaign_name";
        return $this->db->query($qry)->result_array();
    }
	
	 public function get_data_counts_by_pot($options)
    {
        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();
        $outcomes = isset($options['outcomes']) ? $options['outcomes'] : array();
        $users = isset($options['agents']) ? $options['agents'] : array();
        $teams = isset($options['teams']) ? $options['teams'] : array();
        $sources = isset($options['sources']) ? $options['sources'] : array();
		$campaign = isset($options['campaign']) ? $options['campaign'] : array();
        $where = "where 1 ";
        if (!empty($date_from)) {
            $where .= " and date(date_updated) >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $where .= " and date(date_updated) <= '$date_to' ";
        }
        if (!empty($campaigns)) {
            $where .= " and campaign_id IN (".implode(",",$campaigns).") ";
        }
		if (!empty($campaign)) {
            $where .= " and campaign_id =  '".$campaign ."'";
        }
        if (!empty($outcomes)) {
            $where .= " and outcome_id IN (".implode(",",$outcomes).") ";
        }
        if (!empty($users)) {
            $where .= " and users.user_id IN (".implode(",",$users).") ";
        }
        if (!empty($teams)) {
            $where .= " and users.team_id IN (".implode(",",$teams).") ";
        }
        if (!empty($sources)) {
            $where .= " and source_id IN (".implode(",",$sources).") ";
        }
        //if the user does not have the agent reporting permission they can only see their own stats
        if (@!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and users.user_id = '{$_SESSION['user_id']}' ";
        }

        //if the user does not have the group reporting permission they can only see their own stats
        if (@!in_array("by group", $_SESSION['permissions'])) {
            //$where .= " and history.group_id = '{$_SESSION['group']}' ";
        }

        //if the user does not have the group reporting permission they can only see their own stats
        if (@!in_array("by team", $_SESSION['permissions'])) {
            //this doesnt work because some people dont have a team such as clients
            //$where .= " and history.team_id = '{$_SESSION['team']}' ";
        }

        $qry = "select campaign_status,campaign_name,campaigns.campaign_id,pot_id id,pot_name name,tr,ta,tp,va,vp,wa,wp,fd,fc from 
		(select pot_id,campaign_id,count(distinct urn) tr from records left join ownership using(urn) left join users using(user_id) $where and campaign_id in({$_SESSION['campaign_access']['list']}) group by pot_id) total_r join
		(select pot_id,campaign_id,count(distinct urn) ta from records left join ownership using(urn) left join users using(user_id) $where and record_status = 1 and parked_code is null group by pot_id) total_a using(pot_id) left join
		(select pot_id,campaign_id,count(distinct urn) tp from records left join ownership using(urn) left join users using(user_id) $where and record_status = 1 and parked_code is not null group by pot_id) total_p using(pot_id) left join
		(select pot_id,campaign_id,count(distinct urn) va from records left join ownership using(urn) left join users using(user_id) $where and (dials = 0) and record_status = 1 and parked_code is null group by pot_id) virgin_a using(pot_id) left join
		(select pot_id,campaign_id,count(distinct urn) vp from records left join ownership using(urn) left join users using(user_id) $where and (dials = 0) and record_status = 1 and parked_code is not null group by pot_id) virgin_p using(pot_id) left join
		(select pot_id,campaign_id,count(distinct urn) wa from records left join ownership using(urn) left join users using(user_id) $where and (dials > 0) and record_status = 1 and parked_code is null group by pot_id) working_a using(pot_id) left join
		(select pot_id,campaign_id,count(distinct urn) wp from records left join ownership using(urn) left join users using(user_id) $where and (dials > 0) and record_status = 1 and parked_code is not null group by pot_id) working_p using(pot_id) left join
		(select pot_id,campaign_id,count(distinct urn) fd from records left join ownership using(urn) left join users using(user_id) $where and record_status = 3 group by pot_id) finished_d using(pot_id) left join
		(select pot_id,campaign_id,count(distinct urn) fc from records left join ownership using(urn) left join users using(user_id) $where and record_status = 4 group by pot_id) finished_c using(pot_id) left join campaigns on campaigns.campaign_id = total_r.campaign_id left join data_pots using(pot_id) order by campaign_status desc,pot_name";
        return $this->db->query($qry)->result_array();
    }
}