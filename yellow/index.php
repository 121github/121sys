<?
require_once('conn.php');
include('dateclass.php');
include('functions.php');
include('theme.php');
include('phpQuery-onefile.php');
$keyword = $_POST['keyword'];

$location = $_POST['location'];

if($_POST['submit']=="Export"){
$local_only = $_POST['local_only'];
$website_only = $_POST['website_only'];
$sector = mysql_real_escape_string($_POST['sector']);
$export_query = "select * from freedata where user_id = '$user_id' ";
if($local_only=="1"){  $export_query .= " and local = '1'"; }
if($website_only=="1"){  $export_query .= " and website <> ''"; }
if(!empty($sector)){  $export_query .= " and sector_name ='$sector'"; }
$run = mysql_query($export_query);
while($row = mysql_fetch_assoc($run)){
unset($headers);
foreach ($row as $k=>$v){
	if($k<>"user_id"){
$headers .= $k.",";	
$data .= str_replace(",","",$v).",";
	}
}
$data = rtrim($data,",")."\n";
}
$out = rtrim($headers,",")."\n";
$out .= $data;

header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=".$_POST['filename']);
	echo $out;
	exit;	
}


if(!empty($keyword)&&!empty($location)){
$_SESSION['location'] = $location;	
$_SESSION['keyword'] = $keyword;
mysql_query("CREATE TABLE IF NOT EXISTS `freedata` (
  `data_id` int(11) NOT NULL auto_increment,
  `coname` varchar(150) default NULL,
  `local` int(1) NOT NULL,
  `phone` varchar(17) default NULL,
  `mobile` varchar(17) default NULL,
  `website` varchar(200) default NULL,
  `add1` varchar(100) default NULL,
  `add2` varchar(100) default NULL,
  `add3` varchar(100) default NULL,
  `postcode` varchar(15) default NULL,
  `user_id` int(3) default NULL,
  PRIMARY KEY  (`data_id`),
  UNIQUE KEY `coname` (`coname`,`phone`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
mysql_query("delete from freedata where user_id = '$user_id'");
	
$yell = file_get_contents("http://www.yell.com/ucs/UcsSearchAction.do?keywords=$keyword&location=$location");
$dom = phpQuery::newDocumentHTML($yell);
foreach($dom->find('.results_headercount')  as $count) {
$results = preg_replace('/[^\d]/i','',pq($count)->text());
$pages = ceil($results/10);
}

for($page=1;$page<$pages;$page++){
$content = file_get_contents("http://www.yell.com/ucs/UcsSearchAction.do?keywords=$keyword&location=$location&pageNum=$page");
$html = phpQuery::newDocumentHTML($content);

foreach($html->find('.parentListing')  as $companies) { $count++; 
$local = false;
$website="";
$add1="";
$add2="";
$add3="";
$sector="";
$postcode="";
$mob="";
$tel="";
$logo="";
$coname="";
$company = pq($companies);
foreach(pq($company)->find('li[data-company-item="telephone"]')  as $phone) { 
if (preg_match("/tel/i", pq($phone)->text())) {
$tel = 	preg_replace('/[^\d]/i','',pq($phone)->text());
};
if (preg_match("/mob/i", pq($phone)->text())) {
$mob = 	preg_replace('/[^\d]/i','',pq($phone)->text());
};
if ($mob||preg_match("/^01|^07|^02/", $tel)) {
$local = true;
};
}
foreach(pq($company)->find('a[itemprop="name"]')  as $name) { 
$coname = mysql_real_escape_string(trim(pq($name)->text()));
}
foreach(pq($company)->find('.keywords')  as $sector) { 
$sectors = explode("|",pq($sector)->text());
$sector = mysql_real_escape_string(trim($sectors[0]));
}

foreach(pq($company)->find('span[itemprop="streetAddress"]')  as $address) { 
$add1 =  mysql_real_escape_string(str_replace(",","",pq($address)->text()));
}
foreach(pq($company)->find('span[itemprop="addressLocality"]')  as $locality) { 
$add2 = pq($locality)->text();
}
foreach(pq($company)->find('span[itemprop="postalCode"]')  as $postcode) { 
$postcode = pq($postcode)->text();
}
foreach(pq($company)->find('a[data-target-weblink]')  as $website) { 
$website = mysql_real_escape_string(pq($website)->attr("href"));
}

if(!in_array(strtolower($coname).$tel,$dupe_array)){
$insert_query = "replace into freedata set coname='$coname',
add1='$add1',
add2='$add2',
add3='$add3',
postcode='$postcode',
phone='$tel',
mobile='$mod',
`website`='$website',
`local`='$local',
`sector_name` = '$sector',
user_id = '$user_id'";
//echo ";<br>";
mysql_query($insert_query);// or die(mysql_error());
$dupe_array[] = strtolower($coname).$tel;
}


//$array[strtolower($coname).$tel] = array("coname"=>$coname,"add1"=>$add1,"add2"=>$add2,"add3"=>$add3,"postcode"=>$postcode,"tel"=>$tel,"mob"=>$mob,"website"=>$website,"local"=>$local); 

}
}
/*
foreach ($array as $k=>$v){
$insert_query = "insert into freedata set coname='{$v['coname']}',
add1='{$v['add1']}',
add2='{$v['add2']}',
add3='{$v['add3']}',
postcode='{$v['postcode']}',
phone='{$v['tel']}',
mobile='{$v['mob']}',
`website`='{$v['website']}',
`local`='{$v['local']}'";
//echo ";<br>";
mysql_query($insert_query);// or die(mysql_error());
}
*/
}
$q = mysql_query("select * from freedata where user_id = '$user_id'");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Salesdrive Free Data</title>
<link rel="stylesheet" href="css/theme.green.css">
<link rel="stylesheet" href="css/jquery-ui-1.10.1.custom.min.css">
<style>
body { font-family:Verdana, Geneva, sans-serif; padding-top:5px; }

</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
 <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.pager.min.js"></script>
<script src="js/jquery.tablesorter.widgets.min.js"></script>
<script>
$(document).ready(function(){
	function tsorter(){
	$(".tablesorter").tablesorter({

                theme: "green", // this will 

                widthFixed: true,

                headerTemplate: '{content}', // new in v2.7. Needed to add the bootstrap icon!

                widgets: ["uitheme", "zebra"],

                widgetOptions: {

                    zebra: ["even", "odd"],

                    filter_reset: ".reset",

                }

            });
	}
	
	tsorter();
			$('#import').click(function(e){
				e.preventDefault();
				$('#export_div').hide();
				$('#import_div').toggle();
			});
			$('#export').click(function(e){
				e.preventDefault();
				$('#import_div').hide();
				$('#export_div').toggle();
			});
			
			$('#scrape_submit').click(function(e){
				e.preventDefault();
				$('#import_div').hide();
				$('#export_div').hide();
				var keyword_val = $('#keyword').val();
				var location_val = $('#location').val();
				$('#table_div').hide();
				$('#table').remove();
				$('#loading').show();
				
				$.ajax({url:"index.php",data:{keyword:keyword_val,location:location_val},type:"POST" }).done(function(result){
				$('#table_div').load("index.php #table",function(){ $('#loading').hide(); $('#table_div').show();
				tsorter();
				 });
				});
			});
			
			$('#campaign_id').change(function(){
				if($(this).val()==""){
				$('#campaign_div').show();	
				} else { $('#campaign_div').hide(); $('#campaign').val('');}
			});
			
});
</script>
</head>

<body>
<!--<a style="position:absolute; left:5px; top:5px; font-size:12px" href="lead_manager.php">Back to Salesdrive</a>-->
<img src="yell.gif" />
<form method="post" id="scrape_form" style="padding:15px 5px">
What do you want to find? eg. Plumber
<input name="keyword" id="keyword" value="<? echo $_SESSION['keyword'] ?>"/>
Location? eg.Manchester or M5
<input name="location" id="location" value="<? echo $_SESSION['location'] ?>"/>
<input type="submit" id="scrape_submit" value="Search" /> <? if (!mysql_num_rows($q)){ $disabled="disabled"; } ?>
<button id="export" <? echo $disabled ?> >Export to file</button>  <!--<button id="import" <? echo $disabled ?> >Import to Campaign</button>-->
</form>
<hr />

<div id="export_div" style="display:none; background:#ddd; padding:10px">
<form method="post">
<p><label>Sectors</label> 
<select id="sector" name="sector"><option value="">--All of them--</option>
<? $sector_q = mysql_query("select distinct sector_name from freedata");
while($sector = mysql_fetch_assoc($sector_q)){
?>
<option value="<? echo $sector['sector_name'] ?>"><? echo $sector['sector_name'] ?></option>
<? } ?>
</select></p>
<p><label>Filename</label> <input style="width:200px" type="text" name="filename" value="<? echo date('ymd').ucfirst($_SESSION['keyword']).ucfirst($_SESSION['location']).".csv" ?>" /></p>
<p><label>Local Only</label> <input type="checkbox" name="local_only" value="1" /></p>
<p><label>With Website Only</label> <input type="checkbox" name="website_only" value="1" /></p>

<p><input type="submit" name="submit" value="Export" /></p>
</form>
</div>


<div id="import_div" style="display:none;background:#ddd; padding:10px">
<form method="post">
<p><label>Assign To</label> 
<select name="user_id">
<? $uq = mysql_query("select user_id,client_name from tbl_users");
while($user = mysql_fetch_assoc($uq)){
?>
<option value="<? echo $user['user_id'] ?>"><? echo $user['client_name'] ?></option>
<? } ?>
</select></p>
<p><label>Sectors</label> 
<select id="sector" name="sector"><option value="">--All of them--</option>
<? $sector_q = mysql_query("select distinct sector_name from freedata");
while($sector = mysql_fetch_assoc($sector_q)){
?>
<option value="<? echo $sector['sector_name'] ?>"><? echo $sector['sector_name'] ?></option>
<? } ?>
</select></p>
<p><label>Campaign</label> 
<select id="campaign_id" name="campaign_id"><option value="">--Please Select--</option><option value="">--Create New--</option>
<? $cq = mysql_query("select campaign_id,campaign_name from tbl_campaigns");
while($campaign = mysql_fetch_assoc($cq)){
?>
<option value="<? echo $campaign['campaign_id'] ?>"><? echo $campaign['campaign_name'] ?></option>
<? } ?>
</select></p>
<p id="campaign_div" style="display:none">
<label> Create New Campaign</label>
<input style="width:200px;" type="text" id="campaign" name="campaign" value="<? echo date('ymd').ucfirst($_SESSION['keyword']).ucfirst($_SESSION['location']).".csv" ?>" /></p>

<p><label>Local Only</label> <input type="checkbox" name="local_only" value="1" /></p>
<p><label>With Website Only</label> <input type="checkbox" name="website_only" value="1" /></p>
<p><label>Allow Duplicates</label> <input type="checkbox" name="duplicates" value="1"/></p>
<p><input type="submit" name="submit" value="Import" /></p>
</form>
</div>
<div id="loading" style="display:none">
<p>Please wait, this may take a minute or two depending on how broad your search is!<br />
<img  src="loading_bar.gif" /></p></div>
<div id="table_div">
<h4>Results : <? echo mysql_num_rows($q) ?></h4>
<table class="tablesorter" id="table">
<thead>
<th>Company</th>
<th>Sector</th>
<th>Local</th>
<th>Phone</th>
<th>Mobile</th>
<th>Website</th>
<th>Add1</th>
<th>Add2</th>
<th>Add3</th>
<th>Postcode</th>
</thead><tbody>
<? 
while($row = mysql_fetch_assoc($q)){ ?>
<tr><td><? echo $row['coname'] ?></td>
<td><? echo $row['sector_name'] ?></td>
<td><? echo ($row['local']=="1"?"Yes":"No"); ?></td>
<td><? echo $row['phone'] ?></td>
<td><? echo $row['mobile'] ?></td>
<td><? echo $row['website'] ?></td>
<td><? echo $row['add1'] ?></td>
<td><? echo $row['add2'] ?></td>
<td><? echo $row['add3'] ?></td>
<td><? echo $row['postcode'] ?></td>
</tr>

<? } ?>
</tbody></table>
</div>
</body>
</html>