<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Orders extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->project_version = $this->config->item('project_version');
    }

    public function get_orders()
    {
		//first get all contacts on the record
		$urn = $this->input->post('urn');
		
		//now get the orders
		$this->db = $this->load->database('flexicart', TRUE); 
		$qry = "select ord_order_number,date_format(ord_date,'%d/%m/%Y') order_date,ord_total,ord_status_description from order_summary join order_status on ord_status = ord_status_id where urn='$urn'";
		$data = $this->db->query($qry)->result_array();
		echo json_encode(array("success"=>true,"data"=>$data));
	}
	
	
	
	
}