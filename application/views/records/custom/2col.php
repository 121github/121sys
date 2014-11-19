<?php if(!isset($details['record']['urn'])): ?>

There was a problem while finding the selected record details. Maybe it does not exist or has been deleted.
<?php else: ?>
<div class="page-header">
  <h2>View Details <small>URN: <?php echo $details['record']['urn'] ?> <?php echo (!empty($details['record']['campaign'])?" [". $details['record']['campaign']."]":"") ?></small><span class="pull-right">
    <?php if(!empty($nav['prev'])&&!$automatic&&in_array("search records",$_SESSION['permissions'])): ?>
    <a type="button" class="btn btn-default btn-lg" href="<?php echo $nav['prev'] ?>">Previous</a>
    <?php endif ?>
    <?php if(!empty($nav['next'])&&!$automatic&&in_array("search records",$_SESSION['permissions'])): ?>
    <a type="button" class="btn btn-default btn-lg" href="<?php echo $nav['next'] ?>">Next</a>
    <?php endif ?>
    <?php if($automatic): ?>
    <a type="button" class="btn btn-default btn-lg" href="<?php echo base_url()."records/detail/0" ?>">Next</a>
    <?php endif ?>
    </span></h2>
</div>

<div class="row">
  <div class="col-md-6 col-sm-12"> 
     <?php foreach($features as $k=>$v){
		 if(array_key_exists($v,$panels)){
		if($k%2==1){
     $this->view('records/panels/'.$panels[$v],$details);
		}
		}
 } ?>
</div>
  <div class="col-md-6 col-sm-12"> 
       <?php foreach($features as $k=>$v){
		   if(array_key_exists($v,$panels)){
		if($k%2==0){
     $this->view('records/panels/'.$panels[$v],$details);
		}
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
     <div class="panel panel-primary email-all-container">
        <?php $this->view('email/show_all_email.php'); ?>
	</div>
<?php } ?>
<!-- end email popup -->

<!-- start attachment popup -->
<?php if(in_array(13,$features)){ ?>
    <div class="panel panel-primary attachment-all-container">
        <?php $this->view('records/show_all_attachments.php'); ?>
    </div>
<?php } ?>
<!-- end attachment popup -->

<!-- start history popup -->
<?php if(in_array(13,$features)){ ?>
    <div class="panel panel-primary history-all-container">
        <?php $this->view('records/show_all_history.php'); ?>
    </div>
<?php } ?>
<!-- end history popup -->


<script type="text/javascript">
    $(document).ready(function () {
        var urn = '<?php echo $details['record']['urn'] ?>';
		var campaign = '<?php echo $details['record']['campaign_id'] ?>';
		var role_id = '<?php echo $_SESSION['role'] ?>';
		var permissions = $.parseJSON('<?php echo json_encode(array_flip($_SESSION['permissions'])) ?>');
        record.init(urn,role_id,campaign,permissions);
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
        <?php if(in_array(13,$features)){ ?>
        record.attachment_panel.init();
        <?php } ?>

    });
</script>
<?php endif; ?>
