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
    <title>GHS Inbound Data Capture</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
    <!-- Optional theme -->
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/bootstrap-theme.css">
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
          href="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/icon.png">
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/wavsurfer.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery.numeric.min.js"></script>
    <!--Need to make a new icon for this
          <link rel="apple-touch-icon" href="http://www.121system.com/assets/img/apple-touch-icon.png" />-->
    <style>
        .tooltip-inner {
            max-width: 450px;
            /* If max-width does not work, try using width instead */
            width: 450px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>GHS inbound data capture form</h2>

    <p>Please complete the following questions and click save</p>

    <form id="form" style="padding-bottom:50px;">
        <div id="q8-container">
      <label>Where did you hear about us?</label><br>
<select name="answers[a8]" class="selectpicker q8-question" data-width="100%"
                    data-size="5" >
  <option value='Mail / Leaflet'>Mail / Leaflet</option>
    <option value='Referral'>Referral</option>
      <option value='Scaffold Banner'>Scaffold Banner</option>
        <option value='Newspaper'>Newspaper</option>
          <option value='Website'>Website</option>
             <option value='Radio'>Radio</option>
              <option value='Friend'>Friend</option>
               <option value='Family'>Family</option>
                <option value='Facebook'>Facebook</option>
                 <option value='Neighbour'>Neighbour</option>
  </select>
  </div>
    <hr>
    
        <label>What type of property is it? <span class="glyphicon glyphicon-question-sign tt"
                                                       data-toggle="tooltip" data-placement="right"
                                                       title="If it's a flat we can not do the install. Please explain the reason (they do not won the roof)"></span></label>
                                                       
                                                       
        <div class="radio">
            <label>
                <input class="q1-question" type="radio" name="answers[a1][]" id="optionsRadios1"
                       value="House"  <?php if (@strpos($values['a1'], "House") !== false) {
                    echo "checked";
                } ?> />
                House
            </label>
        </div>
        <div class="radio">
            <label>
                <input class="q1-question" type="radio" name="answers[a1][]" id="optionsRadios2"
                       value="Flat" <?php if (@strpos($values['a1'], "Flat") !== false) {
                    echo "checked";
                } ?>>
                Flat
            </label>
        </div>
        <div id="q1-alert" class="text-danger" style="display:none">Please explain we cannot install solar panels on flats because they do not own the roof</div>
<script>
$(document).on('change','.q1-question',function(){
	if($(this).val()=="Flat"){
		$('#q1-alert').show();
		$('#q2-container').hide();
		$('.q6-question').prop('checked',false);
	} else {
		$('#q1-alert').hide();
		$('#q2-container').show();
	}
});
</script>
<?php if (@$values['a1']=="House"){ ?>
	<script>
	$(document).ready(function(){
		$('#q2-container').show();	
	})
	</script>
<?php } ?>
<?php if (@$values['a1']=="Flat"){ ?>
	<script>
	$(document).ready(function(){
		$('#q1-alert').show();
		$('.q6-question').prop('checked',false)
		$('.q7-question').prop('disabled',true)
	})
	</script>
<?php } ?>
<div id="q2-container" style="display:none">
         <label>Is the property owned, mortgaged or rented? <span class="glyphicon glyphicon-question-sign tt"
                                                       data-toggle="tooltip" data-placement="right"
                                                       title="If rented we cannot do it without the landlords consent. They must get the landlord to contact us directly 0800 8521247"></span></label>
                                                       
        <div class="radio">
            <label>
                <input class="q2-question" type="radio" name="answers[a2][]" id="optionsRadios1"
                       value="Home Owner"  <?php if (@strpos($values['a2'], "Home Owner") !== false) {
                    echo "checked";
                } ?> />
                Home Owner
            </label>
        </div>
              <div class="radio">
            <label>
                <input class="q2-question" type="radio" name="answers[a2][]" id="optionsRadios2"
                       value="Other tenant" <?php if (@strpos($values['a2'], "Other tenant") !== false) {
                    echo "checked";
                } ?>>
                Tenant
            </label>
        </div>
 </div>
             <script>
$(document).on('change','.q2-question',function(){
	if($(this).val()!=="Home Owner"){
			$('#q3-container').show();
			$('#q4-container').hide();
		
		$('.q6-question').prop('checked',false)
		$('.q7-question').prop('disabled',true)
	} else {
			$('#q3-container').hide();
			$('#q4-container').show();
			$('.q3-question').prop('checked',false)
	}
});
</script>
<?php if (@$values['a2']!== "Home Owner"&&@$values['a1']=="Flat"){ ?>
	<script>
	$(document).ready(function(){
	
	})
	</script>
<?php } ?>
<?php if (@$values['a2']=="Home Owner"&&@$values['a1']=="House"){ ?>
	<script>
	$(document).ready(function(){
		$('#q4-container').show();	
	})
	</script>
<?php } ?>
<?php if (@$values['a2']=="Other tenant"&&@$values['a1']=="House"){ ?>
	<script>
	$(document).ready(function(){
		$('#q3-container').show();	
	})
	</script>
<?php } ?>
        <div id="q3-container" style="display:none">
      <label>Is it a private tenancy?</label>
                <div class="radio">
            <label>
                <input class="q3-question" type="radio" name="answers[a3][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a3'], "Yes") !== false) {
                    echo "Yes";
                } ?> />
                Yes
            </label>
        </div>
        <div class="radio">
            <label>
                <input class="q3-question" type="radio" name="answers[a3][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a3'], "No") !== false) {
                    echo "No";
                } ?>>
                No
            </label>
        </div>
        </div> 
                <script>
