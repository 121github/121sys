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