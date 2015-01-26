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
        $qry = "select * from export_forms";

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

    public function get_data($export_form, $options) {

        $qry = $export_form['query'];

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
        if ($export_form['group_by']) {
            $qry .= " group by ".$export_form['group_by'];
        }
        if ($export_form['order_by']) {
            $qry .= " order by ".$export_form['order_by'];
        }

        $result = $this->db->query($qry)->result_array();

        return $result;
    }

    public function dials_export($options)
    {
        $qry = "select date(contact),campaign_name,count(*) from history left join campaigns using(campaign_id) where role_id is not null";
        if (isset($options['from']) && !empty($options['from'])) {
            $qry .= " and contact >= '" . $options['from'] . "' ";
        }
        if (isset($options['to']) && !empty($options['to'])) {
            $qry .= " and contact <= '" . $options['to'] . "' ";
        }
        if (isset($options['campaign']) && !empty($options['campaign'])) {
            $qry .= " and campaign_id = '" . $options['campaign'] . "' ";
        }
        $qry .= " group by date(contact) ";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }

    public function contacts_added_export($options)
    {
        $qry = "select
                  date(date_created),
                  IF(fullname is not null,fullname,' ') as fullname,
                  IF(urn is not null,urn,' ') as urn,
                  IF(add1 is not null,add1,' ') as add1,
                  IF(add2 is not null,add2,' ') as add2,
                  IF(add3 is not null,add3,' ') as add3,
                  IF(postcode is not null,postcode,' ') as postcode,
                  IF(county is not null,county,' ') as county,
                  IF(country is not null,country,' ') as country,
                  IF(telephone_number is not null,telephone_number,' ') as telephone_number,
                  IF(email is not null,email,' ') as email,
                  IF(email_optout is not null,email_optout,' ') as email_optout,
                  IF(website is not null,website,' ') as website,
                  IF(linkedin is not null,linkedin,' ') as linkedin,
                  IF(facebook is not null,facebook,' ') as facebook
                from contacts
                  left join contact_telephone using(contact_id)
                  left join contact_addresses using(contact_id)
                  inner join records using(urn)
                  left join campaigns using(campaign_id)
                where 1=1 ";
        if (isset($options['from']) && !empty($options['from'])) {
            $qry .= " and date_created >= '" . $options['from'] . "' ";
        }
        if (isset($options['to']) && !empty($options['to'])) {
            $qry .= " and date_created <= '" . $options['to'] . "' ";
        }
        if (isset($options['campaign']) && !empty($options['campaign'])) {
            $qry .= " and campaign_id = '" . $options['campaign'] . "' ";
        }
        $qry .= " order by date_created desc";
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
        return $this->db->insert("export_forms", $form);

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
}