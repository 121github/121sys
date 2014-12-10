<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
session_start();
if (empty($_SESSION['db_name'])){ session_destroy(); };
$hostname_salesdrive = "localhost";
$database_salesdrive = "121sys";
$username_salesdrive = "121sys";
$password_salesdrive = "w5Kc8GADw49q24eP";
$salesdrive = mysql_pconnect($hostname_salesdrive, $username_salesdrive, $password_salesdrive) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_select_db($database_salesdrive, $salesdrive);

?>