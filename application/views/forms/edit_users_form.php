      <form style="display:none; padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="user_id">
        <div class="form-group input-group-sm">
          <p>Please enter the username</p>
<input type="text" class="form-control" name="username" title="Enter the user name" required/>
        </div>
                <div class="form-group input-group-sm">
          <p>Please enter the full name</p>
<input type="text" class="form-control" name="name" title="Enter the full name" required/>
        </div>
                        <div class="form-group input-group-sm">
          <p>Please enter the email address</p>
<input type="text" class="form-control" name="user_email" title="Enter the email address" required/>
        </div>
                <div class="form-group input-group-sm">
          <p>Please enter the telephone number</p>
<input type="text" class="form-control" name="user_telephone" title="Enter the telephone number" required/>
        </div>
                        <div class="form-group input-group-sm">
          <p>Please enter the telephone extension</p>
<input type="text" class="form-control" name="ext" title="Enter the extension number" required/>
        </div>
                        <div class="form-group input-group-sm">
          <p>Please enter the telephony system username</p>
<input type="text" class="form-control" name="phone_un" title="Enter the telephony username" required/>
        </div>
                                <div class="form-group input-group-sm">
          <p>Please enter the telephony system password</p>
<input type="password" class="form-control" name="phone_pw" title="Enter the telephony password" required/>
        </div>

                <div class="form-group input-group-sm">
          <p>Please set the status</p>
<select name="user_status" class="selectpicker">
<option value="1">Enabled</option>
<option value="0">Disabled</option>
</select>
        </div>
                        <div class="form-group input-group-sm">
          <p>Attendee <span data-toggle='tooltip' title='Can appointments be booked for this user?' class='fa fa-question-circle'></span></p>
<select name="attendee" class="selectpicker">
<option value="0">No</option>
<option value="1">Yes</option>
</select>
        </div>
        
                                      <div class="form-group input-group-sm">
          <p>User Default Postcode <span data-toggle='tooltip' title='This could be a the home or office postcode and is used in distance calculations when booking appointments' class='fa fa-question-circle'></span></p>
<input type="text" class="form-control" name="home_postcode" title="Enter the default postcode" required/>
        </div>
        
                         <div class="form-group input-group-sm">
          <p>Enable ICS (calendar invites) <span data-toggle='tooltip' title='If enabled the user will be sent a calendar invite to their associated email address whenever an appointment is booked for them' class='fa fa-question-circle'></span></p>
<select name="ics" class="selectpicker">
<option value="0">No</option>
<option value="1">Yes</option>
</select>
        </div>       
        
                        <div class="form-group input-group-sm">
          <p>Please set the user role</p>
<select name="role_id" class="selectpicker">
<?php foreach($roles as $row){ ?>
<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
<?php } ?>
</select>
        </div>
                                <div class="form-group input-group-sm">
          <p>Please set the user group</p>
<select name="group_id" class="selectpicker">
<?php foreach($groups as $row){ ?>
<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
<?php } ?>
</select>
        </div>
                                        <div class="form-group input-group-sm">
          <p>Please set the user team if applicable</p>
<select name="team_id" class="selectpicker">
<option value="">No team</option>
<?php foreach($teams as $row){ ?>
<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
<?php } ?>
</select>
        </div>
        <div class="form-actions pull-right">
         <button class="marl btn btn-default close-btn">Cancel</button>
         <button type="submit" class="marl btn btn-primary save-btn">Save</button>
        </div>
      </form>
