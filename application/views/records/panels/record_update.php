<?php if(isset($collapsable)){ ?> 
  <div id="record-panel" class="panel panel-primary">
      <div class="panel-heading clearfix" role="button" data-toggle="collapse"  data-target="#record-panel-slide" aria-expanded="true" aria-controls="record-panel-slide">Update Record
        </div>
      <div id="record-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
      
        <p id="last-updated">Last Updated: <?php echo (!empty($details['record']['last_update'])?$details['record']['last_update']:"Never") ?>
        </p>

        <form id="record-update-form">
        <input type="hidden" name="token" value="<?php echo time() ?>" />
        <input type="hidden" name="campaign_id" id="campaign_id" value="<?php echo $details['record']['campaign_id'] ?>"/>
          <input type="hidden" name="urn" id="urn" value="<?php echo $details['record']['urn'] ?>"/>
          <div class="form-group">
            <label>Next action date</label>
            <input type="hidden" name="original_nextcall" class="original-nextcall " value="<?php echo (!empty($details['record']['nextcall'])?$details['record']['nextcall']:"") ?>" />
             <div class='input-group '>
              <input name="nextcall" id="nextcall" data-date-format="DD/MM/YYYY HH:mm" placeholder="Set the next action date here" type='text' class="form-control datetime" value="<?php echo (!empty($details['record']['nextcall'])?$details['record']['nextcall']:"") ?>"/>
              <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span> </span> </div>
          </div>
       
            <?php $parktext = "";
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
               <div class="form-group input-group">
            <select <?php if($details['record']['record_status']=="3"||$details['record']['record_status']=="4"){ echo "disabled"; } ?> name="outcome_id" id="outcomes" data-size="12" class="selectpicker outcomepicker">
              <option value="">--Select a call outcome--</option>
              <?php foreach($outcomes as $outcome): ?>
              <option <?php if($outcome['disabled']=="1"){ ?> disabled <?php } ?> <?php if(($details['record']['record_status']=="3"||$details['record']['record_status']=="4")&&$outcome['outcome_id']==$details['record']['outcome_id']){ echo "selected"; } ?> value="<?php echo $outcome['outcome_id'] ?>" <?php echo ($outcome['delay_hours']?"delay='".$outcome['delay_hours']."'":"") ?>><?php echo $outcome['outcome'] ?></option>
              <?php endforeach; ?>
            </select>
               </div>
               <?php if(count($outcome_reasons)>0){ ?>
                   <div class="form-group input-group"> 
            <select disabled name="outcome_reason_id" data-size="12" id="outcome-reasons" class="selectpicker outcomereasonpicker">
              <option value="">--Select a reason--</option>
              <?php foreach($outcome_reasons as $reason): ?>
              <option  <?php if(($details['record']['record_status']=="3"||$details['record']['record_status']=="4")&&$reason['outcome_reason_id']==$details['record']['outcome_reason_id']){ echo "selected"; } ?> value="<?php echo $reason['outcome_reason_id'] ?>" outcome-id="<?php echo $reason['outcome_id']; ?>"><?php echo $reason['outcome_reason'] ?></option>
              <?php endforeach; ?>
               <option value="0">Other - please note</option>
               <option class="option-hidden" value="na">N/A</option>
            </select>
               </div>
               <?php } ?>
               
            <?php }
			
				if(in_array("set progress",$_SESSION['permissions'])) {  ?>
                   <div class="form-group input-group">
            <select name="progress_id" id="progress" class="selectpicker outcomepicker">
             
              <?php foreach($progress_options as $row): ?>
              <option value="<?php echo $row['id'] ?>" <?php if($details['record']['progress_id']==$row['id']){ echo "selected"; } ?> ><?php echo $row['name'] ?></option>
              <?php endforeach; ?>
            </select></div>
            <?php } ?>
        <?php  if(in_array("set call direction",$_SESSION['permissions'])) {  ?>
            <div class="form-group"><label>Inbound <input type="radio" id="cd-inbound" value="1" name="call_direction" /></label>
            <label>Outbound <input type="radio" id="cd-outbound" value="0" name="call_direction" /></label>
            
            </div>
           <?php } ?>
          <div class="form-group">
          <?php $text = ""; $color=""; 
		  if($details['record']['record_status']=="3"){ 
		  $color =  "red"; 
		  $text = "This record has been removed with an outcome of ".$details['record']['outcome'].". ". $deadtext;
		  } 
		  else if($details['record']['record_status']=="4"){ 
		  $color = "green";
		  $text = "This record has been completed with an outcome of ".$details['record']['outcome'].". ". $deadtext; 
		   }
		   if(!empty($details['record']['park_reason'])){
		  $color =  "red"; 
		  $text = "This record has been parked and cannot not be dialed. \r\nReason for parking: ".$details['record']['park_reason']."\r\n".$parktext;   
		   }
		   ?>

                        <textarea name="comments" class="form-control <?php echo $color ?> <?php if(isset($stretch)){ echo "stretch-element"; } ?>" rows="<?php echo isset($stretch)?1:3 ?>" placeholder="Enter the call notes here"><?php echo $text ?>
