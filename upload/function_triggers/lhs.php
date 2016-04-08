<?php 

$servername = '46.37.175.114';
$username = 'one2one_lhs';
$password = 'Og0ZnpvE2pus';
$database = 'one2one_lhs';

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

mysqli_select_db($conn, $database);

$qry = "select * from users";

print_r($db>query($qry));

?> 
