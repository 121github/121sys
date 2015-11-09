      <form style="padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="category_id">
        <div class="form-group input-group-sm">
          <p>Catergoty name</p>
<input type="text" class="form-control" name="category_name" title="Enter the category name" required/>
        </div>
                <div class="form-group input-group-sm">
          <p>Subcategories in this group</p>
<select class="form-control selectpicker" name="campaigns[]" id="subcategory-select" multiple title="Campaigns in this group">
<?php foreach($subcategories as $row){ ?>
<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
<?php } ?>
</select>
        </div>
        <div class="form-actions pull-right">
         <button class="marl btn btn-default close-btn">Cancel</button>
         <button type="submit" class="marl btn btn-primary save-btn">Save</button>
        </div>
      </form>
