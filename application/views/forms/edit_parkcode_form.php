  <div class="panel-heading">Send Email <span class="glyphicon glyphicon-remove pull-right close-parkedcode-btn"></span></div>
  <div class="panel-body">
  <div class="parkedcode-panel">
    <div class="parkcode-content">
        <form id="parkcode-form">
            <div class="row">
                <div class="col-lg-8">
                    <div class="btn-group">
                        <input type="hidden" name="parked_code">
                        <div class="form-group input-group-sm">
                            <label>Park Code</label>
                            <div><span style="color: red; font-size: 11px; display: none;" class="parkcode-error"></span></div>
                            <input type="text" name="park_reason" class="form-control" required/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                <span class="marl btn btn-default close-parkcode-btn">Cancel</span>
                <span class="marl btn btn-success save-parkcode-btn">Save</span>
            </div>
        </form>
    </div>
    </div>
  </div>
