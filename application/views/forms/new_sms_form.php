      <form id="sms-select-form">
        <div class="form-group">
         <?php if(count($templates)>0){ ?>
          <p>Please choose the template you want to use</p>
          <select id="smstemplatespicker" class="smstemplatespicker" name="sms_ref">
          <option value="">--Select template--</option>
            <?php foreach($templates as $row): ?>
            <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
          </select>
          <?php } else { ?>
          <p>No sms templates have been configured for this campaign</p>
          <?php } ?>
        </div>
      </form>