$(document).on('change','.q3-question',function(){
	if($(this).val()=="Yes"){
			$('#q3-alert2').hide();
			$('#q3-alert').show();
	
	} else {
			$('#q3-alert').hide();
			$('#q3-alert2').show();

	}
});
</script>
<?php if (@$values['a3']=="Yes"&&@$values['a1']=="Flat"){ ?>
	<script>
	$(document).ready(function(){
		$('#q3-alert').show();
	})
	</script>
<?php } ?>  
<?php if (@$values['a3']=="No"&&@$values['a1']=="Flat"){ ?>
	<script>
	$(document).ready(function(){
	$('#q3-alert2').show();
	})
	</script>
<?php } ?>    
<div id="q3-alert" class="text-danger" style="display:none">The landlord must contact Global Heat Source directly on 0800 8521247</div>
<div id="q3-alert2" class="text-danger" style="display:none">We do not have a record of their details. Please explain we will need to get back to them. (Set the call outcome as <b>Query</b>)</div>        
   
        <div id="q4-container" style="display:none">
      <label>Is the ownership of the property in joint names?</label>
       <div class="radio">
            <label>
                <input class="q4-question" type="radio"  name="answers[a4][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a4'], "Yes") !== false) {
                    echo "checked";
                } ?> />
               Yes
            </label>
        </div>
        <div class="radio">
            <label>
                <input class="q4-question" type="radio" name="answers[a4][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a4'], "No") !== false) {
                    echo "checked";
                } ?>>
                No
            </label>
        </div>
        </div>
                        <script>
