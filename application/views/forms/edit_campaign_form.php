<form style="display:none; padding:10px 20px;" class="form-horizontal">
    <input type="hidden" name="campaign_id">

    <div class="form-group input-group-sm">
        <p>Please set the campaign name</p>
        <input type="text" class="form-control" name="campaign_name" title="Enter the campaign name" required/>
    </div>
    <div class="form-group input-group-sm">
        <p>Please set the campaign type</p>
        <select name="campaign_type_id" class="selectpicker pull-left" required>
            <option value="">Nothing selected</option>
            <?php foreach ($types as $row): ?>
                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
        </select>

    </div>
    <div class="form-group input-group-sm">
        <p>Please choose the campaign features</p>
        <select name="features[]" multiple class="selectpicker pull-left campaign-features">
            <?php foreach ($features as $row): ?>
                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
        </select>

    </div>
    <div class="form-group input-group-sm">
        <p>Please set the custom panel title if applicable</p>
        <input type="text" class="form-control" name="custom_panel_name" placeholder="Eg. Policy information" required/>
    </div>
    <div class="form-group input-group-sm">
        <p>Please select the client</p>
        <select name="client_id" id="client-select" class="selectpicker pull-left">
            <option value="">Nothing selected</option>
            <?php foreach ($clients as $row): ?>
                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
            <option value="other">Other</option>
        </select>
        <input type="text" name="new_client" class="form-control pull-left marl" style="width:200px; display:none"
               placeholder="Enter the client name"/>
    </div>
        <div class="form-group input-group-sm">
        <p>Please set the record layout</p>
        <select name="record_layout" class="selectpicker" required>
 <?php foreach ($views as $view): ?>
                <option value="<?php echo $view ?>"><?php echo str_replace('.php','',$view) ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group input-group-sm">
        <p>Please set the campaign status</p>
        <select name="campaign_status" class="selectpicker" required>
            <option value="">Nothing selected</option>
            <option value="1">Live</option>
            <option value="0">Not Live</option>
        </select>
    </div>
    <div class="form-group input-group-sm">
        <p>Please set the start date</p>

        <div class="input-group">
            <input data-date-format="DD/MM/YYYY" name="start_date" type="text" class="form-control date"
                   placeholder="Start Date">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
    </div>
    <div class="form-group input-group-sm">
        <p>Please set the end date</p>

        <div class="input-group">
            <input name="end_date" data-date-format="DD/MM/YYYY" type="text" class="form-control date"
                   placeholder="End Date">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
    </div>
    <div class="form-group input-group-sm">
        <p>Please set Quote Days for a renewal date</p>

        <div class="input-group">
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-4">
                    <p>Min Quote Days</p>
                    <input name="min_quote_days" type="text" class="form-control" placeholder="Min Quote">
                </div>
                <div class="col-xs-4">
                    <p>Max Quote Days</p>
                    <input name="max_quote_days" type="text" class="form-control" placeholder="Max Quote">
                </div>
                <div class="col-xs-3" style="color: red;">
                    <p></p>
                    <p class="quote_days_error"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group input-group-sm">
        <p>Months to backup this campaign</p>
        <select name="months_ago" id="months-ago" class="selectpicker pull-left">
            <option value="">Nothing selected</option>
            <?php for ($i=1;$i<=12;$i++): ?>
                <option value="<?php echo $i; ?>"><?php echo $i." months ago"; ?></option>
            <?php endfor ?>
        </select>
        <input type="text" name="months_num" class="form-control pull-left marl" style="width:300px;"
               placeholder="Enter the number of months to backup"/>
    </div>
    <div class="form-actions pull-right">
        <button class="marl btn btn-default close-btn">Cancel</button>
        <button type="submit" class="marl btn btn-primary save-btn">Save</button>
    </div>
</form>
