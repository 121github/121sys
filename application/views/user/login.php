<?php echo validation_errors(); ?>

<?php if($this->session->flashdata('error')||isset($_SESSION['logout_message'])): ?>
    <div class="alert alert-danger alert-dismissable" style="margin-top:10px">  
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>  
  <?php echo ($this->session->flashdata('error')?$this->session->flashdata('error'):$_SESSION['logout_message']); ?>  
	</div>  
<?php endif; ?>
    
          <form class="form-signin" role="form" method="post">
    <?php if($redirect): ?>
    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
    <?php endif; ?>          <h2 class="form-signin-heading">Please sign in</h2>
     <div class="form-group">
        <input type="text" name="username" class="form-control" placeholder="Username" required <?php echo (!$this->session->flashdata('username')?"autofocus":"") ?> value="<?php echo $this->session->flashdata('username'); ?>">
        </div>
         <div class="form-group">
        <input type="password" name="password" class="form-control" placeholder="Password" <?php echo ($this->session->flashdata('username')?"autofocus":"") ?> required>
        </div>
        <label class="checkbox">
          <input type="checkbox" value="remember-me"> Remember me
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>
    
