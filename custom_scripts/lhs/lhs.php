<?php

define("BASEPATH", "../../system/");
include("../../application/config/database.php");

ob_start();
include("../../session.php");

//Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array(
        "success" => false,
        "msg" => "You should be logged"
    ));
    header("Location: ".substr(BASEPATH,0,strlen(BASEPATH)-7));
    exit;
}

$data = ob_get_clean();
ob_end_clean();

$hostname = $db['default']['hostname'];
$username = $db['default']['username'];
$password = $db['default']['password'];
$database = $db['default']['database'];

// Create connection
$conn = new mysqli($hostname, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

mysqli_select_db($conn, $database);

$action = $_POST["action"];

switch ($action) {
    case "update_appointment_title":
        $sql = "UPDATE appointments SET title='".$_POST['title']."' WHERE appointment_id=".$_POST['appointment_id'];

        if ($conn->query($sql) === TRUE) {
            echo json_encode(array(
                "success" => true,
                "msg" => "Appointment title updated successfully"
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "msg" => "Error updating the appointment: " . $conn->error
            ));
        }
        break;
	    case "create_job_number":
		$sql = "select `value` from custom_panel_values where field_id = 1 and `value` like 'LH%' order by `value` limit 1";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();	
		$job_number = "LH".date('y')."-".(intval(str_replace("LH".date('y')."-","",$row['value']))+1);		
		} else {
		$job_number = "LH".date('y')."-1";	
		}
		$update = "";
		$check = "select `value` from custom_panel_values where field_id = 1 and `value` like 'LH%' and data_id = '".$_POST['data_id']."'";
		$exists = $conn->query($check);
		if($exists->num_rows == 0) {
		$update = "insert into custom_panel_values set data_id = '{$_POST['data_id']}',field_id='1',`value`='$job_number'";
		$conn->query($update);
		}
		echo json_encode(array("success"=>true,"job_number"=>$job_number,"query"=>$update));
		break;
		case "clear_job_number":
		$sql = "update custom_panel_values set `value` = '' where field_id = 1 and data_id = '{$_POST['data_id']}'";
		$result = $conn->query($sql);
		break;
}
?> 
