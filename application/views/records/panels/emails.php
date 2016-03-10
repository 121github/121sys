<?php if(isset($collapsable)){ ?> 
   
<div id="email-panel" class="panel panel-primary">
	<div class="panel-heading clearfix" role="button" data-toggle="collapse"  data-target="#emails-panel-slide" aria-expanded="true" aria-controls="emails-panel-slide">Emails <?php if(in_array("send email",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pull-right pointer" id="new-email-btn"><span class="glyphicon glyphicon-envelope"></span> New</button><?php } ?></div>
	 <div id="emails-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
     <div class="panel-body"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
     </div>
</div>

    
    <?php } else { ?>

<div id="email-panel" class="panel panel-primary">
	<div class="panel-heading clearfix">Emails <?php if(in_array("send email",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pull-right pointer" id="new-email-btn"><span class="glyphicon glyphicon-envelope"></span> New</button><?php } ?></div>
	<div class="panel-body"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
</div>

   <?php }  ?>