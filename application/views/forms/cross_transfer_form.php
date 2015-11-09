  <div class="panel-heading">Cross transfer <span class="glyphicon glyphicon-remove pull-right close-xfer"></span></div>
  <div class="panel-body">
	<div class="edit-panel">
      <form class="form-horizontal campaign-select-form">
        <div class="form-group input-group-sm">
          <p>Please choose the campaign this transfer should be applied to</p>
          <select class="selectpicker" name="campaign">
          <option value="">Choose a campaign</option>
            <?php foreach($xfer_campaigns as $row): ?>
            <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
          </select>
        </div> 
        <div class="form-actions pull-right">
         
         <button class="marl btn btn-default close-xfer">Cancel</button>
         <button type="submit" class="marl btn btn-primary set-xfer">Continue</button>
        </div>
      </form>
  </div>
</div>