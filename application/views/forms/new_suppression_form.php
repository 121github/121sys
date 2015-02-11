  <div class="panel-heading">New Suppression number <span class="glyphicon glyphicon-remove pull-right close-suppression-btn"></span></div>
  <div class="panel-body">
  <div class="suppression-panel">
    <div class="suppression-content">
        <form id="suppression-form">
            <span class="suppression_exist" style="display: none;color: red; margin-bottom: 5px">This telephone number is already suppressed. You are going to modify it</span>
            <div class="row">
                <div class="col-lg-5">
                    <div class="btn-group">
                        <input type="hidden" name="suppression_id">
                        <div class="form-group input-group-sm">
                            <label>Telephone Number</label>
                            <input type="text" name="telephone_number" class="form-control" required/>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="btn-group">
                        <label>Please select the campaign</label>
                        <span style="color: red; font-size: 11px; display: none;" class="change-parked-code-campaign-error"></span>
                        <div class="checkbox">
                            <label>Check for all campaigns</label>
                            <input class="all_campaigns_checkbox" id="all_campaigns" name="all_campaigns" type="checkbox">
                        </div>
                        <div>
                            <select class="selectpicker suppression_campaign_select" name="suppression_campaign_id[]" multiple>
                                <?php foreach($campaigns as $row): ?>
                                    <option value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div style="margin-bottom: 10px">
                        <label>Reason</label>
                        <textarea class="form-control" placeholder="Enter the reason here" rows="3" name="reason"></textarea>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                <span class="marl btn btn-default close-suppression-btn">Cancel</span>
                <span class="marl btn btn-success save-suppression-btn">Save</span>
            </div>
        </form>
    </div>
    </div>
  </div>
