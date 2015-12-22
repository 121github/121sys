<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Database extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
		if(isset($_SESSION['user_id'])){
        user_auth_check(false);
        $this->_campaigns = campaign_access_dropdown();
		}
        $this->load->model('Database_model');
    }
//misc db functions

//clean contact names
public function clean_contact_names(){
$this->db->query("update contacts set fullname = trim(replace(fullname,'  ',' '))");
}

public function clean_numbers(){
$dirty = $this->db->query("SELECT *
FROM company_telephone
WHERE telephone_number REGEXP '[[.NUL.]-[.US.]]'")->result_array();
foreach($dirty as $row){
$query = "update company_telephone set telephone_number = '".preg_replace("/[^A-Za-z0-9 ]/", '', $row['telephone_number'])."', description = '".preg_replace("/[^A-Za-z0-9 ]/", '', $row['description'])."' where telephone_id = '".$row['telephone_id']."'";	
$this->db->query($query);
}

$dirty = $this->db->query("SELECT *
FROM contact_telephone
WHERE telephone_number REGEXP '[[.NUL.]-[.US.]]'")->result_array();
foreach($dirty as $row){
$query = "update contact_telephone set telephone_number = '".preg_replace("/[^A-Za-z0-9 ]/", '', $row['telephone_number'])."', description = '".preg_replace("/[^A-Za-z0-9 ]/", '', $row['description'])."' where telephone_id = '".$row['telephone_id']."'";	
$this->db->query($query);
}

}
	public function remove_dupes_now(){
		$this->remove_dupes(true);
	}
	public function remove_dupes($now=false){
		$table=$this->uri->segment(3);
			$field1=$this->uri->segment(4);
			$field2=$this->uri->segment(5);
			$field3=$this->uri->segment(6);
			$concat=array();
			if(!empty($field1)){
			$concat[]=$field1;
			}
			if(!empty($field2)){
			$concat[]=$field2;
			}
			if(!empty($field3)){
			$concat[]=$field3;
			}
			
			$fields = implode(",",$concat);
			$query = "SELECT concat( $fields ) ref , count( * ) count
FROM `$table`
GROUP BY concat( $fields )
HAVING count( concat( $fields ) ) >1";
$result = $this->db->query($query)->result_array();
foreach($result as $row){
$remove = $row['count']-1;
echo $delete = "delete from $table where concat($fields) = '".addslashes($row['ref'])."' limit $remove";	
echo ";<br>";	
if($now){
$this->db->query($delete);	
}
}
	}


    public function index()
    {
        //get current version
        $currentVersion = $this->Database_model->get_version();

        //Update the schema
        $this->load->library('migration');

        //If the version before update the schema did not exist, dump the init data
        if ($currentVersion == "Unknown") {
            $this->Database_model->init_data();
        }

        //Get the version after update the schema
        $version = $this->Database_model->get_version();
        $data = array(
            'page' => 'database',
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Database-management',
            'version' => $version,
            'title' => 'Database management',
            'css' => array(
                'dashboard.css'
            )
        );

        $this->template->load('default', 'database/view_db.php', $data);
    }


    public function drop_tables()
    {
        if ($this->Database_model->drop_tables()) {
            echo json_encode(array("success" => true, "msg" => "Tables were dropped successfully"));
        } else {
            echo json_encode(array("success" => false, "msg" => "Tables could not be dropped"));
        }

    }

    public function add_data()
    {
        session_write_close();
        $status = $this->Database_model->demo_data();
        if ($status == "success") {
            echo json_encode(array("success" => true, "msg" => "Sample data was added"));
        } else {
            echo json_encode(array("success" => false, "msg" => "Sample data could not be added. Failed on $status table"));
        }

    }

    public function add_real_data()
    {
        session_write_close();
        $status = $this->Database_model->real_data();
        if ($status == "success") {
            echo json_encode(array("success" => true, "msg" => "Real data was added"));
        } else {
            echo json_encode(array("success" => false, "msg" => "Real data could not be added. Failed on $status table"));
        }

    }

    public function reset_data()
    {
        session_write_close();
        $status = $this->Database_model->init_data();
        $db = $this->db->database;
        if ($status == "success") {
            echo json_encode(array("success" => true, "msg" => "The default data was restored"));
        } else {
            echo json_encode(array("success" => false, "msg" => "Error restoring the default data. Failed on $status table"));
        }

    }

}