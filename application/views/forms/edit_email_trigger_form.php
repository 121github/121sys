  <div class="panel-heading">Send Email <span class="glyphicon glyphicon-remove pull-right close-email-trigger-btn"></span></div>
  <div class="panel-body">
  <div class="email-trigger-panel">
    <div class="email-trigger-content">
        <form id="email-trigger-form">
            <div class="row">
                <div class="col-lg-6">
                    <div class="btn-group">
                        <input type="hidden" name="trigger_id">
                        <div class="form-group input-group-sm">
                            <label class="campaign_label">Campaign</label>
                            <select class="selectpicker campaign_select" name="campaign_id">
                                <option value="">Nothing selected</option>
                                <?php foreach($campaigns as $campaign): ?>
                                    <option value="<?php echo $campaign['id'] ?>"><?php echo $campaign['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <label class="outcome_label">Outcome</label>
                            <select class="selectpicker outcome_select" name="outcome_id">
                                <option value="">Nothing selected</option>
                                <?php foreach($outcomes as $outcome): ?>
                                    <option value="<?php echo $outcome['id'] ?>"><?php echo $outcome['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <label class="template_label">Template</label>
                            <select class="selectpicker template_select" name="template_id">
                                <option value="">Nothing selected</option>
                                <?php foreach($templates as $template): ?>
                                    <option value="<?php echo $template['id'] ?>"><?php echo $template['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <label class="users_label">Recipients</label>
                            <select class="selectpicker user_select" multiple name="user_id[]">
                                <option value="">Nothing selected</option>
                                <?php foreach($users as $user): ?>
                                    <option value="<?php echo $user['id'] ?>"><?php echo $user['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                <span class="validation_msg" style="display: none"></span>
                <span class="marl btn btn-default close-email-trigger-btn">Cancel</span>
                <span class="marl btn btn-success save-email-trigger-btn">Save</span>
            </div>
        </form>
    </div>
    </div>
  </div>
