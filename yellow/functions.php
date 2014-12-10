<?php

function uk_to_mysql($date){
$timestamp = strtotime(str_replace('/', '.', $date));
return date('Y-m-d', $timestamp);
}

function remove_non_numeric($string) {
return preg_replace('/\D/', '', $string);
}

function get_users($system_url,$ownrep,$users){
$query_string = "select * from tbl_users where 1";
if($ownrep){ $query_string .= " and repgroup_id = 1"; }
if($users){ $query_string .= " and user_group = 3"; }
$query = mysql_query($query_string) or die(mysql_error());
	while ($row = mysql_fetch_assoc($query)){
		$array[$row['user_id']] = $row['client_name'];
	}
	return $array;
}

// ======================================
function array_to_list($array){
// ======================================
foreach($array as $val){
$list_tmp .= $val.",";	
}
return rtrim($list_tmp,",");
}
// ======================================
// ======================================
function sector_array($user,$live){
// ======================================
global $new_outcome;
	$query_string = "select distinct subsector_id, subsector_name, sector_name,tbl_sectors.sector_id from tbl_sectors left join tbl_subsectors using(sector_id) left join tbl_leads using(subsector_id) left join tbl_ownership using(urn) where 1 ";
	
	if (!empty($user)){ $query_string .= " and user_id ='$user' "; }
	if (!empty($live)){ $query_string .= " and lead_status=1 and (nextcall <= now() or outcome_id = '$new_outcome')"; } 
	$query_string .= "order by sector_name, subsector_name";
	$query=mysql_query($query_string);
	

while($row = mysql_fetch_assoc($query)){
$array[$row['sector_id']]['sector_name']=$row['sector_name'];
if ($row['subsector_id']) {
$array[$row['sector_id']][$row['subsector_id']]=$row['subsector_name'];	}
}
return $array;
}
// ======================================

// ======================================
function outcome_array($user,$live,$group){
// ======================================
global $new_outcome;
	$query_string = "select distinct outcome_id,outcome from tbl_outcomes  left join tbl_leads using(outcome_id) left join tbl_ownership using(urn) left join tbl_outcomes_groups using(outcome_id) where 1 and (enable_select=1 or outcome = 'New') ";
	
	if (!empty($user)){ $query_string .= " and user_id ='$user' "; }
	if (!empty($live)){ $query_string .= " and lead_status=1 "; } 
	if (!empty($group)){ $query_string .= " and group_id = '$group'"; } 
	$query_string .= " order by outcome";
	$query=mysql_query($query_string);
	

while($row = mysql_fetch_assoc($query)){
	if (in_array($row['outcome'],$array)){ $array["dupes"][$row['outcome_id']] = $row['outcome']; } else {
$array[$row['outcome_id']]=$row['outcome'];
	}
}
return $array;
}

// ======================================

// ======================================
function campaign_array($user,$live){
// ======================================
global $new_outcome;
	$query_string = "select distinct campaign_id, campaign_name from tbl_campaigns left join tbl_leads using(campaign_id) left join tbl_ownership using(urn) where 1  ";
	
	if (!empty($user)){ $query_string .= " and user_id ='$user' "; }
	if (!empty($live)){ $query_string .= " and lead_status=1 "; } 
	$query_string .= " order by campaign_name";
	$query=mysql_query($query_string);
	

while($row = mysql_fetch_assoc($query)){
$array[$row['campaign_id']]=$row['campaign_name'];
}
return $array;
}

// ======================================


