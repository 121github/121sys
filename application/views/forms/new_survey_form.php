      <form class="survey-select-form">
        <div class="form-group input-group-sm">
          <p>Please choose the survey you want to start</p>
          <select id="surveypicker" class="surveypicker" name="survey_ref">
            <?php foreach($surveys as $row): ?>
            <option <?php echo($row['default']?"selected":"")?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <?php if(@count($contacts)>0): ?>
        <div class="form-group input-group-sm">
          <p>Please choose the contact doing the survey</p>
          <select id="contactpicker" class="contactpicker">
            <?php foreach(@$contacts as $id=>$name): ?>
            <option value="<?php echo $id ?>"><?php echo $name ?></option>
            <?php endforeach ?>
            <option value="">Other</option>
          </select>
        </div>
        <?php else: ?> 
		<p>Please add a contact before starting a survey!</p>
        <?php endif ?>       
      </form>