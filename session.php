<?php
session_start();

if(isset($_GET['logout'])){
 session_destroy();
}

if(isset($_GET['login'])){
 $_SESSION['login']= $_GET['login'];
}

echo "<pre>";
print_r($_SESSION);
echo "</pre>";


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
