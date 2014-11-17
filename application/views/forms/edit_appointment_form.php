      <form style="display:none; padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="appointment_id">
        <input name="urn" type="hidden" value="">
        <div class="form-group input-group-sm">
          <p>Please enter a title</p>
<input type="text" class="form-control" name="title" placeholder="Enter the title" required/>
        </div>
                <div class="form-group input-group-sm">
          <p>Please enter a description</p>
<input type="text" class="form-control" name="text" placeholder="Enter the description" required/>
        </div>
                <div class="form-group input-group-sm">
          <p>Please set the start time</p>
<input type="text" class="form-control datetime startpicker" name="start" placeholder="Enter the start time" required/>
        </div>
                <div class="form-group input-group-sm">
          <p>Please set the end time</p>
<input type="text" class="form-control datetime endpicker" name="end" placeholder="Enter the end time" required/>
        </div>
                <div class="form-group input-group-sm">
          <p>Please add the attendees</p>
<select name="attendees[]" class="selectpicker attendeepicker" title="Choose the attendees" data-width="100%">
 <?php foreach($users as $user): ?>
 <option value="<?php echo $user['user_id'] ?>"><?php echo $user['name'] ?></option>
 <?php endforeach; ?>
</select>
        </div>
        
                                <div class="form-group input-group-sm">
          <p>Please select the address the appointment will take place</p>
<select class="selectpicker addresspicker" title="Choose the address" required data-width="100%">
 <?php foreach($addresses as $address): 
 $add = $address['name'];
 $add .= (!empty($address['add1'])?", ".$address['add1']:"");
  $add .= (!empty($address['postcode'])?", ".$address['postcode']:" - This address has no postcode!");
 ?>
 <option value="<?php echo $address['postcode'] ?>"><?php echo $add ?></option>
 <?php endforeach; ?>
   <option value="">Other</option>
</select>
        </div>
        
                        <div class="form-group input-group-sm">
          <p>Or enter the postcode manually</p>
          <?php $postcode = ""; if(count($addresses)=="1"){$postcode = $addresses[0]['postcode']; } ?>
<input type="text" class="form-control" name="postcode" placeholder="Postcode of the appointment location" required value="<?php echo $postcode ?>"/>
        </div>
                               
        <div class="form-actions pull-right">
         <button class="marl btn btn-default close-appointment">Cancel</button>
         <button type="submit" class="marl btn btn-primary save-appointment">Save</button>
        </div>
      </form>
