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
    <div class="form-actions">
        <span class="marl btn btn-default close-edit-actions-btn">Back</span>
        <button class="marl btn btn-success actions-parkedcode-btn pull-right">Save</button>
    </div>
</form>
