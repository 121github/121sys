    <div class="panel panel-primary">
    <div class="panel-heading"><h4 class="panel-title">Change Password</h4></div>
      <div class="panel-body">
<form id="pass-form">
<div class="form-group">
            <label for="current_pass">Current Password:</label>
            <input type="password" name="current_pass" id="current_pass" class="form-control"/>
</div>
<div class="form-group">
            <label for="new_pass">New Password:</label>
            <input type="password" data-clear-btn="true" name="new_pass" class="form-control" />
       </div>
 <div class="form-group">
            <label for="conf_pass">Confirm Password:</label>
            <input type="password" data-clear-btn="true" name="conf_pass" class="form-control" />
     </div>
       
            <button type="submit" id="change-pass" name="change-pass"class="form-control">Change Password</button>
     
    </form>
    </div>
    </div>
<?php echo form_close(); ?>

<script type="text/javascript">
    
    $(document).ready(function () {
        $('button[type="submit"]').on('click',function(e){
			e.preventDefault();
			 			$.ajax({
                type: "POST",
                dataType: "JSON",
                data: $('#pass-form').serialize()
            }).done(function (response) {
				if(response.success){
				flashalert.success("Password was changed");	
				} else {
				flashalert.danger(response.msg);	
				}
		});
    });
});
    
</script>
