  <div class="panel-heading">Send Email <span class="glyphicon glyphicon-remove pull-right close-email"></span></div>
  <div class="panel-body">
  <div class="edit-panel">
    <div class="email-content">
      <form class="form-horizontal email-select-form">
        <div class="form-group input-group-sm">
         <?php if(count($email_options["templates"])>0){ ?>
          <p>Please choose the template you want to use</p>
          <select class="emailtemplatespicker" name="email_ref">
          <option value="">--Select template--</option>
            <?php foreach($email_options["templates"] as $row): ?>
            <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
          </select>
          <?php } else { ?>
          <p>No email templates have been configured for this campaign</p>
          <?php } ?>
        </div>
        <div class="form-actions pull-right">
         
         <button class="marl btn btn-default close-email">Cancel</button>
         <button type="submit" class="marl btn btn-primary continue-email" disabled>Continue</button>
        </div>
      </form>
    </div>
    </div>
  </div>
