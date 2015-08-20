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
    <div>
          <label>Where did you hear about us?</label>
          <br>
          <select name="answers[a22]" class="selectpicker" data-width="100%"
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
        	<input id="contact-id" name="answers[a1]" value="<?php echo @$values['a1'] ?>" type="hidden" /> 
          <input id="contact-fullname" name="answers[a2]" value="<?php echo @$values['a2'] ?>" type="hidden" /> 
          <div class="form-group" >
          <label>Customer Name</label>
          <br>
          <select id="contact-name">
          <?php foreach($contacts as $contact){ ?>
          <option <?php if($contact['contact_id']==$values['a1']) { echo "selected"; } ?> value="<?php echo $contact['contact_id'] ?>"><?php echo $contact['name'] ?></option>
          <?php } ?>
          </select>
          <script type="text/javascript">
		$(document).ready(function(){
			$("#contact-name").selectpicker();
			$(document).on('change','#contact-name',function(){
				$("#contact-id").val($(this).val());
				$('#contact-fullname').val($("#contact-name option:selected").text());
				
			});
		});
		  </script>
        </div>
              <div class="form-group relative">
          <label>Customer age?</label>
          <br>
          <input type="text" name="answers[a3]" class="form-control" placeholder="Enter the age in years"  value="<?php echo @$values['a3'] ?>" />
        </div>
        <script type="text/javascript">
		/*$(document).ready(function(){
			$('.dob').datetimepicker({
        viewMode: 'years',
        format: 'DD/MM/YYYY',
		defaultDate: new Date(1979, 0, 1,1, 0, 0, 0),
    }).on('keypress paste', function (e) {
  e.preventDefault();
  return false;
});	
		});
		*/
		</script>
         <div class="form-group">
          <label>Customer height?</label>
          <br>
          <input type="text" name="answers[a4]" class="form-control" style="width:50px; display:inline-block" value="<?php echo @$values['a4'] ?>"/> <span>Feet</span>
          <input type="text" name="answers[a5]" class="form-control" style="width:50px;display:inline-block" value="<?php echo @$values['a5'] ?>"/> <span>Inches</span>
        </div>
    <div class="form-group">
          <label>Reason home consultation required?</label>
          <br>
          <input type="text" name="answers[a6]" class="form-control" placeholder="Enter the reason for the home consultation" value="<?php echo @$values['a6'] ?>" />
        </div>
   <div class="question">
          <label>Does customer need assistance to stand/transfer independantly?</label><br>
          <div class="radio" style="display:inline-block">
        <label>
              <input type="radio" name="answers[a7][]" id="optionsRadios1" data-show-notes="true"
                       value="Yes"  <?php if (@strpos($values['a7'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
          <div class="radio" style="display:inline-block; margin-left:20px">
        <label>
              <input class="q3-question helper-required" type="radio" name="answers[a7][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a7'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        <div class="form-group">
      <label>Enter notes here</label>
        <input type="text" name="answers[a8]" class="form-control" placeholder="Enter notes if they answered yes above " value="<?php echo @$values['a8'] ?>"/>
        </div>
        </div>
    <div class="question">
          <label>Can the customer walk unaided?</label><br>
          <div class="radio" style="display:inline-block">
        <label>
              <input type="radio" name="answers[a9][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a9'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
              <div class="radio" style="display:inline-block; margin-left:20px">
        <label>
              <input type="radio" name="answers[a9][]" id="optionsRadios2" data-show-notes="true"
                       value="No" <?php if (@strpos($values['a9'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
       <div class="form-group">
      <label>Enter notes here</label>
        <input type="text" name="answers[a10]" class="form-control" placeholder="Enter notes if they answered no above " value="<?php echo @$values['a10'] ?>"/>
        </div>
        </div>
               <div class="question">
          <label>Does Customer have any problems with sight/hearing/speech/memory?</label><br>
          <div class="radio" style="display:inline-block">
        <label>
              <input type="radio" name="answers[a11][]" id="optionsRadios1" data-show-notes="true"
                       value="Yes"  <?php if (@strpos($values['a11'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
              <div class="radio" style="display:inline-block; margin-left:20px">
        <label>
              <input type="radio" name="answers[a11][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a11'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
       <div class="form-group">
      <label>Enter notes here</label>
        <input type="text" name="answers[a12]" value="<?php echo @$values['a12'] ?>" class="form-control" placeholder="Enter notes if they answered yes above "/>
        </div>
        </div>
             <div>
          <label>Can anyone familiar with the customers needs also be available during the Home Consultation?</label><br>
          <div class="radio" style="display:inline-block">
        <label>
              <input type="radio" name="answers[a12][]" id="optionsRadios1" 
                       value="Yes"  <?php if (@strpos($values['a12'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
                <div class="radio" style="display:inline-block; margin-left:20px">
        <label>
              <input type="radio" name="answers[a12][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a12'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
              </div>  
                             <div class="form-group">
          <label>Please capture the name of the helper/carer if applicable</label>
          <br>
          <input type="text" name="answers[a13]" value="<?php echo @$values['a13'] ?>" class="form-control" placeholder="Helper/carers persons name"/>
        </div>      
              <div class="question">
          <label>Are there any vehicle or parking restrictions at the Customer's Accommodation?</label><br>
          <div class="radio" style="display:inline-block">
        <label>
              <input type="radio" name="answers[a13][]" id="optionsRadios1" data-show-notes="true"
                       value="Yes"  <?php if (@strpos($values['a13'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
              <div class="radio" style="display:inline-block; margin-left:20px">
        <label>
              <input type="radio" name="answers[a13][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a13'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
      <div class="form-group">
          <label>Please enter notes</label>
          <br>
          <input type="text" name="answers[a14]" value="<?php echo @$values['a14'] ?>" class="form-control" placeholder="Add parking details if applicable"/>
        </div>
        </div>
               <div class="question">
          <label>Are there any issues with stairs/doorways at the Customer's Accommodation?</label><br>
          <div class="radio" style="display:inline-block">
        <label>
              <input type="radio" name="answers[a15][]" id="optionsRadios1" data-show-notes="true"
                       value="Yes"  <?php if (@strpos($values['a15'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
               <div class="radio" style="display:inline-block; margin-left:20px">
        <label>
              <input type="radio" name="answers[a15][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a15'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
      <div class="form-group">
          <label>Please enter notes</label>
          <br>
          <input type="text" name="answers[a16]" value="<?php echo @$values['a16'] ?>" class="form-control" placeholder="Enter access notes here if applicable"/>
        </div>
        </div>
             <div class="question">
          <label>Confirm there is a power supply and space to set demonstrate and demonstrate the chair(s)?</label><br>
          <div class="radio" style="display:inline-block">
        <label>
              <input type="radio" name="answers[a17][]" id="optionsRadios1" data-show-notes="true"
                       value="Yes"  <?php if (@strpos($values['a17'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
             <div class="radio" style="display:inline-block; margin-left:20px">
        <label>
              <input type="radio" name="answers[a17][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a17'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
       <div class="form-group">
          <label>Please enter notes</label>
          <br>
          <input type="text" name="answers[a18]" value="<?php echo @$values['a18'] ?>" class="form-control" placeholder="Enter access power supply notes here if applicable"/>
        </div>
        </div>
           <div class="question">
          <label>Confirm that the Home Consultation will take approximately 1 hours</label><br>
          <div class="radio" style="display:inline-block">
        <label>
              <input type="radio" name="answers[a19][]" id="optionsRadios1" data-show-notes="true"
                       value="Yes"  <?php if (@strpos($values['a19'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
                <div class="radio" style="display:inline-block; margin-left:20px">
        <label>
              <input type="radio" name="answers[a19][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a19'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        </div>
                    <div>
          <label>Confirm the home consultant will telephone them the day before the consultation and if known, provide the names of the home consultant and driver?</label><br>
          <div class="radio" style="display:inline-block">
        <label>
              <input type="radio" name="answers[a20][]" id="optionsRadios1"
                       value="Yes"  <?php if (@strpos($values['a20'], "Yes") !== false) {
                    echo "checked";
                } ?> />
              Yes </label>
      </div>
               <div class="radio" style="display:inline-block; margin-left:20px">
        <label>
              <input type="radio" name="answers[a20][]" id="optionsRadios2"
                       value="No" <?php if (@strpos($values['a20'], "No") !== false) {
                    echo "checked";
                } ?>>
              No </label>
      </div>
        </div>
        
        <div class="form-group">
          <label>Any other information relevant</label>
<br>
              <textarea class="form-control" style="height:50px" name="answers[a21][]"><?php echo @$values['a21'] ?></textarea>
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
	//hide all the notes		
	
	$.each($('.question'),function(){ 
$(this).find('.form-group').hide();
});
	
	$.each($('[data-show-notes="true"]:checked'),function(){
		$(this).closest('.question').find('.form-group').show();
	});
	

        $(document).on('blur', 'input[type="text"]', function () {
            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1'
            })
        });

        $(document).on('change', 'select,input[type="radio"]', function () {
			console.log($(this).attr('data-show-notes'));
			if($(this).attr('data-show-notes')){
				$(this).closest('.question').find('.form-group').show();
			} else {
			$(this).closest('.question').find('.form-group').hide().find('input').val('');	
			}
			
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