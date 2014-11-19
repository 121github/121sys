<div class="panel panel-primary">
	<div class="panel-heading">Emails <?php if(in_array("send email",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-envelope pull-right new-email-btn pointer"></span><?php } ?></div>
	<div class="panel-body email-panel"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
</div>