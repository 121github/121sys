<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class File_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
           
    }
	
	public function get_folder_users($id){
	$this->db->where(array("folder_id"=>$id));	
	return $this->db->get('folder_permissions');
	}
	
	
	public function add_file($filename,$folder,$filesize){
		$this->db->insert("files",array("filename"=>$filename,"filesize"=>$filesize,"folder_id"=>$folder,"user_id"=>$_SESSION['user_id']));
		if($this->db->_error_message()){
		return false;	
		} else {
		return true;	
		}
	}
		
		public function delete_folder($id){
			//$this->firephp->log($id);
			//delete the user folder
			$this->db->where(array("folder_id"=>$id));
			$this->db->delete('folders');
			//delete the user permissions
			$this->db->where(array("folder_id"=>$id));
			return $this->db->delete('folder_permissions');
		}
		
		public function folder_name($id){
			$this->db->where(array("folder_id"=>$id));
			$query = $this->db->get('folders');
			if($query->num_rows()){
			return $query->row()->folder_name;
			}
		}
		
		public function create_folder($data){	
		$this->db->insert("folders",array("folder_name"=>$data['folder_name'],"accepted_filetypes"=>$data['accepted_filetypes']));
		return $this->db->insert_id();
		}
		
		public function save_folder($data){
		$this->db->where(array("folder_id"=>$data['folder_id']));	
		return $this->db->update("folders",array("folder_name"=>$data['folder_name'],"accepted_filetypes"=>$data['accepted_filetypes']));
		}
		
		public function select_files($folder_id){
		$this->db->where(array("folder_id"=>$folder_id));
		return $this->db->get("files")->result_array();
	}
	
	public function get_folder_read_users($id){
	$this->db->where(array("folder_id"=>$id,"read"=>"1"));
	return $this->db->get("folder_permissions")->result_array();	
	}
	
	
	
	
		public function get_folder_write_users($id){
	$this->db->where(array("folder_id"=>$id,"write"=>"1"));
	return $this->db->get("folder_permissions")->result_array();	
	}
					public function add_read_users($users=array(),$folder){
						//delete all users from folder so we can readd the new ones
						$this->db->where(array("folder_id"=>$folder));
						$this->db->delete("folder_permissions");

						//now add them all
						if($users){
	foreach($users as $user_id){
				$this->db->replace("folder_permissions",array("folder_id"=>$folder,"user_id"=>$user_id,"read"=>1));
			}	
						}
	}
	
			public function add_write_users($users=array(),$folder){
				//if a user has the write permission they should also have the read permission
					if($users){
			foreach($users as $user_id){
				$this->db->replace("folder_permissions",array("folder_id"=>$folder,"user_id"=>$user_id,"read"=>1,"write"=>1));
			}
			}
	}
	

	
	public function file_from_id($id){
		$query = "select filename,folder_name from files left join folders using(folder_id) left join folder_permissions using(folder_id) where file_id = '".intval($id)."'";
		if($_SESSION['role']>1){
		$query .= " and folder_permissions.user_id = '".$_SESSION['user_id']."'";	
		}
		$this->firephp->log($query);
		return $this->db->query($query)->row_array();
	}
	
		public function get_folders(){
		$folders = array();
		$this->db->select("folders.folder_id,folder_name,user_id,accepted_filetypes,`read`,`write`");
		$this->db->join("folder_permissions","folders.folder_id=folder_permissions.folder_id","LEFT");
		if($_SESSION['role']<>"1"){
		$this->db->where(array("user_id"=>$_SESSION['user_id'],"read"=>"1"));
		}
		$this->db->order_by("folder_name");
		foreach($this->db->get("folders")->result_array() as $row){
			$folders[$row['folder_id']] = $row;
		}
		return $folders;
	}
	
	public function delete_file($id){
	$this->db->where(array("file_id"=>$id));
	$this->db->update("files",array("deleted_on"=>date('Y-m-d H:i:s'),"deleted_by"=>$_SESSION['user_id']));	
	//set the filename to avoid dupes
	$this->db->query("update files set filename = concat(filename,'-',deleted) where file_id = '".intval($id)."'");
	}
	
	public function get_files($folder,$showall=false){
	$this->load->helper('scan');
	$limit = !$showall?"limit 10":"";
	$files = $this->db->query("select file_id,folder_name,filename,date_format(date_added,'%d/%m/%Y %H:%i') date_added,filesize,if(username is null,'Anonymous',username) username from files left join folders using(folder_id) left join users using(user_id) where folder_id = '".intval($folder)."' and deleted_on is null order by date_added desc $limit")->result_array();
	foreach($files as $k=>$row){
	$files[$k]['size'] = 	formatSizeUnits($row['filesize']);
	}
	return $files;
	}
	
}
?>