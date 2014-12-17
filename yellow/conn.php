<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_salesdrive = "localhost";
$database_salesdrive = "121sys";
$username_salesdrive = "121sys";
$password_salesdrive = "Bu4XNm3Q6fqntySp";
$salesdrive = mysql_pconnect($hostname_salesdrive, $username_salesdrive, $password_salesdrive) or die(mysql_error());
mysql_select_db($database_salesdrive, $salesdrive) or die(mysql_error());

?>