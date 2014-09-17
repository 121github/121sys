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
  <div class="col-md-12 col-sm-12"> 
     <?php if(in_array(10,$features)){ ?>
    <!-- start company panel -->
    <?php $this->view('records/panels/company.php',$details) ?>
    <!--end company panel--> 
     <?php } ?>
  </div>
</div>
<!--end row-->

<div class="row">
  <div class="col-md-12 col-sm-12"> 
    <!-- start contacts panel -->
    <?php $this->view('records/panels/contacts.php',$details) ?>
    <!--end contact panel--> 
  </div>
</div>
<!--end row-->

<div class="row">
  <div class="col-md-12 col-sm-12"> 
    <!--script panel-->
    <?php if(in_array(6,$features)){ ?>
   <?php $this->view('records/panels/scripts.php',$details) ?>
    <?php } ?>
    <!--end script panel--> 
  </div>
</div>
<!--end of row-->
<div class="row">
  <div class="col-md-12 col-sm-12"> 
    <!--record panel-->
  <?php $this->view('records/panels/record_update.php',$details) ?>
    <!--end record panel--> 
  </div>
</div>
<!--end row-->
<div class="row">
  <div class="col-md-12 col-sm-12"> 
    <!--additional info panel-->
    <?php if(in_array(5,$features)){ ?>
     <?php $this->view('records/panels/custom_info.php',$details) ?>
    <?php } ?>
    <!--end additional info panel--> 
    
<div class="row">
  <div class="col-md-12 col-sm-12"> 
    <!--ownership panel-->
    <?php if(in_array(5,$features)){ ?>
     <?php $this->view('records/panels/ownership.php',$details) ?>
    <?php } ?>
    <!--end ownership panel--> 
    
  </div>
</div>
<!--end ownership row-->
<div class="row">
  <div class="col-md-12 col-sm-12"> 
    <!--start stick panel-->
    <?php if(in_array(4,$features)){ ?>
      <?php $this->view('records/panels/sticky.php',$details) ?>
    <?php } ?>
    <!--end sticky panel--> 
  </div>
</div>
<!--end sticky row-->
<div class="row">
  <div class="col-md-12 col-sm-12"> 
    <!-- start history panel -->
    <?php if(in_array(3,$features)){ ?>
  <?php $this->view('records/panels/history.php',$details) ?>
    <?php } ?>
    <!-- end history panel --> 
  </div>
</div>
<!-- end history row -->

<div class="row">
  <div class="col-md-12 col-sm-12"> 
    <!--start survey panel -->
    <?php if(in_array(1,$features)){ ?>
  <?php $this->view('records/panels/survey.php',$details) ?>
    <?php } ?>
    <!-- end survey panel --> 
  </div>
</div>
<!-- end survey row -->
<div class="row">
  <div class="col-md-12 col-sm-12"> 
    <!-- start recordings panel -->
    <?php if(in_array(7,$features)){ ?>
  <?php $this->view('records/panels/recordings.php',$details) ?>
    <?php } ?>
    <!-- end recordings panel --> 
  </div>
</div>
<!-- end row panel -->
</div>
<!-- end fluid container --> 
<!-- start survey popup -->
<?php if(in_array(1,$features)){ ?>
<div class="panel panel-primary survey-container">
  <?php 
$this->view('forms/new_survey_form.php',$survey_options); ?>
</div>
<?php } ?>
<!-- end survey popup --> 
<script type="text/javascript">
    $(document).ready(function () {   
        var urn = '<?php echo $details['record']['urn'] ?>';
		var role_id = '<?php echo $_SESSION['role'] ?>';
        record.init(urn,role_id);
		//initializing the generic panels
		record.contact_panel.init(urn);
		record.update_panel.init();
		//initializing the panels for this campaign
				<?php if(in_array(10,$features)){ ?>
        record.company_panel.init();
		<?php } ?>
		<?php if(in_array(3,$features)){ ?>
        record.history_panel.init();
		<?php } ?>
		<?php if(in_array(1,$features)){ ?>
        record.surveys_panel.init(urn);
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
		record.recordings_panel.init();
		<?php } ?>
		<?php if(in_array(8,$features)){ ?>
		record.additional_info.init();
		<?php } ?>
    });
</script>
<?php endif; ?>
