<?php mysql_connect("localhost","root","") or die(mysql_error());
mysql_select_db("thinkmon_nps") or die(mysql_error());

//mysql_query("INSERT INTO `records` (select  urn, 1, NULL, NULL, NULL, 1, NULL, NULL, now(), NULL, NULL, NULL, 1, `GP Ref` from import_temp)") or die(mysql_error());

///mysql_query("insert into contacts (select  '', urn, `Customer Name`,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL from import_temp)") or die(mysql_error());
//mysql_query("insert into contact_addresses (select urn, urn, `First line of address`, NULL, NULL, NULL, NULL, `Postcode`, NULL, NULL,NULL from import_temp)") or die(mysql_error());

//mysql_query("insert into contact_telephone (select '', urn, `Home Tel`, 'Home', NULL from import_temp)") or die(mysql_error());
mysql_query("insert into contact_telephone (select '', urn, `Mobile Tel`, 'Mobile', NULL from import_temp)") or die(mysql_error());
mysql_query("insert into contact_telephone (select '', urn, `Partner Mobile Tel`, 'Partners Mobile', NULL from import_temp)") or die(mysql_error());
//mysql_query("insert into record_details (select urn, `Initial Advisor`, `PFM`, NULL, NULL from import_temp)") or die(mysql_error());
 ?>