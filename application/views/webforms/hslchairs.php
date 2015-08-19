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
    <title>HSL Pre-Consultation Checklist</title>
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
      <h2>HSL Pre-Consultation Checklist</h2>
      <p>Please complete the following questions and click save</p>
      <form id="form" style="padding-bottom:50px;">
    <div id="q1-container">
          <label>Where did you hear about us?</label>
          <br>
          <select name="answers[a1]" class="selectpicker q1-question" data-width="100%"
                    data-size="5" >
        <option value='Mail / Leaflet'>Mail / Leaflet</option>
        <option value='Referral'>Referral</option>
        <option value='Newspaper'>Newspaper</option>
        <option value='Website'>Website</option>
        <option value='Radio'>Radio</option>
        <option value='Facebook'>Facebook</option>
        <option value='Family'>Family</option>
        <option value='Friend'>Friend</option>
        <option value='Neighbour'>Neighbour</option>
      </select>
        </div>
         <input name="contact[contact_id]" value="<?php echo @$contact['contact_id'] ?>" type="hidden" /> 
          <div class="form-group" >
          <label>Customer Name</label>
          <br>
          <input name="fullname" readonly class="form-control" placeholder="Enter the customer name" value="<?php echo $contact['name'] ?>"/>
        </div>
              <div class="form-group relative">
          <label>Customer date of birth?</label>
          <br>
          <input name="contact[dob]" class="form-control dob" placeholder="Enter the date of birth"  <?php if(!empty($contact['dob'])) { echo "value='".@$contact['dob']."'"; } ?> />
        </div>
        <script type="text/javascript">
		$(document).ready(function(){
			$('.dob').datetimepicker({
        viewMode: 'years',
        format: 'DD/MM/YYYY',
		defaultDate: new Date(1979, 0, 1,1, 0, 0, 0),
    }).on('keypress paste', function (e) {
  e.preventDefault();
  return false;
});	
		});
		</script>
         <div class="form-group" id="q14-container">
          <label>Customer height?</label>
          <br>
          <input name="answers[a14]" class="form-control" placeholder="Enter the approximate height of the customer" value="<?php echo @$values['a14'] ?>"/>
        </div>
    <div class="form-group" id="q2-container">
          <label>Reason home consultation required?</label>
          <br>
          <input name="answers[a2]" class="form-control" placeholder="Enter the reason for the home consultation" value="<?php echo @$values['a2'] ?>" />
        </div>
    <div id="q3-container">
          <label>Does customer need assistance to stand/transfer independantly?</label>
          <div class="radio">
        <label>
              <input class="q3-question helper-required" type="radio" name="answers[a3][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a3'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
          <div class="radio">
        <label>
              <input class="q3-question helper-required" type="radio" name="answers[a3][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a3'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        </div>
    <div id="q4-container">
          <label>Can Customer walk unaided?</label>
          <div class="radio">
        <label>
              <input class="q4-question helper-required" type="radio" name="answers[a4][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a4'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
          <div class="radio">
        <label>
              <input class="q4-question helper-required" type="radio" name="answers[a4][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a4'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        </div>
            <div id="q5-container">
          <label>Does Customer have any problems with sight/hearing/speech/memory?</label>
          <div class="radio">
        <label>
              <input class="q5-question helper-required" type="radio" name="answers[a5][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a5'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
          <div class="radio">
        <label>
              <input class="q5-question helper-required" type="radio" name="answers[a5][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a5'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        </div>
            <div id="q6-container" style="display:none">
          <label>Can anyone familiar with the customers needs also be available during the Home Consultation?</label>
          <div class="radio">
        <label>
              <input class="q6-question" type="radio" name="answers[a6][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a6'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
          <div class="radio">
        <label>
              <input class="q6-question" type="radio" name="answers[a6][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a6'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
      
                    <div class="form-group" id="q7-container">
          <label>Please capture the name of the person if applicable</label>
          <br>
          <input name="answers[a7]" class="form-control" placeholder="2nd persons name"/>
        </div>
        </div>
        <script type="text/javascript">

			
			function check_answers(){
			if($('.q3-question:checked').val()=="Yes"){
					$('#q6-container').show();
				} else if($('.q4-question:checked').val()=="No"){
					$('#q6-container').show();
				} else if($('.q5-question:checked').val()=="Yes"){
					$('#q6-container').show();
				} else {
					$('#q6-container').hide();
				}	
			}
					$(document).ready(function(){
			$(document).on('change','.helper-required',function(){
				check_answers();
			});
		});
		</script>
        
            <div id="q8-container">
          <label>Are there any vehicle or parking restrictions at the Customer's Accommodation?</label>
          <div class="radio">
        <label>
              <input class="q8-question" type="radio" name="answers[a8][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a8'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
          <div class="radio">
        <label>
              <input class="q8-question" type="radio" name="answers[a8][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a8'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        </div>
            <div id="q9-container">
          <label>Are there any issues with stairs/doorways at the Customer's Accommodation?</label>
          <div class="radio">
        <label>
              <input class="q9-question" type="radio" name="answers[a9][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a9'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
          <div class="radio">
        <label>
              <input class="q9-question" type="radio" name="answers[a9][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a9'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        </div>
            <div id="q4-container">
          <label>Is there a power supply and space to set demonstrate and demonstrate the chair(s)?</label>
          <div class="radio">
        <label>
              <input class="q10-question" type="radio" name="answers[a10][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a10'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
          <div class="radio">
        <label>
              <input class="q10-question" type="radio" name="answers[a10][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a10'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        </div>
                    <div id="q11-container">
          <label>Advise the Customer that the Home Consultation will take approximately 2 hours</label>
          <div class="radio">
        <label>
              <input class="q11-question" type="radio" name="answers[a11][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a11'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
          <div class="radio">
        <label>
              <input class="q11-question" type="radio" name="answers[a11][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a11'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        </div>
                    <div id="q12-container">
          <label>Advise the customer that the home consultant  will telephone them the day before the home consultation and if known, provide the names of the home consultant and driver?</label>
          <div class="radio">
        <label>
              <input class="q12-question" type="radio" name="answers[a12][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a12'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
          <div class="radio">
        <label>
              <input class="q12-question" type="radio" name="answers[a12][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a12'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        </div>
        
        <div class="form-group" id="q13-container">
          <label>Any other information relevant</label>
<br>
              <textarea class="form-control q13-question" style="height:50px" name="answers[a13][]"><?php echo @$values['a13'] ?></textarea>
        </div>
        
    <a href="<?php echo base_url() . 'records/detail/' . $this->uri->segment(4); ?>" class="btn btn-default">Go
        back</a>
    <?php if(@!empty($values['completed_on'])){ ?>
    <button type="submit" id="save-form" class="btn btn-primary">Save form</button>
    <?php } else { ?>
    <button type="submit" id="complete-form" class="btn btn-primary">Save form</button>
    <?php } ?>
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
		$(document).on('click', '#complete-form', function (e) {
            console.log($('#form').serialize());
            e.preventDefault();
            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1&complete=1'
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