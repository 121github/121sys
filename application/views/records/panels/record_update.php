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
        <input type="hidden" name="campaign_id" id="campaign_id" value="<?php echo $details['record']['campaign_id'] ?>"/>
          <input type="hidden" name="urn" id="urn" value="<?php echo $details['record']['urn'] ?>"/>
          <div class="form-group">
            <label>Next action date</label>
             <div class='input-group datetime'>
              <input name="nextcall" id="nextcall" data-date-format="DD/MM/YYYY HH:mm" placeholder="Set the next action date here" type='text' class="form-control" value="<?php echo (!empty($details['record']['nextcall'])?$details['record']['nextcall']:"") ?>"/>
              <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span> </span> </div>
          </div>
          <div class="form-group input-group">
            <?php 
			if(in_array("park records",$_SESSION['permissions'])){
			$parktext = "Click the unpark button below to allow dialing.";
			}
			if(in_array("reset records",$_SESSION['permissions'])&&$details['record']['record_status']=="3"){
			$deadtext = " Click the reset button below to bring it back for calling. Remember to change the ownership back if you need to.";	
			} else {
			$deadtext = " Only an administrator can bring it back for dialling.";		
			}
			if(in_array("set call outcomes",$_SESSION['permissions'])){  

			?>
            <select <?php if($details['record']['record_status']=="3"||$details['record']['record_status']=="4"){ echo "disabled"; } ?> name="outcome_id" id="outcomes" class="selectpicker outcomepicker">
              <option value="">--Select a call outcome--</option>
              <?php foreach($outcomes as $outcome): ?>
              <option <?php if($details['record']['record_status']=="3"||$details['record']['record_status']=="4"&&$outcome['outcome_id']==$details['record']['outcome_id']){ echo "selected"; } ?> value="<?php echo $outcome['outcome_id'] ?>" <?php echo ($outcome['delay_hours']?"delay='".$outcome['delay_hours']."'":"") ?>><?php echo $outcome['outcome'] ?></option>
              <?php endforeach; ?>
            </select>
            <?php } else {  ?>
            <select name="progress_id" id="progress" class="selectpicker outcomepicker">
              <option value="">No action required</option>
              <?php foreach($progress_options as $row): ?>
              <option value="<?php echo $row['id'] ?>" <?php if($details['record']['progress_id']==$row['id']){ echo "selected"; } ?> ><?php echo $row['name'] ?></option>
              <?php endforeach; ?>
            </select>
            <?php } ?>
          </div>
          <div class="form-group">
          <?php $text = ""; $color=""; 
		  if($details['record']['record_status']=="3"){ 
		  $color =  "red"; 
		  $text = "This record has been removed with an outcome of ".$details['record']['outcome']. $deadtext;
		  } 
		  else if($details['record']['record_status']=="4"){ 
		  $color = "green";
		  $text = "This record has been completed with an outcome of ".$details['record']['outcome']. $deadtext; 
		   }
		   if(!empty($details['record']['park_reason'])){
		  $color =  "red"; 
		  $text = "This record has been parked and cannot not be dialed. \r\nReason for parking: ".$details['record']['park_reason']."\r\n".$parktext;   
		   }
		   ?>
            <textarea name="comments" class="form-control <?php echo $color ?>" rows="3" placeholder="Enter the call notes here"><?php echo $text ?>
</textarea>
          </div>
          <div class="form-group">
            <?php if(!in_array("set call outcomes",$_SESSION['permissions'])){ ?>
            <?php if($details['record']['urgent']){ ?>
            <span class="urgent-btn" action="remove"><span class="red glyphicon glyphicon-flag"></span> Unflag as urgent</span>
            <?php } else { ?>
            <span class="urgent-btn" action="add"><span class="glyphicon glyphicon-flag"></span> Flag as urgent</span>
            <?php } ?>
            <?php } else  { ?>
            <select class="selectpicker" name="pending_manager">
              <option value="">--Additional Options--</option>
              <option data-icon="glyphicon glyphicon-flag" value="1">Requires Attention</option>
              <option <?php if($details['record']['urgent']){ ?> selected <?php } ?> data-icon="red glyphicon-flag" value="2">Requires Urgent Attention</option>
            </select>
            <?php } ?>
            <?php if(!empty($details['record']['park_reason'])&&in_array("park records",$_SESSION['permissions'])){ ?>
            <!-- need to add js to get this button working and set park code to null in record table -->
            <button type="button" class="btn btn-default pull-right unpark-record marl">Unpark Record</button>
			<?php } ?>
            <?php if($details['record']['record_status']=="3"&&in_array("reset records",$_SESSION['permissions'])){ ?>
            <button type="button" class="btn btn-default pull-right reset-record marl">Reset Record</button>
            <?php } ?>
            <?php if($details['record']['record_status']=="1"&&empty($details['record']['park_reason'])){ ?>
            <button type="button" class="btn btn-default pull-right update-record">Update Record</button>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>