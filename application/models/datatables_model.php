<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Datatables_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->custom_fields = array("c1","c2","c3","c4","c5","c6","d1","d2","dt1","dt2","n1","n2");
    }
	
	public function get_visible_columns($table_id){
		$this->db->where(array("user_id"=>$_SESSION['user_id'],"table_id"=>$table_id));
		$this->db->join("datatables_columns","datatables_columns.column_id=datatables_user_columns.column_id");
		$columns = $this->db->get("datatables_user_columns")->result_array();
		$visible_columns = array();
		foreach($columns as $column){
		$visible_columns['columns'][] = array("data" => !empty($column['column_alias'])?$column['column_alias']:$column['column_select']);
		$visible_columns['headings'][] = $column['column_title'];
		$visible_columns['select'][] = $column['column_select'] ." ".$column['column_alias'];
		$visible_columns['filter'][] = $column['column_select'];
		$visible_columns['order'][] = $column['column_order'];
		$visible_columns['tables'][] = $column['column_table'];
		}
		
		foreach($visible_columns['headings'] as $k => $heading){
			if(in_array($heading,$this->custom_fields)){
				$this->db->where(array("campaign_id"=>$_SESSION['current_campaign'],"field"=>$heading));
		$field = $this->db->get('record_details_fields')->row_array();
		if(count($field)){
		$visible_columns['headings'][$k] = $field['field_name'];
		}
			}
		}
		
		return $visible_columns;
}
	
	public function set_default_columns($user_id){
		 $this->db->query("delete from `datatables_user_columns` where user_id = '$user_id'");
				 $this->db->query("INSERT INTO `datatables_user_columns` (`id`, `user_id`, `column_id`, `table_id`, `sort`) VALUES
('', $user_id, 1, 1, 1),
('', $user_id, 2, 1, 1),
('', $user_id, 7, 1, 1),
('', $user_id, 3, 1, 1),
('', $user_id, 4, 1, 1),
('', $user_id, 17, 1, 1)");		
	}
	
	public function all_columns($table_id){
	
	$this->db->select("column_group,datatables_columns.column_id,column_title");
	$this->db->where("table_id",$table_id);
	$this->db->join("datatables_table_columns","datatables_table_columns.column_id = datatables_columns.column_id");
	 $result = $this->db->get("datatables_columns")->result_array();
	 $this->firephp->log($this->db->last_query());
	 if(@$_SESSION['current_campaign']>0){
	 foreach($result as $k => $row){
		if(in_array($row['column_title'],$this->custom_fields)){
		$this->db->where(array("campaign_id"=>$_SESSION['current_campaign'],"field"=>$row['column_title']));
		$field = $this->db->get('record_details_fields')->row_array();
		if(count($field)){
		$result[$k]['column_title'] = $field['field_name'];
		} else {
			unset($result[$k]);
		}
	 }
	 }
	 }
	 return $result;
	}
	
		public function selected_columns($table_id){
	$this->db->select("column_id");
	$this->db->where(array("table_id"=>$table_id,"user_id"=>$_SESSION['user_id']));
	return $this->db->get("datatables_user_columns")->result_array();
	}
	
	public function delete_user_columns($table_id){
	$this->db->where(array("user_id"=>$_SESSION['user_id'],"table_id"=>$table_id));
	$this->db->delete("datatables_user_columns");	
	}
	
	public function save_user_columns($column_id,$table_id){
		$this->db->insert("datatables_user_columns",array("user_id"=>$_SESSION['user_id'],"column_id"=>$column_id,"table_id"=>$table_id));		
	}
	
}