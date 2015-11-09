<div class="edit-panel">
<form class="form-horizontal"> 
<p>Please use the menu below to reallocate this record to other users</p> 
        <div class="form-group input-group-sm">
 <select class="selectpicker owners" multiple name="ownership">
 <?php foreach($users as $user): ?>
 <option value="<?php echo $user['user_id'] ?>"><?php echo $user['name'] ?></option>
 <?php endforeach; ?>
 </select>
</div>
          <div class="form-actions pull-right">  
            <button type="submit" class="btn btn-primary save-ownership">Save changes</button>  
            <button class="btn btn-default close-owner">Cancel</button>  
          </div>  
        </fieldset>  
</form>
</div> 