</textarea></span>
          </div>
          <div class="form-group">
            <?php if(in_array("urgent flag",$_SESSION['permissions'])){ ?>
            <?php if($details['record']['urgent']){ ?>
            <span id="urgent-btn" class="pointer marl" action="remove"><span class="red glyphicon glyphicon-flag"></span> Unflag as urgent</span>
            <?php } else { ?>
            <span id="urgent-btn" class="pointer marl" action="add"><span class="glyphicon glyphicon-flag"></span> Flag as urgent</span>
            <?php } ?>
            
                       <?php if($details['record']['favorite']){ ?>
        <span class="pull-left pointer" id="favorite-btn" action="remove"><span class="glyphicon glyphicon-star yellow"></span> Remove from favourites</span>
        <?php } else { ?>
        <span class="pull-left pointer" id="favorite-btn" action="add"><span class="glyphicon glyphicon-star-empty"></span> Add to favourites</span>
        <?php } ?>
            
            <?php } else  { 
			if(in_array("urgent dropdown",$_SESSION['permissions'])){ ?>
            <select class="selectpicker" name="pending_manager">
              <option value="">--Additional Options--</option>
              <option data-icon="glyphicon glyphicon-flag" value="1">Requires Attention</option>
              <option <?php if($details['record']['urgent']){ ?> selected <?php } ?> data-icon="red glyphicon-flag" value="2">Requires Urgent Attention</option>
            </select>
            <?php } else { ?>
            <input name="pending_manager" type="hidden" value=""> 
            <?php } ?>
            <?php } ?>
            <?php if(!empty($details['record']['park_reason'])&&in_array("park records",$_SESSION['permissions'])){ ?>
            <!-- need to add js to get this button working and set park code to null in record table -->
            <button type="button" class="btn btn-default pull-right marl" id="unpark-record">Unpark Record</button>
			<?php } ?>
            <?php if(($details['record']['record_status']=="3"||$details['record']['record_status']=="4")&&in_array("reset records",$_SESSION['permissions'])){ ?>
            <button type="button" class="btn btn-default pull-right marl" id="reset-record">Reset Record</button>
            <?php } ?>
            <?php if($details['record']['record_status']=="2"||$details['record']['record_status']=="1"&&empty($details['record']['park_reason'])){ ?>
            <button type="button" class="btn btn-default pull-right" id="update-record" disabled>Update Record</button>
            <?php } ?>
                    
          </div>

        </form>
      </div>
    </div>
    </div>
    <?php } else { ?>
    
      <div id="record-panel" class="panel panel-primary">
      <div class="panel-heading clearfix">Record Details
        <?php if($details['record']['favorite']){ ?>
        <span class="pull-right pointer" id="favorite-btn" action="remove"><span class="glyphicon glyphicon-star yellow"></span> Remove from favourites</span>
        <?php } else { ?>
        <span class="pull-right pointer" id="favorite-btn" action="add"><span class="glyphicon glyphicon-star-empty"></span> Add to favourites</span>
        <?php } ?>
      </div>
      <div class="panel-body">
        <p id="last-updated">Last Updated: <?php echo (!empty($details['record']['last_update'])?$details['record']['last_update']:"Never") ?></p>
        <form id="record-update-form">
        <input type="hidden" name="token" value="<?php echo time() ?>" />
        <input type="hidden" name="campaign_id" id="campaign_id" value="<?php echo $details['record']['campaign_id'] ?>"/>
          <input type="hidden" name="urn" id="urn" value="<?php echo $details['record']['urn'] ?>"/>
          <div class="form-group">
            <label>Next action date</label>
            <input type="hidden" name="original_nextcall" class="original-nextcall " value="<?php echo (!empty($details['record']['nextcall'])?$details['record']['nextcall']:"") ?>" />
             <div class='input-group '>
              <input name="nextcall" id="nextcall" data-date-format="DD/MM/YYYY HH:mm" placeholder="Set the next action date here" type='text' class="form-control datetime" value="<?php echo (!empty($details['record']['nextcall'])?$details['record']['nextcall']:"") ?>"/>
              <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span> </span> </div>
          </div>
       
            <?php $parktext = "";
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
               <div class="form-group input-group">
            <select <?php if($details['record']['record_status']=="3"||$details['record']['record_status']=="4"){ echo "disabled"; } ?> name="outcome_id" id="outcomes" data-size="12" class="selectpicker outcomepicker">
              <option value="">--Select a call outcome--</option>
              <?php foreach($outcomes as $outcome): ?>
              <option <?php if($outcome['disabled']=="1"){ ?> disabled <?php } ?> <?php if(($details['record']['record_status']=="3"||$details['record']['record_status']=="4")&&$outcome['outcome_id']==$details['record']['outcome_id']){ echo "selected"; } ?> value="<?php echo $outcome['outcome_id'] ?>" <?php echo ($outcome['delay_hours']?"delay='".$outcome['delay_hours']."'":"") ?>><?php echo $outcome['outcome'] ?></option>
              <?php endforeach; ?>
            </select>
               </div>
               <?php if(count($outcome_reasons)>0){ ?>
                   <div class="form-group input-group"> 
            <select disabled name="outcome_reason_id" data-size="12" id="outcome-reasons" class="selectpicker outcomereasonpicker">
              <option value="">--Select a reason--</option>
              <?php foreach($outcome_reasons as $reason): ?>
              <option  <?php if(($details['record']['record_status']=="3"||$details['record']['record_status']=="4")&&$reason['outcome_reason_id']==$details['record']['outcome_reason_id']){ echo "selected"; } ?> value="<?php echo $reason['outcome_reason_id'] ?>" outcome-id="<?php echo $reason['outcome_id']; ?>"><?php echo $reason['outcome_reason'] ?></option>
              <?php endforeach; ?>
               <option value="0">Other - please note</option>
               <option class="option-hidden" value="na">N/A</option>
            </select>
               </div>
               <?php } ?>
               
            <?php }
			
				if(in_array("set progress",$_SESSION['permissions'])) {  ?>
                   <div class="form-group input-group">
            <select name="progress_id" id="progress" class="selectpicker outcomepicker progress-outcome">
              <option value="">No action required</option>
              <?php foreach($progress_options as $row): ?>
              <option value="<?php echo $row['id'] ?>" <?php if($details['record']['progress_id']==$row['id']){ echo "selected"; } ?> ><?php echo $row['name'] ?></option>
              <?php endforeach; ?>
            </select></div>
            <?php } ?>
        <?php  if(in_array("set call direction",$_SESSION['permissions'])) {  ?>
            <div class="form-group"><label>Inbound <input type="radio" id="cd-inbound" value="1" name="call_direction" /></label>
            <label>Outbound <input type="radio" id="cd-outbound" value="0" name="call_direction" /></label>
            
            </div>
           <?php } ?>
          <div class="form-group">
          <?php $text = ""; $color=""; 
		  if($details['record']['record_status']=="3"){ 
		  $color =  "red"; 
		  $text = "This record has been removed with an outcome of ".$details['record']['outcome'].". ". $deadtext;
		  } 
		  else if($details['record']['record_status']=="4"){ 
		  $color = "green";
		  $text = "This record has been completed with an outcome of ".$details['record']['outcome'].". ". $deadtext; 
		   }
		   if(!empty($details['record']['park_reason'])){
		  $color =  "red"; 
		  $text = "This record has been parked and cannot not be dialed. \r\nReason for parking: ".$details['record']['park_reason']."\r\n".$parktext;   
		   }
		   ?>

                        <textarea name="comments" class="form-control <?php echo $color ?> <?php if(isset($stretch)){ echo "stretch-element"; } ?>" rows="<?php echo isset($stretch)?1:3 ?>" placeholder="Enter the call notes here"><?php echo $text ?>
