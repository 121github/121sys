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
          <p>Please enter the telephone number</p>
<input type="text" class="form-control" name="user_telephone" title="Enter the telephone number" required/>
        </div>
                <div class="form-group input-group-sm">
          <p>Please enter the email address</p>
<input type="text" class="form-control" name="user_email" title="Enter the email address" required/>
        </div>
                <div class="form-group input-group-sm">
          <p>Please set the status</p>
<select name="user_status" class="selectpicker">
<option value="1">Enabled</option>
<option value="0">Disabled</option>
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
