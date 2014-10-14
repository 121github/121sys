<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }
    
    public function get_urgent($filter = "")
    {
        $qry = "select urn,fullname,date_format(records.date_updated,'%d/%m/%y %H:%i') date_updated from records left join contacts using(urn) where urgent = 1 ";
        if (!empty($filter)) {
            $qry .= " and campaign_id = '$filter'";
        }
		$qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " order by records.date_updated asc";
        return $this->db->query($qry)->result_array();
    }
    
    public function get_favorites($filter = "")
    {
        $qry = "select urn,fullname,date_format(records.date_updated,'%d/%m/%y %H:%i') date_updated from records left join contacts using(urn) where urn in(select urn from favorites where user_id = '{$_SESSION['user_id']}')";
        if (!empty($filter)) {
            $qry .= " and campaign_id = '$filter'";
        }
		$qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " order by records.date_updated asc";
        return $this->db->query($qry)->result_array();
    }
    
    public function get_history($filter = "")
    {
        $qry = "select contact, u.name, campaign_name, if(h.outcome_id is null,if(pd.description is null,'No Action Required',pd.description),outcome) as outcome, history_id, comments,urn from history h left join outcomes using(outcome_id) left join progress_description pd using(progress_id) left join campaigns using(campaign_id) left join users u using(user_id) where 1 ";
        //if a filter is selected then we just return history from that campaign, otehrwize get all the history
        if (!empty($filter)) {
            $qry .= " and h.campaign_id = '$filter'";
        }
		$qry .= " and h.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " order by history_id desc limit 10";
		$this->firephp->log($qry);
        return $this->db->query($qry)->result_array();
    }
    
    public function get_outcomes($filter = array())
    {
        $qry = "select outcome,count(*) count from history left join outcomes using(outcome_id) left join records using(urn) where 1 and history.outcome_id is not null ";
        if (!empty($filter['campaign'])) {
            $qry .= " and history.campaign_id = '" . intval($filter['campaign']) . "'";
        }
		$qry .= " and history.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by history.outcome_id order by count desc ";
        return $this->db->query($qry)->result_array();
    }
    
  
    
    public function system_stats($filter = "")
    {
        $extra     = " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $extra_url = "";
        if (!empty($filter)) {
            $extra     = " and campaign_id = '" . intval($filter) . "'";
            $extra_url = "/campaign/$filter";
        }
        //data stats
        $virgin_qry            = "select count(*) data from records where outcome_id is null and nextcall is null and record_status = 1 and progress_id is null $extra  ";
        $data['virgin']        = $this->db->query($virgin_qry)->row()->data;
        $data['virgin_url']    = base_url() . "search/custom/records/nextcall/null/outcome/null/status/live" . $extra_url;
        $active_qry            = "select count(*) data from records where record_status = 1 and outcome_id is not null and outcome_id <> 60 and progress_id is null $extra";
        $data['active']        = $this->db->query($active_qry)->row()->data;
        $data['active_url']    = base_url() . "search/custom/records/progress/null/outcome/not_null/status/live/outcome/survey%20complete:not" . $extra_url;
        $dead_qry              = "select count(*) data from records where record_status = 3 $extra";
        $data['dead']          = $this->db->query($dead_qry)->row()->data;
        $data['dead_url']      = base_url() . "search/custom/records/status/dead" . $extra_url;
        $parked_qry            = "select count(*) data from records where record_status = 2 $extra";
        $data['parked']        = $this->db->query($parked_qry)->row()->data;
        $data['parked_url']    = base_url() . "search/custom/records/status/parked" . $extra_url;
        $pending_qry           = "select count(*) data from records where progress_id = 1 and record_status=1 $extra ";
        $data['pending']       = $this->db->query($pending_qry)->row()->data;
        $data['pending_url']   = base_url() . "search/custom/records/progress/pending/status/live" . $extra_url;
        $no_action_qry         = "select count(*) data from records where record_status = 1 and outcome_id = 60 and progress_id is null $extra ";
        $data['no_action']     = $this->db->query($no_action_qry)->row()->data;
        $data['no_action_url'] = base_url() . "search/custom/records/progress/null/outcome/survey complete/status/live" . $extra_url;
        
        $in_progress_qry         = "select count(*) data from records where progress_id = 2  and record_status=1 $extra";
        $data['in_progress']     = $this->db->query($in_progress_qry)->row()->data;
        $data['in_progress_url'] = base_url() . "search/custom/records/progress/in progress/status/live" . $extra_url;
        $completed_qry           = "select count(*) data from records where progress_id = 3 and record_status=1 $extra ";
        $data['completed']       = $this->db->query($completed_qry)->row()->data;
        $data['completed_url']   = base_url() . "search/custom/records/progress/complete/status/live" . $extra_url;
        //survey stats
        $surveys_qry             = "select count(distinct urn) data from surveys left join records using(urn) where 1 $extra";
        $data['surveys']         = $this->db->query($surveys_qry)->row()->data;
        
        $nps_failures     = "select count(*) data from surveys left join survey_answers using(survey_id) left join questions using(question_id) left join records using(urn) where answer<6 and answer is not null and nps_question=1 $extra";
        $data['failures'] = $this->db->query($nps_failures)->row()->data;
        $nps_average      = "select avg(answer) data from surveys left join survey_answers using(survey_id) left join questions using(question_id) left join records using(urn) where nps_question=1 $extra";
        $data['average']  = number_format($this->db->query($nps_average)->row()->data, 1);
        $nps_averages     = "select survey_name, avg(answer) nps from surveys left join survey_info using(survey_info_id)left join  survey_answers using(survey_id) left join questions using(question_id) left join records using(urn) where nps_question=1  $extra group by surveys.survey_info_id";
        $result           = $this->db->query($nps_averages)->result_array();
        foreach ($result as $row) {
            $data['averages'][$row['survey_name']] = $row['nps'];
        }
        //thinkmoney progress
        $manager_progress = "select count(*) count,description from records left join progress_description using(progress_id) where progress_id is not null group by progress_id";
        $result           = $this->db->query($manager_progress)->result_array();
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
    
    public function get_comments($comments)
    {
		$extra = " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $this->load->helper('date');
        if ($comments == 1) {
            $qry = "select urn,an.notes,completed_date `date`,fullname from answer_notes an left join survey_answers using(answer_id) left join surveys  using(survey_id) left join records using(urn) left join contacts using(urn) where char_length(an.notes) > 50 $extra group by contacts.contact_id order by completed_date desc limit 10";
        } else if ($comments == 2) {
            $qry = "select urn,comments notes,contact `date`,fullname from history left join records using(urn) left join contacts using(urn) where char_length(comments) > 40 $extra order by history_id desc  limit 10";
        } else if ($comments == 3) {
            $qry = "select urn,note notes,s.date_updated `date`,fullname from sticky_notes s left join records using(urn) left join contacts using(urn) where char_length(note) > 40 $extra order by s.date_updated desc  limit 10";
        } else {
            $qry = "select urn,notes,`date`,fullname from (select urn,an.notes,completed_date `date`,fullname from answer_notes an left join survey_answers using(answer_id) left join surveys using(survey_id) left join records using(urn) left join contacts using(urn) where char_length(an.notes) > 40  $extra group by contacts.contact_id union select urn,comments,contact,fullname from history left join records using(urn) left join contacts using(urn) where char_length(comments) > 40 $extra union select urn,note,s.date_updated,fullname from sticky_notes s left join records using(urn) left join contacts using(urn) where char_length(note) > 40 $extra) t order by t.`date` desc limit 10";
        }
        $result = $this->db->query($qry)->result_array();
        $now    = time();
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
    
	public function timely_callbacks($filter)
    {
        $qry = "select urn,fullname as contact,nextcall,campaign_name as campaign,name from records left join ownership using(urn) left join campaigns using(campaign_id) left join contacts using(urn) left join users using(user_id) where outcome_id in(1,2) and nextcall > subdate(NOW(), INTERVAL 1 HOUR) and nextcall <  adddate(NOW(), INTERVAL 1 HOUR) ";
        if (!empty($filter['campaign'])) {
            $qry .= " and campaign_id = " . intval($filter['campaign']);
        }
        if (!empty($filter['user'])) {
            $qry .= " and user_id = " . intval($filter['user']);
        }
        $qry .= " group by urn order by nextcall asc limit 10";
        return $this->db->query($qry)->result_array();
    }
	
    public function missed_callbacks($filter)
    {
        $qry = "select urn,fullname as contact,nextcall,campaign_name as campaign,name from records left join ownership using(urn) left join campaigns using(campaign_id) left join contacts using(urn) left join users using(user_id) where outcome_id in(1,2) and nextcall < now() ";
        if (!empty($filter['campaign'])) {
            $qry .= " and campaign_id = " . intval($filter['campaign']);
        }
        if (!empty($filter['user'])) {
            $qry .= " and user_id = " . intval($filter['user']);
        }
		$qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by urn order by nextcall asc limit 10";
        return $this->db->query($qry)->result_array();
    }
    
    public function upcoming_callbacks($filter)
    {
        $qry = "select urn,fullname as contact,nextcall,campaign_name as campaign,name from records left join ownership using(urn) left join campaigns using(campaign_id) left join contacts using(urn) left join users using(user_id) where outcome_id in(1,2) and nextcall > now() ";
        if (!empty($filter['campaign'])) {
            $qry .= " and campaign_id = " . intval($filter['campaign']);
        }
        if (!empty($filter['user'])) {
            $qry .= " and user_id = " . intval($filter['user']);
        }
		$qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by urn order by nextcall asc limit 10";
        return $this->db->query($qry)->result_array();
    }
    
    public function client_progress($filter)
    {
        $qry = "select urn,fullname as name,nextcall,campaign_name as campaign,pd.`description` as `status`,urgent from records left join ownership using(urn) left join campaigns using(campaign_id) left join contacts using(urn) left join progress_description pd using(progress_id) where (progress_id in(1,2) and progress_id is not null) and nextcall is not null or urgent=1 and nextcall is not null";
        if (!empty($filter['campaign'])) {
            $qry .= " and campaign_id = " . intval($filter['campaign']);
        }
        if (!empty($filter['user'])) {
            $qry .= " and user_id = " . intval($filter['user']);
        }
		$qry .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry .= " group by urn order by nextcall asc limit 20";
        
        return $this->db->query($qry)->result_array();
    }
    public function agent_activity($campaign)
    {
        if (!empty($campaign)) {
            $qry_filter = " and campaign_id = " . intval($campaign);
        } else {
            $qry_filter = "";
        }
        $qry_filter .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry = "select urn, max(contact) as `when`, survey_date,outcome as outcome,`name`,campaign_name as campaign from history left join campaigns using(campaign_id) left join outcomes using(outcome_id) left join users u using(user_id) left join records using(urn)  left join (select user_id, max(contact) survey_date from history h left join records using(urn) where h.outcome_id = 60 $qry_filter group by user_id) ls on ls.user_id = history.user_id where history.role_id = 3 $qry_filter  group by u.user_id ";
        
        return $this->db->query($qry)->result_array();
    }
    
    public function agent_success($campaign)
    {
        if (!empty($campaign)) {
            $qry_filter = " and campaigns.campaign_id = " . intval($campaign);
        } else {
            $qry_filter = "";
        }
         $qry_filter .= " and records.campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry = "select `name`,count(*) dials,surveys,campaign_name as campaign from history h left join campaigns using(campaign_id)  left join records using(urn) left join (select user_id,count(*) surveys from history left join records using(urn) where 1 and history.outcome_id = 60 $qry_filter group by user_id) sv on sv.user_id = h.user_id  left join users on h.user_id = users.user_id where h.role_id = 3 $qry_filter group by h.user_id";
        return $this->db->query($qry)->result_array();
    }
    
    public function agent_data($campaign)
    {
        if (!empty($campaign)) {
            $qry_filter = " and campaign_id = " . intval($campaign);
        } else {
            $qry_filter = "";
        }
         $qry_filter .= " and campaign_id in({$_SESSION['campaign_access']['list']}) ";
        $qry = "select `name`,count(*) total,virgin,in_progress,completed,campaign_name as campaign from records r  left join ownership o using(urn)  left join campaigns using(campaign_id) left join (select user_id,count(*) virgin from records left join ownership o using(urn) left join campaigns using(campaign_id) where outcome_id is null and record_status in(1,3) $qry_filter group by user_id) v on v.user_id = o.user_id  
	 left join (select user_id,count(*) in_progress from records  left join ownership o using(urn) left join campaigns using(campaign_id) where record_status = 1 and outcome_id is not null $qry_filter group by user_id) ip on ip.user_id = o.user_id  
	 left join (select user_id,count(*) completed from records  left join ownership o using(urn) left join campaigns using(campaign_id) where record_status = 3 $qry_filter group by user_id) c on c.user_id = o.user_id  
	left join users on o.user_id = users.user_id where role_id = 3 $qry_filter group by o.user_id";
        return $this->db->query($qry)->result_array();
    }  
    
}