<form class="form-restore_password" role="form" method="post">
    <h2 class="form-restore_password-heading">Restore the password</h2>
         <div class="form-group">
             <span style="color: red; font-size: 11px; display: none;" class="restore-password-error"></span>
             <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
             <input type="hidden" name="username" value="<?php echo $username; ?>">
             <input type="password" name="new_pass" class="form-control" placeholder="New Password" <?php echo ($username?"autofocus":"") ?> required>
             <input type="password" name="conf_pass" class="form-control" placeholder="Confirm Password" <?php echo ($username?"autofocus":"") ?> required>
        </div>
        <button class="btn btn-lg btn-primary save-restored-password" type="submit">Set as the new password</button>
</form>


<script>
    $(document).ready(function(){
        restore_password.init();
    });
</script>