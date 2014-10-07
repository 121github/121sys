  <div class="panel panel-primary record-panel">
      <div class="panel-heading">Record Details
        <?php if($details['record']['favorite']){ ?>
        <span class="pull-right favorite-btn" action="remove"><span class="glyphicon glyphicon-star yellow"></span> Remove from favourites</span>
        <?php } else { ?>
        <span class="pull-right favorite-btn" action="add"><span class="glyphicon glyphicon-star-empty"></span> Add to favourites</span>
        <?php } ?>
      </div>
      <div class="panel-body">
        <p class="last-updated">Last Updated: <?php echo (!empty($details['record']['last_update'])?$details['record']['last_update']:"Never") ?></p>
        <form id="record-update-form">
          <input type="hidden" name="urn" id="urn" value="<?php echo $details['record']['urn'] ?>"/>
          <input type="hidden" name="campaign_id" id="campaign_id" value="<?php echo $details['record']['campaign_id'] ?>"/>
          <div class="form-group">
            <label>Next action date</label>
             <div class='input-group datetime'>
              <input name="nextcall" id="nextcall" data-date-format="DD/MM/YYYY HH:mm" placeholder="Set the next action date here" type='text' class="form-control" value="<?php echo (!empty($details['record']['nextcall'])?$details['record']['nextcall']:"") ?>"/>
              <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span> </span> </div>
          </div>
          <div class="form-group input-group">
            <?php if($_SESSION['role']==3){ 
			$deadtext = ". Only an administrator can unlock this record."; 
			?>
            <select <?php if($details['record']['record_status']=="3"||$details['record']['record_status']=="4"){ echo "disabled"; } ?> name="outcome_id" id="outcomes" class="selectpicker outcomepicker">
              <option value="">--Select a call outcome--</option>
              <?php foreach($outcomes as $outcome): ?>
              <option <?php if($details['record']['record_status']=="3"&&$outcome['outcome_id']==$details['record']['outcome_id']){ echo "selected"; } ?> value="<?php echo $outcome['outcome_id'] ?>" <?php echo ($outcome['delay_hours']?"delay='".$outcome['delay_hours']."'":"") ?>><?php echo $outcome['outcome'] ?></option>
              <?php endforeach; ?>
            </select>
            <?php } else { 
		   $deadtext = " Click the reset button below to bring it back for calling. Remember to change the ownership back if you need to";
		  ?>
            <select name="progress_id" id="progress" class="selectpicker outcomepicker">
              <option value="">No action required</option>
              <?php foreach($progress_options as $row): ?>
              <option value="<?php echo $row['id'] ?>" <?php if($details['record']['progress_id']==$row['id']){ echo "selected"; } ?> ><?php echo $row['name'] ?></option>
              <?php endforeach; ?>
            </select>
            <?php } ?>
          </div>
          <div class="form-group">
            <textarea name="comments" class="form-control <?php if($details['record']['record_status']=="3"){ echo "red"; } else if($details['record']['record_status']=="4"){ echo "green"; } ?>" rows="3" placeholder="Enter the call notes here"><?php if($details['record']['record_status']=="3"){ echo "This record has been removed with an outcome of ".$details['record']['outcome']. $deadtext; } else if($details['record']['record_status']=="4"&&$_SESSION['role']==3){ echo "This record has been completed with an outcome of ".$details['record']['outcome']. $deadtext; } ?>
</textarea>
          </div>
          <div class="form-group">
            <?php if($_SESSION['role']<3){ ?>
            <?php if($details['record']['urgent']){ ?>
            <span class="urgent-btn" action="remove"><span class="red glyphicon glyphicon-flag"></span> Unflag as urgent</span>
            <?php } else { ?>
            <span class="urgent-btn" action="add"><span class="glyphicon glyphicon-flag"></span> Flag as urgent</span>
            <?php } ?>
            <?php } else  { ?>
            <select class="selectpicker" name="pending_manager">
              <option value="">--Additional Options--</option>
              <option data-icon="glyphicon glyphicon-flag" value="1">Requires Attention</option>
              <option data-icon="red glyphicon-flag" value="2">Requires Urgent Attention</option>
            </select>
            <?php } ?>
            <?php if($details['record']['record_status']=="3"){ ?>
            <button type="button" class="btn btn-default pull-right reset-record" <?php if($_SESSION['role']==3){ echo "disabled"; } ?>>Reset Record</button>
            <?php } else { ?>
            <button type="button" class="btn btn-default pull-right update-record" <?php if($_SESSION['role']==3){ echo "disabled"; } ?>>Update Record</button>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>