
    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Role Admin</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary roles-panel">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Role Admin
            <div class="pull-right">
              <div class="btn-role">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle add-btn" data-toggle="dropdown"><span class="glyphicon glyphicon-plus"></span> Add Role</button>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body role-data">
            <?php $this->view('forms/edit_roles_form.php'); ?>
            <table class="table ajax-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="3"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /.panel-body --> 
        </div>
      </div>
      
      <!-- /.row --> 

<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 

<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 
<script>
$(document).ready(function(){
	admin.init();
	admin.roles.init();
});
</script> 
