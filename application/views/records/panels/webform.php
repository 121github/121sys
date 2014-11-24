    <div class="panel panel-primary webforms-panel">
      <div class="panel-heading">Webforms</div>
      <div class="panel-body">
        <div class="panel-content"> 
          <?php foreach($webforms as $webform){ ?>
          <a href="<?php echo base_url()."webforms/edit/".$webform['campaign_id']."/$urn/".$webform['webform_id']; ?>"><?php echo $webform['webform_name'] ?></a>
          <?php } ?>
        </div>
      </div>
    </div>