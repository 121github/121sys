<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GHS Tool</title>

     <link rel="stylesheet" href="https://www.121system.com/assets/css/bootstrap.css">
    <!-- Optional theme -->
    <link rel="stylesheet"
          href="https://www.121system.com/assets/themes/default/bootstrap-theme.css">
    <link rel="stylesheet" href="https://www.121system.com/assets/css/plugins/dataTables/css/dataTables.bootstrap.css">
    <!-- Latest compiled and minified JavaScript -->
    <link rel="stylesheet" href="https://www.121system.com/assets/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="https://www.121system.com/assets/css/datepicker3.css">
    <link rel="stylesheet" href="https://www.121system.com/assets/css/bootstrap-select.css">
    <link rel="stylesheet" href="https://www.121system.com/assets/css/slider.css">
    <link rel="stylesheet" href="https://www.121system.com/assets/css/default.css">
    <link rel="stylesheet" href="https://www.121system.com/assets/css/plugins/mmenu/jquery.mmenu.css">
    <link rel="stylesheet" href="https://www.121system.com/assets/css/plugins/mmenu/addons/jquery.mmenu.labels.css">
    <link rel="stylesheet" href="https://www.121system.com/assets/css/plugins/dataTables/css/font-awesome.css">

    <style>
        .navbar-toggle {
            display: block;
        }

        .navbar-toggle {
            float: left;
            margin-left: 15px;
        }
    </style>
    <!-- Set the baseUrl in the JavaScript helper -->
                <link rel="stylesheet" href="https://www.121system.com/assets/css/plugins/jqfileupload/jquery.fileupload.css">
                    <link rel="stylesheet" href="https://www.121system.com/assets/css/plugins/countdown/jquery.countdown.css">
                    <link rel="stylesheet" href="https://www.121system.com/assets/css/plugins/responsive-calendar/responsive-calendar.css">
                    <link rel="stylesheet" href="https://www.121system.com/assets/css/plugins/bootstrap-iconpicker/icon-fonts/font-awesome-4.2.0/css/font-awesome.min.css">
                    <link rel="stylesheet" href="https://www.121system.com/assets/css/plugins/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css">
            <link rel="shortcut icon"
          href="https://www.121system.com/assets/themes/default/icon.png">
    <script src="https://www.121system.com/assets/js/lib/jquery.min.js"></script>
    <script src="https://www.121system.com/assets/js/lib/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="https://www.121system.com/assets/js/lib/wavsurfer.js"></script>
</head>

<body>
<div class="row">
<div class="col-lg-12">
<form id="form" style="padding:20px">
<div class="form-group">
<select class="select-picker" id="function">
<option value="trackvia/review_required">Send for desktop check</option>
<option value="trackvia/survey_refused">Refused survey</option>
<option value="trackvia/add_appointment">Add appointment</option>
<option value="trackvia/already_had_survey">Already had survey</option>
<option value="trackvia/unable_to_contact">Unable to contact</option>
<option value="trackvia/unable_to_contact_installs">Unable to contact (installs)</option>
<option value="trackvia/notified_not_eligible">Notified not eligible</option>
<option value="trackvia/update_tv_record">Update record in trackvia</option>
        <option value="trackvia/add_tv_record">Add record to trackvia</option>
         <option value="trackvia/update_record_details">Update record from trackvia</option>
                  <option value="trackvia/send_enquiry_to_tv">Update trackvia enquiry date</option>
                
</select>
</div>
<div class="form-group">
<label>URN</label>
<input class="form-control" id="urnval" placeholder="Enter urn" name="urn"/>
</div>
<div class="form-group">
<label>Trackvia Record locator</label>
<input class="form-control" id="tv" placeholder="Enter GHS URN" name="tv"/><button id="get-urn" class="btn btn-default">Get URN</button>
</div>
<div class="form-group">
<button class="btn btn-primary" id="update">Update</button> <button class="btn btn-primary" id="update-list">Update List</button>
</div>
</form>
</div>
<div class="col-lg-12">

<div id="response"></div>

</div>
</div>
</body>
<script>
$(document).ready(function(){

$('#get-urn').click(function(e){
	e.preventDefault();
	if($('#tv').val()==""){
		alert("Please enter a ghs-urn first")
	} else {
	$.ajax({
		url:"https://www.121system.com/trackvia/get_urn_from_ghs",
	type:"POST",
	dataType:"HTML",
	data:{ ghsurn:$('#tv').val() }
	}).done(function(urn){
	$('#urnval').val(urn);
	});
	}
});	

$('#update').click(function(e){
	e.preventDefault();		
	if($('#urnval').val()==""){
		alert("Please enter a urn or press the ghs urn button first")
	} else {
	
	$.ajax({
		url:"https://www.121system.com/"+$('#function').val(),
	type:"POST",
	dataType:"HTML",
	data:{ urn:$('#urnval').val() }
	}).done(function(response){
		$('#response').html(response);
	});
	}
});

$('#update-list').click(function(e){
	e.preventDefault();		
	if($('#urnval').val()==""){
		alert("Please enter a urn or press the ghs urn button first")
	} else {
	var array = $('#urnval').val().split(',');
	$.each(array,function(k,v){
	$.ajax({
		url:"https://www.121system.com/"+$('#function').val(),
	type:"POST",
	dataType:"HTML",
	data:{ urn:v }
	}).done(function(response){
		$('#response').html(response);
	});
	});
	}
});

});
</script>

</html>