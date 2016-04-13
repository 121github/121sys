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
}
?> 
