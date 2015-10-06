<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Datatables extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
        $this->_campaigns = campaign_access_dropdown();

        $this->load->model('Datatables_model');
    }

	public function save_columns(){
		$data = $this->input->post();
		//delete the existing user columns for this table
		$this->Datatables_model->delete_user_columns($data['table']);
		//insert new column selections
		foreach($data['columns'] as $k=>$v){
		 $this->Datatables_model->save_user_columns($v,$data['table']);
		}
		$columns = $this->Datatables_model->get_visible_columns($data['table']);
		echo json_encode(array("columns"=>$columns));
	}

	public function get_columns(){
		$selected = array();
		$columns = array();
		$all_columns = $this->Datatables_model->all_columns($this->input->post('table'));
		$selected_columns = $this->Datatables_model->selected_columns($this->input->post('table'));
		foreach($selected_columns as $selected_column){
		$selected[$selected_column['column_id']] = $selected_column['column_id'];
		}
		foreach($all_columns as $column){
		$columns[$column['column_group']][]=array("columns"=>$column,"selected"=>$selected);
		}
		
		echo json_encode($columns);
	}
}