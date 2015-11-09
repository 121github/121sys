<form style="display:none; padding:10px 20px;" class="form-horizontal edit-parkedcode-form">
    <div class="form-group input-group-sm">
        <input type="hidden" name="suppress">
        <p>Please set the parked code for these records</p>
        <select class="selectpicker actions_parked_code_select" name="parked_code">
            <option value="" >Nothing selected</option>
            <option value="NULL" >Unparked</option>
            <?php foreach($parked_codes as $row): ?>
                <option value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group input-group-sm suppress-form" style="display: none">
        <p>Please select the campaign</p>
        <span style="color: red; font-size: 11px; display: none;" class="change-parked-code-campaign-error"></span>
        <div class="checkbox">
            <label>Check for all campaigns</label>
            <input class="all_campaigns_checkbox" id="all_campaigns" name="all_campaigns" type="checkbox">

            <select class="selectpicker actions_parked_code_campaign" name="parked_code_campaign_id[]" multiple>
                <?php foreach($campaigns as $row): ?>
                    <option value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <p>Please set the reason</p>
        <textarea class="form-control" placeholder="Enter the reason here" rows="3" name="reason"></textarea>
    </div>
    <div class="form-actions">
        <span class="marl btn btn-default close-edit-actions-btn">Back</span>
        <button class="marl btn btn-success actions-parkedcode-btn pull-right">Save</button>
    </div>
</form>
