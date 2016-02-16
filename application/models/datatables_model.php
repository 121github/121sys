<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Datatables_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->custom_fields = custom_fields();
    }
	public function set_user_view($id){
	//deselect all user views	
	$this->db->where(array("user_id"=>$_SESSION['user_id']));	
	$this->db->update("datatables_views",array("selected"=>0));
	//now select the one they specified	
	$this->db->where(array("view_id"=>$id,"user_id"=>$_SESSION['user_id']));	
	$this->db->update("datatables_views",array("selected"=>1));
			$this->firephp->log($this->db->last_query());
	}
	
	public function get_user_views($table_id){
	$this->db->where(array("user_id"=>$_SESSION['user_id'],"table_id"=>$table_id));
	$views = $this->db->get("datatables_views")->result_array();
	return $views;
	}
	public function get_visible_columns($table_id){
		$this->db->where(array("user_id"=>$_SESSION['user_id'],"table_id"=>$table_id,"selected"=>1));
		$this->db->join("datatables_view_fields","datatables_views.view_id=datatables_view_fields.view_id");
		$this->db->join("datafields","datatables_view_fields.datafield_id=datafields.datafield_id");
		$this->db->order_by("sort");
		$columns = $this->db->get("datatables_views")->result_array();
		if(count($columns)==0){
		return false;
		}
		$visible_columns = array();
		foreach($columns as $column){
		$visible_columns['view_id'] = $column['view_id'];
		$visible_columns['columns'][] = array("data" => !empty($column['datafield_alias'])?$column['datafield_alias']:$column['datafield_select']);
		$visible_columns['headings'][] = $column['datafield_title'];
		$visible_columns['select'][] = $column['datafield_select'] ." ".$column['datafield_alias'];
		//cannot use group_concat with where operators so just use the order instead ;)
		if(strpos($column['datafield_select'],"group_concat")!==false){
		$visible_columns['filter'][] = $column['datafield_order'];
		} else if(strpos($column['datafield_select'],"char_length")!==false){
		$visible_columns['filter'][] = $column['datafield_order'];	
		} else {
		$visible_columns['filter'][] = $column['datafield_select'];
		}
		
		$visible_columns['order'][] = $column['datafield_order'];
		$visible_columns['tables'][] = $column['datafield_table'];
		}
		
		foreach($visible_columns['headings'] as $k => $heading){
			if(in_array($heading,$this->custom_fields)){
				$current_campaign = (isset($_SESSION['current_campaign'])?$_SESSION['current_campaign']:'');
				$this->db->where(array("campaign_id"=>$current_campaign,"field"=>$heading));
		$field = $this->db->get('record_details_fields')->row_array();
		if(count($field)){
		$visible_columns['headings'][$k] = $field['field_name'];
		}
			}
		}
		
		return $visible_columns;
}
	
	public function set_default_columns($user_id){
		 $this->db->query("delete from `datatables_views` where user_id = '$user_id'");
		 $this->db->query("insert ignore into datatables_views set view_name = 'Default view', view_description = 'The default record list view', user_id = '".$_SESSION['user_id']."', table_id = 1,selected=1");
		
		 $view_id = $this->db->insert_id();
				 $this->db->query("INSERT INTO `datatables_view_fields` (`view_id`, `datafield_id`, `sort`) VALUES
($view_id, 1, 1),
($view_id, 2, 1),
($view_id, 7, 1),
($view_id, 3, 1),
($view_id, 4, 1),
($view_id, 17, 1)");

$this->db->query("insert ignore into datatables_views set view_name = 'Default view', view_description = 'The default record list view', user_id = '".$_SESSION['user_id']."', table_id = 3,selected=1");
 $view_id = $this->db->insert_id();
 $this->db->query("INSERT INTO `datatables_view_fields` (`view_id`, `datafield_id`, `sort`) VALUES
($view_id, 4, 1),
($view_id, 44, 1),
($view_id, 45, 1),
($view_id, 48, 1),
($view_id, 50, 1),
($view_id, 52, 1)");		
	}
	
	public function all_columns($table_id){
	
	$this->db->select("datafield_group,datafields.datafield_id,datafield_title");
	$this->db->where("table_id",$table_id);
	$this->db->join("datatables_table_fields","datatables_table_fields.datafield_id = datafields.datafield_id");
	 $result = $this->db->get("datafields")->result_array();
	 if(@$_SESSION['current_campaign']>0){
	 foreach($result as $k => $row){
		if(in_array($row['datafield_title'],$this->custom_fields)){
		$this->db->where(array("campaign_id"=>$_SESSION['current_campaign'],"field"=>$row['datafield_title']));
		$field = $this->db->get('record_details_fields')->row_array();
		if(count($field)){
		$result[$k]['datafield_title'] = $field['field_name'];
		} else {
			unset($result[$k]);
		}
	 }
	 }
	 }
	 return $result;
	}
	
		public function selected_columns($view_id){
	$this->db->select("datafield_id");
	$this->db->where(array("datatables_view_fields.view_id"=>$view_id,"user_id"=>$_SESSION['user_id']));
	$this->db->join("datatables_views","datatables_views.view_id=datatables_view_fields.view_id");
	$this->db->order_by("sort");
	return $this->db->get("datatables_view_fields")->result_array();
	}
	public function create_view($data){
	$this->db->insert("datatables_views",array("view_name"=>$data['view_name'],'user_id'=>$_SESSION['user_id'],"table_id"=>$data['table']));	
	return $this->db->insert_id();
	}
		public function update_view($data){
	$this->db->where("view_id",$data['view_id']);
	$this->db->update("datatables_views",array("view_name"=>$data['view_name']));	
	}
	public function delete_view_columns($view_id){
	$this->db->where(array("view_id"=>$view_id));
	$this->db->delete("datatables_view_fields");	
	}

	public function save_view_columns($column_id,$view_id){
		$this->db->insert("datatables_view_fields",array("datafield_id"=>$column_id,"view_id"=>$view_id));		
	}
	
}