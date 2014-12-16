<?php
session_start();
if(!isset($_SESSION['yell']['user'])){
$_SESSION['yell']['user'] = time();
}
include('conn.php');
include('dateclass.php');
include('functions.php');
include('phpQuery-onefile.php');

function gzdecoder($data) { 
  $len = strlen($data); 
  if ($len < 18 || strcmp(substr($data,0,2),"\x1f\x8b")) { 
    return null;  // Not GZIP format (See RFC 1952) 
  } 
  $method = ord(substr($data,2,1));  // Compression method 
  $flags  = ord(substr($data,3,1));  // Flags 
  if ($flags & 31 != $flags) { 
    // Reserved bits are set -- NOT ALLOWED by RFC 1952 
    return null; 
  } 
  // NOTE: $mtime may be negative (PHP integer limitations) 
  $mtime = unpack("V", substr($data,4,4)); 
  $mtime = $mtime[1]; 
  $xfl   = substr($data,8,1); 
  $os    = substr($data,8,1); 
  $headerlen = 10; 
  $extralen  = 0; 
  $extra     = ""; 
  if ($flags & 4) { 
    // 2-byte length prefixed EXTRA data in header 
    if ($len - $headerlen - 2 < 8) { 
      return false;    // Invalid format 
    } 
    $extralen = unpack("v",substr($data,8,2)); 
    $extralen = $extralen[1]; 
    if ($len - $headerlen - 2 - $extralen < 8) { 
      return false;    // Invalid format 
    } 
    $extra = substr($data,10,$extralen); 
    $headerlen += 2 + $extralen; 
  } 

  $filenamelen = 0; 
  $filename = ""; 
  if ($flags & 8) { 
    // C-style string file NAME data in header 
    if ($len - $headerlen - 1 < 8) { 
      return false;    // Invalid format 
    } 
    $filenamelen = strpos(substr($data,8+$extralen),chr(0)); 
    if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) { 
      return false;    // Invalid format 
    } 
    $filename = substr($data,$headerlen,$filenamelen); 
    $headerlen += $filenamelen + 1; 
  } 

  $commentlen = 0; 
  $comment = ""; 
  if ($flags & 16) { 
    // C-style string COMMENT data in header 
    if ($len - $headerlen - 1 < 8) { 
      return false;    // Invalid format 
    } 
    $commentlen = strpos(substr($data,8+$extralen+$filenamelen),chr(0)); 
    if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) { 
      return false;    // Invalid header format 
    } 
    $comment = substr($data,$headerlen,$commentlen); 
    $headerlen += $commentlen + 1; 
  } 

  $headercrc = ""; 
  if ($flags & 1) { 
    // 2-bytes (lowest order) of CRC32 on header present 
    if ($len - $headerlen - 2 < 8) { 
      return false;    // Invalid format 
    } 
    $calccrc = crc32(substr($data,0,$headerlen)) & 0xffff; 
    $headercrc = unpack("v", substr($data,$headerlen,2)); 
    $headercrc = $headercrc[1]; 
    if ($headercrc != $calccrc) { 
      return false;    // Bad header CRC 
    } 
    $headerlen += 2; 
  } 

  // GZIP FOOTER - These be negative due to PHP's limitations 
  $datacrc = unpack("V",substr($data,-8,4)); 
  $datacrc = $datacrc[1]; 
  $isize = unpack("V",substr($data,-4)); 
  $isize = $isize[1]; 

  // Perform the decompression: 
  $bodylen = $len-$headerlen-8; 
  if ($bodylen < 1) { 
    // This should never happen - IMPLEMENTATION BUG! 
    return null; 
  } 
  $body = substr($data,$headerlen,$bodylen); 
  $data = ""; 
  if ($bodylen > 0) { 
    switch ($method) { 
      case 8: 
        // Currently the only supported compression method: 
        $data = gzinflate($body); 
        break; 
      default: 
        // Unknown compression method 
        return false; 
    } 
  } else { 
    // I'm not sure if zero-byte body content is allowed. 
    // Allow it for now...  Do nothing... 
  } 

  // Verifiy decompressed size and CRC32: 
  // NOTE: This may fail with large data sizes depending on how 
  //       PHP's integer limitations affect strlen() since $isize 
  //       may be negative for large sizes. 
  if ($isize != strlen($data) || crc32($data) != $datacrc) { 
    // Bad format!  Length or CRC doesn't match! 
    return false; 
  } 
  return $data; 
} 


$keyword = @$_POST['keyword'];
$location = @$_POST['location'];

