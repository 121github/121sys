      <form style="padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="appointment_slot_id">
        <div class="form-group input-group-sm">
          <p>Please set the slot name</p>
<input type="text" class="form-control" name="slot_name" title="Enter the slot name" required/>
        </div>
          <div class="form-group input-group-sm">
          <p>Please set the slot description</p>
<input type="text" class="form-control" name="slot_description" title="Enter the slot description" required/>
        </div>
                <div class="form-group input-group input-group-sm ">
          <p>Set the slot start time</p>
<input type="text" class="form-control timepicker" name="slot_start" title="Enter the start time"/>
        </div>        
                     <div class="form-group input-group input-group-sm">
          <p>Set the slot end time</p>
<input type="text" class="form-control timepicker" name="slot_end" title="Enter the end time"/>
        </div>
        <div class="form-actions pull-right">
         <button class="marl btn btn-default close-btn">Cancel</button>
         <button type="submit" class="marl btn btn-primary save-btn">Save</button>
        </div>
      </form>
