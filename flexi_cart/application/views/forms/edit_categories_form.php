      <form style="padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="item_category_id">
        <div class="form-group input-group-sm">
          <p>Category name</p>
<input type="text" class="form-control" name="item_category_name" title="Enter the category name" required/>
        </div>
        <div class="form-group input-group-sm">
        <p>Catergory Description</p>
<input type="text" class="form-control" name="item_category_description" title="Enter the category description" required/>
        </div>
                <div id="subcat-container" class="form-group input-group-sm"  <?php if(count($subcategories)==0){ echo "style='display:none'"; } ?>>
          <p>Subcategories in this group</p>
<select class="form-control selectpicker" name="subcategories[]" id="subcategory-select" multiple title="Campaigns in this group">
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
