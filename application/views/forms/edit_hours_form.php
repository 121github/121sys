<div class="row">
	<form style="display:none; padding:10px 20px;" class="form-horizontal" id="edit_hours_form">
		<div class="col-xs-2">
			  <input type="hidden" name="hours_id">
			  <div class="form-group input-hour-sm">
			    <p>Duration (minutes)</p>
			    <input type="integer"  disabled class="form-control" name="duration" title="Enter the duration in minutes" required/>
			  </div>
		</div>
		<div class="col-xs-1"></div>
		<div class="col-xs-3">
			  <input type="hidden" name="hours_id">
			  <div class="form-group input-hour-sm">
			    <p>Please set the exception in minutes</p>
			    <input type="integer" class="form-control" name="exception" title="Enter the exception in minutes" required/>
			  </div>
		</div>
		<div class="col-xs-1"></div>
		<div class="col-xs-5">
			<div class="form-group input-date-sm">
			    <p>Please set the day</p>
				<div class="input-group">
			    	<input  name="date" type="text" readonly="readonly" class="form-control date2" style="" placeholder="Date">
			        <span class="input-group-btn">
			        	<button class="btn btn-default clear-input" type="button">Clear</button>
			        </span>
			 	</div>
			  </div>
		</div>
		<div class="col-xs-12">
			<div class="form-group input-campaign-sm">
			    <p>Please select the Campaign</p>
			    <select name="campaign_id" class="selectpicker" id="group-select">
			      <?php foreach($campaigns as $campaign){ ?>
			      <option value="<?php echo $campaign['id'] ?>"><?php echo $campaign['name'] ?></option>
			      <?php } ?>
			    </select>
			</div>
		</div>
		<div class="form-actions pull-right">
   		    <button type="submit" class="marl btn btn-primary save-btn">Save</button>
   		    <button class="marl btn btn-default close-btn">Cancel</button>
	  	</div>
	</form>
</div>