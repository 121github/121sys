<?php if(!isset($details['record']['urn'])): ?>

There was a problem while finding the selected record details. Maybe it does not exist or has been deleted.
<?php else: ?>
<div class="page-header">
  <h2>View Details <small>URN: <?php echo $details['record']['urn'] ?> <?php echo (!empty($details['record']['campaign'])?" [". $details['record']['campaign']."]":"") ?></small><span class="pull-right">
    <?php if(!empty($nav['prev'])): ?>
    <a type="button" class="btn btn-default btn-lg" href="<?php echo $nav['prev'] ?>">Previous</a>
    <?php endif ?>
    <?php if(!empty($nav['next'])): ?>
    <a type="button" class="btn btn-default btn-lg" href="<?php echo $nav['next'] ?>">Next</a>
    <?php endif ?>
    </span></h2>
</div>

<div class="row">
  <div class="col-md-6 col-sm-12"> 
     <?php foreach($features as $k=>$v){
		if($k%2==1){
     $this->view('records/panels/'.$panels[$v],$details);
		}
 } ?>
</div>
  <div class="col-md-6 col-sm-12"> 
       <?php foreach($features as $k=>$v){
		if($k%2==0){
     $this->view('records/panels/'.$panels[$v],$details);
		}
 } ?>
  </div>
</div>
<!-- end row panel -->
</div>
<!-- end fluid container --> 
<!-- start survey popup -->
<?php if(in_array(11,$features)){ ?>
<div class="panel panel-primary survey-container">
  <?php 
$this->view('forms/new_survey_form.php',$survey_options); ?>
</div>
<?php } ?>
<?php if(in_array(1,$features)){ ?>
<div class="panel panel-primary xfer-container">
  <?php 
$this->view('forms/cross_transfer_form.php',$xfer_campaigns); ?>
</div>
<?php } ?>
<!-- end survey popup -->

<!-- start email popup -->
<?php if(in_array(9,$features)){ ?>
	<div class="panel panel-primary email-container">
		<?php $this->view('forms/new_email_form.php',$email_options); ?>
	</div>
	<div class="panel panel-primary email-view-container">
		<?php $this->view('email/view_email.php'); ?>
	</div>
<?php } ?>
<!-- end email popup -->

<script type="text/javascript">
    $(document).ready(function () {   
        var urn = '<?php echo $details['record']['urn'] ?>';
		var campaign = '<?php echo $details['record']['campaign_id'] ?>';
		var role_id = '<?php echo $_SESSION['role'] ?>';
        record.init(urn,role_id,campaign);
		//initializing the generic panels
		record.contact_panel.init();
		record.update_panel.init();
		//initializing the panels for this campaign
		<?php if(in_array(2,$features)){ ?>
        record.company_panel.init();
		<?php } ?>
		<?php if(in_array(4,$features)){ ?>
        record.sticky_note.init();
		<?php } ?>
		<?php if(in_array(5,$features)){ ?>
        record.ownership_panel.init();
		<?php } ?>
		<?php if(in_array(6,$features)){ ?>
		record.script_panel();
		<?php } ?>
		<?php if(in_array(7,$features)){ ?>
        record.history_panel.init();
		<?php } ?>
		<?php if(in_array(8,$features)){ ?>
		record.additional_info.init();
		<?php } ?>
		<?php if(in_array(9,$features)){ ?>
        record.email_panel.init();
		<?php } ?>
		<?php if(in_array(10,$features)){ ?>
		record.appointment_panel.init();
		<?php } ?>
		<?php if(in_array(11,$features)){ ?>
        record.surveys_panel.init();
		<?php } ?>
		<?php if(in_array(12,$features)){ ?>
		record.recordings_panel.init();
		<?php } ?>

    });
</script>
<?php endif; ?>
