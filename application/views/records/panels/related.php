 <?php if(isset($collapsable)){ ?>  

 <div id="related-panel" class="panel panel-primary">
        <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-parent="#detail-accordion" href="#related-panel-slide" aria-expanded="true" aria-controls="related-panel-slide">Related Records</div>
           <div id="related-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
            <div class="panel-content">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
        </div>
        </div>
    </div>
    <?php } else { ?>    <div id="related-panel" class="panel panel-primary">
        <div class="panel-heading clearfix">Related Records</div>
        <div class="panel-body">
            <div class="panel-content">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
        </div>
    </div>
      <?php } ?> 
