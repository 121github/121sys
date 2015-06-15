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
            URN: <?php echo $details['record']['urn'] ?> <?php echo(!empty($details['record']['campaign']) ? " [" . $details['record']['campaign'] . "]" : "") ?></small> <?php echo(!empty($details['record']['logo']) ? '<img style="max-height:40px" src="' . base_url() . 'assets/logos/' . $details['record']['logo'] . '" />' : ""); ?>

        <button class="btn btn-info" id="map-icon" data-iconset="fontawesome" data-icon="<?php echo $map_icon ?>"
                role="iconpicker"></button>

      <span class="pull-right">
            <?php //show navigation if the user came from the list records page
            if (!empty($nav['prev']) && !$automatic && in_array("search records", $_SESSION['permissions'])): ?>
                <a type="button" class="btn btn-default btn-lg <?php if (!$allow_skip) {
                    echo "nav-btn";
                } ?>" href="<?php echo $nav['prev'] ?>">Previous</a>
            <?php endif ?>
          <?php if (!empty($nav['next']) && !$automatic && in_array("search records", $_SESSION['permissions'])): ?>
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

<div class="row">
    <div class="col-md-6 col-sm-12">
        <?php foreach ($features as $k => $v) {
            if (array_key_exists($v, $panels)) {
                if ($k % 2 == 1) {
                    $this->view('records/panels/' . $panels[$v], $details);
                }
            }
        } ?>
    </div>
    <div class="col-md-6 col-sm-12">
        <?php foreach ($features as $k => $v) {
            if (array_key_exists($v, $panels)) {
                if ($k % 2 == 0) {
                    $this->view('records/panels/' . $panels[$v], $details);
                }
            }
        } ?>
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
        record.init(urn, role_id, campaign, permissions);
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
        <?php if(in_array(14,$features)){ ?>
        //record.attachment_panel.init();
        <?php } ?>
        <?php if(in_array(15,$features)){ ?>
        record.related_panel.init();
        <?php } ?>
    });
</script>
<?php endif; ?>
