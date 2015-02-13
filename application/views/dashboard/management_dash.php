
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
   <form class="agent-activity-filter" data-func="agent_activity">
            	<div class="btn-group">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="team">
                  <input type="hidden" name="agent">
                  <input type="hidden" name="source">
			      <input type="hidden" name="colname">
</div>
			      
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($campaigns as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="campaign"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="campaign">All campaigns</a> </li>
	                  </ul>
                  </div>
                  <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="agent"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="agent">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if(in_array("by team",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($team_managers as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="team"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="team">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Source</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($sources as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="source"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="source">All Sources</a> </li>
	                  </ul>
                  </div>
                </form>
              </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body agent-activity table-responsive">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
            <!-- /.panel-body --> 
          </div>
          
           <div class="panel panel-primary" id="a_success">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Agent Success Rates
              <div class="pull-right">
               <form class="success-filter" data-func="agent_success">
            	<div class="btn-group">
                  <input type="hidden" name="date_from" value="<?php echo date('Y-m-d') ?>">
                  <input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="team">
                  <input type="hidden" name="agent">
                  <input type="hidden" name="source">
			      <input type="hidden" name="colname">
                  			      <button type="button" class="daterange btn btn-default btn-xs"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "Today"; ?> </span></button>
</div>
			      
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($campaigns as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="campaign"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="campaign">All campaigns</a> </li>
	                  </ul>
                  </div>
                  <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="agent"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="agent">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if(in_array("by team",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($team_managers as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="team"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="team">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Source</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($sources as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="source"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="source">All Sources</a> </li>
	                  </ul>
                  </div>
                </form>
              </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body agent-success table-responsive">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
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
              <div class="panel-body agent-data table-responsive">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>

      	<!-- /.row --> 
    	</div>
    	
    	<div class="panel panel-primary" id="a_current" style="display: none">
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
dashboard.init();
		
		$('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                },
                format: 'DD/MM/YYYY',
                minDate: "02/07/2014",
                maxDate: moment(),
                startDate: moment(),
                endDate: moment()
            },
            function(start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                dashboard.agent_success()
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
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
