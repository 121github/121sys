 <?php if(isset($collapsable)){ ?>  
     <div id="tasks-panel" class="panel panel-primary">
        <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-parent="#detail-accordion" href="#tasks-panel-slide" aria-expanded="true" aria-controls="tasks-panel-slide">Tasks <button class='btn btn-xs btn-default pull-right' id='task-history'>View History</button></div>
           <div id="tasks-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
            <div class="panel-content">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
        </div>
        </div>
    </div>
    <?php } else { ?>
    
        <div id="tasks-panel" class="panel panel-primary">
        <div class="panel-heading clearfix">Tasks <button class='btn btn-xs btn-default pull-right' id='task-history'>View History</button></div>
        <div class="panel-body">
            <div class="panel-content">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
        </div>
    </div>
    
     <?php }  ?>
