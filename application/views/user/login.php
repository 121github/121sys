<?php //echo validation_errors(); ?>

<?php /* if(isset($_SESSION['logout_message'])): ?>
    <div class="alert alert-danger alert-dismissable" style="margin-top:10px">  
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>  
    <span class="glyphicon glyphicon-alert"></span> <?php echo $_SESSION['logout_message']; ?>  
	</div>  
<?php endif; */ ?>

<section class="login-form">
			 <form class="form-signin" role="login" method="post">
              <?php if($redirect): ?>
    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
    <?php endif; ?>
    <?php 
$logo = base_url()."assets/themes/images/".(isset($_SESSION['theme_images']) ? $_SESSION['theme_images'] : "default")."/login-logo.png"; ?>

	<img id="login-logo" class="img-responsive" onerror="logo_text()" src="<?php echo $logo ?>">	
<div id="no-logo"></div>
					<input type="text" name="username" placeholder="Username" <?php echo (empty($username)?"autofocus":"") ?> value="<?php echo $username ?>" required class="form-control input-lg" />
					<input type="password" name="password" placeholder="Password" required class="form-control input-lg" <?php echo (!empty($username)?"autofocus":"") ?> />
					<button type="submit" name="go" class="btn btn-lg btn-primary btn-block">Sign in</button>
					<div>
						<!--<a href="#">Sign up</a> or --><a href="#" class="forgot-password">Reset password</a>
					</div>
				</form>
				<!--<div class="form-links">
				<a href="http://www.smartprospector.co.uk/">Smart Prospector</a>
				</div>-->
			</section>
            
            <div class="panel panel-primary forgot-password-container">
    <?php $this->view('forms/forgot_password_form.php'); ?>
</div>
<?php
$project_version = $this->config->item('project_version');
$js_files=array("plugins/browser/jquery.browser.min.js",
"browser.js",
"modals.js",
"login.js"
);
?>
<script src="<?php echo minify("assets/js",$js_files, $project_version) ?>"></script>
<script>
    $(document).ready(function(){
		modals.init();
        login.init();
		browser.init();
    });
	function logo_text(){
	$('#login-logo').remove();
	$('#no-logo').html('<h3>System Login</h3>');
	}
</script>