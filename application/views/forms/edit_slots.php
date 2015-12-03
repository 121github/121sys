      <form style="padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="appointment_slot_id">
         <div class="form-group input-group-sm">
          <p>Please select the slot group</p>
<select id="slot-group-select" name="slot_group_id" class="selectpicker">
<?php foreach($slot_groups as $row){ ?>
<option value="<?php echo $row['slot_group_id'] ?>"><?php echo $row['slot_group_name'] ?></option>
<?php } ?>
<option value="other">Other</option>
</select>
        </div>
        <div style="display:none" id="other-slot-group">
           <div class="form-group input-group-sm">
<input type="text" class="form-control" name="new_group" id="new-slot-group" placeholder="Enter new group name" />
</div>   <div class="form-group input-group-sm">
<button id="add-slot-group" class="btn btn-default">Add</button>
</div>
</div>
        <div class="form-group input-group-sm">
          <p>Please set the slot name</p>
<input type="text" class="form-control" name="slot_name" title="Enter the slot name" required/>
        </div>
          <div class="form-group input-group-sm">
          <p>Please set the slot description</p>
<input type="text" class="form-control" name="slot_description" title="Enter the slot description" required/>
        </div>
                <div class="form-group input-group input-group-sm ">
          <p>Set the slot start time (h:m:s)</p>
<input type="text" class="form-control timepicker" name="slot_start" title="Enter the start time"/>
        </div>        
                     <div class="form-group input-group input-group-sm">
          <p>Set the slot end time (h:m:s)</p>
<input type="text" class="form-control timepicker" name="slot_end" title="Enter the end time"/>
        </div>
        <div class="form-actions pull-right">
         <button class="marl btn btn-default close-btn">Cancel</button>
         <button type="submit" class="marl btn btn-primary save-btn">Save</button>
        </div>
      </form>
