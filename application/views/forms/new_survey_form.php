  <div class="panel-heading">Create Survey <span class="glyphicon glyphicon-remove pull-right close-survey"></span></div>
  <div class="panel-body">
  <div class="edit-panel">
    <div class="survey-content-1">
      <form class="form-horizontal survey-select-form">
        <div class="form-group input-group-sm">
          <p>Please choose the survey you want to create</p>
          <select class="surveypicker" name="survey_ref">
            <?php foreach($survey_options["surveys"] as $row): ?>
            <option <?php echo($row['default']?"selected":"")?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <?php if(count($survey_options['contacts'])>0): ?>
        <div class="form-group input-group-sm">
          <p>Please choose the contact doing the survey</p>
          <select class="contactpicker">
            <?php foreach($survey_options["contacts"] as $id=>$name): ?>
            <option value="<?php echo $id ?>"><?php echo $name ?></option>
            <?php endforeach ?>
            <option value="">Other</option>
          </select>
        </div>
        <?php else: ?> 
		<p>Please add a contact before starting a survey!</p>
        <?php endif ?>       
        <div class="form-actions pull-right">
         
         <button class="marl btn btn-default close-survey">Cancel</button>
         <button type="submit" class="marl btn btn-primary continue-survey">Continue</button>
        </div>
      </form>
    </div>
    <div class="survey-content-2">
        <form class="form-horizontal survey-form">
            </form>
    </div>
    </div>
  </div>
