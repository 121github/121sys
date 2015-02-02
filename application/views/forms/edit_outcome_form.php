  <div class="panel-heading">Send Email <span class="glyphicon glyphicon-remove pull-right close-outcome-btn"></span></div>
  <div class="panel-body">
  <div class="outcome-panel">
    <div class="outcome-content">
        <form id="outcome-form">
            <div class="row">
                <div class="col-lg-8">
                    <div class="btn-group">
                        <input type="hidden" name="outcome_id">
                        <div class="form-group input-group-sm">
                            <label>Name</label>
                            <input type="text" name="outcome" class="form-control" required/>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="disabled" type='checkbox' name="disabled" onclick="$(this).attr('value', this.checked ? 1 : 0)">
                            <label>Disabled</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <label>Status</label>
                            <select class="selectpicker status_select" name="set_status">
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
                            <select class="selectpicker progress_select" name="set_progress">
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
                        <div class="form-group input-group-sm">
                            <input id="no_history" type='checkbox' name="no_history" onclick="$(this).attr('value', this.checked ? 1 : 0)">
                            <label>No history</label>
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
                            <input id="keep_record" type='checkbox' name="keep_record" onclick="$(this).attr('value', this.checked ? 1 : 0)">
                            <label>Keep record</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="force_comment" type='checkbox' name="force_comment" onclick="$(this).attr('value', this.checked ? 1 : 0)">
                            <label>Force comment</label>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="force_nextcall" type='checkbox' name="force_nextcall" onclick="$(this).attr('value', this.checked ? 1 : 0)">
                            <label>Force Nextcall</label>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="positive" type='checkbox' name="positive" onclick="$(this).attr('value', this.checked ? 1 : 0)">
                            <label>Positive</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="dm_contact" type='checkbox' name="dm_contact" onclick="$(this).attr('value', this.checked ? 1 : 0)">
                            <label>Dm contact</label>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="btn-group">
                        <div class="form-group input-group-sm">
                            <input id="enable_select" type='checkbox' name="enable_select" onclick="$(this).attr('value', this.checked ? 1 : 0)">
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
