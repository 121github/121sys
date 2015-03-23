
      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">New Business Dashboard</h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-lg-8">
                  <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>My Favorites</div>
              <div class="panel-body favorites-panel">
             <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
        </div>
        
           <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Interest Nows
              <div class="pull-right">
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Filter <span class="caret"></span> </button>
                  <ul class="dropdown-menu pull-right" role="menu">
                    <?php foreach($campaigns as $row): ?>
                    <li><a href="#" class="interest-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                    <?php endforeach ?>
                    <li class="divider"></li>
                    <li><a class="interest-filter" ref="#">Show All</a> </li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body interest-panel">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
            <!-- /.panel-body --> 
          </div>
          
      
           <!-- /col-lg-8 -->

</div>
<div class="col-lg-4">
                  <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Pending</div>
              <div class="panel-body pending-panel">
             <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
        </div>
        
                <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Urgent</div>
              <div class="panel-body urgent-panel">
<img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
            </div>
                            <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Appointments</div>
              <div class="panel-body appointments-panel">
<img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
            </div>
        <!-- /.col-lg-4 --> 
   </div>
      <!-- /.row --> 

<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 

<!-- Page-Level Plugin Scripts - Dashboard --> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/raphael-2.1.0.min.js"></script> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/morris.js"></script> 

<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 
<script>
	$(document).ready(function(){
		dashboard.urgent_panel();
		dashboard.favorites_panel(3);
		dashboard.appointments_panel();
		dashboard.pending_panel();
		dashboard.interest_panel(<?php echo ($_SESSION['role']>1?$_SESSION['user_id']:"false") ?>);
				$(document).on("click",".interest-filter",function(e){
			e.preventDefault();
			dashboard.interest_panel(<?php echo ($_SESSION['role']>1?$_SESSION['user_id']:"false") ?>,$(this).attr('id'))
		});
	});
	</script> 
