 <?php if(isset($collapsable)){ ?>    
    

    
     <div id="recordings-panel" class="panel panel-primary">
      <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-parent="#detail-accordion" href="#recordings-panel-slide" aria-expanded="true" aria-controls="recordings-panel-slide">Recordings</div>
       <div id="recordings-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
      </div>
    </div>
    
    <?php } else { ?>
    <div id="recordings-panel" class="panel panel-primary">
      <div class="panel-heading clearfix">Recordings</div>
      <div class="panel-body"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
    </div>
    
        <?php }  ?>