
<form style="display: none; padding: 10px 20px;" class="form-horizontal">
	<input type="hidden" name="script_id">

	<div class="form-group input-group-sm">
		<div class="row">
			<p class="col-xs-1" for="inputPassword1">Name</p>
      		<div class="col-xs-9">
				<input type="text" class="form-control" name="script_name"
					title="Enter the script name" required />
			</div>
			<div class="col-xs-1">Expandable</div>
			<div class="col-xs-1">
				<input type="checkbox" id="expandable" name="expandable" title="Enter the expandable" />
			</div>
		</div>
	</div>
	<div class="form-group input-group-sm">
		<div class="row">
			<p class="col-xs-1" for="inputPassword1">Campaigns</p>
      		<div class="col-xs-9">
				<select  name="campaign_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
		           <?php foreach($campaigns as $row): ?>
		        	   	<option value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
		           <?php endforeach; ?>
				</select>
			</div>
			<div class="col-xs-2 form-inline">
				Sort <input type="text" class="form-control" style="width: 45px;" class="" name="sort" title="Enter the sort" required />
			</div>
		</div>
	</div>
	<div class="form-group input-group-sm">		
		<div class="row">
			<p class="col-xs-1" for="inputPassword1">Script</p>
      		<div class="col-xs-11">
				<textarea class="form-control" id="script" name="script"
					title="Enter the script" required ></textarea>
			</div>
		</div>
	</div>
	
	
	<!-- SUBMIT AND CANCEL BUTTONS -->
    <div class="form-actions pull-right">
		<button class="marl btn btn-default close-btn">Cancel</button>
		<button type="submit" class="marl btn btn-primary save-btn">Save</button>
	</div>
</form>