// ======================================
function outcome_groups_array($outcome){
// ======================================
$query = mysql_query("select group_id,group_name from tbl_outcomes natural left join tbl_outcomes_groups natural left join tbl_groups
 where outcome_id = '$outcome'");
while($row = mysql_fetch_assoc($query)){
$array[$row['group_name']]=$row['group_id'];	
}
return $array;
}
// ======================================
// ======================================
function outcome_groups_formatted($outcome){
// ======================================
$query = mysql_query("select group_id,group_name from tbl_outcomes natural left join tbl_outcomes_groups natural left join tbl_groups
 where outcome_id = '$outcome'");
while($row = mysql_fetch_assoc($query)){
$array[$row['group_name']]=$row['group_id'];	
}
foreach ($array as $k => $v){
$outcomes .= $k.", ";	
}
return rtrim($outcomes,", ");
}
// ======================================
// ======================================
function get_origin($urn){
$query = mysql_query("select client_name,campaign_type, campaign_name from tbl_leads  left join tbl_history using(urn) left join tbl_users using(user_id) left join tbl_campaigns using(campaign_id) where urn = '$urn' order by contact asc limit 0,1");
$row = mysql_fetch_assoc($query);
if ($row['client_name']) {$client_name = " : ".$row['client_name']; }
if ($row['campaign_type'] == "Outbound") { return "Outbound". $client_name; } else {return $row['campaign_name']; }
}
// ======================================
function id_to_name($id,$type){
    $query = mysql_query("select username,client_name from tbl_users where user_id='$id'");
    $row = mysql_fetch_assoc($query);
    if ($type=="fullname"){ return $row['client_name']; }
    else { return $row['username']; }
}

// ======================================
function get_all_managers(){
// --------------------------------------
	// Managers
	$managerArray = array();
	$query_rsManager = "SELECT user_id,client_name,initials,user_email FROM `tbl_users` u where login_mode <> 'dev' or login_mode is null ORDER BY client_name ASC";
	$rsManager = mysql_query($query_rsManager) or die(mysql_error());
	while($row_rsManager = mysql_fetch_assoc($rsManager)){
		$managerArray[$row_rsManager['user_id']]['user_id'] 		= $row_rsManager['user_id'];
		$managerArray[$row_rsManager['user_id']]['name'] 		= $row_rsManager['client_name'];
		$managerArray[$row_rsManager['user_id']]['initials'] 	= $row_rsManager['initials'];
		$managerArray[$row_rsManager['user_id']]['email'] 		= $row_rsManager['user_email'];
	}
	return $managerArray;
}
// ======================================
function get_last_comment($urn){
// --------------------------------------
	$query = "SELECT urn, comments from tbl_history where comments <> '' and comments is not null and urn = '$urn' order by history_id desc limit 0,1";
$lastcomment_query = mysql_query($query);
$comment = mysql_fetch_assoc($lastcomment_query);
return $comment['comments'];
}

function get_last_outcome($urn){
// --------------------------------------
	$query = "SELECT urn, outcome_id from tbl_history where outcome_id is not null and urn = '$urn' order by history_id desc limit 0,1";
$lastoutcome_query = mysql_query($query);
$comment = mysql_fetch_assoc($lastoutcome_query);
return $comment['outcome_id'];
}
// ======================================

function get_last_nextcall($urn){
// --------------------------------------
	$query = "SELECT urn, nextcall from tbl_history where year(nextcall) <> 0 and urn = '$urn' order by history_id desc limit 0,1";
$lastoutcome_query = mysql_query($query);
$comment = mysql_fetch_assoc($lastoutcome_query);
return $comment['nextcall'];
}
// ======================================
function get_last_comments(){
// --------------------------------------

	// Last comments
	$commentsArray = array();
	//$query = "SELECT urn,comments FROM `tbl_history` where id in (select max(id) from tbl_history where comments is not null group by urn)  ";
	// Fix to get rid of strange Â character - something to do with UTF-8 encoding
	$query = "SELECT urn, history_id,replace(comments,'Â£','£') as fmtcomments
FROM tbl_history
INNER JOIN (

SELECT max(history_id ) AS maxid
FROM tbl_history
GROUP BY urn
) AS maxy ON maxy.maxid = tbl_history.history_id
where comments is not null";
	$result = mysql_query($query) or die(mysql_error());
	while($row=mysql_fetch_assoc($result)){
		$commentsArray[$row['urn']] = html_entity_decode($row['fmtcomments']);
		//echo $row['fmtcomments'];
		//echo "<br/>";
	}
	
	return $commentsArray;
}
// ======================================

// ======================================
function get_last_comments_pdf(){
// --------------------------------------

	// Last comments
	$commentsArray = array();
	//$query = "SELECT urn,comments FROM `tbl_history` where id in (select max(id) from tbl_history where comments is not null group by urn)  ";
	// Fix to get rid of strange Â character - something to do with UTF-8 encoding
		$query = "SELECT urn, history_id,replace(comments,'Â£','£') as fmtcomments
FROM tbl_history
INNER JOIN (

SELECT max( history_id ) AS maxid
FROM tbl_history
GROUP BY urn
) AS maxy ON maxy.maxid = tbl_history.history_id
where comments is not null";
	$result = mysql_query($query) or die(mysql_error());
	while($row=mysql_fetch_assoc($result)){
		$commentsArray[$row['urn']] = utf8_decode($row['fmtcomments']);
	}
	
	return $commentsArray;
}
// ======================================
function get_owners($urn){
// --------------------------------------
$ownership_query = mysql_query("select user_id,client_name from tbl_users natural left join tbl_ownership where urn ='$urn'");
while ($row = mysql_fetch_assoc($ownership_query)) {
$owners[$row['user_id']]=$row['client_name'];
}
return $owners;
}

// ======================================
function show_img_flag($status){
// --------------------------------------
	switch($status){
		case "Amber":
			$image = "flag_orange.png";	
			break;
		case "Green":
			$image = "flag_green.png";	
			break;
		case "Red":
			$image = "flag_red.png";	
			break;
		
	}
	
	if($image){
		echo "<img src='images/{$image}' border=0 />";
	} else {
		echo "&nbsp;";
	}
}
// ======================================



// ======================================
function show_file_icon($fileName){
// --------------------------------------

	$ext = strtolower(substr($fileName, strrpos($fileName, '.') + 1));
	
	switch($ext){
		case "bmp":
		case "jpg":
		case "jpeg":
		case "gif":
		case "png":
			$image = "photo.png";	
			break;
		case "doc":
		case "docx":
			$image = "page_white_word.png";	
			break;
		case "txt":
		case "rtf":
			$image = "page_white_text.png";
			break;
		case "pdf":
			$image = "pdfIcon16x16.png";	
			break;
		case "xls":
		case "xlsx":
			$image = "page_white_excel.png";	
			break;
		case "ppt":
		case "pptx":
			$image = "page_white_powerpoint.png";	
			break;
		case "wav":
		case "mp3":
			$image = "sound.png";	
			break;
		
	}
	
	if($image){
		echo "<img src='images/{$image}' title='Download File' border='0'/>";
	} else {
		echo "<img src='images/{$image}' title='Download File' border='0'/>";
	}
}
// ======================================
//alert new owners
function alert_owners($new_owners,$user,$urn,$company) {

#### puts current managers into an array ####
$current_managers = mysql_query("select user_id from tbl_ownership where urn = '$urn'") or die(mysql_error());
$current = mysql_fetch_assoc($current_managers);

$prev_owners = array_filter($current);
##### find removed managers #####
$removed_owners = array_diff($prev_owners ,$new_owners);

##### find new managers #####
$added_owners = array_diff($new_owners ,$prev_owners);

####email removed owners#####
foreach ($removed_owners as $key => $val) {
$user_query = mysql_query("select username,client_name, user_email from tbl_users where user_id = '$val'");
while ($owner = mysql_fetch_assoc($user_query)) {
	$headers = "From: newbusinessforum@121customerinsight.co.uk\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$email = $owner['email'];
$name = $owner['client_name'];
$subject = "You have been removed from a lead";
$body = '<p>Dear '.$name.'</p>
<p>The following lead has been unassigned from you by <i>'.$user.'</i></p>

<p>URN: '.$urn.'
<br>Company: '.$company.'</p>
<p><a href="http://www.121leads.co.uk/salesdrive/lead_manager.php?sys=$system_url&urn='.$urn.'">Click here to view the lead</a></p>

Kind Regards<br>
$system_name
';	

if ($user != $owner['username']) {
mail($email,$subject,$body,$headers); }
}
}
####email new owners######
foreach ($added_owners as $key => $val) {
$user_query = mysql_query("select username,client_name, email from tbl_users where username = '$val'");
while ($owner = mysql_fetch_assoc($user_query)) {
$headers = "From: newbusinessforum@121customerinsight.co.uk\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$email = $owner['email'];
$name = $owner['client_name'];
$subject = "A lead has been assigned to you";
$body = '<p>Dear '.$name.'</p>
<p>You have been tagged as an owner of the following lead by <i>'.$user.'</i></p>

<p><strong>URN:</strong> '.$urn.'
<br><strong>Company:</strong> '.$company.'</p>
<p><a href="http://www.121leads.co.uk/salesdrive/lead_manager.php?sys=$system_url&urn='.$urn.'">Click here to view the lead</a></p>

Kind Regards<br>
$system_name
';

if ($user != $owner['username']) {
mail($email,$subject,$body,$headers); }
}
}

}

//fix url
function fix_url($url) { 
if ($url != ""){
    if (substr($url, 0, 7) == 'http://') { return $url; } 
	if (substr($url, 0, 8) == 'https://') { return $url; }     
	return 'http://'. $url; } } 
	
	
	function send_email2($sql_result,$email,$placeholders){
// -----------------------------------

	require_once('includes/phpmailer/class.phpmailer.php');
	$row_email = mysql_fetch_assoc($sql_result);
	$mail = new PHPMailer();
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host = $row_email['host']; // SMTP server

	if($row_email['auth'] == "true"){												  
		$mail->SMTPAuth   = true;  // enable SMTP authentication
	}

	$mail->Host       = $row_email['host']; // sets the SMTP server
	$mail->Port       = $row_email['port'];                   // set the SMTP port for the GMAIL server
	$mail->Username   = $row_email['username']; // SMTP account username
	$mail->Password   = $row_email['password'];        // SMTP account password
	
	foreach ($placeholders as $key => $val) {
		
		if ($key == "nextcall") {
			$datefull = strtotime(fmt_string($_POST['nextcall'], 'datetime'));
			//$datefull = strtotime($val);
			$dayname = date('l',$datefull);
			$day = ltrim(date('d',$datefull),'0');
			$month = date('F',$datefull);
			$time = date('H:i',$datefull);
		}
	}
	
	if ($day == '01') { $day_affix = 'st'; } else 
	if ($day == '02') { $day_affix = 'nd'; } else
	if ($day == '03') { $day_affix = 'rd'; } else
	if ($day == '21') { $day_affix = 'st'; } else
	if ($day == '22') { $day_affix = 'nd'; } else
	if ($day == '23') { $day_affix = 'rd'; } else
	if ($day == '31') { $day_affix = 'st'; } else
	{ $day_affix = 'th'; }

	
	$date_array = array("datefull"=>$datefull,
	"dayname"=>$dayname,
	"day"=>$day.$day_affix,
	"month"=>$month,
	"time"=>$time);

	if 	(!$placeholders['surname'] || $placeholders['surname']==''){
		$placeholders['title'] = '';
		$placeholders['surname']='Sir/Madam'; 
	}

	$body = $row_email['message'];
	
	foreach ($placeholders as $key => $val){ 
	if ($key == "forename") {$val = ucfirst($val);}
	if ($key == "surname") {$val = ucfirst($val);}
	if ($key == "title") {$val = ucfirst($val);}
	$key ."=>" .$val."<br>"; 
	$body = str_replace("<%$key%>",$val,$body);
	}
	
	foreach ($date_array as $key => $val){ 
	$body = str_replace("<%$key%>",$val,$body);
	}


	$recipient = $email;
	$mail->Subject = $row_email['subject'];
	$mail->SetFrom($row_email['address'],$row_email['from_name']);
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	$mail->IsHTML(true);
	$mail->MsgHTML($body);
	$mail->AddAddress($recipient);
	$mail->AddBCC("jon-man@121customerinsight.co.uk");

	// Email Attachments
	if(isset($row_email['attachments'])){
		ini_set("memory_limit","32M");
		$attachments = explode(",", $row_email['attachments']);
		$att_directory= "attachments/";
		$att_handle=opendir($att_directory);

		foreach($attachments as $att_file) {
			$mail->AddAttachment($att_directory . $att_file);
		}

	}

	$mail->Send();

}

// ===================================
function fmt_string($string, $type){
// -----------------------------------

	switch($type){
		case 'phone':
			$string = str_replace("(","",$string);
			$string = str_replace(")","",$string);
			$string = str_replace("+","",$string);
			$string = str_replace(" ","",$string);
			break;
		case 'datetime':
			// FROM: dd/mm/yyyy hh:mm:ss TO: YYYY-mm-dd hh:mm:ss 
			//$string = substr($string,6,4) . "-" . substr($string,3,2) . "-" . substr($string,0,2) . " " . substr($string,11,2) . ":" . substr($string,14,2) . ":" . substr($string,17,2) ;
			$dt_date = explode("/",$string);
			$nextcall = $dt_date[2] . "-" . $dt_date[1] . "-" . $dt_date[0] . " " . $_POST['dt_hour'] .":". $_POST['dt_minute'] .":00";
			$string = $nextcall;
			break;
		case 'date':
			// FROM: dd/mm/yyyy TO: YYYY-mm-dd  
			$dt_date = explode("/",$string);
			$nextcall = $dt_date[2] . "-" . $dt_date[1] . "-" . $dt_date[0] ;
			$string = $nextcall;
			break;
		case 'ukdate':
			// FROM: YYYY-mm-dd TO: dd/mm/yyyy
			$dt_date = explode("-",$string);
			$nextcall = $dt_date[2] . "/" . $dt_date[1] . "/" . $dt_date[0] ;
			$string = $dt_date[2];
			break;
		case 'yyyymmdd':
			// FROM: dd/mm/yyyy TO: YYYY-mm-dd  
			$dt_date = explode("/",$string);
			$nextcall = $dt_date[2] . $dt_date[1] . $dt_date[0] ;
			$string = $nextcall;
			break;
	}
	
	return $string;

}
	###############
	
	function sendIcalEmail($email,$meeting_date,$meeting_name,$meeting_duration,$meeting_description) {
 
	$from_name = "121 Customer Insight";
	$from_address = "info@121leads.co.uk";
//	$meeting_location = "34 Modwen Rd"; //Where will your meeting take place
 	$subject = $meeting_name;
 
	//Convert MYSQL datetime and construct iCal start, end and issue dates
	$meetingstamp = STRTOTIME($meeting_date . " UTC");    
	$dtstart= GMDATE("Ymd\THis\Z",$meetingstamp);
	$dtend= GMDATE("Ymd\THis\Z",$meetingstamp+$meeting_duration);
	$todaystamp = GMDATE("Ymd\THis\Z");
 
	//Create unique identifier
	$cal_uid = DATE('Ymd').'T'.DATE('His')."-".RAND()."@121leads.co.uk";
 
	//Create Mime Boundry
	$mime_boundary = "----Meeting Booking----".MD5(TIME());
 
	//Create Email Headers
	$headers = "From: ".$from_name." <".$from_address.">\n";
	$headers .= "Reply-To: ".$from_name." <".$from_address.">\n";
	 if ($_SESSION['system_url']=="nbf"){ $headers .= "Cc: rachaeln@121customerinsight.co.uk\n"; }
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
	$headers .= "Content-class: urn:content-classes:calendarmessage\n";
 
	//Create Email Body (HTML)
	$message .= "--$mime_boundary\n";
	$message .= "Content-Type: text/html; charset=UTF-8\n";
	$message .= "Content-Transfer-Encoding: 8bit\n\n";
 
	$message .= "<html>\n";
	$message .= "<body>\n";
	$message .= '<p>' . $meeting_description . '</p>';    
	$message .= "</body>\n";
	$message .= "</html>\n";
	$message .= "--$mime_boundary\n";
 
	//Create ICAL Content (Google rfc 2445 for details and examples of usage) 
	$ical =    'BEGIN:VCALENDAR
PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN
VERSION:2.0
METHOD:REQUEST
BEGIN:VEVENT
DTSTART:'.$dtstart.'
DTEND:'.$dtend.'
TRANSP:OPAQUE
SEQUENCE:0
UID:'.$cal_uid.'
DTSTAMP:'.$todaystamp.'
DESCRIPTION:'.$meeting_description.'
SUMMARY:'.$subject.'
PRIORITY:5
CLASS:PUBLIC
END:VEVENT
END:VCALENDAR';   
//LOCATION:'.$meeting_location.'
//ORGANIZER:MAILTO:'.$from_address.'

	$message .= 'Content-Type: text/calendar;name="meeting.ics";method=REQUEST\n';
	$message .= "Content-Transfer-Encoding: 8bit\n\n";
	$message .= $ical;            
 
	//SEND MAIL
	$mail_sent = mail( $email, $subject, $message, $headers );
 
	IF($mail_sent)     {
		RETURN TRUE;
	} ELSE {
		RETURN FALSE;
	}   
 
}
?>