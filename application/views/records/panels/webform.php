    <div class="panel panel-primary webforms-panel">
      <div class="panel-heading clearfix">Webforms</div>
      <div class="panel-body">
        <div class="panel-content"> 
        <?php if(count($webforms)>0){ ?>
          <?php $x=0; foreach($webforms as $webform){ $x++; ?>
          <?php if($x>1){ echo "<br />"; } ?><a href="<?php echo base_url()."webforms/edit/".$webform['campaign_id']."/".$details['record']['urn']."/".$webform['webform_id']; ?>"><?php echo $webform['webform_name'] ?></a> <?php if(!empty($webform['completed_on'])){ ?><small class="text-success"><span class="fa fa-check-circle"></span>  Completed: <?php echo $webform['completed_on']; ?> by <?php echo $webform['name']; ?> </small><?php } else { ?><small class="text-danger"><span class="fa fa-exclamation-circle"></span> Incomplete</small><?php } ?>
          <?php } ?>  <?php } else { ?> 
          <p>No Webforms have been created for this campaign</p>
          <?php } ?>
        </div>
      </div>
    </div>