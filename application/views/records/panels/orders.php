 <?php if(isset($collapsable)){ ?>  
     <div  id="orders-panel" class="panel panel-primary">
        <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-parent="#detail-accordion" href="#orders-panel-slide" aria-expanded="true" aria-controls="orders-panel-slide">Orders <button class='btn btn-xs btn-default pull-right' id='create-order'><span class="glyphicon glyphicon-plus" id="new-sms-btn"></span> New</button></div>
          <div id="orders-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
            <div class="panel-content">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
        </div>
    </div>
    </div>
    <?php } else { ?> 
       <div  id="orders-panel" class="panel panel-primary">
        <div class="panel-heading clearfix">Orders <button class='btn btn-xs btn-default pull-right' id='create-order'>Create order</button></div>
        <div class="panel-body">
            <div class="panel-content">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
        </div>
    </div>
     <?php }  ?> 