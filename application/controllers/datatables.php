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
	public function set_user_view(){
	$id = $this->input->post('id');
	$this->Datatables_model->set_user_view($id);
	$columns = $this->Datatables_model->get_visible_columns($this->input->post('table'));
	echo json_encode(array("success"=>true,"columns"=>$columns));
	}
	public function get_user_views(){
		$views = $this->Datatables_model->get_user_views($this->input->post('table'));
		$all_columns = $this->Datatables_model->all_columns($this->input->post('table'));
		foreach($all_columns as $column){
		$columns[$column['column_group']][]=array("columns"=>$column);
		}
		echo json_encode(array("views"=>$views,"columns"=>$columns));
	}

	public function save_order(){
	$columns = $this->input->post('columns');
	$view_id = $this->input->post('view');

	$selected_columns = $this->Datatables_model->selected_columns($view_id);
	foreach($selected_columns as $k=>$column){
	$key  = array_search($k, $columns);
	$this->db->where(array("column_id"=>$column['column_id'],"view_id"=>$view_id));
	$this->db->join("datatables_views","datatables_view_columns.view_id=datatables_views.view_id");
	$this->db->update("datatables_view_columns",array("sort"=>$key));
	}
	}

	public function save_columns(){
		$data = $this->input->post();	
		
		if(empty($data['view_id'])){
			//create the view
		$data['view_id'] = $this->Datatables_model->create_view($data);
		$action = "create";
		} else {
			//or delete the columns
		$this->Datatables_model->update_view($data);
		$action = "updated";
		$this->Datatables_model->delete_view_columns($data['view_id']);	
		}
		//insert new column selections
		foreach($data['columns'] as $k=>$v){
		 $this->Datatables_model->save_view_columns($v,$data['view_id']);
		}
		echo json_encode(array("action"=>$action,"id"=>$data['view_id'],"name"=>$data['view_name']));
	}

	public function get_columns(){
		$selected = array();
		$selected_columns = $this->Datatables_model->selected_columns($this->input->post('id'));
		foreach($selected_columns as $selected_column){
		$selected[] = $selected_column['column_id'];
		}
		
		echo json_encode($selected);
	}
}