
<form style="display:none; padding:10px 20px;" class="form-horizontal">
  <input type="hidden" name="team_id">
  <div class="form-group input-team-sm">
    <p>Please set the team name</p>
    <input type="text" class="form-control" name="team_name" title="Enter the team name" required/>
  </div>
  <div class="form-group input-group-sm">
    <p>Which group does this team belong to?</p>
    <select name="group_id" class="selectpicker" id="group-select">
      <?php foreach($options['groups'] as $group){ ?>
      <option value="<?php echo $group['id'] ?>"><?php echo $group['name'] ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="form-group input-group-sm">
    <p>Who can manage this team? (Hours, Data etc)</p>
    <select name="managers[]" multiple class="selectpicker" id="user-select">
      <?php foreach($options['managers'] as $manager){ ?>
      <option value="<?php echo $manager['id'] ?>"><?php echo $manager['name'] ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="form-actions pull-right">
    <button class="marl btn btn-default close-btn">Cancel</button>
    <button type="submit" class="marl btn btn-primary save-btn">Save</button>
  </div>
</form>
