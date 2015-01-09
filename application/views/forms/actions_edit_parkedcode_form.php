<form style="display:none; padding:10px 20px;" class="form-horizontal edit-parkedcode-form">
    <div class="form-group input-group-sm">
        <p>Please set the parked code for these records</p>
        <select class="selectpicker actions_parked_code_select" name="parked_code">
            <option value="" >Nothing selected</option>
            <?php foreach($parked_codes as $row): ?>
                <option value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group input-group-sm suppress-form" style="display: none">
        <div class="checkbox">
            <label>Check for all campaigns</label>
            <input class="all_campaigns_checkbox" name="all_campaigns" type="checkbox">
        </div>
        <textarea class="form-control" placeholder="Enter the reason here" rows="3" name="reason"></textarea>
    </div>
    <div class="form-actions">
        <span class="marl btn btn-default close-edit-actions-btn">Back</span>
        <button class="marl btn btn-success actions-parkedcode-btn pull-right">Save</button>
    </div>
</form>
