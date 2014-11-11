<form style="display:none; padding:10px 20px;" class="form-horizontal" id="edit_time_form">
	<div class="row">
		<div class="col-xs-2">
			  <input type="hidden" name="time_id">
              <input type="hidden" name="user_id">
			  <div class="form-group input-duration-sm">
			    <p>Start Time</p>
                <div class='input-group time datetimepicker' style='width: 9em;'>
                    <input name='start_time' data-date-format='HH:mm' type='text' class='form-control' />
                    <span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>
                </div>
			  </div>
		</div>
		<div class="col-xs-2">
            <div class="form-group input-duration-sm">
                <p>End Time</p>
                <div class='input-group time datetimepicker' style='width: 9em;'>
                    <input name='end_time' data-date-format='HH:mm' type='text' class='form-control' />
                    <span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>
                </div>
            </div>
		</div>
		<div class="col-xs-3"></div>
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
	
	<h3>Exceptions</h3>
	<div class="form-group input-time-sm">
		<div class="row">
			<div class="col-xs-2">
			    <select name="exception_id" class="selectpicker" id="exception-select">
			      <option value=0>Add an exception</option>
			      <?php foreach($exception_types as $exception_type){ ?>
			      <option value="<?php echo $exception_type['id'] ?>"><?php echo $exception_type['name'] ?></option>
			      <?php } ?>
			    </select>
			</div>
			<div class="col-xs-1"></div>
			<div class="col-xs-1">
	    		<input type="integer" class="form-control" name="exception-duration" title="Enter the exception duration in minutes" required/>
	    	</div>
	    	<div class="col-xs-1">
	    		<button type="button" class="marl btn btn-success btn-xs add-exception-btn">Add</button>
	    	</div>
	    </div>
	</div>
	<div class="panel panel-primary exceptions-panel" style="border: 0px solid black;">
		<div class="panel-body time-panel">
			<div class="row">
				<div class="col-xs-5">
					<table class="table exceptions">
						<tbody class="exceptions-body"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</form>

<script>
    $(document).ready(function(){
        $('.datetimepicker').datetimepicker({
            pickDate: false
        });
    });
</script>