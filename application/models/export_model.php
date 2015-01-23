<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Export_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
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
        $qry = "select date(date_created),fullname,urn, add1, add2, add3, postcode, county, country, telephone_number, email, email_optout, website, linkedin, facebook
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
        $result = $this->db->query($qry)->result_array();
        return $result;
    }
}