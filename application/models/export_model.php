<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Export_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    public function sample_export($options)
    {
        $qry = "select date(contact),campaign,count(*) from history left join campaigns using(campaign_id) where role_id = 5 ";
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