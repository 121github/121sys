<?php
$show_footer = false;
if (isset($_SESSION['current_campaign']) && in_array("show footer", $_SESSION['permissions'])) {
    $show_footer = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title>Technical Visit</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
    <!-- Optional theme -->
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/themes/colors/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/bootstrap-theme.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/dataTables/css/dataTables.bootstrap.css">
    <!-- Latest compiled and minified JavaScript -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datepicker3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-select.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/slider.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/default.css">
    <!-- Set the baseUrl in the JavaScript helper -->
    <?php //load specific javascript files set in the controller
    if (isset($css)):
        foreach ($css as $file): ?>
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/<?php echo $file ?>">
        <?php endforeach;
    endif; ?>
    <link rel="shortcut icon"
          href="<?php echo base_url(); ?>assets/themes/colors/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/icon.png">
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/wavsurfer.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery.numeric.min.js"></script>
     <script src="<?php echo base_url(); ?>assets/js/lib/bootstrap.min.js"></script>
    <!--Need to make a new icon for this
          <link rel="apple-touch-icon" href="http://www.121system.com/assets/img/apple-touch-icon.png" />-->
    <style>
        .tooltip-inner {
            max-width: 450px;
            /* If max-width does not work, try using width instead */
            width: 450px;
        }
		select,input,textarea { display:none }
    </style>
</head>
<body>
<div class="container">
    <h2>HSL Technician Report <small>Report ID: 162 / URN: 500</small></h2>

    <form id="form" style="padding-bottom:50px;">
    <input type="hidden" name="appointment_id" value="<?php echo $values['appointment_id'] ?>" />
    
    
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading pointer" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
      <h4 class="panel-title">
          Customer Details
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
      
            <div class="row">
      <div class="col-md-6">
      <h4>Contact Info</h4>
<table class="table"><tr><th>Customer</th><td>Brad Foster</td></tr>
       <tr><th>Address</th><td>5 oak street<br>littleborough<br>Rochdale<br>OL150HH</td></tr>
          <tr><th>Telephone</th><td>07814401867</td></tr>
               <tr><th>Email</th><td>brad@compsmart.co.uk</td></tr>
       </table>
       </div>
        <div class="col-md-6">
        <h4>Order Info</h4>
   <table class="table">
   <tr><th>HSL Ref</th><td>HSL/R202022</td></tr>
   <tr><th>Value</th><td>&pound;2000</td></tr>
       <tr><th>Model</th><td>Holly Standard DUA</td></tr>
          <tr><th>Material</th><td>Fabric</td></tr>
               <tr><th>Retailer</th><td>-</td></tr>
       </table>
       </div>
       </div>
      
       
      </div>
    </div>
  </div><!--
  <div class="panel panel-default">
    <div class="panel-heading pointer" role="tab" id="headingTwo"  data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
      <h4 class="panel-title">
          Order Details
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
        <table class="table"><tr><th>Value</th><td>&pound;2000</td></tr>
       <tr><th>Model</th><td>Holly Standard DUA</td></tr>
          <tr><th>Material</th><td>Fabric</td></tr>
               <tr><th>Retailer</th><td>-</td></tr>
       </table>
      </div>
    </div>
  </div>
  -->
  <div class="panel panel-default">
    <div class="panel-heading pointer" role="tab" id="headingThree" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
      <h4 class="panel-title">
          Appointment Details
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
      <div class="row">
      <div class="col-md-6">
         <table class="table"><tr><th>Appointment</th><td>9/11/2015</td></tr>
       <tr><th>Environment</th><td class="val-text"><span>Good</span> <select><option value="good">Good</option><option value="average">Average</option><option value="bad">Bad</option></select></td></tr>
          <tr><th>Condition</th><td class="val-text"><span>Good</span> <select><option  value="Good">Good</option><option value="average">Average</option><option value="bad">Bad</option></select></td></tr>
               <tr><th>Maintenance</th><td class="val-text"><span>Average</span> <select><option value="Good">Good</option><option value="average">Average</option><option value="bad" >Bad</option></select></td></tr>
       </table>
       </div>
        <div class="col-md-6">
        <table class="table"><tr><th>Soiled</th><td class="val-text"><span>No</span> <select><option value="yes">Yes</option><option value="no">No</option></select></td></tr>
       <tr><th>Batch Label Photo</th><td class="val-text"><span>Yes</span><select><option value="yes">Yes</option><option value="no">No</option></select></td></tr>
          <tr><th>Time Taken</th><td class="val-text"><span>40 mins</span><input value="40 mins" placeholder="eg:40mins"/></td></tr>
               <tr><th>Approved</th><td class="val-text"><span>Yes</span><select><option value="yes">Yes</option><option value="no">No</option></select></td></tr>
       </table>
       </div>
       </div>
      </div>
    </div>
  </div>
    <div class="panel panel-default">
    <div class="panel-heading pointer" role="tab" id="headingFour"  data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
      <h4 class="panel-title">
          Fault Details
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
      <div class="panel-body">
       <div class="row">
      <div class="col-md-6">
         <table class="table"><tr><th>Fault Code</th><td class="val-text"><span>Upholstery</span> <input value="Upholstery" placeholder="eg:Upholstery"/></td></tr>
       <tr><th>Description</th><td class="val-text"><span>Chair was not functioning</span><textarea name="">Chair was not functioning</textarea></td></tr>
       </table>
       </div>
        <div class="col-md-6">
        <table class="table"><tr><th>Sub Code</th><td class="val-text"><span>Electric reclinder in/op</span><input value="Electric reclinder in/op" placeholder="eg:Electric reclinder in/op"/></td></tr>
       <tr><th>Actions Taken</th><td class="val-text"><span>Replaced components</span><textarea name="">Replaced components</textarea></td></tr>
       
       </table>
       </div>
       </div>
      </div>
    </div>
  </div>
   <div class="panel panel-default">
    <div class="panel-heading pointer" role="tab" id="headingFive" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
      <h4 class="panel-title">
        Images
      </h4>
    </div>
    <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
      <div class="panel-body">
      <table class="table"><tr><th>Upload Images</th><td><input type="file" name="upload"/></td></tr></table>
       
      </div>
    </div>
  </div>
</div>
    
    

</form>
  
  
   
          </div>
          
             <div class="navbar navbar-default navbar-fixed-bottom" style="z-index:1">
             <div style="padding:8px 50px">
       <button class="btn btn-default  navbar-btn">Go Back</button>
       <button class="btn btn-primary  navbar-btn" id="edit-report">Edit</button>
       <button class="btn btn-success  navbar-btn">Set Complete</button>
       <button class="btn btn-primary  navbar-btn" style="display:none">Save</button>
       </div>
    </div>
          <script type="text/javascript">
		  $(document).ready(function(){
			  $(document).on('click','#edit-report',function(e){
				  e.preventDefault();
				  $('.panel-collapse').removeClass('in');
				   $('#collapseFour,#collapseThree').addClass('in');
				  $('.val-text').each(function(){
					   $(this).find('span').hide();
					  $(this).find('input,textarea,select').show();
				  });
				   });
		  });
		  </script>
</body>
</html>