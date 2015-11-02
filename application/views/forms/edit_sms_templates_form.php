<form style="display: none; padding: 10px 20px;" class="form-horizontal">
    <input type="hidden" name="template_id">

    <div class="row">
        <div class="col-xs-8">
            <div class="form-group input-group-sm">
                <p>Please set the template name</p>
                <input type="text" class="form-control" name="template_name"
                       title="Enter the template name" required/>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group pull-right">
                <label for="type">Include unsubscribe message</label>
                <br>

                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-info btn-sm">
                        <input type="radio" name="template_unsubscribe" value="1" autocomplete="off"
                               id="unsubscribe-yes">Yes
                    </label>
                    <label class="btn btn-info btn-sm renewal-label">
                        <input type="radio" name="template_unsubscribe" value="0" autocomplete="off"
                               id="unsubscribe-no">No
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8">
            <div class="form-group input-group-sm">
                <p>From</p>
                <select name="template_sender_id" class="selectpicker" id="sender_select" data-width="70%" data-size="5">
                    <option value="">Nothing selected</option>
                    <?php foreach ($sms_senders as $row): ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php endforeach; ?>
                    <option value="0">Custom Sender</option>
                </select>
                         <input type="text" placeholder="Eg:[c3]" class="form-control" style="width:15%;display:inline-block" disabled name="custom_sender" id="custom-sender" value="" />             
            </div>
              
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8">
            <div class="form-group input-group-sm">
                <p>Campaigns</p>
                <select name="campaign_id[]" class="selectpicker" id="campaigns_select" data-width="100%" data-size="5"
                        multiple>
                    <?php foreach ($campaigns as $row): ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group input-group-sm">
        <p>Text (<span id="chars"></span> characters remaining...)</p>
        <textarea class="form-control" title="Enter the sms text" name="template_text" required
                  style="width: 1112px; height: 298px;" maxlength="305"></textarea>
    </div>

    <!-- SUBMIT AND CANCEL BUTTONS -->
    <div class="form-actions pull-right">
        <button class="marl btn btn-default close-btn">Cancel</button>
        <button type="submit" class="marl btn btn-primary save-btn">Save</button>
    </div>
</form>