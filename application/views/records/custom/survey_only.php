<?php if (!isset($details['record']['urn'])):
//if this is set to true it forces the first contact in the panel to be expanded when loaded on b2b campaigns
    $details['expand_contacts'] = true;
    ?>

    There was a problem while finding the selected record details. Maybe it does not exist or has been deleted.
<?php else: ?>
<div class="row">
  <div class="col-md-6 col-sm-12">    
   <?php $this->view('records/panels/record_update.php',$details); ?>
</div>

   <div class="col-md-6 col-sm-12"> 
  <?php $this->view('records/panels/contacts.php',$details); ?>
  <?php $this->view('records/panels/survey.php',$details); ?>
  <?php $this->view('records/panels/history.php',$details); ?>
  </div>
     </div>

</div>
</div>

<!-- end fluid container --> 
<!-- start survey popup -->
<?php if(in_array(11,$features)){ ?>
<div class="panel panel-primary survey-container">
  <?php 
$this->view('forms/new_survey_form.php',$survey_options); ?>
</div>
<?php } ?>



<script type="text/javascript">
    $(document).ready(function () {
        var urn = '<?php echo $details['record']['urn'] ?>';
		var campaign = '<?php echo $details['record']['campaign_id'] ?>';
		var role_id = '<?php echo $_SESSION['role'] ?>';
		var permissions = $.parseJSON('<?php echo json_encode(array_flip($_SESSION['permissions'])) ?>');
        record.init(urn,role_id,campaign,permissions);
		//initializing the generic panels
		record.contact_panel.init();
		
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
		record.script_panel.init();
		<?php } ?>
		<?php if(in_array(7,$features)){ ?>
        record.history_panel.init();
		<?php } ?>
		 <?php if(in_array(8,$features)){ ?>
		<?php $formats = custom_formats(); ?>
        record.additional_info.init('<?php echo $formats[$details['record']['custom_format']] ?>');
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
