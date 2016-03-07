 <?php if(isset($collapsable)){ ?>  
        <div id="surveys-panel" class="panel panel-primary">
      <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-parent="#detail-accordion" href="#survey-panel-slide" aria-expanded="true" aria-controls="survey-panel-slide">Surveys <button class="btn btn-default btn-xs pointer pull-right" id="new-survey"><span class="glyphicon glyphicon-plus" ></span> New</button></div>
      <div id="survey-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body surveys-panel"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
      </div>
    </div>
    <?php } else { ?>
        <div id="surveys-panel" class="panel panel-primary">
      <div class="panel-heading clearfix">Surveys <span class="glyphicon glyphicon-plus pointer pull-right" id="new-survey"></span></div>
      <div class="panel-body surveys-panel"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
    </div>
     <?php } ?>