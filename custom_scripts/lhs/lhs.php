<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("BASEPATH", "../../system/");
include("../../application/config/database.php");

ob_start();
include("../../session.php");

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

if(isset($_GET['update_status'])){ 
$conn->query("update `appointments` set `appointment_type_id` = 3 where `title` like '%[conf%'");
$conn->query("update `appointments` set `appointment_type_id` = 2 where `title` like '%tbc%'");
//$conn->query("delete from appointments where title like 'https://www.google.co.uk%'");
echo "Appointment status was updated for LHS";
exit;
}

//Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array(
        "success" => false,
        "msg" => "You should be logged"
    ));
    header("Location: " . substr(BASEPATH, 0, strlen(BASEPATH) - 7));
    exit;
}



if(isset($_GET['get_appointment_title'])||isset($_GET['set_appointment_title'])){
	$urn = "";
	$appointment_id = "";
	if(!empty($_GET['urn'])){
	$urn = 	intval($_GET['urn']);
	}
	if(!empty($_POST['urn'])){
	$urn = 	intval($_POST['urn']);
	}
	if(!empty($_POST['appointment_id'])){
	$appointment_id = 	intval($_POST['appointment_id']);
	}
	//get the appointment_id 
	$qry = "select max(appointment_id) appointment_id,appointment_type_id,urn from appointments where 1 ";
	if($urn){
	$qry .= " and urn = '$urn' ";	
	}
	if($appointment_id){
	$qry .= " and appointment_id = '$appointment_id' ";	
	}
	$qry .= " order by appointment_id desc limit 1";
	$result = $conn->query($qry);
	if($result->num_rows<1){
	echo json_encode(array("success"=>false,"msg"=>"No appointments set"));
	}
	$row = $result->fetch_assoc();
	$appointment_id = $row['appointment_id'];
	$appointment_type_id = $row['appointment_type_id'];
	$urn = $row['urn'];
	//access address
	$access_address = "";
	$qry = "select * from contact_addresses join contacts using(contact_id) where urn = '$urn' and description = 'Access Address'";
	$result = $conn->query($qry);
	if($result->num_rows){
	$row = $result->fetch_assoc();
	$access_address	= addressFormat($row);
	}
	//surveying address
	$address = "";
	$qry = "select * from contact_addresses join contacts using(contact_id) where urn = '$urn' and description = 'Surveying Address'";
	$result = $conn->query($qry);
	if($result->num_rows){
	$row = $result->fetch_assoc();
	$address	= addressFormat($row);
	}
	

	//get the job reference 
	$job_ref = "";
	$qry = "select `value` from  custom_panel_values join custom_panel_data using(data_id) where appointment_id = '$appointment_id' and field_id = 1 group by appointment_id";
	$result = $conn->query($qry);
	if($result->num_rows){
	$row = $conn->query($qry)->fetch_assoc();
	$job_ref = $row['value'];
	}
	//get the job status
	$qry = "select `value` from  custom_panel_values join custom_panel_data using(data_id) where appointment_id = '$appointment_id' and field_id = 6 group by appointment_id";
	$result = $conn->query($qry);
	if($result->num_rows){
	$row = $conn->query($qry)->fetch_assoc();
	$job_status = $row['value'];
	}
	//get the job type
	$type_of_survey = "";
	$qry = "select `value` from  custom_panel_values join custom_panel_data using(data_id) where appointment_id = '$appointment_id' and field_id = 7 group by appointment_id";
	$result = $conn->query($qry);
	if($result->num_rows){
	$row = $conn->query($qry)->fetch_assoc();
	$type_of_survey = $row['value'];
	}
	//get the additional_services
	$additional_services  = "";
	$qry = "select `value` from  custom_panel_values join custom_panel_data using(data_id) where appointment_id = '$appointment_id' and field_id = 11 group by appointment_id";
	$result = $conn->query($qry);
	if($result->num_rows){
	$row = $conn->query($qry)->fetch_assoc();
	$additional_services = $row['value'];
	}
	
        $title = "";
        $type = "";
        $add_services = "";

        if ($job_status != "Invoiced") {
            $title .= $job_ref;
        }

        switch($appointment_type_id) {
            case "1":
                $title .= " [poss]";
                break;
            case "2":
                $title .= " [tbc]";
                break;
            case "3":
                $title .= " [conf]";
                break;
        }
        switch ($type_of_survey) {
            case "Building Survey":
                $type = "BS";
                break;
            case "Home Buyer Report":
                $type = "HBR";
                break;
            case "General Structural Inspection":
                $type = "GSI";
                break;
            case "Specific Inspection":
                $type = "SSI";
                break;
            case "Site Visit":
                $type = "SV";
                break;
            case "Valuation":
                $type = "VAL";
                break;
            case "Schedule Of Condition":
                $type = "SOC";
                break;
            case "Structural Calculations":
                $type = "SCALC";
                break;
            case "Party Wall":
                $type = "PW";
                break;
        }

        switch ($additional_services) {
            case "Valuation":
                $add_services = "VAL";
                break;
            case "Express Write Up Service":
                $add_services = "EXP";
                break;
            case "Platinum Plus":
                $add_services = "PP";
                break;
            case "High Level Images":
               $add_services = "HRP";
                break;
            case "Thermal Images":
                $add_services = "TI";
                break;
        }

        if ($access_address) {
            $title .= " KA ".$type." ".$add_services." ".$address;
            $title .= " - KA ".$access_address;
        }
        else {
            $title .= " STP ".$type." ".$add_services." ".$address;
        }	

		 if(isset($_GET['set_appointment_title'])){
		  $sql = "UPDATE appointments SET title='" . addslashes($title) . "' WHERE appointment_id=" . $appointment_id;
		  $conn->query($sql);
		 }
		 echo json_encode(array("success"=>true,"title"=>$title,"msg"=>"Appointment title updated"));
			exit; 
		 
}




