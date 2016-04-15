<?php if (!isset($details['record']['urn'])):
//if this is set to true it forces the first contact in the panel to be expanded when loaded on b2b campaigns
    $details['expand_contacts'] = true;
    ?>

    There was a problem while finding the selected record details. Maybe it does not exist or has been deleted.
<?php else: ?>
<div class="page-header">


    <h2>
        View Details
        <small>
            URN: <?php echo $details['record']['urn'] ?> <?php if(!empty($details['record']['client_ref'])){ ?><span id="client-ref">Ref: <?php echo $details['record']['client_ref'] ?></span><?php } ?> <span class='text-primary'><?php echo(!empty($details['record']['campaign']) ? " [" . $details['record']['campaign'] . "]" : "") ?> <?php echo(!empty($details['record']['pot_name']) ? " [" . $details['record']['pot_name'] . "]" : "") ?></span></small> <?php echo(!empty($details['record']['logo']) ? '<img style="max-height:40px" src="' . base_url() . 'assets/logos/' . $details['record']['logo'] . '" />' : ""); ?>

<?php if(in_array("record options",$_SESSION['permissions'])){  ?>
<div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
   <span class="glyphicon glyphicon-cog"></span> Options <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" id="record-options">
  <?php if(in_array("planner",$_SESSION['permissions'])){ ?>
       <li><a href="#" data-tab="tab-planner" data-modal="view-record" data-urn="<?php echo $details['record']['urn'] ?>">Add to planner</a></li>
         <li role="separator" class="divider"></li>
    <?php } ?>   
   <?php if(in_array("change color",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="color">Change Color</a></li>
      <?php } ?>
     <?php if(in_array("change icon",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="icon">Change Icon</a></li>
      <?php } ?>
     <?php if(in_array("change source",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="source">Change Source</a></li>
      <?php } ?>
     <?php if(in_array("change pot",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="pot">Change Pot</a></li>
      <?php } ?>
     <?php if(in_array("change campaign",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="campaign">Change Camapign</a></li>
          <li role="separator" class="divider"></li>
      <?php } ?>
    <?php if(in_array("park records",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="other">Remove Record</a></li>
    <?php } ?>
  </ul>
</div>
<?php } ?>


      <span class="pull-right">
            <?php //show navigation if the user came from the list records page
            if (!empty($nav['prev']) && !$automatic): ?>
                <a type="button" class="btn btn-default btn-lg <?php if (!$allow_skip) {
                    echo "nav-btn";
                } ?>" href="<?php echo $nav['prev'] ?>">Previous</a>
            <?php endif ?>
          <?php if (!empty($nav['next']) && !$automatic): ?>
              <a type="button" class="btn btn-default btn-lg <?php if (!$allow_skip) {
                  echo "nav-btn";
              } ?>" href="<?php echo $nav['next'] ?>">Next</a>
          <?php endif ?>

          <?php //this is the agent navigation which brings single records in they can only go +/-1 record at a time and they must update the record before they can move on
          if (isset($_SESSION['current_campaign'])):
              if ($automatic || empty($nav['next']) && in_array("use callpot", $_SESSION['permissions'])): ?>
                  <?php if (isset($_SESSION['prev']) && !empty($_SESSION['prev']) && $_SESSION['prev'] != $details['record']['urn']): ?>
                      <a type="button" class="btn btn-default btn-lg"
                         href="<?php echo base_url() . "records/detail/" . $_SESSION['prev'] ?>">Previous</a>
                  <?php endif ?>
            <a type="button"
               class="btn btn-default btn-lg <?php if (!isset($_SESSION['next']) && !$allow_skip || empty($_SESSION['next']) && !$allow_skip) {
                   echo "nav-btn";
               } ?>"
               href="<?php echo base_url() . "records/detail/" . (isset($_SESSION['next']) ? $_SESSION['next'] : "0") ?>">Next</a>
              <?php endif;
          endif; ?>
        </span>
    </h2>
</div>
<?php $details['collapsable'] = false; 
?>
<style>
[data-toggle="collapse"] { cursor:pointer }
.panel:not(:last-of-type){  border-bottom:none; }
.panel:not(:first-of-type) { border-radius:0 !important; }
.panel { margin-top:0 !important; margin-bottom:0 !important; border-bottom-right-radius:0 !important;  border-bottom-left-radius:0 !important; }
.panel-heading { border-top-left-radius:0 !important;border-top-right-radius:0 !important }
.panel-group .panel
{
  overflow: visible !important;
}
.list-group { margin-bottom:0 !important }
.panel-primary > .panel-heading + .panel-collapse .panel-body {
  border-top:none !important;
}
</style>
<div class="row">
    <div class="col-sm-12">
        
 <div class="panel-group" id="detail-accordion" role="tablist" aria-multiselectable="true">
        <?php  foreach ($features as $k => $v) {
            if (array_key_exists($v, $panels)) {
                    $this->view('records/panels/' . $panels[$v], $details);
            }
        } ?>
           <?php if(isset($custom_panels)){ 
		   foreach ($custom_panels as $k => $v) { 
                    $this->view('records/panels/' . "custom_panels.php", $custom_panels[$k]);
        }
		   }?>
           </div>
    </div>
   
</div>
<!-- end row panel -->
</div>
<!-- end fluid container -->

<?php if (in_array(1, $features)) { ?>
    <div class="panel panel-primary xfer-container">
        <?php
        $this->view('forms/cross_transfer_form.php', $xfer_campaigns); ?>
    </div>
<?php } ?>
<!-- end survey popup -->



<!-- start attachment popup -->
<?php if (in_array(13, $features)) { ?>
    <div class="panel panel-primary attachment-all-container">
        <?php $this->view('records/show_all_attachments.php'); ?>
    </div>
<?php } ?>
<!-- end attachment popup -->




<script type="text/javascript">
    $(document).ready(function () {
        var urn = '<?php echo $details['record']['urn'] ?>';
        var campaign = '<?php echo $details['record']['campaign_id'] ?>';
        var role_id = '<?php echo $_SESSION['role'] ?>';
        var permissions = $.parseJSON('<?php echo json_encode(array_flip($_SESSION['permissions'])) ?>');
        record.init(urn,campaign);
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
        <?php if(in_array(14,$features)){ ?>
        //record.attachment_panel.init();
        <?php } ?>
        <?php if(in_array(15,$features)){ ?>
        record.related_panel.init();
        <?php } ?>
		<?php if(in_array(16,$features)){ ?>
        record.sms_panel.init();
        <?php } ?>
		<?php if(in_array(17,$features)){ ?>
        record.appointment_slots_panel.init();
        <?php } ?>
		<?php if(in_array(18,$features)){ ?>
        //hsl branches
        <?php } ?>
		<?php if(in_array(19,$features)){ ?>
        record.order_panel.init(); 
        <?php } ?>
		<?php if(in_array(21,$features)){ ?>
        record.tasks.init();
        <?php } ?>
		<?php if(in_array(22,$features)){ ?>
		record.referral_panel.init();
		<?php } ?>
		 <?php if(isset($custom_panels)){ ?>
		 custom_panels.init();
		 <?php } ?>
    });
</script>
<?php endif; ?>
