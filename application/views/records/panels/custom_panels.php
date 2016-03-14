<?php if(isset($collapsable)){ ?> 

     <div class="panel panel-primary custom-panel" custom-panel-display="<?php echo $display ?>" custom-panel-id="<?php echo $custom_panel_id ?>">
      <div class="panel-heading clearfix" role="button" data-toggle="collapse"  data-target="#dynamic-panel-slide" aria-expanded="true" aria-controls="dynamic-panel-slide">
     <?php echo $name ?>
      <?php if(in_array("edit custom data",$_SESSION['permissions'])){ ?>
      <span class='btn btn-default btn-xs pull-right edit-custom-btn marl' style="display:none" custom-data-id=''><span class='glyphicon glyphicon-pencil'></span> Edit</span>
      <?php } ?>
      <?php if(in_array("add custom data",$_SESSION['permissions'])){ ?>
	  <span class="btn btn-default btn-xs pull-right add-custom-btn marl" custom-panel-id="<?php echo $custom_panel_id ?>"><span class="glyphicon glyphicon-plus"></span> New</span>
      <?php } ?>
	 </div>
     	 <div id="dynamic-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body" style="overflow-x:auto">
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
      </div>
      </div>
    </div>
    
    <?php } else { ?> <div class="panel panel-primary custom-panel" custom-panel-display="<?php echo $display ?>" custom-panel-id="<?php echo $custom_panel_id ?>">
      <div class="panel-heading clearfix"><span class="title"><?php echo $name ?></span>
      <span class='btn btn-default btn-xs pull-right edit-custom-btn marl' style="display:none" custom-data-id=''><span class='glyphicon glyphicon-pencil'></span> Edit</span>
	  <span class="btn btn-default btn-xs pull-right add-custom-btn marl" custom-panel-id="<?php echo $custom_panel_id ?>"><span class="glyphicon glyphicon-plus"></span> New</span>
	 </div>
      <div class="panel-body" style="overflow-x:auto">
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
      </div>
    </div>
    
     <?php } ?> 