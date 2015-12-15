  <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Availability <a class="pull-right btn btn-default" href="slots">Edit Slots</a></h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      <form id="slot-form">
        <div class="row">
        <div class="col-lg-12">
       <div class="panel panel-primary" id="slot-options-panel">
            <div class="panel-heading">User booking slot configuration</div>
             <div class="panel-body">
             	
                <div class="form-group">
                <label>Search users by campaign</label><br>
                <select id="slot-campaign-select" class="selectpicker" data-width="230px">
                <option value="">Select campaign</option>
            <?php foreach ($options['campaigns'] as $group => $camp_array) { ?>
                <optgroup label="<?php echo $group ?>">
                    <?php foreach ($camp_array as $camp) { ?>
                        <option value="<?php echo $camp['campaign_id'] ?>"><?php echo $camp['campaign_name'] ?></option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
                </select>
                </div>
                
                  <div class="form-group">
                <label>Select the user</label><br>
                <select name="user_id" id="slot-user-select" disabled class="selectpicker"><option value="">Select user</option>
                </select>
                </div>
                
                 <div class="form-group">
                <label>Select the booking slots used by this attendee</label><br>
                <select id="slot-select" disabled class="selectpicker"><option value="">Select slot group</option>
                <?php foreach($options['slots'] as $row){ ?>
					<option value="<?php echo $row['slot_group_id'] ?>"><?php echo $row['slot_group_name'] ?></option>
					<?php } ?>
                </select>
                </div>          
     
      </div>
      </div>
      </div>
      </div>          
                 <div class="row">
        <div class="col-lg-12">
       <div class="panel panel-primary" id="slot-day-panel">
            <div class="panel-heading">Default slot configuration - set the maximum allowed appointments per day/timeslot <span class="slot-rule-user"></span></div>
             <div class="panel-body">
             <p>Select the user you want to configure using the options above</p>
            </div>
      </div>
      
      </div>
      </div>
      </form> 
      
           <div class="row">
      
        <div class="col-lg-4 col-sm-12">
      <div class="panel panel-primary">
            <div class="panel-heading">Create rules to override the default slot configuration</div>
             <div class="panel-body">
              <p id="date-slots-form-notice">Select the user you want to configure using the options above</p>

            <form id="slot-date-form" style="display:none">
            <p>Add new threshold for <b><span class="slot-rule-user"></span></b></p>
            <div class="form-group col-sm-12">
            <label>Slot</label><br />
            <select name="slot_id[]"  multiple class="selectpicker" id="slot-date-rule-select"></select>
            </div>
            <div class="form-group col-sm-6">
            <label>Date From</label><br />
            <input type="text" id="date-from" name="date_from" class="form-control date" />
            </div> 
            <div class="form-group col-sm-6">
            <label>Date To</label><br />
            <input type="text" id="date-to" name="date_to" class="form-control date" />
            </div> 
             <div class="form-group col-sm-12">
             <label>Maximum appointments allowed in this slot</label><br />
            <input type="text" name="max_apps" class="form-control" />
            </div> 
             <div class="form-group col-sm-12">
             <label>Notes/Reason</label><br />
            <input type="text" name="notes" class="form-control" placeholder="Eg: Do not book 9am-11am" />
            </div> 
             <div class="form-group col-sm-12">
             <button class="btn btn-primary" id="add-date-rule">Add</button>
             </div>
            </form>
            </div>
      </div>

      </div>
        <div class="col-lg-8 col-sm-12">
       <div class="panel panel-primary" id="slot-date-panel">
            <div class="panel-heading">Current rules <span class="slot-rule-user"></span></div>
             <div class="panel-body">
             <p>Select the user you want to configure using the options above</p>
            </div>
      </div>
      </div>
      </div>
<script>
$(document).ready(function(){
	admin.init();
	admin.slots.init();
});
</script>