<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Merge_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
	
	##### These functions just return the fields that will be merged ##########
	
	public function merge_company_preview($options){
		$preview=array();
		$query = "select company_id,name,description,conumber,turnover,employees,website,email,status from companies where urn ='".$options['source']."' and name in(select name from companies where urn = '".$options['target']."')";
		$existing = $this->db->query($query)->result_array();
		foreach($existing as $row){
			$data=array();
				//get the target company
				$query = "select company_id,name,description,conumber,turnover,employees,website,email,status from companies where name='".$row['name']."' and urn ='".$options['target']."'";
				$target_row = $this->db->query($query)->row_array();
					foreach($target_row as $k=>$v){
						$company_id = $target_row['company_id'];
					if($options['company_details']==2&&empty($v)&&!empty($row[$k])){
						$data[$k]=$row[$k];
					} else if($options['company_details']==3&&!empty($row[$k])&&$row[$k]!==$v){
						$data[$k]=$row[$k];
					}
					}
					
			unset($data['company_id']);
			unset($data['urn']);
			unset($data['fullname']);
			$data['company_id'] = $company_id;
			$preview['updated'][] = $data;	
		}
		return $preview;
	}
	
	public function merge_contact_preview($options){
		$preview=array();
		//contact details preview
				$query = "select contact_id,fullname,gender,position,dob,email,website,linkedin,facebook,notes from contacts where urn ='".$options['source']."' and replace(replace(fullname,'Mr ',''),'Mrs ','')  in(select replace(replace(fullname,'Mr ',''),'Mrs ','') from contacts where urn = '".$options['target']."')";		//$this->firephp->log($query);
		$existing = $this->db->query($query)->result_array();
		foreach($existing as $row){
			$data=array();
			$source_contact = $row['contact_id'];
				//get the target company
				$query = "select contact_id,fullname,gender,position,dob,email,website,linkedin,facebook,notes from contacts where replace(replace(fullname,'Mr ',''),'Mrs ','')=replace(replace('".$row['fullname']."','Mr ',''),'Mrs ','') and urn ='".$options['target']."'";
				$target_row = $this->db->query($query)->row_array();
					foreach($target_row as $k=>$v){
						$contact_id = $target_row['contact_id'];
					if($options['contact_details']==2&&empty($v)&&!empty($row[$k])){
						$data[$k]=$row[$k];
					} else if($options['contact_details']==3&&!empty($row[$k])&&$row[$k]!==$v){
						$data[$k]=$row[$k];
					}
					}
			unset($data['contact_id']);
			unset($data['urn']);
			unset($data['title']);
			unset($data['firstname']);
			unset($data['lastname']);
			unset($data['date_updated']);
				$data['contact_id'] = $contact_id;
				$data['source_contact'] = $source_contact;
			$preview['updated'][] = $data;	
		}
		
		$query = "select contact_id,fullname,gender,position,dob,email,website,linkedin,facebook,notes from contacts where urn ='".$options['source']."' and replace(replace(fullname,'Mr ',''),'Mrs ','') not in(select replace(replace(fullname,'Mr ',''),'Mrs ','') from contacts where urn = '".$options['target']."')";
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
				$newrow['source_contact'] = $newrow['contact_id'];
				unset($newrow['contact_id']);
			$preview['added'][] = $newrow;
			}
		return $preview;

		
	}
	
	
	public function merge_cotel_preview($options,$target_company){
		$preview=array();
		$query = "select company_id,telephone_id,telephone_number,t.description,ctps from companies inner join company_telephone t using(company_id) where urn ='".$options['source']."' and telephone_number not in(select telephone_number from company_telephone where company_id = '$target_company')";
			$new_rows = $this->db->query($query)->result_array();
			foreach($new_rows as $newrow){
				foreach($newrow as $k=>$v){
				 if(empty($v)){
					unset($newrow[$k]); 
				 }
				}
				unset($newrow['telephone_id']);
				$newrow['company_id'] = $target_company;
			$preview['added'][] = $newrow;
			}
		return $preview;

		
	}
	
	public function merge_tel_preview($options,$source_contact,$target_contact=false){
		$preview=array();
		if($target_contact){
		$query = "select fullname,telephone_id,telephone_number,t.description,tps from contacts inner join contact_telephone t using(contact_id) where contact_id ='".$source_contact."' and telephone_number not in(select telephone_number from contact_telephone where contact_id = '$target_contact')";
		
			$new_rows = $this->db->query($query)->result_array();
			foreach($new_rows as $newrow){
				foreach($newrow as $k=>$v){
				 if(empty($v)){
					unset($newrow[$k]); 
				 }
				}
			
				unset($newrow['telephone_id']);
			$preview[] = $newrow;
			}
		}
		if(!$target_contact){	
			$query = "select fullname,telephone_id,telephone_number,t.description,tps from contacts inner join contact_telephone t using(contact_id) where contact_id = '$source_contact'";	
			$new_rows = $this->db->query($query)->result_array();
			foreach($new_rows as $newrow){
				foreach($newrow as $k=>$v){
				 if(empty($v)){
					unset($newrow[$k]); 
				 }
				}
			unset($newrow['telephone_id']);
			$preview[] = $newrow;
			}
		}
		return $preview;
	}


