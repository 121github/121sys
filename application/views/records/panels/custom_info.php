 <div class="panel panel-primary custom-panel">
      <div class="panel-heading"><?php echo (!empty($record['custom_name'])?$record['custom_name']:"Additional Info") ?> <span class="glyphicon glyphicon-plus pull-right add-detail-btn"></span></div>
      <div class="panel-body">
        <?php $this->view('forms/edit_additional_info.php'); ?>
        <div class="panel-content"> 
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
      </div>
    </div>