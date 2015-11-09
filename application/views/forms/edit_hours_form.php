<form style="display:none; padding:10px 20px;" class="form-horizontal" id="edit_hours_form">
	<div class="row">
		<div class="col-xs-2">
			  <input type="hidden" name="hours_id">
              <input type="hidden" name="user_id">
			  <div class="form-group input-duration-sm">
			    <p>Duration (minutes)</p>
			    <input type="integer" class="form-control" name="duration" title="Enter the duration in minutes" required/>
			  </div>
		</div>
		<div class="col-xs-4"></div>
		<div class="col-xs-3">
            <p>Campaign</p>
			<div class="form-group input-campaign-sm" style="display: none;">
			    <select name="campaign_id" class="selectpicker" id="group-select">
			      <?php foreach($campaigns as $campaign){ ?>
			      <option value="<?php echo $campaign['id'] ?>"><?php echo $campaign['name'] ?></option>
			      <?php } ?>
			    </select>
			</div>
            <div id="campaign_name"></div>
		</div>
		<div class="col-xs-1"></div>
		<div class="col-xs-2">
            <p>Date</p>
			<div class="form-group input-date-sm" style="display: none;">
			    <div class="input-group">
			    	<input name="date" type="text" readonly="readonly" class="form-control date2" style="" placeholder="Date">
			 	</div>
            </div>
            <div id="date"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="form-group input-comment-sm">
			    <p>Comment</p>
			    <textarea name="comment" class="form-control"></textarea>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="form-actions pull-right">
   		    <button type="submit" class="marl btn btn-primary save-btn">Save</button>
   		    <button class="marl btn btn-default close-btn">Cancel</button>
	  	</div>
	</div>
</form>