##### These functions will do the merging #########

	public function merge_company($options){
		$preview=array();
		$query = "select company_id,name,description,conumber,turnover,employees,website,email,status from companies where urn ='".$options['source']."' and name in(select name from companies where urn = '".$options['target']."')";
		$existing = $this->db->query($query)->result_array();
		foreach($existing as $row){
			$data=array();
				//get the target company
				$query = "select company_id,name,description,conumber,turnover,employees,website,email,status from companies where name='".$row['name']."' and urn ='".$options['target']."'";
				$target_row = $this->db->query($query)->row_array();
					foreach($target_row as $k=>$v){
						$company_id = $target_row['company_id'];
					if($options['company_details']==2&&empty($v)&&!empty($row[$k])){
						$data[$k]=$row[$k];
					} else if($options['company_details']==3&&!empty($row[$k])&&$row[$k]!==$v){
						$data[$k]=$row[$k];
					}
					}
					
			unset($data['company_id']);

			if(!empty($data)){
			$data['company_id'] = $company_id;
			$this->db->where("company_id",$data['company_id']);
			$this->db->update("companies",$data);
			$preview['updated'][] = $data;
			
			}	
		}
		return $preview;
	}
	
	public function merge_contact($options){
		$preview=array();
		//contact details preview
				$query = "select contact_id,fullname,gender,position,dob,email,website,linkedin,facebook,notes from contacts where urn ='".$options['source']."' and replace(replace(fullname,'Mr ',''),'Mrs ','')  in(select replace(replace(fullname,'Mr ',''),'Mrs ','') from contacts where urn = '".$options['target']."')";		//$this->firephp->log($query);
		$existing = $this->db->query($query)->result_array();
		foreach($existing as $row){
			$data=array();
			$source_contact = $row['contact_id'];
				//get the target company
				$query = "select contact_id,fullname,gender,position,dob,email,website,linkedin,facebook,notes from contacts where replace(replace(fullname,'Mr ',''),'Mrs ','')=replace(replace('".$row['fullname']."','Mr ',''),'Mrs ','') and urn ='".$options['target']."'";
				$target_row = $this->db->query($query)->row_array();
					foreach($target_row as $k=>$v){
						$contact_id = $target_row['contact_id'];
					if($options['contact_details']==2&&empty($v)&&!empty($row[$k])){
						$data[$k]=$row[$k];
					} else if($options['contact_details']==3&&!empty($row[$k])&&$row[$k]!==$v){
						$data[$k]=$row[$k];
					}
					}
			unset($data['contact_id']);
			unset($data['urn']);
			if(count($data)>0){
			$this->db->where("contact_id",$contact_id);
			$this->db->update("contacts",$data);
			$this->merge_tel($options,$source_contact,$contact_id);	
			}
		}
		
		$query = "select contact_id,fullname,gender,position,dob,email,website,linkedin,facebook,notes from contacts where urn ='".$options['source']."' and replace(replace(fullname,'Mr ',''),'Mrs ','') not in(select replace(replace(fullname,'Mr ',''),'Mrs ','') from contacts where urn = '".$options['target']."')";
			$new_rows = $this->db->query($query)->result_array();
			foreach($new_rows as $newrow){
				$source_contact = $newrow['contact_id'];
				foreach($newrow as $k=>$v){
				 if(empty($v)){
					unset($newrow[$k]); 
				 }
				}
				unset($newrow['contact_id']);
				$newrow['urn'] = $options['target'];
			$this->db->insert("contacts",$newrow);
			$new_id = $this->db->insert_id();
			$this->merge_tel($options,$source_contact,$new_id);
			}
		return true;

		
	}
	
	
	public function merge_cotel($options,$target_company){
		$preview=array();
		$query = "select company_id,telephone_id,telephone_number,t.description,ctps from companies inner join company_telephone t using(company_id) where urn ='".$options['source']."' and telephone_number not in(select telephone_number from company_telephone where company_id = '$target_company')";
		
			$new_rows = $this->db->query($query)->result_array();
			foreach($new_rows as $newrow){
				foreach($newrow as $k=>$v){
				 if(empty($v)){
					unset($newrow[$k]); 
				 }
				}
				unset($newrow['telephone_id']);
				$newrow['company_id'] = $target_company;
				
			$this->db->where("company_id",$target_company);
			$insert_query = $this->db->insert_string("company_telephone",$newrow);
			$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
			$this->db->query($insert_query);
			}
		return $preview;

		
	}
	
	public function merge_tel($options,$source_contact,$target_contact){
		$preview=array();		
			$query = "select telephone_id,telephone_number,t.description,tps from contacts inner join contact_telephone t using(contact_id) where contact_id ='".$source_contact."' and telephone_number not in(select telephone_number from contact_telephone inner join contacts using(contact_id) where contact_id = '".$target_contact."')";
			$new_rows = $this->db->query($query)->result_array();
			foreach($new_rows as $newrow){
				foreach($newrow as $k=>$v){
				 if(empty($v)){
					unset($newrow[$k]); 
				 }
				}
				unset($newrow['telephone_id']);
			$newrow['contact_id'] = $target_contact;
		$insert_query = $this->db->insert_string("contact_telephone",$newrow);
			$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
			//$this->firephp->log($insert_query);
			$this->db->query($insert_query);
			}
			
		return $preview;
	}
	
}