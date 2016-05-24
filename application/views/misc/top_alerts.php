<?php if($this->session->flashdata('success')){ ?>
<div class="alert alert-success alert-dismissable" style="margin-top:10px">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <span class="glyphicon glyphicon-ok"></span> <?php echo $this->session->flashdata('success'); ?> </div>
<?php } ?>
<?php if($this->session->flashdata('danger')){ ?>
<div class="alert alert-danger alert-dismissable" style="margin-top:10px">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <span class="glyphicon glyphicon-alert"></span> <?php echo $this->session->flashdata('danger'); ?> </div>
<?php } ?>
<?php if($this->session->flashdata('info')){ ?>
<div class="alert alert-info alert-dismissable" style="margin-top:10px">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <span class="glyphicon glyphicon-info-sign"></span> <?php echo $this->session->flashdata('info'); ?> </div>
<?php } ?>
<?php if($this->session->flashdata('warning')){ ?>
<div class="alert alert-warning alert-dismissable" style="margin-top:10px">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <span class="glyphicon glyphicon-exclamation-sign"></span> <?php echo $this->session->flashdata('warning'); ?> </div>
<?php } ?>
