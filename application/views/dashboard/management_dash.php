
<div id="wrapper">
  <div id="sidebar-wrapper">
 <?php  $this->view('dashboard/navigation.php',$page) ?>
  </div>
  <div id="page-content-wrapper">
    <div id="page-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Management Dashboard</h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-lg-12">
         <div class="panel panel-primary" id="a_activity">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Agent Activity
              <div class="pull-right">
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Filter <span class="caret"></span> </button>
                  <ul class="dropdown-menu pull-right" role="menu">
                    <?php foreach($campaigns as $row): ?>
                    <li><a href="#" class="agent-activity-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                    <?php endforeach ?>
                    <li class="divider"></li>
                    <li><a class="agent-activity-filter" ref="#">Show All</a> </li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body agent-activity">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
            <!-- /.panel-body --> 
          </div>
          
           <div class="panel panel-primary" id="a_success">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Agent Success Rates
              <div class="pull-right">
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Filter <span class="caret"></span> </button>
                  <ul class="dropdown-menu pull-right" role="menu">
                    <?php foreach($campaigns as $row): ?>
                    <li><a href="#" class="agent-success-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                    <?php endforeach ?>
                    <li class="divider"></li>
                    <li><a class="agent-success-filter" ref="#">Show All</a> </li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body agent-success">
              <div id="progress"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
            </div>
            <!-- /.panel-body --> 
          </div>
          

        <div class="panel panel-primary" id="a_data">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Agent  Data
             <div class="pull-right">
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Filter <span class="caret"></span> </button>
                  <ul class="dropdown-menu pull-right" role="menu">
                    <?php foreach($campaigns as $row): ?>
                    <li><a href="#" class="agent-data-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                    <?php endforeach ?>
                    <li class="divider"></li>
                    <li><a class="agent-data-filter" ref="#">Show All</a> </li>
                  </ul>
                </div>
              </div>
            
            </div>
              <div class="panel-body agent-data">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>

      <!-- /.row --> 
    </div>
    <!-- /#page-wrapper --></div>
</div>
<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 

<!-- Page-Level Plugin Scripts - Dashboard --> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/raphael-2.1.0.min.js"></script> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/morris.js"></script> 

<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 
<script>
	$(document).ready(function(){
		dashboard.agent_activity();
		dashboard.agent_success()
		dashboard.agent_data()
		$(document).on("click",".agent-activity-filter",function(e){
			e.preventDefault();
			dashboard.agent_activity($(this).attr('id'));
		});
		$(document).on("click",".agent-success-filter",function(e){
			e.preventDefault();
			dashboard.agent_success($(this).attr('id'));
		});
		$(document).on("click",".agent-data-filter",function(e){
			e.preventDefault();
			dashboard.agent_data($(this).attr('id'));
		});

		$("#agent_activity").on("click", function(){
			$("html,body").animate(
					{ scrollTop : $("#a_activity").offset().top  },
					1500 
			);
		});
		$("#agent_success_rate").on("click", function(){
			$("html,body").animate(
					{ scrollTop : $("#a_success").offset().top  },
					1500 
			);
		});
		$("#agent_data").on("click", function(){
			$("html,body").animate(
					{ scrollTop : $("#a_data").offset().top  },
					1500 
			);
		});
	});
	</script> 
