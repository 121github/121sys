
    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Team Admin</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary teams-panel">
          <div class="panel-heading"> Team Admin
            <div class="pull-right">
              <div class="btn-team">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle add-btn" data-toggle="dropdown"><span class="glyphicon glyphicon-plus"></span> Add Team</button>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body team-data">
            <?php $this->view('forms/edit_teams_form.php',$options); ?>
            <table class="table ajax-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Group</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="4"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /.panel-body --> 
        </div>
      </div>

<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 

<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 
<script>
$(document).ready(function(){
	admin.init();
	admin.teams.init();
});
</script> 
