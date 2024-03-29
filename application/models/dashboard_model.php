<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

	public function pending_tasks(){
		$qry = "select urn,task_name,companies.name company, date_format(`timestamp`,'%d/%m/%y %H:%i') `date`, users.name, task_status, campaign_name from task_history join (select max(task_history_id) maxid from task_history group by concat(task_id,'-',urn)) maxtasks on maxtasks.maxid = task_history.task_history_id join records using(urn) join campaigns using(campaign_id) join users using(user_id) join task_status_options using(task_status_id) join tasks using(task_id) join companies using(urn) where task_status_id > 1 and record_status = 1 ";
		return $this->db->query($qry)->result_array();	
		
	}
	
    public function overdue_visits($filter = "")
    {
        $qry = "select urn,users.name owner, c1 as category,campaign_name as type,companies.name,records.date_updated,outcome from records inner join companies using(urn) inner join record_details using(urn) left join ownership using(urn) left join users using(user_id) inner join campaigns using(campaign_id) inner join outcomes using(outcome_id) where curdate()>date(nextcall) and record_status = 1 ";
        if (!empty($filter['campaign'])) {
            $qry .= " and campaign_id = '" . $filter['campaign'] . "'";
        }
        if (!empty($filter['agent'])) {
            $qry .= " and user_id = '" . $filter['agent'] . "'";
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $qry .= " and user_id = '" . $_SESSION['user_id'] . "'";
        }
        $qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= "order by records.date_updated asc";
        return $this->db->query($qry)->result_array();
    }

    public function get_urgent($filter = "")
    {
        $qry = "select urn,fullname,date_format(records.date_updated,'%d/%m/%y %H:%i') date_updated from records left join contacts using(urn) where urgent = 1 ";
        if (!empty($filter['campaign'])) {
            $qry .= " and campaign_id = '" . $filter['campaign'] . "'";
        }
        $qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by urn order by records.date_updated asc";
        return $this->db->query($qry)->result_array();
    }

    public function get_appointments($filter = "")
    {
        $qry = "select urn,if(companies.name is null,fullname,name) as fullname,date_format(`start`,'%d/%m/%y %H:%i') start_date from records left join appointments using(urn) left join appointment_attendees using(appointment_id) left join contacts using(urn) left join companies using(urn) where appointments.start > subdate(now(),interval 3 day) and user_id = '{$_SESSION['user_id']}' ";
        if (!empty($filter['campaign'])) {
            $qry .= " and campaign_id = '" . $filter['campaign'] . "'";
        }
        $qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by appointment_id order by `start`";
        return $this->db->query($qry)->result_array();
    }

    public function get_pending($filter = "")
    {
        $qry = "select urn,if(companies.name is null,fullname,name) as fullname,date_format(records.date_updated,'%d/%m/%y %H:%i') date_updated from records left join contacts using(urn) left join companies using(urn) where record_status = 2 ";
        $qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by urn order by records.date_updated asc";
        return $this->db->query($qry)->result_array();
    }

    public function get_favorites($filter = array())
    {
        $qry = "select urn,if(companies.name is null,fullname,name) as fullname,campaign_name,if(records.date_updated is null,'-',date_format(records.date_updated,'%d/%m/%y %H:%i')) date_updated,if(records.nextcall is null,'-',date_format(records.nextcall,'%d/%m/%y %H:%i')) nextcall,comments,if(outcome is null,'-',outcome) outcome from favorites left join records using(urn) left join outcomes using(outcome_id) left join (select urn,max(history_id) mhid from history group by urn) mh using(urn) left join (select comments,history_id from history where comments <> '') h on h.history_id = mhid left join campaigns using(campaign_id) left join companies using(urn) left join contacts using(urn) where 1 ";

        if (!empty($filter['campaigns'])) {
            $qry .= " and campaign_id IN (".implode(",",$filter['campaigns']).")";
        }
        if (!empty($filter['teams'])) {
            $qry .= " and team_id IN (".implode(",",$filter['teams']).")";
        }
        if (!empty($filter['user'])) {
            $qry .= " and user_id IN (".implode(",",$filter['user']).")";
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $qry .= " and user_id = '" . $_SESSION['user_id'] . "'";
        }

        $qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= "  group by urn order by records.date_updated asc";
        return $this->db->query($qry)->result_array();
    }

    public function get_history($filter = array())
    {
        $qry = "select contact, u.name, campaign_name, if(h.outcome_id is null,if(pd.description is null,'No Action Required',pd.description),outcome) as outcome, history_id,h.urn,if(com.name is null,fullname,com.name) cname from history h left join outcomes using(outcome_id) left join progress_description pd using(progress_id) left join campaigns using(campaign_id) left join users u using(user_id) left join contacts on contacts.urn = h.urn and contacts.`primary`=1 left join companies com on com.urn = h.urn where 1 ";
        //if a filter is selected then we just return history from that campaign, otehrwize get all the history
        if (!empty($filter['campaigns'])) {
            $qry .= " and h.campaign_id IN (".implode(",",$filter['campaigns']).")";
        }
        if (!empty($filter['outcomes'])) {
            $qry .= " and h.outcome_id IN (".implode(",",$filter['outcomes']).")";
        }
        if (!empty($filter['teams'])) {
            $qry .= " and h.team_id IN (".implode(",",$filter['teams']).")";
        }
        if (!empty($filter['user'])) {
            $qry .= " and h.user_id IN (".implode(",",$filter['user']).")";
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $qry .= " and h.user_id = '" . $_SESSION['user_id'] . "'";
        }

        $qry .= " and h.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by history_id order by history_id desc limit 10";
        return $this->db->query($qry)->result_array();
    }

    public function get_outcomes($filter = array())
    {
        $qry = "select outcome,count(*) count from history h left join outcomes using(outcome_id) left join records using(urn) where 1 and h.outcome_id is not null and date(contact) = curdate() ";
        if (!empty($filter['campaigns'])) {
            $qry .= " and h.campaign_id IN (".implode(",",$filter['campaigns']).")";
        }
        if (!empty($filter['teams'])) {
            $qry .= " and h.team_id IN (".implode(",",$filter['teams']).")";
        }
        if (!empty($filter['user'])) {
            $qry .= " and h.user_id IN (".implode(",",$filter['user']).")";
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $qry .= " and h.user_id = '" . $_SESSION['user_id'] . "'";
        }
        $qry .= " and h.campaign_id in({$_SESSION['campaign_access']['list']}) and h.role_id is not null ";
        $qry .= " group by h.outcome_id order by count desc ";
        //$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();

    }


    public function system_stats($filter = array())
    {
        $extra = " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $extra_url = "";
        if (!empty($filter['campaigns'])) {
            $extra = " and campaign_id IN (" . implode(",",$filter['campaigns']) . ")";
            $extra_url = "/campaign/".implode("_",$filter['campaigns']).(count($filter['campaigns'])>1?":in":"");
        }
        //data stats
        $virgin_qry = "select count(*) data from records where outcome_id is null and nextcall is null and record_status = 1 and progress_id is null $extra  ";
        $data['virgin'] = $this->db->query($virgin_qry)->row()->data;
        $data['virgin_url'] = base_url() . "search/custom/records/nextcall/null/outcome/null/status/live" . $extra_url;
        $active_qry = "select count(*) data from records where record_status = 1 and outcome_id is not null and progress_id is null $extra ";
        //$this->firephp->log($active_qry);
        $data['active'] = $this->db->query($active_qry)->row()->data;
        $data['active_url'] = base_url() . "search/custom/records/progress/null/outcome/null:not/status/live" . $extra_url;
        $dead_qry = "select count(*) data from records where record_status = 3 $extra";
        $data['dead'] = $this->db->query($dead_qry)->row()->data;
        $data['dead_url'] = base_url() . "search/custom/records/status/dead" . $extra_url;
        $parked_qry = "select count(*) data from records where parked_code is not null $extra";
        $data['parked'] = $this->db->query($parked_qry)->row()->data;
        $data['parked_url'] = base_url() . "search/custom/records/parked/yes" . $extra_url;
        $pending_qry = "select count(*) data from records where progress_id = 1 and record_status=1 $extra ";
        $data['pending'] = $this->db->query($pending_qry)->row()->data;
        $data['pending_url'] = base_url() . "search/custom/records/progress/1/status/live" . $extra_url;

        $in_progress_qry = "select count(*) data from records where progress_id = 2  and record_status=1 $extra";
        $data['in_progress'] = $this->db->query($in_progress_qry)->row()->data;
        $data['in_progress_url'] = base_url() . "search/custom/records/progress/in progress/status/live" . $extra_url;
        $completed_qry = "select count(*) data from records where progress_id = 3 and record_status=1 $extra ";
        $data['completed'] = $this->db->query($completed_qry)->row()->data;
        $data['completed_url'] = base_url() . "search/custom/records/progress/complete/status/liv" . $extra_url;
        //survey stats
        $surveys_qry = "select count(distinct urn) data from surveys left join records using(urn) where 1 $extra";
        $data['surveys'] = $this->db->query($surveys_qry)->row()->data;

        $nps_failures = "select count(*) data from surveys left join survey_answers using(survey_id) left join questions using(question_id) left join records using(urn) where answer<6 and answer is not null and nps_question=1 $extra";
        $data['failures'] = $this->db->query($nps_failures)->row()->data;
        $nps_average = "select avg(answer) data from surveys left join survey_answers using(survey_id) left join questions using(question_id) left join records using(urn) where nps_question=1 $extra";
        $data['average'] = number_format($this->db->query($nps_average)->row()->data, 1);
        $nps_averages = "select survey_name, avg(answer) nps from surveys left join survey_info using(survey_info_id)left join  survey_answers using(survey_id) left join questions using(question_id) left join records using(urn) where nps_question=1  $extra group by surveys.survey_info_id";
        $result = $this->db->query($nps_averages)->result_array();
        foreach ($result as $row) {
            $data['averages'][$row['survey_name']] = $row['nps'];
        }
        //thinkmoney progress
        $manager_progress = "select count(*) count,description from records left join progress_description using(progress_id) where progress_id is not null group by progress_id";
        $result = $this->db->query($manager_progress)->result_array();
        foreach ($result as $row) {
            $data['progress'][$row['description']] = $row['count'];
        }

        $data['target'] = "50";
        if ($filter == 1) {
            $data['target'] = "50";
        }
        $data['target_pc'] = number_format(($data['surveys'] / $data['target']) * 100, 1);
        return $data;
    }

    public function get_comments($filter = array())
    {
        $extra = " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $survey_extra = "";
        $notes_extra = "";
        $comments_extra = "";


        if (!empty($filter['campaigns'])) {
            $survey_extra .= " and surveys.user_id IN (" . implode(",",$filter['campaigns']) . ") ";
            $notes_extra .= " and s.updated_by IN (" . implode(",",$filter['campaigns']) . ") ";
            $comments_extra .= " and history.campaign_id IN (" . implode(",",$filter['campaigns']) . ") ";
        }
        if (!empty($filter['teams'])) {
            $survey_extra .= " and surveys.user_id in(select user_id from users where team_id IN (" . implode(",",$filter['teams']) . ") ";
            $notes_extra .= " and s.updated_by in(select user_id from users where team_id IN (" . implode(",",$filter['teams']) . ") ";
            $comments_extra .= " and history.user_id in(select user_id from users where team_id IN (" . implode(",",$filter['teams']) . ") ";
        }
        if (!empty($filter['user'])) {
            $survey_extra .= " and surveys.user_id IN (" . implode(",",$filter['user']) . ") ";
            $notes_extra .= " and s.updated_by IN (" . implode(",",$filter['user']) . ") ";
            $comments_extra .= " and history.user_id IN (" . implode(",",$filter['user']) . ") ";
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $survey_extra .= " and surveys.user_id = '" . $_SESSION['user_id'] . "' ";
            $notes_extra .= " and s.updated_by = '" . $_SESSION['user_id'] . "' ";
            $comments_extra .= " and history.user_id = '" . $_SESSION['user_id'] . "' ";

        }

        $this->load->helper('date');
        if ($filter['comments'] == 1) {
            $qry = "select urn,an.notes,completed_date `date`,if(companies.name is null,fullname,name) as fullname from answer_notes an left join survey_answers using(answer_id) left join surveys  using(survey_id) left join records using(urn) left join contacts using(urn) left join companies using(urn) where char_length(an.notes) > 50 $extra $survey_extra group by contacts.contact_id order by completed_date desc limit 10";
        } else if ($filter['comments'] == 2) {
            $qry = "select urn,comments notes,contact `date`,if(companies.name is null,fullname,name) as fullname from history left join records using(urn) left join contacts using(urn) left join companies using(urn) where char_length(comments) > 40 $comments_extra $extra order by history_id desc  limit 10";
        } else if ($filter['comments'] == 3) {
            $qry = "select urn,note notes,s.date_updated `date`,if(companies.name is null,fullname,name) as fullname from sticky_notes s left join records using(urn) left join contacts using(urn) left join companies using(urn) where char_length(note) > 40 $extra $notes_extra order by s.date_updated desc  limit 10";
        } else {
            $qry = "select urn,notes,`date`,fullname from (select records.urn,an.notes,completed_date `date`,if(companies.name is null,fullname,name) as fullname from answer_notes an left join survey_answers using(answer_id) left join surveys using(survey_id) left join records using(urn)  left join companies using(urn) left join contacts on contacts.urn = records.urn and contacts.`primary`=1 where char_length(an.notes) > 40 $survey_extra $extra group by an.answer_id 
			union select history.urn,comments,contact,if(companies.name is null,fullname,name) as fullname from history left join records using(urn) left join companies using(urn)  left join contacts on contacts.urn = records.urn and contacts.`primary`=1 where char_length(comments) > 40 $extra $comments_extra group by history.history_id 
			union select records.urn,note,s.date_updated,if(companies.name is null,fullname,name) as fullname from sticky_notes s left join records using(urn) left join companies using(urn) left join contacts on contacts.urn = records.urn and contacts.`primary`=1 where char_length(note) > 40 $extra $notes_extra group by records.urn) t order by t.`date` desc limit 10";
        }
        $result = $this->db->query($qry)->result_array();
        $now = time();
        if (count($result)) {
            foreach ($result as $row) {
                $data[] = array(
                    "urn" => $row['urn'],
                    "comment" => $row['notes'],
                    "date" => timespan(strtotime($row['date']), $now) . " ago",
                    "name" => $row['fullname'],
                    "timestamp" => strtotime($row['date'])
                );
            }
            foreach ($data as $key => $node) {
                $timestamp[$key] = $node['timestamp'];
            }
            array_multisort($timestamp, SORT_DESC, $data);
        } else {
            $data[] = array(
                "urn" => "",
                "comment" => "Nothing was found",
                "date" => "",
                "name" => "-"
            );
        }
        return $data;
    }

    public function all_callbacks($filter)
    {
        $last_comments = "(select h.comments from history h where h.urn = records.urn and CHAR_LENGTH(h.comments) > 0 order by h.contact desc limit 1)";
        $qry = "select urn,outcome,if(companies.name is null,fullname,companies.name) as contact,nextcall,campaign_name as campaign,users.name, if($last_comments is not null,$last_comments,'') as last_comments from records left join companies using(urn) join outcomes using(outcome_id) left join ownership using(urn) left join campaigns using(campaign_id) left join contacts using(urn) left join users using(user_id) where outcome_id in(select outcome_id from outcomes where requires_callback = 1) ";
        $date_from = $filter['date_from'];
        $date_to = $filter['date_to'];
        if (!empty($date_from)) {
            $qry .= " and date(nextcall) >= date('$date_from') ";
        }
        if (!empty($date_to)) {
            $qry .= " and date(nextcall) <= date('$date_to') ";
        }
        if (!empty($filter['campaign'])) {
            $qry .= " and campaign_id = '{$filter['campaign']}'";
        }
        if (!empty($filter['team'])) {
            $qry .= " and users.team_id = '{$filter['team']}'";
        }
        if (!empty($filter['agent'])) {
            $qry .= " and ownership.user_id = '{$filter['agent']}'";
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $qry .= " and ownership.user_id = '" . $_SESSION['user_id'] . "'";
        }

        $qry .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";

        $qry .= " group by urn order by nextcall asc limit 50";
        return $this->db->query($qry)->result_array();
    }


    public function client_progress($filter)
    {
        $qry = "select urn,if(companies.name is null,fullname,companies.name) as name,nextcall,comments,campaign_name as campaign,pd.`description` as `status`,urgent from records left join (select urn,max(history_id) mhid from history group by urn) mh using(urn) left join (select comments,history_id from history where comments <> '') h on h.history_id = mhid left join ownership using(urn) left join campaigns using(campaign_id) left join companies using(urn) left join contacts using(urn) left join progress_description pd using(progress_id) where (progress_id in(1,2) and progress_id is not null and nextcall is not null or urgent=1 and nextcall is not null)";
        if (!empty($filter['campaign'])) {
            $qry .= " and campaign_id = " . intval($filter['campaign']);
        }
        if (!empty($filter['agent'])) {
            $qry .= " and user_id = " . intval($filter['agent']);
        }
        $qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by urn order by nextcall asc";
//$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }

    public function nbf_progress($filter)
    {
        $qry = "select urn,if(companies.name is null,fullname,companies.name) as name,nextcall,comments,campaign_name as campaign,outcome as `status`,urgent from records left join (select urn,max(history_id) mhid from history group by urn) mh using(urn) left join (select comments,history_id from history where comments <> '') h on h.history_id = mhid left join ownership using(urn) left join campaigns using(campaign_id) left join companies using(urn) left join contacts using(urn) left join outcomes using(outcome_id) where outcome_id in(85) and (nextcall is not null or urgent=1)";
        if (!empty($filter['campaign'])) {
            $qry .= " and campaign_id = " . intval($filter['campaign']);
        }
        if (!empty($filter['agent'])) {
            $qry .= " and user_id = " . intval($filter['agent']);
        }
        $qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by urn order by nextcall asc";
//$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }

    public function agent_activity($filter)
    {
        $qry_filter = "";
        if (!empty($filter['campaign'])) {
            $qry_filter .= " and campaigns.campaign_id = '{$filter['campaign']}'";
        }
        if (!empty($filter['team'])) {
            $qry_filter .= " and h.team_id = '{$filter['team']}'";
        }
        if (!empty($filter['agent'])) {
            $qry_filter .= " and h.user_id = '{$filter['agent']}'";
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $qry_filter .= " and h.user_id = '" . $_SESSION['user_id'] . "'";
        }


        $qry_filter .= " and campaigns.campaign_id in({$_SESSION['campaign_access']['list']}) and h.role_id is not null ";
        $qry = "select urn, max(contact) as `when`, contact as outcome_date,outcome as outcome,`name`,campaign_name as campaign from history h left join campaigns using(campaign_id) left join outcomes using(outcome_id) left join users u using(user_id) left join records using(urn)  left join (select user_id, max(contact) survey_date from history h left join campaigns using(campaign_id)  left join records using(urn) where h.outcome_id in(select outcome_id from outcomes where positive=1) $qry_filter group by user_id) ls on ls.user_id = h.user_id where u.team_id is not null and date(`contact`) = curdate() $qry_filter  group by u.user_id ";

        return $this->db->query($qry)->result_array();
    }

    public function agent_success($filter)
    {
        $qry_filter = "";
        $date_from = $filter['date_from'];
        $date_to = $filter['date_to'];
        if (!empty($date_from)) {
            $qry_filter .= " and date(contact) >= '$date_from' ";
        }
        if (!empty($date_to)) {
            $qry_filter .= " and date(contact) <= '$date_to' ";
        }
        if (!empty($filter['campaign'])) {
            $qry_filter .= " and h.campaign_id = '{$filter['campaign']}'";
            $campaign = "campaign_name";
        } else {
            $campaign = "'All'";
        }
        if (!empty($filter['team'])) {
            $qry_filter .= " and h.team_id = '{$filter['team']}'";
        }
        if (!empty($filter['agent'])) {
            $qry_filter .= " and h.user_id = '{$filter['agent']}'";
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $qry_filter .= " and h.user_id = '" . $_SESSION['user_id'] . "'";
        }


        $qry_filter .= " and campaigns.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry = "select `name`,count(*) dials,if(positive is null,'0',positive) positives,$campaign as campaign from history h left join campaigns using(campaign_id)  left join records using(urn) left join (select user_id,count(*) positive from history h left join campaigns using(campaign_id) left join records using(urn) where 1 and h.outcome_id in(select outcome_id from outcomes where positive=1) $qry_filter group by user_id) sv on sv.user_id = h.user_id  left join users on h.user_id = users.user_id where users.team_id is not null and users.user_status = 1 $qry_filter group by h.user_id";

        return $this->db->query($qry)->result_array();
    }

    public function agent_data($filter)
    {
        $qry_filter = "";
        if (!empty($filter['campaign'])) {
            $qry_filter .= " and campaign_id = '{$filter['campaign']}'";
            $campaign = "campaign_name";
        } else {
            $campaign = "'All'";
        }
        if (!empty($filter['team'])) {
            $qry_filter .= " and o.user_id in (select user_id from users where team_id='{$filter['team']}')";
        }
        if (!empty($filter['agent'])) {
            $qry_filter .= " and o.user_id = '{$filter['agent']}'";
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $qry_filter .= " and o.user_id = '" . $_SESSION['user_id'] . "'";
        }
        $qry_filter .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry = "select `name`,count(*) total,if(virgin is null,'0',virgin) as virgin,if(in_progress is null,'0',in_progress) as in_progress,if(completed is null,'0',completed) as completed,$campaign as campaign from records r  left join ownership o using(urn)  left join campaigns using(campaign_id) left join (select user_id,count(*) virgin from records left join ownership o using(urn) left join campaigns using(campaign_id) where outcome_id is null and record_status = 1 $qry_filter group by user_id) v on v.user_id = o.user_id  
	 left join (select user_id,count(*) in_progress from records  left join ownership o using(urn) left join campaigns using(campaign_id) where record_status = 1 and outcome_id is not null $qry_filter group by user_id) ip on ip.user_id = o.user_id  
	 left join (select user_id,count(*) completed from records  left join ownership o using(urn) left join campaigns using(campaign_id) where record_status in(3,4) $qry_filter group by user_id) c on c.user_id = o.user_id  
	left join users on o.user_id = users.user_id where record_status in(1,3,4) and users.team_id is not null  and users.user_status = 1 $qry_filter group by o.user_id";
        return $this->db->query($qry)->result_array();
    }

    public function agent_current_hours($campaign)
    {
        if (!empty($campaign)) {
            $qry_filter = " and campaigns.campaign_id = " . intval($campaign);
        } else {
            $qry_filter = "";
        }
        if (!empty($_SESSION['team_id'])) {
            $qry_filter = " and team_id = " . $_SESSION['team'];
        }
        $qry_filter .= " and campaigns.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry = "select `name`,count(*) dials,if(transfers is null,'0',transfers) transfers,campaign_name as campaign from history h left join campaigns using(campaign_id)  left join records using(urn) left join (select user_id,count(*) transfers from history left join campaigns using(campaign_id) left join records using(urn) where 1 and history.outcome_id in(70,71) $qry_filter group by user_id) sv on sv.user_id = h.user_id  left join users on h.user_id = users.user_id where users.team_id is not null $qry_filter group by h.user_id";
        return $this->db->query($qry)->result_array();
    }

    public function get_email_stats($filter = array())
    {
        $camp_url = "";
        $user_url = "";
        $where = " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        if (!empty($filter['user'])) {
            $where .= " and email_history.user_id IN (".implode(",",$filter['user']).")";
            $user_url = "/user/".implode("_",$filter['users']).(count($filter['user'])>1?":in":"");
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and email_history.user_id = '{$_SESSION['user_id']}'";
            $user_url .= "/user/" . $_SESSION['user_id'];
        }
        if (!empty($filter['campaigns'])) {
            $where .= " and records.campaign_id IN (".implode(",",$filter['campaigns']).")";
            $camp_url = "/campaign/".implode("_",$filter['campaigns']).(count($filter['campaigns'])>1?":in":"");
        }
        $qry_all = "select count(distinct urn) num from email_history left join records using(urn) where date(sent_date) = curdate() and `status` =1 $where";
        $all = $this->db->query($qry_all)->row()->num;
        $all_url = base_url() . 'search/custom/records/sent-email-from/' . date('Y-m-d') . '/emails/sent' . $camp_url . $user_url;

        $qry_read = "select count(distinct urn) num from email_history left join records using(urn) where date(read_confirmed_date) = curdate() and read_confirmed = 1 $where";
        $read = $this->db->query($qry_read)->row()->num;
        $read_url = base_url() . 'search/custom/records/read-date/' . date('Y-m-d') . '/emails/read' . $camp_url . $user_url;

        $qry_unsent = "select count(distinct urn) num from email_history left join records using(urn) where date(sent_date) = curdate() and `status` = 0 and pending = 0 $where";
        $unsent = $this->db->query($qry_unsent)->row()->num;
        $unsent_url = base_url() . 'search/custom/records/sent-email-from/' . date('Y-m-d') . '/emails/unsent' . $camp_url . $user_url;

        $qry_pending = "select count(distinct urn) num from email_history left join records using(urn) where date(sent_date) = curdate() and pending = 1 $where";
        $pending = $this->db->query($qry_pending)->row()->num;
        $pending_url = base_url() . 'search/custom/records/sent-email-from/' . date('Y-m-d') . '/emails/pending' . $camp_url . $user_url;

        $qry_new = "select count(distinct urn) num from email_history left join records using(urn) where date(read_confirmed_date) = curdate() and read_confirmed = 1 and read_confirmed_date > records.date_updated $where";
        $new = $this->db->query($qry_new)->row()->num;
        $new_url = base_url() . 'search/custom/records/read-date/' . date('Y-m-d') . '/emails/new' . $camp_url . $user_url;

        $qry_readall = "select count(distinct urn) num from email_history left join records using(urn) where 1 and read_confirmed = 1 $where";
        $readall = $this->db->query($qry_readall)->row()->num;
        $readall_url = base_url() . 'search/custom/records/emails/read' . $camp_url . $user_url;

        $qry_sentall = "select count(distinct urn) num from email_history left join records using(urn) where status = 1 $where";
        $sentall = $this->db->query($qry_sentall)->row()->num;
        $sentall_url = base_url() . 'search/custom/records/emails/sent' . $camp_url . $user_url;

        return array("new" => $new, "new_url" => $new_url, "all" => $all, "all_url" => $all_url, "read" => $read, "read_url" => $read_url, "unsent" => $unsent, "pending" => $pending, "pending_url" => $pending_url, "unsent_url" => $unsent_url, "readall" => $readall, "readall_url" => $readall_url, "sentall" => $sentall, "sentall_url" => $sentall_url);
    }

    public function get_sms_stats($filter = array())
    {
        $camp_url = "";
        $user_url = "";
        $where = " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";

        if (!empty($filter['user'])) {
            $where .= " and sms_history.user_id IN (".implode(",",$filter['user']).")";
            $user_url = "/user/".implode("_",$filter['user']).(count($filter['user'])>1?":in":"");
        }
        if (!in_array("by agent", $_SESSION['permissions'])) {
            $where .= " and sms_history.user_id = '{$_SESSION['user_id']}'";
            $user_url .= "/user/" . $_SESSION['user_id'];
        }
        if (!empty($filter['campaigns'])) {
            $where .= " and records.campaign_id IN (".implode(",",$filter['campaigns']).")";
            $camp_url = "/campaign/".implode("_",$filter['campaigns']).(count($filter['campaigns'])>1?":in":"");
        }

        $qry_today = "select sms_history.* from sms_history left join records using(urn) where date(sent_date) = curdate() $where";
        $today_sms = $this->db->query($qry_today)->result_array();
        $today_url = base_url() . 'search/custom/records/sent-sms-from/' . date('Y-m-d') . $camp_url . $user_url;

        $qry_all = "select sms_history.* from sms_history left join records using(urn) where 1 $where";
        $all_sms = $this->db->query($qry_all)->result_array();
        $all_url = base_url() . 'search/custom/records/sent-sms-from/' . '2014-01-01' . $camp_url . $user_url;


        return array("today" => $today_sms, "today_url" => $today_url, "all" => $all_sms, "all_url" => $all_url);
    }


    public function get_num_appointments_set($filter)
    {
        $where = " where 1 ";

        if (isset($filter['date_from'])) {
            $where .= " and DATE(a.`start`) >= '" . $filter['date_from'] . "' ";
        }
        if (isset($filter['date_to'])) {
            $where .= " and DATE(a.`start`) < '" . $filter['date_to'] . "' ";
        }

        $qry = "select
                    CONCAT(YEAR(a.`start`), '/', WEEK(a.`start`)) week,
                    count(*) num_appointments,
                    br.region_name,
                    br.region_id
                from appointments a
                    inner join branch b using (branch_id)
                    inner join branch_regions br using (region_id)
                " . $where . "
                group by CONCAT(YEAR(a.`start`), '/', WEEK(a.`start`)), br.region_id";

        return $this->db->query($qry)->result_array();
    }


    public function get_branch_regions()
    {
        $qry = "select * from branch_regions br";

        return $this->db->query($qry)->result_array();
    }


    public function get_webform_data($webform_id, $filter)
    {
        $where = " ";

        if (isset($filter['date_from'])) {
            $where .= " and DATE(wa.`updated_on`) >= '" . $filter['date_from'] . "' ";
        }
        if (isset($filter['date_to'])) {
            $where .= " and DATE(wa.`updated_on`) < '" . $filter['date_to'] . "' ";
        }

        $qry = "SELECT *
                from webforms w
                inner join webform_answers wa using (webform_id)
                inner join webform_questions wq using (webform_id)
                where w.webform_id = " . $webform_id .$where;

        return $this->db->query($qry)->result_array();
    }

    //Get Dashboards
    public function get_dashboards() {
        $where = "1 ";

        if ($_SESSION['role'] != 1) {
            $where .= " AND du.user_id = ".$_SESSION['user_id'];
        }
        $qry = "SELECT
                  d.*,
                  IF(du.user_id is not null,GROUP_CONCAT(DISTINCT du.user_id SEPARATOR ','),'') as viewers
                  FROM dashboards d
                  LEFT JOIN dashboard_by_user du USING (dashboard_id)
                  WHERE ".$where."
                GROUP BY d.dashboard_id";

        return $this->db->query($qry)->result_array();
    }

    //Get Dashboards to Manage
    public function get_dashboards_to_manage() {
        $where = "1 ";

        if ($_SESSION['role'] != 1 && !in_array("dashboard viewers", $_SESSION['permissions'])) {
            $where .= " AND du.user_id = ".$_SESSION['user_id'];
        }
        $qry = "SELECT
                  d.*,
                  IF(du.user_id is not null,GROUP_CONCAT(DISTINCT du.user_id SEPARATOR ','),'') as viewers
                  FROM dashboards d
                  LEFT JOIN dashboard_by_user du USING (dashboard_id)
                  WHERE ".$where."
                GROUP BY d.dashboard_id";

        return $this->db->query($qry)->result_array();
    }

    //Get Dashboard by id
    public function get_dashboard_by_id($dashboard_id) {
        $where = "d.dashboard_id = ".$dashboard_id;

        if ($_SESSION['role'] != 1) {
            $where .= " AND du.user_id = ".$_SESSION['user_id'];
        }
        $qry = "SELECT
                  d.*,
                  IF(du.user_id is not null,GROUP_CONCAT(DISTINCT du.user_id SEPARATOR ','),'') as viewers
                  FROM dashboards d
                  LEFT JOIN dashboard_by_user du USING (dashboard_id)
                  WHERE ".$where."
                GROUP BY d.dashboard_id";

        return $this->db->query($qry)->result_array();
    }

    /**
     * Save Dashboard
     */
    public function save_dashboard($form) {

        if ($form['dashboard_id']) {
            $form['updated_by'] = $_SESSION['user_id'];
            $form['updated_date'] = to_mysql_datetime(date('now'));
            $this->db->where('dashboard_id', $form['dashboard_id']);
            $this->db->update("dashboards", $form);
            return $form['dashboard_id'];

        }
        else {
            $form['created_by'] = $_SESSION['user_id'];
            $this->db->insert("dashboards", $form);
            return $this->db->insert_id();
        }

    }

    /**
     * Save Dashboard Viewers
     */
    public function save_dashboard_viewers($dashboard_id, $viewers) {

        // Start SQL transaction.
        $this->db->trans_start();

        //Delete the current viewers for this dashboards
        $this->db->where('dashboard_id', $dashboard_id);
        $result = $this->db->delete("dashboard_by_user");

        if (!$result) {
            return false;
        }

        foreach ($viewers as $viewer) {
            $result = $this->db->insert("dashboard_by_user", array(
                "dashboard_id" => $dashboard_id,
                "user_id" => $viewer
            ));

            if (!$result) {
                return false;
            }
        }

        // Complete SQL transaction.
        $this->db->trans_complete();

        return $this->db->trans_status();

    }

    /**
     * Save Dashboard Filters
     */
    public function save_dashboard_filters($dashboard_id, $filters) {

        // Start SQL transaction.
        $this->db->trans_start();

        //Delete the current viewers for this dashboards
        $this->db->where('dashboard_id', $dashboard_id);
        $result = $this->db->delete("dashboard_filters");

        if (!$result) {
            return false;
        }

        foreach ($filters as $filter) {
            $result = $this->db->insert("dashboard_filters", array(
                "dashboard_id" => $dashboard_id,
                "filter_name" => $filter['filter_name'],
                "filter_value" => $filter['filter_value'],
                "editable" => $filter['editable']
            ));

            if (!$result) {
                return false;
            }
        }

        // Complete SQL transaction.
        $this->db->trans_complete();

        return $this->db->trans_status();

    }


    /**
     * Get viewers
     */
    public function get_viewers () {

        $qry = "SELECT ur.role_name, u.user_id id,u.name
                FROM users u INNER JOIN user_roles ur  USING (role_id) WHERE u.user_status = 1 ORDER BY ur.role_name, u.name ";

        return $this->db->query($qry)->result_array();
    }


    /**
     * Add report to the Dashboard
     */
    public function add_report($form) {
        return $this->db->insert("dashboard_reports", $form);
    }

    /**
     * Remove report from the Dashboard
     */
    public function remove_report($dashboard_id, $report_id) {

        $this->db->where('dashboard_id', $dashboard_id);
        $this->db->where('report_id', $report_id);
        return $this->db->delete("dashboard_reports");
    }

    /**
     * Update reports on a dashboard
     */
    public function update_reports($dash_reports) {
        // Start SQL transaction.
        $this->db->trans_start();

        foreach ($dash_reports as $report) {
            $this->db->where('dashboard_id', $report['dashboard_id']);
            $this->db->where('report_id', $report['report_id']);
            $this->db->update("dashboard_reports", $report);
        }

        // Complete SQL transaction.
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Update report by Dashboard Id
     */
    public function update_report($dash_report) {

        $this->db->where('dashboard_id', $dash_report['dashboard_id']);
        $this->db->where('report_id', $dash_report['report_id']);
        return $this->db->update("dashboard_reports", $dash_report);
    }


    /**
     * Remove reports by Dashboard Id
     */
    public function remove_reports_by_dashboard_id($dashboard_id) {

        $this->db->where('dashboard_id', $dashboard_id);
        return $this->db->delete("dashboard_reports");
    }

    /**
     * Insert reports on a particular dashboard
     */
    public function insert_reports($dash_reports) {
        return $this->db->insert_batch('dashboard_reports', $dash_reports);
    }

    /**
     * Reorder reports
     */
    public function reorder_reports($dashboard_id, $dash_reports) {
        // Start SQL transaction.
        $this->db->trans_start();

        $this->remove_reports_by_dashboard_id($dashboard_id);
        $this->insert_reports($dash_reports);

        // Complete SQL transaction.
        $this->db->trans_complete();

        return $this->db->trans_status();
    }



    //Get Dashboard Reports by id

    public function get_dashboard_reports_by_id($dashboard_id) {
        $where = "";

        if ($_SESSION['role'] != 1) {
            $where .= " AND drv.user_id = ".$_SESSION['user_id'];
        }

        $qry = "SELECT
                  dr.*, e.name, e.description
                FROM dashboard_reports dr
                INNER JOIN export_forms e ON (e.export_forms_id = dr.report_id)
                LEFT JOIN  export_to_viewers drv USING (export_forms_id)
                  WHERE dr.dashboard_id = ".$dashboard_id." ".$where."
                  GROUP BY  dr.dashboard_id,export_forms_id
                  ORDER BY dr.position asc";

        return $this->db->query($qry)->result_array();
    }


    //Get dashboard filters by dashboard_id
    public function get_dash_filters($dashboard_id) {
        $qry = "SELECT
                  *
                FROM dashboard_filters
                WHERE dashboard_id = ".$dashboard_id;

        return $this->db->query($qry)->result_array();
    }
}