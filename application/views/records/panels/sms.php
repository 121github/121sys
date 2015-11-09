<div class="panel panel-primary">
	<div class="panel-heading clearfix">SMS History <?php if(in_array("send sms",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-phone pull-right pointer" id="new-sms-btn"></span><?php } ?></div>
	<div class="panel-body" id="sms-panel"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
</div>