      <form style="display:none; padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="folder_id">
        <div class="form-group input-group-sm">
          <p>Please set the folder name</p>
<input type="text" class="form-control" name="folder_name" title="Enter the folder name" required/>
        </div>
        
                <div class="form-group input-group-sm">
          <p>Please add the file extentions allowed (comma serperated)</p>
<input type="text" class="form-control" name="accepted_filetypes" title="Eg. docx,doc,csv" required/>
        </div>
        
    <div class="form-group input-group-sm">
        <p>Please choose the users that have READ access</p>
        <select name="readusers[]" multiple class="selectpicker pull-left folder-read-users">
            <?php foreach ($users as $row): ?>
                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
        </select>

    </div>
        <div class="form-group input-group-sm">
        <p>Please choose the users that have WRITE access</p>
        <select name="writeusers[]" multiple class="selectpicker pull-left folder-write-users">
            <?php foreach ($users as $row): ?>
                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
        </select>

    </div>
        <div class="form-actions pull-right">
         <button class="marl btn btn-default close-btn">Cancel</button>
         <button type="submit" class="marl btn btn-primary save-btn">Save</button>
        </div>
      </form>