$(document).on('change','.q4-question',function(){
	if($(this).val()=="Yes"){
			$('#q6-container').show();
			$('#q5-container').show().find('input').val('');
	} else {
			$('#q6-container').show();
			$('#q5-container').hide();
	}
});
</script> 
<?php if (@$values['a4']== "Yes"&&@$values['a2']=="Home Owner"&&@$values['a1']=="House"){ ?>
	<script>
	$(document).ready(function(){
		$('#q6-container').show();
		$('#q5-container').show();
	})
	</script>
<?php } ?>
<?php if (@$values['a1']== "House"){ ?>
	<script>
	$(document).ready(function(){
		$('#q6-container').show();	
	})
	</script>
<?php } ?>
        <div id="q5-container" class="text-success" style="display:none">
        <label>Please capture the name of 2nd person</label><br>
        <input type="text" class="form-control" name="answers[a5]" value="<?php echo @$values['a5'] ?>">
        </div>
        <hr>
      
      <div id="q6-container" style="display:none">
      <label>Is the property mortgaged?</label>
       <div class="radio">
            <label>
                <input class="q6-question" type="radio"  name="answers[a6][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a6'], "Yes") !== false) {
                    echo "checked";
                } ?> />
               Yes
            </label>
        </div>
        <div class="radio">
            <label>
                <input class="q6-question" type="radio" name="answers[a6][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a6'], "No") !== false) {
                    echo "checked";
                } ?>>
                No
            </label>
        </div>
        </div>    
         <script>
$(document).on('change','.q6-question',function(){
	if($(this).val()=="Yes"){
			$('#q7-container').show();
			$('#hide').show();	
	} else {
			$('#q7-container').hide();
			$('#finished').show();
	}
});
</script> 
<?php if (@$values['a6']=="Yes"&&@$values['a1']=="House"){ ?>
	<script>
	$(document).ready(function(){
		$('#q7-container').show();	

	})
	</script>
<?php } ?>
<?php if (@$values['a6']=="No"&&@$values['a1']=="House"){ ?>
	<script>
	$(document).ready(function(){
		$('#finished').show();	
	})
	</script>
<?php } ?>
           <div id="q7-container" style="display:none">
      <label>Who is the mortgage provider?</label><br>
