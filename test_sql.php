<?php
// Server in the this format: <computer>\<instance name> or 
// <server>,<port> when using a non default port number
$server = 'unit34phonesvr\SQLEXPRESS';

// Connect to MSSQL
$link = mssql_connect($server, 'postgres', 'postgres');

if (!$link) {
    die('Something went wrong while connecting to MSSQL');
}
?>