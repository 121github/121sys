
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
    	
    	<div class="panel panel-primary" id="a_current">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Agent Current Hours (Today)
              <div class="pull-right">
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Filter <span class="caret"></span> </button>
                  <ul class="dropdown-menu pull-right" role="menu">
                    <?php foreach($campaigns as $row): ?>
                    <li><a href="#" class="agent-current-hours-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                    <?php endforeach ?>
                    <li class="divider"></li>
                    <li><a class="agent-current-hours-filter" ref="#">Show All</a> </li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body agent-current-hours">
              <div id="progress"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
            </div>
            <!-- /.panel-body --> 
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
		dashboard.agent_success();
		dashboard.agent_data();
		dashboard.agent_current_hours();
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
		$(document).on("click",".agent-current-hours-filter",function(e){
			e.preventDefault();
			dashboard.agent_current_hours($(this).attr('id'));
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
		$("#agent_current_hours").on("click", function(){
			$("html,body").animate(
					{ scrollTop : $("#a_current").offset().top  },
					1500 
			);
		});
		
		var start = new Date;
		setInterval(function() {
			groups = $('[id $= duration]'); 
	
			$.each(groups, function(key, group) {
			    inputs_seconds = $(group).children('[id^=time_box_seconds]');
			    inputs_date = $(group).children('[id^=time_box_date]');
			    inputs_rate = $(group).children('[id^=rate_box_]');
			    inputs_transfers = $(group).children('[id^=transfers_box_]');

			    elapsed_seconds = ((new Date - start)/1000)+Number(inputs_seconds.text());
				inputs_date.text(get_elapsed_time_string(elapsed_seconds));

				rate = inputs_transfers.text()/(elapsed_seconds/60/60);
				inputs_rate.text(rate.toFixed(2)+ ' per hour');
			});
		}, 1000);
	});
	</script> 
