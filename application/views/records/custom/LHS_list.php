<?php if (!isset($details['record']['urn'])):
//if this is set to true it forces the first contact in the panel to be expanded when loaded on b2b campaigns
    $details['expand_contacts'] = true;
    ?>

    There was a problem while finding the selected record details. Maybe it does not exist or has been deleted.
<?php else: ?>
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
      
           <?php $this->view('records/panels/record_update.php', $details); ?>
           <?php $this->view('records/panels/contacts.php', $details); ?>
            <?php $this->view('records/panels/referral.php', $details); ?>
             <?php $this->view('records/panels/history.php', $details); ?>
               <?php //$this->view('records/panels/custom_info.php', $details); ?>
           <?php if(isset($custom_panels)){ 
		   foreach ($custom_panels as $k => $v) { 
                    $this->view('records/panels/' . "custom_panels.php", $custom_panels[$k]);
        }
		   }?>
             <?php $this->view('records/panels/emails.php', $details); ?>
              <?php $this->view('records/panels/attachments.php', $details); ?>
               <?php $this->view('records/panels/appointments.php', $details); ?>
                <?php $this->view('records/panels/quick_planner.php', $details); ?>
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

