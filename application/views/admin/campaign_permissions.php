
    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Campaign Permissions <small>Override role permissions for a campaign</small> <a href="<?php echo base_url() ?>admin/roles" class="btn btn-default pull-right">Role Permissions</a></h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary" id="campaign-permissions-panel">
          <div class="panel-heading">Campaign Permissions 
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body campaign-permissions-data">
            <?php $this->view('forms/edit_campaign_permissions_form.php'); ?>
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
	admin.campaign_permissions.init();
});
</script> 