if(@$_POST['submit']=="Export"){
$local_only = $_POST['local_only'];
$website_only = $_POST['website_only'];
$sector = mysql_real_escape_string($_POST['sector']);
$export_query = "select * from freedata where user_id = '{$_SESSION['yell']['user']}' ";
if($local_only=="1"){  $export_query .= " and local = '1'"; }
if($website_only=="1"){  $export_query .= " and website <> ''"; }
if(!empty($sector)){  $export_query .= " and sector_name ='$sector'"; }
$run = mysql_query($export_query);
while($row = mysql_fetch_assoc($run)){
unset($headers);
foreach ($row as $k=>$v){
	if($k<>$_SESSION['yell']['user']){
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

ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT
5.1)');
if(!empty($keyword)&&!empty($location)){
$pages=0;
$_SESSION['yell']['location'] = $location;	
$_SESSION['yell']['keyword'] = $keyword;
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
  `sector_name`  varchar(100) default NULL,
  PRIMARY KEY  (`data_id`),
  UNIQUE KEY `coname` (`coname`,`phone`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1") or die(mysql_error()) ;
mysql_query("delete from freedata where user_id = '{$_SESSION['yell']['user']}'");
	
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Host: www.yell.com\r\n" .
              "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0\r\n" .
			  "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
			  "Accept-Language: en-US,en;q=0.5\r\n" .
			  "x-insight: activate\r\n" .
			  "Connection: keep-alive\r\n" .
			  "Accept-Encoding: gzip, deflate\r\n".
			  "Cache-Control: max-age=0")
);
$count = 1;
$context = stream_context_create($opts);

$original = file_get_contents("http://www.yell.com/ucs/UcsSearchAction.do?keywords=$keyword&location=$location",false,$context);
$decode = gzdecoder($original);
$html = ($decode?$decode:$original);
$dom = phpQuery::newDocumentHTML($html);
foreach($dom->find('.results_headercount') as $count) {
$results = preg_replace('/[^\d]/i','',pq($count)->text());
$pages = ceil($results/10);
}
for($page=1;$page<$pages;$page++){
$context = "";
$context = stream_context_create($opts);
$original = file_get_contents("http://www.yell.com/ucs/UcsSearchAction.do?keywords=$keyword&location=$location&pageNum=$page",false,$context);
$decode = gzdecoder($original);
$html = ($decode?$decode:$original);
$dom = phpQuery::newDocumentHTML($html);
foreach($dom->find('.parentListing')  as $companies) { $count++; 
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
$dupe_array = array();
if(!in_array(strtolower($coname).$tel,$dupe_array)){
$insert_query = "insert ignore into freedata set coname='$coname',
add1='$add1',
add2='$add2',
add3='$add3',
postcode='$postcode',
phone='$tel',
mobile='$mob',
`website`='$website',
`local`='$local',
`sector_name` = '$sector',
user_id = '{$_SESSION['yell']['user']}'";
//echo ";<br>";
mysql_query($insert_query) or die(mysql_error());
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
exit;
}
$q = mysql_query("select * from freedata where user_id = '{$_SESSION['yell']['user']}'");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>121 Yellow Pages Scraper</title>
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
<input name="keyword" id="keyword" value="<?php echo @$_SESSION['yell']['keyword'] ?>"/>
Location? eg.Manchester or M5
<input name="location" id="location" value="<?php echo @$_SESSION['yell']['location'] ?>"/>
<input type="submit" id="scrape_submit" value="Search" /> <?php if (@!mysql_num_rows($q)){ $disabled="disabled"; } ?>
<button id="export" <?php echo $disabled ?> >Export to file</button>  <!--<button id="import" <?php echo $disabled ?> >Import to Campaign</button>-->
</form>
<hr />

<div id="export_div" style="display:none; background:#ddd; padding:10px">
<form method="post">
<p><label>Sectors</label> 
<select id="sector" name="sector"><option value="">--All of them--</option>
<?php $sector_q = mysql_query("select distinct sector_name from freedata");
while($sector = mysql_fetch_assoc($sector_q)){
?>
<option value="<?php echo $sector['sector_name'] ?>"><?php echo $sector['sector_name'] ?></option>
<?php } ?>
</select></p>
<p><label>Filename</label> <input style="width:200px" type="text" name="filename" value="<?php echo date('ymd').ucfirst($_SESSION['yell']['keyword']).ucfirst($_SESSION['yell']['location']).".csv" ?>" /></p>
<p><label>Local Only</label> <input type="checkbox" name="local_only" value="1" /></p>
<p><label>With Website Only</label> <input type="checkbox" name="website_only" value="1" /></p>

<p><input type="submit" name="submit" value="Export" /></p>
</form>
</div>

<div id="loading" style="display:none">
<p>Please wait, this may take a minute or two depending on how broad your search is!<br />
<img  src="loading_bar.gif" /></p></div>
<div id="table_div">
<?php if (@mysql_num_rows($q)>0){ ?><h4>Results : <?php echo @mysql_num_rows($q) ?></h4>
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
<?php
while($row = mysql_fetch_assoc($q)){ ?>
<tr><td><?php echo $row['coname'] ?></td>
<td><?php echo $row['sector_name'] ?></td>
<td><?php echo ($row['local']=="1"?"Yes":"No"); ?></td>
<td><?php echo $row['phone'] ?></td>
<td><?php echo $row['mobile'] ?></td>
<td><?php echo $row['website'] ?></td>
<td><?php echo $row['add1'] ?></td>
<td><?php echo $row['add2'] ?></td>
<td><?php echo $row['add3'] ?></td>
<td><?php echo $row['postcode'] ?></td>
</tr>

<?php } ?>
</tbody></table>
<?php } ?>
</div>
</body>
</html>