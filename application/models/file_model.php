<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class File_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
           
    }
	
	public function add_file($filename,$folder){
		return $this->db->insert("files",array("filename"=>$filename,"folder_id"=>$folder,"user_id"=>$_SESSION['user_id']));
	}
	
		public function delete_file($file_id){
		$this->db->where(array("file_id"=>$file_id));
		return $this->db->delete("files");
	}
	
			public function select_files($folder_id){
		$this->db->where(array("folder_id"=>$folder_id));
		return $this->db->get("files")->result_array();
	}
	
				public function get_folders(){
		$this->db->join("folder_users","folders.folder_id=folder_users.folder_id","LEFT");
		$this->db->where(array("user_id"=>$_SESSION['user_id']));
		return $this->db->get("folders")->result_array();
	}
	
}
?>