<select name="answers[a7]" class="selectpicker q7-question"  data-live-search="true" data-width="100%"
                    data-size="5" >
                      <option value=''>Please select a provider</option>
                       <option value='Other'>Other</option>
  <option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Abbey'>Abbey</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Accord'>Accord</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Aldermore'>Aldermore</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Alliance & Leicester'>Alliance & Leicester</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Allied Irish Bank (GB)'>Allied Irish Bank (GB)</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Amber Homeloans Ltd'>Amber Homeloans Ltd</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Ascendon'>Ascendon</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Bank of Ireland'>Bank of Ireland</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Bank of Scotland'>Bank of Scotland</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Barclays Bank'>Barclays Bank</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Barnsley'>Barnsley</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Bath Investment & Building Society'>Bath Investment & Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='BDW Trading'>BDW Trading</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Beverley'>Beverley</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Birmingham building society'>Birmingham building society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Birmingham Midshire'>Birmingham Midshire</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='BM Solutions'>BM Solutions</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Bradford & Bingley'>Bradford & Bingley</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Bristol & West'>Bristol & West</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Britannia'>Britannia</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Buckinghamshire Building Society'>Buckinghamshire Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='C & G Mortgage'>C & G Mortgage</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Cambridge Building Society'>Cambridge Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Capital Home Loans'>Capital Home Loans</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Charter'>Charter</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Chelsea Building Society'>Chelsea Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Cheltenham & Gloucester'>Cheltenham & Gloucester</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Chesham Building Society'>Chesham Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Cheshire Building Society'>Cheshire Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Chorley & District Building Society'>Chorley & District Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Church House Trust'>Church House Trust</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Clysedale Bank'>Clysedale Bank</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='CO-OP'>CO-OP</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Coventry'>Coventry</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Cumberland'>Cumberland</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Darlington Building Society'>Darlington Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Derbyshire'>Derbyshire</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Direct Line'>Direct Line</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Dudley Building Society'>Dudley Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Dunfermline Building Society'>Dunfermline Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Earl Shilton'>Earl Shilton</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Ecology Building Society'>Ecology Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Egg'>Egg</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Engage Credit'>Engage Credit</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Equity Release Mortgages Ltd'>Equity Release Mortgages Ltd</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='First Active'>First Active</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='First Direct'>First Direct</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='First National Bank'>First National Bank</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='First Trust Bank (NI)'>First Trust Bank (NI)</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Furness Building Society'>Furness Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Future mortgage ltd'>Future mortgage ltd</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='GE Money'>GE Money</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='GMAC - RFC'>GMAC - RFC</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='GMAC - RFC Partner'>GMAC - RFC Partner</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Halifax'>Halifax</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Hanley Economic Building Society'>Hanley Economic Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Harpenden Building Society'>Harpenden Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Heritable Bank'>Heritable Bank</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Hinckley & Rugby Building Society'>Hinckley & Rugby Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Holmesdale Building Society'>Holmesdale Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='HSBC'>HSBC</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='I Group loans'>I Group loans</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Infinity'>Infinity</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Intelligent Finance'>Intelligent Finance</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Ipswich Building Society'>Ipswich Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='J Casey & Co'>J Casey & Co</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='JP Morgan'>JP Morgan</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Just Retirement ltd'>Just Retirement ltd</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Kensington Mortgages'>Kensington Mortgages</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Kent Reliance Building Society'>Kent Reliance Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Leeds Building Society'>Leeds Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Leek United'>Leek United</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Lloyds TSB'>Lloyds TSB</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='London Mortgage Co'>London Mortgage Co</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='London Scottish'>London Scottish</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Loughborough'>Loughborough</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Manchester'>Manchester</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Mansfield'>Mansfield</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Market Harborough Building Society'>Market Harborough Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Marsden'>Marsden</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Melton Mowbray'>Melton Mowbray</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Monmouthshire Building Society'>Monmouthshire Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Morgan Stanley'>Morgan Stanley</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Mortgage agency services 1-5'>Mortgage agency services 1-5</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Mortgage agency services limited'>Mortgage agency services limited</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Mortgage Express'>Mortgage Express</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Mortgage Next'>Mortgage Next</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Mortgage Trust'>Mortgage Trust</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Mortgages plc'>Mortgages plc</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='National Counties'>National Counties</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Nationwide Building Society'>Nationwide Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='NatWest'>NatWest</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='New Life'>New Life</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Newbury Building Society'>Newbury Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Newcastle Building Society'>Newcastle Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Northern Bank Ltd'>Northern Bank Ltd</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Northern rock asset management'>Northern rock asset management</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Norton Home Loans'>Norton Home Loans</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Norwich & Peterborough Building Society'>Norwich & Peterborough Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Nottingham Building Society'>Nottingham Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='NRAM'>NRAM</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Oakwood Homes Ltd'>Oakwood Homes Ltd</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Other'>Other</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Papilio UK'>Papilio UK</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Paragon'>Paragon</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Paratus'>Paratus</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Penrith Building Society'>Penrith Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Pink Home Loans'>Pink Home Loans</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Platform Funding'>Platform Funding</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Portman Building Society'>Portman Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Preferred mortgages'>Preferred mortgages</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Premier Mortgage Service'>Premier Mortgage Service</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Principality'>Principality</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Progressive Building Society'>Progressive Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Prudential'>Prudential</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='RBS IP First Active'>RBS IP First Active</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='RBS IP Natwest'>RBS IP Natwest</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='RBS IP One Account'>RBS IP One Account</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='RBS IP Royal Bank Of Scotland'>RBS IP Royal Bank Of Scotland</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Redstone'>Redstone</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Rooftop Mortgages'>Rooftop Mortgages</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Royal Bank of Scotland'>Royal Bank of Scotland</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Saffron'>Saffron</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Santander'>Santander</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Scarborough Building Society'>Scarborough Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Scotland'>Scotland</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Scottish Building Society'>Scottish Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Scottish Widows Bank'>Scottish Widows Bank</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Shepshed Building Society'>Shepshed Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Skipton'>Skipton</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Smile'>Smile</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Southern Pacific'>Southern Pacific</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Stafford Railway'>Stafford Railway</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Standard Life Bank'>Standard Life Bank</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Stroud & Swindon Building Society'>Stroud & Swindon Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Teachers Building Society'>Teachers Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Tesco Personal Finance'>Tesco Personal Finance</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='The Mortgage Business PLC'>The Mortgage Business PLC</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='The Mortgage Works'>The Mortgage Works</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='The One account'>The One account</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Tipton & Coseley'>Tipton & Coseley</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Topez'>Topez</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='TSB'>TSB</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='UBS'>UBS</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='UCB Home Loans'>UCB Home Loans</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Ulster Bank (NI)'>Ulster Bank (NI)</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Universal'>Universal</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Vernon'>Vernon</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='VIRGIN MONEY'>VIRGIN MONEY</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='West Bromwich Building Society'>West Bromwich Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Woolwich Bromwich Building Society'>Woolwich Bromwich Building Society</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Woolwich Mortgage'>Woolwich Mortgage</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Yorkshire Bank'>Yorkshire Bank</option>
