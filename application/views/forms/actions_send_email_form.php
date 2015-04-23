<form style="display:none; padding:10px 20px;" class="form-horizontal send-email-form">
    <div class="form-group input-group-sm">
        <input type="hidden" name="suppress">
        <p>Please select the template</p>
        <select id="actions_template_select" class="selectpicker actions_template_select" name="template_id">
            <option value="" >Nothing selected</option>
            <?php foreach($email_templates as $row): ?>
                <option value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-actions">
        <span class="marl btn btn-default close-edit-actions-btn">Back</span>
        <button class="marl btn btn-success actions-send-email-btn pull-right">Select</button>
    </div>
</form>
