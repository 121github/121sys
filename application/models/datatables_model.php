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
	public function set_user_view($id,$table){
	//deselect all user views	
	$this->db->where(array("user_id"=>$_SESSION['user_id'],"table_id"=>$table));	
	$this->db->update("datatables_views",array("selected"=>0));
	//now select the one they specified	
	$this->db->where(array("view_id"=>$id,"user_id"=>$_SESSION['user_id']));	
	$this->db->update("datatables_views",array("selected"=>1));
	}
	
	public function get_user_views($table_id){
	$this->db->where(array("user_id"=>$_SESSION['user_id'],"table_id"=>$table_id));
	$this->db->group_by("view_name");
	$views = $this->db->get("datatables_views")->result_array();
	return $views;
	}
	
	public function get_distance_query(){
		if(isset($_SESSION['filter']['values']['postcode'])){
				$coords = postcode_to_coords($_SESSION['filter']['values']['postcode']);
				$sql = "ROUND(if(camp.campaign_type_id=1,min((((ACOS(SIN((" .
                    $coords['lat'] . "*PI()/180)) * SIN((contact_locations.lat*PI()/180))+COS((" .
                    $coords['lat'] . "*PI()/180)) * COS((contact_locations.lat*PI()/180)) * COS(((" .
                    $coords['lng'] . "- contact_locations.lng)*PI()/180))))*180/PI())*60*1.1515)),min((((ACOS(SIN((" .
                    $coords['lat'] . "*PI()/180)) * SIN((company_locations.lat*PI()/180))+COS((" .
                    $coords['lat'] . "*PI()/180)) * COS((company_locations.lat*PI()/180)) * COS(((" .
                    $coords['lng'] . "- company_locations.lng)*PI()/180))))*180/PI())*60*1.1515))),2)";
		} else {
		$sql = "'-'"; //null distance
		}
		return $sql;
	}
	
	public function get_visible_columns($table_id){
		$query = "SELECT *
FROM (`datatables_views`)
JOIN `datatables_view_fields` ON `datatables_views`.`view_id`=`datatables_view_fields`.`view_id`
JOIN `datafields` ON `datatables_view_fields`.`datafield_id`=`datafields`.`datafield_id`
WHERE `user_id` =  '".$_SESSION['user_id']."'
AND `table_id` =  '".$table_id."'
AND `selected` =  1
ORDER BY `sort`";
//$this->firephp->log($query);
		$columns = $this->db->query($query)->result_array();
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
		 $this->db->query("delete from `datatables_views` where user_id = '".$_SESSION['user_id']."' and table_id = 1");
		 $this->db->query("insert ignore into datatables_views set view_name = 'Default view', view_description = 'The default record list view', user_id = '".$_SESSION['user_id']."', table_id = 1,selected=1");
	 //default record fields	
		 $view_id = $this->db->insert_id();
				 $this->db->query("INSERT ignore INTO `datatables_view_fields` (`view_id`, `datafield_id`, `sort`) VALUES
($view_id, 1, 1),
($view_id, 2, 1),
($view_id, 7, 1),
($view_id, 3, 1),
($view_id, 4, 1),
($view_id, 17, 1)");
 //default appointment fields
		 $this->db->query("delete from `datatables_views` where user_id = '".$_SESSION['user_id']."' and table_id = 3");
$this->db->query("insert ignore into datatables_views set view_name = 'Default view', view_description = 'The default appointment list view', user_id = '".$_SESSION['user_id']."', table_id = 3,selected=1");
 $view_id = $this->db->insert_id();
 $this->db->query("INSERT ignore INTO `datatables_view_fields` (`view_id`, `datafield_id`, `sort`) VALUES
($view_id, 4, 1),
($view_id, 44, 1),
($view_id, 45, 1),
($view_id, 48, 1),
($view_id, 50, 1),
($view_id, 52, 1)");	
 //default survey fields
		 $this->db->query("delete from `datatables_views` where user_id = '".$_SESSION['user_id']."' and table_id = 5");
$this->db->query("insert ignore into datatables_views set view_name = 'Default view', view_description = 'The default record list view', user_id = '".$_SESSION['user_id']."', table_id = 5,selected=1");
 $view_id = $this->db->insert_id();
 $this->db->query("INSERT ignore INTO `datatables_view_fields` (`view_id`, `datafield_id`, `sort`)  (select $view_id,datafield_id,1 from datafields where datafield_table='survey_contacts' or datafield_group = 'Survey')");	
 //default history fields
  $this->db->query("delete from `datatables_views` where user_id = '".$_SESSION['user_id']."' and table_id = 2");
$this->db->query("insert ignore into datatables_views set view_name = 'Default view', view_description = 'The default history list view', user_id = '".$_SESSION['user_id']."', table_id = 2,selected=1");
 $view_id = $this->db->insert_id();
 $this->db->query("INSERT ignore INTO `datatables_view_fields` (`view_id`, `datafield_id`, `sort`)  (select $view_id,datafield_id,1 from datafields where datafield_table='survey_contacts' or datafield_group = 'History')");	



	}
	
	public function dynamic_panel_fields($campaign){
		$this->db->select('custom_panel_fields.name');
		$this->db->where("campaign_id",$campaign);
		$this->db->join("campaign_custom_panels","campaign_custom_panels.custom_panel_id","custom_panels.custom_panel_id");
		$this->db->join("custom_panels","custom_panel_fields.custom_panel_id","custom_panels.custom_panel.id");
		return $this->db->get("custom_panel_fields")->result_array();
	}
		public function dynamic_field_name($campaign,$field_id){
		$this->db->select('custom_panel_fields.name');
		$this->db->where("campaign_id",$campaign);
		$this->db->join("campaign_custom_panels","campaign_custom_panels.custom_panel_id","custom_panels.custom_panel_id");
		$this->db->join("custom_panels","custom_panel_fields.custom_panel_id","custom_panels.custom_panel.id");
		return $this->db->get("custom_panel_fields")->result_array();
	}
	
	
	public function all_columns($table_id){
		$campaign = "";
	 if(isset($_SESSION['current_campaign'])&&$_SESSION['current_campaign']>0){
		$campaign = "or campaign = ". $_SESSION['current_campaign'];
	 }
	$query = "select datafield_group,datafields.datafield_id,datafield_title from datafields join datatables_table_fields using(datafield_id) where table_id = '$table_id' and (campaign is null $campaign) and datafield_group <> '' ";

	$result = $this->db->query($query)->result_array();
	 if(isset($_SESSION['current_campaign'])&&$_SESSION['current_campaign']>0){
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
	
		public function selected_columns($view_id=false,$table_id=false){
	$this->db->select("datafield_id");
	if($view_id){
	$this->db->where(array("datatables_view_fields.view_id"=>$view_id,"user_id"=>$_SESSION['user_id']));
	} else {
	$this->db->where(array("datatables_views.table_id"=>$table_id,"user_id"=>$_SESSION['user_id'],"selected"=>1));	
	}
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