<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Export_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_export_forms() {
        $qry = "select * from export_forms order by name";

        return $this->db->query($qry)->result_array();
    }

    public function get_export_forms_by_id($id) {
        $qry = "select * from export_forms where export_forms_id = ".$id;

        $results =  $this->db->query($qry)->result_array();

        if (!empty($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    public function get_export_users() {
        $qry = "select *
                from export_to_users
                inner join users using (user_id) left join users_to_campaigns using(user_id) left join campaigns using(campaign_id) where campaign_status = 1 or role_id = 1 group by user_id";

        return $this->db->query($qry)->result_array();
    }

    public function get_export_users_by_export_id($export_forms_id) {
        $qry = "select * from export_to_users where export_forms_id = ".$export_forms_id;

        return $this->db->query($qry)->result_array();
    }

    public function get_export_graphs_by_export_id($export_forms_id) {
        $qry = "select * from export_graphs where export_forms_id = ".$export_forms_id;

        return $this->db->query($qry)->result_array();
    }



    public function get_data($export_form, $options) {

        $qry = $export_form['query'];

        //If there is not where clause in the query, add it
        if (!stripos($qry, "where")) {
            $qry .= " where 1=1 ";
        }

        if ($export_form['date_filter']) {
            if (isset($options['from']) && !empty($options['from'])) {
                $qry .= " and ".$export_form['date_filter']." >= '" . $options['from'] . "' ";
            }
            if (isset($options['to']) && !empty($options['to'])) {
                $qry .= " and ".$export_form['date_filter']." <= '" . $options['to'] . "' ";
            }
        }
        if ($export_form['campaign_filter'] && isset($options['campaign']) && !empty($options['campaign'])) {
            $qry .= " and ".$export_form['campaign_filter'] . " IN (".implode(",",$options['campaign']).") ";
        }
        if ($export_form['outcome_filter'] && isset($options['outcome']) && !empty($options['outcome'])) {
            $qry .= " and ".$export_form['outcome_filter'] . " IN (".implode(",",$options['outcome']).") ";
        }
        if ($export_form['source_filter'] && isset($options['sources']) && !empty($options['sources'])) {
            $qry .= " and " . $export_form['source_filter'] . " IN (".implode(",",$options['sources']).") ";
        }
		if ($export_form['pot_filter'] && isset($options['pot']) && !empty($options['pot'])) {
            $qry .= " and " . $export_form['pot_filter'] . " IN (".implode(",",$options['pot']).") ";
        }
        if ($export_form['team_filter'] && isset($options['team']) && !empty($options['team'])) {
            $qry .= " and " . $export_form['team_filter'] . " IN (".implode(",",$options['team']).") ";
        }
        if ($export_form['agent_filter'] && isset($options['agent']) && !empty($options['agent'])) {
            $qry .= " and " . $export_form['agent_filter'] . " IN (".implode(",",$options['agent']).") ";
        }
        if ($export_form['user_filter'] && isset($options['user']) && !empty($options['user'])) {
            if (in_array("user_id", $options['user']) && isset($_SESSION['user_id'])) {
                $options['user'] = array_replace($options['user'],
                    array_fill_keys(
                        array_keys($options['user'], "user_id"),
                        $_SESSION['user_id']
                    )
                );
            }
            $qry .= " and " . $export_form['user_filter'] . " IN (".implode(",",$options['user']).") ";
        }
        if ($export_form['group_by']) {
            $qry .= " group by ".$export_form['group_by'];
        }
        if ($export_form['order_by']) {
            $qry .= " order by ".$export_form['order_by'];
        }
        $result = $this->db->query($qry)->result_array();

        $this->firephp->log($qry);

        return $result;
    }

    /**
     * Add a new export form
     *
     * @param Form $form
     */
    public function insert_export_form($form)
    {
         $this->db->insert("export_forms", $form);
         return $this->db->insert_id();

    }

    /**
     * Update an export form
     *
     * @param Form $form
     */
    public function update_export_form($form)
    {
        $this->db->where("export_forms_id", $form['export_forms_id']);
         return $this->db->update("export_forms", $form);
    }

    /**
     * Remove an export form
     *
     * @param integer $export_forms_id
     */
    public function delete_export_form($export_forms_id)
    {
        $this->db->where("export_forms_id", $export_forms_id);
        return $this->db->delete("export_forms");
    }

    /**
     * Update a user to an export. Delete the old_users and add the new_users selected
     *
     * @param Form $form
     */
    public function update_export_user($users, $export_forms_id)
    {
        //Delete all the users for this export before
        $this->db->where("export_forms_id", $export_forms_id);
        $results = $this->db->delete("export_to_users");

        //Insert the new users selected
        if (!empty($users) && $results) {
            $aux = array();
            foreach($users as $user) {
                array_push($aux,array(
                    'export_forms_id' => $export_forms_id,
                    'user_id' => $user
                ));
            }
            $users = $aux;

            $results = $this->db->insert_batch("export_to_users", $users);
        }

        return $results;

    }

    /**
     * Add a new export graph
     *
     */
    public function insert_export_graph($graph)
    {
        $this->db->insert("export_graphs", $graph);
        return $this->db->insert_id();

    }

    /**
     * Remove an export graph
     *
     * @param integer $graph_id
     */
    public function delete_export_graph($graph_id)
    {
        $this->db->where("graph_id", $graph_id);
        return $this->db->delete("export_graphs");
    }

    /**
     *
     * Get the contacts data
     *
     */
    public function get_contacts_data($options) {

        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();
        $outcomes = isset($options['outcomes']) ? $options['outcomes'] : array();
        $teams = isset($options['teams']) ? $options['teams'] : array();
        $sources = isset($options['sources']) ? $options['sources'] : array();
        $pots = isset($options['pots']) ? $options['pots'] : array();

        $where = " where (contacts.contact_id IS NOT NULL OR companies.company_id IS NOT NULL)  ";

        if (!empty($date_from)) {
            $where .= " and (date(contacts.date_updated) >= '".$date_from."' or (contacts.date_updated is null and date(contacts.date_created) >=  '".$date_from."')) ";
        }
        if (!empty($date_to)) {
            $where .= " and (date(contacts.date_updated) <= '".$date_to."' or (contacts.date_updated is null and date(contacts.date_created) <=  '".$date_to."')) ";
        }
        if (!empty($campaigns)) {
            $where .= " and records.campaign_id IN (".implode(",",$campaigns).") ";
        }
        if (!empty($outcomes)) {
            $where .= " and records.outcome_id IN (".implode(",",$outcomes).") ";
        }
        if (!empty($teams)) {
            $where .= " and teams.team_id IN (".implode(",",$teams).") ";
        }
        if (!empty($sources)) {
            $where .= " and records.source_id IN (".implode(",",$sources).") ";
        }
        if (!empty($pots)) {
            $where .= " and records.pot_id IN (".implode(",",$pots).") ";
        }

        $qry = "select
                  IFNULL(campaigns.campaign_name,'-') as campaign_name,
                  IFNULL(companies.name,'-') as company_name,
                  IFNULL(company_addresses.add1,'-') as add1,
                  IFNULL(company_addresses.add2,'-') as add2,
                  IFNULL(company_addresses.add3,'-') as add3,
                  IFNULL(company_addresses.postcode,'-') as postcode,
                  IFNULL(company_addresses.county,'-') as county,
                  IFNULL(company_addresses.country,'-') as country,
                  GROUP_CONCAT(DISTINCT company_telephone.telephone_number separator ',') as company_telephone_number,
                  IFNULL(contacts.title,'-') as title,
                  IFNULL(contacts.fullname,'-') as fullname,
                  IFNULL(contacts.position,'-') as position,
                  IFNULL(contacts.email,'-') as email,
                  GROUP_CONCAT(DISTINCT ct.telephone_number separator ',') as contact_telephone_number,
                  outcomes.outcome,
                  records.dials
                from records
                  left join outcomes using(outcome_id)
                  inner join campaigns using (campaign_id)
                  left join companies using (urn)
                  left join company_addresses using (company_id)
                  left join company_telephone using (company_id)
                  left join contacts using (urn)
                  left join contact_addresses ca ON (ca.contact_id = contacts.contact_id)
                  left join contact_telephone ct ON (ct.contact_id = contacts.contact_id)
                  left join data_sources sources on records.source_id = sources.source_id
                  left join data_pots pots on records.pot_id = pots.pot_id ";

        $qry .= $where;

        $qry .= " group by contacts.contact_id
                  order by records.urn";

        $result = $this->db->query($qry)->result_array();


        return $result;
    }

    /**
     *
     * Get the hours data
     *
     */
    public function get_hours_data($options) {

        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();
        $agents = isset($options['agents']) ? $options['agents'] : array();
        $teams = isset($options['teams']) ? $options['teams'] : array();

        $where = " where 1 ";

        if (!empty($date_from)) {
            $where .= " and h.date >= '".$date_from."'";
        }
        if (!empty($date_to)) {
            $where .= " and h.date <= '".$date_to."'";
        }
        if (!empty($campaigns)) {
            $where .= " and h.campaign_id IN (".implode(",",$campaigns).") ";
        }
        if (!empty($agents)) {
            $where .= " and h.user_id IN (".implode(",",$agents).") ";
        }
        if (!empty($teams)) {
            $where .= " and u.team_id IN (".implode(",",$teams).") ";
        }

        $qry = "SELECT u.user_id, u.ext, u.name, date(h.date) as date, c.campaign_name, TRUNCATE(h.duration/3600,2) as duration
                  FROM hours h
                  INNER JOIN campaigns c using (campaign_id)
                  INNER JOIN users u using (user_id) ";

        $qry .= $where;

        $qry .= " GROUP BY h.date, u.ext, c.campaign_name
                  ORDER BY h.date, u.user_id asc";

        $result = $this->db->query($qry)->result_array();


        return $result;
    }

    /**
     *
     * Get the positive outcomes data
     *
     */
    public function get_positive_outcomes_data($options) {

        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();
        $agents = isset($options['agents']) ? $options['agents'] : array();
        $teams = isset($options['teams']) ? $options['teams'] : array();
        $sources = isset($options['sources']) ? $options['sources'] : array();
        $pots = isset($options['pots']) ? $options['pots'] : array();

        $where = " where o.positive = 1 ";

        if (!empty($date_from)) {
            $where .= " and date(h.contact) >= '".$date_from."'";
        }
        if (!empty($date_to)) {
            $where .= " and date(h.contact) <= '".$date_to."'";
        }
        if (!empty($campaigns)) {
            $where .= " and h.campaign_id IN (".implode(",",$campaigns).") ";
        }
        if (!empty($agents)) {
            $where .= " and h.user_id IN (".implode(",",$agents).") ";
        }
        if (!empty($teams)) {
            $where .= " and u.team_id IN (".implode(",",$teams).") ";
        }
        if (!empty($sources)) {
            $where .= " and h.source_id IN (".implode(",",$sources).") ";
        }
        if (!empty($pots)) {
            $where .= " and h.pot_id IN (".implode(",",$pots).") ";
        }

        $qry = "SELECT u.user_id, u.ext, u.name, date(h.contact) as date, c.campaign_name, count(*) as num
                  FROM history h
                  INNER JOIN campaigns c using (campaign_id)
                  INNER JOIN users u using (user_id)
                  INNER JOIN outcomes o using (outcome_id)
                  LEFT JOIN data_sources sources on h.source_id = sources.source_id
                  LEFT JOIN data_pots pots on h.pot_id = pots.pot_id ";

        $qry .= $where;

        $qry .= " GROUP BY date(h.contact), u.ext, c.campaign_name
                  ORDER BY date(h.contact) asc";

        $result = $this->db->query($qry)->result_array();


        return $result;
    }

    /**
     *
     * Get the combo data for the file export (hours + positive outcomes)
     *
     */
    public function get_combo_export_data($options, $campaigns) {

        $result['data'] = array();

        //Get the hours
        $result['hours'] = $this->get_hours_data($options);

        foreach ($result['hours'] as $hours) {
            if (!isset($result['data'][$hours['date']][$hours['ext']][$hours['campaign_name']])) {
                $result['data'][$hours['date']][$hours['user_id']]['name'] = $hours['name'];
                $result['data'][$hours['date']][$hours['user_id']]['ext'] = $hours['ext'];
                $result['data'][$hours['date']][$hours['user_id']][$hours['campaign_name']] = array();
            }
            $result['data'][$hours['date']][$hours['user_id']][$hours['campaign_name']]['duration'] = $hours['duration'];
            $result['data'][$hours['date']][$hours['user_id']][$hours['campaign_name']]['positive'] = 0;
        }

        //Get the positive outcomes
        $result['positive_outcomes'] = $this->get_positive_outcomes_data($options);

        foreach ($result['positive_outcomes'] as $outcomes) {
            if (!isset($result['data'][$outcomes['date']][$outcomes['user_id']][$outcomes['campaign_name']])) {
                $result['data'][$outcomes['date']][$outcomes['user_id']]['name'] = $outcomes['name'];
                $result['data'][$outcomes['date']][$outcomes['user_id']]['ext'] = $outcomes['ext'];
                $result['data'][$outcomes['date']][$outcomes['user_id']][$outcomes['campaign_name']] = array();
            }
            if (!isset($result['data'][$outcomes['date']][$outcomes['user_id']][$outcomes['campaign_name']]['duration'])) {
                $result['data'][$outcomes['date']][$outcomes['user_id']][$outcomes['campaign_name']]['duration'] = 0;
            }
            $result['data'][$outcomes['date']][$outcomes['user_id']][$outcomes['campaign_name']]['positive'] = $outcomes['num'];
        }

        unset($result['hours']);
        unset($result['positive_outcomes']);
        ksort($result['data']);

        array_unique($campaigns);
        $aux = array ();

        foreach ($result['data'] as $date => $user) {
            foreach ($user as $id => $val) {
                $data = array(
                    'login' => ($val['ext']?$val['ext']:'-'),
                    'name' => $val['name'],
                    'date' => $date,
                );
                foreach ($campaigns as $campaign) {
                    $data[$campaign." [hours]"] = (isset($val[$campaign]['duration'])?$val[$campaign]['duration']:'');
                    $data[$campaign." [positive]"] = (isset($val[$campaign]['positive'])?$val[$campaign]['positive']:'');
                }
                array_push($aux,$data);
            }
        }
        $result['data'] = $aux;

        return $result['data'];
    }

    /**
     *
     * Get the dials data
     *
     */
    public function get_dials_data($options) {

        $date_from = $options['date_from'];
        $date_to = $options['date_to'];
        $campaigns = isset($options['campaigns']) ? $options['campaigns'] : array();
        $agents = isset($options['agents']) ? $options['agents'] : array();
        $outcomes = isset($options['outcomes']) ? $options['outcomes'] : array();
        $teams = isset($options['teams']) ? $options['teams'] : array();
        $sources = isset($options['sources']) ? $options['sources'] : array();
        $pots = isset($options['pots']) ? $options['pots'] : array();

        $where = " where 1 ";

        if (!empty($date_from)) {
            $where .= " and date(h.contact) >= '".$date_from."'";
        }
        if (!empty($date_to)) {
            $where .= " and date(h.contact) <= '".$date_to."'";
        }
        if (!empty($campaigns)) {
            $where .= " and h.campaign_id IN (".implode(",",$campaigns).") ";
        }
        if (!empty($outcomes)) {
            $where .= " and h.outcome_id IN (".implode(",",$outcomes).") ";
        }
        if (!empty($agents)) {
            $where .= " and h.user_id IN (".implode(",",$agents).") ";
        }
        if (!empty($teams)) {
            $where .= " and u.team_id IN (".implode(",",$teams).") ";
        }
        if (!empty($sources)) {
            $where .= " and h.source_id IN (".implode(",",$sources).") ";
        }
        if (!empty($pots)) {
            $where .= " and h.pot_id IN (".implode(",",$pots).") ";
        }

        $qry = "SELECT date(h.contact) as date, c.campaign_name, count(*) as dials
                  FROM history h
                  INNER JOIN campaigns c using (campaign_id)
                  INNER JOIN users u using (user_id)
                  INNER JOIN outcomes o using (outcome_id)
                  LEFT JOIN data_sources sources on h.source_id = sources.source_id
                  LEFT JOIN data_pots pots on h.pot_id = pots.pot_id ";

        $qry .= $where;

        $qry .= " GROUP BY date(h.contact), c.campaign_name
                  ORDER BY date(h.contact) asc";

        $result = $this->db->query($qry)->result_array();


        return $result;
    }

    /**
     *
     * Get the dials data for the file export
     *
     */
    public function get_dials_export_data($options, $campaigns) {

        $result['data'] = array();

        //Get the hours
        $result['dials'] = $this->get_dials_data($options);

        foreach ($result['dials'] as $dials) {
            $result['data'][$dials['date']][$dials['campaign_name']]['dials'] = $dials['dials'];
        }

        unset($result['dials']);
        ksort($result['data']);

        array_unique($campaigns);
        $aux = array ();

        foreach ($result['data'] as $date => $val) {
            $data = array(
                'date' => $date,
            );
            foreach ($campaigns as $campaign) {
                $data[$campaign] = (isset($val[$campaign]['dials'])?$val[$campaign]['dials']:'');
            }
            array_push($aux,$data);
        }
        $result['data'] = $aux;

        $this->firephp->log($result['data']);

        return $result['data'];
    }

    /**
     *
     * Get campaigns by id list
     *
     */
    public function get_campaigns_by_id_list($campaign_list) {

        $qry = "SELECT c.campaign_name
                  FROM campaigns c
                  WHERE c.campaign_id IN (".implode(',',$campaign_list).")";

        $result = $this->db->query($qry)->result_array();

        $aux = array();
        foreach($result as $val) {
            array_push($aux, $val['campaign_name']);
        }
        $result = $aux;

        return $result;
    }


}