</textarea></span>
          </div>
          <div class="form-group">
            <?php if(in_array("urgent flag",$_SESSION['permissions'])){ ?>
            <?php if($details['record']['urgent']){ ?>
            <span id="urgent-btn" class="pointer" action="remove"><span class="red glyphicon glyphicon-flag"></span> Unflag as urgent</span>
            <?php } else { ?>
            <span id="urgent-btn" class="pointer" action="add"><span class="glyphicon glyphicon-flag"></span> Flag as urgent</span>
            <?php } ?>
            <?php } else  { 
			if(in_array("urgent dropdown",$_SESSION['permissions'])){ ?>
            <select class="selectpicker" name="pending_manager">
              <option value="">--Additional Options--</option>
              <option data-icon="glyphicon glyphicon-flag" value="1">Requires Attention</option>
              <option <?php if($details['record']['urgent']){ ?> selected <?php } ?> data-icon="red glyphicon-flag" value="2">Requires Urgent Attention</option>
            </select>
            <?php } else { ?>
            <input name="pending_manager" type="hidden" value=""> 
            <?php } ?>
            <?php } ?>
            <?php if(!empty($details['record']['park_reason'])&&in_array("park records",$_SESSION['permissions'])){ ?>
            <!-- need to add js to get this button working and set park code to null in record table -->
            <button type="button" class="btn btn-default pull-right marl" id="unpark-record">Unpark Record</button>
			<?php } ?>
            <?php if(($details['record']['record_status']=="3"||$details['record']['record_status']=="4")&&in_array("reset records",$_SESSION['permissions'])){ ?>
            <button type="button" class="btn btn-default pull-right marl" id="reset-record">Reset Record</button>
            <?php } ?>
            <?php if($details['record']['record_status']=="2"||$details['record']['record_status']=="1"&&empty($details['record']['park_reason'])){ ?>
            <button type="button" class="btn btn-default pull-right" id="update-record" disabled>Update Record</button>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>
      <?php } ?>
  <script>
      $(document).ready(function () {
          if(typeof campaign_functions !== "undefined"){
              if(typeof campaign_functions.record_setup_update !== "undefined"){
                  campaign_functions.record_setup_update();
              }
          }
      });
  </script>

