<div class="panel-heading">
    <div style="font-size: 18px;">Found: <span class="records-found" style="color: green;"></span> records <span class="saving"></span> <span class="glyphicon glyphicon-remove pull-right close-actions"></span></div>
</div>
<div class="panel-body">
    <div class="actions-panel">
        <div class="actions-content">
          <div class="actions-select-form">
              <ul class="list-group">
                  <li class="list-group-item" style="font-weight: bold;">PARCKED CODE</li>
                  <li class="list-group-item">
                      <button class="btn change-parkedcode btn-success" style="width:130px">Set</button>
                      Set/Change the parked code <span style="font-weight: bold" class="change-parkedcode-result pull-right"></span>
                  </li>

                  <li class="list-group-item" style="font-weight: bold;">OWNERSHIP</li>
                  <li class="list-group-item">
                      <button class="btn change-ownership btn-success" style="width:130px">Set</button>
                      Set/Change the ownership <span style="font-weight: bold" class="change-ownership-result pull-right"></span>
                  </li>

                  <li class="list-group-item" style="font-weight: bold;">CAMPAIGN</li>
                  <li class="list-group-item">
                      <button class="btn copy-records btn-warning" style="width:130px">Copy</button>
                      Copy the records to another campaign <span style="font-weight: bold" class="copy-records-result pull-right"></span>
                  </li>
                  <li class="list-group-item">
                      <span style="color: red; font-size: 11px; display: none;" class="copy_records_error">* You have more than one campaing selected in the main filter</span>
                  </li>

              </ul>
             <button class="marl btn btn-default close-actions pull-right">Cancel</button>
          </div>
        </div>
        <?php $this->view('forms/actions_edit_parkedcode_form.php'); ?>
        <?php $this->view('forms/actions_edit_ownership_form.php'); ?>
        <?php $this->view('forms/actions_copy_records_form.php'); ?>
    </div>
</div>
