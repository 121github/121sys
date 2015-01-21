<form style="display:none; padding:10px 20px;" class="form-horizontal edit-ownership-form">
    <p>Please use the menu below to reallocate these records to other users</p>
    <div class="form-group input-group-sm">
        <select class="selectpicker actions_ownership_select" name="ownership_id[]" multiple>
            <?php foreach($users as $user): ?>
                <option value="<?php echo $user['id'] ?>"><?php echo $user['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-actions">
        <span class="marl btn btn-default close-edit-actions-btn">Back</span>
        <button type="submit" class="marl btn btn-success actions-ownership-add-btn pull-right">Add</button>
        <button type="submit" class="marl btn btn-warning actions-ownership-replace-btn pull-right">Replace</button>
    </div>
</form>
