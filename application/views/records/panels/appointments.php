 <?php if(isset($collapsable)){ ?>
     
        <div id="appointment-panel" class="panel panel-primary">
      <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-target="#appointment-panel-slide" href="#appointment-panel-slide" aria-expanded="true" aria-controls="appointment-panel-slide">
 Appointments<?php if(in_array("add appointments",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pointer pull-right marl" data-modal="create-appointment" data-urn="<?php echo $record['urn'] ?>"><span class="glyphicon glyphicon-plus"></span> New</button><?php } ?> <button class="btn btn-default btn-xs pull-right pointer view-calendar"><span class="glyphicon glyphicon-calendar"></span> Calendar</button>
    </div>
       <div id="appointment-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body appointment-panel"> 
        <div class="panel-content"> 
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
      </div>
      </div>
    </div>
    
    <?php } else { ?>
 
    <div id="appointment-panel" class="panel panel-primary">
      <div class="panel-heading clearfix">Appointments  <?php if(in_array("add appointments",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pointer pull-right marl" data-modal="create-appointment" data-urn="<?php echo $record['urn'] ?>"><span class="glyphicon glyphicon-plus"></span> New</button><?php } ?> <button class="btn btn-default btn-xs pull-right pointer view-calendar"><span class="glyphicon glyphicon-calendar"></span> Calendar</button></div>
      <div class="panel-body appointment-panel"> 
        <div class="panel-content"> 
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
      </div>
    </div>
 <?php } ?>