<div id="email-panel" class="panel panel-primary">
	<div class="panel-heading clearfix">Emails <?php if(in_array("send email",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-envelope pull-right pointer" id="new-email-btn"></span><?php } ?></div>
	<div class="panel-body"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
</div>