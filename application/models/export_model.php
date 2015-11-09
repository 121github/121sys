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
            $qry .= " and ".$export_form['campaign_filter']." = '" . $options['campaign'] . "' ";
        }
        if ($export_form['source_filter'] && isset($options['source']) && !empty($options['source'])) {
            $qry .= " and " . $export_form['source_filter'] . " = '" . $options['source'] . "' ";
        }
        if ($export_form['group_by']) {
            $qry .= " group by ".$export_form['group_by'];
        }
        if ($export_form['order_by']) {
            $qry .= " order by ".$export_form['order_by'];
        }

        $result = $this->db->query($qry)->result_array();

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
     *
     * Get the contacts data
     *
     */
    public function get_contacts_data($form) {

        $where = " where contacts.contact_id is not null and companies.company_id is not null  ";

        if (isset($form['campaign']) && !empty($form['campaign'])) {
            $where .= " and records.campaign_id = '" . $form['campaign'] . "' ";
            //$where .= " and records.campaign_id = 13";

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
                  inner join companies using (urn)
                  left join company_addresses using (company_id)
                  left join company_telephone using (company_id)
                  left join contacts using (urn)
                  left join contact_addresses ca ON (ca.contact_id = contacts.contact_id)
                  left join contact_telephone ct ON (ct.contact_id = contacts.contact_id) ";

        $qry .= $where;

        $qry .= " group by contacts.contact_id
                  order by records.urn";

        $this->firephp->log($qry);
        $result = $this->db->query($qry)->result_array();
        $this->firephp->log($result);
        exit(0);

        return $result;
    }
}