<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Script_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
        
    }
    
    
    /**
     * Get the scripts
     */
    public function get_scripts()
    {
        $this->db->select("*");
        $this->db->from("scripts s");
        return $this->db->get()->result_array();
    }
    
    /**
     * Get the campaings for all the existing scripts
     */
    public function get_campaigns_by_scripts()
    {
        $this->db->select("c.*");
        $this->db->from("scripts_to_campaigns c");
        $this->db->join("scripts s", "c.script_id = s.script_id");
        return $this->db->get()->result_array();
    }
    
    /**
     * Get a script
     *
     * @param integer $id
     * @return Script
     */
    public function get_script($id)
    {
        $this->db->select("*");
        $this->db->where("script_id", $id);
        
        $results = $this->db->get("scripts")->result_array();
        return $results[0];
    }
    
    
    /**
     * Get the campaings by script
     */
    public function get_campaigns_by_script_id($id)
    {
        $this->db->select("c.*");
        $this->db->from("scripts_to_campaigns c");
        $this->db->where("c.script_id", $id);
        return $this->db->get()->result_array();
    }
    
    /**
     * Add a new script
     *
     * @param Form $form
     */
    public function add_new_script($form)
    {
        $this->db->insert("scripts", $form);
        
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
        
    }
    
    /**
     * Update a script
     *
     * @param Form $form
     */
    public function update_script($form)
    {
        $this->db->where("script_id", $form['script_id']);
        return $this->db->update("scripts", $form);
    }
    
    /**
     * Remove a script
     *
     * @param integer $id
     */
    public function delete_script($id)
    {
        $this->db->where("script_id", $id);
        return $this->db->delete("scripts");
    }
    
    /**
     * Insert the campaings for a script
     */
    public function insert_campaigns_by_script_id($script_id, $campaignList)
    {
        $response = true;
        
        foreach ($campaignList as $campaign) {
            if (!$this->db->insert("scripts_to_campaigns", array(
                "script_id" => $script_id,
                "campaign_id" => $campaign
            ))) {
                $response = false;
            }
        }
        
        return $response;
    }
    
    /**
     * Remove the campaings by script
     */
    public function delete_campaigns_by_script_id($script_id, $campaignList)
    {
        
        $this->db->where("script_id", $script_id);
        $this->db->where_in("campaign_id", $campaignList);
        return $this->db->delete("scripts_to_campaigns");
    }
    
}