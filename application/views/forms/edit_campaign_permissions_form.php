      <form style="display:none; padding:10px 20px;" id="cp-form">
      <input type="hidden" name="campaign_id">
           <ul class="list-group">
       <?php foreach($permissions as $group_name => $group_permissions){ ?>
    <li class="list-group-item" style="height:auto"><?php echo $group_name ?><span class="pull-right" style="width:80%">
    <?php foreach($group_permissions as $id=>$name){ ?>
    <div class="checkbox-group" style="display:inline-block; width:170px">
    <input id="pm_<?php echo $id ?>" type="hidden" name="permission[<?php echo $id ?>]" />
    <input id="cb_<?php echo $id ?>" type="checkbox" ><label class="cbx-label"><span data-toggle="tooltip" class="tt" title="<?php echo $name['description'] ?>"><?php echo $name['name'] ?></span></label></div ><?php } ?>
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
