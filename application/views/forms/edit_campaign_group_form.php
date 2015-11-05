      <form style="padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="campaign_group_id">
        <div class="form-group input-group-sm">
          <p>Campaign group name</p>
<input type="text" class="form-control" name="campaign_group_name" title="Enter the campaign group name" required/>
        </div>
                <div class="form-group input-group-sm">
          <p>Campaigns in this group</p>
<select class="form-control selectpicker" name="campaigns[]" id="campaign-group-select" multiple title="Campaigns in this group">
<?php foreach($campaigns as $campaign){ ?>
<option value="<?php echo $campaign['id'] ?>"><?php echo $campaign['name'] ?></option>
<?php } ?>
</select>
        </div>
        <div class="form-actions pull-right">
         <button class="marl btn btn-default close-btn">Cancel</button>
         <button type="submit" class="marl btn btn-primary save-btn">Save</button>
        </div>
      </form>
