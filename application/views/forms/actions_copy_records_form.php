<form style="display:none; padding:10px 20px;" class="form-horizontal copy-records-form">
    <ul class="list-group">
    <p>Please use the menu below to copy these records to other campaigns</p>
    <div class="form-group input-group-sm">
        <select class="selectpicker actions_campaign_select" name="campaign">
            <option value="" >Nothing selected</option>
            <?php foreach($campaigns as $row): ?>
                <option value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-actions">
        <span class="marl btn btn-default close-edit-actions-btn">Back</span>
        <button type="submit" class="marl btn btn-primary actions-copy-btn pull-right">Copy</button>
    </div>
</form>
