      <form id="email-select-form">
        <div class="form-group">
         <?php if(count($templates)>0){ ?>
          <p>Please choose the template you want to use</p>
          <select id="emailtemplatespicker" class="emailtemplatespicker" name="email_ref">
          <option value="">--Select template--</option>
            <?php foreach($templates as $row): ?>
            <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
          </select>
          <?php } else { ?>
          <p>No email templates have been configured for this campaign</p>
          <?php } ?>
        </div>
      </form>
