 <div class="panel panel-primary" id="contact-panel">
   <div class="panel-heading clearfix">
  <h4 class="panel-title"> Contact Details<?php if(in_array("add contacts",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-plus pointer pull-right" data-modal="add-contact" data-urn="<?php echo $details['record']["urn"] ?>"></span><?php } ?></h4>
      </div>

      <ul class="list-group contacts-list">
     <li class="list-group-item"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
      </ul>
    
    </div>