<?php if(!isset($details['record']['urn'])): ?>

There was a problem while finding the selected record details. Maybe it does not exist or has been deleted.
<?php else: ?>
<div class="page-header">


  <h2>View Details <small>URN: <?php echo $details['record']['urn'] ?> <?php echo (!empty($details['record']['campaign'])?" [". $details['record']['campaign']."]":"") ?></small> <?php echo (!empty($details['record']['logo'])?'<img style="max-height:40px" src="'.base_url().'assets/logos/'.$details['record']['logo'].'" />':""); ?> <span class="pull-right">

    <?php //show navigation if the user came from the list records page
	if(!empty($nav['prev'])&&!$automatic&&in_array("search records",$_SESSION['permissions'])): ?>
    <a type="button" class="btn btn-default btn-lg <?php if(!$allow_skip){ echo "nav-btn"; } ?>" href="<?php echo $nav['prev'] ?>">Previous</a>
    <?php endif ?>
    <?php if(!empty($nav['next'])&&!$automatic&&in_array("search records",$_SESSION['permissions'])): ?>
    <a type="button" class="btn btn-default btn-lg <?php if(!$allow_skip){ echo "nav-btn"; } ?>" href="<?php echo $nav['next'] ?>">Next</a>
    <?php endif ?>
    
    <?php //this is the agent navigation which brings single records in they can only go +/-1 record at a time and they must update the record before they can move on
	if($automatic||empty($nav['next'])&&in_array("set call outcomes",$_SESSION['permissions'])): ?>
    <?php if(isset($_SESSION['prev'])&&!empty($_SESSION['prev'])&&$_SESSION['prev']!=$details['record']['urn']): ?>
    <a type="button" class="btn btn-default btn-lg" href="<?php echo base_url()."records/detail/".$_SESSION['prev'] ?>">Previous</a>
    <?php endif ?>  
    <a type="button" class="btn btn-default btn-lg <?php if(!isset($_SESSION['next'])&&!$allow_skip||empty($_SESSION['next'])&&!$allow_skip){ echo "nav-btn"; } ?>" href="<?php echo base_url()."records/detail/".(isset($_SESSION['next'])?$_SESSION['next']:"0") ?>">Next</a>
    <?php endif ?>
    </span></h2>
</div>
<div class="row">
  <div class="col-md-4 col-sm-12"> 

   <?php $details['stretch'] = true; $this->view('records/panels/record_update.php',$details); ?>

   
  
</div>
  <div class="col-md-4 col-sm-12"> 
     <span class="stretch-panel">
  <?php $this->view('records/panels/contacts.php',$details); ?>
     </span>
  <?php $this->view('records/panels/scripts.php',$details); ?>

</div>
  <div class="col-md-4 col-sm-12">

</div>
</div>
<div class="row">
  <div class="col-md-12 col-sm-12"> 
  <?php $this->view('records/panels/history.php',$details); ?>
  </div>
</div>
<?php if(in_array(12,$features)){ ?>
<div class="row">
  <div class="col-md-12 col-sm-12"> 
  <?php $this->view('records/panels/recordings.php',$details); ?>
</div>
</div>
<?php } ?>

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
<?php if(in_array('view history',$_SESSION['permissions'])){ ?>
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
