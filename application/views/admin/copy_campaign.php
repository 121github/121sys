<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Clone Campaign Setup</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->
<div class="row">
<div class="col-lg-12">
  <div class="panel panel-primary campaign-panel">
    <div class="panel-heading">Clone Campaign Setup </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
      <form id="copycampaignform">
        <div class="form-group">
          <label>Copy from</label>
          <br />
          <select required class="selectpicker" name="campaign_id">
            <?php foreach($options['campaigns'] as $row){ ?>
            <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group">
          <label>Copy to</label>
          <input name="new_name" required type="text" class="form-control" placeholder="Enter the new campaign name"/>
        </div>
        <div class="form-group">
          <label>Which settings should be copied?</label>
          <br />
          <select multiple name="tables[]" class="selectpicker" data-width="100%" id="copycampaignselect">
            <option selected value="features">Features</option>
            <option selected value="appointment_types">Available Appointment Types</option>
            <option selected value="campaign_permissions">Permissions</option>
            <option selected value="tasks">Tasks</option>
                        <option selected value="outcomes">Outcomes</option>
            <option selected value="triggers">Outcome triggers</option>
            <option selected value="ownership_triggers">Ownership triggers</option>

            <option selected value="scripts">Scripts</option>
                        <option selected value="emails">Email Templates</option>
            <option selected value="email_triggers">Email Triggers</option>
            <option selected value="sms">SMS templates</option>
            <option selected value="sms_triggers">SMS triggers</option>
            <option selected value="surveys">Surveys</option>
            <option selected value="webforms">Webforms</option>
                        <option selected value="managers">Associated Managers</option>
          </select> <button class="btn btn-warning btn-sm" id="deselectall">Deselect all</button> <button class="btn btn-info btn-sm" id="selectall">Select all</button>
        </div>
        <button class="btn btn-primary" id="copycampaignsubmit" >Submit</button>
      </form>
    </div>
    <!-- /.panel-body --> 
  </div>
</div>