if(isset($_GET['address_form'])){
	$contact_id = intval($_POST['contact_id']);
	$urn = intval($_POST['urn']);
	$is_primary = $conn->query("select contact_id from contacts where primary = 1 and contact_id = '$contact_id'");
?>

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div id="survey-panel" class="panel panel-default">

    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> <span style="color:#666">Survey Details</span></a></h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
        <?php
	    $sql = "select * from contact_addresses where description = 'Surveying address' and contact_id = $contact_id ";
		$row = array();
		$result = $conn->query($sql);
        if($result){
				$row = $result->fetch_assoc();
		}
		?>
      
      
        <form id="survey-address-form">
         <input type="hidden" name="urn" value="<?php echo $urn ?>" />
              <input type="hidden" name="description" value="Surveying Address" />
                 <input type="hidden" name="contact_id" value="<?php echo $contact_id ?>" />
          <div class="row">
            <div class="col-md-6">
             <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="1st Line of address" name="add1" value="<?php echo @$row['add1'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="2nd Line of address" name="add2" value="<?php echo @$row['add2'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="3rd Line of address" name="add3" value="<?php echo @$row['add3'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="4rd Line of address" name="add4" value="<?php echo @$row['add4'] ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Town/City" name="city" value="<?php echo @$row['city'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="County" name="county" value="<?php echo @$row['county'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Country" name="country" value="<?php echo @$row['country'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Postcode" name="postcode" value="<?php echo @$row['postcode'] ?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="form-group">
             <button class="btn btn-default pull-left clear-address">Clear</button> <button class="btn btn-default pull-left reset-address marl">Reset</button>   <button class="btn btn-primary pull-right save-address">Save</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div id="access-panel" class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"> <span style="color:#666;"> Access Details</span> </a> </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
      <?php
	  $access_contact = $contact_id;
	    $sql = "select * from contacts left join contact_telephone using(contact_id) left join contact_addresses using(contact_id) where (contacts.`primary`=0 or contacts.`primary` is null) and urn = $urn";
		$row = array();
		$result = $conn->query($sql);
        if($result){
				$row = $result->fetch_assoc();
		}
		?>
      
      
        <form id="access-address-form">
                 <input type="hidden" name="urn" value="<?php echo $urn ?>" />
           <input type="hidden" name="description" value="Access Address" />
                            <input type="hidden" name="contact_id" value="<?php echo @$row['contact_id'] ?>" />
                            
          <div class="row">
            <div class="col-md-6">               
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Access Name/Company" name="name" value="<?php echo @$row['fullname'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="1st Line of address" name="add1" value="<?php echo @$row['add1'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="2nd Line of address" name="add2" value="<?php echo @$row['add2'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="3rd Line of address" name="add3" value="<?php echo @$row['add3'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="4rd Line of address" name="add4" value="<?php echo @$row['add4'] ?>">
              </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Access contact tel" name="telephone_number" value="<?php echo @$row['telephone_number'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Town/City" name="city" value="<?php echo @$row['city'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="County" name="county" value="<?php echo @$row['county'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Country" name="country" value="<?php echo @$row['country'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Postcode" name="postcode" value="<?php echo @$row['postcode'] ?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="form-group">
                 <button class="btn btn-default pull-left clear-address">Clear</button><button class="btn btn-default pull-left reset-address marl">Reset</button> <button class="btn btn-primary pull-right save-address">Save</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="correspondance-panel" class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree">
      <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree"> <span style="color:#666">Correspondence Address</span> </a> </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
              <?php
	     $sql = "select * from contact_addresses where contact_addresses.description = 'Correspondence address' and contact_id = $contact_id ";
		$row = array();
		$result = $conn->query($sql);
        if($result){
				$row = $result->fetch_assoc();
		}
		?>
        <form id="correspondence-address-form">
                 <input type="hidden" name="urn" value="<?php echo $urn ?>" />
        <input type="hidden" name="description" value="Correspondence Address" />
                            <input type="hidden" name="contact_id" value="<?php echo $contact_id ?>" />
          <div class="row">
            <div class="col-md-6">
             <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="1st Line of address" name="add1" value="<?php echo @$row['add1'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="2nd Line of address" name="add2" value="<?php echo @$row['add2'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="3rd Line of address" name="add3" value="<?php echo @$row['add3'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="4rd Line of address" name="add4" value="<?php echo @$row['add4'] ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Town/City" name="city" value="<?php echo @$row['city'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="County" name="county" value="<?php echo @$row['county'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Country" name="country" value="<?php echo @$row['country'] ?>">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="Postcode" name="postcode" value="<?php echo @$row['postcode'] ?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="form-group">
               <button class="btn btn-default pull-left clear-address">Clear</button><button class="btn btn-default pull-left reset-address marl">Reset</button> <button class="btn btn-primary pull-right save-address">Save</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
	$modal.find('.reset-address').click(function(e){
		e.preventDefault();
		$(this).closest('form')[0].reset();
	});
	$modal.find('.clear-address').click(function(e){
		e.preventDefault();
		$(this).closest('form').find('input').val('');
	});
	
	$modal.find('.save-address').click(function(e){
		e.preventDefault();
		var description = $(this).closest('form').find('input[name="description"]').val();
		console.log(description);
		$.ajax({ url:helper.baseUrl+'custom_scripts/lhs/lhs.php?save_address',
		type:"POST",
		data:$(this).closest('form').serialize(),
		dataType:"JSON",
		}).done(function(response){
			if(response.success){
				if(description=="Access Address"){
		$('#access-address-form').find('input[name="contact_id"]').val(response.contact_id);
				}
			flashalert.success(response.msg);
			record.contact_panel.load_panel(record.urn);
			campaign_functions.set_appointment_title("",record.urn);
			} else {
				flashalert.danger(response.msg);
			}
			
			
		});
	});
	
});
</script>
<?php  exit; }

//create the LHS survey description
if(isset($_GET['calendar_description'])){
	$description = "";
	$survey_type = "";
	$notes = "";
	if(isset($_POST['id'])){
	$id = intval($_POST['id']);
	}

if(isset($_GET['id'])){
	$id = intval($_GET['id']);
}

//first get the job reference and survey type
$query = "select * from custom_panel_data cpd join custom_panel_values using(data_id) join custom_panel_fields using(field_id) where cpd.appointment_id = '$id' ";
$job= $conn->query($query);


while($row =$job->fetch_assoc()){
if($row['name']=='Job Reference'){
$description .= $row['value']."\n";
}
if($row['name']=='Type of survey'){
$survey_type = $row['value']."\n";
}
}

$query = "select * from appointments a join records using(urn) join sticky_notes using(urn) join contacts c using(urn) where a.appointment_id = '$id' and c.`primary`=1";
$result= $conn->query($query);
$contact = $result->fetch_assoc();
$description .= $contact['fullname']."\n";
$description .= $contact['email']."\n";
$description .= $contact['notes']."\n";
$notes = $contact['text'];
$query = "select ca.add1,ca.add2,ca.add3,ca.add4,ca.city,ca.county,ca.postcode from appointments a join contacts c using(urn) join contact_addresses ca on ca.contact_id = c.contact_id where description = 'Correspondance Address' and a.appointment_id = '$id'";
$result= $conn->query($query);
$address = $result->fetch_assoc();
$description .= addressFormat($address)."\n";

$telephone="";
$query = "select * from appointments a join records using(urn) join contacts c using(urn) left join contact_telephone ct on c.contact_id = ct.contact_id where a.appointment_id = '$id' and c.`primary`=1";
$result = $conn->query($query);
while($row =$result->fetch_assoc()){
$telephone .= $row['description'].": ".$row['telephone_number']."\n";
}
$description .= $telephone."\n\n";
$description .= $survey_type."\n";

$query = "select ca.add1,ca.add2,ca.add3,ca.add4,ca.city,ca.county,ca.postcode from appointments a join contacts c using(urn) join contact_addresses ca on ca.contact_id = c.contact_id left join contact_telephone ct on ct.contact_id = c.contact_id where (ca.description = 'Access Address' or ca.description is null)  and a.appointment_id = '$id' and (c.`primary` = 0 or c.`primary` is null)";
$result= $conn->query($query);
$address = $result->fetch_assoc();
$description .= addressFormat($address)."\n";
$description .= $address['fullname']."\n";
$description .= $address['position']."\n";
$description .= $address['notes']."\n";
$description .= $address['telephone_number']."\n";

$description .= $notes;

echo json_encode(array("success"=>true,"description"=>$description));
exit;
}

if(isset($_GET['save_address'])){
	$contact_id =intval($_POST['contact_id']);
	$urn = intval($_POST['urn']);
	
	if(!empty($_POST['postcode'])&&!validate_postcode($_POST['postcode'])){
	echo json_encode(array("success"=>false,"msg"=>"Postcode is invalid"));
	exit;	
	}
	if($_POST['description']<>'Access Address'&&empty($_POST['postcode'])){
	echo json_encode(array("success"=>false,"msg"=>"Address needs a postcode"));
	exit;	
	}
	if(empty($_POST['add1'])&&empty($_POST['postcode'])){
		 $sql = "delete from contact_addresses where contact_id = '".intval($_POST['contact_id'])."' and description = '".addslashes($_POST['description'])."'";
		 $conn->query($sql);
	}
	
	
		//if its the access address we have to create a new contact and link the address to the access contact because they have might their own name and number.
	if($_POST['description']=="Access Address"){
		if(empty($_POST['contact_id'])){
			 $add_access_contact = "insert into contacts set fullname = '".$_POST['name']."', urn = '$urn',notes ='Access details',`primary`=0";
			 $conn->query($add_access_contact);
			 $contact_id = $conn->insert_id;
			if($contact_id&&!empty($_POST['telephone_number'])){
			 $add_access_phone = "insert into contact_telephone set contact_id = $contact_id, telephone_number = '".$_POST['telephone_number']."',description = 'Access Number'";
			 $conn->query($add_access_phone);
			}
		} else {
			$update_access_contact = "update contacts set fullname = '".$_POST['name']."' where contact_id = '".$_POST['contact_id']."'";
			 $conn->query($update_access_contact);
			  $delete_access_phone = "delete from contact_telephone where description = 'Access Number'  and contact_id = '".$_POST['contact_id']."'";
			  	 $conn->query($delete_access_phone);
	 $add_access_phone = "insert into contact_telephone set contact_id = $contact_id, telephone_number = '".$_POST['telephone_number']."',description = 'Access Number'";
			 $conn->query($add_access_phone);
		}
	}
	
	//if the address is ok then we can proceed
		if(!empty($_POST['postcode'])){
	$postcode = postcodeFormat($_POST['postcode']);
	
	$sql = "delete from contact_addresses where contact_id = '".$contact_id."' and description = '".addslashes($_POST['description'])."'";
	$conn->query($sql);
	
	
	$sql = "insert into contact_addresses set description = '".addslashes($_POST['description'])."', add1 = '".addslashes($_POST['add1'])."'
	, add2 = '".addslashes($_POST['add2'])."'
	, add3 = '".addslashes($_POST['add3'])."'
	, add4 = '".addslashes($_POST['add4'])."'
	, city = '".addslashes($_POST['city'])."'
	, county = '".addslashes($_POST['county'])."'
	, country = '".addslashes($_POST['country'])."'
	, postcode = '".addslashes($postcode)."' ";
	if($_POST['description']=="Surveying Address"){
	$sql .= " ,`primary` = '1' ";	
	}
	 $sql .= ",contact_id = '".$contact_id."'";
	
	$conn->query($sql);	
	 $address_id = $conn->insert_id;
	 //check if there is an appointment and add or update the addresses
	 $check = "select appointment_id from appointments where urn = '$urn' and start > now() ";
	 $result = $conn->query($check);
		 if($result->num_rows>0){
			 $app = $result->fetch_assoc();
			 $app_id = $app['appointment_id'];
			  $address = addressFormat($_POST);
			 if($_POST['description'] == "Surveying Address"){
				$sql = "update appointments set address = '$address' ,postcode='$postcode',address_table='contact_addresses',address_id='$address_id' where appointment_id = '$app_id'";
				$conn->query($sql);
			 }
			 
			  if($_POST['description'] == "Access Address"){
				 $sql = "update appointments set access_address = '$address' ,access_postcode='$postcode',access_address_table='contact_addresses',access_address_id='$address_id' where appointment_id = '$app_id'";
				$conn->query($sql);
			 }
			 
	 }	
	 
	}
			
		echo json_encode(array("success"=>true,"msg"=>"The address was updated","contact_id"=>$contact_id));
		exit;

}


if(isset($_POST["action"])){
$action = $_POST["action"];

switch ($action) {
    case "update_appointment_title":
        $sql = "UPDATE appointments SET title='" . addslashes($_POST['title']) . "' WHERE appointment_id=" . $_POST['appointment_id'];

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
    case "create_job_number":
        $sql = "select `value` from custom_panel_values where field_id = 1 and `value` like 'LH%' order by `value` desc limit 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $job_number = "LH" . date('y') . "-" . (intval(str_replace("LH" . date('y') . "-", "", $row['value'])) + 1);
        } else {
            $job_number = "LH" . date('y') . "-1";
        }
        $update = "";
        $check = "select `value` from custom_panel_values where field_id = 1 and `value` like 'LH%' and data_id = '" . $_POST['data_id'] . "'";
        $exists = $conn->query($check);
        if ($exists->num_rows == 0) {
            $update = "replace into custom_panel_values set data_id = '{$_POST['data_id']}',field_id='1',`value`='$job_number'";
            $conn->query($update);
        }
        echo json_encode(array("success" => true, "job_number" => $job_number, "query" => $update));
        break;
    case "clear_job_number":
        $sql = "update custom_panel_values set `value` = '' where field_id = 1 and data_id = '{$_POST['data_id']}'";
        $result = $conn->query($sql);
        break;
}
}




function postcodeFormat($postcode)
{
    //trim and remove spaces
    $cleanPostcode = preg_replace("/[^A-Za-z0-9]/", '', $postcode);
 
    //make uppercase
    $cleanPostcode = strtoupper($cleanPostcode);
 
    //if 5 charcters, insert space after the 2nd character
    if(strlen($cleanPostcode) == 5)
    {
        $postcode = substr($cleanPostcode,0,2) . " " . substr($cleanPostcode,2,3);
    }
 
    //if 6 charcters, insert space after the 3rd character
    elseif(strlen($cleanPostcode) == 6)
    {
        $postcode = substr($cleanPostcode,0,3) . " " . substr($cleanPostcode,3,3);
    }
 
 
    //if 7 charcters, insert space after the 4th character
    elseif(strlen($cleanPostcode) == 7)
    {
        $postcode = substr($cleanPostcode,0,4) . " " . substr($cleanPostcode,4,3);
    }
 
    return $postcode;
}

    function validate_postcode(&$toCheck) {

        // Permitted letters depend upon their position in the postcode.
        $alpha1 = "[abcdefghijklmnoprstuwyz]";                          // Character 1
        $alpha2 = "[abcdefghklmnopqrstuvwxy]";                          // Character 2
        $alpha3 = "[abcdefghjkpmnrstuvwxy]";                            // Character 3
        $alpha4 = "[abehmnprvwxy]";                                     // Character 4
        $alpha5 = "[abdefghjlnpqrstuwxyz]";                             // Character 5
        $BFPOa5 = "[abdefghjlnpqrst]{1}";                               // BFPO character 5
        $BFPOa6 = "[abdefghjlnpqrstuwzyz]{1}";                          // BFPO character 6
        // Expression for BF1 type postcodes 
        $pcexp[0] = '/^(bf1)([[:space:]]{0,})([0-9]{1}' . $BFPOa5 . $BFPOa6 . ')$/';

        // Expression for postcodes: AN NAA, ANN NAA, AAN NAA, and AANN NAA with a space
        $pcexp[1] = '/^(' . $alpha1 . '{1}' . $alpha2 . '{0,1}[0-9]{1,2})([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})$/';

        // Expression for postcodes: ANA NAA
        $pcexp[2] = '/^(' . $alpha1 . '{1}[0-9]{1}' . $alpha3 . '{1})([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})$/';

        // Expression for postcodes: AANA NAA
        $pcexp[3] = '/^(' . $alpha1 . '{1}' . $alpha2 . '{1}[0-9]{1}' . $alpha4 . ')([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})$/';

        // Exception for the special postcode GIR 0AA
        $pcexp[4] = '/^(gir)([[:space:]]{0,})(0aa)$/';

        // Standard BFPO numbers
        $pcexp[5] = '/^(bfpo)([[:space:]]{0,})([0-9]{1,4})$/';

        // c/o BFPO numbers
        $pcexp[6] = '/^(bfpo)([[:space:]]{0,})(c\/o([[:space:]]{0,})[0-9]{1,3})$/';

        // Overseas Territories
        $pcexp[7] = '/^([a-z]{4})([[:space:]]{0,})(1zz)$/';

        // Anquilla
        $pcexp[8] = '/^ai-2640$/';

        // Load up the string to check, converting into lowercase
        $postcode = strtolower($toCheck);

        // Assume we are not going to find a valid postcode
        $valid = false;

        // Check the string against the six types of postcodes
        foreach ($pcexp as $regexp) {

            if (preg_match($regexp, $postcode, $matches)) {

                // Load new postcode back into the form element  
                $postcode = strtoupper($matches[1] . ' ' . $matches [3]);

                // Take account of the special BFPO c/o format
                $postcode = preg_replace('/C\/O([[:space:]]{0,})/', 'c/o ', $postcode);

                // Take acount of special Anquilla postcode format (a pain, but that's the way it is)
                if (preg_match($pcexp[7], strtolower($toCheck), $matches))
                    $postcode = 'AI-2640';

                // Remember that we have found that the code is valid and break from loop
                $valid = true;
                break;
            }
        }

        // Return with the reformatted valid postcode in uppercase if the postcode was 
        // valid
        if ($valid) {
            $toCheck = $postcode;
            return true;
        } else
            return false;
    }
	
		function addressFormat($array=array(),$seperator=", "){
		$allowed = array("add1","add2","add3","add4","city","county","country","postcode");
		$address = "";
	if(is_array($array)){
	foreach($array as $k=>$v){
	if(!in_array($k,$allowed)||empty($v)){
		unset($array[$k]);
	}
	}
	$address =implode($seperator,$array);
	return $address;
	} else {
	return "";	
	}
}

?>