<option <?php if(@$values['a7']=="Abbey"){ echo "selected"; } ?> value='Yorkshire Building Society'>Yorkshire Building Society</option>
            </select>
            <br>
             <label>If other, capture the name</label><br>
        <input type="text" class="form-control" name="answers[a9]" value="<?php echo @$values['a9'] ?>">
        </div> 
              
        
            <script>
$(document).on('change','.q7-question',function(){
	var lenders = new Array("BDW Trading","Birmingham building society","Bradford & Bingley","Britannia","Church House Trust","Clysedale Bank","CO-OP","Engage Credit","Future mortgage ltd","GE Money","I Group loans","J Casey & Co","JP Morgan","Kensington Mortgages","Morgan Stanley","Mortgage agency services 1-5","Mortgage agency services limited","Mortgage Express","New Life","Northern rock asset management","Norton Home Loans","Papilio UK Equity Release Mortgages Ltd","Paragon","Paratus","Platform Funding","Preferred mortgages","Principality","Prudential","Redstone","Rooftop Mortgages","Saffron","Skipton","South Pacific","The Mortgage Business PLC","West Bromwich");

	var prefered = $.inArray($(this).val(),lenders );
	if(!prefered){
	$('#q7-alert').show();	
	$('#finished').hide();	
	} else {
		$('#q7-alert').hide();	
			$('#finished').show();	
	}

});
</script> 
<?php if (@!empty($values['a7'])&&@$values['a1']=="house"&&@$values['a6']=="yes"){ ?>
	<script>
	$(document).ready(function(){
		$('.q7-question').trigger('change');
	})
	</script>
<?php } ?>
   <div class="text-danger" id="q7-alert"  style="display:none">You need to email the homeowner a mortage consent letter which will need to be signed and returned before we can proceed. The details will be in the letter</div>
        <div class="text-success" id="finished"  style="display:none">They have met the initial critera and could be eligible for the offer pending some additional checks. Explain that we will be in touch.</div>



<hr>
        <a href="<?php echo base_url() . 'records/detail/' . $this->uri->segment(4); ?>" class="btn btn-default">Go
            back</a>
        <button type="submit" id="save-form" class="btn btn-primary">Save form</button>

    </form>

</div>

<div class="page-success alert alert-success hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div class="page-info alert alert-info hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div class="page-warning alert alert-warning hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div class="page-danger alert alert-danger hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-datetimepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-slider.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/browser/jquery.browser.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/modals.js"></script>
<script src="<?php echo base_url(); ?>assets/js/main.js"></script>

<script>
    $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip()

        $(document).on('blur', 'input[type="text"]', function () {
            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1'
            })
        });

        $(document).on('change', 'select,input[type="radio"]', function () {
            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1'
            })
        });
        $(document).on('click', '#save-form', function (e) {
            console.log($('#form').serialize());
            e.preventDefault();
            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1'
            }).done(function (response) {
                flashalert.success("Form was saved");
            });
        });




    });
</script>


<?php //load specific javascript files set in the controller
if (isset($javascript)):
    foreach ($javascript as $file): ?>
        <script src="<?php echo base_url(); ?>assets/js/<?php echo $file ?>"></script>
    <?php endforeach;
endif; ?>

</body>
</html>