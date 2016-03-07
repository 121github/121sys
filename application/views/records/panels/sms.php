 <?php if(isset($collapsable)){ ?>  
        <div class="panel panel-primary">
	<div class="panel-heading clearfix" role="button" data-toggle="collapse" data-parent="#detail-accordion" href="#sms-panel-slide" aria-expanded="true" aria-controls="sms-panel-slide">SMS History <?php if(in_array("send sms",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pull-right pointer" id="new-sms-btn"><span class="glyphicon glyphicon-phone" id="new-sms-btn"></span> New</button><?php } ?></div>
	   <div id="sms-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
    <div class="panel-body" id="sms-panel"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
    </div>
</div>
    <?php } else { ?>
    <div class="panel panel-primary">
	<div class="panel-heading clearfix">SMS History <?php if(in_array("send sms",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pull-right pointer" id="new-sms-btn"><span class="glyphicon glyphicon-phone" id="new-sms-btn"></span> New</button><?php } ?></div></div>
	  
    <div class="panel-body" id="sms-panel"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>

</div>
 <?php } ?>