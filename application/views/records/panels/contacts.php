 <?php if(isset($collapsable)){ ?>
     
         
         <div class="panel panel-primary" id="contact-panel">
   <div class="panel-heading clearfix"  role="button" data-toggle="collapse"  data-target="#contact-panel-slide" aria-expanded="true" aria-controls="contact-panel-slide">
Contact Details<?php if(in_array("add contacts",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pointer pull-right" data-modal="add-contact" data-urn="<?php echo $details['record']["urn"] ?>"><span class="glyphicon glyphicon-plus"></span> New</button><?php } ?>
      </div>
   <div id="contact-panel-slide" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">      <ul class="list-group contacts-list">
     <li class="list-group-item"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
      </ul>
    </div>
    </div>
    <?php } else { ?>
    
     <div class="panel panel-primary" id="contact-panel">
   <div class="panel-heading clearfix">
  <h4 class="panel-title"> Contact Details<?php if(in_array("add contacts",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pointer pull-right" data-modal="add-contact" data-urn="<?php echo $details['record']["urn"] ?>"><span class="glyphicon glyphicon-plus"></span> New</button><?php } ?></h4>
      </div>

      <ul class="list-group contacts-list">
     <li class="list-group-item"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
      </ul>
    
    </div>
    
     <?php }  ?>