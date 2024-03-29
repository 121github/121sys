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
	$table = $this->input->post('table');
	$this->Datatables_model->set_user_view($id,$table);
	$columns = $this->Datatables_model->get_visible_columns($table);
	echo json_encode(array("success"=>true,"columns"=>$columns));
	}
	public function get_user_views(){
		$views = $this->Datatables_model->get_user_views($this->input->post('table'));
		$all_columns = $this->Datatables_model->all_columns($this->input->post('table'));
		foreach($all_columns as $column){
		$columns[$column['datafield_group']][]=array("columns"=>$column);
		}
		echo json_encode(array("views"=>$views,"columns"=>$columns));
	}

	public function save_order(){
	$columns = $this->input->post('columns');
	$view_id = $this->input->post('view');

	$selected_columns = $_SESSION['col_order'];	
	$newsort = array();
	foreach($selected_columns as $k=>$column){
	$newsort[]["datafield_id"]=$column['datafield_id'];
	$key  = array_search($k, $columns);
	$this->db->where(array("datafield_id"=>$column['datafield_id'],"view_id"=>$view_id));
	$this->db->join("datatables_views","datatables_view_fields.view_id=datatables_views.view_id");
	$this->db->update("datatables_view_fields",array("sort"=>$key));
	}
	$_SESSION['col_order'] = $newsort;
	echo json_encode(array("success"=>true));
	}

	public function save_columns(){
		$data = $this->input->post();	
		if(!isset($data['columns'])){
		echo json_encode(array("success"=>false,"msg"=>"No columns selected"));	
		exit;
		}
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
		echo json_encode(array("success"=>true,"action"=>$action,"id"=>$data['view_id'],"name"=>$data['view_name']));
	}

	public function get_columns(){
		$selected = array();
		$selected_columns = $this->Datatables_model->selected_columns($this->input->post('id'));
		foreach($selected_columns as $selected_column){
		$selected[] = $selected_column['datafield_id'];
		}
		echo json_encode($selected);
	}
}