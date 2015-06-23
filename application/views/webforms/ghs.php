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
<div id="q3-alert2" class="text-danger" style="display:none">We do not have a record of their details. Please ask them to contact GHS direct on 0800 8521247</div>        
   
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
			$('#q5-container').show();
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
  <option value='Abbey'>Abbey</option>
<option value='Accord'>Accord</option>
<option value='Aldermore'>Aldermore</option>
<option value='Alliance & Leicester'>Alliance & Leicester</option>
<option value='Allied Irish Bank (GB)'>Allied Irish Bank (GB)</option>
<option value='Amber Homeloans Ltd'>Amber Homeloans Ltd</option>
<option value='Ascendon'>Ascendon</option>
<option value='Bank of Ireland'>Bank of Ireland</option>
<option value='Bank of Scotland'>Bank of Scotland</option>
<option value='Barclays Bank'>Barclays Bank</option>
<option value='Barnsley'>Barnsley</option>
<option value='Bath Investment & Building Society'>Bath Investment & Building Society</option>
<option value='BDW Trading'>BDW Trading</option>
<option value='Beverley'>Beverley</option>
<option value='Birmingham building society'>Birmingham building society</option>
<option value='Birmingham Midshire'>Birmingham Midshire</option>
<option value='BM Solutions'>BM Solutions</option>
<option value='Bradford & Bingley'>Bradford & Bingley</option>
<option value='Bristol & West'>Bristol & West</option>
<option value='Britannia'>Britannia</option>
<option value='Buckinghamshire Building Society'>Buckinghamshire Building Society</option>
<option value='C & G Mortgage'>C & G Mortgage</option>
<option value='Cambridge Building Society'>Cambridge Building Society</option>
<option value='Capital Home Loans'>Capital Home Loans</option>
<option value='Charter'>Charter</option>
<option value='Chelsea Building Society'>Chelsea Building Society</option>
<option value='Cheltenham & Gloucester'>Cheltenham & Gloucester</option>
<option value='Chesham Building Society'>Chesham Building Society</option>
<option value='Cheshire Building Society'>Cheshire Building Society</option>
<option value='Chorley & District Building Society'>Chorley & District Building Society</option>
<option value='Church House Trust'>Church House Trust</option>
<option value='Clysedale Bank'>Clysedale Bank</option>
<option value='CO-OP'>CO-OP</option>
<option value='Coventry'>Coventry</option>
<option value='Cumberland'>Cumberland</option>
<option value='Darlington Building Society'>Darlington Building Society</option>
<option value='Derbyshire'>Derbyshire</option>
<option value='Direct Line'>Direct Line</option>
<option value='Dudley Building Society'>Dudley Building Society</option>
<option value='Dunfermline Building Society'>Dunfermline Building Society</option>
<option value='Earl Shilton'>Earl Shilton</option>
<option value='Ecology Building Society'>Ecology Building Society</option>
<option value='Egg'>Egg</option>
<option value='Engage Credit'>Engage Credit</option>
<option value='Equity Release Mortgages Ltd'>Equity Release Mortgages Ltd</option>
<option value='First Active'>First Active</option>
<option value='First Direct'>First Direct</option>
<option value='First National Bank'>First National Bank</option>
<option value='First Trust Bank (NI)'>First Trust Bank (NI)</option>
<option value='Furness Building Society'>Furness Building Society</option>
<option value='Future mortgage ltd'>Future mortgage ltd</option>
<option value='GE Money'>GE Money</option>
<option value='GMAC - RFC'>GMAC - RFC</option>
<option value='GMAC - RFC Partner'>GMAC - RFC Partner</option>
<option value='Halifax'>Halifax</option>
<option value='Hanley Economic Building Society'>Hanley Economic Building Society</option>
<option value='Harpenden Building Society'>Harpenden Building Society</option>
<option value='Heritable Bank'>Heritable Bank</option>
<option value='Hinckley & Rugby Building Society'>Hinckley & Rugby Building Society</option>
<option value='Holmesdale Building Society'>Holmesdale Building Society</option>
<option value='HSBC'>HSBC</option>
<option value='I Group loans'>I Group loans</option>
<option value='Infinity'>Infinity</option>
<option value='Intelligent Finance'>Intelligent Finance</option>
<option value='Ipswich Building Society'>Ipswich Building Society</option>
<option value='J Casey & Co'>J Casey & Co</option>
<option value='JP Morgan'>JP Morgan</option>
<option value='Just Retirement ltd'>Just Retirement ltd</option>
<option value='Kensington Mortgages'>Kensington Mortgages</option>
<option value='Kent Reliance Building Society'>Kent Reliance Building Society</option>
<option value='Leeds Building Society'>Leeds Building Society</option>
<option value='Leek United'>Leek United</option>
<option value='Lloyds TSB'>Lloyds TSB</option>
<option value='London Mortgage Co'>London Mortgage Co</option>
<option value='London Scottish'>London Scottish</option>
<option value='Loughborough'>Loughborough</option>
<option value='Manchester'>Manchester</option>
<option value='Mansfield'>Mansfield</option>
<option value='Market Harborough Building Society'>Market Harborough Building Society</option>
<option value='Marsden'>Marsden</option>
<option value='Melton Mowbray'>Melton Mowbray</option>
<option value='Monmouthshire Building Society'>Monmouthshire Building Society</option>
<option value='Morgan Stanley'>Morgan Stanley</option>
<option value='Mortgage agency services 1-5'>Mortgage agency services 1-5</option>
<option value='Mortgage agency services limited'>Mortgage agency services limited</option>
<option value='Mortgage Express'>Mortgage Express</option>
<option value='Mortgage Next'>Mortgage Next</option>
<option value='Mortgage Trust'>Mortgage Trust</option>
<option value='Mortgages plc'>Mortgages plc</option>
<option value='National Counties'>National Counties</option>
<option value='Nationwide Building Society'>Nationwide Building Society</option>
<option value='NatWest'>NatWest</option>
<option value='New Life'>New Life</option>
<option value='Newbury Building Society'>Newbury Building Society</option>
<option value='Newcastle Building Society'>Newcastle Building Society</option>
<option value='Northern Bank Ltd'>Northern Bank Ltd</option>
<option value='Northern rock asset management'>Northern rock asset management</option>
<option value='Norton Home Loans'>Norton Home Loans</option>
<option value='Norwich & Peterborough Building Society'>Norwich & Peterborough Building Society</option>
<option value='Nottingham Building Society'>Nottingham Building Society</option>
<option value='NRAM'>NRAM</option>
<option value='Oakwood Homes Ltd'>Oakwood Homes Ltd</option>
<option value='Other'>Other</option>
<option value='Papilio UK'>Papilio UK</option>
<option value='Paragon'>Paragon</option>
<option value='Paratus'>Paratus</option>
<option value='Penrith Building Society'>Penrith Building Society</option>
<option value='Pink Home Loans'>Pink Home Loans</option>
<option value='Platform Funding'>Platform Funding</option>
<option value='Portman Building Society'>Portman Building Society</option>
<option value='Preferred mortgages'>Preferred mortgages</option>
<option value='Premier Mortgage Service'>Premier Mortgage Service</option>
<option value='Principality'>Principality</option>
<option value='Progressive Building Society'>Progressive Building Society</option>
<option value='Prudential'>Prudential</option>
<option value='RBS IP First Active'>RBS IP First Active</option>
<option value='RBS IP Natwest'>RBS IP Natwest</option>
<option value='RBS IP One Account'>RBS IP One Account</option>
<option value='RBS IP Royal Bank Of Scotland'>RBS IP Royal Bank Of Scotland</option>
<option value='Redstone'>Redstone</option>
<option value='Rooftop Mortgages'>Rooftop Mortgages</option>
<option value='Royal Bank of Scotland'>Royal Bank of Scotland</option>
<option value='Saffron'>Saffron</option>
<option value='Santander'>Santander</option>
<option value='Scarborough Building Society'>Scarborough Building Society</option>
<option value='Scotland'>Scotland</option>
<option value='Scottish Building Society'>Scottish Building Society</option>
<option value='Scottish Widows Bank'>Scottish Widows Bank</option>
<option value='Shepshed Building Society'>Shepshed Building Society</option>
<option value='Skipton'>Skipton</option>
<option value='Smile'>Smile</option>
<option value='Southern Pacific'>Southern Pacific</option>
<option value='Stafford Railway'>Stafford Railway</option>
<option value='Standard Life Bank'>Standard Life Bank</option>
<option value='Stroud & Swindon Building Society'>Stroud & Swindon Building Society</option>
<option value='Teachers Building Society'>Teachers Building Society</option>
<option value='Tesco Personal Finance'>Tesco Personal Finance</option>
<option value='The Mortgage Business PLC'>The Mortgage Business PLC</option>
<option value='The Mortgage Works'>The Mortgage Works</option>
<option value='The One account'>The One account</option>
<option value='Tipton & Coseley'>Tipton & Coseley</option>
<option value='Topez'>Topez</option>
<option value='TSB'>TSB</option>
<option value='UBS'>UBS</option>
<option value='UCB Home Loans'>UCB Home Loans</option>
<option value='Ulster Bank (NI)'>Ulster Bank (NI)</option>
<option value='Universal'>Universal</option>
<option value='Vernon'>Vernon</option>
<option value='VIRGIN MONEY'>VIRGIN MONEY</option>
<option value='West Bromwich Building Society'>West Bromwich Building Society</option>
<option value='Woolwich Bromwich Building Society'>Woolwich Bromwich Building Society</option>
<option value='Woolwich Mortgage'>Woolwich Mortgage</option>
<option value='Yorkshire Bank'>Yorkshire Bank</option>
<option value='Yorkshire Building Society'>Yorkshire Building Society</option>
            </select>
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