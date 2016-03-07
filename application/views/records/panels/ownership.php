 <?php if(isset($collapsable)){ ?>    
    
      <div id="ownership-panel" class="panel panel-primary">
      <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-parent="#detail-accordion" href="#ownership-panel-slide" aria-expanded="true" aria-controls="ownership-panel-slide">Ownership <?php if(in_array("change ownership",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pointer pull-right edit-owner"><span class="glyphicon glyphicon-pencil"></span> Edit</button><?php } ?></div>
          <div id="ownership-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
        <?php $this->view('forms/edit_ownership_form.php',$users); ?>
        <div class="panel-content"> 
          <!-- This panel now loads via ajax --> 
        </div>
      </div>
      </div>
    </div>
    <?php } else { ?>

    <div id="ownership-panel" class="panel panel-primary">
      <div class="panel-heading clearfix">Ownership <?php if(in_array("change ownership",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-pencil pointer pull-right edit-owner"></span><?php } ?></div>
      <div class="panel-body">
        <?php $this->view('forms/edit_ownership_form.php',$users); ?>
        <div class="panel-content"> 
          <!-- This panel now loads via ajax --> 
        </div>
      </div>
    </div>
    
        <?php }  ?>
    