<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Merge_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
	
	public function merge_company_preview($options){
		$preview=array();
		$query = "select * from companies where urn ='".$options['source']."' and name in(select name from companies where urn = '".$options['target']."')";
		$existing = $this->db->query($query)->result_array();
		$data=array();
		foreach($existing as $row){
				//get the target company
				$query = "select * from companies where name='".$row['name']."' and urn ='".$options['target']."'";
				$target_row = $this->db->query($query)->row_array();
					foreach($target_row as $k=>$v){
						
					if($options['company_details']==2&&empty($v)&&!empty($row[$k])){
						$data[$k]=$row[$k];
					} else if($options['company_details']==3&&!empty($row[$k])){
						$data[$k]=$row[$k];
					}
					}
					
			unset($data['company_id']);
			unset($data['urn']);
			unset($data['fullname']);
			if(!empty($data)){
			$preview['updated'][] = $data;
			}	
		}
		return $preview;
	}
	
	public function merge_contact_preview($options){
		$preview=array();
		//contact details preview
				$query = "select * from contacts where urn ='".$options['source']."' and replace(replace(fullname,'','Mr '),'','Mrs ')  in(select replace(replace(fullname,'','Mr '),'','Mrs ') from contacts where urn = '".$options['target']."')";
		$existing = $this->db->query($query)->result_array();
		$data=array();
		foreach($existing as $row){
				//get the target company
				$query = "select * from contacts where replace(replace(fullname,'','Mr '),'','Mrs ')=replace(replace('".$row['fullname']."','','Mr '),'','Mrs ') and urn ='".$options['target']."'";
				$target_row = $this->db->query($query)->row_array();
					foreach($target_row as $k=>$v){
						
					if($options['contact_details']==2&&empty($v)&&!empty($row[$k])){
						$data[$k]=$row[$k];
					} else if($options['contact_details']==3&&!empty($row[$k])){
						$data[$k]=$row[$k];
					}
					}
					
			unset($data['contact_id']);
			unset($data['urn']);
			unset($data['fullname']);
			if(!empty($data)){
			$preview['updated'][] = $data;
			}	
		}
		
		$query = "select * from contacts where urn ='".$options['source']."' and replace(replace(fullname,'','Mr '),'','Mrs ') not in(select replace(replace(fullname,'','Mr '),'','Mrs ') from contacts where urn = '".$options['target']."')";
			$new_rows = $this->db->query($query)->result_array();
			foreach($new_rows as $newrow){
				foreach($newrow as $k=>$v){
				 if(empty($v)){
					unset($newrow[$k]); 
				 }
				 unset($newrow['title']);
				  unset($newrow['firstname']);
				   unset($newrow['lastname']);
				}
				unset($newrow['contact_id']);
				$newrow['urn'] = $options['target'];
			$preview['added'][] = $newrow;
			}
		return $preview;

		
	}
	
	
	public function merge_cotel_preview($options){
		$preview=array();
		//contact details preview
				$query = "select urn,telephone_id,telephone_number,t.description,ctps from companies inner join company_telephone t using(company_id) where urn ='".$options['source']."' and telephone_number  in(select telephone_number from companies inner join company_telephone where urn = '".$options['target']."')";
		$existing = $this->db->query($query)->result_array();
		$data=array();
		foreach($existing as $row){
				//get the target company
				$query = "select urn,telephone_id,telephone_number,t.description,ctps from companies inner join company_telephone t using(company_id) where telephone_number ='".$row['telephone_number']."' and urn ='".$options['target']."'";
				$target_row = $this->db->query($query)->row_array();
					foreach($target_row as $k=>$v){
						
					if($options['company_details']==2&&empty($v)&&!empty($row[$k])){
						$data[$k]=$row[$k];
					} else if($options['company_details']==3&&!empty($row[$k])){
						$data[$k]=$row[$k];
					}
					}
					
			unset($data['telephone_id']);
			unset($data['urn']);
			if(!empty($data)){
			$preview['updated'][] = $data;
			}	
		}
		
		$query = "select company_id,telephone_id,telephone_number,t.description,ctps from companies inner join company_telephone t using(company_id) where urn ='".$options['source']."' and telephone_number not in(select telephone_number from companies inner join company_telephone using(company_id) where urn = '".$options['target']."')";
			$new_rows = $this->db->query($query)->result_array();
			foreach($new_rows as $newrow){
				foreach($newrow as $k=>$v){
				 if(empty($v)){
					unset($newrow[$k]); 
				 }
				}
				unset($newrow['telephone_id']);
			$preview['added'][] = $newrow;
			}
		return $preview;

		
	}
	
	public function merge_tel_preview($options){
		$preview=array();
		//contact details preview
				$query = "select urn,telephone_id,telephone_number,t.description,tps from contacts inner join contact_telephone t using(contact_id) where urn ='".$options['source']."' and telephone_number  in(select telephone_number from contacts inner join contact_telephone where urn = '".$options['target']."')";
		$existing = $this->db->query($query)->result_array();
		$data=array();
		foreach($existing as $row){
				//get the target company
				$query = "select urn,telephone_id,telephone_number,t.description,tps from contacts inner join contact_telephone t using(contact_id) where telephone_number ='".$row['telephone_number']."' and urn ='".$options['target']."'";
				$target_row = $this->db->query($query)->row_array();
					foreach($target_row as $k=>$v){
						
					if($options['contact_details']==2&&empty($v)&&!empty($row[$k])){
						$data[$k]=$row[$k];
					} else if($options['contact_details']==3&&!empty($row[$k])){
						$data[$k]=$row[$k];
					}
					}
					
			unset($data['telephone_id']);
			unset($data['urn']);
			if(!empty($data)){
			$preview['updated'][] = $data;
			}	
		}
		
		$query = "select contact_id,telephone_id,telephone_number,t.description,ctps from contacts inner join contact_telephone t using(contact_id) where urn ='".$options['source']."' and telephone_number not in(select telephone_number from contacts inner join contact_telephone using(contact_id) where urn = '".$options['target']."')";
			$new_rows = $this->db->query($query)->result_array();
			foreach($new_rows as $newrow){
				foreach($newrow as $k=>$v){
				 if(empty($v)){
					unset($newrow[$k]); 
				 }
				}
				unset($newrow['telephone_id']);
			$preview['added'][] = $newrow;
			}
		return $preview;
	}
	
}