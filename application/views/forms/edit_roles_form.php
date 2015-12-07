      <form style="display:none; padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="role_id">
        <div class="form-group input-group-sm">
          <p>Please set the role name</p>
<input type="text" class="form-control" name="role_name" title="Enter the role name" required/>
        </div>
   
                <h4>Permissions</h4>
        <ul class="list-group">
       <?php foreach($permissions as $group_name => $group_permissions){ ?>
    <li class="list-group-item" style="height:auto"><?php echo $group_name ?><span class="pull-right" style="width:80%">
    <?php foreach($group_permissions as $id=>$name){ ?>
    <div style="display:inline-block; width:150px"><input id="pm_<?php echo $id ?>" type="checkbox" name="permission[<?php echo $id ?>]"> <span data-toggle="tooltip" class="tt" title="<?php echo $name['description'] ?>"><?php echo $name['name'] ?></span></div ><?php } ?>
</span>
<div class="clearfix"></div>
    </li>
    <?php } ?>
  </ul>
    
       <div class="form-actions pull-right">
         <button class="marl btn btn-default close-btn">Cancel</button>
         <button type="submit" class="marl btn btn-primary save-btn">Save</button>
        </div>
      </form>
