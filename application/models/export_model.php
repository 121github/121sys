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
            $qry .= " and campaign_id IN '" . $options['campaign'] . "' ";
        }
        $qry .= " group by date(contact) ";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }

    public function contacts_added_export($options)
    {
        $qry = "select date(contact),campaign_name,count(*) from history left join campaigns using(campaign_id) where role_id = 5 ";
        if (isset($options['from']) && !empty($options['from'])) {
            $qry .= " and contact >= '" . $options['from'] . "' ";
        }
        if (isset($options['to']) && !empty($options['to'])) {
            $qry .= " and contact <= '" . $options['to'] . "' ";
        }
        $qry .= " group by date(contact) ";
        $result = $this->db->query($qry)->result_array();
        return $result;
    }
}