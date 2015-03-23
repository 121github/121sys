    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">File Admin</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary folders-panel">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Folders
            <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle add-btn" data-toggle="dropdown"><span class="glyphicon glyphicon-plus"></span> Add Folder</button>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body">
              <?php $this->view('forms/edit_folders_form.php',$options); ?>
              <div class="folder-data">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
              </div>
              </div>
        </div>
      </div>

<script>
$(document).ready(function(){
	admin.init();
	admin.folders.init();
});
</script>