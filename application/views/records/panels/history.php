<?php if(isset($collapsable)){ ?> 
    
      <div class="panel panel-primary" id="history-panel">
        <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-parent="#detail-accordion" href="#history-panel-slide" aria-expanded="true" aria-controls="company-panel-slide">History</div>
         <div id="history-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
        </div>
    </div>
    
    <?php } else { ?>    <div class="panel panel-primary" id="history-panel">
        <div class="panel-heading clearfix">History</div>
        <div class="panel-body">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
    </div>
<?php } ?> 