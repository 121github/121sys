<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class File_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
        
    }
    
    public function get_folder_users($id)
    {
        $this->db->where(array(
            "folder_id" => $id
        ));
        return $this->db->get('folder_permissions');
    }
    
    
    public function add_file($filename, $folder, $filesize)
    {
        $this->db->insert("files", array(
            "filename" => $filename,
            "filesize" => $filesize,
            "folder_id" => $folder,
            "user_id" => $_SESSION['user_id']
        ));
        if ($this->db->_error_message()) {
            return false;
        } else {
            return true;
        }
    }
    
    public function delete_folder($id)
    {
        //$this->firephp->log($id);
        //delete the user folder
        $this->db->where(array(
            "folder_id" => $id
        ));
        $this->db->delete('folders');
        //delete the user permissions
        $this->db->where(array(
            "folder_id" => $id
        ));
        return $this->db->delete('folder_permissions');
    }
    
    public function folder_name($id)
    {
        $this->db->where(array(
            "folder_id" => $id
        ));
        $query = $this->db->get('folders');
        if ($query->num_rows()) {
            return $query->row()->folder_name;
        }
    }
    
    public function create_folder($data)
    {
       $insert_query =  $this->db->insert_string("folders", array(
            "folder_name" => $data['folder_name'],
            "accepted_filetypes" => $data['accepted_filetypes']
        ));
		$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
		$this->db->query($insert_query);
        return $this->db->insert_id();
    }
    
    public function save_folder($data)
    {
        $this->db->where(array(
            "folder_id" => $data['folder_id']
        ));
        return $this->db->update("folders", array(
            "folder_name" => $data['folder_name'],
            "accepted_filetypes" => $data['accepted_filetypes']
        ));
    }
    
    public function select_files($folder_id)
    {
        $this->db->where(array(
            "folder_id" => $folder_id
        ));
        return $this->db->get("files")->result_array();
    }
    
    public function get_folder_read_users($id)
    {
        $this->db->where(array(
            "folder_id" => $id,
            "read" => "1"
        ));
        return $this->db->get("folder_permissions")->result_array();
    }
    
    
    
    
    public function get_folder_write_users($id)
    {
        $this->db->where(array(
            "folder_id" => $id,
            "write" => "1"
        ));
        return $this->db->get("folder_permissions")->result_array();
    }
    public function add_read_users($users = array(), $folder)
    {
        //delete all users from folder so we can readd the new ones
        $this->db->where(array(
            "folder_id" => $folder
        ));
        $this->db->delete("folder_permissions");
        
        //now add them all
        if ($users) {
            foreach ($users as $user_id) {
                $this->db->replace("folder_permissions", array(
                    "folder_id" => $folder,
                    "user_id" => $user_id,
                    "read" => 1
                ));
            }
        }
    }
    
    public function add_write_users($users = array(), $folder)
    {
        //if a user has the write permission they should also have the read permission
        if ($users) {
            foreach ($users as $user_id) {
                $this->db->replace("folder_permissions", array(
                    "folder_id" => $folder,
                    "user_id" => $user_id,
                    "read" => 1,
                    "write" => 1
                ));
            }
        }
    }
    
    
    
    public function file_from_id($id)
    {
        $query = "select filename,folder_name,date(date_added) subfolder from files left join folders using(folder_id) left join folder_permissions using(folder_id) where file_id = '" . intval($id) . "'";
        if ($_SESSION['role'] > 1) {
            $query .= " and folder_permissions.user_id = '" . $_SESSION['user_id'] . "'";
        }
        //$this->firephp->log($query);
        return $this->db->query($query)->row_array();
    }
    
    public function get_folders()
    {
        $folders = array();
        $this->db->select("folders.folder_id,folder_name,user_id,accepted_filetypes,`read`,`write`");
        $this->db->join("folder_permissions", "folders.folder_id=folder_permissions.folder_id", "LEFT");
        if ($_SESSION['role'] <> "1") {
            $this->db->where(array(
                "user_id" => $_SESSION['user_id'],
                "read" => "1"
            ));
        }
        $this->db->order_by("folder_name");
        foreach ($this->db->get("folders")->result_array() as $row) {
			if ($_SESSION['role']== "1") {
				$row['read']="1";
				$row['write']="1";
			}
            $folders[$row['folder_id']] = $row;
        }
        return $folders;
    }
    
    public function delete_file($id)
    {
        $this->db->where(array(
            "file_id" => $id
        ));
        $this->db->update("files", array(
            "deleted_on" => date('Y-m-d H:i:s'),
            "deleted_by" => $_SESSION['user_id']
        ));
        //set the filename to avoid dupes
        $this->db->query("update files set filename = concat(filename,'-deleted') where file_id = '" . intval($id) . "'");
    }
    
	 public function get_permissions($id){
		 	$qry = "select `read` read_access,`write` write_access,accepted_filetypes,folders.folder_id,folder_name from folder_permissions left join folders on folders.folder_id=folder_permissions.folder_id where folders.folder_id = $id and user_id = ".$_SESSION['user_id'];
			//$this->firephp->log($qry);
			return  $this->db->query($qry)->row_array();
}
    public function get_files_for_table($options,$count=false)
    {
        
        $table_columns = array(
            "folder_name",
            "filename",
            "filesize",
            "username",
            "date_format(date_added,'%d/%m/%y %H:%i')"
        );
        
        $order_columns = array(
            "folder_name",
            "filename",
            "filesize",
            "username",
            "date_added"
        );
        //admins dont need permissions set
		if($_SESSION['role']>1){
		$permissions = " `read`,`write` ";
		} else {
		$permissions = " '1' as `read`,'1' as `write` ";	
		}
		
        $qry = "select folder_name,filename,filesize,if(name is null,'Anonymous',name) as username,date_format(date_added,'%d/%m/%y %H:%i') date_uploaded,$permissions,accepted_filetypes,file_id,concat(folder_name,'/',date_format(date_added,'%Y-%m-%d')) path from files left join folders using(folder_id) left join users using(user_id) left join folder_permissions using(folder_id) ";
		
		
        $qry .= $this->get_where($options, $table_columns);
		if(isset($options['folder'])){
		$qry .= " and folder_id = ".$options['folder'];
		}
		$qry .= " and deleted_on is null ";
		//admins dont need permissions set
		if($_SESSION['role']>1){
		$qry .= " and folder_permissions.user_id = ".$_SESSION['user_id'];
		}
				
        $start  = $options['start'];
        $length = $options['length'];
        if (isset($_SESSION['files']['order']) && $options['draw'] == "1") {
            $order = $_SESSION['files']['order'];
        } else {
            $order = " order by CASE WHEN " . $order_columns[$options['order'][0]['column']] . " IS NULL THEN 1 ELSE 0 END," . $order_columns[$options['order'][0]['column']] . " " . $options['order'][0]['dir'] . ",filename";
            unset($_SESSION['files']['order']);
            unset($_SESSION['files']['values']['order']);
        }
        $qry .= " group by file_id ";
		if($count){
		return $files = $this->db->query($qry)->num_rows();	
		}
        $qry .= $order;
		
        $qry .= "  limit $start,$length";
		 $this->firephp->log($qry);
        $files = $this->db->query($qry)->result_array();
        $this->load->helper('scan');
        foreach ($files as $k => $row) {
            $files[$k]['filesize'] = formatSizeUnits($row['filesize']);
            $file_options          = '<button data-file="' . $row['file_id'] . '" class="btn btn-xs btn-default download-file"><span  class="glyphicon glyphicon-download pointer"></span> Zip</button>  <a href="'.base_url().'upload/files/'.$row['path'].'/'.$row['filename'].'" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-file"></span> Open</a>';
            if ($row['write'] == "1") {
                $file_options .= '<button data-file="' . $row['file_id'] . '" class="btn btn-xs btn-danger delete-file "><span  class="glyphicon glyphicon-remove"></span> Delete</button>';
            }
            $files[$k]['options'] = $file_options;
        }
        return $files;
        
    }
    
    
    public function get_where($options, $table_columns)
    {
        //the default condition in ever search query to stop people viewing campaigns they arent supposed to!
        $where = " where 1 ";
        
        //check the tabel header filter
        foreach ($options['columns'] as $k => $v) {
            //if the value is not empty we add it to the where clause
            if ($v['search']['value'] <> "") {
                $where .= " and " . $table_columns[$k] . " like '%" . $v['search']['value'] . "%' ";
            }
        }
        return $where;
    }
    
    public function get_files($folder, $showall = false)
    {
        $this->load->helper('scan');
        $limit = !$showall ? "limit 10" : "";
        $files = $this->db->query("select folder_id,file_id,folder_name,filename,date_format(date_added,'%d/%m/%Y %H:%i') date_added,filesize,if(username is null,'Anonymous',username) username from files left join folders using(folder_id) left join users using(user_id) where folder_id = '" . intval($folder) . "' and deleted_on is null order by date_added desc $limit")->result_array();
        foreach ($files as $k => $row) {
            $files[$k]['size'] = formatSizeUnits($row['filesize']);
        }
        return $files;
    }


    public function get_folder_by_name($folder_name)
    {
        $this->db->where(array(
            "folder_name" => $folder_name
        ));
        $query = $this->db->get('folders');
        if ($query->num_rows()) {
            return $query->row()->folder_id;
        }
    }
    
}
?>