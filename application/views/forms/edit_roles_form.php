      <form style="display:none; padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="role_id">
        <div class="form-group input-group-sm">
          <p>Please set the role name</p>
<input type="text" class="form-control" name="role_name" title="Enter the role name" required/>
        </div>
   
                <h4>Permissions</h4>
        <ul class="list-group">
       <?php foreach($permissions as $group_name => $group_permissions){ ?>
    <li class="list-group-item" style="height:60px"><?php echo $group_name ?><span class="pull-right"><div>
    <?php foreach($group_permissions as $id=>$name){ ?>
    <div style="display:inline-block"><input id="pm_<?php echo $id ?>" type="checkbox" name="permission[<?php echo $id ?>]"> <?php echo $name ?></div >
     <?php } ?></div>
     </span>
    </li>
    <?php } ?>
  </ul>
       <div class="form-actions pull-right">
         <button class="marl btn btn-default close-btn">Cancel</button>
         <button type="submit" class="marl btn btn-primary save-btn">Save</button>
        </div>
      </form>
