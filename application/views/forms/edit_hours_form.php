<form style="display:none; padding:10px 20px;" class="form-horizontal" id="edit_hours_form">
	<div class="row">
		<div class="col-xs-2">
			  <input type="hidden" name="hours_id">
			  <div class="form-group input-duration-sm">
			    <p>Duration (minutes)</p>
			    <input type="integer" class="form-control" name="duration" title="Enter the duration in minutes" required/>
			  </div>
		</div>
		<div class="col-xs-4"></div>
		<div class="col-xs-3">
			<div class="form-group input-campaign-sm">
			    <p>Please select the Campaign</p>
			    <select disabled name="campaign_id" class="selectpicker" id="group-select">
			      <?php foreach($campaigns as $campaign){ ?>
			      <option value="<?php echo $campaign['id'] ?>"><?php echo $campaign['name'] ?></option>
			      <?php } ?>
			    </select>
			</div>
		</div>
		<div class="col-xs-1"></div>
		<div class="col-xs-2">
			<div class="form-group input-date-sm">
			    <p>Please set the day</p>
				<div class="input-group">
			    	<input  disabled name="date" type="text" readonly="readonly" class="form-control date2" style="" placeholder="Date">
			 	</div>
			  </div>
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
	<h3>Exceptions</h3>
	<div class="form-group input-hour-sm">
	    <select name="campaign_id" class="selectpicker" id="group-select">
	      <option value="Select an exception"></option>
	      <?php foreach($exception_types as $exception_type){ ?>
	      <option value="<?php echo $exception_type['id'] ?>"><?php echo $exception_type['name']." - "; echo ($exception_type['paid'])?"paid":"unpaid" ?></option>
	      <?php } ?>
	    </select>
	    <button type="button" class="marl btn btn-success btn-xs add-btn">Add</button>
	</div>
	<div class="panel panel-primary exceptions-panel" style="border: 0px solid black;">
		<div class="panel-body hours-panel">
			<div class="row">
				<div class="col-xs-3">
					<table class="exceptions">
						<tbody class="exceptions-body"></tbody>
					</table>
				</div>
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