  <div class="panel-heading">Send Email <span class="glyphicon glyphicon-remove pull-right close-outcome-btn"></span></div>
  <div class="panel-body">
  <div class="outcome-panel">
    <div class="outcome-content">
        <form id="outcome-form">
            <div class="row">
                <div class="col-lg-12">
                    <div class="btn-group">
                        <input type="hidden" name="outcome_id">
                        <div class="form-group input-group-sm">
                            <label>Name</label>
                            <input type="text" name="outcome" class="form-control"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <label>Status</label>
                            <select class="selectpicker status_select" name="status_id">
                                <option value="">Nothing selected</option>
                                <?php foreach($status_list as $status): ?>
                                    <option value="<?php echo $status['id'] ?>"><?php echo $status['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <label>Progress</label>
                            <select class="selectpicker progress_select" name="progress_id">
                                <option value="">Nothing selected</option>
                                <?php foreach($progress_list as $progress): ?>
                                    <option value="<?php echo $progress['id'] ?>"><?php echo $progress['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="btn-group">
                        <input type="hidden" name="outcome_id">
                        <div class="form-group input-group-sm">
                            <input id="disable" type='checkbox' name="disable">
                            <label>Disabled</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <label>Sort</label>
                            <input type="text" name="sort" class="form-control"/>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <label>Delay Hours</label>
                            <input type="text" name="delay_hours" class="form-control"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="keep_record" type='checkbox' name="keep_record">
                            <label>Keep record</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="force_comment" type='checkbox' name="force_comment">
                            <label>Force comment</label>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="force_nextcall" type='checkbox' name="force_nextcall">
                            <label>Force Nextcall</label>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="positive" type='checkbox' name="positive">
                            <label>Positive</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="positive" type='checkbox' name="positive">
                            <label>Dm contact</label>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="positive" type='checkbox' name="positive">
                            <label>Enable select</label>

                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                <span class="marl btn btn-default close-outcome-btn">Cancel</span>
                <span class="marl btn btn-success save-outcome-btn">Save</span>
            </div>
        </form>
    </div>
    </div>
  </div>
