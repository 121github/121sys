 <div id="custom-panel" class="panel panel-primary">
      <div class="panel-heading clearfix"><?php echo (!empty($record['custom_name'])?$record['custom_name']:"Additional Info") ?> <?php if(in_array("add custom items",$_SESSION['permissions'])){ ?>	  
	  <span class="btn btn-default btn-xs pull-right add-detail-btn marl"  item-id="' + detail_id + '"><span class="glyphicon glyphicon-plus"></span> New</span>
	  <?php } ?></div>
      <div class="panel-body" style="overflow-x:auto">
        <?php $this->view('forms/edit_additional_info.php'); ?>
        <div class="panel-content"> 
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
      </div>
    </div>